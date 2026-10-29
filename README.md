# Cosmic Lineage ⚡🌌

An interactive, experiment-centric knowledge graph mapping the 2,600-year evolution of electromagnetism and scientific discovery across 10 eras of understanding.

---

## 🚀 Getting Started

### 1. Requirements
* PHP 8.2+
* Composer
* Node.js & NPM

### 2. Setup
```bash
# Clone the repository
git clone https://github.com/ReyKiptoo/cosmic-lineage.git
cd cosmic-lineage

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run database migrations and seed the 47 experiments & 10 eras
php artisan migrate:fresh --seed

# Build frontend assets
npm run build

# Start the application
php artisan serve
```

Visit `http://localhost:8000` to explore all 7 interactivity modes.

---

## 🎮 7 Interactivity Modes

1. **📜 Chronology River**: Full timeline with macro-shift callout banners and dynamic SVG bezier connectors.
2. **🗺️ Quest Mode**: Progressive disclosure—start at Age 1, explore experiments to earn XP and unlock subsequent eras.
3. **🧬 Storylines**: Guided narrative threads (*The Flow of Charge*, *The Mystery of Lodestone*, *The Ripple of Light*, *The Quantum Frontier*).
4. **⏳ Film Playback**: Hands-free auto-playback documentary mode with speed controls (`0.5×`, `1×`, `2×`) and timeline scrubber.
5. **🌌 Matrix**: Compact network constellation view with connectivity metrics.
6. **📖 Book Mode**: Long-form chapter reader presenting each era as a historical narrative with hyperlinked experiment archives.
7. **🧩 Quiz Mode**: Active recall challenges testing historical refutations, confirmations, and apparatus principles.

---

## 🧪 Testing

```bash
php artisan test
```
