{{-- PWA Install Banner Component: Non-intrusive, smartphone-only, cross-browser (Android & iOS Safari) --}}
<style>
    #pwa-install-banner {
        position: fixed;
        bottom: 1rem;
        left: 0.75rem;
        right: 0.75rem;
        z-index: 9999;
        margin: 0 auto;
        max-width: 440px;
        box-sizing: border-box;
    }

    #pwa-guide-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        z-index: 10000;
        background-color: rgba(15, 23, 42, 0.7);
        display: none;
        align-items: flex-end;
        justify-content: center;
        padding: 1rem;
        box-sizing: border-box;
    }

    @media (min-width: 640px) {
        #pwa-guide-modal {
            align-items: center;
        }
    }

    /* Strict Desktop Suppression: Never display on screens >= 768px */
    @media (min-width: 768px) {
        #pwa-install-banner,
        #pwa-guide-modal {
            display: none !important;
        }
    }
</style>

<!-- Floating Mobile Install Banner -->
<div id="pwa-install-banner" style="display: none;">
    <div style="background-color: #102a43; color: #ffffff; padding: 0.875rem 1rem; border-radius: 1rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1;">
            <div style="width: 2.75rem; height: 2.75rem; border-radius: 0.75rem; background-color: #ffffff; padding: 0.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <img src="/images/icons/icon-96x96.png" alt="Logo Masjid Salahuddin" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div style="min-width: 0; flex: 1;">
                <h4 style="font-size: 0.8125rem; font-weight: 700; margin: 0; line-height: 1.25; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ffffff;">
                    Pasang Aplikasi Masjid
                </h4>
                <p style="font-size: 0.6875rem; color: #cbd5e1; margin: 0.2rem 0 0 0; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    Akses cepat jadwal & kajian tanpa browser
                </p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.375rem; flex-shrink: 0;">
            <button id="pwa-install-btn" 
                    type="button" 
                    style="background-color: #fbbf24; color: #102a43; font-weight: 700; font-size: 0.75rem; padding: 0.45rem 0.875rem; border-radius: 0.5rem; display: inline-flex; align-items: center; gap: 0.25rem; border: none; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 0.15s ease;"
                    onmouseover="this.style.backgroundColor='#f59e0b'" 
                    onmouseout="this.style.backgroundColor='#fbbf24'">
                <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Pasang</span>
            </button>
            <button id="pwa-dismiss-btn" 
                    type="button" 
                    style="background: transparent; border: none; padding: 0.375rem; color: #94a3b8; border-radius: 0.5rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: color 0.15s ease;"
                    onmouseover="this.style.color='#ffffff'" 
                    onmouseout="this.style.color='#94a3b8'"
                    title="Tutup (ingatkan 2 hari lagi)">
                <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Platform Guide Modal (For iOS Safari and manual fallback on Android) -->
<div id="pwa-guide-modal" style="display: none;">
    <div style="background-color: #ffffff; border-radius: 1rem; padding: 1.25rem; width: 100%; max-width: 22rem; margin: 0 auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; color: #1e293b; box-sizing: border-box;">
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.625rem; background-color: #ffffff; padding: 0.25rem; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                    <img src="/images/icons/icon-72x72.png" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div>
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #102a43; margin: 0; line-height: 1.2;">Pasang Aplikasi Masjid</h3>
                    <p style="font-size: 0.6875rem; color: #64748b; font-weight: 500; margin: 0.15rem 0 0 0; line-height: 1;">Panduan Layar Utama</p>
                </div>
            </div>
            <button id="pwa-guide-close-btn" type="button" style="background: transparent; border: none; color: #94a3b8; padding: 0.25rem; border-radius: 0.375rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div id="pwa-guide-content" style="font-size: 0.75rem; line-height: 1.5; color: #475569; margin-bottom: 1rem;">
            <!-- Populated via JavaScript dynamically based on iOS or Android -->
        </div>

        <button id="pwa-guide-ack-btn" type="button" style="width: 100%; padding: 0.625rem 1rem; border-radius: 0.75rem; background-color: #102a43; color: #ffffff; font-weight: 700; font-size: 0.75rem; border: none; cursor: pointer; text-align: center; transition: background-color 0.15s ease;"
                onmouseover="this.style.backgroundColor='#1e3a5f'"
                onmouseout="this.style.backgroundColor='#102a43'">
            Saya Mengerti
        </button>
    </div>
</div>

<script>
(function() {
    let deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');
    const guideModal = document.getElementById('pwa-guide-modal');
    const guideContent = document.getElementById('pwa-guide-content');
    const guideCloseBtn = document.getElementById('pwa-guide-close-btn');
    const guideAckBtn = document.getElementById('pwa-guide-ack-btn');

    const STORAGE_KEY = 'ms_pwa_dismissed_until';

    // Allow manual force-show via URL parameter (e.g. ?pwa=1)
    if (window.location.search.includes('pwa=1') || window.location.search.includes('install=1')) {
        try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
    }

    // 1. Strict Smartphone Detection (< 768px or mobile user-agent)
    const isSmartphone = () => {
        return window.innerWidth < 768 || 
               /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile/i.test(navigator.userAgent);
    };

    // 2. Genuine Standalone Mode Detection (Only true if opened via installed PWA app icon)
    const isStandalone = () => {
        return (window.matchMedia && (
            window.matchMedia('(display-mode: standalone)').matches ||
            window.matchMedia('(display-mode: fullscreen)').matches ||
            window.matchMedia('(display-mode: minimal-ui)').matches
        )) || window.navigator.standalone === true;
    };

    // 3. Check dismissal cooldown
    const isDismissed = () => {
        try {
            const until = localStorage.getItem(STORAGE_KEY);
            return until && Date.now() < parseInt(until, 10);
        } catch (e) {
            return false;
        }
    };

    const showBanner = () => {
        // Never show on desktop or if already running inside standalone installed PWA
        if (!isSmartphone() || isStandalone() || isDismissed()) {
            return;
        }
        if (banner) {
            banner.style.display = 'block';
        }
    };

    const hideBanner = (days = 2) => {
        if (banner) {
            banner.style.display = 'none';
        }
        try {
            localStorage.setItem(STORAGE_KEY, (Date.now() + days * 24 * 60 * 60 * 1000).toString());
        } catch (e) {}
    };

    const isIos = () => {
        return /iPhone|iPad|iPod/i.test(navigator.userAgent) && !window.MSStream;
    };

    const showGuide = () => {
        if (!guideModal || !guideContent) return;

        if (isIos()) {
            guideContent.innerHTML = `
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.75rem; display: flex; flex-direction: column; gap: 0.625rem;">
                    <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="width: 1.25rem; height: 1.25rem; border-radius: 9999px; background-color: #102a43; color: #ffffff; font-size: 0.6875rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.1rem;">1</span>
                        <span>Ketuk tombol <strong>Bagikan (Share)</strong> <svg style="display: inline-block; width: 0.875rem; height: 0.875rem; color: #2563eb; vertical-align: -0.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><polyline points="16 6 12 2 8 6"></polyline><line x1="12" y1="2" x2="12" y2="15"></line></svg> di bilah bawah Safari.</span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="width: 1.25rem; height: 1.25rem; border-radius: 9999px; background-color: #102a43; color: #ffffff; font-size: 0.6875rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.1rem;">2</span>
                        <span>Gulir ke bawah dan pilih opsi <strong>Tambah ke Layar Utama</strong> (<em>Add to Home Screen</em>) 📲.</span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="width: 1.25rem; height: 1.25rem; border-radius: 9999px; background-color: #102a43; color: #ffffff; font-size: 0.6875rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.1rem;">3</span>
                        <span>Ketuk <strong>Tambah</strong> di pojok kanan atas untuk menyelesaikan.</span>
                    </div>
                </div>
            `;
        } else {
            guideContent.innerHTML = `
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.75rem; display: flex; flex-direction: column; gap: 0.625rem;">
                    <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="width: 1.25rem; height: 1.25rem; border-radius: 9999px; background-color: #102a43; color: #ffffff; font-size: 0.6875rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.1rem;">1</span>
                        <span>Ketuk menu <strong>titik tiga (⋮)</strong> di pojok kanan atas browser.</span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="width: 1.25rem; height: 1.25rem; border-radius: 9999px; background-color: #102a43; color: #ffffff; font-size: 0.6875rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.1rem;">2</span>
                        <span>Pilih opsi <strong>Instal aplikasi</strong> atau <strong>Tambahkan ke Layar Utama</strong>.</span>
                    </div>
                </div>
            `;
        }

        guideModal.style.display = 'flex';
    };

    const hideGuide = () => {
        if (guideModal) {
            guideModal.style.display = 'none';
        }
    };

    // Global helper to trigger install from any button/link
    window.installPwa = () => {
        if (deferredPrompt) {
            hideBanner(0);
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choice) => {
                deferredPrompt = null;
                if (choice.outcome === 'accepted') hideBanner(30);
            });
        } else {
            showGuide();
        }
    };

    // Global helper to force show banner
    window.showPwaBanner = showBanner;

    // Capture Chromium/Android beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showBanner();
    });

    // Directly schedule presentation on smartphones without blocking on window.load
    setTimeout(showBanner, 800);
    setTimeout(showBanner, 2200);

    // Handle "Pasang" button click
    if (installBtn) {
        installBtn.addEventListener('click', () => {
            window.installPwa();
        });
    }

    // Handle "Tutup" button click
    if (dismissBtn) {
        dismissBtn.addEventListener('click', () => hideBanner(2));
    }

    // Guide Modal dismissal handlers
    if (guideCloseBtn) guideCloseBtn.addEventListener('click', hideGuide);
    if (guideAckBtn) guideAckBtn.addEventListener('click', () => {
        hideGuide();
        hideBanner(2);
    });
    if (guideModal) {
        guideModal.addEventListener('click', (e) => {
            if (e.target === guideModal) hideGuide();
        });
    }

    // Hide banner once successfully installed
    window.addEventListener('appinstalled', () => {
        hideBanner(365);
        deferredPrompt = null;
    });
})();
</script>
