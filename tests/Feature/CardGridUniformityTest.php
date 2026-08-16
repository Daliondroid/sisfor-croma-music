<?php

namespace Tests\Feature;

use Tests\TestCase;

class CardGridUniformityTest extends TestCase
{
    public function test_instrument_catalog_has_uniform_grid_and_card_structure(): void
    {
        $response = $this->get(route('instruments.index'));
        $response->assertStatus(200);

        // Grid class check
        $response->assertSee('instrument-catalog-grid grid-4');
        $response->assertSee('instrument-card');
        $response->assertSee('instrument-img-wrapper');
    }

    public function test_mentor_directory_has_uniform_grid_and_card_structure(): void
    {
        $response = $this->get(route('mentors.index'));
        $response->assertStatus(200);

        // Grid and card check
        $response->assertSee('grid-4 program-flex');
        $response->assertSee('tutor-card');
        $response->assertSee('tutor-img-wrapper');
        $response->assertSee('tutor-avatar-placeholder');
    }

    public function test_instrument_detail_mentors_use_standard_grid_and_card_structure(): void
    {
        $response = $this->get(route('instruments.show', 'piano'));
        $response->assertStatus(200);

        // Check grid-4 and tutor-card with avatar placeholder
        $response->assertSee('grid-4 program-flex');
        $response->assertSee('tutor-card');
        $response->assertSee('tutor-img-wrapper');
        $response->assertSee('tutor-avatar-placeholder');
    }
}
