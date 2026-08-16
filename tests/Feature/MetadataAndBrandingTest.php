<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetadataAndBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $guruUser;

    protected User $muridUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        Admin::create([
            'id_user' => $this->adminUser->id_user,
            'nama_admin' => 'Admin Test',
        ]);

        $this->guruUser = User::factory()->create(['role' => 'guru']);
        Guru::create([
            'id_user' => $this->guruUser->id_user,
            'nama_guru' => 'Guru Test',
            'status_aktif' => true,
        ]);

        $this->muridUser = User::factory()->create(['role' => 'murid']);
        Murid::create([
            'id_user' => $this->muridUser->id_user,
            'nama_murid' => 'Murid Test',
            'status_aktif' => true,
        ]);
    }

    public function test_public_pages_follow_croma_music_title_pattern(): void
    {
        $responseHome = $this->get(route('home'));
        $responseHome->assertStatus(200);
        $responseHome->assertSee('<title>Beranda - Croma Music</title>', false);
        $responseHome->assertSee('images/croma_logo.jpg');

        $responseInstruments = $this->get(route('instruments.index'));
        $responseInstruments->assertStatus(200);
        $responseInstruments->assertSee('<title>Katalog Instrumen - Croma Music</title>', false);

        $responseInstrumentDetail = $this->get(route('instruments.show', 'piano'));
        $responseInstrumentDetail->assertStatus(200);
        $responseInstrumentDetail->assertSee('<title>Kursus Piano - Croma Music</title>', false);

        $responseMentors = $this->get(route('mentors.index'));
        $responseMentors->assertStatus(200);
        $responseMentors->assertSee('<title>Direktori Mentor - Croma Music</title>', false);

        $responseMentorProfile = $this->get(route('mentors.show', 'kak-budi'));
        $responseMentorProfile->assertStatus(200);
        $responseMentorProfile->assertSee('<title>Profil Kak Budi - Croma Music</title>', false);
    }

    public function test_dashboard_pages_follow_cromis_title_pattern(): void
    {
        $responseAdmin = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('<title>Dashboard Admin - CROMIS</title>', false);
        $responseAdmin->assertSee('images/croma_logo.jpg');

        $responseGuru = $this->actingAs($this->guruUser)->get(route('guru.dashboard'));
        $responseGuru->assertStatus(200);
        $responseGuru->assertSee('<title>Dashboard Guru - CROMIS</title>', false);

        $responseMurid = $this->actingAs($this->muridUser)->get(route('murid.dashboard'));
        $responseMurid->assertStatus(200);
        $responseMurid->assertSee('<title>Dashboard Murid - CROMIS</title>', false);

        auth()->logout();
        $responseLogin = $this->get(route('login'));
        $responseLogin->assertStatus(200);
        $responseLogin->assertSee('<title>Login - CROMIS</title>', false);
        $responseLogin->assertSee('images/croma_logo.jpg');
    }

    public function test_sitemap_xml_exists_and_is_valid(): void
    {
        $sitemapPath = public_path('sitemap.xml');
        $this->assertFileExists($sitemapPath);

        $content = file_get_contents($sitemapPath);
        $xml = simplexml_load_string($content);
        $this->assertNotFalse($xml, 'sitemap.xml must be valid XML');
        $this->assertGreaterThan(5, count($xml->url));
    }

    public function test_404_error_page_renders_cleanly_with_branding(): void
    {
        $response = $this->get('/non-existent-random-route-xyz-404');
        $response->assertStatus(404);
        $response->assertSee('404 Halaman Tidak Ditemukan - Croma Music', false);
        $response->assertSee('images/croma_logo.jpg');
    }
}
