<!DOCTYPE html>
<html lang="en" class="h-full bg-[#07090e]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cosmic Lineage | History of Science & Electromagnetism</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    <script>
        window.__HISTORY_DATA__ = {!! $jsonPayload !!};
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-['Instrument_Sans'] overflow-x-hidden text-slate-100"
      x-data="historyApp(window.__HISTORY_DATA__)">

    <!-- Ambient background glows -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-900/20 rounded-full blur-[128px]"></div>
        <div class="absolute top-1/3 -right-40 w-[30rem] h-[30rem] bg-cyan-900/20 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[32rem] h-[32rem] bg-emerald-900/15 rounded-full blur-[140px]"></div>
    </div>

    <!-- Top Sticky Header -->
    <header class="sticky top-0 z-40 border-b border-white/10 glass-panel bg-[#07090e]/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Brand Title -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 via-indigo-500 to-amber-400 p-[1px] shadow-lg shadow-cyan-500/20">
                        <div class="w-full h-full bg-[#0b0f19] rounded-[11px] flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-lg font-bold tracking-tight text-white flex items-center gap-2">
                            Cosmic Lineage
                            <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 font-medium">History of Science</span>
                        </span>
                        <p class="text-xs text-slate-400 hidden sm:block">2,600 Years of Scientific Revolutions & Experiment Lineage</p>
                    </div>
                </div>

                <!-- 7 Interactivity Modes Switcher -->
                <div class="flex items-center gap-1 bg-white/5 border border-white/10 rounded-xl p-1 overflow-x-auto max-w-full">
                    <a href="{{ route('inquiry.index') }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 shadow-md shadow-amber-500/20 hover:from-amber-400 hover:to-orange-400 transition flex items-center gap-1 shrink-0">
                        <span>🧱 Inquiry Canvas</span>
                    </a>
                    <button @click="setMode('timeline')"
                            :class="activeMode === 'timeline' ? 'bg-cyan-500 text-slate-950 font-bold shadow' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1 shrink-0">
                        <span>📜 Chronology</span>
                    </button>
                    <button @click="setMode('quest')"
                            :class="activeMode === 'quest' ? 'bg-amber-400 text-slate-950 font-bold shadow' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1 shrink-0">
                        <span>🗺️ Quest Mode</span>
                    </button>
                    <button @click="setMode('threads')"
                            :class="activeMode === 'threads' ? 'bg-indigo-500 text-white font-bold shadow' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1 shrink-0">
                        <span>🧬 Storylines</span>
                    </button>
                    <button @click="setMode('film')"
                            :class="activeMode === 'film' ? 'bg-rose-500 text-white font-bold shadow' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1 shrink-0">
                        <span>⏳ Film Playback</span>
                    </button>
                    <button @click="setMode('constellation')"
                            :class="activeMode === 'constellation' ? 'bg-cyan-500 text-slate-950 font-bold shadow' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1 shrink-0">
                        <span>🌌 Matrix</span>
                    </button>
                    <button @click="setMode('book')"
                            :class="activeMode === 'book' ? 'bg-emerald-500 text-slate-950 font-bold shadow' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1 shrink-0">
                        <span>📖 Book Mode</span>
                    </button>
                    <button @click="setMode('quiz')"
                            :class="activeMode === 'quiz' ? 'bg-purple-500 text-white font-bold shadow' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1 shrink-0">
                        <span>🧩 Quiz</span>
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-36 sm:w-56 shrink-0 hidden md:block">
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Search experiment, thinker..."
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 pl-8 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-2 text-slate-400 hover:text-white text-xs">✕</button>
                </div>

            </div>
        </div>

        <!-- MODE 1: QUEST PROGRESS BAR (Shown only in Quest Mode) -->
        <div x-show="activeMode === 'quest'" class="border-t border-amber-500/20 bg-amber-950/30 px-4 sm:px-6 py-2.5">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-amber-300 flex items-center gap-1.5">
                        <span>🏆 Quest Progress:</span>
                        <span class="font-mono text-white" x-text="'Era ' + unlockedAge + ' of ' + ages.length + ' Unlocked'"></span>
                    </span>
                    <span class="text-slate-400">
                        (Explored: <strong class="text-white" x-text="currentAgeExploredCount"></strong>/<span x-text="currentAgeTotalCount"></span> in current era)
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <button x-show="canUnlockNextAge"
                            @click="unlockNextAge()"
                            class="px-3 py-1 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold transition flex items-center gap-1.5 shadow-lg shadow-amber-400/20 animate-pulse">
                        <span>Unlock Age <span x-text="unlockedAge + 1"></span> →</span>
                    </button>
                    <span x-show="!canUnlockNextAge && unlockedAge < ages.length" class="text-slate-400 italic">
                        Explore at least 2 milestones below to unlock next era
                    </span>
                    <button @click="unlockAllAges()" class="text-slate-400 hover:text-white underline text-[11px]">
                        Unlock All
                    </button>
                    <button @click="resetQuest()" class="text-slate-400 hover:text-white underline text-[11px]">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- MODE 2: GUIDED THREAD BAR (Shown only in Storylines Mode) -->
        <div x-show="activeMode === 'threads'" class="border-t border-indigo-500/20 bg-indigo-950/30 px-4 sm:px-6 py-2.5">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 overflow-x-auto">
                    <span class="text-slate-400 font-semibold shrink-0">Storylines:</span>
                    <template x-for="t in threads" :key="t.id">
                        <button @click="selectThread(t.id)"
                                :class="activeThreadId === t.id ? 'bg-indigo-500 text-white font-bold' : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                                class="px-2.5 py-1 rounded-lg transition shrink-0"
                                x-text="t.title">
                        </button>
                    </template>
                </div>

                <div x-show="activeThread" class="flex items-center gap-2">
                    <span class="text-slate-300 font-mono">
                        Step <strong class="text-white" x-text="activeThreadIndex + 1"></strong> of <span x-text="activeThreadNodes.length"></span>
                    </span>
                    <button @click="stepThread(-1)"
                            :disabled="activeThreadIndex <= 0"
                            class="px-2 py-0.5 rounded bg-white/10 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-white/20 text-white">
                        ← Prev
                    </button>
                    <button @click="stepThread(1)"
                            :disabled="activeThreadIndex >= activeThreadNodes.length - 1"
                            class="px-2 py-0.5 rounded bg-white/10 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-white/20 text-white">
                        Next →
                    </button>
                </div>
            </div>
        </div>

        <!-- MODE 3: TIMELINE FILM TRANSPORT BAR (Shown only in Playback Mode) -->
        <div x-show="activeMode === 'film'" class="border-t border-rose-500/20 bg-rose-950/30 px-4 sm:px-6 py-2.5">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-3">
                    <button @click="togglePlayback()"
                            class="px-4 py-1.5 rounded-xl font-bold transition flex items-center gap-2"
                            :class="isPlaying ? 'bg-rose-500 hover:bg-rose-400 text-white' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950'">
                        <span x-text="isPlaying ? '⏸ Pause Film' : '▶ Play History Film'"></span>
                    </button>

                    <!-- Speed Toggle -->
                    <div class="flex items-center gap-1 bg-white/5 rounded-lg p-0.5 border border-white/10">
                        <button @click="playbackSpeed = 0.5" :class="playbackSpeed === 0.5 ? 'bg-white/20 text-white font-bold' : 'text-slate-400'" class="px-2 py-0.5 rounded text-[11px]">0.5×</button>
                        <button @click="playbackSpeed = 1" :class="playbackSpeed === 1 ? 'bg-white/20 text-white font-bold' : 'text-slate-400'" class="px-2 py-0.5 rounded text-[11px]">1×</button>
                        <button @click="playbackSpeed = 2" :class="playbackSpeed === 2 ? 'bg-white/20 text-white font-bold' : 'text-slate-400'" class="px-2 py-0.5 rounded text-[11px]">2×</button>
                    </div>
                </div>

                <!-- Timeline Scrubber -->
                <div class="flex-1 max-w-md flex items-center gap-2">
                    <span class="text-slate-400 font-mono text-[11px]" x-text="currentPlaybackIndex + 1"></span>
                    <input type="range"
                           min="0"
                           :max="nodes.length - 1"
                           x-model="currentPlaybackIndex"
                           @input="seekPlayback($event.target.value)"
                           class="w-full accent-rose-500 cursor-pointer">
                    <span class="text-slate-400 font-mono text-[11px]" x-text="nodes.length"></span>
                </div>

                <div class="text-slate-300 font-mono text-[11px]">
                    Current: <strong class="text-white" x-text="(nodes[currentPlaybackIndex] || {}).person_name"></strong> (<span x-text="(nodes[currentPlaybackIndex] || {}).display_year"></span>)
                </div>
            </div>
        </div>

        <!-- Filter Subbar (Common for Chronology / Matrix) -->
        <div x-show="activeMode === 'timeline' || activeMode === 'constellation'" class="border-t border-white/5 bg-[#0b0e17]/90 px-4 sm:px-6 lg:px-8 py-2">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-xs">
                
                <!-- Category Filters (Observation / Experiment / Invention / Theory) -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <span class="text-slate-400 mr-1 font-semibold">Type:</span>
                    <button @click="selectedTypeFilter = 'all'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'all' ? 'bg-white text-slate-950 font-bold' : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                            class="px-2.5 py-1 rounded-lg transition shrink-0">
                        All (<span x-text="nodes.length"></span>)
                    </button>
                    <button @click="selectedTypeFilter = 'observation'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'observation' ? 'bg-amber-400 text-slate-950 font-bold' : 'bg-amber-500/10 text-amber-300 border border-amber-500/20 hover:bg-amber-500/20'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shrink-0">
                        <span>👁️ Observation</span>
                    </button>
                    <button @click="selectedTypeFilter = 'experiment'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'experiment' ? 'bg-emerald-400 text-slate-950 font-bold' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 hover:bg-emerald-500/20'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shrink-0">
                        <span>🔬 Experiment</span>
                    </button>
                    <button @click="selectedTypeFilter = 'invention'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'invention' ? 'bg-cyan-400 text-slate-950 font-bold' : 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 hover:bg-cyan-500/20'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shrink-0">
                        <span>⚡ Invention</span>
                    </button>
                    <button @click="selectedTypeFilter = 'theory'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'theory' ? 'bg-purple-400 text-slate-950 font-bold' : 'bg-purple-500/10 text-purple-300 border border-purple-500/20 hover:bg-purple-500/20'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shrink-0">
                        <span>💡 Theory</span>
                    </button>
                </div>

                <!-- Dynamics Filter (Confirms / Refutes / Builds on) -->
                <div class="flex items-center gap-1.5 overflow-x-auto">
                    <span class="text-slate-400 mr-1 font-semibold">Link:</span>
                    <button @click="selectedRelationFilter = 'all'; updateConnectorPositions()"
                            :class="selectedRelationFilter === 'all' ? 'bg-white/20 text-white font-medium' : 'text-slate-400 hover:text-white'"
                            class="px-2 py-0.5 rounded transition">
                        All
                    </button>
                    <button @click="selectedRelationFilter = 'confirms'; updateConnectorPositions()"
                            :class="selectedRelationFilter === 'confirms' ? 'bg-emerald-500/30 text-emerald-300 font-medium' : 'text-slate-400 hover:text-emerald-300'"
                            class="px-2 py-0.5 rounded transition flex items-center gap-1">
                        <span class="text-emerald-400">✓</span> Confirms
                    </button>
                    <button @click="selectedRelationFilter = 'refutes'; updateConnectorPositions()"
                            :class="selectedRelationFilter === 'refutes' ? 'bg-rose-500/30 text-rose-300 font-medium' : 'text-slate-400 hover:text-rose-300'"
                            class="px-2 py-0.5 rounded transition flex items-center gap-1">
                        <span class="text-rose-400">⚡</span> Refutes
                    </button>
                    <button @click="selectedRelationFilter = 'builds_on'; updateConnectorPositions()"
                            :class="selectedRelationFilter === 'builds_on' ? 'bg-cyan-500/30 text-cyan-300 font-medium' : 'text-slate-400 hover:text-cyan-300'"
                            class="px-2 py-0.5 rounded transition flex items-center gap-1">
                        <span class="text-cyan-400">↑</span> Builds On
                    </button>
                </div>

                <!-- Age Select -->
                <div class="flex items-center gap-2">
                    <label for="age-selector" class="text-slate-400 font-semibold">Era:</label>
                    <select id="age-selector"
                            x-model="selectedAgeFilter"
                            @change="updateConnectorPositions()"
                            class="bg-white/5 border border-white/10 rounded-lg px-2 py-1 text-xs text-white focus:outline-none focus:ring-1 focus:ring-cyan-500">
                        <option value="all">All 10 Eras</option>
                        <template x-for="age in ages" :key="age.id">
                            <option :value="age.id" x-text="age.name"></option>
                        </template>
                    </select>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content Area with Dynamic Canvas & Connected Nodes -->
    <main class="flex-1 relative z-10 overflow-y-auto" id="discovery-canvas-container" @scroll.passive="debounceUpdateConnectors()">
        
        <!-- Interactive SVG Line Connector Canvas Layer -->
        <svg class="absolute inset-0 pointer-events-none z-20 w-full h-full"
             :width="svgWidth"
             :height="svgHeight"
             xmlns="http://www.w3.org/2000/svg">
            
            <defs>
                <marker id="arrow-confirms" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1 L 10 5 L 0 9 z" fill="#10b981" />
                </marker>
                <marker id="arrow-refutes" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1 L 10 5 L 0 9 z" fill="#f43f5e" />
                </marker>
                <marker id="arrow-builds_on" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1 L 10 5 L 0 9 z" fill="#38bdf8" />
                </marker>
                <marker id="arrow-inspires" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1 L 10 5 L 0 9 z" fill="#fbbf24" />
                </marker>
                <marker id="arrow-anticipates" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1 L 10 5 L 0 9 z" fill="#c084fc" />
                </marker>
                <marker id="arrow-peer" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1 L 10 5 L 0 9 z" fill="#eab308" />
                </marker>
            </defs>

            <!-- Rendered Connecting Lines -->
            <template x-for="line in connectorLines" :key="line.id">
                <g class="transition-opacity duration-300"
                   :opacity="line.isDimmed ? 0.08 : (line.isRelevant ? 1 : 0.45)">
                    
                    <path x-show="line.isRelevant"
                          :d="line.path"
                          fill="none"
                          :stroke="line.color"
                          stroke-width="6"
                          stroke-opacity="0.35"
                          stroke-linecap="round" />

                    <path :d="line.path"
                          fill="none"
                          :stroke="line.color"
                          :stroke-width="line.isRelevant ? 2.5 : 1.5"
                          :class="line.isRelevant ? 'animate-dash-flow' : ''"
                          :marker-end="'url(#arrow-' + line.type + ')'"
                          stroke-linecap="round" />
                </g>
            </template>
        </svg>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-30">

            <!-- Hero Introduction -->
            <div class="text-center max-w-3xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-semibold mb-4">
                    <span>⚡ The Architecture of Scientific Revolutions</span>
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white mb-4">
                    The Experiment-Centric Knowledge Graph
                </h1>
                <p class="text-base text-slate-300 leading-relaxed">
                    Every breakthrough began with what was known before, a bold question, and a physical experiment. Trace how each discovery sparks confirmations, refutations, and "Wait, how is this possible?!" paradoxes.
                </p>

                <!-- Color Legend -->
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 text-xs">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300">
                        <span class="w-2.5 h-0.5 bg-emerald-400"></span>
                        <strong class="font-semibold">Confirms:</strong> Validates earlier hypothesis
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300">
                        <span class="w-2.5 h-0.5 bg-rose-400"></span>
                        <strong class="font-semibold">Refutes:</strong> Overturns earlier dogma
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-300">
                        <span class="w-2.5 h-0.5 bg-cyan-400"></span>
                        <strong class="font-semibold">Builds On:</strong> Direct progression
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-500/10 border border-yellow-500/20 text-yellow-300">
                        <span class="w-2.5 h-0.5 bg-yellow-400"></span>
                        <strong class="font-semibold">Peer:</strong> Contemporary dialogue
                    </div>
                </div>
            </div>

            <!-- CHRONOLOGY & QUEST & FILM & THREAD VIEWS (Experiment Cards) -->
            <div x-show="activeMode === 'timeline' || activeMode === 'quest' || activeMode === 'film' || activeMode === 'threads'" class="space-y-20">
                <template x-for="group in nodesByAge" :key="group.age.id">
                    <section :id="'age-section-' + group.age.id" class="relative">
                        
                        <!-- Macro-Shift Age Banner -->
                        <div class="mb-8 rounded-2xl glass-panel p-6 sm:p-8 relative overflow-hidden border"
                             :style="'border-color: ' + group.age.color + '40; background: radial-gradient(circle at top right, ' + group.age.bg_glow + ', rgba(11, 15, 25, 0.95))'">
                            
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-semibold uppercase tracking-wider px-2.5 py-1 rounded-md text-white"
                                              :style="'background-color: ' + group.age.color + '33; border: 1px solid ' + group.age.color + '66; color: ' + group.age.color"
                                              x-text="'Age ' + group.age.id"></span>
                                        <span class="text-xs text-slate-400 font-mono" x-text="group.age.period"></span>
                                    </div>
                                    <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight" x-text="group.age.name.replace(/^Age \d+:\s*/, '')"></h2>
                                    <p class="text-sm font-medium mt-1" :style="'color: ' + group.age.color" x-text="group.age.subtitle"></p>
                                </div>

                                <div class="shrink-0 text-right">
                                    <span class="text-xs text-slate-400 block mb-1">Experiments in this Era</span>
                                    <span class="text-2xl font-bold text-white" x-text="group.nodes.length"></span>
                                </div>
                            </div>

                            <!-- Paradigm Shift Concept Explanation -->
                            <div class="pt-4 border-t border-white/10 mt-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1.5">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    The Macro Paradigm Shift:
                                </span>
                                <p class="text-sm text-slate-200 leading-relaxed" x-text="group.age.paradigm_shift"></p>
                            </div>
                        </div>

                        <!-- Grid of Experiment Cards within this Age -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <template x-for="node in group.nodes" :key="node.id">
                                <div :id="'node-card-' + node.id"
                                     @mouseenter="hoverNode(node.id); updateConnectorPositions()"
                                     @mouseleave="unhoverNode(); updateConnectorPositions()"
                                     @click="selectNode(node.id)"
                                     :class="{
                                         'ring-2 ring-cyan-400 shadow-2xl shadow-cyan-500/20 bg-white/10': selectedNodeId === node.id,
                                         'border-white/30 bg-white/5': hoveredNodeId === node.id,
                                         'opacity-30': (hoveredNodeId || selectedNodeId) && !isNodeConnected(node.id)
                                     }"
                                     class="rounded-xl glass-panel glass-panel-hover p-5 cursor-pointer relative flex flex-col justify-between transition-all duration-200">
                                    
                                    <div>
                                        <!-- Header: Year & Type Badge -->
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300"
                                                  x-text="node.display_year"></span>
                                            
                                            <!-- Nature of Discovery Pill -->
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full border flex items-center gap-1.5"
                                                  :class="getTypeBadge(node.type).bg + ' ' + getTypeBadge(node.type).text + ' ' + getTypeBadge(node.type).border">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="getTypeBadge(node.type).dot"></span>
                                                <span x-text="getTypeBadge(node.type).label"></span>
                                            </span>
                                        </div>

                                        <!-- Experiment Title -->
                                        <h3 class="text-base font-bold text-white mb-1 group-hover:text-cyan-300 transition" x-text="node.title"></h3>
                                        <span class="text-xs text-cyan-400 font-medium block mb-3" x-text="node.person_name"></span>

                                        <!-- Prior Knowledge Callout -->
                                        <div class="mb-3 text-[11px] text-slate-400 bg-black/25 p-2 rounded-lg border border-white/5 leading-relaxed">
                                            <span class="text-amber-400 font-bold block mb-0.5">⏮️ Prior Mental Model:</span>
                                            "<span x-text="node.prior_knowledge"></span>"
                                        </div>

                                        <!-- The Driving Question -->
                                        <div class="mb-3 text-xs text-slate-300 italic bg-white/5 p-2 rounded-lg border border-white/5">
                                            <span class="text-slate-500 font-semibold block not-italic mb-0.5">The Wonder:</span>
                                            "<span x-text="node.question"></span>"
                                        </div>

                                        <!-- The Conclusion / Breakthrough -->
                                        <p class="text-xs text-slate-200 line-clamp-3 leading-relaxed mb-4" x-text="node.conclusion"></p>
                                    </div>

                                    <!-- Card Footer: Connections & Next Path Suggestion -->
                                    <div class="pt-3 border-t border-white/5 space-y-2.5">
                                        
                                        <!-- Upstream Connections (Roots) -->
                                        <template x-if="node.connections && node.connections.length > 0">
                                            <div class="flex flex-wrap gap-1.5 items-center">
                                                <span class="text-[10px] text-slate-500 uppercase font-semibold">Roots:</span>
                                                <template x-for="conn in node.connections.slice(0, 2)" :key="conn.target_id">
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded border"
                                                          :class="getRelationStyle(conn.type).class"
                                                          x-text="getRelationStyle(conn.type).label + ' ' + conn.target_name">
                                                    </span>
                                                </template>
                                                <span x-show="node.connections.length > 2" class="text-[10px] text-slate-500" x-text="'+' + (node.connections.length - 2)"></span>
                                            </div>
                                        </template>

                                        <!-- Next Path Prompt -->
                                        <template x-if="node.next_paths && node.next_paths.paradox_question">
                                            <div class="text-[11px] text-rose-300/90 italic bg-rose-500/5 p-1.5 rounded border border-rose-500/20 truncate">
                                                ⚡ <strong>Paradox:</strong> <span x-text="node.next_paths.paradox_question"></span>
                                            </div>
                                        </template>

                                        <div class="flex items-center justify-between text-[11px] text-cyan-400 font-medium pt-1">
                                            <span>Inspect full experiment →</span>
                                            <span x-show="node.macro_shift" class="text-amber-400 flex items-center gap-1 font-semibold text-[10px]">
                                                ★ Macro-Shift
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </template>
                        </div>

                    </section>
                </template>
            </div>

            <!-- MODE 5: CONSTELLATION MATRIX VIEW -->
            <div x-show="activeMode === 'constellation'" class="space-y-8">
                <div class="p-6 rounded-2xl glass-panel text-center max-w-2xl mx-auto">
                    <h3 class="text-xl font-bold text-white mb-2">Constellation Network Matrix</h3>
                    <p class="text-xs text-slate-300">
                        All 47 experiments plotted in chronological order. Hover any milestone to trace all predecessors (roots) and successors (fruits) across the entire network.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="node in filteredNodes" :key="'matrix-' + node.id">
                        <div :id="'node-card-' + node.id"
                             @mouseenter="hoverNode(node.id); updateConnectorPositions()"
                             @mouseleave="unhoverNode(); updateConnectorPositions()"
                             @click="selectNode(node.id)"
                             :class="{
                                 'ring-2 ring-cyan-400 bg-white/10': selectedNodeId === node.id,
                                 'border-white/30': hoveredNodeId === node.id,
                                 'opacity-30': (hoveredNodeId || selectedNodeId) && !isNodeConnected(node.id)
                             }"
                             class="p-4 rounded-xl glass-panel glass-panel-hover cursor-pointer transition relative">
                            
                            <div class="flex items-center justify-between gap-1 mb-1.5">
                                <span class="text-[10px] font-mono text-cyan-400" x-text="node.display_year"></span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded border" :class="getTypeBadge(node.type).bg + ' ' + getTypeBadge(node.type).text + ' ' + getTypeBadge(node.type).border" x-text="getTypeBadge(node.type).label"></span>
                            </div>

                            <h4 class="text-sm font-bold text-white truncate" x-text="node.title"></h4>
                            <span class="text-xs text-cyan-300 truncate block" x-text="node.person_name"></span>
                            <p class="text-xs text-slate-400 line-clamp-2 mt-1.5 leading-relaxed" x-text="node.conclusion"></p>

                            <div class="mt-3 flex items-center justify-between text-[10px] text-slate-500 pt-2 border-t border-white/5">
                                <span x-text="(node.connections ? node.connections.length : 0) + ' roots'"></span>
                                <span x-text="(node.descendants ? node.descendants.length : 0) + ' fruits'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- MODE 6: CHAPTER BOOK MODE (Narrative Reader) -->
            <div x-show="activeMode === 'book'" class="max-w-3xl mx-auto space-y-16">
                <template x-for="group in nodesByAge" :key="'book-' + group.age.id">
                    <article class="p-8 rounded-2xl glass-panel border border-white/10 space-y-6">
                        <div class="border-b border-white/10 pb-4">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-amber-400" x-text="'Chapter ' + group.age.id + ' • ' + group.age.period"></span>
                            <h2 class="text-3xl font-extrabold text-white mt-1" x-text="group.age.name.replace(/^Age \d+:\s*/, '')"></h2>
                            <p class="text-sm text-cyan-300 mt-1" x-text="group.age.subtitle"></p>
                        </div>

                        <!-- Macro shift narrative -->
                        <div class="p-4 rounded-xl bg-cyan-950/30 border border-cyan-500/20 text-slate-200 text-sm leading-relaxed">
                            <strong class="text-cyan-400 block mb-1">The Paradigm Shift:</strong>
                            <p x-text="group.age.paradigm_shift"></p>
                        </div>

                        <!-- Experiments as Narrative Episodes -->
                        <div class="space-y-8 pt-4">
                            <template x-for="node in group.nodes" :key="'book-node-' + node.id">
                                <div class="pl-4 border-l-2 border-white/10 hover:border-cyan-400 transition space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono text-cyan-400" x-text="node.display_year"></span>
                                        <span class="text-xs text-slate-400">•</span>
                                        <strong class="text-white text-base" x-text="node.title"></strong>
                                        <span class="text-xs text-slate-400" x-text="'by ' + node.person_name"></span>
                                    </div>
                                    <p class="text-xs text-slate-300 leading-relaxed" x-text="node.significance"></p>
                                    <div class="text-[11px] text-cyan-400 pt-1">
                                        <a :href="'/discovery/' + node.id" class="hover:underline">Read full discovery archive →</a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </article>
                </template>
            </div>

            <!-- MODE 7: CHALLENGE QUIZ MODE -->
            <div x-show="activeMode === 'quiz'" class="max-w-2xl mx-auto space-y-8">
                <div class="p-8 rounded-2xl glass-panel border border-purple-500/30 bg-purple-950/20 text-center">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-400 mb-1 block">Active Recall Challenge</span>
                    <h2 class="text-2xl font-bold text-white mb-2">Test Your Understanding of the 2,600-Year Chain</h2>
                    <p class="text-xs text-slate-300 mb-4">
                        Current Score: <strong class="text-white text-base" x-text="quizScore"></strong> points
                    </p>

                    <!-- Era Selector for Quizzes -->
                    <div class="flex flex-wrap items-center justify-center gap-1.5 text-xs">
                        <template x-for="q in quizzes" :key="q.id">
                            <button @click="selectedQuizAge = q.age_id; hasSubmittedAnswer = false; selectedAnswerIndex = null"
                                    :class="selectedQuizAge === q.age_id ? 'bg-purple-500 text-white font-bold' : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                                    class="px-3 py-1 rounded-lg transition"
                                    x-text="'Age ' + q.age_id">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Active Quiz Card -->
                <div x-show="activeQuiz" class="p-8 rounded-2xl glass-panel border border-white/10 space-y-6">
                    <div>
                        <span class="text-xs font-mono text-purple-400 uppercase font-bold" x-text="activeQuiz.title"></span>
                        <h3 class="text-lg font-bold text-white mt-1 leading-snug" x-text="activeQuiz.question"></h3>
                    </div>

                    <!-- Options -->
                    <div class="space-y-3">
                        <template x-for="(opt, idx) in activeQuiz.options" :key="idx">
                            <div @click="selectQuizAnswer(idx)"
                                 :class="{
                                     'border-purple-400 bg-purple-500/20 text-white': selectedAnswerIndex === idx && !hasSubmittedAnswer,
                                     'border-emerald-500 bg-emerald-500/20 text-emerald-200': hasSubmittedAnswer && idx === activeQuiz.correct_index,
                                     'border-rose-500 bg-rose-500/20 text-rose-200': hasSubmittedAnswer && selectedAnswerIndex === idx && idx !== activeQuiz.correct_index,
                                     'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10': selectedAnswerIndex !== idx && (!hasSubmittedAnswer || idx !== activeQuiz.correct_index)
                                 }"
                                 class="p-4 rounded-xl border cursor-pointer transition text-xs leading-relaxed flex items-center justify-between">
                                <span x-text="opt"></span>
                                <span x-show="hasSubmittedAnswer && idx === activeQuiz.correct_index" class="text-emerald-400 font-bold ml-2">✓ Correct</span>
                                <span x-show="hasSubmittedAnswer && selectedAnswerIndex === idx && idx !== activeQuiz.correct_index" class="text-rose-400 font-bold ml-2">✕ Incorrect</span>
                            </div>
                        </template>
                    </div>

                    <!-- Submit / Next Button -->
                    <div class="pt-4 border-t border-white/10 flex items-center justify-between">
                        <div x-show="hasSubmittedAnswer" class="text-xs text-slate-300 flex-1 pr-4">
                            <strong class="text-white block mb-0.5">Historical Explanation:</strong>
                            <p x-text="activeQuiz.explanation"></p>
                        </div>

                        <div>
                            <button x-show="!hasSubmittedAnswer"
                                    @click="submitQuizAnswer()"
                                    :disabled="selectedAnswerIndex === null"
                                    class="px-5 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-xs transition">
                                Submit Answer
                            </button>
                            <button x-show="hasSubmittedAnswer"
                                    @click="nextQuiz()"
                                    class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition">
                                Next Era Challenge →
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Deep Inspector Modal / Slide-Over -->
    <div x-show="inspectorOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-12"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-12"
         class="fixed inset-y-0 right-0 z-50 w-full sm:w-[32rem] bg-[#0b0f19] border-l border-white/10 shadow-2xl flex flex-col justify-between overflow-hidden"
         style="display: none;">
        
        <template x-if="selectedNode">
            <div class="h-full flex flex-col justify-between">
                
                <!-- Inspector Header -->
                <div class="p-6 border-b border-white/10 bg-[#0d121f]">
                    <div class="flex items-center justify-between gap-4 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20"
                                  x-text="selectedNode.display_year"></span>
                            
                            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full border flex items-center gap-1.5"
                                  :class="getTypeBadge(selectedNode.type).bg + ' ' + getTypeBadge(selectedNode.type).text + ' ' + getTypeBadge(selectedNode.type).border">
                                <span class="w-1.5 h-1.5 rounded-full" :class="getTypeBadge(selectedNode.type).dot"></span>
                                <span x-text="getTypeBadge(selectedNode.type).label"></span>
                            </span>
                        </div>

                        <!-- Close Button -->
                        <button @click="closeInspector()"
                                class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-400 hover:text-white transition">
                            ✕
                        </button>
                    </div>

                    <h2 class="text-2xl font-bold text-white tracking-tight" x-text="selectedNode.title"></h2>
                    <p class="text-xs text-cyan-400 mt-0.5 font-medium" x-text="selectedNode.person_name + ' • ' + (ageMap.get(selectedNode.age_id) || {}).name"></p>

                    <!-- Stepper -->
                    <div x-show="activeLineageChain.length > 1" class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between text-xs">
                        <span class="text-slate-400">
                            Genealogy Step: <strong class="text-white" x-text="(currentLineageIndex + 1) + ' of ' + activeLineageChain.length"></strong>
                        </span>
                        <div class="flex items-center gap-1">
                            <button @click="stepLineage(-1)"
                                    :disabled="currentLineageIndex <= 0"
                                    class="px-2.5 py-1 rounded bg-white/5 hover:bg-white/10 disabled:opacity-30 disabled:cursor-not-allowed text-white transition">
                                ← Older
                            </button>
                            <button @click="stepLineage(1)"
                                    :disabled="currentLineageIndex >= activeLineageChain.length - 1"
                                    class="px-2.5 py-1 rounded bg-white/5 hover:bg-white/10 disabled:opacity-30 disabled:cursor-not-allowed text-white transition">
                                Newer →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Inspector Scrollable Body -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    <!-- Prior Mental Model -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <span class="text-amber-400">⏮️</span> Prior Mental Model:
                        </h4>
                        <div class="p-3.5 rounded-xl bg-amber-500/5 border border-amber-500/20 text-slate-300 text-xs italic leading-relaxed">
                            "<span x-text="selectedNode.prior_knowledge"></span>"
                        </div>
                    </div>

                    <!-- The Driving Question -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <span class="text-cyan-400">❓</span> The Guiding Wonder:
                        </h4>
                        <div class="p-3.5 rounded-xl bg-cyan-500/5 border border-cyan-500/20 text-slate-200 text-xs leading-relaxed font-medium">
                            "<span x-text="selectedNode.question"></span>"
                        </div>
                    </div>

                    <!-- Method & Apparatus -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <span class="text-emerald-400">🔬</span> Method & Apparatus:
                        </h4>
                        <div class="p-3.5 rounded-xl bg-emerald-500/5 border border-emerald-500/20 text-slate-200 text-xs leading-relaxed font-mono">
                            <p x-text="selectedNode.method"></p>
                        </div>
                    </div>

                    <!-- The Core Breakthrough -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <span class="text-indigo-400">💡</span> The Core Breakthrough:
                        </h4>
                        <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 text-slate-100 text-xs leading-relaxed">
                            <p x-text="selectedNode.conclusion"></p>
                        </div>
                    </div>

                    <!-- NEXT POSSIBLE PATHS (Suggested Ways to Progress) -->
                    <template x-if="selectedNode.next_paths">
                        <div class="p-4 rounded-xl bg-gradient-to-br from-cyan-950/30 to-purple-950/30 border border-cyan-500/30 space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-cyan-300 flex items-center gap-1.5">
                                <span>🧭 Where Does This Discovery Lead Next?</span>
                            </h4>

                            <template x-if="selectedNode.next_paths.confirm_question">
                                <div class="text-xs bg-emerald-500/10 p-2.5 rounded border border-emerald-500/20 text-slate-300">
                                    <strong class="text-emerald-400 block mb-0.5">To Confirm:</strong>
                                    <span x-text="selectedNode.next_paths.confirm_question"></span>
                                </div>
                            </template>

                            <template x-if="selectedNode.next_paths.new_question">
                                <div class="text-xs bg-cyan-500/10 p-2.5 rounded border border-cyan-500/20 text-slate-300">
                                    <strong class="text-cyan-400 block mb-0.5">New Question:</strong>
                                    <span x-text="selectedNode.next_paths.new_question"></span>
                                </div>
                            </template>

                            <template x-if="selectedNode.next_paths.paradox_question">
                                <div class="text-xs bg-rose-500/10 p-2.5 rounded border border-rose-500/20 text-slate-300">
                                    <strong class="text-rose-400 block mb-0.5">"Wait, How is this possible?!":</strong>
                                    <span x-text="selectedNode.next_paths.paradox_question"></span>
                                </div>
                            </template>

                            <template x-if="selectedNode.next_paths.recommended_next_id">
                                <button @click="selectNode(selectedNode.next_paths.recommended_next_id); scrollToNode(selectedNode.next_paths.recommended_next_id)"
                                        class="w-full mt-2 py-2 px-3 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition flex items-center justify-center gap-1.5 shadow">
                                    <span>Follow Suggested Next Step →</span>
                                </button>
                            </template>
                        </div>
                    </template>

                    <!-- Ancestor Chain (Roots) -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
                            <span>Roots (What It Built On / Refuted):</span>
                            <span class="text-slate-500" x-text="(selectedNode.connections || []).length"></span>
                        </h4>

                        <div class="space-y-2">
                            <template x-for="conn in selectedNode.connections || []" :key="conn.target_id">
                                <div @click="selectNode(conn.target_id); scrollToNode(conn.target_id)"
                                     class="p-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-bold text-white hover:text-cyan-300"
                                              x-text="conn.target_name"></span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded border"
                                              :class="getRelationStyle(conn.type).class"
                                              x-text="getRelationStyle(conn.type).label"></span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono block mb-1" x-text="conn.target_title + ' (' + conn.target_year + ')'"></span>
                                    <p class="text-xs text-slate-300 leading-normal" x-text="conn.description"></p>
                                </div>
                            </template>

                            <div x-show="!selectedNode.connections || selectedNode.connections.length === 0"
                                 class="p-4 rounded-lg bg-white/5 text-slate-500 text-xs text-center">
                                The primordial root of our narrative in antiquity.
                            </div>
                        </div>
                    </div>

                    <!-- Descendant Chain (Fruits) -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
                            <span>Fruits (Subsequent Breakthroughs Sparked):</span>
                            <span class="text-slate-500" x-text="(selectedNode.descendants || []).length"></span>
                        </h4>

                        <div class="space-y-2">
                            <template x-for="desc in selectedNode.descendants || []" :key="desc.source_id">
                                <div @click="selectNode(desc.source_id); scrollToNode(desc.source_id)"
                                     class="p-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-bold text-white hover:text-cyan-300" x-text="desc.source_name"></span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded border"
                                              :class="getRelationStyle(desc.type).class"
                                              x-text="getRelationStyle(desc.type).label"></span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono block mb-1" x-text="desc.source_title + ' (' + desc.source_year + ')'"></span>
                                    <p class="text-xs text-slate-300 leading-normal" x-text="desc.description"></p>
                                </div>
                            </template>

                            <div x-show="!selectedNode.descendants || selectedNode.descendants.length === 0"
                                 class="p-4 rounded-lg bg-white/5 text-slate-500 text-xs text-center">
                                Modern horizon or frontier of current research.
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Inspector Footer with Jump to Dedicated Page -->
                <div class="p-4 border-t border-white/10 bg-[#0d121f] flex items-center justify-between">
                    <a :href="'/discovery/' + selectedNode.id"
                       class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition flex items-center gap-1.5">
                        <span>Open Dedicated Deep-Dive Page →</span>
                    </a>

                    <button @click="closeInspector()" class="text-xs text-slate-400 hover:text-white px-3 py-2">
                        Close
                    </button>
                </div>

            </div>
        </template>

    </div>

</body>
</html>
