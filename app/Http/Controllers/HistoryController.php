<?php

namespace App\Http\Controllers;

use App\Services\HistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __construct(
        protected HistoryService $historyService
    ) {}

    /**
     * Render the main History of Science interactive multi-mode platform.
     */
    public function index(Request $request): View
    {
        $dataset = $this->historyService->getDataset();

        return view('history.index', [
            'ages' => $dataset['ages'],
            'nodes' => $dataset['nodes'],
            'threads' => $dataset['threads'],
            'quizzes' => $dataset['quizzes'],
            'stats' => $dataset['stats'],
            'initialMode' => $request->query('mode', 'timeline'),
            'initialThread' => $request->query('thread', null),
            'jsonPayload' => json_encode($dataset, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
        ]);
    }

    /**
     * Dedicated Deep-Dive Page for a specific discovery/experiment.
     */
    public function show(string $slug): View
    {
        $experiment = $this->historyService->findExperiment($slug);

        if (! $experiment) {
            abort(404, 'Discovery not found in historical record.');
        }

        $dataset = $this->historyService->getDataset();

        return view('history.show', [
            'experiment' => $experiment,
            'dataset' => $dataset,
        ]);
    }

    /**
     * Return JSON dataset for programmatic access or async requests.
     */
    public function api(): JsonResponse
    {
        return response()->json($this->historyService->getDataset());
    }
}
