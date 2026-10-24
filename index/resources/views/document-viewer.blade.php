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
            background-color: #0f172a;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            -webkit-tap-highlight-color: transparent;
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
            #documentContainer {
                height: auto !important;
            }
            #documentFrame {
                height: 100vh !important;
            }
        }
    </style>
</head>
<body class="flex flex-col h-screen h-[100dvh] antialiased">

    <!-- Sticky Navigation Topbar (Navy Gov & Harmonious Slate Palette) -->
    <header class="no-print bg-slate-900 border-b border-slate-800 px-3 sm:px-6 h-[52px] shrink-0 flex items-center justify-between gap-3 shadow-md z-30 select-none">
        
        <!-- Sisi Kiri: Tombol Kembali -->
        <div class="flex items-center shrink-0">
            <a href="{{ $fallbackUrl }}"
                class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-950 text-slate-200 hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-700 transition-colors shadow-2xs cursor-pointer select-none"
                title="Kembali ke halaman sebelumnya">
                <svg class="w-4 h-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Sisi Tengah: Dikosongkan agar tetap bersih & lega -->
        <div class="flex-1"></div>

        <!-- Sisi Kanan: Tombol Aksi Cetak -->
        <div class="flex items-center gap-2 shrink-0">
            <!-- Tombol Cetak (Buka Dokumen Asli untuk Cetak / AirPrint / Simpan PDF) -->
            <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-600 transition-colors shadow-2xs cursor-pointer select-none"
                title="Buka untuk Cetak / Simpan PDF">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak</span>
            </a>
        </div>
    </header>

    <!-- Main Content: Embedded Responsive Document Container -->
    <main id="documentContainer" class="flex-1 w-full bg-slate-900 relative overflow-hidden flex flex-col">
        <iframe 
            id="documentFrame" 
            src="{{ $fileUrl }}" 
            class="w-full flex-1 border-0 bg-white"
            title="Pratinjau Dokumen">
        </iframe>
    </main>
</body>
</html>
