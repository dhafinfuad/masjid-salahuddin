import { createIcons, icons } from 'lucide';
import html2canvas from 'html2canvas-pro';

window.html2canvas = html2canvas;

let isRenderingIcons = false;
window.createLucideIcons = () => {
    if (isRenderingIcons) return;

    // Fast bail out: only convert if unconverted icon tags exist in DOM
    const unrendered = document.querySelectorAll('i[data-lucide], span[data-lucide]');
    if (unrendered.length === 0) return;

    isRenderingIcons = true;
    try {
        createIcons({ icons });
    } catch (e) {
        // Ignore during transitions
    } finally {
        isRenderingIcons = false;
    }
};

// 1. Initial page load
document.addEventListener('DOMContentLoaded', () => {
    window.createLucideIcons();
});

// 2. Livewire navigate (SPA page switches)
document.addEventListener('livewire:navigated', () => {
    window.createLucideIcons();
});

// 3. Livewire lifecycle hooks: run ONCE after commit roundtrip completes
document.addEventListener('livewire:initialized', () => {
    window.createLucideIcons();
    
    if (window.Livewire) {
        // Run once after server roundtrip commits and finishes morphing
        window.Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                requestAnimationFrame(() => {
                    window.createLucideIcons();
                });
            });
        });
    }
});

// 4. Modal Backdrop Guard: Distinguish genuine backdrop clicks from drag-selection releases
let _lastMouseDownTarget = null;
let _lastMouseUpTarget = null;
document.addEventListener('mousedown', (e) => { _lastMouseDownTarget = e.target; }, true);
document.addEventListener('mouseup', (e) => { _lastMouseUpTarget = e.target; }, true);
document.addEventListener('touchstart', (e) => { _lastMouseDownTarget = e.target; }, { capture: true, passive: true });
document.addEventListener('touchend', (e) => { _lastMouseUpTarget = e.target; }, { capture: true, passive: true });

window.isBackdropClick = (e, element) => {
    const el = element || (e && (e.currentTarget || e.target));
    if (!el || !e) return false;
    return (_lastMouseDownTarget === el) && (_lastMouseUpTarget === el) && (e.target === el);
};

// 5. Global Thousand Separator (Ribuan Titik) Helpers & Alpine Directive
window.formatRibuan = (val) => {
    if (val === null || val === undefined || val === '') return '';
    let str = String(val).replace(/\D/g, '');
    if (!str) return '';
    str = str.replace(/^0+(?=\d)/, '');
    return str.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

window.handleRibuanInput = (el, onCleanValue) => {
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

const setupRibuanDirective = (alp) => {
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
};

if (window.Alpine) {
    setupRibuanDirective(window.Alpine);
} else {
    document.addEventListener('alpine:init', () => setupRibuanDirective(window.Alpine));
}
document.addEventListener('livewire:init', () => {
    if (window.Alpine) setupRibuanDirective(window.Alpine);
});

// 6. html2canvas-pro Loader with native OKLCH & Modern Color Support
window.getHtml2Canvas = async () => {
    if (window.html2canvas && typeof window.html2canvas === 'function') {
        return window.html2canvas;
    }
    window.html2canvas = html2canvas;
    return html2canvas;
};

// 7. Global Alpine Component: posterExporter (On-Demand 720x720 px Poster Exporter)
window.posterExporter = function (baseConfig = {}) {
    const config = Object.assign({}, window.posterSettingConfig || {}, baseConfig || {});
    return {
        state: {
            template: config.template || 1,
            masjid_line1: config.masjid_line1 || 'KPP Madya Malang',
            masjid_line2: config.masjid_line2 || 'Masjid Salahuddin',
            footer_label: config.footer_label || 'Informasi Kajian :',
            footer_url: config.footer_url || 'masjidsalahuddin.my.id',
            logo_url: config.logo_url || '/resources/Logo Masjid Salahuddin.svg',
            logo_size: config.logo_size || 32,
            title_font_size: config.title_font_size || 42,
            desc_font_size: config.desc_font_size || 15,
            title_y: config.title_y || 10,
            desc_y: config.desc_y || 8,
            title_desc_gap: config.title_desc_gap || 14,
            schedule_gap: config.schedule_gap || 20,
            content_y: config.content_y || 0,
            show_pattern: config.show_pattern ?? true,
            show_leaves: config.show_leaves ?? true,
            title: 'Kajian Tematik',
            subtitle: '',
            speaker: 'Nama Ustadz',
            date: 'Hari, DD MMMM YYYY',
            time: '08.30–10.00 WIB',
            location: 'Masjid Salahuddin',
            photo: null,
            posX: 0,
            posY: 0,
            zoom: 100
        },
        titleP1: 'Kajian',
        titleP2: 'Tematik',
        isGenerating: false,
        showIosModal: false,
        currentBlobUrl: null,
        currentFile: null,
        currentFileName: '',
        _shadowCache: {},

        async ensureHtml2Canvas() {
            if (typeof window.html2canvas === 'function') return window.html2canvas;
            if (window.getHtml2Canvas) {
                return await window.getHtml2Canvas();
            }
            return html2canvas;
        },

        roundRectPath(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        },

        createSoftRasterShadow(width, height, radius, shadowLayers, scale = 2) {
            const pad = 50;
            const canvas = document.createElement('canvas');
            canvas.width = Math.ceil((width + pad * 2) * scale);
            canvas.height = Math.ceil((height + pad * 2) * scale);
            const ctx = canvas.getContext('2d');
            ctx.scale(scale, scale);

            shadowLayers.forEach(s => {
                ctx.save();
                ctx.shadowColor = s.color;
                ctx.shadowBlur = s.blur;
                ctx.shadowOffsetX = s.offsetX;
                ctx.shadowOffsetY = s.offsetY;

                ctx.fillStyle = '#000000';
                this.roundRectPath(ctx, pad, pad, width, height, radius);
                ctx.fill();
                ctx.restore();

                // Punch-out bagian tengah
                ctx.save();
                ctx.globalCompositeOperation = 'destination-out';
                ctx.fillStyle = '#000000';
                this.roundRectPath(ctx, pad, pad, width, height, radius);
                ctx.fill();
                ctx.restore();
            });

            return {
                dataUrl: canvas.toDataURL('image/png'),
                width: width + pad * 2,
                height: height + pad * 2,
                offset: pad
            };
        },

        updateRasterShadows() {
            const tpl = this.state.template;
            if (!this._shadowCache[tpl]) {
                const scheduleCard = document.getElementById('exporterScheduleCard');
                const polaroidCard = document.getElementById('exporterPolaroidCard');
                const sW = (scheduleCard && scheduleCard.offsetWidth) || 218;
                const sH = (scheduleCard && scheduleCard.offsetHeight) || 180;
                const pW = (polaroidCard && polaroidCard.offsetWidth) || 195;
                const pH = (polaroidCard && polaroidCard.offsetHeight) || 250;
                const isTpl1 = tpl === 1;

                const sShadows = isTpl1 ? [
                    { color: 'rgba(20, 60, 30, 0.11)', blur: 24, offsetX: 0, offsetY: 7 },
                    { color: 'rgba(0, 0, 0, 0.06)', blur: 8, offsetX: 0, offsetY: 3 }
                ] : [
                    { color: 'rgba(0, 15, 55, 0.28)', blur: 24, offsetX: 0, offsetY: 7 },
                    { color: 'rgba(0, 10, 35, 0.16)', blur: 8, offsetX: 0, offsetY: 3 }
                ];

                const pShadows = isTpl1 ? [
                    { color: 'rgba(25, 45, 25, 0.12)', blur: 26, offsetX: 1, offsetY: 8 },
                    { color: 'rgba(0, 0, 0, 0.07)', blur: 8, offsetX: 1, offsetY: 3 }
                ] : [
                    { color: 'rgba(0, 15, 55, 0.28)', blur: 26, offsetX: 1, offsetY: 8 },
                    { color: 'rgba(0, 10, 35, 0.16)', blur: 8, offsetX: 1, offsetY: 3 }
                ];

                this._shadowCache[tpl] = {
                    schedule: this.createSoftRasterShadow(sW, sH, 16, sShadows, 2),
                    polaroid: this.createSoftRasterShadow(pW, pH, 8, pShadows, 2)
                };
            }

            const scheduleImg = document.getElementById('exporterScheduleShadowImg');
            const polaroidImg = document.getElementById('exporterPolaroidShadowImg');
            if (scheduleImg && polaroidImg) {
                const cached = this._shadowCache[tpl];
                scheduleImg.src = cached.schedule.dataUrl;
                scheduleImg.style.width = `${cached.schedule.width}px`;
                scheduleImg.style.height = `${cached.schedule.height}px`;
                scheduleImg.style.left = `${-cached.schedule.offset}px`;
                scheduleImg.style.top = `${-cached.schedule.offset}px`;

                polaroidImg.src = cached.polaroid.dataUrl;
                polaroidImg.style.width = `${cached.polaroid.width}px`;
                polaroidImg.style.height = `${cached.polaroid.height}px`;
                polaroidImg.style.left = `${-cached.polaroid.offset}px`;
                polaroidImg.style.top = `${-cached.polaroid.offset}px`;
            }
        },

        drawSpeakerPhoto(img) {
            const canvas = document.getElementById('exporterSpeakerCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const cw = canvas.width;
            const ch = canvas.height;

            ctx.clearRect(0, 0, cw, ch);

            if (img && img.naturalWidth) {
                const scale = Math.max(cw / img.naturalWidth, ch / img.naturalHeight) * ((this.state.zoom || 100) / 100);
                const w = img.naturalWidth * scale;
                const h = img.naturalHeight * scale;
                const x = (cw - w) / 2 + (this.state.posX || 0);
                const y = (ch - h) / 2 + (this.state.posY || 0);
                ctx.drawImage(img, x, y, w, h);
            } else {
                const skyGrad = ctx.createLinearGradient(0, 0, 0, ch);
                skyGrad.addColorStop(0, '#b8ddf5');
                skyGrad.addColorStop(0.65, '#e4f1fb');
                ctx.fillStyle = skyGrad;
                ctx.fillRect(0, 0, cw, ch);

                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.arc(cw * 0.52, ch * 0.28, 38, 0, Math.PI * 2);
                ctx.arc(cw * 0.43, ch * 0.32, 28, 0, Math.PI * 2);
                ctx.arc(cw * 0.62, ch * 0.33, 26, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillRect(cw * 0.40, ch * 0.31, 95, 20);

                ctx.fillStyle = '#9cb84e';
                ctx.beginPath();
                ctx.moveTo(0, ch * 0.64);
                ctx.bezierCurveTo(cw * 0.35, ch * 0.55, cw * 0.65, ch * 0.72, cw, ch * 0.60);
                ctx.lineTo(cw, ch);
                ctx.lineTo(0, ch);
                ctx.closePath();
                ctx.fill();

                ctx.fillStyle = '#7a961f';
                ctx.beginPath();
                ctx.moveTo(0, ch * 0.74);
                ctx.bezierCurveTo(cw * 0.4, ch * 0.85, cw * 0.7, ch * 0.68, cw, ch * 0.76);
                ctx.lineTo(cw, ch);
                ctx.lineTo(0, ch);
                ctx.closePath();
                ctx.fill();
            }
        },

        closeModal() {
            this.showIosModal = false;
        },

        openInNewTab() {
            if (this.currentBlobUrl) {
                window.open(this.currentBlobUrl, '_blank');
            }
        },

        async sharePoster() {
            if (this.currentFile && navigator.canShare && navigator.canShare({ files: [this.currentFile] })) {
                try {
                    await navigator.share({
                        files: [this.currentFile],
                        title: this.currentFileName,
                        text: `Poster ${this.state.title}`
                    });
                    return;
                } catch (e) {
                    if (e.name === 'AbortError') return;
                }
            }
            if (this.currentBlobUrl) {
                window.open(this.currentBlobUrl, '_blank');
            }
        },

        async generate(detail) {
            if (this.isGenerating) return;
            this.isGenerating = true;

            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message: 'Menyiapkan pembuatan poster (720×720 px)...' }
            }));

            try {
                const h2c = await this.ensureHtml2Canvas();

                // Merge data dari event
                if (detail.config && typeof detail.config === 'object') {
                    Object.assign(this.state, detail.config);
                }
                const tpl = (detail.template !== undefined && detail.template !== null) ? Number(detail.template) : Number(this.state.template || 1);
                this.state.template = tpl;
                this.state.title = detail.title || 'Kajian';
                this.state.subtitle = detail.subtitle || '';
                this.state.speaker = detail.speaker || 'Asatidz';
                this.state.date = detail.date || '';
                this.state.time = detail.time || '';
                this.state.location = detail.location || 'Masjid Salahuddin';

                if (tpl === 1) {
                    if (detail.title1 || detail.title2) {
                        this.titleP1 = detail.title1 || 'Kajian';
                        const cleanT2 = (detail.title2 || 'Tematik').replace(/^kajian\s+/i, '');
                        this.titleP2 = ' ' + cleanT2;
                    } else {
                        const words = (this.state.title || '').trim().split(/\s+/);
                        if (words.length > 1) {
                            this.titleP1 = words[0];
                            this.titleP2 = ' ' + words.slice(1).join(' ');
                        } else {
                            this.titleP1 = words[0] || 'Kajian';
                            this.titleP2 = '';
                        }
                    }
                } else {
                    this.titleP1 = '';
                    const rawT2 = detail.title || detail.title2 || this.state.title || 'KAJIAN PEKANAN';
                    this.titleP2 = rawT2.toUpperCase();
                }

                let loadedImg = null;
                if (detail.photo && typeof detail.photo === 'string' && detail.photo.trim() !== '') {
                    try {
                        loadedImg = await new Promise((resolve) => {
                            const timer = setTimeout(() => resolve(null), 400);
                            const img = new Image();
                            img.crossOrigin = 'anonymous';
                            img.onload = () => {
                                clearTimeout(timer);
                                resolve(img);
                            };
                            img.onerror = () => {
                                clearTimeout(timer);
                                resolve(null);
                            };
                            img.src = detail.photo;
                            if (img.complete && img.naturalWidth) {
                                clearTimeout(timer);
                                resolve(img);
                            }
                        });
                    } catch (e) {
                        loadedImg = null;
                    }
                }

                this.drawSpeakerPhoto(loadedImg);

                await this.$nextTick();
                this.updateRasterShadows();
                await new Promise(r => requestAnimationFrame(r));

                const posterElement = document.getElementById('posterExportCard');
                posterElement.style.border = 'none';
                posterElement.style.outline = 'none';
                posterElement.style.boxShadow = 'none';

                // Pastikan gambar logo ter-render sebelum dicapture
                const logoImg = posterElement.querySelector('img[alt="Logo"], img[alt="Logo Resmi"]');
                if (logoImg && !logoImg.complete) {
                    await new Promise((resolve) => {
                        logoImg.onload = resolve;
                        logoImg.onerror = resolve;
                        setTimeout(resolve, 300);
                    });
                }

                const rawCanvas = await h2c(posterElement, {
                    scale: 1.44,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: tpl === 1 ? '#fbfcf9' : '#0264d6',
                    logging: false,
                    width: 500,
                    height: 500,
                    x: 0,
                    y: 0,
                    scrollX: 0,
                    scrollY: 0,
                    imageTimeout: 300,
                    ignoreElements: (node) => {
                        if (node.hasAttribute && node.hasAttribute('data-html2canvas-ignore')) {
                            return true;
                        }
                        if (node.tagName === 'LINK' && node.href && node.href.includes('fonts.googleapis.com')) {
                            return true;
                        }
                        return false;
                    },
                    onclone: (clonedDoc) => {
                        try {
                            if (document.fonts) {
                                for (const font of document.fonts) {
                                    if (font.status === 'loaded') {
                                        clonedDoc.fonts.add(font);
                                    }
                                }
                            }
                        } catch (e) {}
                    }
                });

                // Render ke kanvas akhir 720×720 px
                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = 720;
                finalCanvas.height = 720;
                const fCtx = finalCanvas.getContext('2d');
                fCtx.imageSmoothingEnabled = true;
                fCtx.imageSmoothingQuality = 'high';
                fCtx.drawImage(rawCanvas, 0, 0, 720, 720);

                const safeTitle = (this.state.title || 'Kajian').replace(/[^a-zA-Z0-9_-]/g, '-').replace(/-+/g, '-').slice(0, 35);
                const safeDate = (this.state.date || '').replace(/[^a-zA-Z0-9_-]/g, '-').replace(/-+/g, '-');
                const fileName = `Poster-${safeTitle}-${safeDate}.png`;

                const blob = await new Promise(resolve => finalCanvas.toBlob(resolve, 'image/png'));
                const file = new File([blob], fileName, { type: 'image/png' });
                const blobUrl = URL.createObjectURL(blob);

                this.currentBlobUrl = blobUrl;
                this.currentFile = file;
                this.currentFileName = fileName;

                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || 
                              (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

                if (isIOS) {
                    let shared = false;
                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        try {
                            await navigator.share({
                                files: [file],
                                title: fileName,
                                text: `Poster ${this.state.title}`
                            });
                            shared = true;
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: { message: `Poster ${fileName} siap disimpan!` }
                            }));
                        } catch (shareErr) {
                            if (shareErr.name === 'AbortError') {
                                return;
                            }
                        }
                    }

                    if (!shared) {
                        this.showIosModal = true;
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: 'Poster siap disimpan! Pilih "Simpan ke Galeri" atau tekan lama gambar.' }
                        }));
                    }
                } else {
                    const link = document.createElement('a');
                    link.download = fileName;
                    link.href = blobUrl;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: `Poster ${fileName} berhasil diunduh (720×720 px)!` }
                    }));
                }
            } catch (err) {
                console.error('Poster export error:', err);
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Gagal membuat poster: ' + err.message }
                }));
            } finally {
                this.isGenerating = false;
            }
        }
    };
};

if (window.Alpine) {
    window.Alpine.data('posterExporter', (cfg) => window.posterExporter(cfg));
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('posterExporter', (cfg) => window.posterExporter(cfg));
    });
}
document.addEventListener('livewire:init', () => {
    if (window.Alpine) {
        window.Alpine.data('posterExporter', (cfg) => window.posterExporter(cfg));
    }
});

window.SAMPLE_NOTULA_TEMPLATE = `# Notula Kajian Tematik

**Tema:** Karakteristik Keimanan yang Kokoh (*Al-Mu'minun*) dalam Al-Qur'an

**Fokus Bahasan:** Analisis Semantik Kebahasaan dan Tadabbur 4 Ayat Berawalan *“Innamal-Mu’minun”*

---

## 1. Landasan Semantik: Pembedaan *Alladzina Âmanû* vs *Al-Mu'minûn*

Dalam Al-Qur'an, Allah SWT menggunakan dua bentuk redaksi utama ketika merujuk kepada orang-orang yang beriman, yang memiliki implikasi makna gramatikal (*dilâlah nahwiyyah*)[^1] berbeda:

* **Bentuk Kata Kerja / *Fi'il* (*Alladzina Âmanû* / الَّذِينَ آمَنُوا):**
* Secara kaidah bahasa Arab, kata kerja (*fi'il*) terikat oleh dimensi waktu (*zaman*) dan menunjukkan proses pembaharuan (*tajaddud*) serta perubahan (*huduts*)[^2].
* Implikasi maknanya bersifat dinamis, bertingkat, dan belum sepenuhnya kokoh/stabil (temporer).
* Panggilan *“Yâ ayyuhalladzîna âmanû”* umumnya diiringi dengan perintah, larangan, atau dorongan untuk meningkatkan mutu ibadah, seperti perintah berpuasa (QS. Al-Baqarah: 183)[^3], menjaga keluarga dari api neraka (QS. At-Tahrim: 6)[^3], atau perintah untuk terus memperbarui iman (QS. An-Nisa: 136)[^3], karena keimanan mereka masih dalam proses pembinaan.


* **Bentuk Kata Benda / *Isim* (*Al-Mu'minûn* / الْمُؤْمِنُونَ):**
* Bentuk kata benda (*isim*, khususnya *isim fa'il*) menunjukkan ketetapan (*tsubut*) dan kesinambungan (*dawam*)[^2] yang tidak terikat batas waktu.
* Mengindikasikan keimanan yang telah terhunjam mantap, stabil, kokoh, dan paripurna.
* Penyebutan *Al-Mu'minun* sering diiringi dengan pujian atau jaminan keberuntungan dari Allah SWT, seperti dalam QS. Al-Mu'minun: 1 (*Qad aflahal-mu'minûn* – "Sungguh beruntung orang-orang yang beriman").

---

## 2. Empat Ayat Al-Qur'an Berawalan *“Innamal-Mu’minûn”*

Kata *Innama* (إِنَّمَا) dalam kaidah balaghah berfungsi sebagai *adatul qashr/hashr* (perangkat pembatasan)[^4], yang menegaskan bahwa sifat-sifat berikut merupakan tolok ukur esensial dari keimanan yang kokoh.

### Ayat I: Karakter Hati, Intelektual, dan Tawakal (QS. Al-Anfal: 2)

> إِنَّمَا الْمُؤْمِنُونَ الَّذِينَ إِذَا ذُكِرَ اللَّهُ وَجِلَتْ قُلُوبُهُمْ وَإِذَا تُلِيَتْ عَلَيْهِمْ آيَاتُهُ زَادَتْهُمْ إِيمَانًا وَعَلَىٰ رَبِّهِمْ يَتَوَكَّلُونَ
> *"Sesungguhnya orang-orang yang beriman adalah mereka yang apabila disebut nama Allah gemetarlah hati mereka, dan apabila dibacakan ayat-ayat-Nya kepada mereka, bertambahlah iman mereka, dan hanya kepada Tuhan mereka bertawakal."*

Ayat ini merangkum tiga pilar keimanan yang utuh:

1. **Dimensi Emosional/Hati (*Wajilat Qulûbuhum*):**
* Makna *wajilat*: Rasa gentar atau takut yang mendalam—seperti sensasi bergidik saat berdiri di tepi jurang/sumur yang dalam—yang mendorong seseorang untuk segera tunduk, melaksanakan perintah, dan meninggalkan larangan.
* Ketika diingatkan tentang Allah atau aturan syariat, hatinya langsung terkoneksi (*attachment*), melembut, dan tunduk (*sami'na wa atha'na*).


2. **Dimensi Intelektual dan Tindakan (*Zâdathum Îmânâ*):**
* Keimanan dipelihara melalui kedekatan dengan Al-Qur'an.
* Makna *tilawah* (*tala - yatlu*): Tidak sekadar membaca lafalnya, tetapi *to follow* (mengikuti, memahami, dan mempraktikkan kandungannya).
* Kutipan Syekh Ahmad Ar-Rifa'i: Tidak ada kebaikan sejati bagi orang yang sibuk dengan beragam wirid/zikir namun melalaikan interaksi dan pengkajian terhadap Al-Qur'an.


3. **Dimensi Sikap Hidup (*Wa 'alâ Rabbihim Yatawakkalûn*):**
* Tawakal lahir setelah adanya pemahaman ilmu dan usaha/ikhtiar yang maksimal. Ketika ikhtiar manusiawi telah mencapai batasnya, orang beriman menyerahkan hasil sepenuhnya kepada ketetapan Allah.

---

### Ayat II: Loyalitas dan Kepedulian Komunal (QS. An-Nur: 62)

> إِنَّمَا الْمُؤْمِنُونَ الَّذِينَ آمَنُوا بِاللَّهِ وَرَسُولِهِ وَإِذَا كَانُوا مَعَهُ عَلَىٰ أَمْرٍ جَامِعٍ لَّمْ يَذْهَبُوا حَتَّىٰ يَسْتَأْذِنُوهُ...
> *"Sesungguhnya yang dinamakan orang mukmin sejati adalah orang-orang yang beriman kepada Allah dan Rasul-Nya, dan apabila mereka berada bersama-sama Rasulullah dalam suatu urusan yang memerlukan kebersamaan, mereka tidak meninggalkan (Rasulullah) sebelum meminta izin kepadanya..."*

* **Konteks Historis:** Turun terkait peristiwa Perang Khandaq (Ahzab). Ketika kaum muslimin menghadapi paceklik parah dan ancaman 10.000 pasukan sekutu Quraisy, Nabi ﷺ dan sahabat menggali parit pertahanan (*khandaq*) sepanjang sekitar 5 km. Di tengah situasi berat ini, orang-orang munafik mencari dalih remeh untuk menyelinap pulang tanpa izin.
* **Aplikasi Kontemporer:** Orang beriman yang kokoh tidak bersikap individualis. Mereka memiliki komitmen terhadap urusan publik, kemaslahatan umat, serta kegiatan kemasyarakatan (seperti kepedulian lingkungan warga/RT/RW). Kontribusi diberikan sesuai kapasitas: kehadiran tenaga, gagasan, maupun dukungan finansial/logistik.

---

### Ayat III: Persaudaraan Universal dan Rekonsiliasi (QS. Al-Hujurat: 10)

> إِنَّمَا الْمُؤْمِنُونَ إِخْوَةٌ فَأَصْلِحُوا بَيْنَ أَخَوَيْكُمْ وَاتَّقُوا اللَّهَ لَعَلَّكُمْ تُرْحَمُونَ
> *"Sesungguhnya orang-orang mukmin itu bersaudara, karena itu damaikanlah antara kedua saudaramu (yang berselisih) dan bertakwalah kepada Allah agar kamu mendapat rahmat."*

* **Persaudaraan Hakiki (*Ukhuwwah*):** Memandang sesama muslim sebagai saudara kandung melintasi batas organisasi, afiliasi mazhab, suku, maupun bendera politik.
* **Semangat *Ishlah* (Rekonsiliasi):** Jika terjadi perselisihan antar-elemen umat, perannya adalah mendamaikan dan memadamkan konflik, bukan memperbesar friksi atau menyiramkan minyak ke dalam api perselisihan.
* **Kedewasaan Bermazhab:** Perbedaan pandangan pada ranah cabang (*furu'iyyah*)[^5] dalam fikih adalah hal wajar yang berlandaskan dalil ijtihad, bukan alasan perpecahan.

---

### Ayat IV: Keyakinan Bulat dan Totalitas Pengorbanan (QS. Al-Hujurat: 15)

> إِنَّمَا الْمُؤْمِنُونَ الَّذِينَ آمَنُوا بِاللَّهِ وَرَسُولِهِ ثُمَّ لَمْ يَرْتَابُوا وَجَاهَدُوا بِأَمْوَالِهِمْ وَأَنفُسِهِمْ فِي سَبِيلِ اللَّهِ ۚ أُولَٰئِكَ هُمُ الصَّادِقُونَ
> *"Sesungguhnya orang-orang mukmin yang sebenarnya adalah mereka yang beriman kepada Allah dan Rasul-Nya kemudian mereka tidak ragu-ragu, dan mereka berjihad dengan harta dan jiwanya di jalan Allah. Mereka itulah orang-orang yang benar (kejujuran imannya)."*

* **Keyakinan Tanpa Ragu (*Tsumma Lam Yartâbû*):** Kemantapan hati yang bulat terhadap ketetapan Allah dan syariat-Nya, tanpa ada skeptisisme batin.
* **Totalitas Perjuangan (*Wa Jâhadû bi Amwâlihim wa Anfusihim*):**
* **Harta:** Tidak bakhil/kikir. Bersegera menunaikan zakat, infak, dan sedekah, meneladani Rasulullah ﷺ dan para sahabat dermawan (seperti Abu Bakar, Umar, dan Abdurrahman bin Auf).
* **Jiwa/Diri:** Mengerahkan waktu, tenaga, keahlian, dan pengorbanan jiwa untuk kemaslahatan dinul Islam.

---

## 3. Dalil Pendukung dan Kisah Hikmah

Karakteristik keimanan yang kokoh tercermin secara nyata dalam jejak sejarah generasi awal umat Islam melalui ketundukan mutlak, kepasrahan, dan pengendalian diri:

* **Perselisihan Ghanimah pada Perang Badar (QS. Al-Anfal: 1–2):**
Pasukan muslim terbagi ke dalam tiga kelompok: kelompok yang memproteksi Rasulullah ﷺ, kelompok yang mengejar sisa musuh, dan kelompok yang mengumpulkan rampasan perang (*ghanimah*). Usai pertempuran, terjadi silang pendapat mengenai pihak yang paling berhak atas harta tersebut, bahkan Sa'ad bin Abi Waqqas sempat meminta pedang tertentu. Allah SWT menegur kecenderungan ini melalui turunnya ayat pertama surah Al-Anfal yang menegaskan bahwa ghanimah adalah milik Allah dan Rasul-Nya. Seketika ayat kedua dibacakan—mengingatkan sifat orang beriman yang gemetar hatinya saat disebut nama Allah—seluruh sahabat menundukkan ego masing-masing dan patuh total pada keputusan syariat.
* **Ketundukan Zainab binti Jahsy dalam Perjodohan (QS. Al-Ahzab: 36)[^6]:**
Ketika Rasulullah ﷺ melamar Zainab binti Jahsy—seorang wanita bangsawan Quraisy terpandang—untuk dinikahkan dengan Zaid bin Haritsah yang merupakan mantan budak, Zainab dan keluarganya sempat menolak karena memandang tidak adanya kesetaraan (*kufu'*) dari segi nasab maupun kekayaan. Namun, begitu turun QS. Al-Ahzab: 36 yang menegaskan bahwa seorang mukmin laki-laki maupun perempuan tidak memiliki pilihan lain apabila Allah dan Rasul-Nya telah menetapkan suatu urusan, Zainab seketika bersikap *taslim* (tunduk sepenuhnya) dan menerima pernikahan tersebut demi mencari keridaan Allah dan Rasul-Nya.
* **Pernikahan Sahabat Julaibib radhiyallahu 'anhu (QS. Al-Ahzab: 36)[^6]:**
Julaibib r.a. adalah seorang sahabat miskin dan bertampang kurang rupawan. Ketika Rasulullah ﷺ meminang seorang gadis putri sahabat Anshar untuknya, orang tua gadis tersebut sempat berkeberatan. Namun, sang putri mendengar perdebatan tersebut dan langsung menyadarkan orang tuanya dengan membacakan QS. Al-Ahzab: 36. Ia menyatakan ketundukannya pada arahan Rasulullah ﷺ. Sikap beriman yang tulus ini disambut oleh doa Rasulullah ﷺ yang memohon agar hidupnya dicurahi limpahan rezeki. Setelah Julaibib gugur sebagai syahid dalam peperangan, sang istri menjadi salah satu wanita yang paling berkah, berkecukupan, dan dihormati di Madinah.
* **Sikap Khalifah Umar bin Khattab Meredam Amarah (QS. Al-A'raf: 199)[^7]:**
Sebagai pemimpin tertinggi, Umar bin Khattab r.a. selalu mengangkat para penghafal dan pengkaji Al-Qur'an (*ahlul Qur'an*) sebagai penasihat spiritual dan kenegaraan. Ketika salah seorang warga[^8] melontarkan tuduhan pedas bahwa Umar tidak berlaku adil, amarah Umar memuncak hingga raut wajahnya memerah. Seketika itu pula penasihatnya, Al-Hurr bin Qais[^9], membacakan QS. Al-A'raf: 199 yang memerintahkan sikap pemaaf, menegakkan kebaikan, dan berpaling dari orang-orang yang bodoh (*jahilin*). Mendengar ayat Allah dibacakan, amarah Umar padam seketika; ia berhenti dan menahan diri karena hatinya senantiasa tunduk di hadapan Kitabullah.
* **Kepasrahan Total Ibunda Nabi Musa 'alaihissalam (QS. Al-Qasas: 7)[^10]:**
Di tengah ancaman tentara Firaun yang menyisir dan membantai setiap bayi laki-laki Bani Israil, ibu Nabi Musa berada di titik buntu daya manusiawi. Melalui ilham Ilahi, ia diperintahkan menyusui Musa lalu menghanyutkannya ke Sungai Nil dalam sebuah peti. Secara logika naluriah seorang ibu, melepaskan bayi ke sungai deras tampak berbahaya. Namun atas dasar tawakal sejati—menyerahkan hasil kepada Allah setelah menjalankan petunjuk-Nya—Allah mengembalikan Musa ke pangkuan ibunya sendiri di bawah pemeliharaan istana Firaun dan perlindungan kasih sayang Ilahi.

---

## 4. Studi Kasus: Sikap Lapang Dada Terhadap Khilafiyah Fikih

Perwujudan nyata dari semangat persaudaraan dalam QS. Al-Hujurat: 10 adalah bagaimana seorang mukmin bersikap dewasa terhadap keragaman furu'iyah (cabang syariat) tanpa terjebak pada fanatisme kelompok:

* **Status dan Waktu Niat dalam Salat:**
Mazhab Syafi'i memposisikan niat sebagai rukun salat yang esensial, sehingga niat wajib dihadirkan di dalam hati secara bersamaan (*muqaranah*)[^11] saat lisan melafalkan takbiratul ihram (*Allahu Akbar*). Karena menuntut ketelitian menghadirkan niat bersamaan dengan takbir, lafal takbir pada penganut mazhab ini kerap terdengar dipanjangkan. Sebaliknya, Mazhab Hanafi dan Hanbali menempatkan niat sebagai syarat sah salat (sebagaimana halnya kesucian tempat dan wudhu), yang mana niat dinilai cukup dihadirkan sesaat sebelum salat ditunaikan. Perbedaan landasan hukum ini membuat pelaksanaan takbir pada Mazhab Hanbali atau Hanafi lebih ringkas, tanpa keharusan menahan lafal takbir untuk merangkai lintasan niat di dalam dada.
* **Posisi Tangan saat Bersedekap:**
Keragaman tata cara bersedekap sepenuhnya bersumber dari variasi sudut pandang para sahabat Nabi saat meriwayatkan postur salat Rasulullah ﷺ. Mazhab Syafi'i menetapkan posisi sedekap di antara dada dan pusar dengan sedikit condong ke sisi kiri dada. Mazhab Hanafi dan Hanbali menempatkan posisi tangan tepat di pusar atau di bawah pusar. Sementara itu, Mazhab Maliki yang dominan di kawasan Afrika Utara (seperti Maroko) masyhur dengan pendapat *sadl*[^12], yakni meluruskan kedua tangan ke bawah tanpa bersedekap.
* **Prinsip Intelektual Menghadapi Perbedaan:**
Seluruh variasi praktik fikih di atas memiliki landasan sanad dan ijtihad yang sah dari para imam mujtahid. Kecenderungan menyalahkan orang lain atau merasa paling benar sendiri kerap timbul akibat kedangkalan wawasan keilmuan. Selama perbedaan tersebut berada pada ranah *ijtihadiyah furu'iyyah* dan bukan penyimpangan pokok akidah, seorang mukmin sejati wajib mengedepankan sikap *tasamuh* (toleran)[^13], merawat ukhuwah, dan tidak menjadikan perbedaan tata cara ibadah sebagai bahan perpecahan umat.

---

## 5. Ringkasan Karakter Mukmin Sejati

Seorang hamba mencapai derajat keimanan yang kokoh (*Al-Mu'minun*) manakala memiliki keterpaduan sifat:

1. **Sensitivitas Spiritual:** Hatinya bergetar (*wajal*) dan tunduk seketika saat diingatkan akan asma dan hukum Allah.
2. **Koneksi Al-Qur'an:** Senantiasa mengkaji, mentadaburi, serta mengamalkan ayat-ayat Al-Qur'an dalam kehidupan nyata.
3. **Tawakal Proporsional:** Menyandarkan seluruh hasil kepada ketetapan Allah setelah memaksimalkan usaha.
4. **Solidaritas Sosial:** Memiliki loyalitas dan tanggung jawab terhadap urusan kebersamaan masyarakat/jamaah.
5. **Semangat Rekonsiliasi:** Memandang setiap muslim sebagai saudara dan proaktif merajut persatuan.
6. **Kemantapan Tekad & Dermawan:** Bebas dari keraguan hati serta ringan tangan mengorbankan harta dan tenaga di jalan Allah.

---

### Catatan Kaki (Footnotes): Informasi Tambahan di Luar Transkrip

[^1]: **Istilah Ilmiah Gramatika:** Istilah *dilâlah nahwiyyah* (implikasi makna sintaksis) ditambahkan sebagai istilah teknis kebahasaan untuk merangkum penjelasan lisan penceramah mengenai pengaruh jenis kata terhadap pergeseran makna.
[^2]: **Kaidah Balaghah & Nahwu:** Istilah *tajaddud* & *huduts* (karakter kata kerja yang menunjukkan pembaruan dan perubahan temporal) serta *tsubut* & *dawam* (karakter kata benda yang menunjukkan ketetapan permanen) ditambahkan untuk membakukan penjelasan penceramah mengenai analogi *"I love you"* (kata kerja/temporer) versus *"You are my love"* (kata benda/permanen).
[^3]: **Identifikasi Referensi Al-Qur'an:** Penceramah hanya melafalkan potongan ayat lisan (*"kutiba 'alaikumus siyam"*, *"qu anfusakum wa ahlikum nara"*, dan *"aminu"*). Nama surah dan nomor ayat (QS. Al-Baqarah: 183, QS. At-Tahrim: 6, QS. An-Nisa: 136) diidentifikasi dan ditambahkan secara mandiri untuk ketepatan rujukan.
[^4]: **Istilah Balaghah:** Istilah *adatul qashr/hashr* ditambahkan untuk mendefinisikan fungsi partikel *Innama* (إِنَّمَا) dalam tata bahasa Arab baku.
[^5]: **Istilah Fikih:** Frasa *furu'iyyah* ditambahkan untuk mempertegas klasifikasi ranah hukum cabang non-akidah.
[^6]: **Identifikasi Referensi Al-Qur'an:** Penceramah hanya melafalkan potongan ayat (*"wa ma kana limu'minin wa la mu'minatin idza qadhallahu..."*) dan menyebutnya berasal dari surah Al-Ahzab tanpa menyebutkan nomor ayat (QS. Al-Ahzab: 36).
[^7]: **Identifikasi Referensi Al-Qur'an:** Penceramah melafalkan potongan ayat (*"khudzil 'afwa wa'mur bil 'urfi..."*) tanpa menyebut nama surah dan nomor ayatnya (QS. Al-A'raf: 199).
[^8]: **Identifikasi Tokoh Historis:** Dalam transkrip hanya disebut *"pamanku ini"* atau *"orang yang diserang"*. Berdasarkan riwayat asbabun nuzul (HR. Bukhari no. 4642), pria yang mencela Umar r.a. tersebut adalah Uyainah bin Hishn Al-Fazari.
[^9]: **Identifikasi Tokoh Historis:** Penceramah hanya menyebut *"ponaan yang ahli Quran segera berdiri"*. Identitas keponakan tersebut adalah sahabat Al-Hurr bin Qais r.a. (sebagaimana termaktub dalam Shahih al-Bukhari no. 4642).
[^10]: **Identifikasi Referensi Al-Qur'an:** Penceramah menceritakan kisah ilham kepada ibunda Nabi Musa a.s. secara naratif; rujukan ayat (QS. Al-Qasas: 7) ditambahkan sebagai dasar tekstual Al-Qur'an.
[^11]: **Istilah Teknis Fikih:** Istilah *muqaranah* (kebersamaan antara niat di dalam hati dan ucapan takbiratul ihram) ditambahkan untuk melengkapi penjelasan penceramah mengenai alasan warga NU memanjangkan takbir.
[^12]: **Istilah Teknis Fikih:** Istilah *sadl* (posisi meluruskan tangan ke bawah tanpa sedekap) ditambahkan untuk membakukan deskripsi penceramah (*"lurus ngenten tok"*).
[^13]: **Istilah Sikap:** Kata *tasamuh* dan *ijtihadiyah* ditambahkan untuk memformalkan pesan penceramah mengenai sikap toleran dan keterbukaan dalam menghadapi variasi furu'iyah.
`;


// ==========================================
// Notula Kajian Intelligent Parser & Print Engine
// ==========================================
window.parseNotulaToHtml = (rawText, meta = {}) => {
    if (!rawText || !rawText.trim()) {
        return `<div class="p-12 text-center text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <p class="font-medium text-slate-600">Belum ada naskah notula yang diinput.</p>
            <p class="text-xs text-slate-400 mt-1">Silakan beralih ke tab <strong>Tulis / Edit Notula</strong> untuk menyalin atau menulis isi notula kajian.</p>
        </div>`;
    }

    const lines = rawText.replace(/\r\n/g, '\n').split('\n');

    // 1. Extract Footnotes ([^1]: content)
    const footnotes = [];
    const filteredLines = [];

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        const fnMatch = line.match(/^\[\^(\d+)\]:\s*(.*)/);
        if (fnMatch) {
            let fnNum = fnMatch[1];
            let fnContent = fnMatch[2];
            while (i + 1 < lines.length && lines[i + 1].trim() && !lines[i + 1].match(/^\[\^(\d+)\]:/) && !lines[i + 1].match(/^##/)) {
                i++;
                fnContent += ' ' + lines[i].trim();
            }
            footnotes.push({ id: fnNum, text: fnContent });
        } else {
            if (line.match(/^###?\s*Catatan Kaki/i)) {
                // skip heading
            } else {
                filteredLines.push(line);
            }
        }
    }

    const isArabic = (text) => /[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF]/.test(text);

    const formatInline = (text) => {
        if (!text) return '';
        let out = text;
        // Footnote references [^1]
        out = out.replace(/\[\^(\d+)\]/g, '<sup><a href="#fn$1" class="text-gov-600 hover:text-gov-800 font-semibold">[$1]</a></sup>');
        // Bold
        out = out.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Italic
        out = out.replace(/\*(.*?)\*/g, '<em>$1</em>');
        // Quranic Reference in parenthesis: (QS. Al-Anfal: 2) -> format tulisan bold biasa, tanpa border, font asli dokumen
        out = out.replace(/\((Q\.?S\.?[^\)]+)\)/g, '<strong class="font-bold">($1)</strong>');
        // Arabic span wrapper if contains Arabic characters
        out = out.replace(/([\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF\u064B-\u065F\u0670\s]+)/g, (match) => {
            if (match.trim().length > 1) {
                return `<span class="font-arabic font-normal text-gov-800" dir="rtl">${match}</span>`;
            }
            return match;
        });
        return out;
    };

    // 2. Extract Document Header / Metadata
    let docType = meta.type === 'tematik' ? 'Dokumen Kajian Tafsir Tematik Al-Qur\'an' : (meta.type === 'jumat' ? 'Dokumen Risalah Shalat Jumat' : 'Dokumen Kajian Pekanan Rutin');
    let docTitle = meta.title || 'Notula Kajian';
    let docFocus = '';
    let docSpeaker = meta.speaker_name || '';
    let docDate = meta.date || '';

    let contentStartIndex = 0;

    for (let i = 0; i < Math.min(filteredLines.length, 12); i++) {
        const line = filteredLines[i].trim();
        if (line.startsWith('# ')) {
            const h1 = line.replace(/^#\s+/, '').trim();
            if (h1.toLowerCase().includes('tematik')) {
                docType = 'Dokumen Kajian Tafsir Tematik Al-Qur\'an';
            } else if (h1.toLowerCase().includes('pekanan')) {
                docType = 'Dokumen Risalah Kajian Pekanan Rutin';
            }
            contentStartIndex = Math.max(contentStartIndex, i + 1);
        } else if (line.match(/^\*\*Tema:\*\*\s*(.*)/i)) {
            const match = line.match(/^\*\*Tema:\*\*\s*(.*)/i);
            docTitle = match[1].trim();
            contentStartIndex = Math.max(contentStartIndex, i + 1);
        } else if (line.match(/^\*\*Fokus Bahasan:\*\*\s*(.*)/i)) {
            const match = line.match(/^\*\*Fokus Bahasan:\*\*\s*(.*)/i);
            docFocus = match[1].trim();
            contentStartIndex = Math.max(contentStartIndex, i + 1);
        } else if (line.match(/^\*\*Pemateri:\*\*\s*(.*)/i)) {
            const match = line.match(/^\*\*Pemateri:\*\*\s*(.*)/i);
            docSpeaker = match[1].trim();
            contentStartIndex = Math.max(contentStartIndex, i + 1);
        } else if (line === '---') {
            contentStartIndex = Math.max(contentStartIndex, i + 1);
            break;
        }
    }

    // Build Kop / Document Header HTML
    let html = `
      <!-- Dokumen Header / Kop Identitas Bersih & Formal -->
      <div class="border-b border-slate-200 pb-5 mb-2">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gov-50 border border-gov-200 text-gov-800 text-xs font-semibold uppercase tracking-wider mb-2.5">
          <svg class="w-3.5 h-3.5 text-gov-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1"/><path d="M19 4h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-2"/><path d="M15 2v4"/><path d="M15 8v2"/><path d="M15 14v4"/><path d="M15 20v2"/></svg>
          <span>${formatInline(docType)}</span>
        </div>
        <h1 class="text-xl sm:text-3xl font-extrabold text-gov-950 tracking-tight leading-snug">
          ${formatInline(docTitle)}
        </h1>
        <div class="text-xs sm:text-sm text-slate-500 font-medium mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5">
          ${docFocus ? `
            <span class="flex items-center gap-1.5 text-gov-800 bg-gov-50/80 px-2.5 py-1 rounded-lg border border-gov-100">
              <svg class="w-3.5 h-3.5 text-gov-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
              <span><strong>Fokus:</strong> ${formatInline(docFocus)}</span>
            </span>
          ` : ''}
          ${docSpeaker ? `
            <span class="flex items-center gap-1.5 text-slate-600">
              <svg class="w-3.5 h-3.5 text-gov-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <span>Narasumber: <strong>${docSpeaker}</strong></span>
            </span>
          ` : ''}
          ${docDate ? `
            <span class="flex items-center gap-1.5 text-slate-600">
              <svg class="w-3.5 h-3.5 text-gov-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
              <span>${docDate}</span>
            </span>
          ` : ''}
        </div>
      </div>
    `;

    // 3. Process content lines
    const bodyLines = filteredLines.slice(contentStartIndex);
    let inSection = false;
    let inOrderedList = false;
    let inBlockquote = false;
    let blockquoteArabic = [];
    let blockquoteTranslation = [];

    const flushBlockquote = () => {
        if (!inBlockquote) return '';
        inBlockquote = false;
        const arText = blockquoteArabic.join(' ');
        const trText = blockquoteTranslation.join(' ');
        blockquoteArabic = [];
        blockquoteTranslation = [];

        let out = `<div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 text-right space-y-2.5 my-3 shadow-2xs">`;
        if (arText) {
            out += `<p class="font-arabic text-xl sm:text-2xl leading-loose text-slate-900 font-medium" dir="rtl">${arText}</p>`;
        }
        if (trText) {
            out += `<p class="text-xs sm:text-sm text-slate-600 italic text-justify pt-2 border-t border-slate-100 font-sans leading-relaxed">${formatInline(trText)}</p>`;
        }
        out += `</div>`;
        return out;
    };

    const flushOrderedList = () => {
        if (!inOrderedList) return '';
        inOrderedList = false;
        return `</ol>`;
    };

    const closeSection = () => {
        let out = '';
        out += flushBlockquote();
        out += flushOrderedList();
        if (inSection) {
            out += `</section>`;
            inSection = false;
        }
        return out;
    };

    for (let i = 0; i < bodyLines.length; i++) {
        let line = bodyLines[i].trim();

        if (!line) {
            html += flushBlockquote();
            continue;
        }

        if (line === '---' || line.startsWith('---')) {
            html += flushBlockquote();
            html += flushOrderedList();
            continue;
        }

        // Section Heading: ## 1. Title or ## Title
        const sectionMatch = line.match(/^##\s+(\d+[\.\)]?)?\s*(.*)/);
        if (sectionMatch) {
            html += closeSection();
            inSection = true;
            const secNum = sectionMatch[1] ? sectionMatch[1].replace(/[\.\)]/, '') : '';
            const secTitle = sectionMatch[2] ? sectionMatch[2].trim() : '';

            html += `
              <section class="space-y-4 page-break-avoid pt-2">
                <div class="flex items-center gap-2.5 text-gov-900 border-b border-slate-200/80 pb-2">
                  ${secNum ? `<span class="flex items-center justify-center w-7 h-7 rounded-lg bg-gov-800 text-white font-bold text-xs shadow-xs shrink-0">${secNum}</span>` : ''}
                  <h2 class="text-lg sm:text-xl font-bold tracking-tight text-gov-950">
                    ${formatInline(secTitle)}
                  </h2>
                </div>
            `;
            continue;
        }

        // Subsections: ### Ayat I: ... or ### Title
        const subSecMatch = line.match(/^###\s*(.*)/);
        if (subSecMatch) {
            html += flushBlockquote();
            html += flushOrderedList();

            let titleStr = subSecMatch[1].trim();

            html += `
              <div class="flex items-center justify-between flex-wrap gap-2 pt-3 pb-1">
                <h3 class="font-bold text-gov-900 text-sm sm:text-base flex items-center gap-2">
                  <span class="w-2.5 h-2.5 rounded-full bg-gov-700 shrink-0"></span>
                  <span>${formatInline(titleStr)}</span>
                </h3>
              </div>
            `;
            continue;
        }

        // Blockquote (Ayat / Quote)
        if (line.startsWith('>')) {
            inBlockquote = true;
            const quoteContent = line.replace(/^>\s*/, '').trim();
            if (isArabic(quoteContent)) {
                blockquoteArabic.push(quoteContent);
            } else {
                blockquoteTranslation.push(quoteContent);
            }
            continue;
        } else {
            if (inBlockquote) {
                html += flushBlockquote();
            }
        }

        // Ordered List: 1. **Title:** text
        const olMatch = line.match(/^(\d+)\.\s+(.*)/);
        if (olMatch) {
            if (!inOrderedList) {
                inOrderedList = true;
                html += `<ol class="space-y-3 pt-1 list-none pl-0">`;
            }
            const itemNum = olMatch[1];
            const itemText = olMatch[2];
            html += `
              <li class="flex items-start gap-3.5 bg-slate-50/70 hover:bg-slate-50 p-4 rounded-xl border border-slate-200 transition-all">
                <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gov-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">${itemNum}</span>
                <div class="text-sm sm:text-base leading-relaxed text-slate-700">
                  ${formatInline(itemText)}
                </div>
              </li>
            `;
            continue;
        } else {
            if (inOrderedList) {
                html += flushOrderedList();
            }
        }

        // Bullet point cards with bold header: * **Title:** text
        const bpHeaderMatch = line.match(/^[\*\-]\s+\*\*(.*?)\*\*:?\s*(.*)/);
        if (bpHeaderMatch) {
            let itemHeader = bpHeaderMatch[1];
            let restOfText = bpHeaderMatch[2];

            let bodyParas = [];
            if (restOfText) bodyParas.push(restOfText);

            while (i + 1 < bodyLines.length) {
                const nextLine = bodyLines[i + 1].trim();
                if (!nextLine) {
                    i++;
                    continue;
                }
                if (nextLine.startsWith('##') || nextLine.startsWith('###') || nextLine.startsWith('>') || nextLine.match(/^\d+\./) || nextLine.match(/^[\*\-]\s+\*\*/) || nextLine.startsWith('---')) {
                    break;
                }
                const cleanNext = nextLine.replace(/^[\*\-]\s*/, '').trim();
                if (cleanNext && cleanNext !== '---') {
                    bodyParas.push(cleanNext);
                }
                i++;
            }

            html += `
              <div class="p-4 sm:p-5 rounded-xl border border-slate-200 bg-slate-50/70 space-y-2.5 my-2">
                <div class="border-b border-slate-200/80 pb-2">
                  <span class="font-bold text-gov-950 text-sm sm:text-base">${formatInline(itemHeader)}</span>
                </div>
                ${bodyParas.map(p => `<p class="text-sm text-justify leading-relaxed text-slate-700">${formatInline(p)}</p>`).join('')}
              </div>
            `;
            continue;
        }

        // Normal bullet points (* text)
        if (line.match(/^[\*\-]\s+(.*)/)) {
            const bpMatch = line.match(/^[\*\-]\s+(.*)/);
            html += `
              <div class="flex items-start gap-2.5 text-sm leading-relaxed text-slate-700 pl-2 my-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-gov-700 mt-2 shrink-0"></span>
                <div class="text-justify">${formatInline(bpMatch[1])}</div>
              </div>
            `;
            continue;
        }

        // Regular paragraph
        html += `<p class="text-justify leading-relaxed text-slate-700 text-sm sm:text-base">${formatInline(line)}</p>`;
    }

    html += closeSection();

    // 4. Render Footnotes Section if any
    if (footnotes.length > 0) {
        html += `
          <!-- Catatan Kaki (Footnotes) -->
          <footer class="pt-6 border-t border-slate-300/80 space-y-3 page-break-avoid mt-6">
            <div class="flex items-center gap-2 text-slate-800 font-bold text-sm tracking-wide uppercase">
              <svg class="w-4 h-4 text-gov-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
              <span>Catatan Kaki (Footnotes) — Informasi Penjelas Dokumen</span>
            </div>
            <div class="text-xs text-slate-600 space-y-2.5 leading-relaxed bg-slate-50 p-4 sm:p-5 rounded-xl border border-slate-200">
              ${footnotes.map(fn => `
                <p id="fn${fn.id}">
                  <strong>[${fn.id}]</strong> ${formatInline(fn.text)}
                </p>
              `).join('')}
            </div>
          </footer>
        `;
    }

    return html;
};

window.printNotulaDocument = (renderedHtml, title = 'Notula Kajian') => {
    const printWindow = window.open('', '_blank');
    if (!printWindow) {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { message: 'Gagal membuka jendela cetak. Pastikan izin pop-up browser telah aktif.', type: 'error' }
        }));
        return;
    }

    const docContent = `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>${title} — Masjid Salahuddin</title>
  
  <script src="https://cdn.tailwindcss.com"><\/script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
            arabic: ['"Amiri"', 'serif']
          },
          colors: {
            gov: {
              50: '#f0f4f9',
              100: '#dde6f2',
              200: '#c0d0e6',
              300: '#94b2d5',
              600: '#1f4e79',
              700: '#173d61',
              800: '#133250',
              900: '#0f243a',
              950: '#0a1726'
            },
            navy: {
              800: '#111c34',
              900: '#0d1527',
              950: '#080d19'
            }
          }
        }
      }
    };
  <\/script>

  <style>
    @page {
      size: A4 portrait;
      margin: 12mm 15mm 15mm 15mm;
    }
    @media print {
      .no-print {
        display: none !important;
      }
      body {
        background: white !important;
        color: #0f172a !important;
        padding: 0 !important;
      }
      .print-shadow-none {
        box-shadow: none !important;
        border: 1px solid #cbd5e1 !important;
      }
      .page-break-avoid {
        break-inside: avoid;
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body class="bg-slate-100/90 text-slate-800 font-sans antialiased min-h-screen flex flex-col items-center p-4 sm:p-8">
  <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200 p-6 sm:p-10 space-y-7 text-slate-700 leading-relaxed text-base print-shadow-none">
    ${renderedHtml}
  </div>
  <script>
    window.addEventListener('load', () => {
      setTimeout(() => {
        window.print();
      }, 500);
    });
  <\/script>
</body>
</html>`;

    printWindow.document.open();
    printWindow.document.write(docContent);
    printWindow.document.close();
};



