<?php

namespace Tests\Feature;

use App\Logging\SanitizeLogProcessor;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\ProgramKursus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\TestCase;

class ObservabilityLayer7Test extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that all HTTP responses include an X-Request-ID header.
     */
    public function test_http_response_contains_x_request_id_header(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Request-ID');
        $this->assertTrue(Str::isUuid($response->headers->get('X-Request-ID')));
    }

    /**
     * Test that an incoming X-Request-ID is preserved and propagated.
     */
    public function test_incoming_x_request_id_is_propagated(): void
    {
        $customTraceId = (string) Str::uuid();

        $response = $this->withHeaders([
            'X-Request-ID' => $customTraceId,
        ])->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Request-ID', $customTraceId);
    }

    /**
     * Test that SanitizeLogProcessor masks sensitive keys in log context.
     */
    public function test_sanitize_log_processor_masks_sensitive_data(): void
    {
        $processor = new SanitizeLogProcessor;

        $record = new LogRecord(
            datetime: new \DateTimeImmutable,
            channel: 'test',
            level: Level::Info,
            message: 'User authentication test',
            context: [
                'email' => 'admin@croma.com',
                'password' => 'secret_password_123',
                'password_confirmation' => 'secret_password_123',
                'remember_token' => 'sensitive_token_abc',
                'no_hp' => '081234567890',
                'nested' => [
                    'api_key' => 'live_sk_12345678',
                    'regular_field' => 'visible_value',
                ],
            ]
        );

        $sanitizedRecord = $processor($record);

        $this->assertEquals('[REDACTED]', $sanitizedRecord->context['password']);
        $this->assertEquals('[REDACTED]', $sanitizedRecord->context['password_confirmation']);
        $this->assertEquals('[REDACTED]', $sanitizedRecord->context['remember_token']);
        $this->assertEquals('[REDACTED]', $sanitizedRecord->context['no_hp']);
        $this->assertEquals('[REDACTED]', $sanitizedRecord->context['nested']['api_key']);
        $this->assertEquals('visible_value', $sanitizedRecord->context['nested']['regular_field']);
        $this->assertEquals('admin@croma.com', $sanitizedRecord->context['email']);
    }

    /**
     * Test that JadwalController logs exception and returns sanitized error message.
     */
    public function test_jadwal_controller_catches_and_sanitizes_errors(): void
    {
        $adminUser = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        // Do not create Admin profile to trigger the exception in JadwalController@store
        $this->actingAs($adminUser);

        $guruUser = User::factory()->create(['role' => 'guru', 'is_active' => true]);
        $guru = Guru::create([
            'id_user' => $guruUser->id_user,
            'nama_guru' => 'Guru Test',
            'no_hp' => '0811111111',
            'status_aktif' => true,
        ]);

        $muridUser = User::factory()->create(['role' => 'murid', 'is_active' => true]);
        $murid = Murid::create([
            'id_user' => $muridUser->id_user,
            'nama_murid' => 'Murid Test',
            'status_aktif' => true,
        ]);

        $program = ProgramKursus::create([
            'nama_program' => 'Gitar Klasik',
            'biaya_kursus' => 500000,
            'tipe_les' => 'onsite',
        ]);

        $response = $this->post(route('admin.jadwals.store'), [
            'id_murid' => $murid->id_murid,
            'id_guru' => $guru->id_guru,
            'id_program' => $program->id_program,
            'tipe_les' => 'Onsite',
            'total_sesi' => 4,
            'tanggal_mulai' => now()->toDateString(),
            'tipe_jadwal' => 'tetap',
            'pola_tunggal' => [
                'hari' => 'Senin',
                'jam_mulai' => '14:00',
                'jam_selesai' => '15:00',
            ],
        ]);

        $response->assertSessionHasErrors('error');
        $errorMsg = session('errors')->get('error')[0];

        // Ensure raw exception string is not exposed
        $this->assertStringContainsString('gagal diproses oleh sistem', $errorMsg);
        $this->assertStringNotContainsString('SQLSTATE', $errorMsg);
    }

    /**
     * Test that error views render with Croma Music branding.
     */
    public function test_custom_error_views_render(): void
    {
        $view403 = view('errors.403')->render();
        $this->assertStringContainsString('403', $view403);
        $this->assertStringContainsString('Akses Ditolak', $view403);

        $view404 = view('errors.404')->render();
        $this->assertStringContainsString('404', $view404);
        $this->assertStringContainsString('Halaman Tidak Ditemukan', $view404);

        $view419 = view('errors.419')->render();
        $this->assertStringContainsString('419', $view419);
        $this->assertStringContainsString('Sesi Telah Kedaluwarsa', $view419);

        $view429 = view('errors.429')->render();
        $this->assertStringContainsString('429', $view429);
        $this->assertStringContainsString('Batas Permintaan Terlampaui', $view429);

        $view500 = view('errors.500')->render();
        $this->assertStringContainsString('500', $view500);
        $this->assertStringContainsString('Terjadi Gangguan Server', $view500);

        $view503 = view('errors.503')->render();
        $this->assertStringContainsString('503', $view503);
        $this->assertStringContainsString('Pemeliharaan Sistem Sedang Berlangsung', $view503);
    }
}
