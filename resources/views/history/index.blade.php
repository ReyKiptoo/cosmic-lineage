<!DOCTYPE html>
<html lang="en" class="h-full bg-[#07090e]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chronicles of Electromagnetism | History of Science</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-['Instrument_Sans'] overflow-x-hidden"
      x-data="historyApp({!! $jsonPayload !!})">

    <!-- Ambient background glows -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-900/20 rounded-full blur-[128px]"></div>
        <div class="absolute top-1/3 -right-40 w-[30rem] h-[30rem] bg-cyan-900/20 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[32rem] h-[32rem] bg-emerald-900/15 rounded-full blur-[140px]"></div>
    </div>

    <!-- Top Sticky Header -->
    <header class="sticky top-0 z-40 border-b border-white/10 glass-panel bg-[#07090e]/80">
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
                        <p class="text-xs text-slate-400 hidden sm:block">The 2,600-Year Race from Wonder to Electromagnetic & Quantum Unification</p>
                    </div>
                </div>

                <!-- Stats summary pills -->
                <div class="hidden lg:flex items-center gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-slate-300">
                        <strong class="text-white" x-text="stats.total_nodes"></strong> Thinkers
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300">
                        <strong x-text="stats.observations"></strong> Observations
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300">
                        <strong x-text="stats.experiments"></strong> Experiments
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-purple-500/10 border border-purple-500/30 text-purple-300">
                        <strong x-text="stats.theories"></strong> Theories
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-300">
                        <strong x-text="stats.refutations"></strong> Refutations
                    </span>
                </div>

                <!-- View switch & Quick Search -->
                <div class="flex items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative w-44 sm:w-64">
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Search thinker, idea, year..."
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 pl-8 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-2 text-slate-400 hover:text-white text-xs">✕</button>
                    </div>

                    <!-- View toggles -->
                    <div class="bg-white/5 border border-white/10 rounded-xl p-1 flex items-center gap-1">
                        <button @click="activeView = 'timeline'; $nextTick(() => updateConnectorPositions())"
                                :class="activeView === 'timeline' ? 'bg-cyan-500 text-slate-950 font-semibold shadow' : 'text-slate-400 hover:text-white'"
                                class="px-3 py-1 rounded-lg text-xs transition">
                            Chronology
                        </button>
                        <button @click="activeView = 'constellation'; $nextTick(() => updateConnectorPositions())"
                                :class="activeView === 'constellation' ? 'bg-cyan-500 text-slate-950 font-semibold shadow' : 'text-slate-400 hover:text-white'"
                                class="px-3 py-1 rounded-lg text-xs transition">
                            Matrix
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Filter Subbar -->
        <div class="border-t border-white/5 bg-[#0b0e17]/90 px-4 sm:px-6 lg:px-8 py-2.5">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-xs">
                
                <!-- Category Filters (Observation / Experiment / Theory) -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <span class="text-slate-400 mr-1 font-medium">Type:</span>
                    <button @click="selectedTypeFilter = 'all'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'all' ? 'bg-white text-slate-950 font-semibold' : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                            class="px-2.5 py-1 rounded-lg transition shrink-0">
                        All (<span x-text="nodes.length"></span>)
                    </button>
                    <button @click="selectedTypeFilter = 'observation'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'observation' ? 'bg-amber-400 text-slate-950 font-semibold' : 'bg-amber-500/10 text-amber-300 border border-amber-500/20 hover:bg-amber-500/20'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        Observation (<span x-text="stats.observations"></span>)
                    </button>
                    <button @click="selectedTypeFilter = 'experiment'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'experiment' ? 'bg-emerald-400 text-slate-950 font-semibold' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 hover:bg-emerald-500/20'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Experiment (<span x-text="stats.experiments"></span>)
                    </button>
                    <button @click="selectedTypeFilter = 'theory'; updateConnectorPositions()"
                            :class="selectedTypeFilter === 'theory' ? 'bg-purple-400 text-slate-950 font-semibold' : 'bg-purple-500/10 text-purple-300 border border-purple-500/20 hover:bg-purple-500/20'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                        Theory (<span x-text="stats.theories"></span>)
                    </button>
                </div>

                <!-- Dynamics Filter (Confirms / Refutes / Builds on) -->
                <div class="flex items-center gap-1.5 overflow-x-auto">
                    <span class="text-slate-400 mr-1 font-medium">Link:</span>
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

                <!-- Age Jump Select -->
                <div class="flex items-center gap-2">
                    <label for="age-selector" class="text-slate-400 font-medium">Age:</label>
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
                <!-- Line Arrowheads -->
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
            </defs>

            <!-- Rendered Connecting Lines -->
            <template x-for="line in connectorLines" :key="line.id">
                <g class="transition-opacity duration-300"
                   :opacity="line.isDimmed ? 0.08 : (line.isRelevant ? 1 : 0.45)">
                    
                    <!-- Glow halo on highlighted links -->
                    <path x-show="line.isRelevant"
                          :d="line.path"
                          fill="none"
                          :stroke="line.color"
                          stroke-width="6"
                          stroke-opacity="0.35"
                          stroke-linecap="round" />

                    <!-- Main path curve with animated dash on selection -->
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
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-medium mb-4">
                    <span>⚡ The Genesis of Electromagnetic Science</span>
                </div>
                <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-white mb-4">
                    The Architecture of Scientific Revolutions
                </h1>
                <p class="text-base text-slate-300 leading-relaxed">
                    Trace how ideas build upon one another, how future experiments vindicate early hypotheses, and how daring discoveries completely overturn centuries of dogma. Hover any card to illuminate its connections.
                </p>

                <!-- Color Legend -->
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300">
                        <span class="w-2.5 h-0.5 bg-emerald-400"></span>
                        <strong class="font-semibold">Confirms:</strong> Vindicates past theory
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300">
                        <span class="w-2.5 h-0.5 bg-rose-400"></span>
                        <strong class="font-semibold">Refutes:</strong> Overturns earlier model
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-300">
                        <span class="w-2.5 h-0.5 bg-cyan-400"></span>
                        <strong class="font-semibold">Builds On:</strong> Direct progression
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-300">
                        <span class="w-2.5 h-0.5 bg-purple-400"></span>
                        <strong class="font-semibold">Anticipates:</strong> Ahead of its time
                    </div>
                </div>
            </div>

            <!-- CHRONOLOGY VIEW (River of Ages) -->
            <div x-show="activeView === 'timeline'" class="space-y-20">
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
                                    <span class="text-xs text-slate-400 block mb-1">Milestones in this Era</span>
                                    <span class="text-2xl font-bold text-white" x-text="group.nodes.length"></span>
                                </div>
                            </div>

                            <!-- Macro-Shift Concept Explanation -->
                            <div class="pt-4 border-t border-white/10 mt-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1.5">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    The Macro-Shift (Paradigm Leap):
                                </span>
                                <p class="text-sm text-slate-200 leading-relaxed" x-text="group.age.macro_shift"></p>
                            </div>
                        </div>

                        <!-- Grid of Nodes within this Age -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <template x-for="node in group.nodes" :key="node.id">
                                <div :id="'node-card-' + node.id"
                                     @mouseenter="hoverNode(node.id); updateConnectorPositions()"
                                     @mouseleave="unhoverNode(); updateConnectorPositions()"
                                     @click="selectNode(node.id)"
                                     :class="{
                                         'ring-2 ring-cyan-400 shadow-xl shadow-cyan-500/20': selectedNodeId === node.id,
                                         'border-white/30 bg-white/10': hoveredNodeId === node.id,
                                         'opacity-40': (hoveredNodeId || selectedNodeId) && !isNodeConnected(node.id)
                                     }"
                                     class="rounded-xl glass-panel glass-panel-hover p-5 cursor-pointer relative flex flex-col justify-between transition-all duration-200">
                                    
                                    <div>
                                        <!-- Card Header: Year & Type Badge -->
                                        <div class="flex items-center justify-between gap-2 mb-3">
                                            <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300"
                                                  x-text="node.display_year"></span>
                                            
                                            <!-- Nature of Discovery Pill -->
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full border flex items-center gap-1.5"
                                                  :class="getTypeBadge(node.type).bg + ' ' + getTypeBadge(node.type).text + ' ' + getTypeBadge(node.type).border">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="getTypeBadge(node.type).dot"></span>
                                                <span x-text="getTypeBadge(node.type).label"></span>
                                            </span>
                                        </div>

                                        <!-- Scientist / Thinker Name -->
                                        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-cyan-300 transition" x-text="node.name"></h3>

                                        <!-- The Guiding Question -->
                                        <div class="mb-3 text-xs text-slate-400 italic bg-black/20 p-2.5 rounded-lg border border-white/5">
                                            <span class="text-slate-500 font-semibold block not-italic mb-0.5">The Curiosity:</span>
                                            "<span x-text="node.question"></span>"
                                        </div>

                                        <!-- The Breakthrough Discovery -->
                                        <p class="text-xs text-slate-200 line-clamp-3 leading-relaxed mb-4" x-text="node.discovery"></p>
                                    </div>

                                    <!-- Card Footer: Quick Connections Indicator -->
                                    <div class="pt-3 border-t border-white/5 space-y-2">
                                        
                                        <!-- Upstream Connections (Roots) -->
                                        <template x-if="node.connections && node.connections.length > 0">
                                            <div class="flex flex-wrap gap-1.5 items-center">
                                                <span class="text-[10px] text-slate-500 uppercase font-semibold">Builds:</span>
                                                <template x-for="conn in node.connections" :key="conn.target_id">
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded border"
                                                          :class="getRelationStyle(conn.type).class"
                                                          x-text="getRelationStyle(conn.type).label + ' ' + (nodeMap.get(conn.target_id) || {}).name">
                                                    </span>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Downstream Impact (Fruits) -->
                                        <template x-if="node.descendants && node.descendants.length > 0">
                                            <div class="flex flex-wrap gap-1.5 items-center">
                                                <span class="text-[10px] text-slate-500 uppercase font-semibold">Impacts:</span>
                                                <template x-for="desc in node.descendants.slice(0, 2)" :key="desc.source_id">
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-slate-300"
                                                          x-text="desc.source_name">
                                                    </span>
                                                </template>
                                                <span x-show="node.descendants.length > 2"
                                                      class="text-[10px] text-slate-400"
                                                      x-text="'+' + (node.descendants.length - 2) + ' more'"></span>
                                            </div>
                                        </template>

                                        <div class="flex items-center justify-between text-[11px] text-cyan-400 font-medium pt-1">
                                            <span>Inspect connections →</span>
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

            <!-- CONSTELLATION MATRIX VIEW -->
            <div x-show="activeView === 'constellation'" class="space-y-12">
                <div class="p-6 rounded-2xl glass-panel text-center max-w-2xl mx-auto">
                    <h3 class="text-xl font-bold text-white mb-2">Constellation Matrix</h3>
                    <p class="text-xs text-slate-300">
                        View all 47 nodes arranged across chronological eras. Hover any milestone to trace all predecessors and successors across the entire network.
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
                                <span class="w-2 h-2 rounded-full" :class="getTypeBadge(node.type).dot"></span>
                            </div>

                            <h4 class="text-sm font-bold text-white truncate" x-text="node.name"></h4>
                            <p class="text-xs text-slate-400 line-clamp-2 mt-1" x-text="node.discovery"></p>

                            <div class="mt-3 flex items-center justify-between text-[10px] text-slate-500">
                                <span x-text="(node.connections ? node.connections.length : 0) + ' roots'"></span>
                                <span x-text="(node.descendants ? node.descendants.length : 0) + ' impacts'"></span>
                            </div>
                        </div>
                    </template>
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
                            
                            <!-- Type Badge -->
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

                    <h2 class="text-2xl font-bold text-white tracking-tight" x-text="selectedNode.name"></h2>
                    <p class="text-xs text-slate-400 mt-1" x-text="(ageMap.get(selectedNode.age_id) || {}).name"></p>

                    <!-- Interactive Lineage Step Navigation -->
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

                    <!-- The Driving Question -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <span class="text-amber-400">❓</span> The Guiding Wonder:
                        </h4>
                        <div class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/20 text-slate-200 text-sm italic leading-relaxed">
                            "<span x-text="selectedNode.question"></span>"
                        </div>
                    </div>

                    <!-- The Breakthrough Discovery -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <span class="text-cyan-400">💡</span> The Core Breakthrough:
                        </h4>
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 text-slate-100 text-sm leading-relaxed">
                            <p x-text="selectedNode.discovery"></p>
                        </div>
                    </div>

                    <!-- The Apparatus / Method -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <span class="text-emerald-400">🔬</span> Experimental Apparatus & Method:
                        </h4>
                        <div class="p-4 rounded-xl bg-emerald-500/5 border border-emerald-500/20 text-slate-200 text-xs leading-relaxed font-mono">
                            <p x-text="selectedNode.experiment_details"></p>
                        </div>
                    </div>

                    <!-- Ancestor Chain (What this idea built upon / refuted) -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
                            <span>Direct Intellectual Ancestors (Roots):</span>
                            <span class="text-slate-500" x-text="(selectedNode.connections || []).length"></span>
                        </h4>

                        <div class="space-y-2">
                            <template x-for="conn in selectedNode.connections || []" :key="conn.target_id">
                                <div @click="selectNode(conn.target_id); scrollToNode(conn.target_id)"
                                     class="p-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-bold text-white hover:text-cyan-300"
                                              x-text="(nodeMap.get(conn.target_id) || {}).name"></span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded border"
                                              :class="getRelationStyle(conn.type).class"
                                              x-text="getRelationStyle(conn.type).label"></span>
                                    </div>
                                    <p class="text-xs text-slate-400 leading-normal" x-text="conn.description"></p>
                                </div>
                            </template>

                            <div x-show="!selectedNode.connections || selectedNode.connections.length === 0"
                                 class="p-4 rounded-lg bg-white/5 text-slate-500 text-xs text-center">
                                The primordial root of our narrative in antiquity.
                            </div>
                        </div>
                    </div>

                    <!-- Descendant Chain (What this idea sparked or confirmed/refuted later) -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
                            <span>Subsequent Breakthroughs Sparked (Fruits):</span>
                            <span class="text-slate-500" x-text="(selectedNode.descendants || []).length"></span>
                        </h4>

                        <div class="space-y-2">
                            <template x-for="desc in selectedNode.descendants || []" :key="desc.source_id">
                                <div @click="selectNode(desc.source_id); scrollToNode(desc.source_id)"
                                     class="p-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-bold text-white hover:text-cyan-300" x-text="desc.source_name"></span>
                                            <span class="text-[10px] text-slate-500" x-text="'(' + desc.source_year + ')'"></span>
                                        </div>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded border"
                                              :class="getRelationStyle(desc.type).class"
                                              x-text="getRelationStyle(desc.type).label"></span>
                                    </div>
                                    <p class="text-xs text-slate-400 leading-normal" x-text="desc.description"></p>
                                </div>
                            </template>

                            <div x-show="!selectedNode.descendants || selectedNode.descendants.length === 0"
                                 class="p-4 rounded-lg bg-white/5 text-slate-500 text-xs text-center">
                                Modern horizon or frontier of current research.
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Inspector Footer with Jump to Canvas Button -->
                <div class="p-4 border-t border-white/10 bg-[#0d121f] flex items-center justify-between">
                    <button @click="scrollToNode(selectedNode.id)"
                            class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold text-xs transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Focus in Canvas
                    </button>

                    <button @click="closeInspector()" class="text-xs text-slate-400 hover:text-white px-3 py-2">
                        Close
                    </button>
                </div>

            </div>
        </template>

    </div>

</body>
</html>
