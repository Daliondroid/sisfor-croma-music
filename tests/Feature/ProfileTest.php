<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Murid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Admin::create([
            'id_user' => $user->id_user,
            'nama_admin' => $user->name,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('admin.profil.edit'));

        $response->assertOk();
    }

    public function test_murid_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['role' => 'murid']);
        Murid::create([
            'id_user' => $user->id_user,
            'nama_murid' => $user->name,
            'status_aktif' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('murid.profil.edit'));

        $response->assertOk();
    }

    public function test_murid_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'murid']);
        $murid = Murid::create([
            'id_user' => $user->id_user,
            'nama_murid' => $user->name,
            'status_aktif' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('murid.profil.update'), [
                'nama_murid' => 'Updated Murid',
                'nomor_hp' => '08123456789',
                'alamat' => 'Jl. Test No. 1',
                'nama_orang_tua' => 'Ortu Test',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $murid->refresh();
        $this->assertSame('Updated Murid', $murid->nama_murid);
    }
}
