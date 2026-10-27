<div data-html2canvas-ignore="true"
    class="min-h-screen flex flex-col bg-gov-canvas text-gov-textMain selection:bg-gov-navy selection:text-white"
    @scroll.window.passive="isScrolled = (window.pageYOffset || document.documentElement.scrollTop || window.scrollY || 0) > 0"
    x-data="{
         sidebarDrawerOpen: window.innerWidth >= 768,
         isScrolled: false,
         activeTab: '{{ $currentTab }}',
         kegiatanSubTab: '{{ $kegiatanSubTab }}',
         kajianSubTab: '{{ $kajianSubTab }}',
         agendaSubTab: '{{ $agendaSubTab }}',
         userSubTab: '{{ $userSubTab }}',
         settingsSubTab: '{{ $settingsSubTab }}',
         init() {
             this.updateDocumentTitle();
             const updateScroll = () => {
                 this.isScrolled = (window.pageYOffset || document.documentElement.scrollTop || window.scrollY || 0) > 0;
             };
             window.addEventListener('scroll', updateScroll, { passive: true });
             updateScroll();
             this.$watch('activeTab', () => this.updateDocumentTitle());
             this.$watch('kegiatanSubTab', () => this.updateDocumentTitle());
             this.$watch('financeSubTab', () => this.updateDocumentTitle());
             this.$watch('socialSubTab', () => this.updateDocumentTitle());
             this.$watch('userSubTab', () => this.updateDocumentTitle());
             this.$watch('settingsSubTab', () => this.updateDocumentTitle());
             this.$watch('dutyForm.day_name', (val) => {
                 if (this.showPrayerDutyModal && val === 'Jumat' && this.dutyForm.prayer_time === 'dzuhur') {
                     this.redirectToFridayKajian();
                 }
             });
             this.$watch('dutyForm.prayer_time', (val) => {
                 if (this.showPrayerDutyModal && (this.dutyForm.day_name === 'Jumat' || (this.dutyRowData && this.dutyRowData.is_friday)) && val === 'dzuhur') {
                     this.redirectToFridayKajian();
                 }
             });
         },
         updateDocumentTitle() {
             let tabTitle = 'Dashboard';
             if (this.activeTab === 'dashboard') {
                 tabTitle = 'Dashboard';
             } else if (this.activeTab === 'kegiatan' || this.activeTab === 'kajian' || this.activeTab === 'agenda') {
                 const sub = this.kegiatanSubTab || 'pekanan';
                 if (sub === 'pekanan') tabTitle = 'Kajian';
                 else if (sub === 'jumat') tabTitle = 'Kajian Jumat & Khutbah';
                 else if (sub === 'agenda') tabTitle = 'Kegiatan Akbar';
                 else if (sub === 'odoj') tabTitle = 'One Day One Juz';
                 else tabTitle = 'Kajian';
             } else if (this.activeTab === 'petugas') {
                 tabTitle = 'Petugas Shalat';
             } else if (this.activeTab === 'finance') {
                 const sub = this.financeSubTab || 'utama';
                 if (sub === 'kategori') tabTitle = 'Kategori Kas';
                 else tabTitle = 'Kas & Keuangan';
             } else if (this.activeTab === 'programs') {
                 const sub = this.socialSubTab || 'katalog';
                 if (sub === 'peserta') tabTitle = 'Rekapitulasi Peserta';
                 else if (sub === 'pernyataan') tabTitle = 'Surat Pernyataan Potongan Tukin';
                 else if (sub === 'setoran') tabTitle = 'Setoran Kantor';
                 else tabTitle = 'Program Sosial';
             } else if (this.activeTab === 'users') {
                 tabTitle = (this.userSubTab === 'kepengurusan') ? 'Takmir & Kepengurusan' : 'Manajemen Pengguna';
             } else if (this.activeTab === 'settings') {
                 tabTitle = (this.settingsSubTab === 'poster') ? 'Template & Studio Poster Kajian' : 'Pengaturan Masjid & Layar TV';
             } else if (this.activeTab === 'statistics' || this.activeTab === 'statistik') {
                 tabTitle = 'Statistik Pengunjung';
             } else if (this.activeTab === 'galeri' || this.activeTab === 'gallery') {
                 tabTitle = 'Galeri Kegiatan';
             } else if (this.activeTab === 'saran') {
                 tabTitle = 'Saran & Kritik Jamaah';
             }
             document.title = tabTitle + ' - Masjid Salahuddin';
         },
         switchSubTab(tab) {
             this.kegiatanSubTab = tab;
             if (tab === 'pekanan' || tab === 'jumat') this.kajianSubTab = tab;
             if (tab === 'agenda' || tab === 'odoj') this.agendaSubTab = tab;
             if (typeof $wire !== 'undefined') {
                 $wire.set('kegiatanSubTab', tab, false);
                 if (tab === 'pekanan' || tab === 'jumat') $wire.set('kajianSubTab', tab, false);
                 if (tab === 'agenda' || tab === 'odoj') $wire.set('agendaSubTab', tab, false);
             }
             this.updateDocumentTitle();
             this.$nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); });
         },
         switchSettingsSubTab(subTab) {
             this.settingsSubTab = subTab;
             if (typeof $wire !== 'undefined') {
                 $wire.set('settingsSubTab', subTab, false);
             }
             this.updateDocumentTitle();
             this.$nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); });
         },
         getBreadcrumb() {
             if (this.activeTab === 'dashboard') {
                 return 'Dashboard';
             }
             if (this.activeTab === 'kegiatan' || this.activeTab === 'kajian' || this.activeTab === 'agenda') {
                 return 'Kegiatan';
             }
             if (this.activeTab === 'petugas') {
                 return 'Petugas Shalat';
             }
             if (this.activeTab === 'finance') {
                 return 'Kas & Keuangan';
             }
             if (this.activeTab === 'programs') {
                 return 'Program Sosial';
             }
             if (this.activeTab === 'users') {
                 return 'Pengguna';
             }
             if (this.activeTab === 'settings') {
                 return 'Pengaturan';
             }
             if (this.activeTab === 'statistics' || this.activeTab === 'statistik') {
                 return 'Statistik Pengunjung';
             }
             if (this.activeTab === 'galeri' || this.activeTab === 'gallery') {
                 return 'Galeri Kegiatan';
             }
             if (this.activeTab === 'saran') {
                 return 'Saran & Kritik';
             }
             return this.activeTab ? (this.activeTab.charAt(0).toUpperCase() + this.activeTab.slice(1)) : 'Dashboard';
         },
         showEmployeeModal: false,
         showImportEmployeeModal: false,
         showUploadDocModal: false,
         showTemplateKepModal: false,
         showKajianModal: {{ $showKajianModal ? 'true' : 'false' }},
         showImportKajianModal: false,
         showUstadzModal: false,
         get ustadzPhotoMap() {
             const el = document.getElementById('ustadzDataContainer');
             if (!el) return {};
             try { return JSON.parse(el.dataset.photomap || '{}'); }
             catch(e) { return {}; }
         },
         get ustadzDirectory() {
             const el = document.getElementById('ustadzDataContainer');
             if (!el) return [];
             try { return JSON.parse(el.dataset.directory || '[]'); }
             catch(e) { return []; }
         },
         importKajianTab: 'paste',
         importKajianType: 'pekanan',
         showPrayerDutyModal: false,
         showImportDutyModal: false,
         importDutyTab: 'paste',
         isEditingKajian: false,
         kajianForm: {
             id: null,
             type: 'pekanan',
             date: '{{ Carbon\Carbon::now()->format('Y-m-d') }}',
             time_display: '',
             title: '',
             speaker_name: '',
             speaker_phone: '',
             is_holiday_disabled: false,
             khatib_name: '',
             mc_name: '',
             muadzin_name: '',
             khatib_phone: '',
             mc_notes: '',
             youtube_url: '',
         },
         async updateKajianDefaultTime() {
             if (this.kajianForm.type === 'tematik') {
                 this.kajianForm.time_display = '';
                 return;
             }
             if (typeof $wire !== 'undefined' && typeof $wire.getDefaultKajianTime === 'function') {
                 try {
                     const res = await $wire.getDefaultKajianTime(this.kajianForm.type, this.kajianForm.date);
                     if (res) {
                         this.kajianForm.time_display = res;
                     }
                 } catch (e) {}
             }
         },
         openCreateKajian(type = 'pekanan') {
             this.isEditingKajian = false;
             this.kajianForm = {
                 id: null,
                 type: type || 'pekanan',
                 date: '{{ Carbon\Carbon::now()->format('Y-m-d') }}',
                 time_display: '',
                 title: '',
                 speaker_name: '',
                 speaker_phone: '',
                 is_holiday_disabled: false,
                 khatib_name: '',
                 mc_name: '',
                 muadzin_name: '',
                 khatib_phone: '',
                 mc_notes: '',
                 speaker_photo: '',
                 youtube_url: '',
             };
             this.updateKajianDefaultTime();
             if (typeof $wire !== 'undefined') {
                 $wire.set('isEditingKajian', false, false);
                 $wire.set('editingKajianId', null, false);
                 $wire.set('kajianSpeakerPhoto', null, false);
                 $wire.set('kajianExistingPhoto', null, false);
                 $wire.set('kajianYoutubeUrl', null, false);
                 $wire.set('showKajianModal', true, false);
             }
             this.showKajianModal = true;
             this.$nextTick(() => {
                 if (window.createLucideIcons) window.createLucideIcons();
             });
         },
         openEditKajian(data) {
             this.isEditingKajian = true;
             this.kajianForm = {
                 id: data.id,
                 type: data.type || 'pekanan',
                 date: data.date || '',
                 time_display: data.time_display || '',
                 title: data.title || '',
                 speaker_name: data.speaker_name || '',
                 speaker_phone: data.speaker_phone || '',
                 is_holiday_disabled: Boolean(data.is_holiday_disabled),
                 khatib_name: data.khatib_name || '',
                 mc_name: data.mc_name || '',
                 muadzin_name: data.muadzin_name || '',
                 khatib_phone: data.khatib_phone || '',
                 mc_notes: data.mc_notes || '',
                 speaker_photo: data.speaker_photo || '',
                 youtube_url: data.youtube_url || '',
             };
             if (!this.kajianForm.time_display && this.kajianForm.type !== 'tematik') {
                 this.updateKajianDefaultTime();
             }
             if (typeof $wire !== 'undefined') {
                 $wire.set('isEditingKajian', true, false);
                 $wire.set('editingKajianId', data.id, false);
                 $wire.set('kajianSpeakerPhoto', null, false);
                 $wire.set('kajianExistingPhoto', data.speaker_photo || null, false);
                 $wire.set('kajianYoutubeUrl', data.youtube_url || null, false);
                 $wire.set('showKajianModal', true, false);
             }
             this.showKajianModal = true;
             this.$nextTick(() => {
                 if (window.createLucideIcons) window.createLucideIcons();
             });
         },
         closeKajianModal() {
             this.showKajianModal = false;
             this.isEditingKajian = false;
             if (typeof $wire !== 'undefined') {
                 $wire.set('showKajianModal', false, false);
                 $wire.set('isEditingKajian', false, false);
                 $wire.set('editingKajianId', null, false);
                 $wire.set('kajianSpeakerPhoto', null, false);
             }
         },
          showNotulaModal: false,
          notulaActiveTab: 'preview',
          notulaFontSizeIndex: 1,
          notulaTranscriptDismissed: false,
          isYoutubeTranscript(text) {
              if (!text || typeof text !== 'string' || text.length < 40 || this.notulaTranscriptDismissed) return false;
              const hasDetikPattern = /\b\d+(\.\d+)?\s*detik\b/i.test(text);
              const hasTimestamp = /\b\d{1,2}:\d{2}\b/.test(text) && (text.toLowerCase().includes('transkrip') || text.toLowerCase().includes('telusuri') || text.toLowerCase().includes('detik'));
              const hasHeader = /^\s*(?:transkrip|telusuri transkrip|transcript)/im.test(text.slice(0, 300));
              return hasDetikPattern || (hasTimestamp && hasHeader) || (hasHeader && /\b\d+\b/.test(text));
          },
          notulaForm: {
              id: null,
              title: '',
              type: 'pekanan',
              speaker_name: '',
              date: '',
              time_display: '',
              raw_text: '',
          },
          openNotulaModal(data) {
              if (Array.isArray(data) && data.length > 0) data = data[0];
              if (data && data.data && typeof data.data === 'object') data = data.data;
              if (!data || typeof data !== 'object') return;
              this.notulaTranscriptDismissed = false;
              this.notulaForm = {
                  id: data.id,
                  title: data.title || '',
                  type: data.type || 'pekanan',
                  speaker_name: data.speaker_name || '',
                  date: data.date || '',
                  time_display: data.time_display || '',
                  raw_text: data.notula || '',
              };
              this.notulaFontSizeIndex = 1;
              this.notulaActiveTab = (data.notula && data.notula.trim().length > 0) ? 'preview' : 'editor';
              this.showNotulaModal = true;
              this.$nextTick(() => {
                  if (this.$refs.notulaTextarea) {
                      this.$refs.notulaTextarea.value = this.notulaForm.raw_text;
                  }
                  if (window.createLucideIcons) window.createLucideIcons();
              });
          },
          adjustNotulaFontSize(delta) {
              const newIdx = this.notulaFontSizeIndex + delta;
              if (newIdx >= 0 && newIdx <= 3) {
                  this.notulaFontSizeIndex = newIdx;
              }
          },
          showYoutubeModal: false,
          youtubeForm: {
              id: null,
              type: 'kajian',
              title: '',
              subtitle: '',
              date: '',
              youtube_url: '',
          },
          openYoutubeModal(data) {
              if (Array.isArray(data) && data.length > 0) data = data[0];
              if (data && data.data && typeof data.data === 'object') data = data.data;
              if (!data || typeof data !== 'object') return;
              this.youtubeForm = {
                  id: data.id,
                  type: data.type || 'kajian',
                  title: data.title || '',
                  subtitle: data.subtitle || data.speaker_name || '',
                  date: data.date || data.event_date || '',
                  youtube_url: data.youtube_url || '',
              };
              this.showYoutubeModal = true;
              if (window.Livewire) {
                  $wire.set('showYoutubeModal', true, false);
                  $wire.set('youtubeItemId', data.id, false);
                  $wire.set('youtubeItemType', data.type || 'kajian', false);
                  $wire.set('youtubeItemTitle', data.title || '', false);
                  $wire.set('youtubeItemSubtitle', data.subtitle || data.speaker_name || '', false);
                  $wire.set('youtubeItemDate', data.date || data.event_date || '', false);
                  $wire.set('youtubeUrl', data.youtube_url || '', false);
              }
          },
          dutyRowData: null,
          isEditingPrayerDuty: false,
          dutyForm: {
              id: null,
              day_name: 'Senin',
              prayer_time: 'dzuhur',
              week_pattern: 'semua',
              tahun: {{ Carbon\Carbon::now('Asia/Jakarta')->year }},
              imam_name: '',
              muadzin_name: ''
          },
          isDutyFridayDzuhur() {
              return (this.dutyForm.day_name === 'Jumat' || (this.dutyRowData && this.dutyRowData.is_friday)) && this.dutyForm.prayer_time === 'dzuhur';
          },
          redirectToFridayKajian(kajianId = null) {
              this.showPrayerDutyModal = false;
              const targetKajianId = kajianId || (this.dutyRowData ? this.dutyRowData.friday_kajian_id : null);
              this.activeTab = 'kegiatan';
              this.kegiatanSubTab = 'jumat';
              this.kajianSubTab = 'jumat';
              this.updateDocumentTitle();
              if (window.history && window.history.replaceState) {
                  window.history.replaceState({}, '', '/admin/kegiatan');
              }
              $wire.openFridayKajianFromPetugas(targetKajianId);
          },
          handleDutyDayChange(day) {
              this.dutyForm.day_name = day;
              if (this.showPrayerDutyModal && day === 'Jumat' && this.dutyForm.prayer_time === 'dzuhur') {
                  this.redirectToFridayKajian();
              }
          },
          openCreatePrayerDuty(day = 'Senin', time = 'dzuhur') {
              if (day === 'Jumat' && time === 'dzuhur') {
                  this.redirectToFridayKajian();
                  return;
              }
              this.dutyRowData = null;
              this.isEditingPrayerDuty = false;
              this.dutyForm = {
                  id: null,
                  day_name: day || 'Senin',
                  prayer_time: time || 'dzuhur',
                  week_pattern: 'semua',
                  tahun: ($wire.prayerDutyFilterYear && $wire.prayerDutyFilterYear !== 'all') ? parseInt($wire.prayerDutyFilterYear) : {{ Carbon\Carbon::now('Asia/Jakarta')->year }},
                  imam_name: '',
                  muadzin_name: ''
              };
              this.showPrayerDutyModal = true;
              this.$nextTick(() => {
                  if (window.createLucideIcons) window.createLucideIcons();
              });
          },
          openEditPrayerDutyRow(data) {
              this.dutyRowData = {
                  day_name: data.day_name || 'Senin',
                  week_pattern: data.week_pattern || 'semua',
                  tahun: data.tahun || {{ Carbon\Carbon::now('Asia/Jakarta')->year }},
                  dzuhur: data.dzuhur || null,
                  ashar: data.ashar || null,
                  is_friday: !!data.is_friday,
                  friday_kajian_id: data.friday_kajian_id || null,
              };

              let initialTime = 'dzuhur';
              if (data.is_friday) {
                  initialTime = data.ashar ? 'ashar' : 'dzuhur';
              } else {
                  initialTime = data.dzuhur ? 'dzuhur' : (data.ashar ? 'ashar' : 'dzuhur');
              }

              if (data.is_friday && initialTime === 'dzuhur') {
                  this.redirectToFridayKajian(data.friday_kajian_id || null);
                  return;
              }

              this.switchDutyTime(initialTime);
              this.showPrayerDutyModal = true;
              this.$nextTick(() => {
                  if (window.createLucideIcons) window.createLucideIcons();
              });
          },
          switchDutyTime(time) {
              this.dutyForm.prayer_time = time;
              if (this.showPrayerDutyModal && (this.dutyForm.day_name === 'Jumat' || (this.dutyRowData && this.dutyRowData.is_friday)) && time === 'dzuhur') {
                  this.redirectToFridayKajian(this.dutyRowData ? this.dutyRowData.friday_kajian_id : null);
                  return;
              }
              if (this.dutyRowData) {
                  this.dutyForm.day_name = this.dutyRowData.day_name || this.dutyForm.day_name || 'Senin';
                  this.dutyForm.tahun = this.dutyRowData.tahun || this.dutyForm.tahun || {{ Carbon\Carbon::now('Asia/Jakarta')->year }};
                  const targetDuty = (time === 'dzuhur') ? this.dutyRowData.dzuhur : this.dutyRowData.ashar;
                  if (targetDuty && targetDuty.id) {
                      this.isEditingPrayerDuty = true;
                      this.dutyForm.id = targetDuty.id;
                      this.dutyForm.week_pattern = targetDuty.week_pattern || this.dutyRowData.week_pattern || 'semua';
                      this.dutyForm.imam_name = targetDuty.imam_name || '';
                      this.dutyForm.muadzin_name = targetDuty.muadzin_name || '';
                  } else {
                      this.isEditingPrayerDuty = false;
                      this.dutyForm.id = null;
                      this.dutyForm.week_pattern = this.dutyRowData.week_pattern || 'semua';
                      this.dutyForm.imam_name = '';
                      this.dutyForm.muadzin_name = '';
                  }
              }
          },
          openEditPrayerDuty(data) {
              this.dutyRowData = null;
              this.isEditingPrayerDuty = true;
              this.dutyForm = {
                  id: data.id,
                  day_name: data.day_name || 'Senin',
                  prayer_time: data.prayer_time || 'dzuhur',
                  week_pattern: data.week_pattern || 'semua',
                  tahun: data.tahun || {{ Carbon\Carbon::now('Asia/Jakarta')->year }},
                  imam_name: data.imam_name || '',
                  muadzin_name: data.muadzin_name || ''
             };
             this.showPrayerDutyModal = true;
             this.$nextTick(() => {
                 if (window.createLucideIcons) window.createLucideIcons();
             });
         },
         showBatchReplaceDutyModal: false,
         replaceRoleScope: 'both',
         replaceYearScope: 'current',
         openBatchReplaceDutyModal() {
             this.showBatchReplaceDutyModal = true;
             this.replaceRoleScope = 'both';
             this.replaceYearScope = 'current';
             $wire.openBatchReplaceDutyModal();
             this.$nextTick(() => {
                 if (window.createLucideIcons) window.createLucideIcons();
             });
         },
         closeBatchReplaceDutyModal() {
             this.showBatchReplaceDutyModal = false;
             $wire.closeBatchReplaceDutyModal();
         },
        agendaSubTab: '{{ $agendaSubTab }}',
        agendaViewMode: 'grid',
        showAgendaModal: {{ $showAgendaModal ? 'true' : 'false' }},
        showImportAgendaModal: false,
        importAgendaTab: 'paste',
        isEditingAgenda: false,
        agendaForm: {
            id: null,
            title: '',
            description: '',
            event_date: '{{ Carbon\Carbon::now()->addDays(7)->format('Y-m-d') }}',
            budget: 0,
            status: 'Direncanakan',
            committee_members: '',
            report_summary: '',
            report_pdf_path: '',
            youtube_url: '',
        },
        openCreateAgenda() {
            this.isEditingAgenda = false;
            this.agendaForm = {
                id: null,
                title: '',
                description: '',
                event_date: '{{ Carbon\Carbon::now()->addDays(7)->format('Y-m-d') }}',
                budget: 0,
                status: 'Direncanakan',
                committee_members: '',
                report_summary: '',
                report_pdf_path: '',
                youtube_url: '',
            };
            const pdfInput = document.getElementById('agendaReportPdfInput');
            if (pdfInput) pdfInput.value = '';
            this.showAgendaModal = true;
            if (window.Livewire) {
                $wire.set('showAgendaModal', true, false);
            }
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        openEditAgenda(data) {
            this.isEditingAgenda = true;
            this.agendaForm = {
                id: data.id,
                title: data.title || '',
                description: data.description || '',
                event_date: data.event_date ? String(data.event_date).substring(0, 10) : '',
                budget: data.budget ? parseInt(data.budget) : 0,
                status: data.status || 'Direncanakan',
                committee_members: data.committee_members || '',
                report_summary: data.report_summary || '',
                report_pdf_path: data.report_pdf_path || '',
                youtube_url: data.youtube_url || '',
            };
            const pdfInput = document.getElementById('agendaReportPdfInput');
            if (pdfInput) pdfInput.value = '';
            this.showAgendaModal = true;
            if (window.Livewire) {
                $wire.set('showAgendaModal', true, false);
            }
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        showAssignOdojModal: false,
        assignJuzNumber: 1,
        assignPegawai1: '',
        assignPegawai2: '',
        openAssignOdoj(juz = 1, currentName = '') {
            this.assignJuzNumber = juz;
            const parts = (currentName || '').split(/[\/,]/);
            this.assignPegawai1 = (parts[0] || '').trim();
            this.assignPegawai2 = (parts[1] || '').trim();
            this.showAssignOdojModal = true;
            $wire.set('assignJuzNumber', juz, false);
            $wire.set('assignPegawai1', this.assignPegawai1, false);
            $wire.set('assignPegawai2', this.assignPegawai2, false);
        },
        showUserModal: false,
        isEditingUser: false,
        userForm: {
            id: null,
            name: '',
            email: '',
            password: '',
            role: 'Jamaah',
        },
        openCreateUser() {
            this.isEditingUser = false;
            this.userForm = {
                id: null,
                name: '',
                email: '',
                password: '',
                role: 'Jamaah',
            };
            $wire.set('isEditingUser', false, false);
            $wire.set('editingUserId', null, false);
            $wire.set('userName', '', false);
            $wire.set('userEmail', '', false);
            $wire.set('userPassword', '', false);
            $wire.set('userRole', 'Jamaah', false);
            $wire.set('userStatus', 'AKTIF', false);
            this.showUserModal = true;
        },
        openEditUser(data) {
            this.isEditingUser = true;
            this.userForm = {
                id: data.id,
                name: data.name || '',
                email: data.email || '',
                password: '',
                role: data.role || 'Jamaah',
            };
            $wire.set('isEditingUser', true, false);
            $wire.set('editingUserId', data.id, false);
            $wire.set('userName', data.name || '', false);
            $wire.set('userEmail', data.email || '', false);
            $wire.set('userPassword', '', false);
            $wire.set('userRole', data.role || 'Jamaah', false);
            $wire.set('userStatus', data.status || 'AKTIF', false);
            this.showUserModal = true;
        },
        financeSubTab: '{{ $financeSubTab }}',
        programSubView: '{{ $programSubView }}',
        showFinanceModal: false,
        showLumpSumModal: false,
        isEditingFinance: false,
        showPrintFinanceModal: false,
        currentFinanceReceiptUrl: null,
        receiptPreviewModal: {
            show: false,
            items: [],
            currentIndex: 0,
            description: '',
            date: '',
            amount: '',
            type: 'pemasukan',
            fromFinanceModal: false,
            get currentItem() {
                if (this.items && this.items.length > 0) {
                    return this.items[this.currentIndex] || this.items[0];
                }
                return null;
            },
            get currentUrl() {
                return this.currentItem ? this.currentItem.url : '';
            },
            get isPdf() {
                if (!this.currentItem) return false;
                if (typeof this.currentItem.is_pdf !== 'undefined') return Boolean(this.currentItem.is_pdf);
                const u = (this.currentUrl || '').toLowerCase();
                return u.endsWith('.pdf') || u.includes('.pdf?');
            },
            next() {
                if (this.items && this.items.length > 1) {
                    this.currentIndex = (this.currentIndex + 1) % this.items.length;
                }
            },
            prev() {
                if (this.items && this.items.length > 1) {
                    this.currentIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
                }
            },
            setIndex(idx) {
                if (this.items && idx >= 0 && idx < this.items.length) {
                    this.currentIndex = idx;
                }
            }
        },
        openReceiptModal(data) {
            let items = [];
            if (Array.isArray(data.items) && data.items.length > 0) {
                items = data.items.map(item => {
                    if (typeof item === 'string') {
                        const isPdf = item.toLowerCase().endsWith('.pdf') || item.toLowerCase().includes('.pdf?');
                        return { url: item, is_pdf: isPdf };
                    }
                    return item;
                });
            } else if (data.url) {
                const isPdf = data.url.toLowerCase().endsWith('.pdf') || data.url.toLowerCase().includes('.pdf?');
                items = [{ url: data.url, is_pdf: isPdf }];
            }

            this.receiptPreviewModal.items = items;
            this.receiptPreviewModal.currentIndex = 0;
            this.receiptPreviewModal.description = data.description || 'Bukti Transaksi';
            this.receiptPreviewModal.date = data.date || '';
            this.receiptPreviewModal.amount = data.amount || '';
            this.receiptPreviewModal.type = data.type || 'pemasukan';
            this.receiptPreviewModal.fromFinanceModal = Boolean(data.fromFinanceModal);

            if (this.receiptPreviewModal.fromFinanceModal) {
                this.showFinanceModal = false;
            }

            this.receiptPreviewModal.show = true;

            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        closeReceiptModal(forceCloseAll = false) {
            const shouldReopenFinance = !forceCloseAll && this.receiptPreviewModal.fromFinanceModal;
            this.receiptPreviewModal.show = false;
            this.receiptPreviewModal.fromFinanceModal = false;
            if (shouldReopenFinance) {
                this.$nextTick(() => {
                    this.showFinanceModal = true;
                    if (window.createLucideIcons) window.createLucideIcons();
                });
            }
        },
        galleryLightboxModal: {
            show: false,
            title: '',
            date: '',
            location: '',
            photos: [],
            currentIndex: 0,
            touchStartX: 0,
            touchEndX: 0,
            get currentPhoto() {
                if (this.photos && this.photos.length > 0) {
                    return this.photos[this.currentIndex] || this.photos[0];
                }
                return '';
            },
            next() {
                if (this.photos && this.photos.length > 1) {
                    this.currentIndex = (this.currentIndex + 1) % this.photos.length;
                }
            },
            prev() {
                if (this.photos && this.photos.length > 1) {
                    this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
                }
            },
            setIndex(idx) {
                if (this.photos && idx >= 0 && idx < this.photos.length) {
                    this.currentIndex = idx;
                }
            },
            handleSwipe() {
                const threshold = 40;
                if (this.touchStartX - this.touchEndX > threshold) {
                    this.next();
                } else if (this.touchEndX - this.touchStartX > threshold) {
                    this.prev();
                }
            }
        },
        openGalleryLightbox(data, index = 0) {
            this.galleryLightboxModal.title = data.title || '';
            this.galleryLightboxModal.date = data.date || data.formatted_date || '';
            this.galleryLightboxModal.location = data.location || '';
            this.galleryLightboxModal.photos = Array.isArray(data.photos) ? data.photos : (Array.isArray(data.photo_urls) ? data.photo_urls : []);
            this.galleryLightboxModal.currentIndex = (index >= 0 && index < this.galleryLightboxModal.photos.length) ? index : 0;
            this.galleryLightboxModal.show = true;
            document.body.classList.add('overflow-hidden');
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        closeGalleryLightbox() {
            this.galleryLightboxModal.show = false;
            document.body.classList.remove('overflow-hidden');
        },
        openCreateFinance() {
            this.isEditingFinance = false;
            this.currentFinanceReceiptUrl = null;
            this.showFinanceModal = true;
            const fileInput = document.getElementById('financeReceiptFilesInput');
            if (fileInput) fileInput.value = '';
            this.$dispatch('finance-modal-reset');
            $wire.call('openFinanceModal');
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        openEditFinance(data) {
            this.isEditingFinance = true;
            this.currentFinanceReceiptUrl = data.receipt_path ? ('{{ asset('storage') }}/' + data.receipt_path) : null;
            this.showFinanceModal = true;
            const fileInput = document.getElementById('financeReceiptFilesInput');
            if (fileInput) fileInput.value = '';
            $wire.call('editFinance', data.id);
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        showFinanceCategoryModal: false,
        isEditingFinanceCategory: false,
        financeCategoryForm: {
            id: null,
            name: '',
            group: 'pengeluaran_rutin',
            color: 'emerald'
        },
        openCreateFinanceCategory() {
            this.isEditingFinanceCategory = false;
            this.financeCategoryForm = {
                id: null,
                name: '',
                group: 'pengeluaran_rutin',
                color: 'emerald'
            };
            this.showFinanceCategoryModal = true;
        },
        openEditFinanceCategory(data) {
            this.isEditingFinanceCategory = true;
            this.financeCategoryForm = {
                id: data.id,
                name: data.name || '',
                group: data.group || (data.type === 'pemasukan' ? 'penerimaan' : 'pengeluaran_rutin'),
                color: data.color || 'emerald'
            };
            this.showFinanceCategoryModal = true;
        },
        showFinanceImportModal: false,
        importFinanceTab: 'paste',
        socialSubTab: '{{ $socialSubTab }}',
        showSocialProgramModal: false,
        isEditingSocialProgram: false,
        openCreateSocialProgram() {
            this.isEditingSocialProgram = false;
            this.showSocialProgramModal = true;
            $wire.call('openCreateSocialProgram');
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        openEditSocialProgram(id) {
            this.isEditingSocialProgram = true;
            this.showSocialProgramModal = true;
            $wire.call('openEditSocialProgram', id);
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        showImportPotonganModal: false,
        openImportPotonganModal() {
            this.showImportPotonganModal = true;
            $wire.call('openImportPotonganModal');
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        showExportPotonganModal: false,
        exportPotonganYear: {{ now()->year }},
        exportPotonganStartMonth: {{ now()->month }},
        exportPotonganEndMonth: {{ now()->month }},
        exportPotonganMonth: {{ now()->month }},
        showPrintPernyataanModal: $wire.entangle('showPrintPernyataanModal'),
        pernyataanModalTab: $wire.entangle('pernyataanModalTab'),
        printPernyataanMode: 'all',
        printPernyataanEmployeeId: '',
        printPernyataanEmployeeName: '',
        printPernyataanYear: {{ now()->year }},
        printPernyataanDateLetter: '',
@php
        $daftarBulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $nextMonthDate = now()->startOfMonth()->addMonth();
        $bulanDepanIndo = $daftarBulanIndo[(int) $nextMonthDate->month] ?? 'Januari';
        $tahunDepanIndo = (string) $nextMonthDate->year;
@endphp
        printPernyataanEffectiveMonth: '{{ $bulanDepanIndo }}',
        printPernyataanEffectiveYear: '{{ $tahunDepanIndo }}',
        printPernyataanSecretaryName: 'Deril Amrizal Kholid',
        printPernyataanSecretarySection: 'Seksi Penjaminan Kualitas Data',
        printPernyataanOpenDropdown: null,
        printPernyataanSearchEmp: '',
        printPernyataanSearchNewEmp: '',
        printPernyataanSelectedNewName: '',
        togglePrintDropdown(name) {
            this.printPernyataanOpenDropdown = (this.printPernyataanOpenDropdown === name ? null : name);
        },
        closePrintDropdowns() {
            this.printPernyataanOpenDropdown = null;
        },
        openPrintPernyataanModal(tab = 'cetak') {
            const targetTab = (tab === 'upload' || tab === 'impor') ? 'impor' : 'cetak';
            this.activeTab = 'programs';
            this.socialSubTab = 'pernyataan';
            this.pernyataanModalTab = targetTab;
            this.printPernyataanOpenDropdown = null;
            this.printPernyataanSearchEmp = '';
            this.printPernyataanSearchNewEmp = '';
            this.printPernyataanSelectedNewName = '';
            if (!this.printPernyataanEffectiveMonth) {
                this.printPernyataanEffectiveMonth = '{{ $bulanDepanIndo }}';
            }
            if (!this.printPernyataanEffectiveYear) {
                this.printPernyataanEffectiveYear = '{{ $tahunDepanIndo }}';
            }
            this.showPrintPernyataanModal = false;
            $wire.set('socialSubTab', 'pernyataan', false);
            $wire.set('pernyataanModalTab', targetTab, false);
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        showParticipantModal: false,
        isEditingParticipant: false,
        openCreateParticipant() {
            this.isEditingParticipant = false;
            this.showParticipantModal = true;
            $wire.set('isEditingParticipant', false, false);
            $wire.set('editingParticipantId', null, false);
            $wire.set('participantName', '', false);
            $wire.set('participantProgram', 'Santunan Anak Yatim', false);
            $wire.set('participantAmount', '250000', false);
            const now = new Date();
            const defaultPeriod = 'Periode ' + (now.getMonth() + 1) + '/' + now.getFullYear();
            $wire.set('participantPeriod', defaultPeriod, false);
            $wire.set('participantStatus', 'AKTIF', false);
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        openEditParticipant(data) {
            this.isEditingParticipant = true;
            this.showParticipantModal = true;
            $wire.set('isEditingParticipant', true, false);
            $wire.set('editingParticipantId', data.id, false);
            $wire.set('participantName', data.name || '', false);
            $wire.set('participantProgram', data.program_name || 'Santunan Anak Yatim', false);
            $wire.set('participantAmount', String(parseInt(data.monthly_amount) || ''), false);
            const now = new Date();
            const defaultPeriod = 'Periode ' + (now.getMonth() + 1) + '/' + now.getFullYear();
            $wire.set('participantPeriod', data.period || defaultPeriod, false);
            $wire.set('participantStatus', data.status || 'AKTIF', false);
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        switchTabFast(tab) {
            if (this.activeTab === tab) return;
            this.activeTab = tab;
            if (window.innerWidth < 768) {
                this.sidebarDrawerOpen = false;
            }
            if (window.history && window.history.replaceState) {
                window.history.replaceState({}, '', '/admin/' + tab);
            }
            this.updateDocumentTitle();
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
            $wire.switchTab(tab);
        },
        confirmDeleteModal: {
            show: false,
            title: '',
            message: '',
            itemName: '',
            targetAction: '',
            targetId: null,
            loading: false
        },
        openDeleteModal(options) {
            this.confirmDeleteModal = {
                show: true,
                title: options.title || 'Konfirmasi Hapus Data',
                message: options.message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
                itemName: options.itemName || '',
                targetAction: options.action,
                targetId: options.id,
                loading: false
            };
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        async executeConfirmDelete() {
            if (!this.confirmDeleteModal.targetAction || !this.confirmDeleteModal.targetId) return;
            if (this.confirmDeleteModal.loading) return;
            this.confirmDeleteModal.loading = true;
            try {
                await $wire.call(this.confirmDeleteModal.targetAction, this.confirmDeleteModal.targetId);
            } catch (err) {
                console.error('Confirm delete error:', err);
            } finally {
                this.confirmDeleteModal.show = false;
                this.confirmDeleteModal.loading = false;
            }
        },
        copyOdojToClipboard(text = null) {
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
        },
        copyJarkomanToClipboard(text = null) {
            if (window.copyJarkomanToClipboard) {
                window.copyJarkomanToClipboard(text);
            }
        },
        mobileActionSheetOpen: false,
        openMobileActionSheet() {
            if (['settings', 'statistics', 'statistik', 'saran'].includes(this.activeTab) || (this.activeTab === 'programs' && this.socialSubTab === 'pernyataan')) {
                return;
            }
            this.mobileActionSheetOpen = true;
            this.$nextTick(() => {
                if (window.createLucideIcons) window.createLucideIcons();
            });
        },
        closeMobileActionSheet() {
            this.mobileActionSheetOpen = false;
        },
        getMobileActionSubtitle() {
            if (this.activeTab === 'kegiatan' || this.activeTab === 'kajian' || this.activeTab === 'agenda') {
                if (this.kegiatanSubTab === 'pekanan') return 'Pilih aksi untuk jadwal Kajian Pekanan';
                if (this.kegiatanSubTab === 'jumat') return 'Pilih aksi untuk Shalat Jumat & Khutbah';
                if (this.kegiatanSubTab === 'agenda') return 'Pilih aksi untuk Agenda Kegiatan Akbar';
                if (this.kegiatanSubTab === 'odoj') return 'Pilih aksi untuk One Day One Juz';
                return 'Pilih aksi untuk halaman Kegiatan & Kajian';
            }
            if (this.activeTab === 'petugas') return 'Pilih aksi untuk Jadwal Petugas Shalat';
            if (this.activeTab === 'finance') {
                if (this.financeSubTab === 'kategori') return 'Pilih aksi untuk Kelola Pos Kategori';
                return 'Pilih aksi untuk Pembukuan Kas & Keuangan';
            }
            if (this.activeTab === 'programs') {
                if (this.socialSubTab === 'peserta') return 'Pilih aksi untuk Rekapitulasi Peserta';
                if (this.socialSubTab === 'setoran') return 'Pilih aksi untuk Rekap Setoran Kantor';
                return 'Pilih aksi untuk Program Sosial & Infaq';
            }
            if (this.activeTab === 'users') {
                if (this.userSubTab === 'pegawai') return 'Pilih aksi untuk Master Data Pegawai';
                if (this.userSubTab === 'kepengurusan') return 'Pilih aksi untuk Kepengurusan Takmir & Dokumen SK';
                return 'Pilih aksi untuk Manajemen Akun Pengguna';
            }
            if (this.activeTab === 'galeri' || this.activeTab === 'gallery') return 'Pilih aksi untuk Dokumentasi Galeri Kegiatan';
            return 'Pilih tindakan cepat untuk halaman ini';
        }
    }" @resize.window="if (window.innerWidth >= 768) { sidebarDrawerOpen = true; }"
    @open-edit-kajian-modal.window="
        activeTab = 'kegiatan';
        kegiatanSubTab = 'jumat';
        kajianSubTab = 'jumat';
        updateDocumentTitle();
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, '', '/admin/kegiatan');
        }
        openEditKajian($event.detail.data || $event.detail);
    "
    @open-create-kajian-modal.window="
        activeTab = 'kegiatan';
        kegiatanSubTab = 'jumat';
        kajianSubTab = 'jumat';
        updateDocumentTitle();
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, '', '/admin/kegiatan');
        }
        openCreateKajian($event.detail.type || 'jumat');
    "
    @open-kajian-modal.window="showKajianModal = true; if (typeof $wire !== 'undefined') { $wire.set('showKajianModal', true, false); }"
    @close-kajian-modal.window="showKajianModal = false; if (typeof $wire !== 'undefined') { $wire.set('showKajianModal', false, false); }"
    @open-ustadz-modal.window="showUstadzModal = true; $wire.set('showUstadzModal', true, false)" @close-ustadz-modal.window="showUstadzModal = false; $wire.set('showUstadzModal', false, false)"
    @open-import-kajian-modal.window="if ($event.detail && $event.detail.type) { importKajianType = $event.detail.type; } showImportKajianModal = true"
    @close-import-kajian-modal.window="showImportKajianModal = false"
    @open-notula-modal.window="openNotulaModal($event.detail)"
    @close-notula-modal.window="showNotulaModal = false; $wire.set('showNotulaModal', false, false)"
    @notula-saved.window="let d = Array.isArray($event.detail) ? $event.detail[0] : ($event.detail.data || $event.detail); if (notulaForm && d && d.id == notulaForm.id) { notulaForm.raw_text = d.notula; if ($refs.notulaTextarea) { $refs.notulaTextarea.value = d.notula; } }"
    @notula-transformed.window="let d = Array.isArray($event.detail) ? $event.detail[0] : ($event.detail.data || $event.detail); if (notulaForm && d) { notulaForm.raw_text = d.notula; if ($refs.notulaTextarea) { $refs.notulaTextarea.value = d.notula; } notulaActiveTab = 'preview'; notulaTranscriptDismissed = false; }"
    @open-youtube-modal.window="openYoutubeModal($event.detail)"
    @close-youtube-modal.window="showYoutubeModal = false; $wire.set('showYoutubeModal', false, false)"
    @youtube-saved.window="let d = Array.isArray($event.detail) ? $event.detail[0] : ($event.detail.data || $event.detail); if (youtubeForm && d && d.id == youtubeForm.id) { youtubeForm.youtube_url = d.youtube_url; }"
    @open-duty-modal.window="showPrayerDutyModal = true" @close-duty-modal.window="showPrayerDutyModal = false"
    @open-batch-replace-duty-modal.window="showBatchReplaceDutyModal = true; replaceRoleScope = $wire.replaceRoleScope || 'both'; replaceYearScope = $wire.replaceYearScope || 'current'" @close-batch-replace-duty-modal.window="showBatchReplaceDutyModal = false"
    @open-import-duty-modal.window="showImportDutyModal = true"
    @close-import-duty-modal.window="showImportDutyModal = false" @open-agenda-modal.window="showAgendaModal = true"
    @close-agenda-modal.window="showAgendaModal = false" @open-import-agenda-modal.window="showImportAgendaModal = true"
    @close-import-agenda-modal.window="showImportAgendaModal = false"
    @open-assign-odoj-modal.window="showAssignOdojModal = true"
    @close-assign-odoj-modal.window="showAssignOdojModal = false"
    @open-user-modal.window="showUserModal = true"
    @close-user-modal.window="showUserModal = false"
    @open-employee-modal.window="showEmployeeModal = true"
    @close-employee-modal.window="showEmployeeModal = false"
    @open-import-employee-modal.window="showImportEmployeeModal = true"
    @close-import-employee-modal.window="showImportEmployeeModal = false"
    @open-upload-doc-modal.window="showUploadDocModal = true"
    @close-upload-doc-modal.window="showUploadDocModal = false"
    @open-template-kep-modal.window="showTemplateKepModal = true"
    @close-template-kep-modal.window="showTemplateKepModal = false"
    @open-finance-modal.window="showFinanceModal = true"
    @close-finance-modal.window="showFinanceModal = false"
    @open-finance-category-modal.window="showFinanceCategoryModal = true"
    @close-finance-category-modal.window="showFinanceCategoryModal = false"
    @open-import-finance-modal.window="showFinanceImportModal = true"
    @close-import-finance-modal.window="showFinanceImportModal = false"
    @open-lumpsum-modal.window="showLumpSumModal = true"
    @close-lumpsum-modal.window="showLumpSumModal = false"
    @open-participant-modal.window="showParticipantModal = true"
    @close-participant-modal.window="showParticipantModal = false"
    @open-social-program-modal.window="showSocialProgramModal = true"
    @close-social-program-modal.window="showSocialProgramModal = false"
    @open-import-potongan-modal.window="showImportPotonganModal = true"
    @close-import-potongan-modal.window="showImportPotonganModal = false"
    @open-export-potongan-modal.window="showExportPotonganModal = true"
    @close-export-potongan-modal.window="showExportPotonganModal = false"
    @open-print-pernyataan-modal.window="openPrintPernyataanModal($event.detail?.tab || 'cetak')"
    @open-print-pernyataan-tab.window="openPrintPernyataanModal($event.detail?.tab || 'cetak')"
    @close-print-pernyataan-modal.window="showPrintPernyataanModal = false"
    @open-delete-modal.window="openDeleteModal($event.detail)"
    @close-delete-modal.window="confirmDeleteModal.show = false"
    @open-receipt-modal.window="openReceiptModal($event.detail)"
    @close-receipt-modal.window="closeReceiptModal()"
    @open-gallery-lightbox.window="openGalleryLightbox($event.detail.data || $event.detail, $event.detail.index || 0)"
    @close-gallery-lightbox.window="closeGalleryLightbox()">

    <!-- Admin Drawer Backdrop Overlay (Mobile Only) -->
    <div x-show="sidebarDrawerOpen && window.innerWidth < 768" @click="sidebarDrawerOpen = false" x-cloak
        wire:ignore.self x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-40 md:hidden" style="display: none;">
    </div>

    <!-- Admin Sidebar Drawer with Square Menu Items -->
    <aside wire:ignore.self
        class="admin-sidebar bg-gov-navyDark text-white flex flex-col justify-between border-r border-[#0D284C] shadow-2xl md:shadow-none"
        :class="sidebarDrawerOpen ? '' : 'sidebar-closed'">
        <div class="flex-1 flex flex-col min-h-0 overflow-y-auto">
            <!-- Drawer Header with Close and Desktop Menu Button -->
            <div
                class="p-3.5 pl-5 border-b border-[#0F2F59] flex items-center justify-between bg-[#06172E] shrink-0 sticky top-0 z-10">
                <span class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span>Masjid Salahuddin</span>
                </span>

                <!-- Desktop: Menu Toggle Button moved here -->
                <button type="button" @click="sidebarDrawerOpen = !sidebarDrawerOpen"
                    class="hidden md:flex items-center justify-center p-1.5 bg-transparent text-white text-xs font-semibold rounded-lg transition border-0 border-none shadow-none cursor-pointer"
                    title="Tutup Menu Drawer">
                    <i data-lucide="x" class="w-4 h-4 text-amber-400"></i>
                </button>

                <!-- Mobile: Close X Button -->
                <button type="button" @click="sidebarDrawerOpen = false"
                    class="p-1 text-slate-400 hover:text-white hover:bg-white/10 transition cursor-pointer md:hidden"
                    title="Tutup Drawer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Navigation Links with Square (rounded-none) Style & Amber-Gold Active Border & strictly font-normal -->
            <div class="p-3 pt-4 space-y-1 text-xs">
                <span
                    class="text-xs uppercase tracking-wider text-slate-400 font-semibold px-3 block mb-1.5">MANAJEMEN</span>

                <a href="{{ url('/admin/dashboard') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition shadow-2xs cursor-pointer border-l-[3px] {{ $currentTab === 'dashboard' ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="layout-grid" class="w-4 h-4 text-amber-300"></i>
                    <span class="text-xs font-medium">Dashboard</span>
                </a>

                <a href="{{ url('/admin/kegiatan') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ in_array($currentTab, ['kegiatan', 'kajian', 'agenda']) ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="calendar-range" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Jadwal Kegiatan</span>
                </a>

                <a href="{{ url('/admin/petugas') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ $currentTab === 'petugas' ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="user-check" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Imam & Muadzin</span>
                </a>

                <a href="{{ url('/admin/finance') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ $currentTab === 'finance' ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="wallet" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Kas & Keuangan</span>
                </a>

                <a href="{{ url('/admin/programs') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ $currentTab === 'programs' ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="heart-handshake" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Program Sosial</span>
                </a>

                <a href="{{ url('/admin/galeri') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ in_array($currentTab, ['galeri', 'gallery']) ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="images" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Galeri Kegiatan</span>
                </a>

                @unless(Auth::user()->isJamaah())
                <a href="{{ url('/admin/saran') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ $currentTab === 'saran' ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="message-square-quote" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Saran & Kritik</span>
                </a>
                @endunless
            </div>

            <!-- Secondary Links with Square (rounded-none) Style -->
            <div class="p-3 pt-1 space-y-1 text-xs border-t border-[#0F2F59] mt-2">
                @unless(Auth::user()->isJamaah())
                <span
                    class="text-xs uppercase tracking-wider text-slate-400 font-semibold px-3 block mb-1.5">SISTEM</span>

                <a href="{{ url('/admin/settings') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ $currentTab === 'settings' ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Pengaturan</span>
                </a>

                <a href="{{ url('/admin/users') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ $currentTab === 'users' ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="user-cog" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Pengguna</span>
                </a>

                <a href="{{ url('/admin/statistics') }}" wire:navigate
                    class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-none font-normal transition border-l-[3px] cursor-pointer {{ in_array($currentTab, ['statistics', 'statistik']) ? 'bg-white/10 text-white border-amber-400' : 'text-slate-300 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-xs font-medium">Statistik Pengunjung</span>
                </a>
                @endunless

                <a href="{{ url('/') }}" target="_blank"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-none font-normal text-slate-300 hover:text-white hover:bg-white/5 transition">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="external-link" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-xs font-medium">Portal Jamaah</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Sidebar User Footer -->
        <div class="p-3.5 border-t border-[#0F2F59] bg-[#06172E] flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div
                    class="w-8 h-8 rounded-lg bg-amber-400 text-gov-navy font-extrabold flex items-center justify-center text-xs shadow-2xs shrink-0">
                    {{ substr(Auth::user()->name ?? 'DKM', 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Pengurus' }}</div>
                    <div class="text-[10px] text-slate-400">
                        @if(Auth::user()->isReadOnly())
                            Viewer (Read-Only)
                        @else
                            {{ ucfirst(Auth::user()->role ?? 'Admin') }}
                        @endif
                    </div>
                </div>
            </div>
            <button wire:click="logout" wire:loading.attr="disabled" title="Keluar"
                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-white/10 transition cursor-pointer disabled:opacity-50">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </button>
        </div>
    </aside>

    <!-- Admin Main Workspace -->
    <div class="admin-workspace flex-1 flex flex-col min-w-0 min-h-screen bg-gov-canvas"
        :class="sidebarDrawerOpen ? '' : 'sidebar-closed'">

        <!-- Top Workspace Bar with Drawer Trigger Button & Sticky Sub-Header -->
        <div class="sticky top-0 z-20 transition-shadow duration-200"
            :class="isScrolled ? 'shadow-lg' : 'shadow-2xs'">
            <header
                class="bg-white border-b border-gov-border px-6 py-3 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <!-- Drawer Toggle Trigger Button (Mobile, or Desktop when sidebar closed) -->
                    <button type="button" @click="sidebarDrawerOpen = !sidebarDrawerOpen"
                        class="items-center justify-center p-1.5 bg-transparent border-0 border-none shadow-none text-xs font-semibold rounded-lg cursor-pointer transition-all duration-300 ease-out"
                        :class="sidebarDrawerOpen ? 'md:opacity-0 md:w-0 md:p-0 md:pointer-events-none md:overflow-hidden flex' : 'opacity-100 w-8 flex'"
                        title="Buka/Tutup Menu Drawer">
                        <i data-lucide="menu" class="w-4 h-4 text-gov-navyHover shrink-0"></i>
                    </button>

                    <div class="flex items-center text-xs text-gov-textMuted font-medium">
                        <span class="font-semibold text-gov-textMain flex items-center gap-3">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500 ring-4 ring-amber-500/15"></span>
                            <span x-text="getBreadcrumb()">{{ $this->getBreadcrumbTitle() }}</span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                </div>
            </header>

            <!-- OPSI 4: Sticky Sub-Header Tabs (Tempatkan tepat di bawah Header utama) -->
            @if($currentTab === 'programs')
                <div class="bg-white border-b border-slate-200 px-6 sm:px-6 pt-1 transition-shadow duration-200"
                    :class="isScrolled ? 'shadow-lg' : ''">
                    <div class="flex items-center overflow-x-auto no-scrollbar gap-1">
                        <!-- Tab 1: Katalog Program -->
                        <button type="button"
                            @click="socialSubTab = 'katalog'; $wire.set('socialSubTab', 'katalog', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="socialSubTab === 'katalog' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="heart-handshake" class="w-3 h-3"></i>
                            <span>Katalog Program</span>
                        </button>

                        <!-- Tab 2: Rekap Peserta -->
                        @if(Auth::user()->canManage())
                            <button type="button"
                                @click="socialSubTab = 'peserta'; $wire.set('socialSubTab', 'peserta', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                                :class="socialSubTab === 'peserta' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                                <i data-lucide="users" class="w-3 h-3"></i>
                                <span>Rekap Peserta</span>
                            </button>
                        @endif

                        <!-- Tab 3: Surat Pernyataan -->
                        <button type="button"
                            @click="socialSubTab = 'pernyataan'; $wire.set('socialSubTab', 'pernyataan', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="socialSubTab === 'pernyataan' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="file-text" class="w-3 h-3"></i>
                            <span>Surat Pernyataan</span>
                        </button>

                        <!-- Tab 4: Setoran Kantor -->
                        @if(Auth::user()->canManage())
                            <button type="button"
                                @click="socialSubTab = 'setoran'; $wire.set('socialSubTab', 'setoran', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                                :class="socialSubTab === 'setoran' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                                <i data-lucide="landmark" class="w-3 h-3"></i>
                                <span>Setoran Kantor</span>
                            </button>
                        @endif
                    </div>
                </div>
            @elseif(in_array($currentTab, ['kegiatan', 'kajian', 'agenda']))
                <div class="bg-white border-b border-slate-200 px-6 sm:px-6 pt-1 transition-shadow duration-200"
                    :class="isScrolled ? 'shadow-lg' : ''">
                    <div class="flex items-center overflow-x-auto no-scrollbar gap-1">
                        <!-- Tab 1: Kajian -->
                        <button type="button"
                            @click="switchSubTab('pekanan')"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="kegiatanSubTab === 'pekanan' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="calendar-days" class="w-3 h-3"></i>
                            <span>Kajian</span>
                        </button>

                        <!-- Tab 2: Khutbah Jumat -->
                        <button type="button"
                            @click="switchSubTab('jumat')"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="kegiatanSubTab === 'jumat' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="mic" class="w-3 h-3"></i>
                            <span>Khutbah Jumat</span>
                        </button>

                        <!-- Tab 3: Kegiatan Akbar -->
                        <button type="button"
                            @click="switchSubTab('agenda')"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="kegiatanSubTab === 'agenda' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="calendar-range" class="w-3 h-3"></i>
                            <span>Kegiatan Akbar</span>
                        </button>

                        <!-- Tab 4: One Day One Juz -->
                        <button type="button"
                            @click="switchSubTab('odoj')"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="kegiatanSubTab === 'odoj' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="book-open" class="w-3 h-3"></i>
                            <span>One Day One Juz</span>
                        </button>
                    </div>
                </div>
            @elseif($currentTab === 'finance')
                <div class="bg-white border-b border-slate-200 px-6 sm:px-6 pt-1 transition-shadow duration-200"
                    :class="isScrolled ? 'shadow-lg' : ''">
                    <div class="flex items-center overflow-x-auto no-scrollbar gap-1">
                        <!-- Tab 1: Kas Utama -->
                        <button type="button"
                            title="Aliran Kas Utama"
                            @click="financeSubTab = 'utama'; $wire.set('financeSubTab', 'utama', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="financeSubTab === 'utama' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="wallet" class="w-3 h-3"></i>
                            <span>Kas Utama</span>
                        </button>

                        <!-- Tab 2: Kategori -->
                        <button type="button"
                            title="Kelola Kategori Kas"
                            @click="financeSubTab = 'kategori'; $wire.set('financeSubTab', 'kategori', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="financeSubTab === 'kategori' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="tags" class="w-3 h-3"></i>
                            <span>Kategori</span>
                        </button>
                    </div>
                </div>
            @elseif($currentTab === 'users')
                <div class="bg-white border-b border-slate-200 px-6 sm:px-6 pt-1 transition-shadow duration-200"
                    :class="isScrolled ? 'shadow-lg' : ''">
                    <div class="flex items-center overflow-x-auto no-scrollbar gap-1">
                        <!-- Tab 1: Anggota -->
                        <button type="button"
                            @click="userSubTab = 'pengguna'; $wire.set('userSubTab', 'pengguna', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="userSubTab === 'pengguna' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="users" class="w-3 h-3"></i>
                            <span>Anggota</span>
                        </button>

                        <!-- Tab 2: Pegawai -->
                        <button type="button"
                            @click="userSubTab = 'pegawai'; $wire.set('userSubTab', 'pegawai', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="userSubTab === 'pegawai' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="16" rx="3"/>
                                <circle cx="9" cy="10" r="2"/>
                                <line x1="15" y1="8" x2="17" y2="8"/>
                                <line x1="15" y1="12" x2="17" y2="12"/>
                                <line x1="7" y1="16" x2="17" y2="16"/>
                            </svg>
                            <span>Pegawai</span>
                        </button>

                        <!-- Tab 3: Kepengurusan -->
                        <button type="button"
                            @click="userSubTab = 'kepengurusan'; $wire.set('userSubTab', 'kepengurusan', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="userSubTab === 'kepengurusan' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="shield-check" class="w-3 h-3"></i>
                            <span>Kepengurusan</span>
                        </button>
                    </div>
                </div>
            @elseif($currentTab === 'settings')
                <div class="bg-white border-b border-slate-200 px-6 sm:px-6 pt-1 transition-shadow duration-200"
                    :class="isScrolled ? 'shadow-lg' : ''">
                    <div class="flex items-center overflow-x-auto no-scrollbar gap-1">
                        <!-- Tab 1: Pengaturan Masjid -->
                        <button type="button"
                            @click="switchSettingsSubTab('masjid')"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="settingsSubTab === 'masjid' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="building-2" class="w-3 h-3"></i>
                            <span>Pengaturan Masjid</span>
                        </button>

                        <!-- Tab 2: Template & Studio Poster -->
                        <button type="button"
                            @click="switchSettingsSubTab('poster')"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] whitespace-nowrap shrink-0 cursor-pointer transition border-b-2"
                            :class="settingsSubTab === 'poster' ? 'font-bold text-[#0e2547] border-[#0e2547] bg-slate-50/60' : 'font-medium text-slate-500 border-transparent hover:text-slate-800'">
                            <i data-lucide="palette" class="w-3 h-3"></i>
                            <span>Template & Studio Poster</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Top Micro Progress Bar for Livewire Background Sync -->
            <div wire:loading wire:target="switchTab, gotoPage, nextPage, previousPage" class="h-0.5 w-full bg-slate-100 overflow-hidden relative z-30">
                <div class="h-full bg-linear-to-r from-amber-400 via-gov-navy to-amber-400 w-full animate-pulse"></div>
            </div>
        </div>

        <!-- Floating Auto-Dismiss Toast Notification -->
        <div x-data="{ 
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
            class="fixed top-16 inset-x-4 sm:top-auto sm:bottom-6 sm:right-6 sm:inset-x-auto z-50 flex items-center justify-between sm:justify-start gap-3 bg-gov-navy text-white px-5 py-3.5 rounded-xl shadow-2xl border border-slate-700/60 text-xs font-semibold backdrop-blur-md"
            style="display: none;">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
            <span x-text="message" class="tracking-wide"></span>
            <button type="button" @click="show = false"
                class="ml-3 text-slate-300 hover:text-white p-1 rounded-lg transition cursor-pointer" title="Tutup">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Admin Workspace Body -->
        <div @scroll.passive="isScrolled = $event.target.scrollTop > 0" class="p-6 overflow-y-auto space-y-6 flex-1 transition-opacity duration-150" wire:loading.class="opacity-60 pointer-events-none" wire:target="switchTab, gotoPage, nextPage, previousPage">

            @if(Auth::user()->isReadOnly())
                <div
                    class="bg-amber-50 border border-amber-300 rounded-xl p-3.5 flex items-center justify-between text-xs text-amber-900 shadow-2xs">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-amber-200/70 text-amber-800 flex items-center justify-center shrink-0 border border-amber-300">
                            <i data-lucide="shield-alert" class="w-4 h-4 text-amber-800"></i>
                        </div>
                        <div>
                            <span class="font-bold block">Mode Akses: Penasihat DKM (Viewer / Read-Only Protection)</span>
                            <span class="text-amber-800 text-xs font-medium">Anda hanya dapat memantau seluruh data dan mengekspor CSV/PDF.</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modular Tab Rendering based on $currentTab -->
            @if($currentTab === 'dashboard')
                <div wire:key="admin-tab-content-dashboard">
                    @include('livewire.admin.tabs._tab-dashboard')
                </div>
            @elseif($currentTab === 'kegiatan' || $currentTab === 'kajian' || $currentTab === 'agenda')
                <div wire:key="admin-tab-content-kegiatan">
                    @include('livewire.admin.tabs._tab-kegiatan')
                </div>
            @elseif($currentTab === 'petugas')
                <div wire:key="admin-tab-content-petugas">
                    @include('livewire.admin.tabs._tab-petugas')
                </div>
            @elseif($currentTab === 'finance')
                <div wire:key="admin-tab-content-finance">
                    @include('livewire.admin.tabs._tab-finance')
                </div>
            @elseif($currentTab === 'programs')
                <div wire:key="admin-tab-content-programs">
                    @include('livewire.admin.tabs._tab-programs')
                </div>
            @elseif($currentTab === 'users')
                <div wire:key="admin-tab-content-users">
                    @include('livewire.admin.tabs._tab-users')
                </div>
            @elseif($currentTab === 'settings')
                <div wire:key="admin-tab-content-settings">
                    @include('livewire.admin.tabs._tab-settings')
                </div>
            @elseif($currentTab === 'statistics' || $currentTab === 'statistik')
                <div wire:key="admin-tab-content-statistics">
                    @include('livewire.admin.tabs._tab-statistics')
                </div>
            @elseif($currentTab === 'galeri' || $currentTab === 'gallery')
                <div wire:key="admin-tab-content-galeri">
                    @include('livewire.admin.tabs._tab-galeri')
                </div>
            @elseif($currentTab === 'saran')
                <div wire:key="admin-tab-content-saran">
                    @include('livewire.admin.tabs._tab-saran')
                </div>
            @endif
        </div>
    </div>


    <!-- Mobile Floating Action Button (FAB) & Bottom Action Sheet -->
    @include('livewire.admin.tabs._mobile-action-sheet')

    <!-- Ustadz Data Container (Decoupled from x-data attribute to prevent Alpine re-initialization) -->
    <div id="ustadzDataContainer" class="hidden"
        data-photomap="{{ json_encode($ustadzPhotoMap ?? []) }}"
        data-directory="{{ json_encode($ustadzListForDatalist ?? []) }}"></div>

    <!-- All Modal Dialogs -->
    @include('livewire.admin.tabs._modals')

</div>
