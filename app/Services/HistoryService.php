<?php

namespace App\Services;

use Illuminate\Support\Collection;
use RuntimeException;

class HistoryService
{
    protected ?array $cachedData = null;

    /**
     * Path to the JSON dataset file.
     */
    protected string $dataPath;

    public function __construct(?string $dataPath = null)
    {
        $this->dataPath = $dataPath ?? database_path('data/history_of_science.json');
    }

    /**
     * Load, enrich, and return all ages and nodes.
     *
     * @return array{ages: array, nodes: array, stats: array}
     */
    public function getDataset(): array
    {
        if ($this->cachedData !== null) {
            return $this->cachedData;
        }

        if (! file_exists($this->dataPath)) {
            throw new RuntimeException("History dataset not found at {$this->dataPath}");
        }

        $raw = json_decode((string) file_get_contents($this->dataPath), true);

        if (! is_array($raw) || ! isset($raw['ages'], $raw['nodes'])) {
            throw new RuntimeException('Invalid history dataset format.');
        }

        $nodesById = collect($raw['nodes'])->keyBy('id');

        // Bidirectional enrichment: attach incoming connections (descendants)
        // so every node knows both what it builds on and what built on it later.
        $enrichedNodes = $nodesById->map(function (array $node) use ($nodesById) {
            $node['ancestors'] = $node['connections'] ?? [];
            $node['descendants'] = [];

            return $node;
        });

        foreach ($enrichedNodes as $sourceId => $node) {
            foreach ($node['connections'] ?? [] as $connection) {
                $targetId = $connection['target_id'] ?? null;
                if ($targetId && $enrichedNodes->has($targetId)) {
                    $target = $enrichedNodes->get($targetId);
                    $target['descendants'][] = [
                        'source_id' => $sourceId,
                        'source_name' => $node['name'],
                        'source_year' => $node['display_year'],
                        'type' => $connection['type'],
                        'description' => $connection['description'] ?? '',
                    ];
                    $enrichedNodes->put($targetId, $target);
                }
            }
        }

        $nodeList = $enrichedNodes->values()->all();

        // Calculate statistics
        $stats = [
            'total_nodes' => count($nodeList),
            'total_ages' => count($raw['ages']),
            'observations' => collect($nodeList)->where('type', 'observation')->count(),
            'experiments' => collect($nodeList)->where('type', 'experiment')->count(),
            'theories' => collect($nodeList)->where('type', 'theory')->count(),
            'total_connections' => collect($nodeList)->sum(fn ($n) => count($n['connections'] ?? [])),
            'refutations' => collect($nodeList)->flatMap(fn ($n) => $n['connections'] ?? [])->where('type', 'refutes')->count(),
            'confirmations' => collect($nodeList)->flatMap(fn ($n) => $n['connections'] ?? [])->where('type', 'confirms')->count(),
        ];

        $this->cachedData = [
            'ages' => $raw['ages'],
            'nodes' => $nodeList,
            'stats' => $stats,
        ];

        return $this->cachedData;
    }

    /**
     * Get nodes grouped by Age ID.
     */
    public function getNodesByAge(): Collection
    {
        return collect($this->getDataset()['nodes'])->groupBy('age_id');
    }

    /**
     * Find a node by ID.
     */
    public function findNode(string $id): ?array
    {
        return collect($this->getDataset()['nodes'])->firstWhere('id', $id);
    }
}
