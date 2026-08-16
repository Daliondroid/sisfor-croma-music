<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Murid;
use App\Models\ProgramKursus;
use App\Models\Spp;
use App\Models\User;
use App\Services\JadwalBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalSchedulingTest extends TestCase
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

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->admin = Admin::create([
            'id_user' => $this->adminUser->id_user,
            'nama_admin' => 'Admin Test',
        ]);

        $this->guruUser = User::factory()->create(['role' => 'guru']);
        $this->guru = Guru::create([
            'id_user' => $this->guruUser->id_user,
            'nama_guru' => 'Guru Test',
            'status_aktif' => true,
        ]);

        $this->muridUser = User::factory()->create(['role' => 'murid']);
        $this->murid = Murid::create([
            'id_user' => $this->muridUser->id_user,
            'nama_murid' => 'Murid Test',
            'status_aktif' => true,
        ]);

        $this->program = ProgramKursus::create([
            'nama_program' => 'Piano Pop',
            'tipe_les' => 'onsite',
            'biaya_kursus' => 500000,
            'is_active' => true,
        ]);
    }

    public function test_onsite_learning_displays_as_onsite_in_murid_dashboard(): void
    {
        $spp = Spp::create([
            'id_murid' => $this->murid->id_murid,
            'id_program' => $this->program->id_program,
            'periode_tagihan' => '2026-09-01',
            'nominal_tagihan' => 500000,
            'tanggal_jatuh_tempo' => '2026-09-02',
            'tipe_les' => 'Onsite',
            'status_bayar' => 'Belum Lunas',
        ]);

        Jadwal::create([
            'id_admin' => $this->admin->id_admin,
            'id_guru' => $this->guru->id_guru,
            'id_spp' => $spp->id_spp,
            'tanggal' => now()->addDays(2)->toDateString(),
            'jam_mulai' => '14:00:00',
            'jam_selesai' => '15:00:00',
            'sesi_ke' => 1,
            'status_jadwal' => 'Sesuai Jadwal',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->muridUser)->get(route('murid.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('ONSITE');
        $response->assertDontSee('HOME');
    }

    public function test_strict_4_sessions_per_month_prevents_5th_session_overflow(): void
    {
        $service = new JadwalBuilderService;

        // 8 sessions starting September 2, 2026 (Wednesday, week 1, day 2 <= 7)
        // September has 5 Wednesdays (Sept 2, 9, 16, 23, 30).
        // Strict 4 sessions should place 4 in Sept (Sept 2, 9, 16, 23) and 4 in Oct (Oct 7, 14, 21, 28).
        // It must NOT place a session on Sept 30.
        $payload = [
            'id_murid' => $this->murid->id_murid,
            'id_program' => $this->program->id_program,
            'id_guru' => $this->guru->id_guru,
            'total_sesi' => 8,
            'tipe_les' => 'Onsite',
            'tipe_jadwal' => 'tetap',
            'tanggal_mulai' => '2026-09-02',
            'pola_tunggal' => [
                'hari' => 'Rabu',
                'jam_mulai' => '15:00',
                'jam_selesai' => '16:00',
            ],
        ];

        $count = $service->createBulkJadwal($payload, $this->admin->id_admin);
        $this->assertEquals(8, $count);

        $jadwals = Jadwal::orderBy('tanggal')->get();
        $this->assertCount(8, $jadwals);

        $dates = $jadwals->pluck('tanggal')->map(fn ($d) => $d->format('Y-m-d'))->toArray();

        // Check September sessions (strictly 4)
        $this->assertEquals('2026-09-02', $dates[0]);
        $this->assertEquals('2026-09-09', $dates[1]);
        $this->assertEquals('2026-09-16', $dates[2]);
        $this->assertEquals('2026-09-23', $dates[3]);

        // Check that Sept 30 was skipped and session 5 is the 1st Wednesday of October (Oct 7)
        $this->assertEquals('2026-10-07', $dates[4]);
        $this->assertEquals('2026-10-14', $dates[5]);
        $this->assertEquals('2026-10-21', $dates[6]);
        $this->assertEquals('2026-10-28', $dates[7]);

        // Check SPP periods
        $spps = Spp::orderBy('periode_tagihan')->get();
        $this->assertCount(2, $spps);
        $this->assertEquals('2026-09-01', $spps[0]->periode_tagihan->format('Y-m-d'));
        $this->assertEquals('Onsite', $spps[0]->tipe_les);
        $this->assertEquals('2026-10-01', $spps[1]->periode_tagihan->format('Y-m-d'));
        $this->assertEquals('Onsite', $spps[1]->tipe_les);
    }

    public function test_week_2_to_4_registration_starts_first_session_in_next_month(): void
    {
        $service = new JadwalBuilderService;

        // Registration reference date: September 15, 2026 (day 15 > 7, week 3)
        // Should automatically shift Month 1 to October 2026.
        $payload = [
            'id_murid' => $this->murid->id_murid,
            'id_program' => $this->program->id_program,
            'id_guru' => $this->guru->id_guru,
            'total_sesi' => 4,
            'tipe_les' => 'Onsite',
            'tipe_jadwal' => 'tetap',
            'tanggal_mulai' => '2026-09-15',
            'pola_tunggal' => [
                'hari' => 'Rabu',
                'jam_mulai' => '15:00',
                'jam_selesai' => '16:00',
            ],
        ];

        $count = $service->createBulkJadwal($payload, $this->admin->id_admin);
        $this->assertEquals(4, $count);

        $jadwals = Jadwal::orderBy('tanggal')->get();
        $this->assertCount(4, $jadwals);

        $dates = $jadwals->pluck('tanggal')->map(fn ($d) => $d->format('Y-m-d'))->toArray();

        // Sessions start in October 2026
        $this->assertEquals('2026-10-07', $dates[0]);
        $this->assertEquals('2026-10-14', $dates[1]);
        $this->assertEquals('2026-10-21', $dates[2]);
        $this->assertEquals('2026-10-28', $dates[3]);

        $spp = Spp::first();
        $this->assertEquals('2026-10-01', $spp->periode_tagihan->format('Y-m-d'));
        $this->assertEquals('2026-10-07', $spp->tanggal_jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('Onsite', $spp->tipe_les);
    }
}
