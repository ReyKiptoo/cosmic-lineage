<?php

namespace App\Services;

use App\Models\Age;
use App\Models\Connection;
use App\Models\Experiment;
use App\Models\Person;
use Illuminate\Support\Collection;

class HistoryService
{
    protected ?array $cachedData = null;

    /**
     * Get the full enriched dataset, including experiments, ages, people, connections, threads, and quizzes.
     */
    public function getDataset(): array
    {
        if ($this->cachedData !== null) {
            return $this->cachedData;
        }

        // Read threads & quizzes from knowledge_graph.json if present
        $jsonPath = database_path('data/knowledge_graph.json');
        $rawJson = file_exists($jsonPath) ? json_decode((string) file_get_contents($jsonPath), true) : [];
        $threads = $rawJson['threads'] ?? [];
        $quizzes = $rawJson['quizzes'] ?? [];

        // Load Eloquent models with relationships
        $ages = Age::orderBy('id')->get()->toArray();
        $people = Person::all()->keyBy('id');
        $rawConnections = Connection::all();

        $experiments = Experiment::with(['age', 'person'])->orderBy('year')->get();

        // Build bidirectional link map
        $ancestorsMap = [];
        $descendantsMap = [];

        foreach ($rawConnections as $conn) {
            $ancestorsMap[$conn->source_experiment_id][] = [
                'target_id' => $conn->target_experiment_id,
                'type' => $conn->type,
                'description' => $conn->description,
            ];

            $descendantsMap[$conn->target_experiment_id][] = [
                'source_id' => $conn->source_experiment_id,
                'type' => $conn->type,
                'description' => $conn->description,
            ];
        }

        // Enrich experiment records
        $enrichedExperiments = $experiments->map(function (Experiment $exp) use ($people, $ancestorsMap, $descendantsMap, $experiments) {
            $expArray = $exp->toArray();
            
            // Person display attributes
            $person = $people->get($exp->person_id);
            $expArray['person_name'] = $exp->person_displayName;
            $expArray['person_nationality'] = $person?->nationality ?? 'Historical Scholar';
            $expArray['person_lifespan'] = $person?->lifespan_display ?? '';

            // Roots & Fruits
            $ancestors = $ancestorsMap[$exp->id] ?? [];
            $descendants = $descendantsMap[$exp->id] ?? [];

            // Attach source & target names
            $expArray['connections'] = collect($ancestors)->map(function ($conn) use ($experiments) {
                $target = $experiments->firstWhere('id', $conn['target_id']);
                $conn['target_name'] = $target?->person_displayName ?? $conn['target_id'];
                $conn['target_title'] = $target?->title ?? '';
                $conn['target_year'] = $target?->display_year ?? '';
                return $conn;
            })->all();

            $expArray['descendants'] = collect($descendants)->map(function ($desc) use ($experiments) {
                $source = $experiments->firstWhere('id', $desc['source_id']);
                $desc['source_name'] = $source?->person_displayName ?? $desc['source_id'];
                $desc['source_title'] = $source?->title ?? '';
                $desc['source_year'] = $source?->display_year ?? '';
                return $desc;
            })->all();

            return $expArray;
        });

        // Statistics
        $stats = [
            'total_nodes' => $enrichedExperiments->count(),
            'total_ages' => count($ages),
            'observations' => $enrichedExperiments->where('type', 'observation')->count(),
            'experiments' => $enrichedExperiments->whereIn('type', ['experiment', 'invention'])->count(),
            'theories' => $enrichedExperiments->where('type', 'theory')->count(),
            'total_connections' => $rawConnections->count(),
            'refutations' => $rawConnections->where('type', 'refutes')->count(),
            'confirmations' => $rawConnections->where('type', 'confirms')->count(),
        ];

        $this->cachedData = [
            'ages' => $ages,
            'nodes' => $enrichedExperiments->values()->all(),
            'people' => $people->values()->all(),
            'threads' => $threads,
            'quizzes' => $quizzes,
            'stats' => $stats,
        ];

        return $this->cachedData;
    }

    /**
     * Find single experiment by ID or slug with deep relations.
     */
    public function findExperiment(string $id): ?array
    {
        $dataset = $this->getDataset();
        return collect($dataset['nodes'])->firstWhere('id', $id);
    }
}
