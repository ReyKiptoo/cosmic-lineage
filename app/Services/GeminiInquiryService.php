<?php

namespace App\Services;

use App\Models\Experiment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiInquiryService
{
    protected ?string $defaultApiKey;
    protected string $defaultModel;

    public function __construct()
    {
        $this->defaultApiKey = config('services.gemini.key');
        $this->defaultModel = config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Forge an Inquiry Building Block from a question/hypothesis and prior foundations.
     */
    public function forgeInquiry(string $prompt, array $priors = [], ?string $parentBlockId = null, ?string $userApiKey = null): array
    {
        $apiKey = $userApiKey ?: $this->defaultApiKey;

        if ($apiKey) {
            try {
                $block = $this->callGeminiForBlock($prompt, $priors, $apiKey);
                if ($block) {
                    $block['id'] = 'blk_' . Str::random(10);
                    $block['parent_id'] = $parentBlockId;
                    $block['created_at'] = now()->toIso8601String();
                    $block['status'] = 'draft';
                    $block['sealed'] = false;
                    $block['challenged'] = false;
                    $block['challenge_notes'] = null;
                    $block['source'] = 'gemini';
                    return $block;
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini API call failed, falling back to heuristic synthesis: ' . $e->getMessage());
            }
        }

        // Offline / Heuristic fallback when no key is set or API call fails
        $block = $this->heuristicFallback($prompt, $priors, $parentBlockId);
        $block['source'] = $apiKey ? 'heuristic_fallback' : 'heuristic';
        return $block;
    }

    /**
     * Evaluate a counter-evidence challenge against an established sealed block.
     */
    public function challengeBlock(array $blockData, string $counterEvidence, ?string $userApiKey = null): array
    {
        $apiKey = $userApiKey ?: $this->defaultApiKey;

        if ($apiKey) {
            try {
                $evaluation = $this->callGeminiForChallenge($blockData, $counterEvidence, $apiKey);
                if ($evaluation) {
                    return $evaluation;
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini API challenge call failed: ' . $e->getMessage());
            }
        }

        return $this->heuristicChallengeFallback($blockData, $counterEvidence);
    }

    /**
     * Call Gemini API to generate the 5-facet building block.
     */
    protected function callGeminiForBlock(string $prompt, array $priors, string $apiKey): ?array
    {
        $systemInstruction = <<<EOT
You are an epistemological thought engine for 'Cosmic Lineage', an interactive platform where users reconstruct scientific discoveries from first principles in modular building blocks.
Each inquiry building block represents a testable truth or experiment and consists of 5 core facets:
1. Hypothesis: The core question or assumption being tested.
2. Things We Know: Accepted foundational priors required to reach this stage.
3. The Experiment: The physical apparatus, setup, and procedure to test the hypothesis.
4. The Results: What was observed, what fundamental physical principle was learned, and its significance.
5. Next Possible Paths: Exactly 3 divergent questions:
   - "validate": A question to test boundaries and deepen certainty.
   - "horizon": A new frontier question branching out into new phenomena.
   - "paradox": A provocative anomaly question ("Wait, how is this possible?!").

Return strictly valid JSON with no markdown wrapping or code fences.
JSON Schema:
{
  "title": "Short poetic/scientific title (3-6 words)",
  "era_context": "Estimated historical era or conceptual epoch (e.g. Classical Antiquity, Scientific Revolution, 19th Century Electrodynamics)",
  "hypothesis": "1-2 sentences stating the hypothesis/inquiry clearly",
  "things_we_know": [
    "Prior fact 1",
    "Prior fact 2",
    "Prior fact 3"
  ],
  "experiment": {
    "apparatus": "Physical materials or apparatus needed",
    "method": "Step-by-step physical procedure to observe nature",
    "variable_tested": "The specific variable isolated or altered"
  },
  "results": {
    "observation": "What actually happens in physical reality",
    "lesson": "The core physical law or principle deduced",
    "significance": "Why this alters our understanding of reality"
  },
  "next_paths": [
    {
      "type": "validate",
      "label": "Validate & Deepen",
      "question": "Question testing limits or conditions",
      "teaser": "One short sentence previewing this path"
    },
    {
      "type": "horizon",
      "label": "New Horizon",
      "question": "New frontier question branching into fresh territory",
      "teaser": "One short sentence previewing this path"
    },
    {
      "type": "paradox",
      "label": "Wait, how is this possible?!",
      "question": "Provocative paradox or anomaly question",
      "teaser": "One short sentence previewing this anomaly"
    }
  ]
}
EOT;

        $priorsContext = count($priors) > 0
            ? "Things already established/known:\n- " . implode("\n- ", $priors)
            : "No prior blocks yet established (Genesis state). Establish foundational baseline priors.";

        $userContent = <<<EOT
User Inquiry / Hypothesis: "{$prompt}"

Context of Accepted Foundations:
{$priorsContext}

Synthesize a complete 5-facet scientific building block according to the JSON schema.
EOT;

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->defaultModel}:generateContent?key={$apiKey}";

        $response = Http::timeout(25)->post($url, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemInstruction . "\n\n" . $userContent]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'responseMimeType' => 'application/json',
            ],
        ]);

        if (! $response->successful()) {
            // Attempt fallback model if 2.5 is unavailable
            if ($this->defaultModel !== 'gemini-1.5-flash') {
                $fallbackUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";
                $fallbackResp = Http::timeout(25)->post($fallbackUrl, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemInstruction . "\n\n" . $userContent]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                if ($fallbackResp->successful()) {
                    return $this->extractJsonFromResponse($fallbackResp->json());
                }
            }
            return null;
        }

        return $this->extractJsonFromResponse($response->json());
    }

    /**
     * Call Gemini API to evaluate a challenge/anomaly against a sealed foundation block.
     */
    protected function callGeminiForChallenge(array $blockData, string $counterEvidence, string $apiKey): ?array
    {
        $prompt = <<<EOT
You are an epistemological arbiter in 'Cosmic Lineage'.
A user is challenging a sealed foundational block with new counter-evidence or an anomaly.

Sealed Block:
Title: {$blockData['title']}
Hypothesis: {$blockData['hypothesis']}
Things We Know:
- {$this->formatList($blockData['things_we_know'] ?? [])}
Experiment & Results: {$blockData['results']['lesson']}

User's Counter-Evidence / Anomaly Challenge:
"{$counterEvidence}"

Evaluate whether this challenge:
1. "fractured" (the original premise is overturned or falsified under these conditions)
2. "refined" (the original premise holds as a special case, but requires boundary conditions)
3. "upheld" (the challenge is based on a misunderstanding or does not refute the observation)

Return strictly valid JSON:
{
  "verdict": "fractured" | "refined" | "upheld",
  "verdict_title": "Short impact summary (e.g., 'Boundary Condition Exposed' or 'Fundamental Refutation')",
  "analysis": "Scientific analysis explaining what happens to the theory",
  "fractured_assumptions": [
    "Specific premise that no longer holds universally"
  ],
  "new_synthesis": "The deeper, more universal truth that emerges from this conflict",
  "divergent_question": "The bold new inquiry we must now pursue"
}
EOT;

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->defaultModel}:generateContent?key={$apiKey}";
        $response = Http::timeout(25)->post($url, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $prompt]]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'responseMimeType' => 'application/json',
            ],
        ]);

        if ($response->successful()) {
            return $this->extractJsonFromResponse($response->json());
        }

        return null;
    }

    /**
     * Extract and parse JSON text from Gemini API response.
     */
    protected function extractJsonFromResponse(array $payload): ?array
    {
        $text = $payload['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (! $text) {
            return null;
        }

        // Clean code blocks if present
        $cleaned = trim($text);
        if (str_starts_with($cleaned, '```json')) {
            $cleaned = substr($cleaned, 7);
        } elseif (str_starts_with($cleaned, '```')) {
            $cleaned = substr($cleaned, 3);
        }
        if (str_ends_with($cleaned, '```')) {
            $cleaned = substr($cleaned, 0, -3);
        }

        $decoded = json_decode(trim($cleaned), true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Heuristic synthesis when offline or running without an API key.
     */
    public function heuristicFallback(string $prompt, array $priors = [], ?string $parentBlockId = null): array
    {
        $lower = strtolower($prompt);

        // Check if there is a close match in our seeded historical experiments
        $historicalMatch = $this->findHistoricalMatch($lower);

        if ($historicalMatch) {
            return [
                'id' => 'blk_' . Str::random(10),
                'parent_id' => $parentBlockId,
                'created_at' => now()->toIso8601String(),
                'title' => $historicalMatch->title,
                'era_context' => $historicalMatch->age ? $historicalMatch->age->name . ' (' . $historicalMatch->year_display . ')' : 'Historical Milestone',
                'hypothesis' => $historicalMatch->question,
                'things_we_know' => $historicalMatch->prior_knowledge ?: [
                    'Matter exhibits distinct states and interactive physical properties.',
                    'Forces can be mediated across physical contacts and mediums.'
                ],
                'experiment' => [
                    'apparatus' => $historicalMatch->apparatus ?: 'Specialized experimental apparatus of the era',
                    'method' => $historicalMatch->method,
                    'variable_tested' => 'Physical interaction under controlled observation'
                ],
                'results' => [
                    'observation' => $historicalMatch->conclusion,
                    'lesson' => $historicalMatch->significance,
                    'significance' => 'Fundamental foundation stone in the cosmic lineage of physics.'
                ],
                'next_paths' => is_array($historicalMatch->next_paths) && count($historicalMatch->next_paths) >= 3
                    ? $historicalMatch->next_paths
                    : [
                        [
                            'type' => 'validate',
                            'label' => 'Validate & Deepen',
                            'question' => "Can this effect be measured quantitatively under varying temperatures and materials?",
                            'teaser' => 'Establish mathematical boundaries and precise formulas.'
                        ],
                        [
                            'type' => 'horizon',
                            'label' => 'New Horizon',
                            'question' => "Does this phenomenon induce or interact with an entirely different force of nature?",
                            'teaser' => 'Look for unseen bridges across physical disciplines.'
                        ],
                        [
                            'type' => 'paradox',
                            'label' => 'Wait, how is this possible?!',
                            'question' => "If energy is conserved, where does the opposing reaction manifest?",
                            'teaser' => 'Expose hidden discrepancies in the observation.'
                        ]
                    ],
                'status' => 'draft',
                'sealed' => false,
                'challenged' => false,
                'challenge_notes' => null,
            ];
        }

        // Generate synthetic epistemological block based on the user's prompt text
        $title = Str::headline(Str::limit($prompt, 38, ''));
        if (empty($title)) {
            $title = 'First Principles Inquiry';
        }

        return [
            'id' => 'blk_' . Str::random(10),
            'parent_id' => $parentBlockId,
            'created_at' => now()->toIso8601String(),
            'title' => $title,
            'era_context' => 'Conceptual Genesis',
            'hypothesis' => "Hypothesis under inquiry: " . rtrim($prompt, '?') . ", and nature operates under verifiable causal consistency.",
            'things_we_know' => count($priors) > 0 ? $priors : [
                'Observable reality can be systematically interrogated via reproducible tests.',
                'Effects have observable causes that can be isolated.',
                'Conservation of mass, energy, and fundamental symmetries.'
            ],
            'experiment' => [
                'apparatus' => 'Calibrated sensors, isolated control chamber, and measurable reaction media',
                'method' => "Isolate external variables, systematically subject the system to the inquiry conditions, and record baseline vs altered states.",
                'variable_tested' => 'The presence, intensity, or direction of the hypothesized interaction'
            ],
            'results' => [
                'observation' => "Measurable divergence from baseline observed when conditions are fulfilled.",
                'lesson' => "Nature confirms that the hypothesized relationship holds under controlled boundary constraints.",
                'significance' => "Establishes a newly proven premise upon which subsequent questions can stand."
            ],
            'next_paths' => [
                [
                    'type' => 'validate',
                    'label' => 'Validate & Deepen',
                    'question' => "What are the extreme limits (infinite distance, zero temperature, vacuum) where this rule holds?",
                    'teaser' => 'Push the boundary conditions to their absolute extremes.'
                ],
                [
                    'type' => 'horizon',
                    'label' => 'New Horizon',
                    'question' => "How does this discovery interact with wave propagation and energy transfer?",
                    'teaser' => 'Build a bridge from this truth to adjacent domains.'
                ],
                [
                    'type' => 'paradox',
                    'label' => 'Wait, how is this possible?!',
                    'question' => "Does this imply an instantaneous action at a distance, or does it take finite time to propagate?",
                    'teaser' => 'Probe the speed and causality of the interaction.'
                ]
            ],
            'status' => 'draft',
            'sealed' => false,
            'challenged' => false,
            'challenge_notes' => null,
        ];
    }

    /**
     * Search seeded experiments for semantic keywords.
     */
    protected function findHistoricalMatch(string $lower): ?Experiment
    {
        $keywords = [
            'amber' => 'thales',
            'straw' => 'thales',
            'friction' => 'thales',
            'compass' => 'gilbert',
            'earth magnet' => 'gilbert',
            'spark' => 'von-guericke',
            'vacuum' => 'von-guericke',
            'lightning' => 'franklin',
            'kite' => 'franklin',
            'positive negative' => 'franklin',
            'battery' => 'volta',
            'pile' => 'volta',
            'current' => 'volta',
            'frog' => 'galvani',
            'needle' => 'oersted',
            'deflection' => 'oersted',
            'induction' => 'faraday',
            'generator' => 'faraday',
            'light' => 'maxwell',
            'equations' => 'maxwell',
            'radio' => 'hertz',
            'electron' => 'thomson',
            'relativity' => 'einstein',
            'photoelectric' => 'einstein',
        ];

        foreach ($keywords as $word => $slugKeyword) {
            if (str_contains($lower, $word)) {
                $match = Experiment::with('age')->where('slug', 'like', "%{$slugKeyword}%")->first();
                if ($match) {
                    return $match;
                }
            }
        }

        return null;
    }

    /**
     * Fallback challenge evaluation.
     */
    protected function heuristicChallengeFallback(array $blockData, string $counterEvidence): array
    {
        return [
            'verdict' => 'refined',
            'verdict_title' => 'Boundary Condition Exposed',
            'analysis' => "The challenge indicates that the original hypothesis is an effective theory holding within classical domains, but requires boundary constraints when subjected to the counter-evidence: \"{$counterEvidence}\".",
            'fractured_assumptions' => [
                'Assumption that the rule holds uniformly across all energy scales and relativistic speeds.'
            ],
            'new_synthesis' => "The original observation remains empirically valid within its domain, but is revealed as a special case of a broader unified law.",
            'divergent_question' => "What unified framework reconciles the original observation with the anomalous counter-evidence?"
        ];
    }

    protected function formatList(array $items): string
    {
        return implode("\n- ", $items);
    }
}
