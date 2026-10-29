export function historyApp(initialData) {
    return {
        ages: initialData.ages || [],
        nodes: initialData.nodes || [],
        stats: initialData.stats || {},
        
        // UI State
        activeView: 'timeline', // 'timeline', 'constellation', 'matrix'
        selectedTypeFilter: 'all', // 'all', 'observation', 'experiment', 'theory'
        selectedAgeFilter: 'all', // 'all', or age_id (1-10)
        selectedRelationFilter: 'all', // 'all', 'refutes', 'confirms', 'builds_on'
        searchQuery: '',
        
        // Active Selection
        selectedNodeId: null,
        hoveredNodeId: null,
        inspectorOpen: false,
        
        // Lineage Exploration
        activeLineageChain: [],
        currentLineageIndex: -1,
        
        // SVG Connectors
        connectorLines: [],
        svgWidth: 1200,
        svgHeight: 2000,
        
        init() {
            // Build lookup index
            this.nodeMap = new Map(this.nodes.map(n => [n.id, n]));
            this.ageMap = new Map(this.ages.map(a => [a.id, a]));

            // Keyboard listeners
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.inspectorOpen) {
                    this.closeInspector();
                }
            });

            // Recompute SVG connection lines after DOM render and on resize/scroll
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

        // Filtered nodes
        get filteredNodes() {
            return this.nodes.filter(node => {
                // Type filter
                if (this.selectedTypeFilter !== 'all' && node.type !== this.selectedTypeFilter) {
                    return false;
                }
                // Age filter
                if (this.selectedAgeFilter !== 'all' && node.age_id !== parseInt(this.selectedAgeFilter)) {
                    return false;
                }
                // Relation filter
                if (this.selectedRelationFilter !== 'all') {
                    const hasRelation = (node.connections || []).some(c => c.type === this.selectedRelationFilter);
                    const isTargetOfRelation = (node.descendants || []).some(d => d.type === this.selectedRelationFilter);
                    if (!hasRelation && !isTargetOfRelation) return false;
                }
                // Search query
                if (this.searchQuery.trim() !== '') {
                    const q = this.searchQuery.toLowerCase();
                    const matchName = node.name.toLowerCase().includes(q);
                    const matchDisc = (node.discovery || '').toLowerCase().includes(q);
                    const matchQuest = (node.question || '').toLowerCase().includes(q);
                    const matchExp = (node.experiment_details || '').toLowerCase().includes(q);
                    const matchYear = String(node.display_year).toLowerCase().includes(q);
                    return matchName || matchDisc || matchQuest || matchExp || matchYear;
                }
                return true;
            });
        },

        // Nodes grouped by Age
        get nodesByAge() {
            const groups = [];
            const filteredSet = new Set(this.filteredNodes.map(n => n.id));

            for (const age of this.ages) {
                const ageNodes = this.nodes.filter(n => n.age_id === age.id && filteredSet.has(n.id));
                if (ageNodes.length > 0 || (this.selectedAgeFilter === 'all' && this.searchQuery === '')) {
                    groups.push({
                        age,
                        nodes: ageNodes
                    });
                }
            }
            return groups;
        },

        get selectedNode() {
            return this.nodeMap.get(this.selectedNodeId) || null;
        },

        // Selection & Inspection
        selectNode(nodeId) {
            this.selectedNodeId = nodeId;
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

        // Jump smoothly to a specific node on the canvas/timeline
        scrollToNode(nodeId) {
            const el = document.getElementById('node-card-' + nodeId);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Flash highlight
                el.classList.add('ring-2', 'ring-cyan-400');
                setTimeout(() => {
                    el.classList.remove('ring-2', 'ring-cyan-400');
                }, 1800);
            }
        },

        // Build complete ancestry chain backwards to antiquity
        buildLineageChain(startNodeId) {
            const chain = [];
            const visited = new Set();
            let current = startNodeId;

            while (current && !visited.has(current)) {
                visited.add(current);
                chain.push(current);
                const node = this.nodeMap.get(current);
                if (node && node.connections && node.connections.length > 0) {
                    // Pick the primary 'builds_on' or 'confirms' ancestor
                    const primary = node.connections.find(c => c.type === 'builds_on') || node.connections[0];
                    current = primary ? primary.target_id : null;
                } else {
                    current = null;
                }
            }

            this.activeLineageChain = chain.reverse(); // Chronological order
            this.currentLineageIndex = this.activeLineageChain.indexOf(startNodeId);
        },

        stepLineage(direction) {
            const nextIdx = this.currentLineageIndex + direction;
            if (nextIdx >= 0 && nextIdx < this.activeLineageChain.length) {
                this.currentLineageIndex = nextIdx;
                const nextId = this.activeLineageChain[nextIdx];
                this.selectedNodeId = nextId;
                this.scrollToNode(nextId);
            }
        },

        // Helper checking if a node is connected to active/hovered node
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

        // Node styling helpers
        getTypeBadge(type) {
            switch (type) {
                case 'observation':
                    return { label: 'Observation', bg: 'bg-amber-500/15', text: 'text-amber-400', border: 'border-amber-500/30', glow: 'glow-gold', dot: 'bg-amber-400' };
                case 'experiment':
                    return { label: 'Experiment', bg: 'bg-emerald-500/15', text: 'text-emerald-400', border: 'border-emerald-500/30', glow: 'glow-emerald', dot: 'bg-emerald-400' };
                case 'theory':
                    return { label: 'Theory', bg: 'bg-purple-500/15', text: 'text-purple-400', border: 'border-purple-500/30', glow: 'glow-purple', dot: 'bg-purple-400' };
                default:
                    return { label: type, bg: 'bg-slate-500/15', text: 'text-slate-300', border: 'border-slate-500/30', glow: '', dot: 'bg-slate-400' };
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
                case 'builds_on':
                default:
                    return { color: '#38bdf8', label: 'Builds On', class: 'text-cyan-400 bg-cyan-500/10 border-cyan-500/30' };
            }
        },

        // Calculate dynamic SVG curved connector lines between rendered nodes
        updateConnectorPositions() {
            const container = document.getElementById('discovery-canvas-container');
            if (!container) return;

            const containerRect = container.getBoundingClientRect();
            this.svgWidth = container.scrollWidth || containerRect.width;
            this.svgHeight = container.scrollHeight || containerRect.height;

            const lines = [];
            const activeId = this.hoveredNodeId || this.selectedNodeId;

            // Only generate connector lines for visible filtered nodes
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

                    // Smooth cubic bezier control points
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
                        sourceName: node.name,
                        targetName: (this.nodeMap.get(conn.target_id) || {}).name,
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
