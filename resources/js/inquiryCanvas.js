/**
 * Cosmic Lineage - Genesis Inquiry Canvas & Thought Engine
 * First-Principles Epistemological Building Blocks powered by Gemini API
 */

export function inquiryCanvasApp() {
    return {
        // State
        blocks: [],
        activeBlockId: null,
        inputPrompt: '',
        isLoading: false,
        loadingMessage: 'Synthesizing first-principles building block...',
        
        // Gemini API Configuration
        apiKey: localStorage.getItem('cosmic_gemini_api_key') || '',
        showKeyModal: false,
        tempApiKey: '',
        keyNotice: '',

        // Challenge Modal
        showChallengeModal: false,
        challengingBlock: null,
        challengeInput: '',
        isChallenging: false,
        challengeResult: null,

        // Canvas Viewport & Layout
        viewMode: 'canvas', // 'canvas' | 'pyramid'
        scale: 1,

        init() {
            this.tempApiKey = this.apiKey;
            this.loadSavedCanvas();

            // Re-render SVG connections on window resize
            window.addEventListener('resize', () => {
                this.$nextTick(() => this.updateConnectors());
            });

            // Auto-render connectors after DOM updates
            this.$watch('blocks', () => {
                this.$nextTick(() => this.updateConnectors());
            });
        },

        // API Key Management
        saveApiKey() {
            const cleanKey = this.tempApiKey.trim();
            this.apiKey = cleanKey;
            if (cleanKey) {
                localStorage.setItem('cosmic_gemini_api_key', cleanKey);
                this.keyNotice = 'API key saved to browser memory!';
            } else {
                localStorage.removeItem('cosmic_gemini_api_key');
                this.keyNotice = 'API key removed. Running in heuristic mode.';
            }
            setTimeout(() => {
                this.showKeyModal = false;
                this.keyNotice = '';
            }, 1200);
        },

        clearApiKey() {
            this.apiKey = '';
            this.tempApiKey = '';
            localStorage.removeItem('cosmic_gemini_api_key');
            this.keyNotice = 'Key removed from browser memory.';
            setTimeout(() => {
                this.showKeyModal = false;
                this.keyNotice = '';
            }, 1200);
        },

        // Starting inquiry from a curated foundation stone
        startWithStarter(starter) {
            this.inputPrompt = starter.question;
            this.forgeNewInquiry(null);
        },

        // Forge a new inquiry block (Root or Child)
        async forgeNewInquiry(parentBlock = null, overridePrompt = null) {
            const prompt = (overridePrompt || this.inputPrompt).trim();
            if (!prompt) return;

            this.isLoading = true;
            this.loadingMessage = parentBlock
                ? `Building upon previous truth: "${parentBlock.title}"...`
                : 'Interrogating nature: formulating hypothesis & experimental apparatus...';

            // Gather priors from parent if available
            let priors = [];
            let parentId = null;
            if (parentBlock) {
                parentId = parentBlock.id;
                priors = [
                    ...(parentBlock.things_we_know || []),
                    `Established Truth: ${parentBlock.results.lesson}`
                ];
            }

            try {
                const response = await fetch('/api/inquiry/forge', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Gemini-Key': this.apiKey || ''
                    },
                    body: JSON.stringify({
                        prompt: prompt,
                        priors: priors,
                        parent_id: parentId
                    })
                });

                if (!response.ok) {
                    throw new Error(`Server returned HTTP ${response.status}`);
                }

                const data = await response.json();
                if (data.status === 'success' && data.block) {
                    const block = data.block;
                    block.isExpanded = true;
                    block.highlight = true;
                    
                    this.blocks.push(block);
                    this.activeBlockId = block.id;
                    this.inputPrompt = '';

                    // Save to local storage
                    this.persistCanvas();

                    // Scroll to newly forged block
                    this.$nextTick(() => {
                        const el = document.getElementById(block.id);
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        this.updateConnectors();
                    });
                }
            } catch (err) {
                console.error('Inquiry synthesis error:', err);
                alert('Could not forge inquiry block: ' + err.message);
            } finally {
                this.isLoading = false;
            }
        },

        // Branching from one of the 3 Next Paths
        branchFromNextPath(parentBlock, pathOption) {
            this.forgeNewInquiry(parentBlock, pathOption.question);
        },

        // Seal a block as Proven Truth 🧱
        sealBlock(block) {
            block.sealed = true;
            block.status = 'sealed';
            block.sealedAt = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            this.persistCanvas();
            this.$nextTick(() => this.updateConnectors());
        },

        // Unseal / Re-open block for editing or testing
        unsealBlock(block) {
            block.sealed = false;
            block.status = 'draft';
            this.persistCanvas();
        },

        // Open Challenge Modal for a sealed block
        openChallenge(block) {
            this.challengingBlock = block;
            this.challengeInput = '';
            this.challengeResult = null;
            this.showChallengeModal = true;
        },

        // Submit Challenge Counter-Evidence
        async submitChallenge() {
            if (!this.challengeInput.trim() || !this.challengingBlock) return;

            this.isChallenging = true;
            try {
                const response = await fetch('/api/inquiry/challenge', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Gemini-Key': this.apiKey || ''
                    },
                    body: JSON.stringify({
                        block: this.challengingBlock,
                        counter_evidence: this.challengeInput
                    })
                });

                const data = await response.json();
                if (data.status === 'success' && data.evaluation) {
                    this.challengeResult = data.evaluation;
                    
                    // Update challenging block status
                    const target = this.blocks.find(b => b.id === this.challengingBlock.id);
                    if (target) {
                        target.challenged = true;
                        target.challenge_notes = data.evaluation;
                        target.status = data.evaluation.verdict === 'fractured' ? 'fractured' : 'refined';
                        
                        // Flag descendant blocks whose foundations are shaken
                        this.flagDescendants(target.id, data.evaluation.verdict);
                    }
                    this.persistCanvas();
                }
            } catch (err) {
                console.error('Challenge error:', err);
                alert('Challenge evaluation error: ' + err.message);
            } finally {
                this.isChallenging = false;
            }
        },

        // Recursively flag descendant blocks
        flagDescendants(parentId, verdict) {
            this.blocks.forEach(b => {
                if (b.parent_id === parentId) {
                    b.upstreamAlert = `Foundation questioned: Upstream truth "${this.challengingBlock?.title}" was assessed as ${verdict}.`;
                    this.flagDescendants(b.id, verdict);
                }
            });
        },

        // Delete a block and reconnect or clean children
        deleteBlock(blockId) {
            if (!confirm('Remove this inquiry block from the canvas?')) return;
            this.blocks = this.blocks.filter(b => b.id !== blockId);
            this.persistCanvas();
            this.$nextTick(() => this.updateConnectors());
        },

        // Clear canvas with confirmation
        resetCanvas() {
            if (this.blocks.length > 0 && !confirm('Start a fresh canvas? All current blocks will be cleared from this workspace.')) {
                return;
            }
            this.blocks = [];
            this.activeBlockId = null;
            this.inputPrompt = '';
            localStorage.removeItem('cosmic_inquiry_blocks');
            this.$nextTick(() => this.updateConnectors());
        },

        // Local Storage Persistence
        persistCanvas() {
            try {
                localStorage.setItem('cosmic_inquiry_blocks', JSON.stringify(this.blocks));
            } catch (e) {
                console.warn('Could not save to localStorage:', e);
            }
        },

        loadSavedCanvas() {
            try {
                const saved = localStorage.getItem('cosmic_inquiry_blocks');
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.blocks = parsed;
                    }
                }
            } catch (e) {
                console.warn('Could not restore from localStorage:', e);
            }
        },

        // SVG Bezier Curves connecting parent blocks to child blocks
        connectors: [],

        updateConnectors() {
            const container = document.getElementById('canvas-container');
            if (!container) return;

            const containerRect = container.getBoundingClientRect();
            const newConnectors = [];

            this.blocks.forEach(child => {
                if (!child.parent_id) return;
                const parentEl = document.getElementById(child.parent_id);
                const childEl = document.getElementById(child.id);

                if (parentEl && childEl) {
                    const pRect = parentEl.getBoundingClientRect();
                    const cRect = childEl.getBoundingClientRect();

                    // Anchor point from bottom center of parent
                    const x1 = pRect.left + pRect.width / 2 - containerRect.left;
                    const y1 = pRect.bottom - containerRect.top;

                    // Anchor point to top center of child
                    const x2 = cRect.left + cRect.width / 2 - containerRect.left;
                    const y2 = cRect.top - containerRect.top;

                    const dy = Math.max(30, (y2 - y1) * 0.45);
                    const pathData = `M ${x1} ${y1} C ${x1} ${y1 + dy}, ${x2} ${y2 - dy}, ${x2} ${y2}`;

                    const isChallenged = child.upstreamAlert || parentEl.classList.contains('border-red-500');

                    newConnectors.push({
                        id: `${child.parent_id}->${child.id}`,
                        d: pathData,
                        isChallenged: !!isChallenged
                    });
                }
            });

            this.connectors = newConnectors;
        },

        // Helper: get sealed count
        get sealedCount() {
            return this.blocks.filter(b => b.sealed).length;
        },

        // Helper: get total count
        get totalCount() {
            return this.blocks.length;
        }
    };
}
