<!DOCTYPE html>
<html lang="en" class="h-full bg-[#07090e]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $experiment['title'] }} | {{ $experiment['person_name'] }} | History of Science</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-['Instrument_Sans'] text-slate-100 overflow-x-hidden">

    <!-- Ambient background glows -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-900/20 rounded-full blur-[128px]"></div>
        <div class="absolute top-1/3 -right-40 w-[30rem] h-[30rem] bg-cyan-900/20 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[32rem] h-[32rem] bg-emerald-900/15 rounded-full blur-[140px]"></div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 border-b border-white/10 glass-panel bg-[#07090e]/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('history.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Cosmic Lineage Timeline</span>
            </a>

            <div class="flex items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded bg-white/5 border border-white/10 font-mono text-cyan-300">
                    {{ $experiment['display_year'] }}
                </span>
                <span class="px-2.5 py-1 rounded-full uppercase text-[10px] font-bold tracking-wider 
                    @if($experiment['type'] === 'observation') bg-amber-500/15 text-amber-400 border border-amber-500/30
                    @elseif($experiment['type'] === 'theory') bg-purple-500/15 text-purple-400 border border-purple-500/30
                    @else bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 @endif">
                    {{ ucfirst($experiment['type']) }}
                </span>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 relative z-10 max-w-4xl mx-auto px-4 sm:px-6 py-12">
        
        <!-- Breadcrumbs & Era Badge -->
        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 mb-6">
            <span>{{ $experiment['age']['name'] ?? 'The Great Journey' }}</span>
            <span>•</span>
            <span class="text-slate-300">{{ $experiment['person_nationality'] ?? '' }}</span>
            @if(!empty($experiment['person_lifespan']))
                <span>•</span>
                <span class="font-mono text-slate-500">({{ $experiment['person_lifespan'] }})</span>
            @endif
        </div>

        <!-- Title & Subheading -->
        <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-white mb-3">
            {{ $experiment['title'] }}
        </h1>
        <div class="text-xl font-medium text-cyan-400 mb-8 flex items-center gap-3">
            <span>{{ $experiment['person_name'] }}</span>
            @if(!empty($experiment['macro_shift']))
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-400/15 border border-amber-400/30 text-amber-300 font-semibold">
                    ★ Revolutionary Macro-Shift
                </span>
            @endif
        </div>

        <!-- 1. What Humanity Thought It Knew (Prior Knowledge) -->
        <section class="mb-10 rounded-2xl glass-panel p-6 sm:p-8 border border-white/10 relative overflow-hidden">
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-400 mb-2">
                <span>⏮️ Prior Mental Model (What Humanity Assumed):</span>
            </div>
            <p class="text-base text-slate-300 leading-relaxed italic">
                "{{ $experiment['prior_knowledge'] }}"
            </p>
        </section>

        <!-- 2. The Driving Question & Physical Method Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            
            <!-- Driving Question -->
            <div class="rounded-2xl glass-panel p-6 border border-white/10">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-400 mb-3">
                    <span>❓ The Guiding Wonder:</span>
                </div>
                <p class="text-sm text-slate-200 leading-relaxed font-medium">
                    "{{ $experiment['question'] }}"
                </p>
            </div>

            <!-- Apparatus & Method -->
            <div class="rounded-2xl glass-panel p-6 border border-white/10">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-400 mb-3">
                    <span>🔬 Apparatus & Experimental Steps:</span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed font-mono">
                    {{ $experiment['method'] }}
                </p>
            </div>

        </div>

        <!-- 3. Conclusion & Significance -->
        <section class="mb-10 rounded-2xl glass-panel p-6 sm:p-8 border border-white/10 bg-white/[0.02]">
            <div class="mb-6">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 block mb-2">
                    💡 The Core Breakthrough:
                </span>
                <p class="text-lg text-white font-medium leading-relaxed">
                    {{ $experiment['conclusion'] }}
                </p>
            </div>

            <div class="pt-6 border-t border-white/10">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">
                    🌍 Long-Term Significance in the 2,600-Year Chain:
                </span>
                <p class="text-sm text-slate-300 leading-relaxed">
                    {{ $experiment['significance'] }}
                </p>
            </div>
        </section>

        <!-- 4. Next Possible Paths: Suggesting How to Progress -->
        @if(!empty($experiment['next_paths']))
            <section class="mb-12 rounded-2xl glass-panel p-6 sm:p-8 border border-cyan-500/30 bg-gradient-to-br from-cyan-950/20 to-indigo-950/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>🧭 Where Does This Discovery Lead Next?</span>
                    </h3>
                    <span class="text-xs text-cyan-400 font-mono">Suggested Inquiries</span>
                </div>
                <p class="text-xs text-slate-300 mb-6">
                    Science rarely stops at an answer; each breakthrough forces three new paths into reality:
                </p>

                <div class="space-y-4">
                    <!-- Path 1: Confirm -->
                    @if(!empty($experiment['next_paths']['confirm_question']))
                        <div class="p-4 rounded-xl bg-emerald-500/5 border border-emerald-500/20 flex items-start gap-3">
                            <span class="text-emerald-400 text-lg shrink-0">✅</span>
                            <div>
                                <strong class="text-xs font-semibold text-emerald-300 block mb-1">To Confirm & Validate:</strong>
                                <p class="text-xs text-slate-300">{{ $experiment['next_paths']['confirm_question'] }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Path 2: New Question -->
                    @if(!empty($experiment['next_paths']['new_question']))
                        <div class="p-4 rounded-xl bg-cyan-500/5 border border-cyan-500/20 flex items-start gap-3">
                            <span class="text-cyan-400 text-lg shrink-0">🔍</span>
                            <div>
                                <strong class="text-xs font-semibold text-cyan-300 block mb-1">To Explore An Unlocked Horizon:</strong>
                                <p class="text-xs text-slate-300">{{ $experiment['next_paths']['new_question'] }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Path 3: The Paradox -->
                    @if(!empty($experiment['next_paths']['paradox_question']))
                        <div class="p-4 rounded-xl bg-rose-500/5 border border-rose-500/20 flex items-start gap-3">
                            <span class="text-rose-400 text-lg shrink-0">⚡</span>
                            <div>
                                <strong class="text-xs font-semibold text-rose-300 block mb-1">"Wait, How Is This Possible?!" (The Anomaly):</strong>
                                <p class="text-xs text-slate-300">{{ $experiment['next_paths']['paradox_question'] }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                @if(!empty($experiment['next_paths']['recommended_next_id']))
                    <div class="mt-6 pt-4 border-t border-white/10 flex justify-end">
                        <a href="{{ route('history.show', $experiment['next_paths']['recommended_next_id']) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition shadow-lg shadow-cyan-500/20">
                            <span>Follow Suggested Next Milestone →</span>
                        </a>
                    </div>
                @endif
            </section>
        @endif

        <!-- 5. Intellectual Connections (Ancestors & Descendants) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-white/10">
            
            <!-- Roots: What this idea built on / refuted -->
            <div>
                <h4 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                    <span>🌱 Intellectual Roots (Ancestors)</span>
                    <span class="text-xs text-slate-400">{{ count($experiment['connections']) }} links</span>
                </h4>

                <div class="space-y-3">
                    @forelse($experiment['connections'] as $conn)
                        <a href="{{ route('history.show', $conn['target_id']) }}"
                           class="block p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition group">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-bold text-white group-hover:text-cyan-300 transition">
                                    {{ $conn['target_name'] }}
                                </span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded border
                                    @if($conn['type'] === 'confirms') bg-emerald-500/10 text-emerald-400 border-emerald-500/30
                                    @elseif($conn['type'] === 'refutes') bg-rose-500/10 text-rose-400 border-rose-500/30
                                    @else bg-cyan-500/10 text-cyan-400 border-cyan-500/30 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $conn['type'])) }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-400 font-mono block mb-1">
                                {{ $conn['target_title'] }} ({{ $conn['target_year'] }})
                            </span>
                            <p class="text-xs text-slate-300 leading-relaxed">{{ $conn['description'] }}</p>
                        </a>
                    @empty
                        <div class="p-6 rounded-xl bg-white/5 text-slate-500 text-xs text-center">
                            The primordial root of our narrative in antiquity.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Fruits: What built on or confirmed/refuted this -->
            <div>
                <h4 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                    <span>🌿 Subsequent Fruits (Descendants)</span>
                    <span class="text-xs text-slate-400">{{ count($experiment['descendants']) }} sparked</span>
                </h4>

                <div class="space-y-3">
                    @forelse($experiment['descendants'] as $desc)
                        <a href="{{ route('history.show', $desc['source_id']) }}"
                           class="block p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition group">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-bold text-white group-hover:text-cyan-300 transition">
                                    {{ $desc['source_name'] }}
                                </span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded border
                                    @if($desc['type'] === 'confirms') bg-emerald-500/10 text-emerald-400 border-emerald-500/30
                                    @elseif($desc['type'] === 'refutes') bg-rose-500/10 text-rose-400 border-rose-500/30
                                    @else bg-cyan-500/10 text-cyan-400 border-cyan-500/30 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $desc['type'])) }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-400 font-mono block mb-1">
                                {{ $desc['source_title'] }} ({{ $desc['source_year'] }})
                            </span>
                            <p class="text-xs text-slate-300 leading-relaxed">{{ $desc['description'] }}</p>
                        </a>
                    @empty
                        <div class="p-6 rounded-xl bg-white/5 text-slate-500 text-xs text-center">
                            The current modern frontier of scientific research.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </main>

</body>
</html>
