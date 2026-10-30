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
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <!-- Google Fonts: Plus Jakarta Sans, Inter, Amiri, Playfair Display -->
        <link rel="preconnect" href="https://fonts.googleapis.com" data-html2canvas-ignore="true">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin data-html2canvas-ignore="true">
        <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Playfair+Display:ital,wght@0,600;0,800;1,600;1,800&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet" data-html2canvas-ignore="true">

        <!-- Vite Assets: Tailwind CSS & JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Standalone Local Vendor Libraries (Deferred) -->
        <script src="{{ asset('vendor/qrcode.min.js') }}" defer></script>
        <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}" defer></script>

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

            /* Livewire Background Sync Top Progress Bar Animation (Matches wire:navigate #2299dd) */
            @keyframes livewireProgressStream {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(250%);
                }
            }
            .livewire-progress-bar-stream {
                width: 45%;
                animation: livewireProgressStream 1.1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
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

                window.downloadPdfDirect = async function(downloadUrl, filename) {
                    if (!downloadUrl) return;
                    const defaultFilename = filename || 'dokumen.pdf';

                    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent) || 
                                     (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

                    // 1. Pada perangkat iOS / Mobile (terutama iOS PWA Standalone):
                    // Web Share API Level 2 memunculkan native iOS Share Sheet ("Simpan ke File", AirDrop, WhatsApp)
                    // Mencegah iOS PWA beralih layar / membuka PDF viewer bawaan "1 of 1"
                    if (isMobile && navigator.canShare) {
                        try {
                            const res = await fetch(downloadUrl);
                            if (res.ok) {
                                const blob = await res.blob();
                                const file = new File([blob], defaultFilename, { type: 'application/pdf' });
                                if (navigator.canShare({ files: [file] })) {
                                    await navigator.share({
                                        files: [file],
                                        title: defaultFilename.replace('.pdf', '')
                                    });
                                    return;
                                }
                            }
                        } catch (e) {
                            if (e.name === 'AbortError') {
                                return;
                            }
                            console.warn('[PDF Download] Web Share API tidak dapat memproses berkas, beralih ke blob link:', e);
                        }
                    }

                    // 2. Pada Desktop, Android, atau browser biasa:
                    // Gunakan fetch blob + Object URL dengan tag <a> tersembunyi untuk unduh langsung tanpa tab baru
                    try {
                        const res = await fetch(downloadUrl);
                        if (res.ok) {
                            const blob = await res.blob();
                            const blobUrl = URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = blobUrl;
                            link.download = defaultFilename;
                            link.style.display = 'none';
                            document.body.appendChild(link);
                            link.click();
                            setTimeout(() => {
                                if (link.parentNode) link.parentNode.removeChild(link);
                                URL.revokeObjectURL(blobUrl);
                            }, 2500);
                            return;
                        }
                    } catch (e) {
                        console.warn('[PDF Download] Fetch blob gagal, memicu fallback link langsung:', e);
                    }

                    // 3. Fallback terakhir: tag <a> langsung
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = defaultFilename;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    setTimeout(() => {
                        if (link.parentNode) link.parentNode.removeChild(link);
                    }, 1500);
                };

                window.addEventListener('download-kep-pdf', (e) => {
                    if (e.detail && e.detail.url) {
                        window.downloadPdfDirect(e.detail.url, e.detail.filename);
                    }
                });
            })();
        </script>

        @livewireStyles
        @stack('styles')
    </head>
    <body class="bg-gov-canvas text-gov-textMain font-sans antialiased min-h-screen flex flex-col selection:bg-gov-navy selection:text-white">
        
        {{ $slot }}

        <!-- Floating Global Auto-Dismiss Toast Notification (Melayang Anggun di Bawah Header) -->
        <div data-html2canvas-ignore="true" x-data="{ 
                show: false, 
                message: '',
                timer: null,
                trigger(msg) {
                    if (!msg) return;
                    const isMobile = window.innerWidth < 768;
                    const sidebar = document.querySelector('.admin-sidebar');
                    const isSidebarOpen = Boolean(isMobile && sidebar && !sidebar.classList.contains('sidebar-closed'));
                    if (isSidebarOpen) return;
                    this.message = msg;
                    this.show = true;
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => {
                        this.show = false;
                    }, 3500);
                }
             }" 
             @toast.window="trigger($event.detail.message)" 
             @close-toast.window="show = false; clearTimeout(timer);"
             x-cloak x-show="show"
            x-transition:enter="transition ease-out duration-250 transform"
            x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
            class="fixed top-16 inset-x-4 max-w-md mx-auto sm:top-6 sm:right-6 sm:inset-x-auto sm:mx-0 z-30 sm:z-50 flex items-center justify-between sm:justify-start gap-3 bg-gov-navy text-white px-5 py-3.5 rounded-xl shadow-2xl border border-slate-700/60 text-xs font-semibold backdrop-blur-md"
            style="display: none;">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                <span x-text="message" class="tracking-wide truncate"></span>
            </div>
            <button type="button" @click="show = false"
                class="ml-3 text-slate-300 hover:text-white p-1 rounded-lg transition cursor-pointer shrink-0" title="Tutup">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- SweetAlert2 Modal Engine (Styled to Match Native Modal Design Exactly) -->
        <script src="/vendor/sweetalert2/sweetalert2.all.min.js"></script>
        <style>
            .swal2-container.swal2-backdrop-show {
                background: rgba(15, 23, 42, 0.6) !important;
                backdrop-filter: blur(4px) !important;
                -webkit-backdrop-filter: blur(4px) !important;
            }
            .swal2-modal-salahuddin {
                background: #ffffff !important;
                border-radius: 1rem !important; /* rounded-2xl (16px), identik dengan Gambar 1 */
                border: 1px solid rgba(226, 232, 240, 0.8) !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
                padding: 1.5rem !important; /* p-6 */
                max-width: 28rem !important; /* max-w-md */
                width: 92% !important;
                box-sizing: border-box !important;
                font-family: 'Plus Jakarta Sans', sans-serif !important;
                position: relative !important;
            }
            .swal2-modal-salahuddin .swal2-html-container {
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                text-align: left !important;
            }
            .swal2-close-salahuddin {
                position: absolute !important;
                top: 1.25rem !important;
                right: 1.25rem !important;
                width: 2rem !important;
                height: 2rem !important;
                border-radius: 0.5rem !important;
                color: #94a3b8 !important;
                transition: all 0.15s ease !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                box-shadow: none !important;
                outline: none !important;
                background: transparent !important;
                border: none !important;
                cursor: pointer !important;
                padding: 0 !important;
                font-family: inherit !important;
            }
            .swal2-close-salahuddin:hover {
                color: #0f172a !important;
                background-color: #f1f5f9 !important;
            }
            .swal2-modal-salahuddin .swal2-loader {
                display: none !important;
            }
            .swal2-actions-salahuddin {
                width: 100% !important;
                margin: 1rem 0 0 0 !important;
                padding-top: 0.75rem !important;
                border-top: 1px solid #f1f5f9 !important;
                display: flex !important;
                flex-direction: row !important;
                justify-content: flex-end !important;
                align-items: center !important;
                gap: 0.625rem !important;
                box-sizing: border-box !important;
            }
            .swal2-modal-salahuddin .swal2-actions.swal2-loading {
                justify-content: flex-end !important;
            }
            .swal2-modal-salahuddin .swal2-actions button {
                margin: 0 !important;
            }
            .swal2-cancel-salahuddin {
                padding: 0.5rem 0.875rem !important;
                border-radius: 0.5rem !important;
                color: #475569 !important; /* text-slate-600 */
                font-weight: 500 !important;
                font-size: 0.875rem !important;
                background: transparent !important;
                border: none !important;
                cursor: pointer !important;
                transition: all 0.15s ease !important;
                font-family: 'Plus Jakarta Sans', sans-serif !important;
                box-shadow: none !important;
                outline: none !important;
            }
            .swal2-cancel-salahuddin:hover {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
            }
            .swal2-confirm-salahuddin {
                padding: 0.5rem 1rem !important;
                border-radius: 0.5rem !important;
                font-weight: 700 !important;
                font-size: 0.875rem !important;
                color: #ffffff !important;
                border: none !important;
                cursor: pointer !important;
                transition: all 0.15s ease !important;
                font-family: 'Plus Jakarta Sans', sans-serif !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.375rem !important;
                outline: none !important;
            }
            .swal2-confirm-salahuddin:disabled {
                opacity: 0.5 !important;
                cursor: not-allowed !important;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        </style>
        <script>
            window.showSweetAlertConfirm = function(options, wireInstance) {
                if (typeof Swal === 'undefined') {
                    console.warn('SweetAlert2 belum dimuat');
                    return;
                }

                const isDanger = !options.type || options.type === 'danger';
                const isWarning = options.type === 'warning';
                const isPrimary = options.type === 'primary';

                const title = options.title || (isPrimary ? 'Konfirmasi Tindakan' : 'Konfirmasi Hapus Data');
                const message = options.message || (isPrimary ? 'Apakah Anda yakin ingin memproses tindakan ini?' : 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.');
                const confirmText = options.confirmButtonText || (isPrimary ? 'Ya, Lanjutkan' : (isWarning ? 'Ya, Hapus' : 'Ya, Hapus'));
                const loadingText = options.loadingText || (isPrimary ? 'Memproses...' : 'Menghapus...');

                // 1. Icon Badge di kiri atas (identik persis dengan Gambar 1)
                let iconBadgeHtml = '';
                if (isPrimary) {
                    iconBadgeHtml = `
                        <div style="width: 3rem; height: 3rem; min-width: 3rem; border-radius: 0.75rem; background-color: #f0f9ff; border: 1px solid rgba(186, 230, 253, 0.7); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 1.5rem; height: 1.5rem; color: #0284c7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <line x1="19" y1="8" x2="19" y2="14"></line>
                                <line x1="22" y1="11" x2="16" y2="11"></line>
                            </svg>
                        </div>
                    `;
                } else if (isWarning) {
                    iconBadgeHtml = `
                        <div style="width: 3rem; height: 3rem; min-width: 3rem; border-radius: 0.75rem; background-color: #fffbeb; border: 1px solid rgba(253, 230, 138, 0.7); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                        </div>
                    `;
                } else {
                    // Danger (Identik dengan Gambar 1)
                    iconBadgeHtml = `
                        <div style="width: 3rem; height: 3rem; min-width: 3rem; border-radius: 0.75rem; background-color: #fff1f2; border: 1px solid rgba(254, 205, 211, 0.8); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 1.5rem; height: 1.5rem; color: #e11d48;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                        </div>
                    `;
                }

                // Header Layout
                const headerHtml = `
                    <div style="display: flex; align-items: flex-start; gap: 1rem; text-align: left; padding-right: 1.75rem;">
                        ${iconBadgeHtml}
                        <div style="flex: 1; min-width: 0;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.35; font-family: 'Plus Jakarta Sans', sans-serif;">${title}</h3>
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem; margin-bottom: 0; line-height: 1.45; font-family: 'Plus Jakarta Sans', sans-serif;">${message}</p>
                        </div>
                    </div>
                `;

                // 2. Item Name Box (Identik dengan Gambar 1: rounded-xl, background merah muda halus, teks merah tua di tengah)
                let itemHtml = '';
                if (options.itemName) {
                    const bg = isPrimary ? '#f0f9ff' : (isWarning ? '#fffbeb' : 'rgba(255, 241, 242, 0.7)');
                    const border = isPrimary ? 'rgba(186, 230, 253, 0.7)' : (isWarning ? 'rgba(253, 230, 138, 0.7)' : 'rgba(254, 205, 211, 0.7)');
                    const text = isPrimary ? '#0369a1' : (isWarning ? '#78350f' : '#881337');
                    itemHtml = `
                        <div style="padding: 0.75rem 1rem; border-radius: 0.75rem; background-color: ${bg}; border: 1px solid ${border}; color: ${text}; font-size: 0.8125rem; font-weight: 700; text-align: center; word-break: break-word; line-height: 1.45; font-family: 'Plus Jakarta Sans', sans-serif;">
                            ${options.itemName}
                        </div>
                    `;
                }

                // 3. Notice / Caution Banner (Identik dengan Gambar 1: background kuning muda halus dengan ikon info)
                let noticeHtml = '';
                if (options.noticeText !== false && options.noticeText !== '') {
                    const noticeText = options.noticeText || 'Tindakan ini tidak dapat dibatalkan. Pastikan Anda telah memeriksa kembali data yang akan diproses.';
                    const bg = isPrimary ? '#f8fafc' : 'rgba(254, 252, 232, 0.7)';
                    const border = isPrimary ? 'rgba(226, 232, 240, 0.8)' : 'rgba(253, 230, 138, 0.6)';
                    const text = isPrimary ? '#334155' : '#78350f';
                    const iconColor = isPrimary ? '#64748b' : '#d97706';
                    noticeHtml = `
                        <div style="padding: 0.75rem 1rem; border-radius: 0.75rem; background-color: ${bg}; border: 1px solid ${border}; color: ${text}; font-size: 0.6875rem; line-height: 1.45; display: flex; align-items: flex-start; gap: 0.5rem; text-align: left; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <svg style="width: 1rem; height: 1rem; color: ${iconColor}; flex-shrink: 0; margin-top: 0.125rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span style="line-height: 1.45;">${noticeText}</span>
                        </div>
                    `;
                }

                // Confirm button styling
                const confirmBg = isPrimary ? '#102a43' : (isWarning ? '#d97706' : '#e11d48');
                const confirmIconSvg = isPrimary
                    ? '<svg style="width: 0.875rem; height: 0.875rem;" class="shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>'
                    : '<svg style="width: 0.875rem; height: 0.875rem;" class="shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';

                Swal.fire({
                    html: `
                        <div style="font-family: 'Plus Jakarta Sans', sans-serif; display: flex; flex-direction: column; gap: 0.875rem;">
                            ${headerHtml}
                            ${itemHtml}
                            ${noticeHtml}
                        </div>
                    `,
                    showCloseButton: true,
                    closeButtonHtml: '<svg style="width: 1.25rem; height: 1.25rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
                    showCancelButton: true,
                    buttonsStyling: false,
                    reverseButtons: true,
                    focusCancel: true,
                    showLoaderOnConfirm: false,
                    allowOutsideClick: () => !Swal.isLoading(),
                    allowEscapeKey: () => !Swal.isLoading(),
                    confirmButtonText: `${confirmIconSvg}<span>${confirmText}</span>`,
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'swal2-modal-salahuddin',
                        closeButton: 'swal2-close-salahuddin',
                        actions: 'swal2-actions-salahuddin',
                        confirmButton: 'swal2-confirm-salahuddin',
                        cancelButton: 'swal2-cancel-salahuddin'
                    },
                    didOpen: () => {
                        const confirmBtn = Swal.getConfirmButton();
                        if (confirmBtn) {
                            confirmBtn.style.backgroundColor = confirmBg;
                        }
                    },
                    preConfirm: async () => {
                        const confirmBtn = Swal.getConfirmButton();
                        const cancelBtn = Swal.getCancelButton();
                        const closeBtn = Swal.getCloseButton();

                        if (confirmBtn) {
                            confirmBtn.disabled = true;
                            confirmBtn.innerHTML = `
                                <svg style="width: 0.875rem; height: 0.875rem; animation: spin 1s linear infinite;" viewBox="0 0 24 24" fill="none">
                                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>${loadingText}</span>
                            `;
                        }
                        if (cancelBtn) {
                            cancelBtn.disabled = true;
                            cancelBtn.style.opacity = '0.5';
                            cancelBtn.style.pointerEvents = 'none';
                        }
                        if (closeBtn) {
                            closeBtn.disabled = true;
                            closeBtn.style.opacity = '0.4';
                            closeBtn.style.pointerEvents = 'none';
                        }

                        try {
                            const params = Array.isArray(options.params)
                                ? options.params
                                : (options.id !== undefined && options.id !== null ? [options.id] : []);
                            await wireInstance.call(options.action, ...params);
                            return true;
                        } catch (error) {
                            console.error('Confirm action error:', error);
                            if (confirmBtn) {
                                confirmBtn.disabled = false;
                                confirmBtn.innerHTML = `${confirmIconSvg}<span>${confirmText}</span>`;
                            }
                            if (cancelBtn) {
                                cancelBtn.disabled = false;
                                cancelBtn.style.opacity = '1';
                                cancelBtn.style.pointerEvents = 'auto';
                            }
                            if (closeBtn) {
                                closeBtn.disabled = false;
                                closeBtn.style.opacity = '1';
                                closeBtn.style.pointerEvents = 'auto';
                            }
                            Swal.showValidationMessage(`Gagal: ${error?.message || 'Terjadi kesalahan sistem'}`);
                            return false;
                        }
                    }
                });
            };
        </script>

        @livewireScripts
        <script>
            document.addEventListener('livewire:navigated', () => {
                if (window.createLucideIcons) window.createLucideIcons();
            });

            // Global drag & drop guard: mencegah browser membuka atau mengunduh file mentah secara tak sengaja jika di-drop di luar dropzone
            ['dragover', 'drop'].forEach(eventType => {
                window.addEventListener(eventType, function(e) {
                    if (e.target && e.target.tagName === 'INPUT' && e.target.type === 'file') return;
                    e.preventDefault();
                }, false);
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

        <!-- PWA Service Worker Registration (Clean, Zero-Reload Engine) -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').catch(function(error) {
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
