<?php

namespace App\Http\Controllers;

use App\Services\HistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __construct(
        protected HistoryService $historyService
    ) {}

    /**
     * Render the main History of Science interactive page.
     */
    public function index(): View
    {
        $dataset = $this->historyService->getDataset();

        return view('history.index', [
            'ages' => $dataset['ages'],
            'nodes' => $dataset['nodes'],
            'stats' => $dataset['stats'],
            'jsonPayload' => json_encode($dataset, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
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
