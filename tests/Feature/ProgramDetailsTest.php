<?php

namespace Tests\Feature;

use App\Models\ProgramKursus;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_all_10_programs_with_correct_pricing_and_keduanya(): void
    {
        $this->seed(DatabaseSeeder::class);

        $programs = ProgramKursus::all();
        $this->assertCount(10, $programs);

        // Group 1: 600.000
        $tier1 = ['Piano', 'Vokal', 'Gitar', 'Keyboard'];
        foreach ($tier1 as $progName) {
            $prog = ProgramKursus::where('nama_program', $progName)->first();
            $this->assertNotNull($prog, "Program {$progName} should exist");
            $this->assertEquals(600000, (int) $prog->biaya_kursus);
            $this->assertEquals('keduanya', $prog->tipe_les);
        }

        // Group 2: 650.000
        $tier2 = ['Drum', 'Bass', 'Saxophone'];
        foreach ($tier2 as $progName) {
            $prog = ProgramKursus::where('nama_program', $progName)->first();
            $this->assertNotNull($prog, "Program {$progName} should exist");
            $this->assertEquals(650000, (int) $prog->biaya_kursus);
            $this->assertEquals('keduanya', $prog->tipe_les);
        }

        // Group 3: 700.000
        $tier3 = ['Flute', 'Trumpet', 'Instrumen Lainnya'];
        foreach ($tier3 as $progName) {
            $prog = ProgramKursus::where('nama_program', $progName)->first();
            $this->assertNotNull($prog, "Program {$progName} should exist");
            $this->assertEquals(700000, (int) $prog->biaya_kursus);
            $this->assertEquals('keduanya', $prog->tipe_les);
        }
    }

    public function test_public_catalog_displays_all_10_programs_and_pricing_tiers(): void
    {
        $response = $this->get(route('instruments.index'));
        $response->assertStatus(200);

        // Check all 10 programs are present
        $response->assertSee('Piano');
        $response->assertSee('Vokal');
        $response->assertSee('Gitar');
        $response->assertSee('Keyboard');
        $response->assertSee('Drum');
        $response->assertSee('Bass');
        $response->assertSee('Saxophone');
        $response->assertSee('Flute');
        $response->assertSee('Trumpet');
        $response->assertSee('Instrumen Lainnya');

        // Check pricing figures
        $response->assertSee('600.000');
        $response->assertSee('650.000');
        $response->assertSee('700.000');

        // Check method tag
        $response->assertSee('Onsite & Home Visit');
    }

    public function test_program_detail_pages_render_methods_and_pricing(): void
    {
        $responsePiano = $this->get(route('instruments.show', 'piano'));
        $responsePiano->assertStatus(200);
        $responsePiano->assertSee('600.000');
        $responsePiano->assertSee('Onsite & Home Visit');

        $responseDrum = $this->get(route('instruments.show', 'drum'));
        $responseDrum->assertStatus(200);
        $responseDrum->assertSee('650.000');
        $responseDrum->assertSee('Onsite & Home Visit');

        $responseTrumpet = $this->get(route('instruments.show', 'trumpet'));
        $responseTrumpet->assertStatus(200);
        $responseTrumpet->assertSee('700.000');
        $responseTrumpet->assertSee('Onsite & Home Visit');
    }

    public function test_landing_page_renders_10_programs(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Piano');
        $response->assertSee('Trumpet');
        $response->assertSee('Instrumen Lainnya');
    }
}
