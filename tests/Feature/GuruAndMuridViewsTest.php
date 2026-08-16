<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\MonthlyReport;
use App\Models\Murid;
use App\Models\ProgramKursus;
use App\Models\ProgresMurid;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruAndMuridViewsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private Admin $admin;

    private User $guruUser;

    private Guru $guru;

    private User $muridUser;

    private Murid $murid;

    private ProgramKursus $program;

    private Spp $spp;

    private Jadwal $jadwal;

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
            'nama_guru' => 'Guru Pengajar',
            'honor_per_sesi' => 75000,
        ]);

        $this->muridUser = User::factory()->create(['role' => 'murid']);
        $this->murid = Murid::create([
            'id_user' => $this->muridUser->id_user,
            'nama_murid' => 'Murid Belajar',
            'tipe_les' => 'onsite',
            'status_aktif' => true,
        ]);

        $this->program = ProgramKursus::create([
            'nama_program' => 'Piano Pop',
            'tipe_les' => 'onsite',
            'biaya_kursus' => 500000,
            'is_active' => true,
        ]);

        $this->spp = Spp::create([
            'id_murid' => $this->murid->id_murid,
            'id_program' => $this->program->id_program,
            'periode_tagihan' => now()->startOfMonth()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(10)->toDateString(),
            'nominal_tagihan' => 500000,
            'status_bayar' => 'Lunas',
            'tipe_les' => 'Onsite',
        ]);

        $this->jadwal = Jadwal::create([
            'id_spp' => $this->spp->id_spp,
            'id_guru' => $this->guru->id_guru,
            'id_admin' => $this->admin->id_admin,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '14:00:00',
            'jam_selesai' => '14:45:00',
            'sesi_ke' => 1,
            'status_kehadiran_murid' => 'Hadir',
            'status_kehadiran_guru' => 'Hadir',
            'status_jadwal' => 'Sesuai Jadwal',
            'is_active' => true,
        ]);
    }

    public function test_guru_dashboard_renders(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('guru.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Guru');
    }

    public function test_guru_jadwal_renders(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('guru.jadwal.index'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Kelas');
    }

    public function test_guru_absensi_renders(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('guru.absensi.index'));
        $response->assertStatus(200);
        $response->assertSee('Data Absensi');
    }

    public function test_guru_presensi_renders(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('guru.presensi.index'));
        $response->assertStatus(200);
        $response->assertSee('Presensi');
    }

    public function test_guru_progres_views_render(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('guru.progres.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan KBM Harian');

        $response = $this->actingAs($this->guruUser)->get(route('guru.progres.create', ['id_jadwal' => $this->jadwal->id_jadwal]));
        $response->assertStatus(200);
        $response->assertSee('Input Laporan KBM Harian');

        $progres = ProgresMurid::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'materi_diajarkan' => 'Tangga nada C mayor',
            'catatan_perkembangan' => 'Bagus dan lancar',
        ]);

        $response = $this->actingAs($this->guruUser)->get(route('guru.progres.show', $this->spp->id_spp));
        $response->assertStatus(200);
        $response->assertSee('Histori Laporan KBM');

        $response = $this->actingAs($this->guruUser)->get(route('guru.progres.edit', $progres->id_progres));
        $response->assertStatus(200);
        $response->assertSee('Edit Laporan KBM');
    }

    public function test_guru_monthly_report_views_render(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('guru.monthly-report.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Bulanan');

        $report = MonthlyReport::create([
            'id_spp' => $this->spp->id_spp,
            'periode_bulan' => now()->startOfMonth()->toDateString(),
            'skor' => 'A',
            'evaluasi_bulanan' => 'Perkembangan sangat baik.',
        ]);

        $this->jadwal->update(['id_report' => $report->id_report]);

        $response = $this->actingAs($this->guruUser)->get(route('guru.monthly-report.show', $report->id_report));
        $response->assertStatus(200);
        $response->assertSee('Detail Laporan Bulanan');

        $response = $this->actingAs($this->guruUser)->get(route('guru.monthly-report.create', ['id_spp' => $this->spp->id_spp, 'bulan' => now()->format('Y-m')]));
        $response->assertStatus(200);
        $response->assertSee('Buat Laporan Bulanan');
    }

    public function test_guru_profil_renders(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('guru.profil.edit'));
        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
    }

    public function test_murid_dashboard_renders(): void
    {
        $response = $this->actingAs($this->muridUser)->get(route('murid.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_murid_jadwal_renders(): void
    {
        $response = $this->actingAs($this->muridUser)->get(route('murid.jadwal.index'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Kelas Saya');
    }

    public function test_murid_spp_renders(): void
    {
        $response = $this->actingAs($this->muridUser)->get(route('murid.spp.index'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat SPP');
    }

    public function test_murid_laporan_views_render(): void
    {
        $response = $this->actingAs($this->muridUser)->get(route('murid.laporan.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Bulanan');

        $report = MonthlyReport::create([
            'id_spp' => $this->spp->id_spp,
            'periode_bulan' => now()->startOfMonth()->toDateString(),
            'skor' => 'A',
            'evaluasi_bulanan' => 'Perkembangan sangat baik.',
        ]);

        $this->jadwal->update(['id_report' => $report->id_report]);

        $response = $this->actingAs($this->muridUser)->get(route('murid.laporan.show', $report->id_report));
        $response->assertStatus(200);
        $response->assertSee('Laporan');
    }

    public function test_murid_profil_renders(): void
    {
        $response = $this->actingAs($this->muridUser)->get(route('murid.profil.edit'));
        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
    }
}
