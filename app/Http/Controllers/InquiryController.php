<?php

namespace App\Http\Controllers;

use App\Models\Experiment;
use App\Services\GeminiInquiryService;
use App\Services\HistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function __construct(
        protected GeminiInquiryService $geminiService,
        protected HistoryService $historyService
    ) {}

    /**
     * Render the Genesis Inquiry Canvas page.
     */
    public function index(Request $request): View
    {
        $dataset = $this->historyService->getDataset();

        // Curated foundation stones for zero-friction jumping off
        $starters = [
            [
                'id' => 'starter_thales',
                'title' => 'The Amber Spark',
                'era' => '600 BC · Classical Antiquity',
                'question' => 'Why does rubbed amber attract lightweight straw and feathers?',
                'hint' => 'Begin at the dawn of electrostatic observation.',
                'color' => '#f59e0b',
            ],
            [
                'id' => 'starter_franklin',
                'title' => 'Lightning as Cosmic Spark',
                'era' => '1751 · The Enlightenment',
                'question' => 'Is thundercloud lightning identical to artificial electric sparks produced in the lab?',
                'hint' => 'Unify celestial atmospheric power with laboratory electricity.',
                'color' => '#38bdf8',
            ],
            [
                'id' => 'starter_volta',
                'title' => 'The Chemical Current',
                'era' => '1800 · Industrial Genesis',
                'question' => 'Can stacked dissimilar metals produce a continuous, inexhaustible electric flow?',
                'hint' => 'Transition from brief static sparks to perpetual currents.',
                'color' => '#10b981',
            ],
            [
                'id' => 'starter_oersted',
                'title' => 'The Deflected Needle',
                'era' => '1820 · The Great Unification',
                'question' => 'Does an active electric current exert a directional force on a magnetic needle?',
                'hint' => 'Discover the invisible geometric link between electricity and magnetism.',
                'color' => '#ec4899',
            ],
            [
                'id' => 'starter_maxwell',
                'title' => 'Electromagnetic Light',
                'era' => '1865 · Field Symphony',
                'question' => 'Are light waves nothing other than undulating ripples of electric and magnetic fields?',
                'hint' => 'Prove that optical vision and electrical force are the same phenomenon.',
                'color' => '#8b5cf6',
            ],
        ];

        return view('history.inquiry', [
            'starters' => $starters,
            'dataset' => $dataset,
            'hasServerKey' => ! empty(config('services.gemini.key')),
            'serverModel' => config('services.gemini.model', 'gemini-2.5-flash'),
        ]);
    }

    /**
     * Forge a new 5-facet inquiry block using Gemini AI or heuristic fallback.
     */
    public function forge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:1000',
            'priors' => 'nullable|array',
            'parent_id' => 'nullable|string|max:100',
        ]);

        $userApiKey = $request->header('X-Gemini-Key') ?: $request->input('api_key');

        $block = $this->geminiService->forgeInquiry(
            prompt: $validated['prompt'],
            priors: $validated['priors'] ?? [],
            parentBlockId: $validated['parent_id'] ?? null,
            userApiKey: $userApiKey
        );

        return response()->json([
            'status' => 'success',
            'block' => $block,
        ]);
    }

    /**
     * Challenge an established sealed foundation block.
     */
    public function challenge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'block' => 'required|array',
            'counter_evidence' => 'required|string|max:2000',
        ]);

        $userApiKey = $request->header('X-Gemini-Key') ?: $request->input('api_key');

        $evaluation = $this->geminiService->challengeBlock(
            blockData: $validated['block'],
            counterEvidence: $validated['counter_evidence'],
            userApiKey: $userApiKey
        );

        return response()->json([
            'status' => 'success',
            'evaluation' => $evaluation,
        ]);
    }
}
