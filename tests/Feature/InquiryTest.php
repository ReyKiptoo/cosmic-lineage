<?php

namespace Tests\Feature;

use App\Models\Age;
use App\Models\Experiment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test the Genesis Inquiry Canvas page loads successfully.
     */
    public function test_inquiry_page_loads_successfully(): void
    {
        $response = $this->get('/inquiry');

        $response->assertStatus(200);
        $response->assertSee('Cosmic Lineage');
        $response->assertSee('Inquiry Canvas');
        $response->assertSee('Where Shall Our Inquiry Begin?');
        $response->assertSee('The Amber Spark');
        $response->assertSee('Lightning as Cosmic Spark');
        $response->assertSee('The Chemical Current');
    }

    /**
     * Test forging a new inquiry block via heuristic fallback.
     */
    public function test_forge_inquiry_block_returns_all_five_facets(): void
    {
        $response = $this->postJson('/api/inquiry/forge', [
            'prompt' => 'Why does amber attract feathers when rubbed with fur?',
            'priors' => ['Static charges can accumulate on insulative matter.'],
            'parent_id' => null,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $block = $response->json('block');
        $this->assertNotNull($block);
        $this->assertArrayHasKey('id', $block);
        $this->assertArrayHasKey('title', $block);
        $this->assertArrayHasKey('hypothesis', $block);
        $this->assertArrayHasKey('things_we_know', $block);
        $this->assertArrayHasKey('experiment', $block);
        $this->assertArrayHasKey('results', $block);
        $this->assertArrayHasKey('next_paths', $block);

        // Verify the 5 facets are populated
        $this->assertNotEmpty($block['hypothesis']);
        $this->assertIsArray($block['things_we_know']);
        $this->assertArrayHasKey('apparatus', $block['experiment']);
        $this->assertArrayHasKey('method', $block['experiment']);
        $this->assertArrayHasKey('observation', $block['results']);
        $this->assertArrayHasKey('lesson', $block['results']);
        $this->assertArrayHasKey('significance', $block['results']);

        // Verify 3 next paths
        $this->assertCount(3, $block['next_paths']);
        $types = array_column($block['next_paths'], 'type');
        $this->assertContains('validate', $types);
        $this->assertContains('horizon', $types);
        $this->assertContains('paradox', $types);
    }

    /**
     * Test challenging an established sealed block.
     */
    public function test_challenge_block_evaluates_anomaly(): void
    {
        $block = [
            'title' => 'Newtonian Gravitation',
            'hypothesis' => 'Gravity exerts an instantaneous force at a distance across space.',
            'things_we_know' => ['Planetary orbits follow inverse-square laws.'],
            'results' => [
                'lesson' => 'Mass attracts mass with a force proportional to product of masses and inversely proportional to square of distance.'
            ]
        ];

        $response = $this->postJson('/api/inquiry/challenge', [
            'block' => $block,
            'counter_evidence' => 'Nothing can travel faster than the speed of light, so gravitational influence cannot propagate instantaneously.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $evaluation = $response->json('evaluation');
        $this->assertNotNull($evaluation);
        $this->assertArrayHasKey('verdict', $evaluation);
        $this->assertArrayHasKey('analysis', $evaluation);
    }
}
