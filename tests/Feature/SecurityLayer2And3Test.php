<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\HonorGuru;
use App\Models\Murid;
use App\Models\ProgramKursus;
use App\Models\Spp;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityLayer2And3Test extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_user_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
            'role' => 'murid',
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_deactivated_session_is_terminated_by_role_middleware(): void
    {
        $user = User::factory()->create([
            'role' => 'murid',
            'is_active' => true,
        ]);
        Murid::create([
            'id_user' => $user->id_user,
            'nama_murid' => $user->name,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($user)->get(route('murid.dashboard'));
        $response->assertOk();

        // Deactivate user
        $user->update(['is_active' => false]);

        $responseAfterDeactivation = $this->actingAs($user->fresh())->get(route('murid.dashboard'));
        $responseAfterDeactivation->assertForbidden();
    }

    public function test_public_registration_creates_murid_record_atomically(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Student',
            'email' => 'newstudent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'newstudent@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('murid', $user->role);

        $murid = Murid::where('id_user', $user->id_user)->first();
        $this->assertNotNull($murid);
        $this->assertEquals('New Student', $murid->nama_murid);
        $this->assertTrue($murid->status_aktif);
    }

    public function test_honor_guru_proof_is_stored_on_private_disk_and_streamed_to_admin(): void
    {
        Storage::fake('local');

        $adminUser = User::factory()->create(['role' => 'admin']);
        $admin = Admin::create([
            'id_user' => $adminUser->id_user,
            'nama_admin' => 'Admin Boss',
        ]);

        $guruUser = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'id_user' => $guruUser->id_user,
            'nama_guru' => 'Teacher One',
            'status_aktif' => true,
        ]);

        $honor = HonorGuru::create([
            'id_guru' => $guru->id_guru,
            'id_admin' => $admin->id_admin,
            'jumlah_pertemuan' => 4,
            'jumlah_honor' => 500000,
            'status_bayar' => 'Belum Lunas',
        ]);

        $file = UploadedFile::fake()->create('transfer_receipt.pdf', 100, 'application/pdf');

        $response = $this->actingAs($adminUser)->put(route('admin.honor-guru.update', $honor), [
            'jumlah_honor' => 500000,
            'status_bayar' => 'Lunas',
            'file_bukti_transfer' => $file,
        ]);

        $response->assertRedirect(route('admin.honor-guru.index'));

        $honor->refresh();
        $this->assertNotNull($honor->file_bukti_transfer);
        Storage::disk('local')->assertExists($honor->file_bukti_transfer);

        // Admin streams the proof
        $streamResponse = $this->actingAs($adminUser)->get(route('admin.honor-guru.bukti', $honor));
        $streamResponse->assertOk();
        $streamResponse->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_student_cannot_view_another_students_spp_proof(): void
    {
        Storage::fake('local');

        $student1User = User::factory()->create(['role' => 'murid']);
        $student1 = Murid::create([
            'id_user' => $student1User->id_user,
            'nama_murid' => 'Student 1',
            'status_aktif' => true,
        ]);

        $student2User = User::factory()->create(['role' => 'murid']);
        $student2 = Murid::create([
            'id_user' => $student2User->id_user,
            'nama_murid' => 'Student 2',
            'status_aktif' => true,
        ]);

        $program = ProgramKursus::create([
            'nama_program' => 'Piano Master',
            'tipe_les' => 'onsite',
            'biaya_kursus' => 350000,
            'is_active' => true,
        ]);

        $spp1 = Spp::create([
            'id_murid' => $student1->id_murid,
            'id_program' => $program->id_program,
            'periode_tagihan' => '2026-08-01',
            'nominal_tagihan' => 350000,
            'tanggal_jatuh_tempo' => '2026-08-31',
            'tipe_les' => 'Onsite',
            'status_bayar' => 'Belum Lunas',
        ]);

        $filePath = 'bukti_transfer/test_proof.pdf';
        Storage::disk('local')->put($filePath, 'fake content');

        Transaksi::create([
            'id_spp' => $spp1->id_spp,
            'file_bukti_transfer' => $filePath,
            'nominal_bayar' => 350000,
            'tanggal_bayar' => '2026-08-10',
        ]);

        // Student 1 views own proof -> 200 OK
        $res1 = $this->actingAs($student1User)->get(route('murid.spp.view-bukti', $spp1));
        $res1->assertOk();

        // Student 2 attempts to view Student 1's proof -> 403 Forbidden
        $res2 = $this->actingAs($student2User)->get(route('murid.spp.view-bukti', $spp1));
        $res2->assertForbidden();
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }
}
