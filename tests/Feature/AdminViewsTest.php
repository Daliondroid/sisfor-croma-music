<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\HonorGuru;
use App\Models\Jadwal;
use App\Models\MonthlyReport;
use App\Models\Murid;
use App\Models\ProgramKursus;
use App\Models\ProgresMurid;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminViewsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        Admin::create([
            'id_user' => $this->admin->id_user,
            'nama_admin' => 'Admin Test',
        ]);
    }

    public function test_admin_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Utama');
    }

    public function test_admin_murids_views_render(): void
    {
        $userMurid = User::factory()->create(['role' => 'murid']);
        $murid = Murid::create([
            'id_user' => $userMurid->id_user,
            'nama_murid' => 'Murid Test',
            'tipe_les' => 'onsite',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.murids.index'));
        $response->assertStatus(200);
        $response->assertSee('Data Murid');
        $response->assertSee('Akademik');

        $response = $this->actingAs($this->admin)->get(route('admin.murids.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Murid');

        $response = $this->actingAs($this->admin)->get(route('admin.murids.edit', $murid));
        $response->assertStatus(200);
        $response->assertSee('Edit Data Murid');
    }

    public function test_admin_gurus_views_render(): void
    {
        $userGuru = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'id_user' => $userGuru->id_user,
            'nama_guru' => 'Guru Test',
            'spesialisasi' => 'Piano',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.gurus.index'));
        $response->assertStatus(200);
        $response->assertSee('Data Guru');
        $response->assertSee('Akademik');

        $response = $this->actingAs($this->admin)->get(route('admin.gurus.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Guru');

        $response = $this->actingAs($this->admin)->get(route('admin.gurus.edit', $guru));
        $response->assertStatus(200);
        $response->assertSee('Edit Data Guru');
    }

    public function test_admin_program_kursus_views_render(): void
    {
        $program = ProgramKursus::create([
            'nama_program' => 'Piano Test',
            'tipe_les' => 'onsite',
            'biaya_kursus' => 350000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.program-kursus.index'));
        $response->assertStatus(200);
        $response->assertSee('Program Kursus');
        $response->assertSee('Akademik');

        $response = $this->actingAs($this->admin)->get(route('admin.program-kursus.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Program Kursus');

        $response = $this->actingAs($this->admin)->get(route('admin.program-kursus.edit', $program));
        $response->assertStatus(200);
        $response->assertSee('Edit Program Kursus');
    }

    public function test_admin_jadwals_views_render(): void
    {
        $userGuru = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'id_user' => $userGuru->id_user,
            'nama_guru' => 'Guru Test',
            'spesialisasi' => 'Piano',
        ]);
        $userMurid = User::factory()->create(['role' => 'murid']);
        $murid = Murid::create([
            'id_user' => $userMurid->id_user,
            'nama_murid' => 'Murid Test',
            'tipe_les' => 'onsite',
        ]);
        $program = ProgramKursus::create([
            'nama_program' => 'Piano Test',
            'tipe_les' => 'onsite',
            'biaya_kursus' => 350000,
            'is_active' => true,
        ]);
        $spp = Spp::create([
            'id_murid' => $murid->id_murid,
            'id_program' => $program->id_program,
            'periode_tagihan' => now()->startOfMonth()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(10)->toDateString(),
            'nominal_tagihan' => 350000,
            'status_bayar' => 'Lunas',
            'tipe_les' => 'Onsite',
        ]);
        $jadwal = Jadwal::create([
            'id_spp' => $spp->id_spp,
            'id_guru' => $guru->id_guru,
            'id_admin' => 1,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '14:00:00',
            'jam_selesai' => '15:00:00',
            'sesi_ke' => 1,
            'status_jadwal' => 'Sesuai Jadwal',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.jadwals.index'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal KBM');

        $response = $this->actingAs($this->admin)->get(route('admin.jadwals.create'));
        $response->assertStatus(200);
        $response->assertSee('Buat Jadwal Baru');

        $response = $this->actingAs($this->admin)->get(route('admin.jadwals.show', $jadwal->id_jadwal));
        $response->assertStatus(200);
        $response->assertSee('Detail Jadwal KBM');

        $response = $this->actingAs($this->admin)->get(route('admin.jadwals.edit', $jadwal->id_jadwal));
        $response->assertStatus(200);
        $response->assertSee('Edit Jadwal KBM');
    }

    public function test_admin_spp_index_renders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.spp.index'));
        $response->assertStatus(200);
        $response->assertSee('Tagihan SPP');
        $response->assertSee('Keuangan');
    }

    public function test_admin_honor_guru_views_render(): void
    {
        $userGuru = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'id_user' => $userGuru->id_user,
            'nama_guru' => 'Guru Test',
            'spesialisasi' => 'Piano',
        ]);
        $honor = HonorGuru::create([
            'id_guru' => $guru->id_guru,
            'bulan_periode' => now()->format('Y-m'),
            'jumlah_honor' => 500000,
            'status_bayar' => 'Lunas',
            'jumlah_pertemuan' => 4,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.honor-guru.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Gaji Guru');

        $response = $this->actingAs($this->admin)->get(route('admin.honor-guru.edit', $honor->id_honor));
        $response->assertStatus(200);
        $response->assertSee('Kelola Gaji Guru');
    }

    public function test_admin_laporan_views_render(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.keuangan'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan Bulanan');

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.gaji'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Gaji & Honor Guru');
    }

    public function test_admin_monthly_report_index_renders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monthly_report.index'));
        $response->assertStatus(200);
        $response->assertSee('Monthly Report Murid');
    }

    public function test_admin_profil_renders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.profil.edit'));
        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
    }

    public function test_admin_monthly_report_show_renders_without_report(): void
    {
        $userMurid = User::factory()->create(['role' => 'murid']);
        $murid = Murid::create([
            'id_user' => $userMurid->id_user,
            'nama_murid' => 'Anisa Rahmawati',
            'tipe_les' => 'onsite',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.report.show', [
            'murid' => $murid->id_murid,
            'bulan' => '2026-08',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Anisa Rahmawati');
        $response->assertSee('Total Sesi');
        $response->assertSee('Monthly report belum dibuat');
    }

    public function test_admin_monthly_report_show_renders_with_report_and_jadwals(): void
    {
        $userMurid = User::factory()->create(['role' => 'murid']);
        $murid = Murid::create([
            'id_user' => $userMurid->id_user,
            'nama_murid' => 'Anisa Rahmawati',
            'tipe_les' => 'onsite',
        ]);

        $userGuru = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'id_user' => $userGuru->id_user,
            'nama_guru' => 'Guru Test',
            'spesialisasi' => 'Piano',
        ]);

        $program = ProgramKursus::create([
            'nama_program' => 'Piano Test',
            'tipe_les' => 'onsite',
            'biaya_kursus' => 350000,
            'is_active' => true,
        ]);

        $spp = Spp::create([
            'id_murid' => $murid->id_murid,
            'id_program' => $program->id_program,
            'periode_tagihan' => '2026-08-01',
            'tanggal_jatuh_tempo' => '2026-08-10',
            'nominal_tagihan' => 350000,
            'status_bayar' => 'Lunas',
            'tipe_les' => 'Onsite',
        ]);

        $report = MonthlyReport::create([
            'id_spp' => $spp->id_spp,
            'periode_bulan' => '2026-08-01',
            'skor' => 'A',
            'evaluasi_bulanan' => 'Perkembangan murid sangat bagus dan konsisten.',
            'url_video' => 'https://youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $jadwal = Jadwal::create([
            'id_spp' => $spp->id_spp,
            'id_guru' => $guru->id_guru,
            'id_admin' => 1,
            'tanggal' => '2026-08-05',
            'jam_mulai' => '14:00:00',
            'jam_selesai' => '15:00:00',
            'sesi_ke' => 1,
            'status_jadwal' => 'Sesuai Jadwal',
            'status_kehadiran_murid' => 'Hadir',
            'status_kehadiran_guru' => 'Hadir',
            'is_active' => true,
        ]);

        ProgresMurid::create([
            'id_jadwal' => $jadwal->id_jadwal,
            'materi_diajarkan' => 'Tangga Nada C Mayor',
            'catatan_perkembangan' => 'Dapat memainkan dengan lancar.',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.report.show', [
            'murid' => $murid->id_murid,
            'bulan' => '2026-08',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Anisa Rahmawati');
        $response->assertSee('Perkembangan murid sangat bagus dan konsisten.');
        $response->assertSee('Tangga Nada C Mayor');
        $response->assertSee('Skor: A');
    }

    public function test_admin_monthly_report_update_success(): void
    {
        $userMurid = User::factory()->create(['role' => 'murid']);
        $murid = Murid::create([
            'id_user' => $userMurid->id_user,
            'nama_murid' => 'Anisa Rahmawati',
            'tipe_les' => 'onsite',
        ]);

        $program = ProgramKursus::create([
            'nama_program' => 'Piano Test',
            'tipe_les' => 'onsite',
            'biaya_kursus' => 350000,
            'is_active' => true,
        ]);

        $spp = Spp::create([
            'id_murid' => $murid->id_murid,
            'id_program' => $program->id_program,
            'periode_tagihan' => '2026-08-01',
            'tanggal_jatuh_tempo' => '2026-08-10',
            'nominal_tagihan' => 350000,
            'status_bayar' => 'Lunas',
            'tipe_les' => 'Onsite',
        ]);

        $report = MonthlyReport::create([
            'id_spp' => $spp->id_spp,
            'periode_bulan' => '2026-08-01',
            'skor' => 'B',
            'evaluasi_bulanan' => 'Evaluasi awal.',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.report.update', $report->id_report), [
            'skor' => 'A+',
            'evaluasi_bulanan' => 'Evaluasi telah diperbarui dengan capaian luar biasa.',
            'url_video' => 'https://youtube.com/watch?v=updated1234',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('monthly_reports', [
            'id_report' => $report->id_report,
            'skor' => 'A+',
            'evaluasi_bulanan' => 'Evaluasi telah diperbarui dengan capaian luar biasa.',
            'url_video' => 'https://youtube.com/watch?v=updated1234',
        ]);
    }
}
