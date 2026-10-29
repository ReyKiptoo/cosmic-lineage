export function historyApp(initialData) {
    const data = initialData || window.__HISTORY_DATA__ || { ages: [], nodes: [], stats: {}, threads: [], quizzes: [] };

    return {
        ages: data.ages || [],
        nodes: data.nodes || [],
        stats: data.stats || {},
        threads: data.threads || [],
        quizzes: data.quizzes || [],
        
        // Active Navigation Paradigm
        // 'quest' | 'threads' | 'film' | 'timeline' | 'constellation' | 'book' | 'quiz'
        activeMode: 'timeline',
        
        // Mode 1: Quest / Progressive Disclosure State
        unlockedAge: 1,
        exploredNodes: new Set(),
        
        // Mode 2: Guided Storylines State
        activeThreadId: null,
        activeThreadIndex: 0,
        
        // Mode 3: Timeline Film Playback State
        isPlaying: false,
        playbackSpeed: 1, // 0.5, 1, 2
        currentPlaybackIndex: 0,
        playbackTimer: null,
        
        // Mode 7: Quiz State
        selectedQuizAge: 1,
        selectedAnswerIndex: null,
        hasSubmittedAnswer: false,
        quizScore: 0,
        answeredQuizzes: new Set(),
        
        // General Filters
        selectedTypeFilter: 'all', // 'all', 'observation', 'experiment', 'theory', 'invention'
        selectedAgeFilter: 'all', // 'all', or age_id (1-10)
        selectedRelationFilter: 'all', // 'all', 'refutes', 'confirms', 'builds_on'
        searchQuery: '',
        
        // Inspector Modal / Slide-Over
        selectedNodeId: null,
        hoveredNodeId: null,
        inspectorOpen: false,
        activeLineageChain: [],
        currentLineageIndex: -1,
        
        // Dynamic SVG Line Connectors
        connectorLines: [],
        svgWidth: 1200,
        svgHeight: 2000,
        
        async init() {
            // Restore saved quest progress from localStorage if available
            try {
                const savedAge = localStorage.getItem('cosmic_unlocked_age');
                if (savedAge) this.unlockedAge = Math.max(1, parseInt(savedAge, 10));
                const savedExplored = localStorage.getItem('cosmic_explored_nodes');
                if (savedExplored) this.exploredNodes = new Set(JSON.parse(savedExplored));
            } catch (e) {
                // Ignore localStorage errors
            }

            // Fallback fetch if data is empty
            if (!this.nodes || this.nodes.length === 0) {
                try {
                    const res = await fetch('/api/history');
                    const remoteData = await res.json();
                    this.ages = remoteData.ages || [];
                    this.nodes = remoteData.nodes || [];
                    this.stats = remoteData.stats || {};
                    this.threads = remoteData.threads || [];
                    this.quizzes = remoteData.quizzes || [];
                } catch (err) {
                    console.error('Failed to load history data from API', err);
                }
            }

            // Build lookup index
            this.nodeMap = new Map(this.nodes.map(n => [n.id, n]));
            this.ageMap = new Map(this.ages.map(a => [a.id, a]));

            // Keyboard navigation
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (this.inspectorOpen) this.closeInspector();
                    if (this.isPlaying) this.pausePlayback();
                }
                if (e.key === ' ' && this.activeMode === 'film') {
                    e.preventDefault();
                    this.togglePlayback();
                }
            });

            // Recompute SVG connectors after DOM mount
            this.$nextTick(() => {
                this.updateConnectorPositions();
            });

            window.addEventListener('resize', () => {
                this.debounceUpdateConnectors();
            });
        },

        debounceUpdateConnectors() {
            if (this._resizeTimer) clearTimeout(this._resizeTimer);
            this._resizeTimer = setTimeout(() => {
                this.updateConnectorPositions();
            }, 100);
        },

        // Switch interaction mode
        setMode(mode) {
            if (this.isPlaying && mode !== 'film') {
                this.pausePlayback();
            }
            this.activeMode = mode;
            if (mode === 'threads' && !this.activeThreadId && this.threads.length > 0) {
                this.selectThread(this.threads[0].id);
            }
            this.$nextTick(() => {
                this.updateConnectorPositions();
            });
        },

        // --- MODE 1: QUEST / PROGRESSIVE DISCLOSURE ---
        unlockNextAge() {
            if (this.unlockedAge < this.ages.length) {
                this.unlockedAge++;
                try {
                    localStorage.setItem('cosmic_unlocked_age', this.unlockedAge);
                } catch (e) {}
                this.$nextTick(() => {
                    const el = document.getElementById('age-section-' + this.unlockedAge);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    this.updateConnectorPositions();
                });
            }
        },

        unlockAllAges() {
            this.unlockedAge = this.ages.length;
            this.updateConnectorPositions();
        },

        resetQuest() {
            this.unlockedAge = 1;
            this.exploredNodes.clear();
            try {
                localStorage.removeItem('cosmic_unlocked_age');
                localStorage.removeItem('cosmic_explored_nodes');
            } catch (e) {}
            this.updateConnectorPositions();
        },

        markNodeExplored(nodeId) {
            this.exploredNodes.add(nodeId);
            try {
                localStorage.setItem('cosmic_explored_nodes', JSON.stringify([...this.exploredNodes]));
            } catch (e) {}
        },

        get currentAgeExploredCount() {
            const ageNodes = this.nodes.filter(n => n.age_id === this.unlockedAge);
            return ageNodes.filter(n => this.exploredNodes.has(n.id)).length;
        },

        get currentAgeTotalCount() {
            return this.nodes.filter(n => n.age_id === this.unlockedAge).length;
        },

        get canUnlockNextAge() {
            return this.unlockedAge < this.ages.length && (this.currentAgeExploredCount >= Math.min(2, this.currentAgeTotalCount));
        },

        // --- MODE 2: GUIDED STORYLINE THREADS ---
        selectThread(threadId) {
            this.activeThreadId = threadId;
            this.activeThreadIndex = 0;
            const thread = this.threads.find(t => t.id === threadId);
            if (thread && thread.experiment_ids && thread.experiment_ids.length > 0) {
                this.scrollToNode(thread.experiment_ids[0]);
            }
            this.$nextTick(() => {
                this.updateConnectorPositions();
            });
        },

        get activeThread() {
            return this.threads.find(t => t.id === this.activeThreadId) || null;
        },

        get activeThreadNodes() {
            if (!this.activeThread) return [];
            return this.activeThread.experiment_ids
                .map(id => this.nodeMap.get(id))
                .filter(Boolean);
        },

        stepThread(direction) {
            const nodes = this.activeThreadNodes;
            const nextIdx = this.activeThreadIndex + direction;
            if (nextIdx >= 0 && nextIdx < nodes.length) {
                this.activeThreadIndex = nextIdx;
                const node = nodes[nextIdx];
                this.selectNode(node.id);
                this.scrollToNode(node.id);
            }
        },

        // --- MODE 3: TIMELINE FILM PLAYBACK ---
        togglePlayback() {
            if (this.isPlaying) {
                this.pausePlayback();
            } else {
                this.startPlayback();
            }
        },

        startPlayback() {
            this.isPlaying = true;
            this.runPlaybackStep();
        },

        pausePlayback() {
            this.isPlaying = false;
            if (this.playbackTimer) clearTimeout(this.playbackTimer);
        },

        runPlaybackStep() {
            if (!this.isPlaying) return;

            if (this.currentPlaybackIndex >= this.nodes.length) {
                this.currentPlaybackIndex = 0;
            }

            const currentNode = this.nodes[this.currentPlaybackIndex];
            if (currentNode) {
                this.selectedNodeId = currentNode.id;
                this.scrollToNode(currentNode.id);
                this.updateConnectorPositions();
            }

            const delay = (3800 / this.playbackSpeed);
            this.playbackTimer = setTimeout(() => {
                if (this.isPlaying) {
                    this.currentPlaybackIndex++;
                    if (this.currentPlaybackIndex < this.nodes.length) {
                        this.runPlaybackStep();
                    } else {
                        this.pausePlayback();
                    }
                }
            }, delay);
        },

        seekPlayback(index) {
            this.currentPlaybackIndex = Math.max(0, Math.min(this.nodes.length - 1, index));
            const node = this.nodes[this.currentPlaybackIndex];
            if (node) {
                this.selectedNodeId = node.id;
                this.scrollToNode(node.id);
                this.updateConnectorPositions();
            }
        },

        // --- MODE 7: QUIZ CHALLENGE ---
        get activeQuiz() {
            return this.quizzes.find(q => q.age_id === parseInt(this.selectedQuizAge, 10)) || this.quizzes[0];
        },

        selectQuizAnswer(idx) {
            if (this.hasSubmittedAnswer) return;
            this.selectedAnswerIndex = idx;
        },

        submitQuizAnswer() {
            if (this.selectedAnswerIndex === null) return;
            this.hasSubmittedAnswer = true;
            const quiz = this.activeQuiz;
            if (quiz && this.selectedAnswerIndex === quiz.correct_index) {
                if (!this.answeredQuizzes.has(quiz.id)) {
                    this.answeredQuizzes.add(quiz.id);
                    this.quizScore += 100;
                }
            }
        },

        nextQuiz() {
            this.hasSubmittedAnswer = false;
            this.selectedAnswerIndex = null;
            const currentIdx = this.quizzes.findIndex(q => q.id === this.activeQuiz.id);
            const nextIdx = (currentIdx + 1) % this.quizzes.length;
            this.selectedQuizAge = this.quizzes[nextIdx].age_id;
        },

        // --- FILTERING & GROUPING ---
        get filteredNodes() {
            return this.nodes.filter(node => {
                // Quest mode constraint
                if (this.activeMode === 'quest' && node.age_id > this.unlockedAge) {
                    return false;
                }
                // Storyline mode constraint
                if (this.activeMode === 'threads' && this.activeThread) {
                    if (!this.activeThread.experiment_ids.includes(node.id)) return false;
                }
                // Type filter
                if (this.selectedTypeFilter !== 'all' && node.type !== this.selectedTypeFilter) {
                    return false;
                }
                // Age filter
                if (this.selectedAgeFilter !== 'all' && node.age_id !== parseInt(this.selectedAgeFilter, 10)) {
                    return false;
                }
                // Relation filter
                if (this.selectedRelationFilter !== 'all') {
                    const hasRelation = (node.connections || []).some(c => c.type === this.selectedRelationFilter);
                    const isTargetOfRelation = (node.descendants || []).some(d => d.type === this.selectedRelationFilter);
                    if (!hasRelation && !isTargetOfRelation) return false;
                }
                // Search query
                if (this.searchQuery && this.searchQuery.trim() !== '') {
                    const q = this.searchQuery.toLowerCase();
                    const matchName = (node.person_name || node.name || '').toLowerCase().includes(q);
                    const matchTitle = (node.title || '').toLowerCase().includes(q);
                    const matchDisc = (node.conclusion || node.discovery || '').toLowerCase().includes(q);
                    const matchQuest = (node.question || '').toLowerCase().includes(q);
                    const matchPrior = (node.prior_knowledge || '').toLowerCase().includes(q);
                    const matchYear = String(node.display_year || '').toLowerCase().includes(q);
                    return matchName || matchTitle || matchDisc || matchQuest || matchPrior || matchYear;
                }
                return true;
            });
        },

        get nodesByAge() {
            const groups = [];
            const filteredSet = new Set(this.filteredNodes.map(n => n.id));

            for (const age of this.ages) {
                // In quest mode, do not show locked eras
                if (this.activeMode === 'quest' && age.id > this.unlockedAge) {
                    continue;
                }

                const ageNodes = this.nodes.filter(n => n.age_id === age.id && filteredSet.has(n.id));
                if (ageNodes.length > 0 || (this.selectedAgeFilter === 'all' && (!this.searchQuery || this.searchQuery === ''))) {
                    groups.push({
                        age,
                        nodes: ageNodes
                    });
                }
            }
            return groups;
        },

        get selectedNode() {
            return (this.nodeMap && this.selectedNodeId) ? this.nodeMap.get(this.selectedNodeId) || null : null;
        },

        // --- SELECTION & INSPECTOR ---
        selectNode(nodeId) {
            this.selectedNodeId = nodeId;
            this.markNodeExplored(nodeId);
            this.inspectorOpen = true;
            this.buildLineageChain(nodeId);
            this.$nextTick(() => {
                this.updateConnectorPositions();
            });
        },

        closeInspector() {
            this.inspectorOpen = false;
            this.selectedNodeId = null;
            this.activeLineageChain = [];
            this.currentLineageIndex = -1;
            this.updateConnectorPositions();
        },

        hoverNode(nodeId) {
            this.hoveredNodeId = nodeId;
        },

        unhoverNode() {
            this.hoveredNodeId = null;
        },

        scrollToNode(nodeId) {
            const el = document.getElementById('node-card-' + nodeId);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                el.classList.add('ring-2', 'ring-cyan-400', 'shadow-2xl');
                setTimeout(() => {
                    el.classList.remove('ring-2', 'ring-cyan-400', 'shadow-2xl');
                }, 1800);
            }
        },

        buildLineageChain(startNodeId) {
            const chain = [];
            const visited = new Set();
            let current = startNodeId;

            while (current && !visited.has(current)) {
                visited.add(current);
                chain.push(current);
                const node = this.nodeMap.get(current);
                if (node && node.connections && node.connections.length > 0) {
                    const primary = node.connections.find(c => c.type === 'builds_on') || node.connections[0];
                    current = primary ? primary.target_id : null;
                } else {
                    current = null;
                }
            }

            this.activeLineageChain = chain.reverse();
            this.currentLineageIndex = this.activeLineageChain.indexOf(startNodeId);
        },

        stepLineage(direction) {
            const nextIdx = this.currentLineageIndex + direction;
            if (nextIdx >= 0 && nextIdx < this.activeLineageChain.length) {
                this.currentLineageIndex = nextIdx;
                const nextId = this.activeLineageChain[nextIdx];
                this.selectedNodeId = nextId;
                this.markNodeExplored(nextId);
                this.scrollToNode(nextId);
            }
        },

        isNodeConnected(nodeId) {
            const activeId = this.hoveredNodeId || this.selectedNodeId;
            if (!activeId) return false;
            if (nodeId === activeId) return true;

            const active = this.nodeMap.get(activeId);
            if (!active) return false;

            const isAncestor = (active.connections || []).some(c => c.target_id === nodeId);
            const isDescendant = (active.descendants || []).some(d => d.source_id === nodeId);

            return isAncestor || isDescendant;
        },

        // Badges and styles
        getTypeBadge(type) {
            switch (type) {
                case 'observation':
                    return { label: 'Observation', bg: 'bg-amber-500/15', text: 'text-amber-400', border: 'border-amber-500/30', dot: 'bg-amber-400', icon: '👁️' };
                case 'invention':
                    return { label: 'Invention', bg: 'bg-cyan-500/15', text: 'text-cyan-400', border: 'border-cyan-500/30', dot: 'bg-cyan-400', icon: '⚡' };
                case 'theory':
                    return { label: 'Theory', bg: 'bg-purple-500/15', text: 'text-purple-400', border: 'border-purple-500/30', dot: 'bg-purple-400', icon: '💡' };
                case 'experiment':
                default:
                    return { label: 'Experiment', bg: 'bg-emerald-500/15', text: 'text-emerald-400', border: 'border-emerald-500/30', dot: 'bg-emerald-400', icon: '🔬' };
            }
        },

        getRelationStyle(type) {
            switch (type) {
                case 'confirms':
                    return { color: '#10b981', label: 'Confirms', class: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30' };
                case 'refutes':
                    return { color: '#f43f5e', label: 'Refutes', class: 'text-rose-400 bg-rose-500/10 border-rose-500/30' };
                case 'anticipates':
                    return { color: '#c084fc', label: 'Anticipates', class: 'text-purple-400 bg-purple-500/10 border-purple-500/30' };
                case 'inspires':
                    return { color: '#fbbf24', label: 'Inspires', class: 'text-amber-400 bg-amber-500/10 border-amber-500/30' };
                case 'peer':
                    return { color: '#eab308', label: 'Peer Work', class: 'text-yellow-400 bg-yellow-500/10 border-yellow-500/30' };
                case 'builds_on':
                default:
                    return { color: '#38bdf8', label: 'Builds On', class: 'text-cyan-400 bg-cyan-500/10 border-cyan-500/30' };
            }
        },

        // Compute SVG cubic bezier lines between cards
        updateConnectorPositions() {
            const container = document.getElementById('discovery-canvas-container');
            if (!container) return;

            const containerRect = container.getBoundingClientRect();
            this.svgWidth = container.scrollWidth || containerRect.width;
            this.svgHeight = container.scrollHeight || containerRect.height;

            const lines = [];
            const activeId = this.hoveredNodeId || this.selectedNodeId;
            const visibleIds = new Set(this.filteredNodes.map(n => n.id));

            for (const node of this.filteredNodes) {
                const sourceEl = document.getElementById('node-card-' + node.id);
                if (!sourceEl) continue;

                const sRect = sourceEl.getBoundingClientRect();
                const sx = (sRect.left - containerRect.left) + sRect.width / 2 + container.scrollLeft;
                const sy = (sRect.top - containerRect.top) + sRect.height / 2 + container.scrollTop;

                for (const conn of node.connections || []) {
                    if (!visibleIds.has(conn.target_id)) continue;

                    const targetEl = document.getElementById('node-card-' + conn.target_id);
                    if (!targetEl) continue;

                    const tRect = targetEl.getBoundingClientRect();
                    const tx = (tRect.left - containerRect.left) + tRect.width / 2 + container.scrollLeft;
                    const ty = (tRect.top - containerRect.top) + tRect.height / 2 + container.scrollTop;

                    const isRelevant = activeId && (node.id === activeId || conn.target_id === activeId);
                    const isDimmed = activeId && !isRelevant;

                    const dx = tx - sx;
                    const dy = ty - sy;
                    const cx1 = sx + dx * 0.25;
                    const cy1 = sy + dy * 0.1;
                    const cx2 = sx + dx * 0.75;
                    const cy2 = ty - dy * 0.1;
                    const path = `M ${sx} ${sy} C ${cx1} ${cy1}, ${cx2} ${cy2}, ${tx} ${ty}`;

                    const style = this.getRelationStyle(conn.type);

                    lines.push({
                        id: `${node.id}->${conn.target_id}`,
                        sourceId: node.id,
                        targetId: conn.target_id,
                        sourceName: node.person_name || node.name,
                        targetName: (this.nodeMap.get(conn.target_id) || {}).person_name || (this.nodeMap.get(conn.target_id) || {}).name,
                        type: conn.type,
                        description: conn.description,
                        path,
                        color: style.color,
                        isRelevant,
                        isDimmed
                    });
                }
            }

            this.connectorLines = lines;
        }
    };
}
