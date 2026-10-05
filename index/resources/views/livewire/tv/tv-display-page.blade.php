<div wire:poll.10m="refreshPrayers" class="min-h-screen w-full bg-gov-tv text-white flex flex-col justify-between p-8 sm:p-16 relative overflow-hidden select-none">
    
    <!-- Top Row: Mosque Identity & Controls & Live Status -->
    <header class="flex flex-wrap items-center justify-between border-b border-white/10 pb-4 gap-4 relative z-10">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight text-white flex items-center space-x-2">
                <span class="inline-block mb-1.5">{{ $settings->name }}</span>
            </h1>
            <p class="text-xs text-slate-400 tracking-wider uppercase font-medium flex items-center gap-1.5 mt-0.5">
                <span>{{ $settings->address }}</span>
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <!-- TV Controls: Sound & Fullscreen -->
            <div class="flex items-center space-x-2">
                <button type="button" onclick="playAdzanChime()" title="Tes Nada Pengingat Adzan" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-semibold flex items-center space-x-1.5 border border-white/10 transition cursor-pointer">
                    <i data-lucide="volume-2" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span class="hidden sm:inline">Tes Nada Adzan</span>
                </button>
                <button type="button" onclick="toggleTvFullscreen()" title="Layar Penuh" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-semibold flex items-center space-x-1.5 border border-white/10 transition cursor-pointer">
                    <i data-lucide="maximize" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Layar Penuh</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Center Main Stage: Big Realtime Clock & Next Prayer Box -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center my-auto py-6 relative z-10">
        
        <!-- Left Column: Giant Clock & Hijri Date (wire:ignore to prevent any morph/reset flicker) -->
        <div class="lg:col-span-6 space-y-2 text-left" wire:ignore>
            <div class="text-xs uppercase tracking-widest text-slate-400 font-semibold flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                <span class="inline-block mb-1.5">WAKTU SEKARANG</span>
            </div>
            
            <div class="flex items-baseline font-bold tracking-tight">
                <span class="text-7xl sm:text-8xl md:text-9xl font-extrabold text-white tnum tracking-tighter" id="tv-giant-hours">--:--</span>
                <span class="text-4xl sm:text-5xl md:text-6xl font-medium text-white/45 tnum ml-2" id="tv-giant-seconds">:--</span>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2 text-sm sm:text-base text-slate-300">
                <span class="text-white font-medium" id="tv-gregorian-date">{{ $gregorianDate }}</span>
                <span class="text-slate-600">|</span>
                <span class="text-amber-300 font-medium flex items-center gap-1.5" id="tv-hijri-date">
                    <span>{{ str_contains($hijriDate, '•') ? trim(explode('•', $hijriDate)[1]) : $hijriDate }}</span>
                </span>
            </div>
        </div>

        <!-- Right Column: Hero Next Prayer Countdown Card (wire:ignore to maintain continuous live countdown) -->
        <div class="lg:col-span-6">
            <div class="bg-white/5 border border-white/15 rounded-xl p-6 sm:p-8 backdrop-blur-md shadow-lg relative overflow-hidden" wire:ignore>
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <span class="text-xs uppercase tracking-wider font-semibold text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span id="tv-hero-header-label">{{ ($nextPrayer['is_ongoing'] ?? false) ? 'SHOLAT SEDANG BERLANGSUNG' : 'SHOLAT BERIKUTNYA' }}</span>
                    </span>
                </div>

                <div class="flex items-baseline justify-between mt-4">
                    <h2 class="text-4xl sm:text-5xl font-bold text-white tracking-tight" id="tv-hero-name">{{ $nextPrayer['prayer']['name'] ?? 'Ashar' }}</h2>
                    <div class="text-4xl sm:text-5xl font-extrabold text-white tnum" id="tv-hero-adzan">{{ $nextPrayer['prayer']['adzan'] ?? '--:--' }}</div>
                </div>

                <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 uppercase tracking-wider block font-medium" id="tv-hero-countdown-label">{{ ($nextPrayer['is_ongoing'] ?? false) ? 'MENUJU IQAMAH' : 'MENUJU ADZAN' }}</span>
                        <span class="text-xl sm:text-2xl font-bold text-amber-300 tnum font-sans" id="tv-hero-countdown">--:--:--</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-400 uppercase tracking-wider block font-medium">IQAMAH</span>
                        <span class="text-lg font-bold text-slate-200 tnum" id="tv-hero-iqamah">{{ $nextPrayer['prayer']['iqamah'] ?? '--:--' }} WIB</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Section: 5 Prayer Times Grid (wire:ignore to prevent class flickering) -->
    <div class="space-y-2 relative z-10">
        <div class="text-xs uppercase tracking-wider text-slate-400 font-semibold flex items-center gap-1.5 mb-[25px]">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
            <span>JADWAL SHOLAT LIMA WAKTU</span>
        </div>
        
        <div class="grid grid-cols-5 gap-3 sm:gap-4" wire:ignore>
            @foreach($prayers as $prayer)
                @php
                    $isActive = $prayer['is_active'] ?? false;
                    $isPassed = $prayer['is_passed'] ?? false;
                @endphp

                <div id="prayer-card-{{ $prayer['key'] }}" class="{{ $isActive ? 'bg-amber-400 text-gov-navy rounded-lg p-2.5 text-center shadow-md relative transition-all duration-300' : ($isPassed ? 'bg-white/5 border border-white/10 rounded-lg p-2.5 text-center opacity-40 transition-all duration-300' : 'bg-white/5 border border-white/10 rounded-lg p-2.5 text-center transition-all duration-300') }}">
                    <div class="prayer-name {{ $isActive ? 'text-xs text-gov-navy mb-1 font-extrabold flex items-center justify-center gap-1' : ($isPassed ? 'text-xs text-slate-400 mb-1 font-medium' : 'text-xs text-slate-300 mb-1 font-medium') }}">
                        @if($isActive)<span class="w-1.5 h-1.5 rounded-full bg-gov-navy"></span>@endif
                        <span>{{ $prayer['name'] }}</span>
                    </div>
                    <div class="prayer-adzan text-2xl sm:text-3xl {{ $isActive ? 'font-extrabold text-gov-navy tnum' : ($isPassed ? 'font-bold text-slate-400 tnum' : 'font-bold text-white tnum') }}">{{ $prayer['adzan'] }}</div>
                    <div class="prayer-iqamah text-xs {{ $isActive ? 'text-gov-navy font-bold mt-1 uppercase' : ($isPassed ? 'text-slate-500 mt-1 uppercase font-medium' : 'text-slate-400 mt-1 uppercase font-medium') }}">
                        @if(!empty($prayer['is_ongoing']))
                            IQOMAH {{ $prayer['iqamah'] }} (SEKARANG)
                        @else
                            IQOMAH {{ $prayer['iqamah'] }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Bottom Bar: Announcement Marquee & Mosque Sensor Stats -->
    <footer class="mt-[30px] pt-3 border-t border-white/10 flex flex-col md:flex-row items-center gap-3 relative z-10">
        
        <!-- Marquee Ticker with Amber Gold Badge -->
        <div class="w-full flex-1 flex items-center bg-black/40 rounded-lg border border-white/10 overflow-hidden">
            <div class="bg-amber-400 text-slate-900 text-xs font-bold px-3 py-2 shrink-0 uppercase tracking-wider flex items-center space-x-1.5">
                <i data-lucide="bell" class="w-3.5 h-3.5 text-slate-900"></i>
                <span>PENGUMUMAN</span>
            </div>
            <div class="overflow-hidden py-1.5 text-xs text-slate-200 font-medium whitespace-nowrap w-full">
                <div class="animate-marquee inline-block">
                    @if(!empty($settings->tv_announcements))
                        @foreach($settings->tv_announcements as $ann)
                            <span class="mx-6 text-slate-200">✦ {{ $ann }}</span>
                        @endforeach
                    @else
                        <span class="mx-6 text-slate-200">✦ Selamat datang di {{ $settings->name }}. Mohon rapatkan dan luruskan shaf sholat.</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sensor / Direction Stats -->
        <div class="flex items-center space-x-4 text-xs shrink-0 text-slate-300">
            <div class="flex items-center space-x-1.5">
                <span class="text-slate-400 text-xs uppercase font-medium">Suhu:</span>
                <span class="font-bold text-white tnum">29°C</span>
            </div>
            <div class="flex items-center space-x-1.5">
                <span class="text-slate-400 text-xs uppercase font-medium">Kelembapan:</span>
                <span class="font-bold text-white tnum">68%</span>
            </div>
            <div class="flex items-center space-x-1.5">
                <span class="text-slate-400 text-xs uppercase font-medium">Kiblat:</span>
                <span class="font-bold text-amber-400 tnum">{{ $settings->qibla_angle }}° NW</span>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
<script>
    const PRAYERS_DATA = @js($prayers);

    function parseTimeToSecs(timeStr) {
        if (!timeStr) return 0;
        const parts = timeStr.split(':');
        return (parseInt(parts[0], 10) * 3600) + (parseInt(parts[1], 10) * 60);
    }

    const padZero = (n) => String(n).padStart(2, '0');

    function updateTvEngine() {
        const now = new Date();
        const nowH = now.getHours();
        const nowM = now.getMinutes();
        const nowS = now.getSeconds();
        const nowSecs = (nowH * 3600) + (nowM * 60) + nowS;

        // 1. Live Giant Clock (Instant, Zero Morphing)
        const tvHours = document.getElementById('tv-giant-hours');
        const tvSeconds = document.getElementById('tv-giant-seconds');
        if (tvHours) tvHours.textContent = `${padZero(nowH)}:${padZero(nowM)}`;
        if (tvSeconds) tvSeconds.textContent = `:${padZero(nowS)}`;

        // 2. Evaluate Prayer Schedule
        let activePrayer = null;
        let isOngoing = false;
        let countdownTarget = new Date(now.getTime());
        let countdownLabel = 'MENUJU ADZAN';

        // Check if any prayer is currently between adzan and iqamah
        for (const p of PRAYERS_DATA) {
            const aSec = parseTimeToSecs(p.adzan);
            const iSec = parseTimeToSecs(p.iqamah);
            if (nowSecs >= aSec && nowSecs < iSec) {
                activePrayer = p;
                isOngoing = true;
                countdownLabel = 'MENUJU IQAMAH';
                const [ih, im] = p.iqamah.split(':').map(Number);
                countdownTarget.setHours(ih, im, 0, 0);
                break;
            }
        }

        // If none is ongoing, find the next upcoming adzan today
        if (!activePrayer) {
            for (const p of PRAYERS_DATA) {
                const aSec = parseTimeToSecs(p.adzan);
                if (nowSecs < aSec) {
                    activePrayer = p;
                    countdownLabel = 'MENUJU ADZAN';
                    const [ah, am] = p.adzan.split(':').map(Number);
                    countdownTarget.setHours(ah, am, 0, 0);
                    break;
                }
            }
        }

        // If all 5 prayers have passed today (late night after Isya), tomorrow's Subuh is next
        if (!activePrayer && PRAYERS_DATA.length > 0) {
            activePrayer = PRAYERS_DATA[0]; // Subuh
            countdownLabel = 'MENUJU SUBUH';
            const [sh, sm] = activePrayer.adzan.split(':').map(Number);
            countdownTarget.setDate(countdownTarget.getDate() + 1);
            countdownTarget.setHours(sh, sm, 0, 0);
        }

        if (!activePrayer) return;

        // 3. Update Hero Card
        const heroName = document.getElementById('tv-hero-name');
        const heroAdzan = document.getElementById('tv-hero-adzan');
        const heroIqamah = document.getElementById('tv-hero-iqamah');
        const heroLabel = document.getElementById('tv-hero-countdown-label');
        const heroHeaderLabel = document.getElementById('tv-hero-header-label');
        const heroCountdown = document.getElementById('tv-hero-countdown');

        if (heroHeaderLabel) {
            heroHeaderLabel.textContent = isOngoing ? 'SHOLAT SEDANG BERLANGSUNG' : 'SHOLAT BERIKUTNYA';
        }
        if (heroName && heroName.textContent !== activePrayer.name) {
            heroName.textContent = activePrayer.name;
        }
        if (heroAdzan && heroAdzan.textContent !== activePrayer.adzan) {
            heroAdzan.textContent = activePrayer.adzan;
        }
        if (heroIqamah) {
            heroIqamah.textContent = `${activePrayer.iqamah} WIB`;
        }
        if (heroLabel && heroLabel.textContent !== countdownLabel) {
            heroLabel.textContent = countdownLabel;
        }

        // Countdown calculation (ms)
        const diffMs = countdownTarget.getTime() - now.getTime();
        if (diffMs > 0 && heroCountdown) {
            const totalSecs = Math.floor(diffMs / 1000);
            const remH = Math.floor(totalSecs / 3600);
            const remM = Math.floor((totalSecs % 3600) / 60);
            const remS = totalSecs % 60;
            heroCountdown.textContent = `${padZero(remH)}:${padZero(remM)}:${padZero(remS)}`;
        } else if (diffMs <= 0 && heroCountdown) {
            heroCountdown.textContent = '00:00:00';
            if (!window._chimePlayedAt || (Date.now() - window._chimePlayedAt > 15000)) {
                window._chimePlayedAt = Date.now();
                playAdzanChime();
            }
        }

        // 4. Update 5 Prayer Cards: STRICTLY ONLY ONE AMBER GOLD CARD
        PRAYERS_DATA.forEach(p => {
            const card = document.getElementById('prayer-card-' + p.key);
            if (!card) return;

            const aSec = parseTimeToSecs(p.adzan);
            const iSec = parseTimeToSecs(p.iqamah);
            const isThisActive = (activePrayer && activePrayer.key === p.key);
            const isThisPassed = (nowSecs >= iSec && !isThisActive);

            const nameEl = card.querySelector('.prayer-name');
            const timeEl = card.querySelector('.prayer-adzan');
            const subEl = card.querySelector('.prayer-iqamah');

            if (isThisActive) {
                card.className = "bg-amber-400 text-gov-navy rounded-lg p-2.5 text-center shadow-md relative transition-all duration-300";
                if (nameEl) {
                    nameEl.className = "prayer-name text-xs text-gov-navy mb-1 font-extrabold flex items-center justify-center gap-1";
                    nameEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-gov-navy"></span><span>${p.name}</span>`;
                }
                if (timeEl) timeEl.className = "prayer-adzan text-2xl sm:text-3xl font-extrabold text-gov-navy tnum";
                if (subEl) {
                    subEl.className = "prayer-iqamah text-xs text-gov-navy font-bold mt-1 uppercase";
                    subEl.textContent = isOngoing ? `IQOMAH ${p.iqamah} (SEKARANG)` : `IQOMAH ${p.iqamah}`;
                }
            } else if (isThisPassed) {
                card.className = "bg-white/5 border border-white/10 rounded-lg p-2.5 text-center opacity-40 transition-all duration-300";
                if (nameEl) {
                    nameEl.className = "prayer-name text-xs text-slate-400 mb-1 font-medium";
                    nameEl.innerHTML = `<span>${p.name}</span>`;
                }
                if (timeEl) timeEl.className = "prayer-adzan text-2xl sm:text-3xl font-bold text-slate-400 tnum";
                if (subEl) {
                    subEl.className = "prayer-iqamah text-xs text-slate-500 mt-1 uppercase font-medium";
                    subEl.textContent = `IQOMAH ${p.iqamah}`;
                }
            } else {
                card.className = "bg-white/5 border border-white/10 rounded-lg p-2.5 text-center transition-all duration-300";
                if (nameEl) {
                    nameEl.className = "prayer-name text-xs text-slate-300 mb-1 font-medium";
                    nameEl.innerHTML = `<span>${p.name}</span>`;
                }
                if (timeEl) timeEl.className = "prayer-adzan text-2xl sm:text-3xl font-bold text-white tnum";
                if (subEl) {
                    subEl.className = "prayer-iqamah text-xs text-slate-400 mt-1 uppercase font-medium";
                    subEl.textContent = `IQOMAH ${p.iqamah}`;
                }
            }
        });
    }

    // Run engine immediately and every second
    updateTvEngine();
    setInterval(updateTvEngine, 1000);

    // Fullscreen Toggle
    function toggleTvFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.warn(`Error attempting to enable fullscreen: ${err.message}`);
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    // Audio Chime Synthesizer (Web Audio API)
    function playAdzanChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6 (Harmonic Chime)
            
            notes.forEach((freq, idx) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, ctx.currentTime + (idx * 0.25));

                gain.gain.setValueAtTime(0, ctx.currentTime + (idx * 0.25));
                gain.gain.linearRampToValueAtTime(0.3, ctx.currentTime + (idx * 0.25) + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + (idx * 0.25) + 1.2);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(ctx.currentTime + (idx * 0.25));
                osc.stop(ctx.currentTime + (idx * 0.25) + 1.2);
            });
        } catch (e) {
            console.log('Audio playback prevented or unsupported');
        }
    }
</script>
@endpush
