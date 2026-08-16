<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Murid;
use App\Models\ProgramKursus;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Layer1AuditRemediationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Admin $admin;

    protected User $guruUser;

    protected Guru $guru;

    protected User $muridUser;

    protected Murid $murid;

    protected ProgramKursus $program;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->admin = Admin::create(['id_user' => $this->adminUser->id_user, 'nama_admin' => 'Admin Test']);

        $this->guruUser = User::factory()->create(['role' => 'guru', 'is_active' => true]);
        $this->guru = Guru::create(['id_user' => $this->guruUser->id_user, 'nama_guru' => 'Guru Test', 'status_aktif' => true]);

        $this->muridUser = User::factory()->create(['role' => 'murid', 'is_active' => true]);
        $this->murid = Murid::create([
            'id_user' => $this->muridUser->id_user,
            'nama_murid' => 'Murid Test',
            'status_aktif' => true,
        ]);

        $this->program = ProgramKursus::create([
            'nama_program' => 'Piano Pop',
            'tipe_les' => 'onsite',
            'biaya_kursus' => 350000,
            'is_active' => true,
        ]);
    }

    public function test_guru_monthly_report_index_batch_queries_without_n_plus_one()
    {
        $bulan = now()->format('Y-m');

        // Create 3 SPPs and Jadwals
        for ($i = 0; $i < 3; $i++) {
            $mUser = User::factory()->create(['role' => 'murid', 'is_active' => true]);
            $m = Murid::create(['id_user' => $mUser->id_user, 'nama_murid' => "Murid {$i}", 'status_aktif' => true]);
            $spp = Spp::create([
                'id_murid' => $m->id_murid,
                'id_program' => $this->program->id_program,
                'periode_tagihan' => $bulan.'-01',
                'nominal_tagihan' => 350000,
                'tanggal_jatuh_tempo' => $bulan.'-28',
                'tipe_les' => 'Onsite',
                'status_bayar' => 'Belum Lunas',
            ]);
            Jadwal::create([
                'id_admin' => $this->admin->id_admin,
                'id_guru' => $this->guru->id_guru,
                'id_spp' => $spp->id_spp,
                'tanggal' => now()->toDateString(),
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '11:00:00',
                'sesi_ke' => 1,
                'status_jadwal' => 'Sesuai Jadwal',
                'is_active' => true,
            ]);
        }

        $this->actingAs($this->guruUser);

        // Count queries
        DB::enableQueryLog();
        $response = $this->get(route('guru.monthly-report.index', ['bulan' => $bulan]));
        $response->assertStatus(200);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // 1 user lookup, 1 guru lookup, 1 batch jadwals, 1 batch monthly reports, 1 batch spps with relations
        $this->assertLessThanOrEqual(8, count($queries));
    }

    public function test_spp_proof_upload_and_validation_with_transactions()
    {
        Storage::fake('local');

        $spp = Spp::create([
            'id_murid' => $this->murid->id_murid,
            'id_program' => $this->program->id_program,
            'periode_tagihan' => now()->format('Y-m').'-01',
            'nominal_tagihan' => 350000,
            'tanggal_jatuh_tempo' => now()->format('Y-m').'-28',
            'tipe_les' => 'Onsite',
            'status_bayar' => 'Belum Lunas',
        ]);

        $this->actingAs($this->muridUser);

        $file = UploadedFile::fake()->image('transfer.jpg');
        $response = $this->post(route('murid.spp.bukti', $spp), [
            'bukti_transfer' => $file,
            'nominal_bayar' => 350000,
            'tanggal_bayar' => now()->toDateString(),
        ]);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transaksis', [
            'id_spp' => $spp->id_spp,
            'nominal_bayar' => 350000,
        ]);

        // Admin validates payment
        $this->actingAs($this->adminUser);
        $valResponse = $this->patch(route('admin.spp.validasi', $spp), [
            'catatan_admin' => 'Valid payment',
        ]);
        $valResponse->assertSessionHas('success');

        $this->assertEquals('Lunas', $spp->fresh()->status_bayar);
    }

    public function test_admin_cek_sesi_parameter_validation()
    {
        $this->actingAs($this->adminUser);

        // Invalid query parameters
        $response = $this->getJson(route('admin.jadwals.cekSesi', [
            'id_murid' => 99999,
            'id_program' => 99999,
        ]));
        $response->assertStatus(422);

        // Valid query parameters
        $validResponse = $this->getJson(route('admin.jadwals.cekSesi', [
            'id_murid' => $this->murid->id_murid,
            'id_program' => $this->program->id_program,
        ]));
        $validResponse->assertStatus(200);
        $validResponse->assertJson(['last_sesi' => 0]);
    }
}
