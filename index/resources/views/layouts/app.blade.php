<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
        <meta name="description" content="Platform Digital Terpadu Dewan Kemakmuran Masjid (DKM) Masjid Salahuddin">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'Masjid Salahuddin' }}</title>

        <!-- PWA Meta Tags & Web App Manifest -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#102a43">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Salahuddin">
        <link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <!-- Google Fonts: Plus Jakarta Sans, Inter, Amiri, Playfair Display -->
        <link rel="preconnect" href="https://fonts.googleapis.com" data-html2canvas-ignore="true">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin data-html2canvas-ignore="true">
        <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,600;0,800;1,600;1,800&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet" data-html2canvas-ignore="true">

        <!-- Vite Assets: Tailwind CSS & JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }

            /* Pure Native App Experience: Invisible scrollbars by default, with .notula-scrollable exception */
            html, body, *:not(.notula-scrollable) {
                scrollbar-width: none !important; /* Firefox */
                -ms-overflow-style: none !important; /* IE / Edge */
            }
            *:not(.notula-scrollable)::-webkit-scrollbar {
                display: none !important; /* Chrome, Safari, WebKit */
                width: 0 !important;
                height: 0 !important;
            }

            /* Dedicated, elegant scrollbar for Notula document viewer */
            .notula-scrollable {
                scrollbar-width: thin !important;
                scrollbar-color: #94a3b8 #f1f5f9 !important;
                -ms-overflow-style: auto !important;
                -webkit-overflow-scrolling: touch;
                overscroll-behavior: contain;
            }
            .notula-scrollable::-webkit-scrollbar {
                display: block !important;
                width: 8px !important;
                height: 8px !important;
            }
            .notula-scrollable::-webkit-scrollbar-track {
                background: #f1f5f9 !important;
                border-radius: 9999px !important;
            }
            .notula-scrollable::-webkit-scrollbar-thumb {
                background-color: #94a3b8 !important;
                border-radius: 9999px !important;
                border: 2px solid #f1f5f9 !important;
            }
            .notula-scrollable::-webkit-scrollbar-thumb:hover {
                background-color: #64748b !important;
            }

            /* Disable double-tap zoom delay and pinch-to-zoom for native app feel */
            html, body {
                touch-action: manipulation;
                -webkit-text-size-adjust: 100%;
            }
        </style>

        <!-- Anti-Zoom Gesture Guard: Prevent iOS Safari pinch-to-zoom -->
        <script>
            document.addEventListener('gesturestart', function(e) {
                e.preventDefault();
            }, { passive: false });
        </script>

        <!-- Pure Backdrop Click Guard: Distinguish genuine backdrop click from text selection drag -->
        <script>
            (function() {
                let _lastMouseDownTarget = null;
                let _lastMouseUpTarget = null;
                document.addEventListener('mousedown', function(e) { _lastMouseDownTarget = e.target; }, true);
                document.addEventListener('mouseup', function(e) { _lastMouseUpTarget = e.target; }, true);
                document.addEventListener('touchstart', function(e) { _lastMouseDownTarget = e.target; }, { capture: true, passive: true });
                document.addEventListener('touchend', function(e) { _lastMouseUpTarget = e.target; }, { capture: true, passive: true });

                window.isBackdropClick = function(e, element) {
                    const el = element || (e && (e.currentTarget || e.target));
                    if (!el || !e) return false;
                    const isPure = (_lastMouseDownTarget === el) && (_lastMouseUpTarget === el) && (e.target === el);
                    return isPure;
                };
            })();
        </script>

        @livewireStyles
        @stack('styles')
    </head>
    <body class="bg-gov-canvas text-gov-textMain font-sans antialiased min-h-screen flex flex-col selection:bg-gov-navy selection:text-white">
        
        {{ $slot }}

        <!-- Floating Global Auto-Dismiss Toast Notification -->
        <div data-html2canvas-ignore="true" x-data="{ 
                show: false, 
                message: '',
                timer: null,
                trigger(msg) {
                    if (!msg) return;
                    this.message = msg;
                    this.show = true;
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => {
                        this.show = false;
                    }, 3500);
                }
             }" @toast.window="trigger($event.detail.message)" x-cloak x-show="show"
            x-transition:enter="transition ease-out duration-250 transform"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-gov-navy text-white px-5 py-3.5 rounded-xl shadow-2xl border border-slate-700/60 text-xs font-semibold backdrop-blur-md"
            style="display: none;">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
            <span x-text="message" class="tracking-wide"></span>
            <button type="button" @click="show = false"
                class="ml-3 text-slate-300 hover:text-white p-1 rounded-lg transition cursor-pointer" title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        @livewireScripts
        <script>
            document.addEventListener('livewire:navigated', () => {
                if (window.createLucideIcons) window.createLucideIcons();
            });

            window.copyTextToClipboard = function(text, onSuccess, onError) {
                if (!text) {
                    if (onError) onError('Tidak ada teks untuk disalin');
                    return;
                }
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        if (onSuccess) onSuccess();
                    }).catch(() => {
                        fallbackCopy(text, onSuccess, onError);
                    });
                } else {
                    fallbackCopy(text, onSuccess, onError);
                }

                function fallbackCopy(str, ok, err) {
                    try {
                        const ta = document.createElement('textarea');
                        ta.value = str;
                        ta.style.position = 'fixed';
                        ta.style.left = '-999999px';
                        ta.style.top = '-999999px';
                        ta.setAttribute('readonly', '');
                        document.body.appendChild(ta);
                        ta.focus();
                        ta.select();
                        const res = document.execCommand('copy');
                        document.body.removeChild(ta);
                        if (res) {
                            if (ok) ok();
                        } else {
                            if (err) err(new Error('execCommand returned false'));
                        }
                    } catch (e) {
                        if (err) err(e);
                    }
                }
            };

            window.copyOdojToClipboard = function(text = null) {
                const rawText = text || document.getElementById('odoj-whatsapp-text-content')?.innerText?.trim() || '';
                if (!rawText) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Tidak ada pesan WhatsApp untuk disalin.' } }));
                    return;
                }
                window.copyTextToClipboard(rawText, () => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Pesan laporan WhatsApp berhasil disalin ke clipboard!' } }));
                }, () => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gagal menyalin pesan ke clipboard. Silakan salin manual.' } }));
                });
            };

            window.copyJarkomanToClipboard = function(text = null) {
                const rawText = text || '';
                if (!rawText) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Tidak ada teks jarkoman untuk disalin.' } }));
                    return;
                }
                window.copyTextToClipboard(rawText, () => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Teks jarkoman WhatsApp berhasil disalin ke clipboard!' } }));
                }, () => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gagal menyalin teks ke clipboard. Silakan salin manual.' } }));
                });
            };

            // Global Thousand Separator (Ribuan Titik) Helpers & Alpine Directive
            window.formatRibuan = function(val) {
                if (val === null || val === undefined || val === '') return '';
                let str = String(val).replace(/\D/g, '');
                if (!str) return '';
                str = str.replace(/^0+(?=\d)/, '');
                return str.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            window.handleRibuanInput = function(el, onCleanValue) {
                let oldVal = el.value || '';
                let oldCursor = el.selectionStart || oldVal.length;
                let digitsBefore = oldVal.slice(0, oldCursor).replace(/\D/g, '').length;
                let clean = oldVal.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
                let formatted = window.formatRibuan(clean);
                
                el.value = formatted;
                
                let newCursor = 0;
                let countedDigits = 0;
                for (let i = 0; i < formatted.length; i++) {
                    if (/\d/.test(formatted[i])) {
                        countedDigits++;
                    }
                    if (countedDigits === digitsBefore) {
                        newCursor = i + 1;
                        break;
                    }
                }
                if (digitsBefore === 0) newCursor = 0;
                if (countedDigits < digitsBefore) newCursor = formatted.length;
                
                try {
                    el.setSelectionRange(newCursor, newCursor);
                } catch (e) {}
                
                if (typeof onCleanValue === 'function') {
                    onCleanValue(clean);
                }
            };

            function setupRibuanDirective(alp) {
                if (!alp || alp.__ribuan_registered) return;
                alp.__ribuan_registered = true;
                alp.directive('ribuan', (el, { expression }, { evaluateLater, effect }) => {
                    const formatInitial = () => {
                        if (el.value) {
                            el.value = window.formatRibuan(el.value);
                        }
                    };
                    setTimeout(formatInitial, 0);

                    if (expression) {
                        const getVal = evaluateLater(expression);
                        effect(() => {
                            getVal((val) => {
                                if (document.activeElement !== el) {
                                    el.value = window.formatRibuan(val);
                                }
                            });
                        });
                    }

                    el.addEventListener('input', () => {
                        window.handleRibuanInput(el);
                    });

                    el.addEventListener('blur', () => {
                        if (el.value) {
                            el.value = window.formatRibuan(el.value);
                        }
                    });
                });
            }

            if (window.Alpine) {
                setupRibuanDirective(window.Alpine);
            } else {
                document.addEventListener('alpine:init', () => setupRibuanDirective(window.Alpine));
            }
            document.addEventListener('livewire:init', () => {
                if (window.Alpine) setupRibuanDirective(window.Alpine);
            });
        </script>

        <!-- PWA Install Banner Component -->
        <div data-html2canvas-ignore="true">
            @include('components.pwa-install-banner')
        </div>

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            // Service worker registered successfully
                        })
                        .catch(function(error) {
                            console.warn('[PWA] Service Worker registration failed:', error);
                        });
                });
            }
        </script>

        <!-- Isolated On-Demand Poster Exporter Component (Off-Screen Global Container) -->
        <x-poster-exporter :config="\App\Models\PosterSetting::getAppConfig()" />

        @stack('scripts')
    </body>
</html>
