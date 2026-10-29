<?php

namespace Tests\Feature;

use App\Models\Age;
use App\Models\Connection;
use App\Models\Experiment;
use App\Models\Person;
use App\Services\HistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test the main interactive History of Science timeline route.
     */
    public function test_history_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Cosmic Lineage');
        $response->assertSee('The Experiment-Centric Knowledge Graph');
        $response->assertSee('Quest Mode');
        $response->assertSee('Storylines');
        $response->assertSee('Film Playback');
        $response->assertSee('Book Mode');
        $response->assertSee('Quiz');
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
                '*' => ['id', 'slug', 'name', 'period', 'subtitle', 'paradigm_shift', 'color'],
            ],
            'nodes' => [
                '*' => ['id', 'title', 'type', 'year', 'display_year', 'age_id', 'prior_knowledge', 'question', 'method', 'conclusion', 'significance', 'next_paths', 'connections', 'descendants'],
            ],
            'threads' => [
                '*' => ['id', 'title', 'subtitle', 'experiment_ids'],
            ],
            'quizzes' => [
                '*' => ['id', 'age_id', 'title', 'question', 'options', 'correct_index', 'explanation'],
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
        $this->assertCount(47, $data['nodes']);
        $this->assertCount(4, $data['threads']);
        $this->assertCount(6, $data['quizzes']);
        $this->assertEquals(64, $data['stats']['total_connections']);
    }

    /**
     * Test dedicated discovery show page with next possible paths.
     */
    public function test_discovery_show_page_loads_successfully(): void
    {
        $response = $this->get('/discovery/orsted_electromagnetism');

        $response->assertStatus(200);
        $response->assertSee('Discovery of Electromagnetism');
        $response->assertSee('Hans Christian Ørsted');
        $response->assertSee('Prior Mental Model');
        $response->assertSee('Apparatus & Experimental Steps', false);
        $response->assertSee('Where Does This Discovery Lead Next?');
        $response->assertSee('Wait, How Is This Possible?!');
    }

    /**
     * Test discovery show returns 404 for invalid slug.
     */
    public function test_discovery_show_page_404_on_invalid_slug(): void
    {
        $response = $this->get('/discovery/non_existent_milestone');

        $response->assertStatus(404);
    }

    /**
     * Test that database models are properly populated and linked.
     */
    public function test_database_models_integrity(): void
    {
        $this->assertEquals(10, Age::count());
        $this->assertEquals(47, Person::count());
        $this->assertEquals(47, Experiment::count());
        $this->assertEquals(64, Connection::count());

        $faraday = Experiment::with(['outgoingConnections', 'incomingConnections', 'age', 'person'])->find('faraday_electromagnetic_induction');
        $this->assertNotNull($faraday);
        $this->assertEquals('Michael Faraday', $faraday->person->name);
        $this->assertCount(2, $faraday->outgoingConnections);
        $this->assertGreaterThan(0, $faraday->incomingConnections->count());
        $this->assertNotNull($faraday->next_paths);
        $this->assertArrayHasKey('confirm_question', $faraday->next_paths);
        $this->assertArrayHasKey('new_question', $faraday->next_paths);
        $this->assertArrayHasKey('paradox_question', $faraday->next_paths);
    }
}
