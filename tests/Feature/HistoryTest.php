<?php

namespace Tests\Feature;

use App\Services\HistoryService;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    /**
     * Test the main interactive History of Science timeline route.
     */
    public function test_history_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Cosmic Lineage');
        $response->assertSee('The Architecture of Scientific Revolutions');
        $response->assertSee('James Clerk Maxwell');
        $response->assertSee('Thales of Miletus');
    }

    /**
     * Test the JSON API endpoint returns accurate structured dataset.
     */
    public function test_history_api_returns_complete_dataset(): void
    {
        $response = $this->getJson('/api/history');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'ages' => [
                '*' => ['id', 'slug', 'name', 'period', 'subtitle', 'macro_shift', 'color'],
            ],
            'nodes' => [
                '*' => ['id', 'name', 'year', 'display_year', 'age_id', 'type', 'question', 'discovery', 'experiment_details', 'connections', 'ancestors', 'descendants'],
            ],
            'stats' => [
                'total_nodes',
                'total_ages',
                'observations',
                'experiments',
                'theories',
                'total_connections',
                'refutations',
                'confirmations',
            ],
        ]);

        $data = $response->json();
        $this->assertCount(10, $data['ages']);
        $this->assertGreaterThanOrEqual(40, count($data['nodes']));
        $this->assertGreaterThan(0, $data['stats']['refutations']);
        $this->assertGreaterThan(0, $data['stats']['confirmations']);
    }

    /**
     * Test that HistoryService enriches bidirectional ancestor and descendant links.
     */
    public function test_history_service_bidirectional_enrichment(): void
    {
        $service = app(HistoryService::class);
        $dataset = $service->getDataset();

        // Galileo / Ørsted / Volta / Galvani check
        // Volta refuted Galvani; Galvani should list Volta as a descendant with type 'refutes'
        $galvani = collect($dataset['nodes'])->firstWhere('id', 'luigi_galvani');
        $this->assertNotNull($galvani);
        
        $hasVoltaRefutation = collect($galvani['descendants'])->contains(function ($desc) {
            return $desc['source_id'] === 'alessandro_volta' && $desc['type'] === 'refutes';
        });
        $this->assertTrue($hasVoltaRefutation, 'Galvani must have Volta listed as a refuting descendant');

        // Maxwell should be confirmed by Hertz
        $maxwell = collect($dataset['nodes'])->firstWhere('id', 'james_clerk_maxwell');
        $this->assertNotNull($maxwell);
        
        $hasHertzConfirmation = collect($maxwell['descendants'])->contains(function ($desc) {
            return $desc['source_id'] === 'heinrich_hertz' && $desc['type'] === 'confirms';
        });
        $this->assertTrue($hasHertzConfirmation, 'Maxwell must be confirmed by Heinrich Hertz');
    }
}
