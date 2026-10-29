<!DOCTYPE html>
<html lang="en" class="h-full bg-[#07090e]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inquiry Canvas & Thought Engine | Cosmic Lineage</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500|cinzel:600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-['Instrument_Sans'] overflow-x-hidden text-slate-100 bg-[#07090e]"
      x-data="inquiryCanvasApp()">

    <!-- Ambient background glows -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-900/15 rounded-full blur-[128px]"></div>
        <div class="absolute top-1/3 -right-40 w-[30rem] h-[30rem] bg-cyan-900/15 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[32rem] h-[32rem] bg-amber-900/10 rounded-full blur-[140px]"></div>
        <!-- Subtle grid overlay -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1f293708_1px,transparent_1px),linear-gradient(to_bottom,#1f293708_1px,transparent_1px)] bg-[size:3rem_3rem]"></div>
    </div>

    <!-- Top Sticky Header -->
    <header class="sticky top-0 z-40 border-b border-white/10 glass-panel bg-[#07090e]/95 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Brand Title -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('history.index') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 via-indigo-500 to-cyan-400 p-[1px] shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                            <div class="w-full h-full bg-[#0b0f19] rounded-[11px] flex items-center justify-center">
                                <span class="text-lg">🧱</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-white flex items-center gap-2">
                                Cosmic Lineage
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30 font-medium">Inquiry Canvas</span>
                            </span>
                            <p class="text-xs text-slate-400 hidden sm:block">First-Principles Thought Engine & Masonry of Truth</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Switcher & Tools -->
                <div class="flex items-center gap-3">
                    <!-- Switch to 2,600-Year Timeline -->
                    <a href="{{ route('history.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-300 bg-white/5 hover:bg-white/10 border border-white/10 transition-colors">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Historical Timeline</span>
                    </a>

                    <!-- Foundation Counter Badge -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-xs font-mono text-amber-300">
                        <span>🧱</span>
                        <span x-text="sealedCount + ' Sealed'"></span>
                        <span class="text-slate-500">/</span>
                        <span class="text-slate-400" x-text="totalCount + ' Inquiries'"></span>
                    </div>

                    <!-- Gemini API Status Button -->
                    <button @click="showKeyModal = true"
                            type="button"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                            :class="apiKey ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/20' : 'bg-amber-500/15 text-amber-300 border border-amber-500/30 hover:bg-amber-500/20'">
                        <span class="w-2 h-2 rounded-full" :class="apiKey ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400'"></span>
                        <span x-text="apiKey ? 'Gemini 2.5 Flash Connected' : 'Set Gemini API Key ⚙️'"></span>
                    </button>

                    <!-- Reset Canvas -->
                    <button x-show="blocks.length > 0"
                            @click="resetCanvas()"
                            type="button"
                            class="px-2.5 py-1.5 rounded-lg text-xs text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors border border-transparent hover:border-red-500/20"
                            title="Reset Canvas">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Viewport -->
    <main class="flex-1 relative z-10 flex flex-col">

        <!-- ========================================== -->
        <!-- STATE 1: GENESIS PORTAL (Zero-State)       -->
        <!-- ========================================== -->
        <div x-show="blocks.length === 0 && !isLoading" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="flex-1 flex flex-col items-center justify-center px-4 py-12 max-w-4xl mx-auto w-full text-center">
            
            <!-- Genesis Icon -->
            <div class="relative mb-6">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-amber-500 via-orange-500 to-cyan-500 p-[1px] shadow-2xl shadow-amber-500/20">
                    <div class="w-full h-full bg-[#0b0f19] rounded-[15px] flex items-center justify-center text-3xl">
                        🌌
                    </div>
                </div>
                <div class="absolute -bottom-2 -right-2 px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-mono tracking-wider">
                    GENESIS
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white mb-3">
                Where Shall Our Inquiry Begin?
            </h1>
            <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto mb-8 font-light leading-relaxed">
                Reconstruct physical science from first principles. State an assumption, formulate an intuition, or pick an ancient question to forge your foundational building block.
            </p>

            <!-- Genesis Prompt Input Bar -->
            <form @submit.prevent="forgeNewInquiry(null)" class="w-full max-w-2xl mb-8 relative">
                <div class="relative flex items-center">
                    <input type="text"
                           x-model="inputPrompt"
                           placeholder="e.g. Why does rubbed amber attract feathers? or Can light bend around massive stars?"
                           class="w-full px-5 py-4 pr-36 rounded-2xl bg-[#0e1322] border-2 border-white/10 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500/80 focus:ring-4 focus:ring-amber-500/10 text-sm sm:text-base shadow-2xl transition-all font-['Instrument_Sans']"
                           autofocus />
                    
                    <button type="submit"
                            :disabled="!inputPrompt.trim()"
                            class="absolute right-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 font-semibold text-xs sm:text-sm hover:from-amber-400 hover:to-orange-400 disabled:opacity-40 disabled:cursor-not-allowed shadow-lg shadow-amber-500/20 transition-all flex items-center gap-1.5">
                        <span>Forge Block</span>
                        <span>🧱</span>
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 mt-2 text-left px-2">
                    ⚡ Powered by Gemini AI. Each block synthesizes: Hypothesis, Things We Know, The Experiment, The Results, and 3 Divergent Paths.
                </p>
            </form>

            <!-- Curated Foundation Stones (Quick Starters) -->
            <div class="w-full max-w-3xl">
                <div class="flex items-center justify-between mb-3 px-1">
                    <span class="text-xs uppercase font-mono tracking-wider text-slate-400">Or ignite inquiry from historical genesis milestones:</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-left">
                    @foreach($starters as $starter)
                    <button @click="startWithStarter({{ json_encode($starter) }})"
                            type="button"
                            class="p-4 rounded-xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/10 hover:border-amber-500/40 transition-all text-left group relative overflow-hidden flex flex-col justify-between">
                        <div class="mb-2">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-amber-400/80 block mb-1">{{ $starter['era'] }}</span>
                            <h3 class="text-sm font-semibold text-white group-hover:text-amber-300 transition-colors flex items-center gap-1.5">
                                <span>{{ $starter['title'] }}</span>
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity text-xs">→</span>
                            </h3>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed font-light line-clamp-2">
                            "{{ $starter['question'] }}"
                        </p>
                    </button>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- STATE 2: ACTIVE INQUIRY CANVAS             -->
        <!-- ========================================== -->
        <div x-show="blocks.length > 0" 
             id="canvas-container"
             class="flex-1 relative p-4 sm:p-8 max-w-5xl mx-auto w-full">

            <!-- Dynamic SVG Bezier Connector Strings Layer -->
            <svg class="absolute inset-0 w-full h-full pointer-events-none z-0" style="min-height: 100%;">
                <defs>
                    <linearGradient id="beamGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.8"/>
                        <stop offset="100%" stop-color="#38bdf8" stop-opacity="0.8"/>
                    </linearGradient>
                    <linearGradient id="fracturedGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#ef4444" stop-opacity="0.9"/>
                        <stop offset="100%" stop-color="#dc2626" stop-opacity="0.9"/>
                    </linearGradient>
                </defs>
                <template x-for="conn in connectors" :key="conn.id">
                    <g>
                        <!-- Glowing shadow path -->
                        <path :d="conn.d" 
                              fill="none" 
                              :stroke="conn.isChallenged ? '#ef4444' : '#f59e0b'" 
                              stroke-width="5" 
                              stroke-opacity="0.2" />
                        <!-- Core connection chord -->
                        <path :d="conn.d" 
                              fill="none" 
                              :stroke="conn.isChallenged ? 'url(#fracturedGradient)' : 'url(#beamGradient)'" 
                              stroke-width="2" 
                              :stroke-dasharray="conn.isChallenged ? '6,4' : 'none'" />
                    </g>
                </template>
            </svg>

            <!-- Top Canvas Status & Controls -->
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 mb-8 p-3 rounded-xl bg-white/[0.02] border border-white/10 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-mono uppercase tracking-wider text-slate-400">Inquiry Masonry Wall</span>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-white/5 text-slate-300 font-mono" x-text="blocks.length + ' blocks in tree'"></span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Add adjacent prompt button -->
                    <button @click="$refs.bottomPromptInput.focus()"
                            type="button"
                            class="px-3 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-xs font-medium transition-colors flex items-center gap-1.5">
                        <span>+ Ask New Inquiry</span>
                    </button>
                </div>
            </div>

            <!-- List of Building Blocks -->
            <div class="relative z-10 space-y-12">
                <template x-for="(block, index) in blocks" :key="block.id">
                    <div :id="block.id"
                         class="relative rounded-2xl transition-all duration-300 border shadow-2xl overflow-hidden"
                         :class="{
                             'bg-[#0b0f1c]/95 border-amber-500/50 shadow-amber-500/10 ring-1 ring-amber-500/20': block.sealed && !block.challenged,
                             'bg-[#120b0e]/95 border-red-500/60 shadow-red-500/20 ring-1 ring-red-500/30': block.challenged,
                             'bg-[#0c101d]/90 border-white/15 hover:border-white/30': !block.sealed && !block.challenged
                         }">
                        
                        <!-- Upstream Foundation Questioned Alert Banner -->
                        <div x-show="block.upstreamAlert" 
                             class="px-4 py-2 bg-red-500/20 border-b border-red-500/30 text-red-300 text-xs flex items-center gap-2">
                            <span class="animate-pulse">⚠️</span>
                            <span x-text="block.upstreamAlert"></span>
                        </div>

                        <!-- Block Header Bar -->
                        <div class="p-4 sm:p-5 border-b border-white/10 flex flex-wrap items-center justify-between gap-3 bg-white/[0.02]">
                            <div class="flex items-center gap-3">
                                <!-- Stone Index Number -->
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-mono font-bold text-xs"
                                     :class="{
                                         'bg-amber-500 text-slate-950': block.sealed && !block.challenged,
                                         'bg-red-500 text-white': block.challenged,
                                         'bg-white/10 text-white': !block.sealed && !block.challenged
                                     }"
                                     x-text="index + 1">
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-mono uppercase tracking-wider text-amber-400" x-text="block.era_context || 'Conceptual Foundation'"></span>
                                        <span x-show="block.source === 'heuristic' || block.source === 'heuristic_fallback'" 
                                              class="text-[9px] px-1.5 py-0.2 rounded bg-slate-700/60 text-slate-300 font-mono">Offline Heuristics</span>
                                    </div>
                                    <h2 class="text-lg sm:text-xl font-bold text-white tracking-tight" x-text="block.title"></h2>
                                </div>
                            </div>

                            <!-- State Badges & Actions -->
                            <div class="flex items-center gap-2">
                                <!-- Sealed Status Indicator -->
                                <template x-if="block.sealed && !block.challenged">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/40 text-amber-300 text-xs font-semibold tracking-wide">
                                        <span>🧱</span>
                                        <span>PROVEN FOUNDATION</span>
                                    </span>
                                </template>

                                <template x-if="block.challenged">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/20 border border-red-500/50 text-red-300 text-xs font-semibold tracking-wide">
                                        <span>⚡</span>
                                        <span x-text="block.challenge_notes?.verdict_title || 'FOUNDATION QUESTIONED'"></span>
                                    </span>
                                </template>

                                <template x-if="!block.sealed">
                                    <button @click="sealBlock(block)"
                                            type="button"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 text-xs font-bold hover:from-amber-400 hover:to-orange-400 shadow-lg shadow-amber-500/20 transition-all">
                                        <span>Seal as Proven Truth</span>
                                        <span>🧱</span>
                                    </button>
                                </template>

                                <!-- Challenge Foundation Action (When Sealed) -->
                                <template x-if="block.sealed && !block.challenged">
                                    <button @click="openChallenge(block)"
                                            type="button"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 text-red-300 text-xs font-medium transition-colors">
                                        <span>⚡ Challenge</span>
                                    </button>
                                </template>

                                <!-- Delete Block -->
                                <button @click="deleteBlock(block.id)"
                                        type="button"
                                        class="p-1.5 text-slate-500 hover:text-red-400 rounded-lg hover:bg-white/5 transition-colors"
                                        title="Remove Block">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- 5 CORE FACETS CONTAINER -->
                        <div class="p-4 sm:p-6 space-y-6">

                            <!-- FACET 1: HYPOTHESIS -->
                            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                                <div class="flex items-center gap-2 mb-2 text-xs font-mono uppercase tracking-wider text-amber-400 font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span>Facet 1 · The Hypothesis / Inquiry</span>
                                </div>
                                <p class="text-sm sm:text-base text-slate-100 font-medium leading-relaxed italic" x-text="block.hypothesis"></p>
                            </div>

                            <!-- FACET 2: THINGS WE KNOW (Priors) -->
                            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                                <div class="flex items-center gap-2 mb-2 text-xs font-mono uppercase tracking-wider text-cyan-400 font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    <span>Facet 2 · Things We Know (Accepted Foundations)</span>
                                </div>
                                <ul class="space-y-1.5 text-xs sm:text-sm text-slate-300">
                                    <template x-for="(prior, pIndex) in block.things_we_know" :key="pIndex">
                                        <li class="flex items-start gap-2">
                                            <span class="text-emerald-400 mt-0.5">✓</span>
                                            <span x-text="prior"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <!-- FACET 3: THE EXPERIMENT -->
                            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                                <div class="flex items-center gap-2 mb-3 text-xs font-mono uppercase tracking-wider text-purple-400 font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                    <span>Facet 3 · The Physical Experiment</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs sm:text-sm">
                                    <div class="p-3 rounded-lg bg-black/30 border border-white/5">
                                        <span class="text-[10px] font-mono text-slate-400 uppercase block mb-1">Apparatus & Tools</span>
                                        <p class="text-slate-200" x-text="block.experiment?.apparatus"></p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-black/30 border border-white/5 md:col-span-2">
                                        <span class="text-[10px] font-mono text-slate-400 uppercase block mb-1">Method & Observation Procedure</span>
                                        <p class="text-slate-200 leading-relaxed" x-text="block.experiment?.method"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- FACET 4: THE RESULTS & PRINCIPLES -->
                            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                                <div class="flex items-center gap-2 mb-3 text-xs font-mono uppercase tracking-wider text-emerald-400 font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    <span>Facet 4 · Results, Deduced Principle & Cosmic Significance</span>
                                </div>
                                <div class="space-y-3 text-xs sm:text-sm">
                                    <div>
                                        <span class="text-[10px] font-mono uppercase text-slate-400 block mb-0.5">Empirical Finding</span>
                                        <p class="text-slate-200 font-medium" x-text="block.results?.observation"></p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                                        <span class="text-[10px] font-mono uppercase text-emerald-300 block mb-0.5 font-bold">Universal Law / Principle Deduced</span>
                                        <p class="text-emerald-100 font-semibold leading-relaxed" x-text="block.results?.lesson"></p>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-mono uppercase text-slate-400 block mb-0.5">Significance in Cosmic Lineage</span>
                                        <p class="text-slate-400" x-text="block.results?.significance"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- CHALLENGE REVIEW BANNER (IF CHALLENGED) -->
                            <template x-if="block.challenged && block.challenge_notes">
                                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-xs sm:text-sm space-y-2">
                                    <div class="flex items-center gap-2 text-red-300 font-bold uppercase tracking-wider text-xs">
                                        <span>⚡ Challenge Assessment</span>
                                        <span class="px-2 py-0.5 rounded bg-red-500/20 font-mono" x-text="block.challenge_notes.verdict"></span>
                                    </div>
                                    <p class="text-slate-300 leading-relaxed" x-text="block.challenge_notes.analysis"></p>
                                    
                                    <template x-if="block.challenge_notes.fractured_assumptions?.length">
                                        <div class="pt-2">
                                            <span class="text-[10px] font-mono uppercase text-red-400 block mb-1">Fractured / Bounded Assumptions:</span>
                                            <ul class="list-disc list-inside space-y-0.5 text-slate-300">
                                                <template x-for="item in block.challenge_notes.fractured_assumptions" :key="item">
                                                    <li x-text="item"></li>
                                                </template>
                                            </ul>
                                        </div>
                                    </template>

                                    <template x-if="block.challenge_notes.new_synthesis">
                                        <div class="pt-2 p-2.5 rounded-lg bg-black/40 border border-white/5">
                                            <span class="text-[10px] font-mono uppercase text-amber-300 block mb-1">Emergent Deeper Synthesis:</span>
                                            <p class="text-slate-200 font-medium" x-text="block.challenge_notes.new_synthesis"></p>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- FACET 5: NEXT POSSIBLE PATHS (BRANCHING SYSTEM) -->
                            <div class="pt-2">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-indigo-400 font-semibold">
                                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                        <span>Facet 5 · Where do we go from here? (Branch New Building Block)</span>
                                    </div>
                                    <span class="text-[11px] text-slate-500" x-show="!block.sealed">Seal block to unlock branching paths</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <template x-for="(path, pathIndex) in block.next_paths" :key="pathIndex">
                                        <button @click="branchFromNextPath(block, path)"
                                                :disabled="!block.sealed || isLoading"
                                                type="button"
                                                class="p-4 rounded-xl text-left transition-all relative overflow-hidden flex flex-col justify-between group"
                                                :class="{
                                                    'opacity-40 cursor-not-allowed bg-white/[0.02] border border-white/5': !block.sealed || isLoading,
                                                    'hover:scale-[1.02] active:scale-[0.99]': block.sealed && !isLoading,
                                                    'bg-emerald-950/20 hover:bg-emerald-900/30 border border-emerald-500/20 hover:border-emerald-500/40': path.type === 'validate',
                                                    'bg-cyan-950/20 hover:bg-cyan-900/30 border border-cyan-500/20 hover:border-cyan-500/40': path.type === 'horizon',
                                                    'bg-amber-950/20 hover:bg-amber-900/30 border border-amber-500/20 hover:border-amber-500/40': path.type === 'paradox'
                                                }">
                                            <div>
                                                <div class="flex items-center justify-between gap-1 mb-2">
                                                    <span class="text-[10px] font-mono uppercase tracking-wider font-semibold"
                                                          :class="{
                                                              'text-emerald-400': path.type === 'validate',
                                                              'text-cyan-400': path.type === 'horizon',
                                                              'text-amber-400': path.type === 'paradox'
                                                          }"
                                                          x-text="path.label">
                                                    </span>
                                                    <span class="text-xs group-hover:translate-x-1 transition-transform">→</span>
                                                </div>
                                                <p class="text-xs sm:text-sm font-semibold text-white group-hover:text-amber-200 transition-colors leading-snug mb-2"
                                                   x-text="path.question">
                                                </p>
                                            </div>
                                            <p class="text-[11px] text-slate-400 font-light" x-text="path.teaser"></p>
                                        </button>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>
            </div>

            <!-- Bottom Floating Prompt to Ask Further Questions -->
            <div class="relative z-20 mt-12 pt-8 border-t border-white/10">
                <form @submit.prevent="forgeNewInquiry(null)" class="max-w-2xl mx-auto relative">
                    <div class="flex items-center relative">
                        <input type="text"
                               x-ref="bottomPromptInput"
                               x-model="inputPrompt"
                               placeholder="Have a new inquiry or hypothesis to test?"
                               class="w-full px-5 py-3.5 pr-32 rounded-xl bg-[#0c101d] border border-white/15 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 text-sm shadow-xl" />
                        <button type="submit"
                                :disabled="!inputPrompt.trim() || isLoading"
                                class="absolute right-2 px-3.5 py-2 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs disabled:opacity-40 transition-colors">
                            Forge Block 🧱
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- LOADING OVERLAY                            -->
        <!-- ========================================== -->
        <div x-show="isLoading" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4">
            <div class="p-6 rounded-2xl bg-[#0e1322] border border-amber-500/30 max-w-md w-full text-center shadow-2xl">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 flex items-center justify-center mx-auto mb-4 animate-spin">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white mb-1">Synthesizing Building Block</h3>
                <p class="text-xs text-slate-300 mb-4" x-text="loadingMessage"></p>
                <div class="w-full bg-white/5 rounded-full h-1 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-cyan-500 h-full w-full animate-pulse"></div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- GEMINI API KEY MODAL                       -->
        <!-- ========================================== -->
        <div x-show="showKeyModal" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4"
             @keydown.escape.window="showKeyModal = false">
            <div class="p-6 rounded-2xl bg-[#0e1322] border border-white/20 max-w-lg w-full shadow-2xl relative"
                 @click.away="showKeyModal = false">
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">⚡</span>
                        <h3 class="text-lg font-bold text-white">Gemini API Key Setup</h3>
                    </div>
                    <button @click="showKeyModal = false" class="text-slate-400 hover:text-white text-sm">✕</button>
                </div>

                <p class="text-xs text-slate-300 mb-4 leading-relaxed">
                    Connect your free Gemini API key to unlock infinite dynamic first-principles synthesis. The key is saved safely in your browser’s <code class="text-amber-400 bg-white/5 px-1 py-0.5 rounded">localStorage</code> and never shared.
                </p>

                <!-- Free Key Link Callout -->
                <div class="p-3 rounded-xl bg-cyan-950/30 border border-cyan-500/20 mb-4 flex items-center justify-between text-xs">
                    <span class="text-cyan-200">Don't have a Gemini key yet?</span>
                    <a href="https://aistudio.google.com/app/apikey" 
                       target="_blank" 
                       rel="noopener"
                       class="font-semibold text-cyan-400 hover:text-cyan-300 underline flex items-center gap-1">
                        <span>Get free key at Google AI Studio</span>
                        <span class="text-[10px]">↗</span>
                    </a>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-mono uppercase tracking-wider text-slate-400 mb-1.5">API Key</label>
                    <input type="password" 
                           x-model="tempApiKey"
                           placeholder="AIzaSy..."
                           class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 text-sm font-mono" />
                </div>

                <div x-show="keyNotice" class="mb-4 text-xs font-semibold text-emerald-400" x-text="keyNotice"></div>

                <div class="flex items-center justify-between gap-3">
                    <button @click="clearApiKey()" 
                            type="button" 
                            class="px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-red-400 transition-colors">
                        Clear Stored Key
                    </button>
                    
                    <div class="flex items-center gap-2">
                        <button @click="showKeyModal = false" 
                                type="button" 
                                class="px-4 py-2 rounded-xl text-xs font-medium text-slate-300 hover:bg-white/5 transition-colors">
                            Cancel
                        </button>
                        <button @click="saveApiKey()" 
                                type="button" 
                                class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition-colors">
                            Save Key to Browser
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- CHALLENGE FOUNDATION MODAL                 -->
        <!-- ========================================== -->
        <div x-show="showChallengeModal" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4"
             @keydown.escape.window="showChallengeModal = false">
            <div class="p-6 rounded-2xl bg-[#130b0e] border border-red-500/30 max-w-xl w-full shadow-2xl relative"
                 @click.away="showChallengeModal = false">
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">⚡</span>
                        <div>
                            <h3 class="text-lg font-bold text-white">Challenge Foundation Block</h3>
                            <p class="text-xs text-red-300" x-text="'Testing: ' + challengingBlock?.title"></p>
                        </div>
                    </div>
                    <button @click="showChallengeModal = false" class="text-slate-400 hover:text-white text-sm">✕</button>
                </div>

                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/5 mb-4 text-xs text-slate-300 space-y-1">
                    <span class="text-[10px] font-mono uppercase text-slate-400 block">Accepted Truth Under Test</span>
                    <p class="text-slate-100 font-medium" x-text="challengingBlock?.results?.lesson"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-mono uppercase tracking-wider text-slate-400 mb-1.5">
                        What counter-evidence or paradox challenges this truth?
                    </label>
                    <textarea x-model="challengeInput"
                              rows="3"
                              placeholder="e.g. When velocities approach the speed of light, classical addition of velocities fails; or at subatomic scales, electrons act as waves rather than localized spheres."
                              class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white placeholder-slate-600 focus:outline-none focus:border-red-500 text-xs sm:text-sm"></textarea>
                </div>

                <!-- Evaluated Result Display -->
                <template x-if="challengeResult">
                    <div class="mb-4 p-4 rounded-xl bg-black/50 border border-red-500/30 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-red-400 font-bold uppercase" x-text="challengeResult.verdict_title"></span>
                            <span class="px-2 py-0.5 rounded bg-red-500/20 text-red-300 font-mono" x-text="challengeResult.verdict"></span>
                        </div>
                        <p class="text-slate-300" x-text="challengeResult.analysis"></p>
                    </div>
                </template>

                <div class="flex items-center justify-end gap-2">
                    <button @click="showChallengeModal = false" 
                            type="button" 
                            class="px-4 py-2 rounded-xl text-xs font-medium text-slate-300 hover:bg-white/5 transition-colors">
                        Close
                    </button>
                    <button @click="submitChallenge()" 
                            :disabled="!challengeInput.trim() || isChallenging"
                            type="button" 
                            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs disabled:opacity-40 transition-colors flex items-center gap-1.5">
                        <span x-text="isChallenging ? 'Arbitrating Anomaly...' : 'Evaluate Challenge'"></span>
                        <span>⚡</span>
                    </button>
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-white/10 bg-[#07090e] py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>Cosmic Lineage · Reconstructing 2,600 Years of Science from First Principles</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('history.index') }}" class="hover:text-slate-300 transition-colors">Historical Timeline</a>
                <a href="{{ route('inquiry.index') }}" class="text-amber-400 hover:underline">Inquiry Canvas</a>
                <a href="https://github.com/ReyKiptoo/cosmic-lineage" target="_blank" class="hover:text-slate-300 transition-colors">GitHub Repository</a>
            </div>
        </div>
    </footer>

</body>
</html>
