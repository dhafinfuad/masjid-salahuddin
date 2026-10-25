<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Penampil Dokumen — Masjid Salahuddin</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('resources/Logo Masjid Salahuddin.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Arial', 'sans-serif'],
                    },
                    colors: {
                        gov: {
                            navy: '#06172e',
                            navyHover: '#0a2347',
                            border: '#c3c9d4',
                            textMain: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Mozilla PDF.js Local Asset -->
    <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #0b1120;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        /* Styling Kanvas Halaman PDF */
        .pdf-page-wrapper {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45);
            background-color: #ffffff;
            transition: transform 0.15s ease-out;
        }

        .pdf-page-canvas {
            display: block;
            width: 100%;
            height: auto;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            html, body {
                height: auto !important;
                overflow: visible !important;
                background: #ffffff !important;
            }
            #viewerScrollContainer {
                overflow: visible !important;
                height: auto !important;
                padding: 0 !important;
                background: #ffffff !important;
            }
            .pdf-page-wrapper {
                box-shadow: none !important;
                margin-bottom: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }
        }
    </style>
</head>
<body class="flex flex-col h-screen h-[100dvh] antialiased select-none">

    <!-- Sticky Navigation Topbar (Navy Gov & Slate Palette dengan Toolbar Lengkap) -->
    <header class="no-print bg-slate-900 border-b border-slate-800 px-2 sm:px-4 h-[54px] shrink-0 flex items-center justify-between gap-1.5 sm:gap-3 shadow-md z-30">
        
        <!-- Sisi Kiri: Tombol Kembali -->
        <div class="flex items-center shrink-0">
            <a href="{{ $fallbackUrl }}"
                class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-950 text-slate-200 hover:text-white text-xs font-semibold px-2.5 sm:px-3 py-1.5 rounded-lg border border-slate-700 transition-colors shadow-2xs cursor-pointer"
                title="Kembali ke halaman sebelumnya">
                <svg class="w-4 h-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span class="hidden xs:inline">Kembali</span>
            </a>
        </div>

        <!-- Sisi Tengah: Kontrol Halaman (1 / 2) & Zoom (- / +) -->
        <div class="flex items-center gap-1 sm:gap-2">
            
            <!-- Kontrol Pindah Halaman -->
            <div class="flex items-center bg-slate-800/90 border border-slate-700/80 rounded-lg p-0.5 text-xs text-slate-200">
                <button type="button" id="btnPrevPage" onclick="goToPrevPage()" 
                    class="p-1.5 hover:bg-slate-700 active:bg-slate-900 rounded disabled:opacity-30 disabled:cursor-not-allowed transition" 
                    title="Halaman Sebelumnya" disabled>
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <span id="pageIndicator" class="px-2 py-0.5 font-mono text-[11px] sm:text-xs font-semibold tracking-tight">1 / 1</span>
                <button type="button" id="btnNextPage" onclick="goToNextPage()" 
                    class="p-1.5 hover:bg-slate-700 active:bg-slate-900 rounded disabled:opacity-30 disabled:cursor-not-allowed transition" 
                    title="Halaman Selanjutnya" disabled>
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>

            <!-- Kontrol Zoom (- / +) -->
            <div class="flex items-center bg-slate-800/90 border border-slate-700/80 rounded-lg p-0.5 text-xs text-slate-200">
                <button type="button" id="btnZoomOut" onclick="zoomOut()" 
                    class="p-1.5 hover:bg-slate-700 active:bg-slate-900 rounded disabled:opacity-30 disabled:cursor-not-allowed transition" 
                    title="Perkecil Tampilan">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
                <span id="zoomIndicator" class="px-1.5 py-0.5 font-mono text-[11px] sm:text-xs font-semibold tracking-tight hidden sm:inline">100%</span>
                <button type="button" id="btnZoomIn" onclick="zoomIn()" 
                    class="p-1.5 hover:bg-slate-700 active:bg-slate-900 rounded disabled:opacity-30 disabled:cursor-not-allowed transition" 
                    title="Perbesar Tampilan">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
            </div>
        </div>

        <!-- Sisi Kanan: Tombol Unduh PDF & Bagikan/Cetak -->
        <div class="flex items-center gap-1 sm:gap-2 shrink-0">
            <!-- Tombol Cetak / Bagikan -->
            <button type="button" onclick="triggerMobileShareOrPrint()"
                class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-950 text-slate-200 hover:text-white text-xs font-semibold px-2.5 sm:px-3 py-1.5 rounded-lg border border-slate-700 transition-colors shadow-2xs cursor-pointer"
                title="Cetak atau Bagikan Dokumen">
                <svg class="w-4 h-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span class="hidden md:inline">Cetak</span>
            </button>

            <!-- Tombol Unduh PDF -->
            <a href="{{ $fileUrl }}" download="{{ $downloadFilename }}"
                class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-200 hover:text-white text-xs font-semibold px-2.5 sm:px-3 py-1.5 rounded-lg border border-slate-700 transition-colors shadow-2xs cursor-pointer"
                title="Unduh Berkas PDF Fisik">
                <svg class="w-4 h-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Unduh</span>
            </a>
        </div>
    </header>

    <!-- Main Content: Scrollable PDF Canvas Container -->
    <main id="viewerScrollContainer" class="flex-1 w-full bg-slate-950 relative overflow-y-auto overflow-x-auto p-2 sm:p-4 flex flex-col items-center">
        
        <!-- Loading Spinner -->
        <div id="pdfLoadingState" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/80 z-20 backdrop-blur-2xs transition-opacity duration-300">
            <div class="w-8 h-8 border-3 border-amber-400 border-t-transparent rounded-full animate-spin mb-3"></div>
            <p class="text-xs font-medium text-slate-300">Memuat berkas dokumen PDF...</p>
        </div>

        <!-- Error State -->
        <div id="pdfErrorState" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-slate-950 p-6 z-20 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-500/10 text-rose-400 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <h4 class="text-sm font-bold text-white mb-1">Gagal Membuka Dokumen</h4>
            <p id="pdfErrorMessage" class="text-xs text-slate-400 mb-4 max-w-sm">Berkas PDF tidak dapat dimuat atau rusak.</p>
            <a href="{{ $fileUrl }}" download="{{ $downloadFilename }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold border border-slate-700 transition">
                Unduh Berkas Langsung
            </a>
        </div>

        <!-- Rendered Pages Container -->
        <div id="pdfPagesContainer" class="w-full flex flex-col items-center space-y-3 sm:space-y-4 my-auto"></div>
    </main>

    <script>
        const PDF_URL = @json($fileUrl);
        const PDF_FILENAME = @json($downloadFilename);
        const WORKER_URL = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";

        let currentPdf = null;
        let totalPages = 0;
        let currentPage = 1;
        let currentScale = 1.0;
        const MIN_SCALE = 0.6;
        const MAX_SCALE = 2.5;

        // Inisialisasi Worker PDF.js
        if (typeof window.pdfjsLib !== 'undefined') {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = WORKER_URL;
        }

        async function initPdfViewer() {
            const loadingEl = document.getElementById('pdfLoadingState');
            const errorEl = document.getElementById('pdfErrorState');
            const errorMsgEl = document.getElementById('pdfErrorMessage');

            try {
                if (typeof window.pdfjsLib === 'undefined') {
                    throw new Error("Modul PDF.js tidak ditemukan.");
                }

                // Hitung skala awal adaptif terhadap lebar layar HP
                const containerWidth = document.getElementById('viewerScrollContainer').clientWidth - 24;
                const baseWidth = Math.min(containerWidth, 680);
                // Ukuran standar halaman A4 adalah 595.28 pt
                currentScale = Math.min(Math.max(baseWidth / 595, 0.75), 1.25);
                updateZoomIndicator();

                const loadingTask = window.pdfjsLib.getDocument({
                    url: PDF_URL,
                    cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/',
                    cMapPacked: true,
                });

                currentPdf = await loadingTask.promise;
                totalPages = currentPdf.numPages;
                currentPage = 1;

                updatePageIndicator();
                updatePageButtons();

                await renderAllPages();

                loadingEl.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => loadingEl.classList.add('hidden'), 300);

                setupScrollObserver();

            } catch (err) {
                console.error("Gagal memuat PDF:", err);
                loadingEl.classList.add('hidden');
                errorEl.classList.remove('hidden');
                if (errorMsgEl) {
                    errorMsgEl.textContent = err.message || "Gagal memproses dokumen PDF.";
                }
            }
        }

        async function renderAllPages() {
            const container = document.getElementById('pdfPagesContainer');
            container.innerHTML = '';

            const dpr = window.devicePixelRatio || 1;

            for (let i = 1; i <= totalPages; i++) {
                const page = await currentPdf.getPage(i);
                const viewport = page.getViewport({ scale: currentScale });

                const wrapper = document.createElement('div');
                wrapper.className = 'pdf-page-wrapper rounded-lg overflow-hidden border border-slate-800';
                wrapper.id = 'page-wrapper-' + i;
                wrapper.setAttribute('data-page-number', i);
                wrapper.style.width = Math.round(viewport.width) + 'px';

                const canvas = document.createElement('canvas');
                canvas.className = 'pdf-page-canvas';
                canvas.height = Math.round(viewport.height * dpr);
                canvas.width = Math.round(viewport.width * dpr);
                canvas.style.width = Math.round(viewport.width) + 'px';
                canvas.style.height = Math.round(viewport.height) + 'px';

                const ctx = canvas.getContext('2d');
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

                wrapper.appendChild(canvas);
                container.appendChild(wrapper);

                await page.render({
                    canvasContext: ctx,
                    viewport: viewport
                }).promise;
            }
        }

        function updatePageIndicator() {
            const el = document.getElementById('pageIndicator');
            if (el) {
                el.textContent = `${currentPage} / ${totalPages || 1}`;
            }
        }

        function updatePageButtons() {
            const btnPrev = document.getElementById('btnPrevPage');
            const btnNext = document.getElementById('btnNextPage');
            if (btnPrev) btnPrev.disabled = (currentPage <= 1);
            if (btnNext) btnNext.disabled = (currentPage >= totalPages);
        }

        function updateZoomIndicator() {
            const el = document.getElementById('zoomIndicator');
            if (el) {
                el.textContent = Math.round(currentScale * 100) + '%';
            }
        }

        function goToPrevPage() {
            if (currentPage > 1) {
                scrollToPage(currentPage - 1);
            }
        }

        function goToNextPage() {
            if (currentPage < totalPages) {
                scrollToPage(currentPage + 1);
            }
        }

        function scrollToPage(pageNum) {
            const targetEl = document.getElementById('page-wrapper-' + pageNum);
            if (targetEl) {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                currentPage = pageNum;
                updatePageIndicator();
                updatePageButtons();
            }
        }

        function zoomIn() {
            if (currentScale < MAX_SCALE) {
                currentScale = Math.min(currentScale + 0.2, MAX_SCALE);
                updateZoomIndicator();
                renderAllPages();
            }
        }

        function zoomOut() {
            if (currentScale > MIN_SCALE) {
                currentScale = Math.max(currentScale - 0.2, MIN_SCALE);
                updateZoomIndicator();
                renderAllPages();
            }
        }

        function setupScrollObserver() {
            const container = document.getElementById('viewerScrollContainer');
            const pages = document.querySelectorAll('.pdf-page-wrapper');

            container.addEventListener('scroll', () => {
                let currentVisiblePage = currentPage;
                const containerTop = container.scrollTop;
                const containerHeight = container.clientHeight;

                pages.forEach((pageEl) => {
                    const top = pageEl.offsetTop - container.offsetTop;
                    const height = pageEl.offsetHeight;
                    if (containerTop >= top - 80 && containerTop < top + height - 80) {
                        const pageNum = parseInt(pageEl.getAttribute('data-page-number'), 10);
                        if (!isNaN(pageNum)) {
                            currentVisiblePage = pageNum;
                        }
                    }
                });

                if (currentVisiblePage !== currentPage) {
                    currentPage = currentVisiblePage;
                    updatePageIndicator();
                    updatePageButtons();
                }
            }, { passive: true });
        }

        async function triggerMobileShareOrPrint() {
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            const fullUrl = PDF_URL.startsWith('http') ? PDF_URL : (window.location.origin + PDF_URL);

            if (isMobile && navigator.share) {
                try {
                    await navigator.share({
                        title: 'Dokumen Masjid Salahuddin',
                        url: fullUrl
                    });
                    return;
                } catch (e) {
                    if (e.name === 'AbortError') return;
                }
            }

            window.print();
        }

        document.addEventListener('DOMContentLoaded', initPdfViewer);
    </script>
</body>
</html>
