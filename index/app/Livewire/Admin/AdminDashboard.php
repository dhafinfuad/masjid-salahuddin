<?php

namespace App\Livewire\Admin;

use App\Models\ActivityGallery;
use App\Models\Agenda;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Event;
use App\Models\FeedbackSuggestion;
use App\Models\Finance;
use App\Models\FinanceCategory;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\Material;
use App\Models\OdojEntry;
use App\Models\PosterSetting;
use App\Models\PrayerDuty;
use App\Models\ProgramParticipant;
use App\Models\Registrant;
use App\Models\SocialProgram;
use App\Models\User;
use App\Models\Ustadz;
use App\Models\VisitorLog;
use App\Services\ImageOptimizerService;
use App\Services\PotonganMasjidSyncService;
use App\Services\PrayerTimeService;
use App\Services\SimpleXlsxService;
use App\Services\TteSignatureService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AdminDashboard extends Component
{
    use WithPagination;
    use WithFileUploads;

    #[Url(as: 'tab')]
    public string $currentTab = 'dashboard';
    public string $kegiatanSubTab = 'pekanan';
    public string $kajianSubTab = 'pekanan';
    public string $settingsSubTab = 'masjid'; // 'masjid' | 'poster'

    public string $search = '';
    public string $pekananSearch = '';
    public string $jumatSearch = '';
    public string $agendaSearch = '';
    public string $toastMessage = '';

    // ==========================================
    // Table Sorting State
    // ==========================================
    public string $sortField = '';
    public string $sortDirection = 'asc';
    public array $tableSorts = [];

    // ==========================================
    // Event Management State
    // ==========================================
    public bool $showEventModal = false;
    public bool $isEditingEvent = false;
    public ?int $editingEventId = null;
    public string $eventTitle = '';
    public ?int $eventCategoryId = null;
    public string $eventSpeakerName = '';
    public string $eventSpeakerRole = '';
    public string $eventDate = '';
    public string $eventTimeDisplay = '';
    public string $eventLocation = 'Ruang Utama Masjid';
    public int $eventCapacity = 100;
    public string $eventStatus = 'DRAF';
    public string $eventDescription = '';

    public string $eventFilterStatus = 'all';

    // ==========================================
    // Category Management State
    // ==========================================
    public bool $showCategoryModal = false;
    public bool $isEditingCategory = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryColor = 'emerald';
    public string $categoryDescription = '';
    public string $categoryFilterStatus = 'all';

    // ==========================================
    // Registrants Management State
    // ==========================================
    public string $regFilterEvent = 'all';
    public string $regFilterStatus = 'all';
    public string $quickTicketCode = '';

    // ==========================================
    // Materials Management State
    // ==========================================
    public bool $showMaterialModal = false;
    public string $materialTitle = '';
    public ?int $materialEventId = null;
    public string $materialYoutube = '';
    public string $materialNotes = '';

    // ==========================================
    // Gallery Management State
    // ==========================================
    public string $gallerySearch = '';
    public string $galleryYearFilter = 'all';
    public bool $showGalleryModal = false;
    public bool $isEditingGallery = false;
    public ?int $editingGalleryId = null;
    public string $galleryTitle = '';
    public string $galleryDate = '';
    public string $galleryLocation = 'Masjid Salahuddin, KPP Madya Malang';
    public $galleryUploadedPhotos = [];
    public array $galleryExistingPhotos = [];

    // ==========================================
    // Saran & Kritik Management State
    // ==========================================
    public string $saranSearch = '';
    public string $saranStatusFilter = 'all'; // 'all', 'baru', 'dibaca', 'ditindaklanjuti', 'arsip'
    public string $saranCategoryFilter = 'all';
    public ?int $selectedSaranId = null;
    public ?FeedbackSuggestion $selectedSaran = null;
    public string $saranReplyText = '';
    public string $saranUpdateStatus = 'dibaca';
    public bool $saranUpdateIsPublic = false;
    public bool $showSaranDetailModal = false;

    // ==========================================
    // Masjid Settings State
    // ==========================================
    public string $settingsName = '';
    public string $settingsAddress = '';
    public string $settingsPhone = '';
    public string $settingsEmail = '';
    public string $settingsCityId = '1634';
    public string $settingsCityName = 'KOTA MALANG';
    public float $settingsQiblaAngle = 295.12;
    public int $settingsIqamahDelay = 10;
    public string $settingsBankName = 'BSI (Bank Syariah Indonesia)';
    public string $settingsBankAccount = '7123-4567-89';
    public string $settingsBankHolder = 'DKM Masjid Salahuddin';
    public string $settingsTvAnnouncementsText = '';
    public string $settingsFridayKhatib = '';
    public string $settingsFridayImam = '';
    public string $settingsFridayMuadzin = '';

    // ==========================================
    // Kajian Management State (Milestone 1)
    // ==========================================
    public bool $showKajianModal = false;
    public bool $isEditingKajian = false;
    public ?int $editingKajianId = null;
    public string $kajianType = 'pekanan'; // 'pekanan', 'jumat', or 'tematik'
    public string $kajianDate = '';
    public string $kajianTimeDisplay = '';
    public string $kajianTitle = '';
    public string $kajianSpeakerName = '';
    public string $kajianSpeakerPhone = '';
    public mixed $kajianSpeakerPhoto = null;
    public ?string $kajianExistingPhoto = null;
    public bool $kajianIsHolidayDisabled = false;
    public string $kajianKhatibName = '';
    public string $kajianMcName = '';
    public string $kajianMuadzinName = '';
    public string $kajianKhatibPhone = '';
    public string $kajianMcNotes = '';
    public string $kajianMonthFilter = 'all';
    public string $kajianYearFilter = 'all';
    public string $kajianYoutubeUrl = '';

    // Notula Kajian State (Role Master)
    public bool $showNotulaModal = false;
    public ?int $notulaKajianId = null;
    public string $notulaRawText = '';
    public string $notulaTitle = '';
    public string $notulaSpeaker = '';
    public string $notulaDate = '';
    public string $notulaTime = '';
    public string $notulaType = '';

    // Link YouTube Kegiatan State
    public bool $showYoutubeModal = false;
    public ?int $youtubeItemId = null;
    public string $youtubeItemType = 'kajian'; // 'kajian' or 'agenda'
    public string $youtubeItemTitle = '';
    public string $youtubeItemSubtitle = '';
    public string $youtubeItemDate = '';
    public string $youtubeUrl = '';

    // Ustadz Directory State
    public bool $showUstadzModal = false;
    public string $ustadzSearch = '';
    public ?int $editingUstadzId = null;
    public string $ustadzName = '';
    public string $ustadzTitle = '';
    public string $ustadzPhone = '';
    public mixed $ustadzPhoto = null;
    public ?string $ustadzExistingPhoto = null;

    // Import Kajian State (Copypaste Excel & CSV Upload)
    public bool $showImportKajianModal = false;
    public string $importKajianTab = 'paste'; // 'paste' | 'file'
    public string $importKajianType = 'pekanan';
    public string $importKajianPasteText = '';
    public mixed $importKajianFile = null;

    // Prayer Duty / Penugasan Ibadah State (Milestone 2)
    public string $prayerDutyFilterWeek = 'all'; // 'all', '1', '2', '3', '4', '5'
    public string $prayerDutyFilterMonth = 'all'; // 'all', '1', '2', ..., '12'
    public string $prayerDutyFilterYear = 'all'; // 'all', '2026', '2025', ...
    public string $prayerDutyFilterPrayerTime = 'all'; // 'all', 'dzuhur', 'ashar'
    public bool $showPrayerDutyModal = false;
    public bool $isEditingPrayerDuty = false;
    public ?int $editingPrayerDutyId = null;
    public string $dutyDayName = 'Senin';
    public string $dutyPrayerTime = 'dzuhur';
    public string $dutyWeekPattern = 'semua';
    public ?int $dutyTahun = null;
    public string $dutyImamName = '';
    public string $dutyMuadzinName = '';
    public string $dutyNotes = '';

    // Import Prayer Duty State
    public bool $showImportDutyModal = false;
    public string $importDutyTab = 'paste'; // 'paste' | 'file'
    public string $importDutyPasteText = '';
    public mixed $importDutyFile = null;

    // ==========================================
    // Agenda & ODOJ State (Milestone 3)
    // ==========================================
    public bool $showAgendaModal = false;
    public bool $isEditingAgenda = false;
    public ?int $editingAgendaId = null;
    public string $agendaTitle = '';
    public string $agendaDescription = '';
    public string $agendaDate = '';
    public string $agendaCommitteeMembers = '';
    public string $agendaBudget = '0';
    public string $agendaStatus = 'Direncanakan';
    public string $agendaReportSummary = '';
    public mixed $agendaReportPdf = null;
    public string $agendaYoutubeUrl = '';

    public string $agendaFilterStatus = 'all'; // 'all', 'Direncanakan', 'Berjalan', 'SELESAI'
    public string $agendaSubTab = 'agenda'; // 'agenda' or 'odoj'
    public string $agendaViewMode = 'grid'; // 'grid', 'table', 'report'
    public ?int $selectedAgendaForReportId = null;

    // Import Agenda State
    public bool $showImportAgendaModal = false;
    public string $importAgendaTab = 'paste'; // 'paste' | 'file'
    public string $importAgendaPasteText = '';
    public mixed $importAgendaFile = null;

    // ODOJ State
    public string $odojDate = '';
    public bool $showAssignOdojModal = false;
    public int $assignJuzNumber = 1;
    public string $assignPegawai1 = '';
    public string $assignPegawai2 = '';
    // ==========================================
    // User Management State (Milestone 4)
    // ==========================================
    public bool $showUserModal = false;
    public bool $isEditingUser = false;
    public ?int $editingUserId = null;
    public string $userName = '';
    public string $userEmail = '';
    public string $userPassword = '';
    public string $userRole = 'Jamaah';
    public string $userStatus = 'AKTIF';
    public string $userFilterRole = 'all';


    // ==========================================
    // Takmir & Kepengurusan State (Subtab under /admin/users)
    // ==========================================
    public string $userSubTab = 'pengguna'; // 'pengguna' | 'pegawai' | 'kepengurusan'
    public string $takmirActiveSection = 'dokumen'; // 'dokumen' | 'struktur' | 'tupoksi'

    // ==========================================
    // Master Data Pegawai Muslim KPP Madya Malang State
    // ==========================================
    public string $employeeSearch = '';
    public string $employeeFilterStatus = 'all'; // 'all' | 'AKTIF' | 'MUTASI' | 'NONAKTIF'
    public string $employeeFilterLink = 'all'; // 'all' | 'linked' | 'unlinked'

    // Form Modal Pegawai (Tambah / Edit)
    public bool $showEmployeeModal = false;
    public ?int $editingEmployeeId = null;
    public string $employeeFormNipPendek = '';
    public string $employeeFormNipPanjang = '';
    public string $employeeFormName = '';
    public string $employeeFormJabatan = '';
    public string $employeeFormUnitKerja = 'KPP Madya Malang';
    public string $employeeFormGender = '';
    public string $employeeFormStatus = 'AKTIF';

    // Modal Import Pegawai Excel/CSV
    public bool $showImportEmployeeModal = false;
    public mixed $importEmployeeFile = null;
    public bool $importEmployeeAutoLink = true;
    public bool $importEmployeeOnlyMuslim = true;
    public ?array $importEmployeeResult = null;

    // Upload & Dokumen SK
    public bool $showUploadDocModal = false;
    public string $uploadDocTarget = 'sk'; // 'sk' | 'lampiran1' | 'lampiran2'
    public string $uploadDocTitle = '';
    public mixed $uploadDocFile = null;
    public string $uploadDocNumber = '';
    public string $uploadDocDate = '';
    public string $uploadDocPages = '';

    // Template Dokumen KEP Takmir State
    public bool $showTemplateKepModal = false;
    public string $templateKepActiveTab = 'identitas'; // 'identitas' | 'pimpinan' | 'bidang' | 'upload'
    public string $docSkPeriode = '2026-2029';
    public mixed $templateKepPdfFile = null;

    public string $docSkNumber = 'KEP-48/KPP.1209/2026';
    public string $docSkDate = '14 April 2026';
    public string $docSkTitle = 'Surat Keputusan Kepala KPP Madya Malang';
    public string $docSkDescription = 'Keputusan tentang Perubahan Susunan Pengurus Takmir Masjid Sholahuddin KPP Madya Malang Periode 2026-2029.';

    public string $docLampiran1Title = 'Lampiran I: Susunan Pengurus Takmir';
    public string $docLampiran1Description = 'Daftar nama lengkap susunan pimpinan, sekretaris, bendahara, pengelola bidang, dan seluruh anggota pengurus Takmir periode 2026-2029.';

    public string $docLampiran2Title = 'Lampiran II: Penjabaran Tugas & Wewenang (Tupoksi)';
    public string $docLampiran2Description = 'Rincian tugas pokok dan fungsi (Tupoksi) Pembina, Ketua, Wakil Ketua, Sekretaris, Bendahara, serta 4 Bidang Pengelola Takmir.';

    // Struktur Pengurus Form
    public string $pembinaRole = 'Pembina';
    public string $pembinaTitle = 'Kepala Kantor Pelayanan Pajak Madya Malang';
    public string $pembinaName = 'Teguh Iman Wirotomo';
    public string $pembinaTupoksi = '';

    public string $ketuaRole = 'Ketua Takmir';
    public string $ketuaName = 'Moh. Nazil Fuadi Kusmawan';
    public string $ketuaTupoksi = '';

    public string $wakilKetuaRole = 'Wakil Ketua Takmir';
    public string $wakilKetuaName = 'Mujiburrokhman';
    public string $wakilKetuaTupoksi = '';

    public string $sekretarisRole = 'Sekretaris';
    public string $sekretarisNames = '';
    public string $sekretarisTupoksi = '';

    public string $bendaharaRole = 'Bendahara';
    public string $bendaharaNames = '';
    public string $bendaharaTupoksi = '';

    // 4 Bidang
    public string $bidang0Name = 'Bidang Dakwah dan Perayaan Hari Besar Islam (PHBI)';
    public string $bidang0Pengelola = '';
    public string $bidang0Anggota = '';
    public string $bidang0Tupoksi = '';

    public string $bidang1Name = 'Bidang Humas dan Sosial';
    public string $bidang1Pengelola = '';
    public string $bidang1Anggota = '';
    public string $bidang1Tupoksi = '';

    public string $bidang2Name = 'Bidang Rumah Tangga dan Sarana Prasarana';
    public string $bidang2Pengelola = '';
    public string $bidang2Anggota = '';
    public string $bidang2Tupoksi = '';

    public string $bidang3Name = 'Bidang Keputrian';
    public string $bidang3Pengelola = '';
    public string $bidang3Anggota = '';
    public string $bidang3Tupoksi = '';

    // ==========================================
    // Finance & Program Monitoring State (Milestone 5)
    // ==========================================
    public string $financeSubTab = 'utama'; // 'utama' | 'program' | 'kategori'
    public string $financeTypeFilter = 'all'; // 'all' | 'pemasukan' | 'pengeluaran'
    public array|string $financeMonthFilter = [];
    public string $financeYearFilter = 'all';
    public string $financeSearch = '';

    // Catat Transaksi Modal
    public bool $showFinanceModal = false;
    public bool $isEditingFinance = false;
    public ?int $editingFinanceId = null;
    public string $financeType = 'pemasukan';
    public ?int $financeCategoryId = null;
    public ?int $financeAgendaId = null;
    public string $financeProgramName = 'Kas Umum';
    public string $financeAmount = '';
    public string $financeDate = '';
    public string $financeDescription = '';
    public mixed $financeReceiptFile = null;
    public $financeReceiptFiles = [];
    public array $existingFinanceReceiptPaths = [];
    public ?string $currentFinanceReceiptPath = null;

    // Pos Anggaran / Kategori Modal (CRUD Lengkap)
    public bool $showFinanceCategoryModal = false;
    public bool $isEditingFinanceCategory = false;
    public ?int $editingFinanceCategoryId = null;
    public string $newCategoryName = '';
    public string $newCategoryGroup = 'pengeluaran_rutin'; // 'penerimaan' | 'pengeluaran_rutin' | 'pengeluaran_nonrutin'
    public string $newCategoryType = 'pengeluaran';
    public string $newCategoryColor = 'emerald';

    // Impor Kas Modal
    public bool $showFinanceImportModal = false;
    public string $importFinanceTab = 'paste'; // 'paste' | 'file'
    public string $importFinancePasteText = '';
    public mixed $importFinanceFile = null;

    // Cetak Laporan PDF Modal (Kas Bulanan & LPJ Program Tematik)
    public bool $showPrintFinanceModal = false;
    public string $printReportType = 'monthly'; // 'monthly' | 'agenda'
    public string $printMonth = '';
    public string $printYear = '';
    public ?int $printAgendaId = null;
    public bool $printWithTte = true;

    // Pemantauan Kas Program: Sub-Tab
    public string $programSubView = 'ringkasan'; // 'ringkasan' | 'peserta' | 'potongan' | 'penerimaan'
    public string $participantProgramFilter = 'all';
    public string $participantSearch = '';

    // Peserta Form Modal
    public bool $showParticipantModal = false;
    public bool $isEditingParticipant = false;
    public ?int $editingParticipantId = null;
    public string $participantName = '';
    public string $participantProgram = 'Santunan Anak Yatim';
    public string $participantAmount = '250000';
    public string $participantPeriod = '';
    public string $participantStatus = '';

    // Import & Sinkronisasi Potongan Bulanan
    public bool $showImportPotonganModal = false;
    public mixed $potonganFile = null;
    public ?array $importPotonganPreview = null;
    public string $importPotonganError = '';

    // Setoran Glondongan Kantor (Penerimaan Kantor)
    public string $lumpSumAmount = '';
    public string $lumpSumDate = '';
    public ?int $lumpSumCategoryId = null;
    public string $lumpSumProgram = 'Infaq Rutin';
    public string $lumpSumNotes = '';
    public bool $showLumpSumModal = false;

    // TTE Pengesahan Digital
    public bool $tteSigned = false;

    // ==========================================
    // Social Programs State (Milestone 6)
    // ==========================================
    public string $socialSubTab = 'katalog'; // 'katalog' | 'peserta' | 'pernyataan' | 'setoran'
    public string $socialProgramFilter = 'all';
    public string $socialParticipantSearch = '';
    // Filter katalog program sosial (khusus tampilan Jamaah)
    public string $catalogSearch = '';
    public string $catalogMonth = '';
    public string $catalogYear = '';
    public array|string $socialParticipantPeriodFilter = [];
    public string $socialParticipantStatusFilter = 'all'; // deprecated alias

    // Program Modal (Tambah / Edit Program Sosial)
    public bool $showSocialProgramModal = false;
    public bool $isEditingSocialProgram = false;
    public ?int $editingSocialProgramId = null;
    public string $socialProgramName = '';
    public string $socialProgramSlug = '';
    public string $socialProgramCategory = 'sosial'; // 'yatim' | 'infaq' | 'zakat' | 'qurban' | 'sosial'
    public string $socialProgramDescription = '';
    public string $socialProgramPeriodType = 'bulanan'; // 'bulanan' | 'tahunan' | 'insidental'
    public string $socialProgramStatus = 'AKTIF'; // 'AKTIF' | 'DITUTUP' | 'SELESAI'
    public string $socialProgramIcon = 'heart-handshake';
    
    // ==========================================
    // Cetak & Impor Scan PDF Pernyataan Potongan Tukin
    // ==========================================
    public bool $showPrintPernyataanModal = false;
    public string $pernyataanModalTab = 'cetak'; // 'cetak' | 'impor'
    public mixed $scanPernyataanFile = null;
    public bool $scanProcessing = false;
    public string $scanError = '';
    public array $scanPages = [];
    public int $activeScanPageIndex = 0;

    // ==========================================
    // Visitor Statistics State
    // ==========================================
    public string $statsPeriod = '7_days'; // 'today' | '7_days' | '30_days' | 'all'
    public string $statsSearch = '';

    public function mount(?string $tab = null): void
    {
        if (Auth::check() && strtoupper(Auth::user()->status ?? 'AKTIF') !== 'AKTIF') {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            session()->flash('error', 'Anda sudah bukan lagi pegawai KPP Madya Malang.');
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $tab = $tab ?: request()->query('tab');
        if ($tab && in_array($tab, ['events', 'categories', 'registrants', 'materials'])) {
            $tab = 'kegiatan';
            $this->kegiatanSubTab = 'pekanan';
        }

        if ($tab === 'kajian') {
            $this->kegiatanSubTab = 'pekanan';
        } elseif ($tab === 'agenda') {
            $this->kegiatanSubTab = 'agenda';
        }

        if ($tab && Auth::check() && Auth::user()->isJamaah() && in_array($tab, ['saran', 'users', 'statistics', 'statistik', 'settings'])) {
            $this->redirect(url('/admin/dashboard'), navigate: true);
            return;
        }

        if ($tab && in_array($tab, ['dashboard', 'kegiatan', 'kajian', 'petugas', 'agenda', 'finance', 'programs', 'users', 'settings', 'statistics', 'statistik', 'galeri', 'gallery', 'saran'])) {
            $this->currentTab = ($tab === 'statistik') ? 'statistics' : (($tab === 'gallery') ? 'galeri' : $tab);
        }

        $subtab = request()->query('subtab') ?: request()->query('socialSubTab');
        if ($subtab && in_array($subtab, ['katalog', 'peserta', 'pernyataan', 'setoran'], true)) {
            $this->socialSubTab = $subtab;
        }

        $this->financeDate = Carbon::today()->format('Y-m-d');
        $this->lumpSumDate = Carbon::today()->format('Y-m-d');
        $this->financeMonthFilter = [(string) Carbon::today()->month];
        $this->financeYearFilter = (string) Carbon::today()->year;
        $this->financeCategoryId = $this->getDefaultFinanceCategoryId('pemasukan');

        $this->agendaDate = Carbon::now()->addDays(7)->format('Y-m-d');
        $this->catalogMonth = (string) Carbon::now('Asia/Jakarta')->month;
        $this->catalogYear = (string) Carbon::now('Asia/Jakarta')->year;
        $this->odojDate = Carbon::today()->format('Y-m-d');

        if (! session()->has('temp_scan_pdf_path') || ! file_exists(session('temp_scan_pdf_path'))) {
            $defaultScanPdf = storage_path('app/private/scan_mesin.pdf');
            if (file_exists($defaultScanPdf)) {
                session([
                    'temp_scan_pdf_path' => $defaultScanPdf,
                    'temp_scan_pdf_time' => time(),
                ]);
            }
        }

        /** @var MasjidSetting $settings */
        $settings = MasjidSetting::getActive();
        $this->settingsName = $settings->name;
        $this->settingsAddress = $settings->address ?? '';
        $this->settingsPhone = $settings->phone ?? '';
        $this->settingsEmail = $settings->email ?? '';
        $this->settingsCityId = $settings->city_id ?: '1634';
        $this->settingsCityName = $settings->city_name ?: 'KOTA MALANG';
        $this->settingsQiblaAngle = (float) $settings->qibla_angle;
        $this->settingsIqamahDelay = (int) ($settings->iqamah_delay_minutes ?: 10);

        $banks = $settings->bank_accounts ?? [];
        if (!empty($banks[0])) {
            $this->settingsBankName = $banks[0]['bank'] ?? 'BSI (Bank Syariah Indonesia)';
            $this->settingsBankAccount = $banks[0]['account_number'] ?? '7123-4567-89';
            $this->settingsBankHolder = $banks[0]['holder'] ?? 'DKM Masjid Salahuddin';
        }

        $this->settingsTvAnnouncementsText = implode("\n", $settings->tv_announcements ?? []);

        $friday = $settings->friday_prayer_info ?? [];
        $this->settingsFridayKhatib = $friday['khatib'] ?? '';
        $this->settingsFridayImam = $friday['imam'] ?? '';
        $this->settingsFridayMuadzin = $friday['muadzin'] ?? '';

        // Inisialisasi default filter pekan & tahun petugas ke periode berjalan saat ini
        $now = Carbon::now('Asia/Jakarta');
        $currentWeekNumber = PrayerDuty::getWeekOfMonth($now);
        $this->prayerDutyFilterWeek = (string) $currentWeekNumber;
        $this->prayerDutyFilterMonth = (string) $now->month;
        $this->prayerDutyFilterYear = (string) $now->year;

        [$kajianDefaultMonth, $kajianDefaultYear] = $this->getDefaultKajianFilterPeriod($now);
        $this->kajianMonthFilter = (string) $kajianDefaultMonth;
        $this->kajianYearFilter = (string) $kajianDefaultYear;

        if (request()->query('subtab') === 'kepengurusan') {
            $this->userSubTab = 'kepengurusan';
        }

        if (request()->query('subtab') === 'poster') {
            $this->settingsSubTab = 'poster';
        }

        $this->loadTakmirDataFromSettings();

        if ($this->currentTab === 'users') {
            try {
                \App\Services\EmployeeStatusSyncService::syncFromEmployeesTable(Auth::id());
            } catch (\Throwable $e) {
                // Abaikan jika tabel employees belum siap
            }
        }
    }

    public function switchTab(string $tab): void
    {
        if (Auth::check() && Auth::user()->isJamaah() && in_array($tab, ['saran', 'users', 'statistics', 'statistik', 'settings'])) {
            return;
        }

        $this->currentTab = $tab;
        if ($tab === 'kajian') {
            $this->kegiatanSubTab = 'pekanan';
        } elseif ($tab === 'agenda') {
            $this->kegiatanSubTab = 'agenda';
        } elseif ($tab === 'users') {
            try {
                \App\Services\EmployeeStatusSyncService::syncFromEmployeesTable(Auth::id());
            } catch (\Throwable $e) {
                // Abaikan jika tabel employees belum siap
            }
        }
        $this->search = '';
        $this->toastMessage = '';
    }

    public function updatedFinanceAmount(mixed $val): void
    {
        $this->financeAmount = preg_replace('/\D/', '', (string) $val);
    }

    public function updatedLumpSumAmount(mixed $val): void
    {
        $this->lumpSumAmount = preg_replace('/\D/', '', (string) $val);
    }

    public function updatedParticipantAmount(mixed $val): void
    {
        $this->participantAmount = preg_replace('/\D/', '', (string) $val);
    }

    public function getBreadcrumbTitle(): string
    {
        if ($this->currentTab === 'dashboard') {
            return 'Dashboard';
        }

        if (in_array($this->currentTab, ['kegiatan', 'kajian', 'agenda'])) {
            return match ($this->kegiatanSubTab) {
                'pekanan' => 'Kegiatan > Kajian Pekanan',
                'jumat' => 'Kegiatan > Kajian Jumat & Khutbah',
                'agenda' => 'Kegiatan > Kegiatan Akbar',
                'odoj' => 'Kegiatan > One Day One Juz',
                default => 'Kegiatan',
            };
        }

        if ($this->currentTab === 'petugas') {
            return 'Petugas Shalat';
        }

        if ($this->currentTab === 'finance') {
            if ($this->financeSubTab === 'utama') {
                return 'Kas & Keuangan > Kas Utama';
            }
            if ($this->financeSubTab === 'program') {
                return match ($this->programSubView) {
                    'ringkasan' => 'Kas & Keuangan > Kas Program > Ringkasan',
                    'peserta' => 'Kas & Keuangan > Kas Program > Peserta',
                    'potongan' => 'Kas & Keuangan > Kas Program > Potongan',
                    'penerimaan' => 'Kas & Keuangan > Kas Program > Setoran Kantor',
                    default => 'Kas & Keuangan > Kas Program',
                };
            }
            if ($this->financeSubTab === 'kategori') {
                return 'Kas & Keuangan > Kategori';
            }
            return 'Kas & Keuangan';
        }

        if ($this->currentTab === 'programs') {
            return match ($this->socialSubTab) {
                'katalog' => 'Program Sosial > Katalog Program',
                'peserta' => 'Program Sosial > Rekapitulasi Peserta',
                'pernyataan' => 'Program Sosial > Surat Pernyataan',
                'setoran' => 'Program Sosial > Setoran Kantor',
                default => 'Program Sosial',
            };
        }

        if ($this->currentTab === 'galeri' || $this->currentTab === 'gallery') {
            return 'Galeri Kegiatan';
        }

        if ($this->currentTab === 'saran') {
            return 'Saran & Kritik';
        }

        if ($this->currentTab === 'users') {
            return 'Pengguna & Role';
        }

        if ($this->currentTab === 'settings') {
            return match ($this->settingsSubTab) {
                'poster' => 'Pengaturan > Template & Studio Poster Kajian',
                default => 'Pengaturan',
            };
        }

        if ($this->currentTab === 'statistics' || $this->currentTab === 'statistik') {
            return 'Sistem > Statistik Pengunjung';
        }

        return ucfirst($this->currentTab);
    }

    public function getPageTitle(): string
    {
        $tabTitle = match ($this->currentTab) {
            'dashboard' => 'Dashboard',
            'kegiatan', 'kajian', 'agenda' => match ($this->kegiatanSubTab) {
                'pekanan' => 'Kajian',
                'jumat' => 'Kajian Jumat & Khutbah',
                'agenda' => 'Kegiatan Akbar',
                'odoj' => 'One Day One Juz',
                default => 'Kajian',
            },
            'petugas' => 'Petugas Shalat',
            'finance' => match ($this->financeSubTab) {
                'program' => 'Kas Program',
                'kategori' => 'Kategori Kas',
                default => 'Kas & Keuangan',
            },
            'programs' => match ($this->socialSubTab) {
                'peserta' => 'Rekapitulasi Peserta',
                'pernyataan' => 'Surat Pernyataan Potongan Tukin',
                'setoran' => 'Setoran Kantor',
                default => 'Program Sosial',
            },
            'users' => match ($this->userSubTab) {
                'pegawai' => 'Data Pegawai Muslim KPP Madya Malang',
                'kepengurusan' => 'Takmir & Kepengurusan',
                default => 'Manajemen Pengguna',
            },
            'settings' => match ($this->settingsSubTab) {
                'poster' => 'Template & Studio Poster Kajian',
                default => 'Pengaturan',
            },
            'statistics', 'statistik' => 'Statistik Pengunjung',
            'galeri', 'gallery' => 'Galeri Kegiatan',
            'saran' => 'Saran & Kritik Jamaah',
            default => 'Admin Dashboard',
        };

        return "{$tabTitle} — Masjid Salahuddin";
    }

    public function switchKegiatanSubTab(string $subTab): void
    {
        $this->kegiatanSubTab = $subTab;
        if (in_array($subTab, ['pekanan', 'jumat'])) {
            $this->kajianSubTab = $subTab;
        } elseif (in_array($subTab, ['agenda', 'odoj'])) {
            $this->agendaSubTab = $subTab;
        }
    }

    public function switchSettingsSubTab(string $subTab): void
    {
        $this->settingsSubTab = $subTab;
    }

    public function setStatsPeriod(string $period): void
    {
        $this->statsPeriod = $period;
        $this->resetPage('statsPage');
    }

    public function updatedStatsSearch(): void
    {
        $this->resetPage('statsPage');
    }

    public function sortBy(string $field, ?string $table = null): void
    {
        if (str_contains($field, '.')) {
            [$prefix, $col] = explode('.', $field, 2);
            $targetTable = $table ?: $prefix;
            $targetField = $col;
        } else {
            $targetTable = $table ?: $this->resolveCurrentTableKey();
            $targetField = $field;
        }

        $currentDir = $this->tableSorts[$targetTable]['direction'] ?? null;
        $currentField = $this->tableSorts[$targetTable]['field'] ?? null;

        if ($currentField === $targetField) {
            $newDir = ($currentDir === 'asc') ? 'desc' : 'asc';
        } else {
            $newDir = 'asc';
        }

        $this->tableSorts[$targetTable] = [
            'field' => $targetField,
            'direction' => $newDir,
        ];

        $this->sortField = $targetField;
        $this->sortDirection = $newDir;

        $pageMap = [
            'pekanan' => 'pekananPage',
            'jumat' => 'jumatPage',
            'petugas' => 'dutyPage',
            'agenda' => 'agendaPage',
            'finance' => 'financePage',
            'participants' => 'participantPage',
            'social_participants' => 'socialParticipantPage',
            'users' => 'userPage',
        ];
        if (isset($pageMap[$targetTable])) {
            $this->resetPage($pageMap[$targetTable]);
        }
    }

    // ==========================================
    // Pagination Reset Hooks
    // ==========================================
    public function updatedSearch(): void
    {
        $this->resetPage('pekananPage');
        $this->resetPage('jumatPage');
        $this->resetPage('dutyPage');
        $this->resetPage('agendaPage');
        $this->resetPage('userPage');
        $this->resetPage('financePage');
    }

    public function updatedPekananSearch(): void
    {
        $this->resetPage('pekananPage');
    }

    public function updatedJumatSearch(): void
    {
        $this->resetPage('jumatPage');
    }

    public function updatedAgendaSearch(): void
    {
        $this->resetPage('agendaPage');
    }

    public function updatedFinanceSearch(): void
    {
        $this->resetPage('financePage');
    }

    public function updatedParticipantSearch(): void
    {
        $this->resetPage('participantPage');
    }

    public function updatedSocialParticipantSearch(): void
    {
        $this->resetPage('socialParticipantPage');
    }

    public function updatedKajianMonthFilter(): void
    {
        $this->resetPage('pekananPage');
        $this->resetPage('jumatPage');
    }

    public function updatedKajianYearFilter(): void
    {
        $this->resetPage('pekananPage');
        $this->resetPage('jumatPage');
    }

    public function updatedFinanceTypeFilter(): void
    {
        $this->resetPage('financePage');
    }

    public function updatedFinanceMonthFilter(): void
    {
        $this->resetPage('financePage');
    }

    public function updatedFinanceYearFilter(): void
    {
        $this->resetPage('financePage');
    }

    public function updatedPrayerDutyFilterYear(): void
    {
        $this->resetPage('dutyPage');
    }

    public function updatedPrayerDutyFilterPrayerTime(): void
    {
        $this->resetPage('dutyPage');
    }

    public function updatedPrayerDutyFilterWeek(): void
    {
        $this->resetPage('dutyPage');
    }

    public function updatedPrayerDutyFilterMonth(): void
    {
        $this->resetPage('dutyPage');
    }

    public function updatedAgendaFilterStatus(): void
    {
        $this->resetPage('agendaPage');
    }

    public function updatedUserFilterRole(): void
    {
        $this->resetPage('userPage');
    }

    public function updatedParticipantProgramFilter(): void
    {
        $this->resetPage('participantPage');
    }

    public function updatedSocialProgramFilter(): void
    {
        $this->resetPage('socialParticipantPage');
    }

    public function updatedSocialParticipantStatusFilter(): void
    {
        $this->resetPage('socialParticipantPage');
    }

    public function isSorted(string $field, ?string $table = null): bool
    {
        if (str_contains($field, '.')) {
            [$prefix, $col] = explode('.', $field, 2);
            $targetTable = $table ?: $prefix;
            $targetField = $col;
        } else {
            $targetTable = $table ?: $this->resolveCurrentTableKey();
            $targetField = $field;
        }

        if (isset($this->tableSorts[$targetTable]['field'])) {
            return $this->tableSorts[$targetTable]['field'] === $targetField;
        }

        return $this->sortField === $targetField;
    }

    public function getSortDirection(string $field, ?string $table = null): string
    {
        if (str_contains($field, '.')) {
            [$prefix, $col] = explode('.', $field, 2);
            $targetTable = $table ?: $prefix;
        } else {
            $targetTable = $table ?: $this->resolveCurrentTableKey();
        }

        if (isset($this->tableSorts[$targetTable]['direction'])) {
            return $this->tableSorts[$targetTable]['direction'];
        }

        return $this->sortDirection ?: 'asc';
    }

    protected function resolveCurrentTableKey(): string
    {
        return match ($this->currentTab) {
            'kegiatan' => match ($this->kegiatanSubTab) {
                'jumat' => 'jumat',
                'agenda' => 'agenda',
                'odoj' => 'odoj',
                default => 'pekanan',
            },
            'kajian' => $this->kajianSubTab === 'jumat' ? 'jumat' : 'pekanan',
            'agenda' => 'agenda',
            'programs' => 'participants',
            'social_programs' => 'social_participants',
            default => $this->currentTab,
        };
    }

    public function notify(string $msg): void
    {
        $this->toastMessage = $msg;
        $this->dispatch('toast', message: $msg);
    }

    // ==========================================
    // Event Actions
    // ==========================================
    public function openCreateEventModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->isEditingEvent = false;
        $this->editingEventId = null;
        $this->reset(['eventTitle', 'eventCategoryId', 'eventSpeakerName', 'eventSpeakerRole', 'eventDate', 'eventTimeDisplay', 'eventDescription']);
        $this->eventLocation = 'Ruang Utama Masjid';
        $this->eventCapacity = 100;
        $this->eventStatus = 'DRAF';
        $this->eventDate = Carbon::now()->addDays(2)->format('Y-m-d');
        $this->showEventModal = true;
        $this->dispatch('open-event-modal');
    }

    public function openEditEventModal(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $event = Event::findOrFail($id);
        $this->isEditingEvent = true;
        $this->editingEventId = $id;
        $this->eventTitle = $event->title;
        $this->eventCategoryId = $event->category_id;
        $this->eventSpeakerName = $event->speaker_name;
        $this->eventSpeakerRole = $event->speaker_role ?? '';
        $this->eventDate = Carbon::parse($event->event_date)->format('Y-m-d');
        $this->eventTimeDisplay = $event->time_display ?? '';
        $this->eventLocation = $event->location;
        $this->eventCapacity = $event->capacity;
        $this->eventStatus = $event->status;
        $this->eventDescription = $event->description ?? '';
        $this->showEventModal = true;
        $this->dispatch('open-event-modal');
    }

    public function saveEvent(?array $data = null): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if ($data) {
            $this->editingEventId = !empty($data['id']) ? (int) $data['id'] : null;
            $this->isEditingEvent = !empty($data['id']);
            $this->eventTitle = trim($data['title'] ?? '');
            $this->eventCategoryId = !empty($data['category_id']) ? (int) $data['category_id'] : null;
            $this->eventSpeakerName = trim($data['speaker_name'] ?? '');
            $this->eventSpeakerRole = trim($data['speaker_role'] ?? '');
            $this->eventDate = $data['event_date'] ?? '';
            $this->eventTimeDisplay = trim($data['time_display'] ?? '');
            $this->eventLocation = trim($data['location'] ?? 'Ruang Utama Masjid');
            $this->eventCapacity = isset($data['capacity']) ? (int) $data['capacity'] : 100;
            $this->eventStatus = $data['status'] ?? 'DRAF';
            $this->eventDescription = trim($data['description'] ?? '');
        }

        $this->validate([
            'eventTitle' => 'required|min:3|max:255',
            'eventCategoryId' => 'required|exists:categories,id',
            'eventSpeakerName' => 'required|min:3|max:150',
            'eventDate' => 'required|date',
            'eventCapacity' => 'required|integer|min:1',
            'eventStatus' => 'required|in:DRAF,TAYANG,BERJALAN,PENUH,SELESAI',
        ]);

        $slug = Str::slug($this->eventTitle) . '-' . Str::random(4);

        if ($this->isEditingEvent && $this->editingEventId) {
            $event = Event::findOrFail($this->editingEventId);
            $event->update([
                'category_id' => $this->eventCategoryId,
                'title' => $this->eventTitle,
                'speaker_name' => $this->eventSpeakerName,
                'speaker_role' => $this->eventSpeakerRole ?: null,
                'event_date' => $this->eventDate,
                'time_display' => $this->eventTimeDisplay ?: null,
                'location' => $this->eventLocation,
                'capacity' => $this->eventCapacity,
                'status' => $this->eventStatus,
                'description' => $this->eventDescription ?: null,
            ]);
            $this->notify('Kegiatan berhasil diperbarui!');
        } else {
            Event::create([
                'category_id' => $this->eventCategoryId,
                'title' => $this->eventTitle,
                'slug' => $slug,
                'speaker_name' => $this->eventSpeakerName,
                'speaker_role' => $this->eventSpeakerRole ?: null,
                'event_date' => $this->eventDate,
                'time_display' => $this->eventTimeDisplay ?: null,
                'location' => $this->eventLocation,
                'capacity' => $this->eventCapacity,
                'registered_count' => 0,
                'status' => $this->eventStatus,
                'description' => $this->eventDescription ?: null,
            ]);
            $this->notify('Kegiatan baru berhasil dibuat!');
        }

        $this->showEventModal = false;
        $this->dispatch('close-event-modal');
    }

    public function deleteEvent(int $id): void
    {
        if (! Auth::user()->isAdmin()) {
            $this->notify('Hanya Admin DKM yang berwenang menghapus kegiatan.');
            return;
        }

        Event::destroy($id);
        $this->notify('Kegiatan berhasil dihapus.');
    }

    public function quickSetEventStatus(int $id, string $status): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $event = Event::findOrFail($id);
        $event->update(['status' => $status]);
        $this->notify("Status kegiatan diubah menjadi {$status}.");
    }

    // ==========================================
    // Category Actions
    // ==========================================
    public function openCreateCategoryModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->isEditingCategory = false;
        $this->editingCategoryId = null;
        $this->reset(['categoryName', 'categoryDescription']);
        $this->categoryColor = 'emerald';
        $this->showCategoryModal = true;
        $this->dispatch('open-category-modal');
    }

    public function openEditCategoryModal(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $cat = Category::findOrFail($id);
        $this->isEditingCategory = true;
        $this->editingCategoryId = $id;
        $this->categoryName = $cat->name;
        $this->categoryColor = $cat->color_badge;
        $this->categoryDescription = $cat->description ?? '';
        $this->showCategoryModal = true;
        $this->dispatch('open-category-modal');
    }

    public function saveCategory(?array $data = null): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if ($data) {
            $this->editingCategoryId = !empty($data['id']) ? (int) $data['id'] : null;
            $this->isEditingCategory = !empty($data['id']);
            $this->categoryName = trim($data['name'] ?? '');
            $this->categoryColor = $data['color'] ?? ($data['color_badge'] ?? 'emerald');
            $this->categoryDescription = trim($data['description'] ?? '');
        }

        $this->validate([
            'categoryName' => 'required|min:2|max:100',
            'categoryColor' => 'required',
        ]);

        if ($this->isEditingCategory && $this->editingCategoryId) {
            $cat = Category::findOrFail($this->editingCategoryId);
            $cat->update([
                'name' => $this->categoryName,
                'slug' => Str::slug($this->categoryName),
                'color_badge' => $this->categoryColor,
                'description' => $this->categoryDescription ?: null,
            ]);
            $this->notify('Kategori berhasil diperbarui!');
        } else {
            Category::create([
                'name' => $this->categoryName,
                'slug' => Str::slug($this->categoryName),
                'color_badge' => $this->categoryColor,
                'description' => $this->categoryDescription ?: null,
            ]);
            $this->notify('Kategori baru berhasil ditambahkan!');
        }

        $this->reset(['categoryName', 'categoryDescription', 'isEditingCategory', 'editingCategoryId']);
        $this->showCategoryModal = false;
        $this->dispatch('close-category-modal');
    }

    public function deleteCategory(int $id): void
    {
        if (! Auth::user()->isAdmin()) {
            $this->notify('Hanya Admin DKM yang berwenang menghapus kategori.');
            return;
        }

        Category::destroy($id);
        $this->notify('Kategori berhasil dihapus.');
    }

    // ==========================================
    // Registrant & Check-In Actions
    // ==========================================
    public function quickCheckInByCode(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $code = trim($this->quickTicketCode);
        if (!$code) {
            $this->notify('Masukkan kode tiket registrasi.');
            return;
        }

        $reg = Registrant::where('ticket_code', $code)->first();
        if (!$reg) {
            $this->notify("Kode tiket {$code} tidak ditemukan.");
            return;
        }

        if ($reg->status === 'HADIR') {
            $this->notify("Jamaah {$reg->full_name} ({$code}) sudah tercatat Hadir sebelumnya.");
            $this->quickTicketCode = '';
            return;
        }

        $reg->update([
            'status' => 'HADIR',
            'checked_in_at' => now(),
            'checked_in_by' => Auth::id(),
        ]);

        $this->quickTicketCode = '';
        $this->notify("✓ Sukses! {$reg->full_name} ({$code}) berhasil Check-In Hadir.");
    }

    public function toggleAttendance(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $reg = Registrant::findOrFail($id);
        
        if ($reg->status === 'HADIR') {
            $reg->update([
                'status' => 'TERKONFIRMASI',
                'checked_in_at' => null,
                'checked_in_by' => null,
            ]);
            $this->notify("Status kehadiran {$reg->full_name} dibatalkan.");
        } else {
            $reg->update([
                'status' => 'HADIR',
                'checked_in_at' => now(),
                'checked_in_by' => Auth::id(),
            ]);
            $this->notify("✓ {$reg->full_name} berhasil check-in (Hadir)!");
        }
    }

    // ==========================================
    // Materials Actions
    // ==========================================
    public function saveMaterial(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->validate([
            'materialTitle' => 'required|min:3|max:255',
            'materialEventId' => 'required|exists:events,id',
        ]);

        Material::create([
            'title' => $this->materialTitle,
            'event_id' => $this->materialEventId,
            'youtube_url' => $this->materialYoutube ?: null,
            'notes' => $this->materialNotes ?: null,
            'file_type' => 'pdf',
        ]);

        $this->reset(['materialTitle', 'materialEventId', 'materialYoutube', 'materialNotes']);
        $this->showMaterialModal = false;
        $this->dispatch('close-material-modal');
        $this->notify('Materi kajian berhasil ditambahkan!');
    }

    public function deleteMaterial(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        Material::destroy($id);
        $this->notify('Materi kajian berhasil dihapus.');
    }

    // ==========================================
    // Export CSV Actions
    // ==========================================
    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'data-pendaftar-masjid-salahuddin-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Microsoft Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Tiket',
                'Nama Jamaah',
                'Jenis Kelamin',
                'No WhatsApp',
                'Alamat Email',
                'Kegiatan',
                'Status Pendaftaran',
                'Waktu Check-In',
                'Petugas Verifikator',
            ]);

            Registrant::with(['event', 'checkedInBy'])
                ->orderBy('id', 'desc')
                ->chunk(100, function ($registrants) use ($handle) {
                    foreach ($registrants as $reg) {
                        fputcsv($handle, [
                            $reg->ticket_code,
                            $reg->full_name,
                            $reg->gender === 'ikhwan' ? 'Ikhwan (Laki-laki)' : 'Akhwat (Perempuan)',
                            $reg->whatsapp,
                            $reg->email ?? '-',
                            $reg->event->title ?? '-',
                            $reg->status,
                            $reg->checked_in_at ? $reg->checked_in_at->format('Y-m-d H:i:s') . ' WIB' : 'Belum Hadir',
                            $reg->checkedInBy->name ?? '-',
                        ]);
                    }
                });

            fclose($handle);
        }, 200, $headers);
    }

    // ==========================================
    // Kajian Actions (Milestone 1)
    // ==========================================
    public function resetKajianFilters(): void
    {
        $this->pekananSearch = '';
        $this->jumatSearch = '';
        $this->kajianMonthFilter = 'all';
        $this->kajianYearFilter = 'all';
        $this->search = '';
        $this->resetPage('pekananPage');
        $this->resetPage('jumatPage');
    }

    /**
     * Dapatkan periode default filter bulan dan tahun untuk kajian dengan Smart Rollover.
     * Jika di bulan berjalan sudah tidak ada lagi jadwal kajian yang tersisa (semua kajian bulan ini telah terlaksana),
     * filter otomatis berpindah ke bulan berikutnya (misal dari September ke Oktober).
     *
     * @return array{0: int, 1: int} [month, year]
     */
    public function getDefaultKajianFilterPeriod(?Carbon $now = null): array
    {
        $now = $now ? $now->copy()->timezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $currentMonth = (int) $now->month;
        $currentYear = (int) $now->year;

        // Cek apakah masih ada jadwal kajian di bulan berjalan pada atau setelah hari ini
        $hasRemainingThisMonth = Kajian::query()->whereDate('date', '>=', $now->toDateString())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->exists();

        if (!$hasRemainingThisMonth) {
            $hasPastKajianThisMonth = Kajian::whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->exists();

            $nextMonth = $now->copy()->addMonth();
            $hasNextMonthKajian = Kajian::whereMonth('date', $nextMonth->month)
                ->whereYear('date', $nextMonth->year)
                ->exists();

            // Jika semua kajian bulan ini telah selesai dilaksanakan, atau bulan depan telah memiliki jadwal
            if ($hasPastKajianThisMonth || $hasNextMonthKajian) {
                return [(int) $nextMonth->month, (int) $nextMonth->year];
            }
        }

        return [$currentMonth, $currentYear];
    }

    public function clearKajianSearch(string $type = 'jumat'): void
    {
        if ($type === 'jumat') {
            $this->jumatSearch = '';
            $this->resetPage('jumatPage');
        } else {
            $this->pekananSearch = '';
            $this->resetPage('pekananPage');
        }
        $this->search = '';

        // Pastikan filter bulan dan tahun terfilter ke periode default (dengan smart rollover)
        $now = Carbon::now('Asia/Jakarta');
        [$kajianDefaultMonth, $kajianDefaultYear] = $this->getDefaultKajianFilterPeriod($now);
        $this->kajianMonthFilter = (string) $kajianDefaultMonth;
        $this->kajianYearFilter = (string) $kajianDefaultYear;
    }

    public function clearAgendaSearch(): void
    {
        $this->agendaSearch = '';
        $this->resetPage('agendaPage');
    }

    public function clearPetugasSearch(): void
    {
        $this->search = '';
    }

    public function clearFinanceSearch(): void
    {
        $this->financeSearch = '';
        $this->resetPage('financePage');
    }

    public function clearParticipantSearch(): void
    {
        $this->participantSearch = '';
        $this->resetPage('participantPage');
    }

    public function clearSocialParticipantSearch(): void
    {
        $this->socialParticipantSearch = '';
        $this->resetPage('socialParticipantPage');
    }

    public function resetCatalogFilters(): void
    {
        $this->catalogSearch = '';
        $this->catalogMonth = (string) Carbon::now('Asia/Jakarta')->month;
        $this->catalogYear = (string) Carbon::now('Asia/Jakarta')->year;
    }

    public function updatedSocialParticipantPeriodFilter(): void
    {
        $this->resetPage('socialParticipantPage');
    }

    public function toggleAllFinanceMonths(): void
    {
        $allMonths = array_map('strval', range(1, 12));
        $current = is_array($this->financeMonthFilter)
            ? array_values(array_filter($this->financeMonthFilter, fn($m) => $m !== 'all' && !empty($m)))
            : ($this->financeMonthFilter !== 'all' && !empty($this->financeMonthFilter) ? [(string) $this->financeMonthFilter] : []);

        if (count($current) === 12) {
            $this->financeMonthFilter = [];
        } else {
            $this->financeMonthFilter = $allMonths;
        }
        $this->resetPage('financePage');
    }

    public function selectAllFinanceMonths(): void
    {
        $this->financeMonthFilter = array_map('strval', range(1, 12));
        $this->resetPage('financePage');
    }

    public function resetFinanceMonths(): void
    {
        $this->financeMonthFilter = [];
        $this->resetPage('financePage');
    }

    public function getAvailableParticipantPeriods(): array
    {
        $periods = ProgramParticipant::distinct()->pluck('period')->filter()->values();
        return $periods->sort(function ($a, $b) {
            preg_match('/(\d+)\/(\d+)/', (string) $a, $mA);
            preg_match('/(\d+)\/(\d+)/', (string) $b, $mB);
            $yearA = isset($mA[2]) ? (int) $mA[2] : 0;
            $monthA = isset($mA[1]) ? (int) $mA[1] : 0;
            $yearB = isset($mB[2]) ? (int) $mB[2] : 0;
            $monthB = isset($mB[1]) ? (int) $mB[1] : 0;
            if ($yearA !== $yearB) {
                return $yearB <=> $yearA;
            }
            return $monthB <=> $monthA;
        })->values()->toArray();
    }

    public function toggleAllParticipantPeriods(): void
    {
        $availablePeriods = $this->getAvailableParticipantPeriods();
        $current = is_array($this->socialParticipantPeriodFilter)
            ? array_values(array_filter($this->socialParticipantPeriodFilter, fn($p) => $p !== 'all' && !empty($p)))
            : ($this->socialParticipantPeriodFilter !== 'all' && !empty($this->socialParticipantPeriodFilter) ? [$this->socialParticipantPeriodFilter] : []);

        if (count($availablePeriods) > 0 && count($current) === count($availablePeriods)) {
            $this->socialParticipantPeriodFilter = [];
        } else {
            $this->socialParticipantPeriodFilter = $availablePeriods;
        }
        $this->resetPage('socialParticipantPage');
    }

    public function selectAllParticipantPeriods(): void
    {
        $this->socialParticipantPeriodFilter = $this->getAvailableParticipantPeriods();
        $this->resetPage('socialParticipantPage');
    }

    public function resetParticipantPeriods(): void
    {
        $this->socialParticipantPeriodFilter = [];
        $this->resetPage('socialParticipantPage');
    }

    public function clearUserSearch(): void
    {
        $this->search = '';
        $this->resetPage('userPage');
    }

    public function updatingAgendaSearch(): void
    {
        $this->resetPage('agendaPage');
    }

    public function updatingFinanceSearch(): void
    {
        $this->resetPage('financePage');
    }

    public function updatingParticipantSearch(): void
    {
        $this->resetPage('participantPage');
    }

    public function updatingSocialParticipantSearch(): void
    {
        $this->resetPage('socialParticipantPage');
    }

    public function resetAgendaFilters(): void
    {
        $this->agendaSearch = '';
        $this->agendaFilterStatus = 'all';
        $this->resetPage('agendaPage');
    }

    public function openCreateKajian(string $type = 'pekanan'): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->isEditingKajian = false;
        $this->editingKajianId = null;
        $this->kajianType = $type;
        $this->kajianDate = Carbon::now()->addDays($type === 'jumat' ? 5 : 3)->format('Y-m-d');
        $this->kajianTimeDisplay = $type === 'jumat' ? '11:45 - 12:45' : '09:00 - 11:30';
        $this->kajianTitle = '';
        $this->kajianSpeakerName = '';
        $this->kajianSpeakerPhone = '';
        $this->kajianSpeakerPhoto = null;
        $this->kajianExistingPhoto = null;
        $this->kajianIsHolidayDisabled = false;
        $this->kajianKhatibName = '';
        $this->kajianMcName = '';
        $this->kajianMuadzinName = '';
        $this->kajianKhatibPhone = '';
        $this->kajianMcNotes = '';
        $this->showKajianModal = true;
        $this->dispatch('open-kajian-modal');
    }

    public function openEditKajian(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $k = Kajian::findOrFail($id);
        $this->isEditingKajian = true;
        $this->editingKajianId = $k->id;
        $this->kajianType = $k->type;
        $this->kajianDate = $k->date ? Carbon::parse($k->date)->format('Y-m-d') : '';
        $this->kajianTimeDisplay = $k->time_display ?? '';
        $this->kajianTitle = $k->title ?? '';
        $this->kajianSpeakerName = $k->speaker_name ?? '';
        $this->kajianSpeakerPhone = $k->speaker_phone ?? '';
        $this->kajianSpeakerPhoto = null;
        $this->kajianExistingPhoto = $k->speaker_photo ?? null;
        $this->kajianIsHolidayDisabled = (bool) $k->is_holiday_disabled;
        $this->kajianKhatibName = $k->khatib_name ?? '';
        $this->kajianMcName = $k->mc_name ?? '';
        $this->kajianMuadzinName = $k->muadzin_name ?? '';
        $this->kajianKhatibPhone = $k->khatib_phone ?? '';
        $this->kajianMcNotes = $k->mc_notes ?? '';
        $this->showKajianModal = true;
        $this->dispatch('open-kajian-modal');
    }

    public function removeKajianPhoto(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if ($this->editingKajianId) {
            $k = Kajian::find($this->editingKajianId);
            if ($k && $k->speaker_photo) {
                $k->update(['speaker_photo' => null]);
            }
        }
        $this->kajianExistingPhoto = null;
        $this->kajianSpeakerPhoto = null;
        $this->notify('Foto pembicara berhasil dihapus dari jadwal.');
    }

    public function saveKajian(?array $data = null): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if ($data) {
            $this->editingKajianId = !empty($data['id']) ? (int) $data['id'] : null;
            $this->isEditingKajian = !empty($data['id']);
            $this->kajianType = $data['type'] ?? 'pekanan';
            $this->kajianDate = $data['date'] ?? '';
            $this->kajianTimeDisplay = trim($data['time_display'] ?? '');
            $this->kajianTitle = trim($data['title'] ?? '');
            $this->kajianSpeakerName = trim($data['speaker_name'] ?? '');
            $this->kajianSpeakerPhone = trim($data['speaker_phone'] ?? '');
            $this->kajianIsHolidayDisabled = !empty($data['is_holiday_disabled']);
            $this->kajianKhatibName = trim($data['khatib_name'] ?? '');
            $this->kajianMcName = trim($data['mc_name'] ?? '');
            $this->kajianMuadzinName = trim($data['muadzin_name'] ?? '');
            $this->kajianKhatibPhone = trim($data['khatib_phone'] ?? '');
            $this->kajianMcNotes = trim($data['mc_notes'] ?? '');
            $this->kajianYoutubeUrl = trim($data['youtube_url'] ?? '');
        }

        $rules = [
            'kajianDate' => 'required|date',
            'kajianTitle' => 'required|string|max:255',
            'kajianType' => 'required|in:pekanan,jumat,tematik',
        ];

        if (in_array($this->kajianType, ['pekanan', 'tematik'])) {
            $rules['kajianSpeakerName'] = 'required|string|max:255';
        }

        if ($this->kajianSpeakerPhoto) {
            $rules['kajianSpeakerPhoto'] = 'image|max:3072';
        }

        $this->validate($rules);

        $payload = [
            'type' => $this->kajianType,
            'date' => $this->kajianDate,
            'time_display' => $this->kajianTimeDisplay ?: ($this->kajianType === 'jumat' ? '11:45 - 12:45' : '09:00 - 11:30'),
            'title' => $this->kajianTitle,
            'youtube_url' => !empty($this->kajianYoutubeUrl) ? trim($this->kajianYoutubeUrl) : null,
        ];

        if (in_array($this->kajianType, ['pekanan', 'tematik'])) {
            $payload['speaker_name'] = $this->kajianSpeakerName;
            $payload['speaker_phone'] = $this->kajianSpeakerPhone;

            $speakerName = trim($this->kajianSpeakerName);

            if ($this->kajianSpeakerPhoto) {
                $path = $this->kajianSpeakerPhoto->store('ustadz-photos', 'public');
                $payload['speaker_photo'] = $path;
                $this->kajianSpeakerPhoto = null;
                $this->kajianExistingPhoto = $path;

                // Sync with Ustadz directory & auto-propagate to ALL kajian records of this ustadz
                if ($speakerName !== '') {
                    $u = Ustadz::where('name', $speakerName)->first();
                    if ($u) {
                        $u->update([
                            'photo' => $path,
                            'phone' => $this->kajianSpeakerPhone ?: $u->phone,
                        ]);
                    } else {
                        Ustadz::create([
                            'name' => $speakerName,
                            'phone' => $this->kajianSpeakerPhone ?: null,
                            'photo' => $path,
                        ]);
                    }
                    Ustadz::syncPhotoToKajians($speakerName, $path);
                }
            } else {
                // If user didn't upload a new photo:
                if ($this->kajianExistingPhoto) {
                    $payload['speaker_photo'] = $this->kajianExistingPhoto;
                } elseif ($speakerName !== '') {
                    // Check if Ustadz database has a photo for this speaker
                    $u = Ustadz::where('name', $speakerName)->first();
                    if ($u && $u->photo) {
                        $payload['speaker_photo'] = $u->photo;
                    }
                }

                // If ustadz exists without phone and kajian provides phone, save phone
                if ($speakerName !== '' && !empty($this->kajianSpeakerPhone)) {
                    $u = Ustadz::where('name', $speakerName)->first();
                    if ($u && empty($u->phone)) {
                        $u->update(['phone' => $this->kajianSpeakerPhone]);
                    }
                }
            }
        } else {
            $payload['is_holiday_disabled'] = $this->kajianIsHolidayDisabled;
            $payload['khatib_name'] = $this->kajianKhatibName;
            $payload['mc_name'] = $this->kajianMcName;
            $payload['muadzin_name'] = $this->kajianMuadzinName;
            $payload['khatib_phone'] = $this->kajianKhatibPhone;
            $payload['mc_notes'] = $this->kajianMcNotes;
        }

        if ($this->isEditingKajian && $this->editingKajianId) {
            $k = Kajian::findOrFail($this->editingKajianId);
            $k->update($payload);
            $this->notify('Jadwal kajian berhasil diperbarui.');
        } else {
            Kajian::create($payload);
            $this->notify('Jadwal kajian baru berhasil ditambahkan.');
        }

        $this->showKajianModal = false;
        $this->dispatch('close-kajian-modal');
    }

    public function closeKajianModal(): void
    {
        $this->showKajianModal = false;
        $this->kajianSpeakerPhoto = null;
        $this->dispatch('close-kajian-modal');
    }

    public function deleteKajian(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $k = Kajian::find($id);
        if (! $k) {
            $this->notify('Jadwal kajian telah dihapus.');
            return;
        }

        $k->delete();
        $this->notify('Jadwal kajian berhasil dihapus.');
    }

    // ==========================================
    // Notula Kajian Methods (Role Master Only)
    // ==========================================
    public function openNotulaModal(int $id): void
    {
        Kajian::ensureNotulaColumnExists();

        if (! Auth::user() || ! Auth::user()->isMaster()) {
            $this->notify('Akses ditolak: Hanya Pengurus dengan role Master yang berhak mengelola Notula.');
            return;
        }

        $k = Kajian::find($id);
        if (! $k) {
            $this->notify('Data jadwal kajian tidak ditemukan.');
            return;
        }

        $speaker = $k->speaker_name ?: ($k->khatib_name ?: 'Asatidz');
        $this->notulaKajianId = $k->id;
        $this->notulaTitle = $k->title;
        $this->notulaSpeaker = $speaker;
        $this->notulaDate = $k->date ? Carbon::parse($k->date)->translatedFormat('l, d F Y') : '';
        $this->notulaTime = $k->time_display ?: '';
        $this->notulaType = $k->type;
        $this->notulaRawText = $k->notula ?? '';
        $this->showNotulaModal = true;

        $this->dispatch('open-notula-modal', [
            'id' => $k->id,
            'title' => $k->title,
            'type' => $k->type,
            'speaker_name' => $speaker,
            'date' => $this->notulaDate,
            'time_display' => $this->notulaTime,
            'notula' => $this->notulaRawText,
        ]);
    }

    public function saveNotula($idOrData = null, ?string $rawText = null): void
    {
        Kajian::ensureNotulaColumnExists();

        if (! Auth::user() || ! Auth::user()->isMaster()) {
            $this->notify('Akses ditolak: Hanya Pengurus dengan role Master yang berhak menyimpan Notula.');
            return;
        }

        if (is_array($idOrData)) {
            $targetId = !empty($idOrData['id']) ? (int) $idOrData['id'] : $this->notulaKajianId;
            $content = $idOrData['raw_text'] ?? ($idOrData['notula'] ?? $this->notulaRawText);
        } else {
            $targetId = $idOrData ? (int) $idOrData : $this->notulaKajianId;
            $content = $rawText !== null ? $rawText : $this->notulaRawText;
        }

        if (! $targetId) {
            $this->notify('ID kajian tidak valid.');
            return;
        }

        $k = Kajian::find($targetId);
        if (! $k) {
            $this->notify('Data kajian tidak ditemukan.');
            return;
        }

        try {
            $k->notula = $content;
            $k->save();
        } catch (\Throwable $e) {
            Kajian::ensureNotulaColumnExists();
            $k->notula = $content;
            $k->save();
        }

        $this->notulaKajianId = $k->id;
        $this->notulaRawText = $content;
        $this->showNotulaModal = true;

        $this->notify('Notula kajian berhasil disimpan.');
        $this->dispatch('notula-saved', ['id' => $k->id, 'notula' => $content]);
    }

    // ==========================================
    // Link YouTube Kegiatan Methods
    // ==========================================
    public function openYoutubeModal(int $id, string $type = 'kajian'): void
    {
        Kajian::ensureYoutubeColumnExists();
        Agenda::ensureYoutubeColumnExists();

        if (! Auth::user() || ! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus dengan izin kelola yang berhak mengatur Link YouTube.');
            return;
        }

        $this->youtubeItemId = $id;
        $this->youtubeItemType = $type;

        if ($type === 'agenda') {
            $item = Agenda::find($id);
            if (! $item) {
                $this->notify('Data agenda kegiatan tidak ditemukan.');
                return;
            }
            $this->youtubeItemTitle = $item->title;
            $this->youtubeItemSubtitle = 'Kegiatan Akbar Masjid';
            $this->youtubeItemDate = $item->event_date ? Carbon::parse($item->event_date)->translatedFormat('l, d F Y') : '';
            $this->youtubeUrl = $item->youtube_url ?? '';
        } else {
            $item = Kajian::find($id);
            if (! $item) {
                $this->notify('Data jadwal kajian tidak ditemukan.');
                return;
            }
            $speaker = $item->speaker_name ?: ($item->khatib_name ?: 'Asatidz');
            $this->youtubeItemTitle = $item->title;
            $this->youtubeItemSubtitle = $speaker;
            $this->youtubeItemDate = $item->date ? Carbon::parse($item->date)->translatedFormat('l, d F Y') : '';
            $this->youtubeUrl = $item->youtube_url ?? '';
        }

        $this->showYoutubeModal = true;
        $this->dispatch('open-youtube-modal', [
            'id' => $id,
            'type' => $type,
            'title' => $this->youtubeItemTitle,
            'subtitle' => $this->youtubeItemSubtitle,
            'date' => $this->youtubeItemDate,
            'youtube_url' => $this->youtubeUrl,
        ]);
    }

    public function saveYoutubeLink($idOrData = null, ?string $url = null, string $type = 'kajian'): void
    {
        Kajian::ensureYoutubeColumnExists();
        Agenda::ensureYoutubeColumnExists();

        if (! Auth::user() || ! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus dengan izin kelola yang berhak menyimpan Link YouTube.');
            return;
        }

        if (is_array($idOrData)) {
            $targetId = !empty($idOrData['id']) ? (int) $idOrData['id'] : $this->youtubeItemId;
            $targetType = !empty($idOrData['type']) ? $idOrData['type'] : $this->youtubeItemType;
            $targetUrl = isset($idOrData['youtube_url']) ? $idOrData['youtube_url'] : $this->youtubeUrl;
        } else {
            $targetId = $idOrData ? (int) $idOrData : $this->youtubeItemId;
            $targetType = $type ?: $this->youtubeItemType;
            $targetUrl = $url !== null ? $url : $this->youtubeUrl;
        }

        $targetUrl = trim((string) $targetUrl);

        if (! empty($targetUrl)) {
            if (! filter_var($targetUrl, FILTER_VALIDATE_URL)) {
                $this->notify('Format URL YouTube tidak valid. Contoh: https://www.youtube.com/watch?v=...');
                return;
            }
        } else {
            $targetUrl = null;
        }

        if ($targetType === 'agenda') {
            $item = Agenda::find($targetId);
        } else {
            $item = Kajian::find($targetId);
        }

        if (! $item) {
            $this->notify('Data kegiatan tidak ditemukan.');
            return;
        }

        try {
            $item->update(['youtube_url' => $targetUrl]);
        } catch (\Throwable $e) {
            if ($targetType === 'agenda') {
                Agenda::ensureYoutubeColumnExists();
            } else {
                Kajian::ensureYoutubeColumnExists();
            }
            $item->update(['youtube_url' => $targetUrl]);
        }

        $this->youtubeUrl = $targetUrl ?? '';
        $this->showYoutubeModal = false;
        $this->dispatch('close-youtube-modal');
        $this->dispatch('youtube-saved', [
            'id' => $targetId,
            'type' => $targetType,
            'youtube_url' => $targetUrl,
        ]);

        if ($targetUrl) {
            $this->notify('Link YouTube kegiatan berhasil disimpan!');
        } else {
            $this->notify('Link YouTube kegiatan telah dihapus.');
        }
    }

    // ==========================================
    // Ustadz Directory Methods (Milestone Database Ustadz)
    // ==========================================
    public function openUstadzModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->resetUstadzForm();
        $this->showUstadzModal = true;
        $this->dispatch('open-ustadz-modal');
    }

    public function closeUstadzModal(): void
    {
        $this->showUstadzModal = false;
        $this->dispatch('close-ustadz-modal');
    }

    public function resetUstadzForm(): void
    {
        $this->editingUstadzId = null;
        $this->ustadzName = '';
        $this->ustadzTitle = '';
        $this->ustadzPhone = '';
        $this->ustadzPhoto = null;
        $this->ustadzExistingPhoto = null;
        $this->showUstadzModal = true;
        $this->dispatch('open-ustadz-modal');
    }

    public function editUstadz(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $u = Ustadz::findOrFail($id);
        $this->editingUstadzId = $u->id;
        $this->ustadzName = $u->name;
        $this->ustadzTitle = $u->title ?? '';
        $this->ustadzPhone = $u->phone ?? '';
        $this->ustadzPhoto = null;
        $this->ustadzExistingPhoto = $u->photo ?? null;
        $this->showUstadzModal = true;
        $this->dispatch('open-ustadz-modal');
    }

    public function saveUstadz(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->validate([
            'ustadzName' => 'required|string|max:255',
            'ustadzTitle' => 'nullable|string|max:255',
            'ustadzPhone' => 'nullable|string|max:50',
            'ustadzPhoto' => 'nullable|image|max:3072',
        ]);

        $name = trim($this->ustadzName);
        $data = [
            'name' => $name,
            'title' => trim($this->ustadzTitle) ?: null,
            'phone' => trim($this->ustadzPhone) ?: null,
        ];

        if ($this->ustadzPhoto) {
            $path = $this->ustadzPhoto->store('ustadz-photos', 'public');
            $data['photo'] = $path;
            $this->ustadzPhoto = null;
            $this->ustadzExistingPhoto = $path;
        }

        if ($this->editingUstadzId) {
            $u = Ustadz::findOrFail($this->editingUstadzId);
            $oldName = $u->name;
            $u->update($data);

            if (isset($data['photo'])) {
                Ustadz::syncPhotoToKajians($u->name, $data['photo']);
            }
            if ($oldName !== $u->name) {
                DB::table('kajians')->where('speaker_name', $oldName)->update(['speaker_name' => $u->name]);
                if ($u->photo) {
                    Ustadz::syncPhotoToKajians($u->name, $u->photo);
                }
            }

            $this->notify('Data ustadz dan foto jadwal kajian berhasil diperbarui.');
        } else {
            $existing = Ustadz::where('name', $name)->first();
            if ($existing) {
                if (isset($data['photo'])) {
                    $existing->update($data);
                    Ustadz::syncPhotoToKajians($existing->name, $data['photo']);
                } else {
                    $existing->update($data);
                }
                $this->notify('Data ustadz berhasil diperbarui.');
            } else {
                $created = Ustadz::create($data);
                if (!empty($data['photo'])) {
                    Ustadz::syncPhotoToKajians($created->name, $data['photo']);
                }
                $this->notify('Ustadz baru berhasil ditambahkan ke database.');
            }
        }

        $this->resetUstadzForm();
        $this->showUstadzModal = true;
        $this->dispatch('open-ustadz-modal');
    }

    // ==========================================
    // Gallery Management Methods
    // ==========================================
    public function openCreateGalleryModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->reset(['galleryTitle', 'editingGalleryId', 'isEditingGallery', 'galleryUploadedPhotos', 'galleryExistingPhotos']);
        $this->galleryDate = Carbon::today()->format('Y-m-d');
        $this->galleryLocation = 'Masjid Salahuddin, KPP Madya Malang';
        $this->showGalleryModal = true;
        $this->dispatch('open-gallery-modal');
    }

    public function openEditGalleryModal(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $gallery = ActivityGallery::findOrFail($id);
        $this->editingGalleryId = $gallery->id;
        $this->isEditingGallery = true;
        $this->galleryTitle = $gallery->title;
        $this->galleryDate = $gallery->event_date ? Carbon::parse($gallery->event_date)->format('Y-m-d') : '';
        $this->galleryLocation = $gallery->location ?: 'Masjid Salahuddin, KPP Madya Malang';
        $this->galleryExistingPhotos = $gallery->photos ?: [];
        $this->galleryUploadedPhotos = [];
        $this->showGalleryModal = true;
        $this->dispatch('open-gallery-modal');
    }

    public function closeGalleryModal(): void
    {
        $this->showGalleryModal = false;
        $this->reset(['galleryTitle', 'editingGalleryId', 'isEditingGallery', 'galleryUploadedPhotos', 'galleryExistingPhotos']);
    }

    public function removeExistingGalleryPhoto(int $index): void
    {
        if (isset($this->galleryExistingPhotos[$index])) {
            unset($this->galleryExistingPhotos[$index]);
            $this->galleryExistingPhotos = array_values($this->galleryExistingPhotos);
        }
    }

    public function removeUploadedGalleryPhoto(int $index): void
    {
        if (isset($this->galleryUploadedPhotos[$index])) {
            unset($this->galleryUploadedPhotos[$index]);
            $this->galleryUploadedPhotos = array_values($this->galleryUploadedPhotos);
        }
    }

    public function updatedGalleryUploadedPhotos(): void
    {
        $this->validate([
            'galleryUploadedPhotos.*' => 'nullable|image|max:30720',
        ], [
            'galleryUploadedPhotos.*.image' => 'Setiap berkas harus berupa gambar valid (JPG, JPEG, PNG, WEBP).',
            'galleryUploadedPhotos.*.max' => 'Ukuran file foto melebihi batas maksimal 30 MB. Silakan pilih foto dengan ukuran maksimal 30 MB.',
            'galleryUploadedPhotos.*.uploaded' => 'Foto dokumentasi gagal diunggah. Pastikan ukuran file atau total foto tidak melebihi batas muatan server.',
            'galleryUploadedPhotos.uploaded' => 'Foto dokumentasi gagal diunggah. Pastikan ukuran file atau total foto tidak melebihi batas muatan server.',
        ]);
    }

    public function saveGallery(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->validate([
            'galleryTitle' => 'required|string|max:255',
            'galleryDate' => 'required|date',
            'galleryLocation' => 'required|string|max:255',
            'galleryUploadedPhotos.*' => 'nullable|image|max:30720',
        ], [
            'galleryTitle.required' => 'Judul kegiatan wajib diisi.',
            'galleryDate.required' => 'Tanggal kegiatan wajib diisi.',
            'galleryLocation.required' => 'Lokasi kegiatan wajib diisi.',
            'galleryUploadedPhotos.*.image' => 'Setiap berkas harus berupa gambar valid (JPG, JPEG, PNG, WEBP).',
            'galleryUploadedPhotos.*.max' => 'Ukuran file foto melebihi batas maksimal 30 MB. Silakan pilih foto dengan ukuran maksimal 30 MB.',
            'galleryUploadedPhotos.*.uploaded' => 'Foto dokumentasi gagal diunggah. Pastikan ukuran file atau total foto tidak melebihi batas muatan server.',
            'galleryUploadedPhotos.uploaded' => 'Foto dokumentasi gagal diunggah. Pastikan ukuran file atau total foto tidak melebihi batas muatan server.',
        ]);

        $photos = $this->galleryExistingPhotos ?: [];
        if (!empty($this->galleryUploadedPhotos)) {
            foreach ($this->galleryUploadedPhotos as $photo) {
                if ($photo) {
                    $path = ImageOptimizerService::convertToWebp($photo, 'galleries');
                    $photos[] = $path;
                }
            }
        }

        if ($this->isEditingGallery && $this->editingGalleryId) {
            $gallery = ActivityGallery::findOrFail($this->editingGalleryId);
            $gallery->update([
                'title' => trim($this->galleryTitle),
                'event_date' => $this->galleryDate,
                'location' => trim($this->galleryLocation),
                'photos' => array_values($photos),
            ]);
            $this->notify('Galeri kegiatan berhasil diperbarui.');
        } else {
            ActivityGallery::create([
                'title' => trim($this->galleryTitle),
                'event_date' => $this->galleryDate,
                'location' => trim($this->galleryLocation),
                'photos' => array_values($photos),
            ]);
            $this->notify('Galeri kegiatan baru berhasil ditambahkan.');
        }

        $this->closeGalleryModal();
    }

    public function deleteGallery(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $gallery = ActivityGallery::find($id);
        if (! $gallery) {
            $this->notify('Galeri kegiatan telah dihapus.');
            return;
        }

        if (!empty($gallery->photos)) {
            foreach ($gallery->photos as $p) {
                Storage::disk('public')->delete($p);
            }
        }
        $gallery->delete();
        $this->notify('Galeri kegiatan berhasil dihapus.');
    }

    // =========================================================================
    // SARAN & KRITIK MANAGEMENT
    // =========================================================================

    public function openSaranDetail(int $id): void
    {
        FeedbackSuggestion::ensureTableExists();
        $saran = FeedbackSuggestion::findOrFail($id);
        $this->selectedSaranId = $saran->id;
        $this->selectedSaran = $saran;
        $this->saranReplyText = $saran->admin_reply ?? '';
        $this->saranUpdateStatus = $saran->status === 'baru' ? 'dibaca' : $saran->status;
        $this->saranUpdateIsPublic = (bool) $saran->is_public;

        if ($saran->status === 'baru') {
            $saran->update(['status' => 'dibaca']);
            $this->selectedSaran->status = 'dibaca';
        }

        $this->showSaranDetailModal = true;
        $this->dispatch('open-saran-detail-modal');
    }

    public function closeSaranDetailModal(): void
    {
        $this->showSaranDetailModal = false;
        $this->reset(['selectedSaranId', 'selectedSaran', 'saranReplyText', 'saranUpdateStatus', 'saranUpdateIsPublic']);
        $this->dispatch('close-saran-detail-modal');
    }

    public function saveSaranResponse(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (! $this->selectedSaranId) return;

        $saran = FeedbackSuggestion::findOrFail($this->selectedSaranId);
        $reply = trim($this->saranReplyText);

        $saran->update([
            'status' => $this->saranUpdateStatus,
            'admin_reply' => $reply ?: null,
            'replied_at' => $reply ? now() : $saran->replied_at,
            'is_public' => $this->saranUpdateIsPublic,
        ]);

        $this->notify('Tanggapan dan status saran berhasil disimpan.');
        $this->closeSaranDetailModal();
    }

    public function toggleSaranPublic(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $saran = FeedbackSuggestion::findOrFail($id);
        $saran->update(['is_public' => ! $saran->is_public]);
        $this->notify($saran->is_public ? 'Saran berhasil ditampilkan di feed publik portal.' : 'Saran berhasil disembunyikan dari publik.');
    }

    public function updateQuickSaranStatus(int $id, string $status): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $saran = FeedbackSuggestion::findOrFail($id);
        $saran->update(['status' => $status]);
        $this->notify('Status saran diperbarui.');
    }

    public function deleteSaran(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $saran = FeedbackSuggestion::findOrFail($id);
        $saran->delete();

        if ($this->selectedSaranId === $id) {
            $this->closeSaranDetailModal();
        }

        $this->notify('Catatan saran berhasil dihapus.');
    }

    public function removeUstadzPhoto(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $u = Ustadz::findOrFail($id);
        if ($u->photo) {
            Storage::disk('public')->delete($u->photo);
            $u->update(['photo' => null]);
            Ustadz::syncPhotoToKajians($u->name, null);
        }

        if ($this->editingUstadzId === $id) {
            $this->ustadzExistingPhoto = null;
            $this->ustadzPhoto = null;
        }

        $this->showUstadzModal = true;
        $this->dispatch('open-ustadz-modal');
        $this->notify('Foto ustadz berhasil dihapus dari database & jadwal.');
    }

    public function deleteUstadz(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $u = Ustadz::findOrFail($id);
        if ($u->photo) {
            Storage::disk('public')->delete($u->photo);
        }
        $u->delete();

        if ($this->editingUstadzId === $id) {
            $this->resetUstadzForm();
        }

        $this->showUstadzModal = true;
        $this->dispatch('open-ustadz-modal');
        $this->notify('Ustadz berhasil dihapus dari direktori.');
    }

    public function toggleHolidayDisabled(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $k = Kajian::findOrFail($id);
        $k->is_holiday_disabled = ! $k->is_holiday_disabled;
        $k->save();
        $this->notify('Status hari libur / tugas berhasil diperbarui.');
    }

    public function openImportKajian(string $type = 'pekanan'): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->importKajianType = $type;
        $this->importKajianTab = 'paste';
        $this->importKajianPasteText = '';
        $this->importKajianFile = null;
        $this->showImportKajianModal = true;
        $this->dispatch('open-import-kajian-modal');
    }

    public function processImportKajianPaste(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (trim($this->importKajianPasteText) === '') {
            $this->notify('Silakan tempelkan (paste) data dari Excel / Spreadsheet terlebih dahulu.');
            return;
        }

        $rows = SimpleXlsxService::parseCsvText($this->importKajianPasteText);
        $importedCount = $this->importKajianRows($rows);

        $this->notify("Berhasil mengimpor {$importedCount} jadwal kajian!");
        $this->showImportKajianModal = false;
        $this->importKajianPasteText = '';
        $this->dispatch('close-import-kajian-modal');
    }

    public function processImportKajianFile(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->validate([
            'importKajianFile' => 'required|file|max:5120|extensions:xlsx,xls,csv,txt',
        ]);

        $path = $this->importKajianFile->getRealPath();
        $ext = $this->importKajianFile->getClientOriginalExtension();

        $rows = SimpleXlsxService::parseFile($path, $ext);
        $importedCount = $this->importKajianRows($rows);

        $this->notify("Berhasil mengunggah dan mengimpor {$importedCount} jadwal kajian dari file Excel!");
        $this->showImportKajianModal = false;
        $this->importKajianFile = null;
        $this->dispatch('close-import-kajian-modal');
    }

    /**
     * Parse structured rows and update or create Kajian records.
     *
     * @param array<int, array<int, string>> $rows
     * @return int Number of records imported or updated
     */
    protected function importKajianRows(array $rows): int
    {
        $importedCount = 0;
        $colMapping = null;

        foreach ($rows as $cols) {
            if (empty($cols)) {
                continue;
            }

            $cols = array_map(fn($c) => trim((string)$c), $cols);

            if (count(array_filter($cols, fn($c) => $c !== '')) === 0) {
                continue;
            }

            // Check if any cell in this row is a parseable date
            $hasDateInRow = false;
            foreach ($cols as $cell) {
                if (SimpleXlsxService::parseFlexibleDate($cell) !== null) {
                    $hasDateInRow = true;
                    break;
                }
            }

            $rowString = strtolower(implode(' ', $cols));

            // Check if this row is a header row (only if no parseable date is found in any column)
            if (! $hasDateInRow && (str_contains($rowString, 'tanggal') || (str_contains($rowString, 'hari') && !str_contains($rowString, 'libur')) || str_contains($rowString, 'judul') || str_contains($rowString, 'khatib') || str_contains($rowString, 'pembicara') || str_contains($rowString, 'waktu'))) {
                $colMapping = [
                    'date' => null,
                    'type' => null,
                    'time' => null,
                    'holiday' => null,
                    'title' => null,
                    'speaker' => null,
                    'khatib' => null,
                    'mc' => null,
                    'muadzin' => null,
                    'phone' => null,
                    'status' => null,
                ];

                foreach ($cols as $idx => $headerText) {
                    $h = strtolower($headerText);
                    // Match phone FIRST because headers might be 'No HP Pembicara' or 'No HP Khatib'
                    if (str_contains($h, 'hp') || str_contains($h, 'telepon') || str_contains($h, 'wa') || str_contains($h, 'phone') || str_contains($h, 'kontak')) {
                        $colMapping['phone'] = $idx;
                    } elseif (str_contains($h, 'libur')) {
                        $colMapping['holiday'] = $idx;
                    } elseif (str_contains($h, 'tanggal') || str_contains($h, 'hari & tanggal') || (str_contains($h, 'hari') && !str_contains($h, 'libur'))) {
                        $colMapping['date'] = $idx;
                    } elseif (str_contains($h, 'jenis') || str_contains($h, 'kategori') || str_contains($h, 'tipe')) {
                        $colMapping['type'] = $idx;
                    } elseif (str_contains($h, 'waktu') || str_contains($h, 'jam')) {
                        $colMapping['time'] = $idx;
                    } elseif (str_contains($h, 'judul') || str_contains($h, 'tema') || str_contains($h, 'khutbah')) {
                        $colMapping['title'] = $idx;
                    } elseif (str_contains($h, 'khatib')) {
                        $colMapping['khatib'] = $idx;
                    } elseif (str_contains($h, 'pembicara') || str_contains($h, 'ustadz') || str_contains($h, 'ustaz') || str_contains($h, 'pemateri') || str_contains($h, 'narasumber')) {
                        $colMapping['speaker'] = $idx;
                    } elseif (str_contains($h, 'mc') || str_contains($h, 'pembawa')) {
                        $colMapping['mc'] = $idx;
                    } elseif (str_contains($h, 'muadzin') || str_contains($h, 'bilal')) {
                        $colMapping['muadzin'] = $idx;
                    } elseif (str_contains($h, 'status')) {
                        $colMapping['status'] = $idx;
                    }
                }
                continue;
            }

            // Extract values using colMapping or position-based fallback
            $rawDate = '';
            $rawType = '';
            $rawTime = '';
            $rawHoliday = '';
            $rawTitle = '';
            $rawSpeaker = '';
            $rawKhatib = '';
            $rawMc = '';
            $rawMuadzin = '';
            $rawPhone = '';
            $rawStatus = '';

            if ($colMapping && $colMapping['date'] !== null) {
                $rawDate = $cols[$colMapping['date']] ?? '';
                $rawType = $colMapping['type'] !== null ? ($cols[$colMapping['type']] ?? '') : '';
                $rawTime = $colMapping['time'] !== null ? ($cols[$colMapping['time']] ?? '') : '';
                $rawHoliday = $colMapping['holiday'] !== null ? ($cols[$colMapping['holiday']] ?? '') : '';
                $rawTitle = $colMapping['title'] !== null ? ($cols[$colMapping['title']] ?? '') : '';
                $rawSpeaker = $colMapping['speaker'] !== null ? ($cols[$colMapping['speaker']] ?? '') : '';
                $rawKhatib = $colMapping['khatib'] !== null ? ($cols[$colMapping['khatib']] ?? '') : '';
                $rawMc = $colMapping['mc'] !== null ? ($cols[$colMapping['mc']] ?? '') : '';
                $rawMuadzin = $colMapping['muadzin'] !== null ? ($cols[$colMapping['muadzin']] ?? '') : '';
                $rawPhone = $colMapping['phone'] !== null ? ($cols[$colMapping['phone']] ?? '') : '';
                $rawStatus = $colMapping['status'] !== null ? ($cols[$colMapping['status']] ?? '') : '';
            } else {
                // If col 0 is a sequence number (1, 2, 3...), remove it
                if (is_numeric($cols[0]) && count($cols) >= 3 && strlen($cols[0]) <= 4 && SimpleXlsxService::parseFlexibleDate($cols[0]) === null) {
                    array_shift($cols);
                }

                // Check where date is located in remaining cols
                $dateColIdx = 0;
                if (SimpleXlsxService::parseFlexibleDate($cols[0] ?? '') === null) {
                    if (isset($cols[1]) && SimpleXlsxService::parseFlexibleDate($cols[1]) !== null) {
                        $dateColIdx = 1;
                        $rawType = $cols[0] ?? '';
                    }
                }

                $rawDate = $cols[$dateColIdx] ?? '';

                if ($this->importKajianType === 'jumat') {
                    $offset = $dateColIdx + 1;
                    $rawTime = $cols[$offset] ?? '';
                    $nextCol = $cols[$offset + 1] ?? '';
                    if (stripos($nextCol, 'libur') !== false || stripos($nextCol, 'aktif') !== false) {
                        $rawHoliday = $nextCol;
                        $rawTitle = $cols[$offset + 2] ?? '';
                        $rawKhatib = $cols[$offset + 3] ?? '';
                        $rawMc = $cols[$offset + 4] ?? '';
                        $rawMuadzin = $cols[$offset + 5] ?? '';
                        $rawPhone = $cols[$offset + 6] ?? '';
                        $rawStatus = $cols[$offset + 7] ?? '';
                    } else {
                        $rawTitle = $nextCol;
                        $rawKhatib = $cols[$offset + 2] ?? '';
                        $rawMc = $cols[$offset + 3] ?? '';
                        $rawMuadzin = $cols[$offset + 4] ?? '';
                        $rawPhone = $cols[$offset + 5] ?? '';
                        $rawStatus = $cols[$offset + 6] ?? '';
                    }
                } else {
                    $offset = $dateColIdx + 1;
                    $rawTime = $cols[$offset] ?? '';
                    $rawTitle = $cols[$offset + 1] ?? '';
                    $rawSpeaker = $cols[$offset + 2] ?? '';
                    $rawPhone = $cols[$offset + 3] ?? '';
                    $rawStatus = $cols[$offset + 4] ?? '';
                }
            }

            $parsedDate = SimpleXlsxService::parseFlexibleDate($rawDate);
            if (! $parsedDate) {
                continue; // Skip row without valid date
            }

            // Determine target type (jumat, pekanan, or tematik)
            $type = $this->importKajianType;
            if (! empty($rawType)) {
                $rawTypeLower = strtolower($rawType);
                if (str_contains($rawTypeLower, 'tematik')) {
                    $type = 'tematik';
                } elseif (str_contains($rawTypeLower, 'jumat')) {
                    $type = 'jumat';
                } elseif (str_contains($rawTypeLower, 'pekanan')) {
                    $type = 'pekanan';
                }
            }

            // Clean values
            $cleanVal = fn($val, $default = '-') => (trim($val) === '' || trim($val) === '-') ? $default : trim($val);
            $cleanPhone = function($val) {
                $val = trim((string)$val);
                if ($val === '' || $val === '-') return null;
                $num = preg_replace('/[^\d+]/', '', $val);
                if (str_starts_with($num, '+62')) {
                    $num = '0' . substr($num, 3);
                } elseif (str_starts_with($num, '62')) {
                    $num = '0' . substr($num, 2);
                }
                return $num ?: null;
            };

            $statusVal = strtoupper(trim($rawStatus));
            $status = ($statusVal === 'NONAKTIF' || $statusVal === 'BATAL') ? 'NONAKTIF' : 'AKTIF';

            if ($type === 'jumat') {
                $time = $cleanVal($rawTime, '11:45 - 12:45');
                $title = $cleanVal($rawTitle, 'Khutbah Shalat Jumat');
                $khatib = $cleanVal($rawKhatib ?: $rawSpeaker, '-');
                $mc = $cleanVal($rawMc, '-');
                $muadzin = $cleanVal($rawMuadzin, '-');
                $khatibPhone = $cleanPhone($rawPhone);
                $isHoliday = stripos($rawHoliday, 'libur') !== false;

                // Look for existing Friday on this date
                /** @var Kajian|null $existing */
                $existing = Kajian::query()
                    ->where('type', 'jumat')
                    ->whereDate('date', $parsedDate)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'time_display' => $time,
                        'title' => $title,
                        'khatib_name' => $khatib,
                        'mc_name' => $mc,
                        'muadzin_name' => $muadzin,
                        'khatib_phone' => $khatibPhone,
                        'is_holiday_disabled' => $isHoliday,
                    ]);
                } else {
                    Kajian::create([
                        'type' => 'jumat',
                        'date' => $parsedDate,
                        'time_display' => $time,
                        'title' => $title,
                        'khatib_name' => $khatib,
                        'mc_name' => $mc,
                        'muadzin_name' => $muadzin,
                        'khatib_phone' => $khatibPhone,
                        'is_holiday_disabled' => $isHoliday,
                    ]);
                }
                $importedCount++;
            } else {
                $time = $cleanVal($rawTime, '09:00 - 11:30');
                $title = $cleanVal($rawTitle, 'Kajian Islam Ilmiah');
                $speaker = $cleanVal($rawSpeaker ?: $rawKhatib, '-');
                $phone = $cleanPhone($rawPhone);

                // Look for existing kajian on this date and type
                /** @var Kajian|null $existing */
                $existing = Kajian::query()
                    ->whereDate('date', $parsedDate)
                    ->where('type', $type)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'time_display' => $time,
                        'title' => $title,
                        'speaker_name' => $speaker,
                        'speaker_phone' => $phone,
                    ]);
                } else {
                    Kajian::create([
                        'type' => $type,
                        'date' => $parsedDate,
                        'time_display' => $time,
                        'title' => $title,
                        'speaker_name' => $speaker,
                        'speaker_phone' => $phone,
                    ]);
                }
                $importedCount++;
            }
        }

        return $importedCount;
    }

    // ==========================================
    // Prayer Duty / Penugasan Ibadah Actions (Milestone 2)
    // ==========================================
    public function openCreatePrayerDuty(?string $day = null, ?string $prayerTime = null): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->isEditingPrayerDuty = false;
        $this->editingPrayerDutyId = null;
        $this->dutyDayName = $day ?: 'Senin';
        $this->dutyPrayerTime = $prayerTime ?: 'dzuhur';
        $this->dutyWeekPattern = 'semua';
        $this->dutyTahun = ($this->prayerDutyFilterYear !== 'all' && is_numeric($this->prayerDutyFilterYear)) ? (int) $this->prayerDutyFilterYear : (int) Carbon::now('Asia/Jakarta')->year;
        $this->dutyImamName = '';
        $this->dutyMuadzinName = '';
        $this->dutyNotes = '';
        $this->showPrayerDutyModal = true;
        $this->dispatch('open-duty-modal');
    }

    public function openFridayKajianFromPetugas(?int $kajianId = null): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->currentTab = 'kegiatan';
        $this->kegiatanSubTab = 'jumat';
        $this->kajianSubTab = 'jumat';

        $k = null;
        if ($kajianId) {
            $k = Kajian::find($kajianId);
        }

        if (! $k) {
            $k = Kajian::jumat()
                ->whereDate('date', '>=', Carbon::now('Asia/Jakarta')->startOfMonth()->subWeeks(1)->format('Y-m-d'))
                ->orderBy('date')
                ->first();
        }

        if (! $k) {
            $k = Kajian::jumat()->orderBy('date', 'desc')->first();
        }

        if ($k) {
            $data = [
                'id' => $k->id,
                'type' => $k->type,
                'date' => $k->date ? Carbon::parse($k->date)->format('Y-m-d') : '',
                'time_display' => $k->time_display ?? '11:45 - 12:45',
                'title' => $k->title ?? '',
                'speaker_name' => '',
                'speaker_phone' => '',
                'is_holiday_disabled' => (bool) $k->is_holiday_disabled,
                'khatib_name' => $k->khatib_name ?? '',
                'mc_name' => $k->mc_name ?? '',
                'muadzin_name' => $k->muadzin_name ?? '',
                'khatib_phone' => $k->khatib_phone ?? '',
                'mc_notes' => $k->mc_notes ?? '',
            ];

            $this->showKajianModal = true;
            $this->dispatch('open-edit-kajian-modal', data: $data);
        } else {
            $this->showKajianModal = true;
            $this->dispatch('open-create-kajian-modal', type: 'jumat');
        }
    }

    public function openEditPrayerDuty(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $duty = PrayerDuty::findOrFail($id);
        $this->isEditingPrayerDuty = true;
        $this->editingPrayerDutyId = $duty->id;
        $this->dutyDayName = $duty->day_name;
        $this->dutyPrayerTime = $duty->prayer_time;
        $this->dutyWeekPattern = $duty->week_pattern;
        $this->dutyTahun = $duty->tahun ? (int) $duty->tahun : (int) Carbon::now('Asia/Jakarta')->year;
        $this->dutyImamName = $duty->imam_name ?? '';
        $this->dutyMuadzinName = $duty->muadzin_name ?? '';
        $this->dutyNotes = '';
        $this->showPrayerDutyModal = true;
        $this->dispatch('open-duty-modal');
    }

    public function savePrayerDuty(?array $data = null): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if ($data) {
            $this->editingPrayerDutyId = !empty($data['id']) ? (int) $data['id'] : null;
            $this->isEditingPrayerDuty = !empty($data['id']);
            $this->dutyDayName = $data['day_name'] ?? 'Senin';
            $this->dutyPrayerTime = $data['prayer_time'] ?? 'dzuhur';
            $this->dutyWeekPattern = $data['week_pattern'] ?? 'semua';
            $this->dutyTahun = !empty($data['tahun']) ? (int) $data['tahun'] : (int) Carbon::now('Asia/Jakarta')->year;
            $this->dutyImamName = trim($data['imam_name'] ?? '');
            $this->dutyMuadzinName = trim($data['muadzin_name'] ?? '');
        }

        $this->validate([
            'dutyDayName' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'dutyPrayerTime' => 'required|in:dzuhur,ashar',
            'dutyWeekPattern' => 'required|in:semua,pekan_1,pekan_2,pekan_3,pekan_4,pekan_5,pekan_1_3_5,pekan_2_4',
            'dutyTahun' => 'nullable|integer|min:2020|max:2099',
            'dutyImamName' => 'nullable|string|max:255',
            'dutyMuadzinName' => 'nullable|string|max:255',
        ]);

        $payload = [
            'day_name' => $this->dutyDayName,
            'prayer_time' => $this->dutyPrayerTime,
            'week_pattern' => $this->dutyWeekPattern,
            'tahun' => $this->dutyTahun ?: (int) Carbon::now('Asia/Jakarta')->year,
            'imam_name' => $this->dutyImamName ?: null,
            'muadzin_name' => $this->dutyMuadzinName ?: null,
        ];

        if ($this->isEditingPrayerDuty && $this->editingPrayerDutyId) {
            $duty = PrayerDuty::findOrFail($this->editingPrayerDutyId);
            $duty->update($payload);
            $this->notify('Jadwal penugasan berhasil diperbarui.');
        } else {
            PrayerDuty::create($payload);
            $this->notify('Jadwal penugasan baru berhasil ditambahkan.');
        }

        $this->showPrayerDutyModal = false;
        $this->dispatch('close-duty-modal');
    }

    public function deletePrayerDuty(int|array $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (is_array($id)) {
            $ids = array_values(array_filter($id));
            if (!empty($ids)) {
                PrayerDuty::whereIn('id', $ids)->delete();
                $this->notify('Jadwal penugasan berhasil dihapus.');
            }
            return;
        }

        $duty = PrayerDuty::find($id);
        if (! $duty) {
            $this->notify('Jadwal penugasan telah dihapus.');
            return;
        }

        $duty->delete();
        $this->notify('Jadwal penugasan berhasil dihapus.');
    }

    public function openImportDuty(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->importDutyTab = 'paste';
        $this->importDutyPasteText = '';
        $this->importDutyFile = null;
        $this->showImportDutyModal = true;
        $this->dispatch('open-import-duty-modal');
    }

    public function processImportDutyPaste(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (trim($this->importDutyPasteText) === '') {
            $this->notify('Silakan tempelkan (paste) data dari Excel / Spreadsheet terlebih dahulu.');
            return;
        }

        $rows = SimpleXlsxService::parseCsvText($this->importDutyPasteText);
        $importedCount = $this->importDutyRows($rows);

        $this->notify("Berhasil mengimpor {$importedCount} data penugasan ibadah!");
        $this->showImportDutyModal = false;
        $this->importDutyPasteText = '';
        $this->dispatch('close-import-duty-modal');
    }

    public function processImportDutyFile(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->validate([
            'importDutyFile' => 'required|file|max:5120|extensions:xlsx,xls,csv,txt',
        ]);

        $path = $this->importDutyFile->getRealPath();
        $ext = $this->importDutyFile->getClientOriginalExtension();

        $rows = SimpleXlsxService::parseFile($path, $ext);
        $importedCount = $this->importDutyRows($rows);

        $this->notify("Berhasil mengunggah dan mengimpor {$importedCount} data penugasan ibadah dari file Excel!");
        $this->showImportDutyModal = false;
        $this->importDutyFile = null;
        $this->dispatch('close-import-duty-modal');
    }

    /**
     * Parse structured rows and update or create PrayerDuty records.
     *
     * @param array<int, array<int, string>> $rows
     * @return int
     */
    protected function importDutyRows(array $rows): int
    {
        $importedCount = 0;
        $targetYear = ($this->prayerDutyFilterYear !== 'all' && is_numeric($this->prayerDutyFilterYear))
            ? (int) $this->prayerDutyFilterYear
            : (int) Carbon::now('Asia/Jakarta')->year;

        $colMapping = null;

        foreach ($rows as $cols) {
            if (empty($cols)) {
                continue;
            }

            $cols = array_map(fn($c) => trim((string)$c), $cols);

            if (count(array_filter($cols, fn($c) => $c !== '')) === 0) {
                continue;
            }

            $rowString = strtolower(implode(' ', $cols));
            if (str_contains($rowString, 'hari') || (str_contains($rowString, 'waktu') && str_contains($rowString, 'imam'))) {
                $colMapping = [
                    'day' => null,
                    'time' => null,
                    'imam' => null,
                    'muadzin' => null,
                    'pattern' => null,
                    'notes' => null,
                ];

                foreach ($cols as $idx => $headerText) {
                    $h = strtolower($headerText);
                    if (str_contains($h, 'hari')) {
                        $colMapping['day'] = $idx;
                    } elseif (str_contains($h, 'waktu') || str_contains($h, 'shalat') || str_contains($h, 'sholat')) {
                        $colMapping['time'] = $idx;
                    } elseif (str_contains($h, 'imam')) {
                        $colMapping['imam'] = $idx;
                    } elseif (str_contains($h, 'muadzin') || str_contains($h, 'bilal')) {
                        $colMapping['muadzin'] = $idx;
                    } elseif (str_contains($h, 'pola') || str_contains($h, 'pekan')) {
                        $colMapping['pattern'] = $idx;
                    } elseif (str_contains($h, 'catatan') || str_contains($h, 'keterangan') || str_contains($h, 'notes')) {
                        $colMapping['notes'] = $idx;
                    }
                }
                continue;
            }

            if ($colMapping && $colMapping['day'] !== null && $colMapping['time'] !== null) {
                $rawDay = $cols[$colMapping['day']] ?? '';
                $rawPrayerTime = $cols[$colMapping['time']] ?? '';
                $rawImam = $colMapping['imam'] !== null ? ($cols[$colMapping['imam']] ?? '') : '';
                $rawMuadzin = $colMapping['muadzin'] !== null ? ($cols[$colMapping['muadzin']] ?? '') : '';
                $rawPattern = $colMapping['pattern'] !== null ? ($cols[$colMapping['pattern']] ?? '') : '';
                $rawNotes = $colMapping['notes'] !== null ? ($cols[$colMapping['notes']] ?? '') : '';
            } else {
                if (is_numeric($cols[0]) && count($cols) >= 3 && strlen($cols[0]) <= 3) {
                    array_shift($cols);
                }

                if (isset($cols[1]) && (str_contains(strtolower($cols[1]), 'dzu') || str_contains(strtolower($cols[1]), 'zuh') || str_contains(strtolower($cols[1]), 'ash') || str_contains(strtolower($cols[1]), 'asr'))) {
                    $rawDay = $cols[0] ?? '';
                    $rawPrayerTime = $cols[1] ?? '';
                    $rawImam = $cols[2] ?? '';
                    $rawMuadzin = $cols[3] ?? '';
                    $rawPattern = $cols[4] ?? '';
                    $rawNotes = $cols[5] ?? '';
                } elseif (isset($cols[2]) && (str_contains(strtolower($cols[2]), 'dzu') || str_contains(strtolower($cols[2]), 'zuh') || str_contains(strtolower($cols[2]), 'ash') || str_contains(strtolower($cols[2]), 'asr'))) {
                    $rawDay = $cols[0] ?? '';
                    $rawPattern = $cols[1] ?? '';
                    $rawPrayerTime = $cols[2] ?? '';
                    $rawImam = $cols[3] ?? '';
                    $rawMuadzin = $cols[4] ?? '';
                    $rawNotes = $cols[5] ?? '';
                } else {
                    $rawDay = $cols[0] ?? '';
                    $rawPrayerTime = $cols[1] ?? '';
                    $rawImam = $cols[2] ?? '';
                    $rawMuadzin = $cols[3] ?? '';
                    $rawPattern = $cols[4] ?? '';
                    $rawNotes = $cols[5] ?? '';
                }
            }

            $rawDayLower = strtolower($rawDay);
            $day = match (true) {
                str_contains($rawDayLower, 'sen') => 'Senin',
                str_contains($rawDayLower, 'sel') => 'Selasa',
                str_contains($rawDayLower, 'rab') => 'Rabu',
                str_contains($rawDayLower, 'kam') => 'Kamis',
                str_contains($rawDayLower, 'jum') => 'Jumat',
                default => null,
            };

            if (! $day) {
                continue;
            }

            $rawTimeLower = strtolower($rawPrayerTime);
            $prayerTime = (str_contains($rawTimeLower, 'ash') || str_contains($rawTimeLower, 'asr')) ? 'ashar' : 'dzuhur';

            $imam = trim($rawImam);
            $imam = ($imam === '' || $imam === '-') ? null : $imam;

            $muadzin = trim($rawMuadzin);
            $muadzin = ($muadzin === '' || $muadzin === '-') ? null : $muadzin;

            $rawPatLower = strtolower($rawPattern);
            $pattern = 'semua';
            if (str_contains($rawPatLower, '1_3_5') || str_contains($rawPatLower, '1, 3, 5') || str_contains($rawPatLower, 'ganjil') || str_contains($rawPatLower, '1,3,5')) {
                $pattern = 'pekan_1_3_5';
            } elseif (str_contains($rawPatLower, '2_4') || str_contains($rawPatLower, '2, 4') || str_contains($rawPatLower, 'genap') || str_contains($rawPatLower, '2,4')) {
                $pattern = 'pekan_2_4';
            } elseif (preg_match('/pekan[_\s]?([1-5])/', $rawPatLower, $m)) {
                $pattern = 'pekan_' . $m[1];
            } elseif (preg_match('/^([1-5])$/', $rawPatLower, $m)) {
                $pattern = 'pekan_' . $m[1];
            }

            PrayerDuty::updateOrCreate(
                [
                    'day_name' => $day,
                    'prayer_time' => $prayerTime,
                    'week_pattern' => $pattern,
                    'tahun' => $targetYear,
                ],
                [
                    'imam_name' => $imam,
                    'muadzin_name' => $muadzin,
                ]
            );

            $importedCount++;
        }

        return $importedCount;
    }

    // ==========================================
    // Agenda Actions (Milestone 3)
    // ==========================================
    public function openCreateAgenda(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->isEditingAgenda = false;
        $this->editingAgendaId = null;
        $this->agendaTitle = '';
        $this->agendaDescription = '';
        $this->agendaDate = Carbon::now()->addDays(7)->format('Y-m-d');
        $this->agendaCommitteeMembers = '';
        $this->agendaBudget = '0';
        $this->agendaStatus = 'Direncanakan';
        $this->agendaReportSummary = '';
        $this->agendaReportPdf = null;
        $this->showAgendaModal = true;
        $this->dispatch('open-agenda-modal');
    }

    public function editAgenda(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $agenda = Agenda::findOrFail($id);
        $this->isEditingAgenda = true;
        $this->editingAgendaId = $agenda->id;
        $this->agendaTitle = $agenda->title;
        $this->agendaDescription = $agenda->description ?? '';
        $this->agendaDate = Carbon::parse($agenda->event_date)->format('Y-m-d');
        $this->agendaCommitteeMembers = $agenda->committee_members ?? '';
        $this->agendaBudget = (string) (int) $agenda->budget;
        $this->agendaStatus = $agenda->status ?? 'Direncanakan';
        $this->agendaReportSummary = $agenda->report_summary ?? '';
        $this->agendaReportPdf = null;
        $this->showAgendaModal = true;
        $this->dispatch('open-agenda-modal');
    }

    public function saveAgenda(?array $clientData = null): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (! empty($clientData)) {
            if (isset($clientData['id'])) {
                if (! empty($clientData['id'])) {
                    $this->isEditingAgenda = true;
                    $this->editingAgendaId = (int) $clientData['id'];
                } else {
                    $this->isEditingAgenda = false;
                    $this->editingAgendaId = null;
                }
            }

            $this->agendaTitle = trim($clientData['title'] ?? '');
            $this->agendaDescription = trim($clientData['description'] ?? '');
            $this->agendaDate = trim($clientData['event_date'] ?? '');
            $this->agendaCommitteeMembers = trim($clientData['committee_members'] ?? '');
            $this->agendaBudget = trim((string) ($clientData['budget'] ?? '0'));
            $this->agendaStatus = $clientData['status'] ?? 'Direncanakan';
            $this->agendaReportSummary = trim($clientData['report_summary'] ?? '');
            $this->agendaYoutubeUrl = trim($clientData['youtube_url'] ?? '');
        }

        $this->validate([
            'agendaTitle' => 'required|string|max:255',
            'agendaDate' => 'required|date',
            'agendaStatus' => 'required|in:Direncanakan,Berjalan,SELESAI',
            'agendaReportPdf' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'agendaTitle.required' => 'Judul agenda kegiatan wajib diisi.',
            'agendaDate.required' => 'Tanggal kegiatan wajib diisi.',
            'agendaStatus.required' => 'Status kegiatan wajib dipilih.',
            'agendaReportPdf.file' => 'Berkas LPJ harus berupa file yang valid.',
            'agendaReportPdf.mimes' => 'Dokumen LPJ harus berformat PDF (.pdf).',
            'agendaReportPdf.max' => 'Ukuran file LPJ maksimal 10 MB.',
        ]);

        $cleanBudget = (float) preg_replace('/\D/', '', (string) $this->agendaBudget);

        $payload = [
            'title' => $this->agendaTitle,
            'description' => $this->agendaDescription,
            'event_date' => $this->agendaDate,
            'committee_members' => $this->agendaCommitteeMembers,
            'budget' => $cleanBudget,
            'status' => $this->agendaStatus,
            'youtube_url' => !empty($this->agendaYoutubeUrl) ? trim($this->agendaYoutubeUrl) : null,
        ];

        if (!empty($this->agendaReportSummary)) {
            $payload['report_summary'] = $this->agendaReportSummary;
        }

        if ($this->agendaReportPdf) {
            $pdfPath = $this->agendaReportPdf->store('lpj', 'public');
            $payload['report_pdf_path'] = $pdfPath;

            // Jika sedang mengedit dan sebelumnya sudah ada berkas PDF lama, hapus berkas lama
            if ($this->isEditingAgenda && $this->editingAgendaId) {
                $oldAgenda = Agenda::find($this->editingAgendaId);
                if ($oldAgenda && $oldAgenda->report_pdf_path && $oldAgenda->report_pdf_path !== $pdfPath) {
                    if (Storage::disk('public')->exists($oldAgenda->report_pdf_path)) {
                        Storage::disk('public')->delete($oldAgenda->report_pdf_path);
                    }
                }
            }
        }

        if ($this->isEditingAgenda && $this->editingAgendaId) {
            $a = Agenda::findOrFail($this->editingAgendaId);
            $a->update($payload);
            $this->notify('Agenda kegiatan berhasil diperbarui.');
        } else {
            Agenda::create($payload);
            $this->notify('Agenda kegiatan baru berhasil disimpan.');
        }

        $this->isEditingAgenda = false;
        $this->editingAgendaId = null;
        $this->agendaReportPdf = null;
        $this->showAgendaModal = false;
        $this->dispatch('close-agenda-modal');
    }

    public function updatedAgendaReportPdf(): void
    {
        $this->showAgendaModal = true;
        $this->validateOnly('agendaReportPdf', [
            'agendaReportPdf' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'agendaReportPdf.file' => 'Berkas LPJ harus berupa file yang valid.',
            'agendaReportPdf.mimes' => 'Dokumen LPJ harus berformat PDF (.pdf).',
            'agendaReportPdf.max' => 'Ukuran file LPJ maksimal 10 MB.',
        ]);
    }

    public function deleteAgenda(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $agenda = Agenda::find($id);
        if (! $agenda) {
            $this->notify('Agenda kegiatan telah dihapus.');
            return;
        }

        if ($agenda->report_pdf_path && Storage::disk('public')->exists($agenda->report_pdf_path)) {
            Storage::disk('public')->delete($agenda->report_pdf_path);
        }

        $agenda->delete();
        $this->notify('Agenda kegiatan berhasil dihapus.');
    }

    public function updateAgendaStatus(int $id, string $status): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (! in_array($status, ['Direncanakan', 'Berjalan', 'SELESAI'])) {
            return;
        }

        $agenda = Agenda::findOrFail($id);
        $agenda->update(['status' => $status]);
        $this->notify("Status kegiatan diubah menjadi: {$status}.");
    }

    public function selectAgendaForReport(int $id): void
    {
        $this->selectedAgendaForReportId = $id;
        $this->agendaViewMode = 'report';
    }

    public function openImportAgenda(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->importAgendaTab = 'paste';
        $this->importAgendaPasteText = '';
        $this->importAgendaFile = null;
        $this->showImportAgendaModal = true;
        $this->dispatch('open-import-agenda-modal');
    }

    public function processImportAgendaPaste(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (trim($this->importAgendaPasteText) === '') {
            $this->notify('Silakan tempelkan data agenda dari Excel terlebih dahulu.');
            return;
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($this->importAgendaPasteText));
        $importedCount = 0;

        foreach ($lines as $index => $line) {
            $cols = preg_split('/\t|,/', trim($line));
            $cols = array_map(function ($val) {
                return trim($val, " \t\n\r\0\x0B\"'");
            }, $cols);

            if (empty($cols[0])) continue;

            // Skip header line
            if ($index === 0 && (stripos($cols[0], 'nama') !== false || stripos($cols[0], 'agenda') !== false || stripos($cols[0], 'no') !== false)) {
                continue;
            }

            // Detect if first column is sequence number
            $title = is_numeric($cols[0]) && isset($cols[1]) ? $cols[1] : $cols[0];
            $dateStr = isset($cols[2]) ? $cols[2] : (isset($cols[1]) ? $cols[1] : '');
            $budgetRaw = isset($cols[3]) ? $cols[3] : '0';
            $committee = isset($cols[4]) ? str_replace('; ', "\n", $cols[4]) : '';
            $status = isset($cols[5]) && in_array(trim($cols[5]), ['Direncanakan', 'Berjalan', 'SELESAI']) ? trim($cols[5]) : 'Direncanakan';

            try {
                $parsedDate = Carbon::parse($dateStr)->format('Y-m-d');
            } catch (\Exception $e) {
                $parsedDate = Carbon::now()->addDays(7)->format('Y-m-d');
            }

            $budgetClean = (float) preg_replace('/[^0-9.]/', '', $budgetRaw);

            Agenda::create([
                'title' => $title,
                'event_date' => $parsedDate,
                'budget' => $budgetClean,
                'committee_members' => $committee,
                'status' => $status,
                'description' => 'Diimpor dari berkas Excel/Spreadsheet',
            ]);
            $importedCount++;
        }

        $this->showImportAgendaModal = false;
        $this->dispatch('close-import-agenda-modal');
        $this->notify("Berhasil mengimpor {$importedCount} data agenda kegiatan.");
    }

    public function processImportAgendaFile(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->validate([
            'importAgendaFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->importAgendaFile->getRealPath();
        $content = file_get_contents($path);
        $this->importAgendaPasteText = $content;
        $this->processImportAgendaPaste();
    }

    // ==========================================
    // ODOJ Actions (Milestone 3)
    // ==========================================
    public function toggleOdojStatus(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $entry = OdojEntry::findOrFail($id);
        $entry->toggle();
        $this->notify("Status tilawah Juz {$entry->juz_number} ({$entry->jamaah_name}) diubah: {$entry->status}.");
    }

    public function setSingleOdojStatus(int $id, string $status): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (! in_array($status, ['Selesai', 'Belum'])) return;

        $entry = OdojEntry::findOrFail($id);
        $entry->status = $status;
        $entry->completed_at = $status === 'Selesai' ? Carbon::now() : null;
        $entry->save();

        $this->notify("Tilawah Juz {$entry->juz_number} ({$entry->jamaah_name}) ditandai: " . ($status === 'Selesai' ? 'Sudah' : 'Belum') . ".");
    }

    public function openAssignOdojModal(?int $juz = null): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->assignJuzNumber = $juz ?: 1;
        $this->loadAssignJuzData();

        $this->showAssignOdojModal = true;
        $this->dispatch('open-assign-odoj-modal');
    }

    public function previousOdojDate(): void
    {
        $current = $this->odojDate ? Carbon::parse($this->odojDate) : Carbon::today();
        $this->odojDate = $current->subDay()->format('Y-m-d');
    }

    public function nextOdojDate(): void
    {
        $current = $this->odojDate ? Carbon::parse($this->odojDate) : Carbon::today();
        $this->odojDate = $current->addDay()->format('Y-m-d');
    }

    public function todayOdojDate(): void
    {
        $this->odojDate = Carbon::today()->format('Y-m-d');
    }

    public function updatedAssignJuzNumber(): void
    {
        $this->loadAssignJuzData();
    }

    public function loadAssignJuzData(): void
    {
        $targetDate = $this->odojDate ?: Carbon::today()->format('Y-m-d');
        $entry = OdojEntry::getEntriesForDate($targetDate)->where('juz_number', $this->assignJuzNumber)->first();
        if ($entry) {
            $parts = preg_split('/[\/,]/', $entry->jamaah_name);
            $this->assignPegawai1 = trim($parts[0] ?? '');
            $this->assignPegawai2 = trim($parts[1] ?? '');
        } else {
            $this->assignPegawai1 = '';
            $this->assignPegawai2 = '';
        }
    }

    public function saveOdojAssignment(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        $this->validate([
            'assignJuzNumber' => 'required|integer|min:1|max:30',
            'assignPegawai1' => 'required|string|max:100',
            'assignPegawai2' => 'nullable|string|max:100',
        ]);

        $combinedName = trim($this->assignPegawai1);
        if (trim($this->assignPegawai2) !== '') {
            $combinedName .= ' / ' . trim($this->assignPegawai2);
        }

        $targetDate = $this->odojDate ?: Carbon::today()->format('Y-m-d');
        $entry = OdojEntry::forDate($targetDate)->where('juz_number', $this->assignJuzNumber)->first();
        if ($entry) {
            $entry->update(['jamaah_name' => $combinedName]);
        } else {
            OdojEntry::create([
                'group_name' => 'Laporan Madya Malang Bertilawah',
                'jamaah_name' => $combinedName,
                'juz_number' => $this->assignJuzNumber,
                'target_date' => $targetDate,
                'status' => 'Belum',
            ]);
        }

        // Propagate to future dates that haven't been completed yet
        $futureDates = OdojEntry::whereDate('target_date', '>', $targetDate)
            ->selectRaw('DATE(target_date) as tdate')
            ->groupBy('tdate')
            ->pluck('tdate');

        $targetCarbon = Carbon::parse($targetDate)->startOfDay();
        foreach ($futureDates as $fDate) {
            $hasCompleted = OdojEntry::forDate($fDate)->where('status', 'Selesai')->exists();
            if (! $hasCompleted) {
                $days = (int) $targetCarbon->diffInDays(Carbon::parse($fDate)->startOfDay(), false);
                $futureJuz = OdojEntry::rotateJuz($this->assignJuzNumber, $days);
                OdojEntry::forDate($fDate)
                    ->where('juz_number', $futureJuz)
                    ->update(['jamaah_name' => $combinedName]);
            }
        }

        $this->showAssignOdojModal = false;
        $this->dispatch('close-assign-odoj-modal');
        $this->notify("Penugasan Juz {$this->assignJuzNumber} berhasil disimpan ({$combinedName}).");
    }

    public function setAllOdojStatus(string $status): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');
            return;
        }

        if (! in_array($status, ['Selesai', 'Belum'])) return;

        $targetDate = $this->odojDate ?: Carbon::today()->format('Y-m-d');
        OdojEntry::forDate($targetDate)->update([
            'status' => $status,
            'completed_at' => $status === 'Selesai' ? Carbon::now() : null,
        ]);

        $this->notify("Seluruh 30 Juz ditandai: {$status}.");
    }

    public function getOdojWhatsappText(): string
    {
        $targetDate = $this->odojDate ?: Carbon::today()->format('Y-m-d');
        $entries = OdojEntry::getEntriesForDate($targetDate);
        $formattedDate = Carbon::parse($targetDate)->translatedFormat('l, d F Y');

        $text = "*Bismillahirrohmanirrohim*\n\n";
        $text .= "📖 *LAPORAN MADYA MALANG BERTILAWAH*\n";
        $text .= "🗓️ *Hari/Tanggal:* {$formattedDate}\n";
        $text .= "--------------------------------------\n";

        foreach ($entries as $e) {
            $icon = $e->status === 'Selesai' ? '✅' : '⏳';
            $juzPad = str_pad($e->juz_number, 2, '0', STR_PAD_LEFT);
            $text .= "{$icon} Juz {$juzPad}: *{$e->jamaah_name}* (" . strtoupper($e->status) . ")\n";
        }

        $completed = $entries->where('status', 'Selesai')->count();
        $text .= "--------------------------------------\n";
        $text .= "✨ *Progres:* {$completed}/30 Juz Selesai\n";
        $text .= "_Semoga Allah meridhoi tilawah kita dan menjadikannya syafaat kelak di hari kiamat. Aamiin._ 🤲";

        return $text;
    }

    // ==========================================
    // Settings Actions
    // ==========================================
    public function saveSettings(PrayerTimeService $prayerService): void
    {
        if (! Auth::user()->isAdmin()) {
            $this->notify('Akses ditolak: Hanya Admin DKM yang berwenang mengubah pengaturan masjid.');
            return;
        }

        $settings = MasjidSetting::getActive();

        $lines = array_filter(array_map('trim', explode("\n", $this->settingsTvAnnouncementsText)));

        $cities = $prayerService->getCities();
        foreach ($cities as $c) {
            if ($c['id'] == $this->settingsCityId) {
                $this->settingsCityName = $c['lokasi'];
                break;
            }
        }

        $settings->update([
            'name' => $this->settingsName,
            'address' => $this->settingsAddress,
            'phone' => $this->settingsPhone,
            'email' => $this->settingsEmail,
            'city_id' => $this->settingsCityId,
            'city_name' => $this->settingsCityName,
            'qibla_angle' => $this->settingsQiblaAngle,
            'iqamah_delay_minutes' => $this->settingsIqamahDelay,
            'bank_accounts' => [
                [
                    'bank' => $this->settingsBankName ?: 'BSI (Bank Syariah Indonesia)',
                    'account_number' => $this->settingsBankAccount ?: '7123-4567-89',
                    'holder' => $this->settingsBankHolder ?: 'DKM Masjid Salahuddin',
                ]
            ],
            'tv_announcements' => array_values($lines),
            'friday_prayer_info' => [
                'khatib' => $this->settingsFridayKhatib,
                'imam' => $this->settingsFridayImam,
                'muadzin' => $this->settingsFridayMuadzin,
                'date' => 'Jumat Mendatang',
                'time' => '11:54 WIB',
            ],
        ]);

        $this->notify('Pengaturan masjid, infaq, & TV display berhasil disimpan!');
    }

    // ==========================================
    // User Management Actions (Milestone 4)
    // ==========================================
    public function openCreateUser(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus yang berwenang menambah pengguna.');
            return;
        }

        $this->isEditingUser = false;
        $this->editingUserId = null;
        $this->userName = '';
        $this->userEmail = '';
        $this->userPassword = '';
        $this->userRole = 'Jamaah';
        $this->userStatus = 'AKTIF';
        $this->showUserModal = true;
        $this->dispatch('open-user-modal');
    }

    public function openEditUser(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus yang berwenang mengubah data pengguna.');
            return;
        }

        $user = User::findOrFail($id);
        $this->isEditingUser = true;
        $this->editingUserId = $user->id;
        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userPassword = '';
        $this->userRole = $user->role;
        $this->userStatus = $user->status ?? 'AKTIF';
        $this->showUserModal = true;
        $this->dispatch('open-user-modal');
    }

    public function saveUser(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk menyimpan data pengguna.');
            return;
        }

        $emailRule = 'required|email|max:255|unique:users,email';
        if ($this->isEditingUser && $this->editingUserId) {
            $emailRule .= ',' . $this->editingUserId;
        }

        $passwordRule = PasswordRule::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        $rules = [
            'userName' => 'required|string|max:255',
            'userEmail' => $emailRule,
            'userRole' => 'required|string|in:Master,Ketua,Sekretaris,Bendahara,Jamaah,admin,operator,viewer',
            'userStatus' => 'required|in:AKTIF,NONAKTIF',
        ];

        if (! $this->isEditingUser) {
            $rules['userPassword'] = ['required', 'string', $passwordRule];
        } else {
            $rules['userPassword'] = ['nullable', 'string', $passwordRule];
        }

        $this->validate($rules, [
            'userName.required' => 'Nama pengguna wajib diisi.',
            'userEmail.required' => 'Email wajib diisi.',
            'userEmail.unique' => 'Email ini sudah terdaftar.',
            'userPassword.required' => 'Kata sandi wajib diisi untuk pengguna baru.',
        ]);

        $data = [
            'name' => $this->userName,
            'email' => $this->userEmail,
            'role' => $this->userRole,
            'status' => $this->userStatus,
        ];

        if (! empty($this->userPassword)) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->userPassword);
        }

        if ($this->isEditingUser && $this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update($data);
            $this->notify("Data pengguna {$user->name} berhasil diperbarui.");
        } else {
            User::create($data);
            $this->notify('Pengguna baru berhasil ditambahkan.');
        }

        $this->showUserModal = false;
        $this->dispatch('close-user-modal');
    }

    public function deleteUser(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk menghapus pengguna.');
            return;
        }

        if ($id === Auth::id()) {
            $this->notify('Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
            return;
        }

        $user = User::find($id);
        if (! $user) {
            $this->notify('Pengguna telah dihapus.');
            return;
        }

        $name = $user->name;
        $user->delete();
        $this->notify("Pengguna {$name} berhasil dihapus.");
    }

    public function toggleUserStatus(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk mengubah status.');
            return;
        }

        if ($id === Auth::id()) {
            $this->notify('Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return;
        }

        $user = User::findOrFail($id);
        $user->status = ($user->status === 'AKTIF') ? 'NONAKTIF' : 'AKTIF';
        $user->save();
        $this->notify("Status {$user->name} diubah menjadi {$user->status}.");
    }

    // ==========================================
    // Master Data Pegawai Actions
    // ==========================================
    private function ensureEmployeeTableExists(): bool
    {
        try {
            if (Schema::hasTable('employees')) {
                return true;
            }

            // 1. Buat tabel employees secara langsung via Schema Builder (bebas hambatan foreign-key/artisan lock)
            Schema::create('employees', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('nip_pendek', 15)->nullable()->index();
                $table->string('nip_panjang', 25)->nullable()->index();
                $table->string('name')->index();
                $table->string('jabatan')->nullable();
                $table->string('unit_kerja')->default('KPP Madya Malang');
                $table->enum('gender', ['L', 'P'])->nullable();
                $table->string('status', 20)->default('AKTIF')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->timestamps();
            });

            // Sinkronkan ke tabel migrations agar artisan tidak mencoba membuat ulang
            try {
                $migrationName = '2026_10_13_110000_create_employees_table';
                if (Schema::hasTable('migrations') && ! DB::table('migrations')->where('migration', $migrationName)->exists()) {
                    $maxBatch = DB::table('migrations')->max('batch') ?? 0;
                    DB::table('migrations')->insert([
                        'migration' => $migrationName,
                        'batch' => $maxBatch + 1,
                    ]);
                }
            } catch (\Throwable $mEx) {
                // Abaikan kesalahan pencatatan riwayat migrasi
            }

            $this->ensureUserNipColumnExists();
            return Schema::hasTable('employees');
        } catch (\Throwable $e) {
            // 2. Fallback: coba panggil artisan migrate jika Schema::create terkendala
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $ex) {
                Log::warning('Fallback migrate employees failed: ' . $ex->getMessage());
            }

            $this->ensureUserNipColumnExists();
            return Schema::hasTable('employees');
        }
    }

    private function ensureUserNipColumnExists(): bool
    {
        try {
            if (Schema::hasColumn('users', 'nip')) {
                return true;
            }

            Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('nip', 30)->nullable()->after('email')->index();
            });

            return Schema::hasColumn('users', 'nip');
        } catch (\Throwable $e) {
            Log::warning('ensureUserNipColumnExists warning: ' . $e->getMessage());
            return false;
        }
    }

    private function syncUserNipIfEmpty(User $user, ?string $nip): void
    {
        if ($nip && empty($user->nip) && $this->ensureUserNipColumnExists()) {
            try {
                $user->update(['nip' => $nip]);
            } catch (\Throwable $e) {
                // Abaikan jika kolom bermasalah
            }
        }
    }

    private function findMatchingUser(?string $nip9, ?string $nip18, ?string $name, $cachedUsers = null): ?User
    {
        $hasNipCol = $this->ensureUserNipColumnExists();

        // 1. Cari berdasarkan NIP jika kolom nip tersedia di tabel users
        if ($hasNipCol) {
            if ($nip9) {
                $user = $cachedUsers ? $cachedUsers->firstWhere('nip', $nip9) : User::where('nip', $nip9)->first();
                if ($user) {
                    return $user;
                }
            }
            if ($nip18) {
                $user = $cachedUsers ? $cachedUsers->firstWhere('nip', $nip18) : User::where('nip', $nip18)->first();
                if ($user) {
                    return $user;
                }
            }
        }

        // 2. Cari berdasarkan awalan email jika berformat NIP (misal 817931806@pajak.go.id)
        if ($nip9) {
            $user = $cachedUsers
                ? $cachedUsers->first(fn($u) => str_starts_with(strtolower((string)$u->email), $nip9 . '@'))
                : User::where('email', 'like', $nip9 . '@%')->first();
            if ($user) {
                $this->syncUserNipIfEmpty($user, $nip18 ?: $nip9);
                return $user;
            }
        }
        if ($nip18) {
            $user = $cachedUsers
                ? $cachedUsers->first(fn($u) => str_starts_with(strtolower((string)$u->email), $nip18 . '@'))
                : User::where('email', 'like', $nip18 . '@%')->first();
            if ($user) {
                $this->syncUserNipIfEmpty($user, $nip18 ?: $nip9);
                return $user;
            }
        }

        // 3. Cari berdasarkan Nama Lengkap (persis)
        if ($name) {
            $user = $cachedUsers ? $cachedUsers->firstWhere('name', $name) : User::where('name', $name)->first();
            if ($user) {
                $this->syncUserNipIfEmpty($user, $nip18 ?: $nip9);
                return $user;
            }

            // 4. Pencocokan toleran nama pokok tanpa gelar akademik (misal: "Dhafin Fuad Mahathir, A.Md.Pjk." vs "Dhafin Fuad Mahathir")
            $norm = \App\Services\EmployeeStatusSyncService::normalizeName($name);
            if (! empty($norm) && strlen($norm) >= 5) {
                $candidates = $cachedUsers ?: User::all();
                foreach ($candidates as $cand) {
                    $candNorm = \App\Services\EmployeeStatusSyncService::normalizeName($cand->name);
                    if ($candNorm === $norm) {
                        $this->syncUserNipIfEmpty($cand, $nip18 ?: $nip9);
                        return $cand;
                    }
                }

                // 5. Pencocokan nama pada email instansi pegawai (misal: "dhafin.mahathir@pajak.go.id" vs "DHAFIN FUAD MAHATHIR")
                foreach ($candidates as $cand) {
                    $emailPrefix = explode('@', strtolower((string)$cand->email))[0] ?? '';
                    $emailNameNorm = \App\Services\EmployeeStatusSyncService::normalizeName(str_replace(['.', '_', '-'], ' ', $emailPrefix));
                    if (! empty($emailNameNorm) && strlen($emailNameNorm) >= 6) {
                        if ($emailNameNorm === $norm || str_contains($norm, $emailNameNorm) || str_contains($emailNameNorm, $norm)) {
                            $this->syncUserNipIfEmpty($cand, $nip18 ?: $nip9);
                            return $cand;
                        }
                    }
                }

                // 6. Substring match toleran untuk nama panjang (panjang kata >= 2)
                foreach ($candidates as $cand) {
                    $candNorm = \App\Services\EmployeeStatusSyncService::normalizeName($cand->name);
                    if (str_contains($norm, ' ') && str_contains($candNorm, ' ')) {
                        if (str_contains($norm, $candNorm) || str_contains($candNorm, $norm)) {
                            $this->syncUserNipIfEmpty($cand, $nip18 ?: $nip9);
                            return $cand;
                        }
                    }
                }
            }
        }

        return null;
    }

    public function autoLinkUnlinkedEmployees(): int
    {
        if (! $this->ensureEmployeeTableExists()) {
            return 0;
        }

        $unlinkedEmployees = Employee::whereNull('user_id')->get();
        if ($unlinkedEmployees->isEmpty()) {
            return 0;
        }

        $allUsers = User::all();
        if ($allUsers->isEmpty()) {
            return 0;
        }

        $linkedCount = 0;
        foreach ($unlinkedEmployees as $emp) {
            $matchedUser = $this->findMatchingUser($emp->nip_pendek, $emp->nip_panjang, $emp->name, $allUsers);
            if ($matchedUser) {
                $emp->update(['user_id' => $matchedUser->id]);
                $this->syncUserNipIfEmpty($matchedUser, $emp->nip_panjang ?: $emp->nip_pendek);
                $linkedCount++;
            }
        }

        return $linkedCount;
    }

    public function syncEmployeeLinks(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus yang berwenang menautkan akun pegawai.');
            return;
        }

        $linkedCount = $this->autoLinkUnlinkedEmployees();
        if ($linkedCount > 0) {
            $this->notify("Berhasil menautkan {$linkedCount} data pegawai ke akun aplikasi yang cocok.");
        } else {
            $this->notify('Seluruh data pegawai sudah tertaut secara optimal ke akun pengguna aplikasi.');
        }
    }

    public function openCreateEmployeeModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus yang berwenang menambah data pegawai.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->notify('Tabel pegawai belum siap di database.');
            return;
        }

        $this->editingEmployeeId = null;
        $this->employeeFormNipPendek = '';
        $this->employeeFormNipPanjang = '';
        $this->employeeFormName = '';
        $this->employeeFormJabatan = '';
        $this->employeeFormUnitKerja = 'KPP Madya Malang';
        $this->employeeFormGender = '';
        $this->employeeFormStatus = 'AKTIF';
        $this->resetValidation([
            'employeeFormName',
            'employeeFormNipPendek',
            'employeeFormNipPanjang',
        ]);
        $this->showEmployeeModal = true;
        $this->dispatch('open-employee-modal');
    }

    public function openEditEmployeeModal(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus yang berwenang mengubah data pegawai.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->notify('Tabel pegawai belum siap di database.');
            return;
        }

        $employee = Employee::findOrFail($id);
        $this->editingEmployeeId = $employee->id;
        $this->employeeFormNipPendek = $employee->nip_pendek ?? '';
        $this->employeeFormNipPanjang = $employee->nip_panjang ?? '';
        $this->employeeFormName = $employee->name ?? '';
        $this->employeeFormJabatan = $employee->jabatan ?? '';
        $this->employeeFormUnitKerja = $employee->unit_kerja ?? 'KPP Madya Malang';
        $this->employeeFormGender = $employee->gender ?? '';
        $this->employeeFormStatus = $employee->status ?? 'AKTIF';
        $this->resetValidation([
            'employeeFormName',
            'employeeFormNipPendek',
            'employeeFormNipPanjang',
        ]);
        $this->showEmployeeModal = true;
        $this->dispatch('open-employee-modal');
    }

    public function saveEmployee(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->notify('Tabel pegawai belum siap di database.');
            return;
        }

        $this->validate([
            'employeeFormName' => 'required|string|min:3|max:255',
            'employeeFormNipPendek' => 'nullable|string|max:15',
            'employeeFormNipPanjang' => 'nullable|string|max:25',
            'employeeFormJabatan' => 'nullable|string|max:255',
            'employeeFormStatus' => 'required|in:AKTIF,MUTASI,NONAKTIF',
        ], [
            'employeeFormName.required' => 'Nama lengkap pegawai wajib diisi.',
            'employeeFormName.min' => 'Nama pegawai minimal 3 karakter.',
            'employeeFormStatus.required' => 'Status kepegawaian wajib dipilih.',
        ]);

        $nipPendek = $this->employeeFormNipPendek ? preg_replace('/\D/', '', $this->employeeFormNipPendek) : null;
        $nipPanjang = $this->employeeFormNipPanjang ? preg_replace('/\D/', '', $this->employeeFormNipPanjang) : null;

        $data = [
            'name' => trim($this->employeeFormName),
            'nip_pendek' => $nipPendek,
            'nip_panjang' => $nipPanjang,
            'jabatan' => trim($this->employeeFormJabatan) ?: null,
            'unit_kerja' => trim($this->employeeFormUnitKerja) ?: 'KPP Madya Malang',
            'gender' => in_array($this->employeeFormGender, ['L', 'P']) ? $this->employeeFormGender : null,
            'status' => $this->employeeFormStatus,
        ];

        if ($this->editingEmployeeId) {
            $employee = Employee::findOrFail($this->editingEmployeeId);
            // Jika sebelumnya belum tertaut akun, coba cari kecocokan akun pengguna
            if (empty($employee->user_id)) {
                $matchedUser = $this->findMatchingUser($nipPendek, $nipPanjang, $data['name']);
                if ($matchedUser) {
                    $data['user_id'] = $matchedUser->id;
                    $this->syncUserNipIfEmpty($matchedUser, $nipPanjang ?: $nipPendek);
                }
            }
            $employee->update($data);
            $this->notify("Data pegawai {$employee->name} berhasil diperbarui.");
        } else {
            // Auto-link ke akun User jika ada yang cocok
            $matchedUser = $this->findMatchingUser($nipPendek, $nipPanjang, $data['name']);
            if ($matchedUser) {
                $data['user_id'] = $matchedUser->id;
                $this->syncUserNipIfEmpty($matchedUser, $nipPanjang ?: $nipPendek);
            }

            $employee = Employee::create($data);
            $this->notify("Pegawai {$employee->name} berhasil ditambahkan ke database.");
        }

        try {
            \App\Services\EmployeeStatusSyncService::syncFromEmployeesTable(Auth::id());
        } catch (\Throwable $e) {
            Log::warning('syncFromEmployeesTable in saveEmployee warning: ' . $e->getMessage());
        }

        $this->showEmployeeModal = false;
        $this->editingEmployeeId = null;
        $this->dispatch('close-employee-modal');
    }

    public function closeEmployeeModal(): void
    {
        $this->showEmployeeModal = false;
        $this->editingEmployeeId = null;
        $this->dispatch('close-employee-modal');
    }

    public function deleteEmployee(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->notify('Tabel pegawai belum siap di database.');
            return;
        }

        $employee = Employee::findOrFail($id);
        $name = $employee->name;
        $employee->delete();

        try {
            \App\Services\EmployeeStatusSyncService::syncFromEmployeesTable(Auth::id());
        } catch (\Throwable $e) {
            Log::warning('syncFromEmployeesTable in deleteEmployee warning: ' . $e->getMessage());
        }

        $this->notify("Data pegawai {$name} telah dihapus dari database.");
    }

    public function toggleEmployeeStatus(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->notify('Tabel pegawai belum siap di database.');
            return;
        }

        $employee = Employee::findOrFail($id);
        $employee->status = ($employee->status === 'AKTIF') ? 'MUTASI' : 'AKTIF';
        $employee->save();

        try {
            \App\Services\EmployeeStatusSyncService::syncFromEmployeesTable(Auth::id());
        } catch (\Throwable $e) {
            Log::warning('syncFromEmployeesTable in toggleEmployeeStatus warning: ' . $e->getMessage());
        }

        $this->notify("Status {$employee->name} diubah menjadi {$employee->status}.");
    }

    public function createUserFromEmployee(int $employeeId): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->notify('Tabel pegawai belum siap di database.');
            return;
        }

        $employee = Employee::findOrFail($employeeId);
        if ($employee->user_id && $employee->user) {
            $this->notify("Pegawai {$employee->name} sudah memiliki akun pengguna.");
            return;
        }

        // 1. Cek terlebih dahulu apakah akun pengguna sebenarnya SUDAH ADA dengan nama/NIP yang cocok
        $existingUser = $this->findMatchingUser($employee->nip_pendek, $employee->nip_panjang, $employee->name);
        if ($existingUser) {
            $employee->update(['user_id' => $existingUser->id]);
            $this->syncUserNipIfEmpty($existingUser, $employee->nip_panjang ?: $employee->nip_pendek);
            $this->notify("Akun pengguna {$existingUser->name} ({$existingUser->email}, Role: {$existingUser->role}) berhasil ditautkan ke {$employee->name}.");
            return;
        }

        // 2. Jika belum ada akun yang cocok sama sekali, buatkan akun baru (Role Jamaah)
        $nipKey = $employee->nip_pendek ?: ($employee->nip_panjang ?: Str::slug($employee->name, ''));
        $email = strtolower($nipKey . '@pajak.go.id');

        $counter = 1;
        $baseEmail = $email;
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@pajak.go.id', "_{$counter}@pajak.go.id", $baseEmail);
            $counter++;
        }

        $userData = [
            'name' => $employee->name,
            'email' => $email,
            'role' => 'Jamaah',
            'status' => 'AKTIF',
            'password' => null,
        ];
        if ($this->ensureUserNipColumnExists()) {
            $userData['nip'] = $employee->nip_pendek ?: $employee->nip_panjang;
        }

        $user = User::create($userData);

        $employee->update(['user_id' => $user->id]);
        $this->notify("Akun pengguna untuk {$employee->name} ({$email}) berhasil dibuat dengan role Jamaah.");
    }

    public function unlinkEmployeeFromUser(int $employeeId): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->notify('Tabel pegawai belum siap di database.');
            return;
        }

        $employee = Employee::findOrFail($employeeId);
        $employee->update(['user_id' => null]);

        try {
            \App\Services\EmployeeStatusSyncService::syncFromEmployeesTable(Auth::id());
        } catch (\Throwable $e) {
            Log::warning('syncFromEmployeesTable in unlinkEmployeeFromUser warning: ' . $e->getMessage());
        }

        $this->notify("Tautan akun pengguna untuk {$employee->name} telah dilepas.");
    }

    public function openImportEmployeeModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus yang berwenang mengimpor data pegawai.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->notify('Tabel pegawai belum siap di database.');
            return;
        }

        $this->importEmployeeFile = null;
        $this->importEmployeeResult = null;
        $this->resetValidation('importEmployeeFile');
        $this->showImportEmployeeModal = true;
        $this->dispatch('open-import-employee-modal');
    }

    public function closeImportEmployeeModal(): void
    {
        $this->showImportEmployeeModal = false;
        $this->importEmployeeFile = null;
        $this->dispatch('close-import-employee-modal');
    }

    public function updatedImportEmployeeFile(): void
    {
        if ($this->importEmployeeFile) {
            $this->importEmployees();
        }
    }

    public function importEmployees(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        if (! $this->ensureEmployeeTableExists()) {
            $this->addError('importEmployeeFile', 'Tabel pegawai belum siap di database.');
            return;
        }

        $this->validate([
            'importEmployeeFile' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ], [
            'importEmployeeFile.required' => 'Silakan pilih atau seret berkas spreadsheet terlebih dahulu.',
            'importEmployeeFile.mimes' => 'Berkas harus berupa .xlsx, .xls, atau .csv.',
            'importEmployeeFile.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $tempPath = $this->importEmployeeFile->getRealPath();
        $parseResult = \App\Services\EmployeeImportService::parseFile($tempPath, $this->importEmployeeOnlyMuslim);
        $entries = $parseResult['items'];

        if (empty($entries)) {
            $msg = 'Tidak ditemukan data nama atau NIP pegawai yang valid pada berkas tersebut.';
            if ($parseResult['skipped_non_muslim'] > 0) {
                $msg = "Seluruh data ({$parseResult['skipped_non_muslim']} baris) dilewati karena opsi 'Hanya Pegawai Muslim' aktif dan tidak terdeteksi beragama Islam.";
            }
            $this->addError('importEmployeeFile', $msg);
            return;
        }

        $imported = 0;
        $updated = 0;
        $linked = 0;

        $cachedUsers = $this->importEmployeeAutoLink ? User::all() : collect();

        foreach ($entries as $entry) {
            $name = $entry['name'] ?? null;
            $nip18 = $entry['nip_panjang'] ?? null;
            $nip9 = $entry['nip_pendek'] ?? null;
            $gender = $entry['gender'] ?? null;
            $jabatan = $entry['jabatan'] ?? null;
            $unitKerja = $entry['unit_kerja'] ?? 'KPP Madya Malang';

            if (empty($name) && empty($nip18) && empty($nip9)) {
                continue;
            }

            // Cari pegawai yang sudah ada
            $emp = null;
            if ($nip9) {
                $emp = Employee::where('nip_pendek', $nip9)->first();
            }
            if (! $emp && $nip18) {
                $emp = Employee::where('nip_panjang', $nip18)->first();
            }
            if (! $emp && $name) {
                $emp = Employee::where('name', $name)->first();
            }

            // Deteksi akun User yang cocok
            $matchedUser = null;
            if ($this->importEmployeeAutoLink) {
                $matchedUser = $this->findMatchingUser($nip9, $nip18, $name, $cachedUsers);
            }

            if ($emp) {
                $updateData = ['status' => 'AKTIF'];
                if ($name && (empty($emp->name) || strtolower($emp->name) === strtolower($name))) {
                    $updateData['name'] = $name;
                }
                if ($nip9) $updateData['nip_pendek'] = $nip9;
                if ($nip18) $updateData['nip_panjang'] = $nip18;
                if ($gender) $updateData['gender'] = $gender;
                if ($jabatan) $updateData['jabatan'] = $jabatan;
                if ($unitKerja) $updateData['unit_kerja'] = $unitKerja;
                if ($matchedUser && empty($emp->user_id)) {
                    $updateData['user_id'] = $matchedUser->id;
                    $linked++;
                }
                $emp->update($updateData);
                $updated++;
            } else {
                $createData = [
                    'name' => $name ?: 'Pegawai ' . ($nip9 ?: $nip18),
                    'nip_pendek' => $nip9,
                    'nip_panjang' => $nip18,
                    'gender' => $gender,
                    'jabatan' => $jabatan,
                    'unit_kerja' => $unitKerja,
                    'status' => 'AKTIF',
                    'user_id' => $matchedUser?->id,
                ];
                Employee::create($createData);
                $imported++;
                if ($matchedUser) $linked++;
            }
        }

        $this->importEmployeeResult = [
            'total_rows' => $parseResult['total_rows'],
            'imported' => $imported,
            'updated' => $updated,
            'linked' => $linked,
            'skipped_non_muslim' => $parseResult['skipped_non_muslim'],
        ];

        $this->importEmployeeFile = null;

        try {
            \App\Services\EmployeeStatusSyncService::syncFromEmployeesTable(Auth::id());
        } catch (\Throwable $e) {
            Log::warning('syncFromEmployeesTable in importEmployees warning: ' . $e->getMessage());
        }

        $notifMsg = "Impor pegawai sukses: {$imported} baru, {$updated} diperbarui, {$linked} tertaut akun pengguna.";
        if ($parseResult['skipped_non_muslim'] > 0) {
            $notifMsg .= " ({$parseResult['skipped_non_muslim']} non-muslim dilewati)";
        }
        $this->notify($notifMsg);
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect(route('login'), navigate: true);
    }

    // ==========================================
    // Takmir & Kepengurusan Actions
    // ==========================================
    public function loadTakmirDataFromSettings(): void
    {
        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();
        $struct = $settings->getTakmirStructure();

        // Documents metadata
        if (isset($docs['sk'])) {
            $this->docSkNumber = $docs['sk']['number'] ?? 'KEP-48/KPP.1209/2026';
            $this->docSkDate = $docs['sk']['date'] ?? '14 April 2026';
            $this->docSkTitle = $docs['sk']['title'] ?? 'Surat Keputusan Kepala KPP Madya Malang';
            $this->docSkDescription = $docs['sk']['description'] ?? '';
        }
        if (isset($docs['lampiran1'])) {
            $this->docLampiran1Title = $docs['lampiran1']['title'] ?? 'Lampiran I: Susunan Pengurus Takmir';
            $this->docLampiran1Description = $docs['lampiran1']['description'] ?? '';
        }
        if (isset($docs['lampiran2'])) {
            $this->docLampiran2Title = $docs['lampiran2']['title'] ?? 'Lampiran II: Penjabaran Tugas & Wewenang (Tupoksi)';
            $this->docLampiran2Description = $docs['lampiran2']['description'] ?? '';
        }

        if (preg_match('/(\d{4}\s*[-–]\s*\d{4})/', $this->docSkDescription, $m)) {
            $this->docSkPeriode = str_replace(' ', '', $m[1]);
        } elseif (preg_match('/(\d{4}\s*[-–]\s*\d{4})/', $this->docLampiran1Description, $m)) {
            $this->docSkPeriode = str_replace(' ', '', $m[1]);
        } else {
            $this->docSkPeriode = '2026-2029';
        }

        // Pembina
        $this->pembinaRole = $struct['pembina']['role'] ?? 'Pembina';
        $this->pembinaTitle = $struct['pembina']['title'] ?? 'Kepala Kantor Pelayanan Pajak Madya Malang';
        $this->pembinaName = $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo';
        $this->pembinaTupoksi = implode("\n", $struct['pembina']['tugas'] ?? []);

        // Ketua
        $this->ketuaRole = $struct['ketua']['role'] ?? 'Ketua Takmir';
        $this->ketuaName = $struct['ketua']['name'] ?? 'Moh. Nazil Fuadi Kusmawan';
        $this->ketuaTupoksi = implode("\n", $struct['ketua']['tugas'] ?? []);

        // Wakil Ketua
        $this->wakilKetuaRole = $struct['wakil_ketua']['role'] ?? 'Wakil Ketua Takmir';
        $this->wakilKetuaName = $struct['wakil_ketua']['name'] ?? 'Mujiburrokhman';
        $this->wakilKetuaTupoksi = implode("\n", $struct['wakil_ketua']['tugas'] ?? []);

        // Sekretaris
        $this->sekretarisRole = $struct['sekretaris']['role'] ?? 'Sekretaris';
        $this->sekretarisNames = implode("\n", $struct['sekretaris']['names'] ?? []);
        $this->sekretarisTupoksi = implode("\n", $struct['sekretaris']['tugas'] ?? []);

        // Bendahara
        $this->bendaharaRole = $struct['bendahara']['role'] ?? 'Bendahara';
        $this->bendaharaNames = implode("\n", $struct['bendahara']['names'] ?? []);
        $this->bendaharaTupoksi = implode("\n", $struct['bendahara']['tugas'] ?? []);

        // 4 Bidang
        $bidang = $struct['bidang'] ?? [];
        if (isset($bidang[0])) {
            $this->bidang0Name = $bidang[0]['name'] ?? 'Bidang Dakwah dan Perayaan Hari Besar Islam (PHBI)';
            $this->bidang0Pengelola = implode("\n", $bidang[0]['pengelola'] ?? []);
            $this->bidang0Anggota = implode("\n", $bidang[0]['anggota'] ?? []);
            $this->bidang0Tupoksi = implode("\n", $bidang[0]['tupoksi'] ?? []);
        }
        if (isset($bidang[1])) {
            $this->bidang1Name = $bidang[1]['name'] ?? 'Bidang Humas dan Sosial';
            $this->bidang1Pengelola = implode("\n", $bidang[1]['pengelola'] ?? []);
            $this->bidang1Anggota = implode("\n", $bidang[1]['anggota'] ?? []);
            $this->bidang1Tupoksi = implode("\n", $bidang[1]['tupoksi'] ?? []);
        }
        if (isset($bidang[2])) {
            $this->bidang2Name = $bidang[2]['name'] ?? 'Bidang Rumah Tangga dan Sarana Prasarana';
            $this->bidang2Pengelola = implode("\n", $bidang[2]['pengelola'] ?? []);
            $this->bidang2Anggota = implode("\n", $bidang[2]['anggota'] ?? []);
            $this->bidang2Tupoksi = implode("\n", $bidang[2]['tupoksi'] ?? []);
        }
        if (isset($bidang[3])) {
            $this->bidang3Name = $bidang[3]['name'] ?? 'Bidang Keputrian';
            $this->bidang3Pengelola = implode("\n", $bidang[3]['pengelola'] ?? []);
            $this->bidang3Anggota = implode("\n", $bidang[3]['anggota'] ?? []);
            $this->bidang3Tupoksi = implode("\n", $bidang[3]['tupoksi'] ?? []);
        }
    }

    public function openUploadDoc(string $target): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus yang berwenang mengunggah dokumen.');
            return;
        }

        $this->uploadDocTarget = in_array($target, ['sk', 'lampiran1', 'lampiran2']) ? $target : 'sk';
        $this->uploadDocFile = null;
        $this->showUploadDocModal = true;
        $this->dispatch('open-upload-doc-modal');
    }

    public function saveUploadDoc(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk mengunggah dokumen.');
            return;
        }

        $this->validate([
            'uploadDocFile' => 'required|file|mimes:pdf|max:15360',
        ], [
            'uploadDocFile.required' => 'Silakan pilih berkas PDF untuk diunggah.',
            'uploadDocFile.mimes' => 'Format berkas harus berupa dokumen PDF (.pdf).',
            'uploadDocFile.max' => 'Ukuran berkas maksimal adalah 15 MB.',
        ]);

        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();

        $fileNames = [
            'sk' => 'Surat_Keputusan_Takmir_KEP-48_KPP.1209_2026.pdf',
            'lampiran1' => 'Lampiran_SK_Takmir_KEP-48_KPP.1209_2026.pdf',
            'lampiran2' => 'Tupoksi_Takmir_KEP-48_KPP.1209_2026.pdf',
        ];

        $targetFileName = $fileNames[$this->uploadDocTarget] ?? ($this->uploadDocTarget . '.pdf');
        $destinationDir = public_path('resources');
        if (! is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $targetPath = $destinationDir . DIRECTORY_SEPARATOR . $targetFileName;
        copy($this->uploadDocFile->getRealPath(), $targetPath);

        $docs[$this->uploadDocTarget]['file'] = $targetFileName;
        $docs[$this->uploadDocTarget]['updated_at'] = Carbon::now('Asia/Jakarta')->format('d M Y H:i');
        $docs[$this->uploadDocTarget]['file_size'] = round(filesize($targetPath) / 1024, 1) . ' KB';

        $settings->takmir_documents = $docs;
        $settings->save();

        $this->showUploadDocModal = false;
        $this->dispatch('close-upload-doc-modal');
        $this->uploadDocFile = null;
        $this->notify('Dokumen PDF resmi berhasil diperbarui.');
    }

    public function saveDocMetadata(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk mengubah dokumen.');
            return;
        }

        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();

        $docs['sk']['number'] = $this->docSkNumber;
        $docs['sk']['date'] = $this->docSkDate;
        $docs['sk']['title'] = $this->docSkTitle;
        $docs['sk']['description'] = $this->docSkDescription;

        $docs['lampiran1']['title'] = $this->docLampiran1Title;
        $docs['lampiran1']['description'] = $this->docLampiran1Description;

        $docs['lampiran2']['title'] = $this->docLampiran2Title;
        $docs['lampiran2']['description'] = $this->docLampiran2Description;

        $settings->takmir_documents = $docs;
        $settings->save();

        $this->notify('Informasi dokumen SK berhasil disimpan.');
    }

    public function saveTakmirStructure(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk mengubah struktur Takmir.');
            return;
        }

        $this->validate([
            'pembinaName' => 'required|string|max:255',
            'ketuaName' => 'required|string|max:255',
            'wakilKetuaName' => 'required|string|max:255',
            'sekretarisNames' => 'required|string',
            'bendaharaNames' => 'required|string',
        ], [
            'pembinaName.required' => 'Nama Pembina wajib diisi.',
            'ketuaName.required' => 'Nama Ketua Takmir wajib diisi.',
            'wakilKetuaName.required' => 'Nama Wakil Ketua Takmir wajib diisi.',
            'sekretarisNames.required' => 'Nama Sekretaris wajib diisi.',
            'bendaharaNames.required' => 'Nama Bendahara wajib diisi.',
        ]);

        $settings = MasjidSetting::getActive();
        $struct = $settings->getTakmirStructure();

        $struct['pembina']['role'] = $this->pembinaRole;
        $struct['pembina']['title'] = $this->pembinaTitle;
        $struct['pembina']['name'] = $this->pembinaName;

        $struct['ketua']['role'] = $this->ketuaRole;
        $struct['ketua']['name'] = $this->ketuaName;

        $struct['wakil_ketua']['role'] = $this->wakilKetuaRole;
        $struct['wakil_ketua']['name'] = $this->wakilKetuaName;

        $struct['sekretaris']['role'] = $this->sekretarisRole;
        $struct['sekretaris']['names'] = $this->parseLines($this->sekretarisNames);

        $struct['bendahara']['role'] = $this->bendaharaRole;
        $struct['bendahara']['names'] = $this->parseLines($this->bendaharaNames);

        // 4 Bidang
        $struct['bidang'][0]['name'] = $this->bidang0Name;
        $struct['bidang'][0]['pengelola'] = $this->parseLines($this->bidang0Pengelola);
        $struct['bidang'][0]['anggota'] = $this->parseLines($this->bidang0Anggota);

        $struct['bidang'][1]['name'] = $this->bidang1Name;
        $struct['bidang'][1]['pengelola'] = $this->parseLines($this->bidang1Pengelola);
        $struct['bidang'][1]['anggota'] = $this->parseLines($this->bidang1Anggota);

        $struct['bidang'][2]['name'] = $this->bidang2Name;
        $struct['bidang'][2]['pengelola'] = $this->parseLines($this->bidang2Pengelola);
        $struct['bidang'][2]['anggota'] = $this->parseLines($this->bidang2Anggota);

        $struct['bidang'][3]['name'] = $this->bidang3Name;
        $struct['bidang'][3]['pengelola'] = $this->parseLines($this->bidang3Pengelola);
        $struct['bidang'][3]['anggota'] = $this->parseLines($this->bidang3Anggota);

        $settings->takmir_structure = $struct;
        $settings->save();

        $this->notify('Susunan personalia pengurus Takmir berhasil disimpan.');
    }

    public function saveTakmirTupoksi(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk mengubah Tupoksi.');
            return;
        }

        $settings = MasjidSetting::getActive();
        $struct = $settings->getTakmirStructure();

        $struct['pembina']['tugas'] = $this->parseLines($this->pembinaTupoksi);
        $struct['ketua']['tugas'] = $this->parseLines($this->ketuaTupoksi);
        $struct['wakil_ketua']['tugas'] = $this->parseLines($this->wakilKetuaTupoksi);
        $struct['sekretaris']['tugas'] = $this->parseLines($this->sekretarisTupoksi);
        $struct['bendahara']['tugas'] = $this->parseLines($this->bendaharaTupoksi);

        $struct['bidang'][0]['tupoksi'] = $this->parseLines($this->bidang0Tupoksi);
        $struct['bidang'][1]['tupoksi'] = $this->parseLines($this->bidang1Tupoksi);
        $struct['bidang'][2]['tupoksi'] = $this->parseLines($this->bidang2Tupoksi);
        $struct['bidang'][3]['tupoksi'] = $this->parseLines($this->bidang3Tupoksi);

        $settings->takmir_structure = $struct;
        $settings->save();

        $this->notify('Rincian tugas pokok & wewenang (Tupoksi) berhasil disimpan.');
    }

    public function resetTakmirToDefault(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin.');
            return;
        }

        $settings = MasjidSetting::getActive();
        $settings->takmir_structure = null;
        $settings->takmir_documents = null;
        $settings->save();

        $this->loadTakmirDataFromSettings();
        $this->notify('Data kepengurusan & tupoksi berhasil direset ke SK KEP-48 default.');
    }

    public function openTemplateKepModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus yang berwenang mengelola template surat KEP.');
            return;
        }

        $this->loadTakmirDataFromSettings();
        $this->templateKepPdfFile = null;
        $this->templateKepActiveTab = 'identitas';
        $this->showTemplateKepModal = true;
        $this->dispatch('open-template-kep-modal');
    }

    public function closeTemplateKepModal(): void
    {
        $this->showTemplateKepModal = false;
        $this->templateKepPdfFile = null;
        $this->dispatch('close-template-kep-modal');
    }

    public function saveTemplateKep(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk memperbarui kepengurusan.');
            return;
        }

        $this->validate([
            'docSkNumber' => 'required|string|max:255',
            'docSkDate' => 'required|string|max:255',
            'docSkPeriode' => 'required|string|max:100',
            'pembinaName' => 'required|string|max:255',
            'ketuaName' => 'required|string|max:255',
            'wakilKetuaName' => 'required|string|max:255',
            'sekretarisNames' => 'required|string',
            'bendaharaNames' => 'required|string',
            'templateKepPdfFile' => 'nullable|file|mimes:pdf|max:20480',
        ], [
            'docSkNumber.required' => 'Nomor Keputusan (SK) wajib diisi.',
            'docSkDate.required' => 'Tanggal Penetapan SK wajib diisi.',
            'docSkPeriode.required' => 'Periode kepengurusan wajib diisi (contoh: 2026-2029).',
            'pembinaName.required' => 'Nama Pembina (Kepala Kantor) wajib diisi.',
            'ketuaName.required' => 'Nama Ketua Takmir wajib diisi.',
            'wakilKetuaName.required' => 'Nama Wakil Ketua Takmir wajib diisi.',
            'sekretarisNames.required' => 'Daftar nama Sekretaris wajib diisi.',
            'bendaharaNames.required' => 'Daftar nama Bendahara wajib diisi.',
            'templateKepPdfFile.mimes' => 'Berkas pindaian KEP harus berupa dokumen PDF (.pdf).',
            'templateKepPdfFile.max' => 'Ukuran berkas PDF maksimal 20 MB.',
        ]);

        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();
        $struct = $settings->getTakmirStructure();

        // 1. Simpan Metadata Dokumen SK
        $docs['sk']['number'] = trim($this->docSkNumber);
        $docs['sk']['date'] = trim($this->docSkDate);
        $docs['sk']['title'] = trim($this->docSkTitle) ?: 'Surat Keputusan Kepala KPP Madya Malang';
        $docs['sk']['description'] = trim($this->docSkDescription) ?: ('Keputusan tentang Perubahan Susunan Pengurus Takmir Masjid Sholahuddin KPP Madya Malang Periode ' . trim($this->docSkPeriode));

        // 2. Simpan Struktur Personalia Pengurus & Tupoksi
        $struct['pembina']['role'] = $this->pembinaRole ?: 'Pembina';
        $struct['pembina']['title'] = $this->pembinaTitle ?: 'Kepala Kantor Pelayanan Pajak Madya Malang';
        $struct['pembina']['name'] = trim($this->pembinaName);
        $struct['pembina']['tugas'] = $this->parseLines($this->pembinaTupoksi);

        $struct['ketua']['role'] = $this->ketuaRole ?: 'Ketua Takmir';
        $struct['ketua']['name'] = trim($this->ketuaName);
        $struct['ketua']['tugas'] = $this->parseLines($this->ketuaTupoksi);

        $struct['wakil_ketua']['role'] = $this->wakilKetuaRole ?: 'Wakil Ketua Takmir';
        $struct['wakil_ketua']['name'] = trim($this->wakilKetuaName);
        $struct['wakil_ketua']['tugas'] = $this->parseLines($this->wakilKetuaTupoksi);

        $struct['sekretaris']['role'] = $this->sekretarisRole ?: 'Sekretaris';
        $struct['sekretaris']['names'] = $this->parseLines($this->sekretarisNames);
        $struct['sekretaris']['tugas'] = $this->parseLines($this->sekretarisTupoksi);

        $struct['bendahara']['role'] = $this->bendaharaRole ?: 'Bendahara';
        $struct['bendahara']['names'] = $this->parseLines($this->bendaharaNames);
        $struct['bendahara']['tugas'] = $this->parseLines($this->bendaharaTupoksi);

        // 4 Bidang
        $struct['bidang'][0]['name'] = trim($this->bidang0Name) ?: 'Bidang Dakwah dan Perayaan Hari Besar Islam (PHBI)';
        $struct['bidang'][0]['pengelola'] = $this->parseLines($this->bidang0Pengelola);
        $struct['bidang'][0]['anggota'] = $this->parseLines($this->bidang0Anggota);
        $struct['bidang'][0]['tupoksi'] = $this->parseLines($this->bidang0Tupoksi);

        $struct['bidang'][1]['name'] = trim($this->bidang1Name) ?: 'Bidang Humas dan Sosial';
        $struct['bidang'][1]['pengelola'] = $this->parseLines($this->bidang1Pengelola);
        $struct['bidang'][1]['anggota'] = $this->parseLines($this->bidang1Anggota);
        $struct['bidang'][1]['tupoksi'] = $this->parseLines($this->bidang1Tupoksi);

        $struct['bidang'][2]['name'] = trim($this->bidang2Name) ?: 'Bidang Rumah Tangga dan Sarana Prasarana';
        $struct['bidang'][2]['pengelola'] = $this->parseLines($this->bidang2Pengelola);
        $struct['bidang'][2]['anggota'] = $this->parseLines($this->bidang2Anggota);
        $struct['bidang'][2]['tupoksi'] = $this->parseLines($this->bidang2Tupoksi);

        $struct['bidang'][3]['name'] = trim($this->bidang3Name) ?: 'Bidang Keputrian';
        $struct['bidang'][3]['pengelola'] = $this->parseLines($this->bidang3Pengelola);
        $struct['bidang'][3]['anggota'] = $this->parseLines($this->bidang3Anggota);
        $struct['bidang'][3]['tupoksi'] = $this->parseLines($this->bidang3Tupoksi);

        // 3. Jika ada Berkas PDF KEP Final yang diunggah
        if ($this->templateKepPdfFile) {
            $destinationDir = public_path('resources');
            if (! is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }
            $cleanSkName = preg_replace('/[^A-Za-z0-9_-]/', '_', $this->docSkNumber);
            $fileName = 'Surat_Keputusan_Takmir_' . $cleanSkName . '.pdf';
            $targetPath = $destinationDir . DIRECTORY_SEPARATOR . $fileName;
            copy($this->templateKepPdfFile->getRealPath(), $targetPath);

            $docs['sk']['file'] = $fileName;
            $docs['sk']['updated_at'] = Carbon::now('Asia/Jakarta')->format('d M Y H:i');
            $docs['sk']['file_size'] = round(filesize($targetPath) / 1024, 1) . ' KB';
        }

        $settings->takmir_documents = $docs;
        $settings->takmir_structure = $struct;
        $settings->save();

        $this->templateKepPdfFile = null;
        $this->showTemplateKepModal = false;
        $this->dispatch('close-template-kep-modal');
        $this->notify('Seluruh data kepengurusan Takmir berhasil diperbarui dari Surat KEP.');
    }

    protected function parseLines(string $text): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", $text)), fn($item) => $item !== ''));
    }

    // ==========================================
    // ==========================================
    // Finance Actions (Milestone 5)
    // ==========================================
    public function openFinanceModal(): void
    {
        $this->isEditingFinance = false;
        $this->editingFinanceId = null;
        $this->financeType = 'pemasukan';
        $this->financeCategoryId = $this->getDefaultFinanceCategoryId('pemasukan');
        $this->financeAgendaId = null;
        $this->financeProgramName = 'Kas Umum';
        $this->financeAmount = '';
        $this->financeDate = Carbon::today()->format('Y-m-d');
        $this->financeDescription = '';
        $this->financeReceiptFile = null;
        $this->financeReceiptFiles = [];
        $this->existingFinanceReceiptPaths = [];
        $this->currentFinanceReceiptPath = null;
        $this->showFinanceModal = true;
        $this->dispatch('open-finance-modal');
    }

    public function setFinanceType(string $type): void
    {
        $this->financeType = in_array($type, ['pemasukan', 'pengeluaran'], true) ? $type : 'pemasukan';
        $this->syncFinanceCategoryIdForType($this->financeType);
    }

    public function updatedFinanceType(mixed $value): void
    {
        $this->syncFinanceCategoryIdForType((string) $value);
    }

    protected function syncFinanceCategoryIdForType(string $type): void
    {
        $currentCategory = $this->financeCategoryId ? FinanceCategory::find($this->financeCategoryId) : null;

        if ($type === 'pemasukan') {
            if (! $currentCategory || $currentCategory->group !== 'penerimaan') {
                $this->financeCategoryId = $this->getDefaultFinanceCategoryId('pemasukan');
            }
        } else {
            if (! $currentCategory || $currentCategory->group === 'penerimaan') {
                $this->financeCategoryId = $this->getDefaultFinanceCategoryId('pengeluaran');
            }
        }
    }

    protected function getDefaultFinanceCategoryId(string $type): ?int
    {
        if ($type === 'pemasukan') {
            return FinanceCategory::where('name', 'Infaq Rutin Pegawai (Tukin)')->value('id')
                ?? FinanceCategory::where('name', 'like', '%Infaq Rutin%')->where('group', 'penerimaan')->value('id')
                ?? FinanceCategory::where('group', 'penerimaan')->value('id')
                ?? FinanceCategory::value('id');
        }

        return FinanceCategory::where('name', 'Konsumsi Jumat Berkah')->value('id')
            ?? FinanceCategory::where('name', 'like', '%Konsumsi Jumat%')->whereIn('group', ['pengeluaran_rutin', 'pengeluaran_nonrutin'])->value('id')
            ?? FinanceCategory::whereIn('group', ['pengeluaran_rutin', 'pengeluaran_nonrutin'])->value('id')
            ?? FinanceCategory::value('id');
    }

    public function editFinance(int $id): void
    {
        $fin = Finance::findOrFail($id);
        $this->isEditingFinance = true;
        $this->editingFinanceId = $fin->id;
        $this->financeType = $fin->type;
        $this->financeCategoryId = $fin->category_id;
        $this->financeAgendaId = $fin->agenda_id;
        $this->financeProgramName = $fin->program_name ?: 'Kas Umum';
        $this->financeAmount = (string) (int) $fin->amount;
        $this->financeDate = $fin->transaction_date ? Carbon::parse($fin->transaction_date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $this->financeDescription = $fin->description;
        $this->financeReceiptFile = null;
        $this->financeReceiptFiles = [];
        $this->existingFinanceReceiptPaths = $fin->receipt_paths ?: ($fin->receipt_path ? [$fin->receipt_path] : []);
        $this->currentFinanceReceiptPath = $fin->receipt_path;
        $this->showFinanceModal = true;
        $this->dispatch('open-finance-modal');
    }

    public function removeExistingFinanceReceipt(int $index): void
    {
        if (isset($this->existingFinanceReceiptPaths[$index])) {
            unset($this->existingFinanceReceiptPaths[$index]);
            $this->existingFinanceReceiptPaths = array_values($this->existingFinanceReceiptPaths);
        }
    }

    public function saveFinance(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk mengelola kas.');
            return;
        }

        Finance::ensureReceiptPathsColumnExists();

        $this->financeAmount = preg_replace('/\D/', '', (string) $this->financeAmount);

        $this->validate([
            'financeDate' => 'required|date',
            'financeType' => 'required|in:pemasukan,pengeluaran',
            'financeAmount' => 'required|numeric|min:1',
            'financeDescription' => 'required|string|min:3|max:255',
            'financeReceiptFile' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
            'financeReceiptFiles' => 'nullable|array|max:10',
            'financeReceiptFiles.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
        ], [
            'financeDate.required' => 'Tanggal transaksi wajib diisi.',
            'financeType.required' => 'Jenis transaksi wajib dipilih.',
            'financeAmount.required' => 'Nominal anggaran wajib diisi.',
            'financeAmount.min' => 'Nominal anggaran minimal Rp 1.',
            'financeDescription.required' => 'Keterangan transaksi wajib diisi.',
            'financeReceiptFile.mimes' => 'Bukti transaksi harus berupa file gambar (JPG, PNG, WEBP) atau dokumen PDF.',
            'financeReceiptFile.max' => 'Ukuran file bukti transaksi maksimal 5 MB.',
            'financeReceiptFiles.*.mimes' => 'Bukti transaksi harus berupa file gambar (JPG, PNG, WEBP) atau dokumen PDF.',
            'financeReceiptFiles.*.max' => 'Ukuran setiap file bukti transaksi maksimal 5 MB.',
        ]);

        $paths = $this->existingFinanceReceiptPaths ?: [];

        // Upload single file fallback
        if ($this->financeReceiptFile) {
            $mime = $this->financeReceiptFile->getMimeType();
            if ($mime === 'application/pdf') {
                $paths[] = $this->financeReceiptFile->store('receipts', 'public');
            } else {
                $paths[] = ImageOptimizerService::convertToWebp($this->financeReceiptFile, 'receipts', 1600, 85);
            }
        }

        // Upload multiple files
        if (!empty($this->financeReceiptFiles)) {
            foreach ($this->financeReceiptFiles as $file) {
                if ($file) {
                    $mime = $file->getMimeType();
                    if ($mime === 'application/pdf') {
                        $paths[] = $file->store('receipts', 'public');
                    } else {
                        $paths[] = ImageOptimizerService::convertToWebp($file, 'receipts', 1600, 85);
                    }
                }
            }
        }

        $paths = array_values(array_filter($paths));

        $programName = $this->financeProgramName ?: 'Kas Umum';
        if ($this->financeAgendaId) {
            $agenda = Agenda::find($this->financeAgendaId);
            if ($agenda) {
                $programName = $agenda->title;
            }
        }

        $data = [
            'transaction_date' => $this->financeDate,
            'type' => $this->financeType,
            'category_id' => $this->financeCategoryId ?: null,
            'agenda_id' => $this->financeAgendaId ?: null,
            'program_name' => $programName,
            'amount' => (float) $this->financeAmount,
            'description' => $this->financeDescription,
            'recorded_by' => Auth::id(),
            'receipt_path' => $paths[0] ?? null,
            'receipt_paths' => !empty($paths) ? $paths : null,
        ];

        if ($this->isEditingFinance && $this->editingFinanceId) {
            $fin = Finance::findOrFail($this->editingFinanceId);
            $fin->update($data);
            $this->notify('Transaksi kas berhasil diperbarui.');
        } else {
            Finance::create($data);
            $this->notify('Transaksi kas baru berhasil dicatat.');
        }

        $this->showFinanceModal = false;
        $this->financeReceiptFile = null;
        $this->financeReceiptFiles = [];
        $this->existingFinanceReceiptPaths = [];
        $this->currentFinanceReceiptPath = null;
        $this->dispatch('close-finance-modal');
    }

    public function deleteFinance(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin untuk menghapus transaksi kas.');
            return;
        }

        $fin = Finance::find($id);
        if (! $fin) {
            $this->notify('Transaksi kas telah dihapus.');
            return;
        }

        $paths = $fin->receipt_paths ?: ($fin->receipt_path ? [$fin->receipt_path] : []);
        foreach ($paths as $p) {
            if ($p) {
                Storage::disk('public')->delete($p);
            }
        }
        $fin->delete();
        $this->notify('Transaksi kas berhasil dihapus.');
    }

    // ==========================================
    // Pos Anggaran / Kategori CRUD
    // ==========================================
    public function openFinanceCategoryModal(): void
    {
        $this->isEditingFinanceCategory = false;
        $this->editingFinanceCategoryId = null;
        $this->newCategoryName = '';
        $this->newCategoryGroup = 'pengeluaran_rutin';
        $this->newCategoryType = 'pengeluaran';
        $this->newCategoryColor = 'emerald';
        $this->showFinanceCategoryModal = true;
        $this->dispatch('open-finance-category-modal');
    }

    public function editFinanceCategory(int $id): void
    {
        $cat = FinanceCategory::findOrFail($id);
        $this->isEditingFinanceCategory = true;
        $this->editingFinanceCategoryId = $cat->id;
        $this->newCategoryName = $cat->name;
        $this->newCategoryGroup = $cat->group ?: ($cat->type === 'pemasukan' ? 'penerimaan' : 'pengeluaran_rutin');
        $this->newCategoryType = $cat->type;
        $this->newCategoryColor = $cat->color ?: 'emerald';
        $this->showFinanceCategoryModal = true;
        $this->dispatch('open-finance-category-modal');
    }

    public function saveFinanceCategory(array $formData = []): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin mengelola kategori kas.');
            return;
        }

        if (! empty($formData)) {
            $this->editingFinanceCategoryId = ! empty($formData['id']) ? (int) $formData['id'] : null;
            $this->isEditingFinanceCategory = ! empty($this->editingFinanceCategoryId);
            $this->newCategoryName = (string) ($formData['name'] ?? '');
            $this->newCategoryGroup = (string) ($formData['group'] ?? 'pengeluaran_rutin');
            $this->newCategoryColor = (string) ($formData['color'] ?? 'emerald');
            $this->newCategoryType = ($this->newCategoryGroup === 'penerimaan') ? 'pemasukan' : 'pengeluaran';
        }

        $this->validate([
            'newCategoryName' => 'required|string|min:2|max:100',
            'newCategoryGroup' => 'required|in:penerimaan,pengeluaran_rutin,pengeluaran_nonrutin',
        ], [
            'newCategoryName.required' => 'Nama pos kategori wajib diisi.',
            'newCategoryGroup.required' => 'Kelompok pos wajib dipilih.',
        ]);

        $type = ($this->newCategoryGroup === 'penerimaan') ? 'pemasukan' : 'pengeluaran';

        if ($this->isEditingFinanceCategory && $this->editingFinanceCategoryId) {
            $cat = FinanceCategory::findOrFail($this->editingFinanceCategoryId);
            $cat->update([
                'name' => trim($this->newCategoryName),
                'group' => $this->newCategoryGroup,
                'type' => $type,
                'color' => $this->newCategoryColor ?: 'emerald',
            ]);
            $this->notify("Pos kategori '{$cat->name}' berhasil diperbarui.");
        } else {
            FinanceCategory::create([
                'name' => trim($this->newCategoryName),
                'group' => $this->newCategoryGroup,
                'type' => $type,
                'color' => $this->newCategoryColor ?: 'emerald',
            ]);
            $this->notify('Pos kategori baru berhasil ditambahkan.');
        }

        $this->newCategoryName = '';
        $this->isEditingFinanceCategory = false;
        $this->editingFinanceCategoryId = null;
        $this->showFinanceCategoryModal = false;
        $this->dispatch('close-finance-category-modal');
    }

    public function deleteFinanceCategory(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin menghapus kategori kas.');
            return;
        }

        $cat = FinanceCategory::find($id);
        if (! $cat) {
            $this->notify('Kategori pos telah dihapus.');
            return;
        }

        $name = $cat->name;
        $cat->delete();
        $this->notify("Kategori pos '{$name}' berhasil dihapus.");
    }

    // ==========================================
    // Print Finance Report Modal (Kas Bulanan & LPJ Agenda)
    // ==========================================
    public function openPrintFinanceModal(): void
    {
        $this->printReportType = 'monthly';
        $selectedMonths = is_array($this->financeMonthFilter)
            ? array_values(array_filter($this->financeMonthFilter, fn($m) => $m !== 'all' && !empty($m)))
            : ($this->financeMonthFilter !== 'all' && !empty($this->financeMonthFilter) ? [(string) $this->financeMonthFilter] : []);
        $this->printMonth = !empty($selectedMonths) ? (string) $selectedMonths[0] : (string) Carbon::today()->month;
        $this->printYear = $this->financeYearFilter !== 'all' ? $this->financeYearFilter : (string) Carbon::today()->year;
        $this->printAgendaId = Agenda::first()?->id;
        $this->syncTteStatus();
        $this->showPrintFinanceModal = true;
    }

    public function updatedPrintMonth(): void
    {
        $this->syncTteStatus();
    }

    public function updatedPrintYear(): void
    {
        $this->syncTteStatus();
    }

    public function updatedPrintAgendaId(): void
    {
        $this->syncTteStatus();
    }

    public function updatedPrintReportType(): void
    {
        $this->syncTteStatus();
    }

    public function getPrintReportPeriodKey(): string
    {
        return TteSignatureService::normalizeKey(
            $this->printReportType,
            $this->printReportType === 'agenda' ? $this->printAgendaId : $this->printYear,
            $this->printMonth
        );
    }

    public function getPrintReportPeriodLabel(): string
    {
        if ($this->printReportType === 'agenda') {
            $agendaTitle = Agenda::find($this->printAgendaId)?->title ?? 'Agenda Kegiatan';
            return "Agenda: {$agendaTitle}";
        }

        $monthName = ($this->printMonth !== 'all' && !empty($this->printMonth))
            ? Carbon::create((int)($this->printYear === 'all' ? 2026 : $this->printYear), (int)$this->printMonth, 1)->translatedFormat('F')
            : 'Semua Bulan';

        $yearLabel = $this->printYear !== 'all' ? $this->printYear : 'Semua Tahun';

        return "Bulan {$monthName} {$yearLabel}";
    }

    public function syncTteStatus(): void
    {
        $key = $this->getPrintReportPeriodKey();
        $this->tteSigned = TteSignatureService::isSigned($key);
        $this->printWithTte = $this->tteSigned;
    }

    public function processImportFinancePaste(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        $text = trim($this->importFinancePasteText);
        if (empty($text)) {
            $this->notify('Silakan tempel teks tabel data kas terlebih dahulu.');
            return;
        }

        $lines = explode("\n", str_replace("\r", "", $text));
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $cols = preg_split("/\t|,/", $line);
            if (count($cols) < 3) continue;

            $rawType = strtolower(trim($cols[0] ?? ''));
            $type = (str_contains($rawType, 'out') || str_contains($rawType, 'keluar')) ? 'pengeluaran' : 'pemasukan';
            $catName = trim($cols[1] ?? 'Kas Umum');
            $category = FinanceCategory::firstOrCreate(['name' => $catName], ['type' => $type, 'color' => 'blue']);

            $amountRaw = preg_replace('/[^0-9]/', '', $cols[2] ?? '0');
            $amount = (float) ($amountRaw ?: 0);
            if ($amount <= 0) continue;

            $date = Carbon::today()->toDateString();
            $desc = 'Transaksi Impor ' . $catName;

            if (isset($cols[3]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($cols[3]))) {
                $date = trim($cols[3]);
                $desc = trim($cols[4] ?? $desc);
            } elseif (isset($cols[3])) {
                $desc = trim($cols[3]);
            }

            Finance::create([
                'transaction_date' => $date,
                'type' => $type,
                'category_id' => $category->id,
                'program_name' => 'Kas Umum',
                'amount' => $amount,
                'description' => $desc ?: 'Transaksi Impor',
                'recorded_by' => Auth::id(),
            ]);
            $count++;
        }

        $this->importFinancePasteText = '';
        $this->showFinanceImportModal = false;
        $this->notify("Berhasil mengimpor {$count} data transaksi kas.");
    }

    public function processImportFinanceFile(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        if (! $this->importFinanceFile) {
            $this->notify('Silakan pilih berkas CSV terlebih dahulu.');
            return;
        }

        $path = $this->importFinanceFile->getRealPath();
        $handle = fopen($path, 'r');
        $count = 0;
        $row = 0;

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $row++;
            if ($row === 1 && (str_contains(strtolower($data[0] ?? ''), 'tipe') || str_contains(strtolower($data[0] ?? ''), 'tanggal'))) {
                continue;
            }

            if (count($data) < 3) continue;

            $rawType = strtolower(trim($data[0] ?? ''));
            $type = (str_contains($rawType, 'out') || str_contains($rawType, 'keluar')) ? 'pengeluaran' : 'pemasukan';
            $catName = trim($data[1] ?? 'Kas Umum');
            $category = FinanceCategory::firstOrCreate(['name' => $catName], ['type' => $type, 'color' => 'emerald']);

            $amount = (float) preg_replace('/[^0-9]/', '', $data[2] ?? '0');
            if ($amount <= 0) continue;

            $date = Carbon::today()->toDateString();
            $desc = 'Impor Berkas ' . $catName;

            if (isset($data[3]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($data[3]))) {
                $date = trim($data[3]);
                $desc = trim($data[4] ?? $desc);
            } elseif (isset($data[3])) {
                $desc = trim($data[3]);
            }

            Finance::create([
                'transaction_date' => $date,
                'type' => $type,
                'category_id' => $category->id,
                'program_name' => 'Kas Umum',
                'amount' => $amount,
                'description' => $desc,
                'recorded_by' => Auth::id(),
            ]);
            $count++;
        }

        fclose($handle);
        $this->importFinanceFile = null;
        $this->showFinanceImportModal = false;
        $this->notify("Berhasil mengimpor {$count} baris transaksi kas dari berkas.");
    }

    public function exportFinanceCsv()
    {
        $finances = Finance::with('category')->latest('transaction_date')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-kas-masjid-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($finances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Tanggal', 'Tipe', 'Kategori', 'Program', 'Jumlah (Rp)', 'Keterangan']);

            foreach ($finances as $f) {
                fputcsv($file, [
                    $f->id,
                    $f->transaction_date ? Carbon::parse($f->transaction_date)->format('Y-m-d') : '',
                    ucfirst($f->type),
                    $f->category?->name ?? 'Kas Umum',
                    $f->program_name ?? 'Kas Umum',
                    $f->amount,
                    $f->description,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportFinanceXlsx()
    {
        return $this->exportFinanceCsv();
    }

    // Participant methods (Task 5.3)
    public function openParticipantModal(): void
    {
        $user = Auth::user();
        $this->isEditingParticipant = false;
        $this->editingParticipantId = null;
        $this->participantName = ($user && $user->isJamaah()) ? ($user->name ?? '') : '';
        $this->participantProgram = 'Santunan Anak Yatim';
        $this->participantAmount = '250000';
        $this->participantPeriod = 'Periode ' . now()->month . '/' . now()->year;
        $this->participantStatus = '';
        $this->showParticipantModal = true;
        $this->dispatch('open-participant-modal');
    }

    public function editParticipant(int $id): void
    {
        $p = ProgramParticipant::findOrFail($id);
        $this->isEditingParticipant = true;
        $this->editingParticipantId = $p->id;
        $this->participantName = $p->name;
        $this->participantProgram = $p->program_name;
        $this->participantAmount = (string) (int) $p->monthly_amount;
        $this->participantPeriod = $p->period ?? ('Periode ' . now()->month . '/' . now()->year);
        $this->participantStatus = '';
        $this->showParticipantModal = true;
        $this->dispatch('open-participant-modal');
    }

    public function saveParticipant(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->canManage() && ! $user->isJamaah())) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin mengelola peserta program.');
            return;
        }

        if ($this->isEditingParticipant && $this->editingParticipantId && ! $user->canManage()) {
            $existing = ProgramParticipant::find($this->editingParticipantId);
            $isOwn = $existing && (strtolower(trim($existing->name)) === strtolower(trim($user->name)));
            if (! $isOwn) {
                $this->notify('Akses ditolak: Anda hanya dapat memperbarui data keikutsertaan Anda sendiri.');
                return;
            }
        }

        $this->participantAmount = preg_replace('/\D/', '', (string) $this->participantAmount);

        $this->validate([
            'participantName' => 'required|string|min:3|max:255',
            'participantProgram' => 'required|string',
            'participantAmount' => 'required|numeric|min:1000',
        ], [
            'participantName.required' => 'Nama peserta wajib diisi.',
            'participantProgram.required' => 'Program sosial wajib dipilih.',
            'participantAmount.required' => 'Nominal komitmen bulanan wajib diisi.',
        ]);

        $socialProg = SocialProgram::where('name', $this->participantProgram)->first();
        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;
        $data = [
            'social_program_id' => $socialProg?->id,
            'name' => $this->participantName,
            'program_name' => $this->participantProgram,
            'monthly_amount' => (float) $this->participantAmount,
            'period' => $this->participantPeriod ?: $currentPeriod,
        ];

        if ($this->isEditingParticipant && $this->editingParticipantId) {
            $p = ProgramParticipant::findOrFail($this->editingParticipantId);
            $p->update($data);
            $this->notify("Data peserta {$p->name} berhasil diperbarui.");
        } else {
            ProgramParticipant::create($data);
            $this->notify('Peserta baru berhasil didaftarkan ke program.');
        }

        $this->showParticipantModal = false;
        $this->dispatch('close-participant-modal');
    }

    public function toggleParticipantStatus(int $id): void
    {
        // Status column has been removed in favor of monthly periods
    }

    public function deleteParticipant(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        $p = ProgramParticipant::findOrFail($id);
        $name = $p->name;
        $p->delete();
        $this->notify("Peserta {$name} berhasil dihapus dari program.");
    }

    public function openImportPotonganModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin mengimpor data potongan.');
            return;
        }

        $this->potonganFile = null;
        $this->importPotonganPreview = null;
        $this->importPotonganError = '';
        $this->showImportPotonganModal = true;
        $this->dispatch('open-import-potongan-modal');
    }

    public function closeImportPotonganModal(): void
    {
        $this->potonganFile = null;
        $this->importPotonganPreview = null;
        $this->importPotonganError = '';
        $this->showImportPotonganModal = false;
        $this->dispatch('close-import-potongan-modal');
    }

    public function resetImportPotonganFile(): void
    {
        $this->potonganFile = null;
        $this->importPotonganPreview = null;
        $this->importPotonganError = '';
    }

    public function updatedPotonganFile(): void
    {
        if (! Auth::user()->canManage()) {
            $this->importPotonganError = 'Akses ditolak.';
            return;
        }

        $this->importPotonganError = '';
        $this->importPotonganPreview = null;

        $this->validate([
            'potonganFile' => 'required|file|mimes:xlsx,xls|max:15360',
        ], [
            'potonganFile.required' => 'Pilih file Excel rekap potongan.',
            'potonganFile.mimes' => 'Format berkas harus berupa Excel (.xlsx atau .xls).',
            'potonganFile.max' => 'Ukuran berkas maksimal 15 MB.',
        ]);

        try {
            $syncService = app(PotonganMasjidSyncService::class);
            $realPath = $this->potonganFile->getRealPath();
            $clientName = $this->potonganFile->getClientOriginalName();
            $size = $this->potonganFile->getSize();

            $this->importPotonganPreview = $syncService->getPreview($realPath, $clientName, $size);
        } catch (\Throwable $e) {
            $this->importPotonganError = 'Gagal membaca berkas Excel: ' . $e->getMessage();
            $this->importPotonganPreview = null;
        }
    }

    public function processConfirmImportPotongan(PotonganMasjidSyncService $syncService): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin mengimpor data potongan.');
            return;
        }

        if (! $this->potonganFile) {
            $this->importPotonganError = 'Silakan pilih berkas Excel terlebih dahulu.';
            return;
        }

        try {
            $realPath = $this->potonganFile->getRealPath();
            $result = $syncService->sync($realPath, false);

            $this->showImportPotonganModal = false;
            $this->dispatch('close-import-potongan-modal');

            $this->potonganFile = null;
            $this->importPotonganPreview = null;
            $this->importPotonganError = '';

            $currentPeriodFormatted = number_format($result['current_period_amount'], 0, ',', '.');
            $deletedInfo = ($result['deleted_count'] ?? 0) > 0 ? " ({$result['deleted_count']} data lama dibersihkan)" : '';
            $this->notify("Sinkronisasi berhasil! {$result['total_rows']} data potongan tersimpan{$deletedInfo}. Bulan berjalan ({$result['current_period']}): {$result['current_period_count']} peserta (Rp {$currentPeriodFormatted}).");
        } catch (\Throwable $e) {
            $this->importPotonganError = 'Terjadi kesalahan saat memproses data: ' . $e->getMessage();
        }
    }

    // ==========================================
    // Impor Scan PDF Pernyataan Potongan Tukin Methods
    // ==========================================
    public function openPrintPernyataanModal(string $tab = 'cetak'): void
    {
        $this->currentTab = 'programs';
        $this->socialSubTab = 'pernyataan';
        $this->pernyataanModalTab = in_array($tab, ['cetak', 'impor']) ? $tab : 'cetak';
        $this->showPrintPernyataanModal = false;
        $this->dispatch('open-print-pernyataan-tab', tab: $this->pernyataanModalTab);
    }

    public function closePrintPernyataanModal(): void
    {
        $this->showPrintPernyataanModal = false;
        $this->dispatch('close-print-pernyataan-modal');
    }

    public function updatedScanPernyataanFile(): void
    {
        $this->processScanPernyataan();
    }

    public function processScanPernyataan(): void
    {
        if (! Auth::user()?->canManage()) {
            $this->scanError = 'Akses ditolak: Anda tidak memiliki wewenang mengimpor data komitmen.';
            return;
        }

        $this->scanError = '';
        $this->currentTab = 'programs';
        $this->socialSubTab = 'pernyataan';
        $this->showPrintPernyataanModal = false;
        $this->pernyataanModalTab = 'impor';
        $this->scanPages = [];
        $this->activeScanPageIndex = 0;

        $this->validate([
            'scanPernyataanFile' => 'required|file|mimes:pdf|max:20480',
        ], [
            'scanPernyataanFile.required' => 'Pilih berkas file PDF scan terlebih dahulu.',
            'scanPernyataanFile.mimes' => 'Format berkas harus berupa file PDF (.pdf).',
            'scanPernyataanFile.max' => 'Ukuran berkas maksimal 20 MB.',
        ]);

        $this->scanProcessing = true;

        try {
            $filePath = $this->scanPernyataanFile->getRealPath();
            $destDir = storage_path('app/private/temp_scans');
            if (! file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $destPath = $destDir . DIRECTORY_SEPARATOR . 'latest_scan.pdf';
            if (file_exists($filePath) && filesize($filePath) > 0) {
                copy($filePath, $destPath);
                session([
                    'temp_scan_pdf_path' => $destPath,
                    'temp_scan_pdf_time' => time(),
                ]);
            } else {
                $fallbackPdf = storage_path('app/private/scan_mesin.pdf');
                if (file_exists($fallbackPdf)) {
                    copy($fallbackPdf, $destPath);
                    session([
                        'temp_scan_pdf_path' => $destPath,
                        'temp_scan_pdf_time' => time(),
                    ]);
                }
            }

            $service = app(\App\Services\PernyataanScanOcrService::class);
            $this->scanPages = $service->processPdf($destPath);

            if (empty($this->scanPages)) {
                $this->scanError = 'Tidak ada lembar atau teks yang dapat diekstrak dari berkas PDF ini.';
            } else {
                $this->activeScanPageIndex = 0;
                $this->notify('Berhasil mengekstrak ' . count($this->scanPages) . ' lembar formulir dari pindaian scan.');
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Scan PDF error: ' . $e->getMessage());
            $this->scanError = 'Terjadi kesalahan saat memproses berkas scan: ' . $e->getMessage();
        } finally {
            $this->scanProcessing = false;
        }
    }

    public function removeScanPage(int $index): void
    {
        if (isset($this->scanPages[$index])) {
            array_splice($this->scanPages, $index, 1);
            if ($this->activeScanPageIndex >= count($this->scanPages)) {
                $this->activeScanPageIndex = max(0, count($this->scanPages) - 1);
            }
        }
    }

    public function resetScanPernyataan(): void
    {
        $this->scanPernyataanFile = null;
        $this->scanPages = [];
        $this->activeScanPageIndex = 0;
        $this->scanError = '';
        $this->scanProcessing = false;
        $this->showPrintPernyataanModal = true;
        $this->pernyataanModalTab = 'impor';
    }

    public function exportScanPernyataanExcel(): mixed
    {
        if (! Auth::user()?->canManage()) {
            $this->notify('Akses ditolak.');
            return null;
        }

        if (empty($this->scanPages)) {
            $this->notify('Tidak ada data hasil scan untuk diekspor.');
            return null;
        }

        $service = app(\App\Services\PernyataanScanOcrService::class);
        $tempPath = $service->generateScanExcel($this->scanPages);

        $dateStr = now()->format('Y-m-d');
        $fileName = "Rekap Potongan Tukin - {$dateStr}.xlsx";

        $this->notify('Berkas Excel rekap potongan berhasil diunduh.');
        $this->showPrintPernyataanModal = false;
        $this->dispatch('close-print-pernyataan-modal');

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        ])->deleteFileAfterSend(true);
    }

    public function saveScanPernyataanCommitments(): mixed
    {
        return $this->exportScanPernyataanExcel();
    }

    // ==========================================
    // Milestone 6: Social Programs Actions
    // ==========================================
    public function openCreateSocialProgram(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin mengelola program sosial.');
            return;
        }

        $this->isEditingSocialProgram = false;
        $this->editingSocialProgramId = null;
        $this->socialProgramName = '';
        $this->socialProgramSlug = '';
        $this->socialProgramCategory = 'sosial';
        $this->socialProgramDescription = '';
        $this->socialProgramPeriodType = 'bulanan';
        $this->socialProgramStatus = 'AKTIF';
        $this->socialProgramIcon = 'heart-handshake';
        $this->showSocialProgramModal = true;
        $this->dispatch('open-social-program-modal');
    }

    public function openEditSocialProgram(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        $prog = SocialProgram::findOrFail($id);
        $this->isEditingSocialProgram = true;
        $this->editingSocialProgramId = $prog->id;
        $this->socialProgramName = $prog->name;
        $this->socialProgramSlug = $prog->slug;
        $this->socialProgramCategory = $prog->category ?? 'sosial';
        $this->socialProgramDescription = $prog->description ?? '';
        $this->socialProgramPeriodType = $prog->period_type ?? 'bulanan';
        $this->socialProgramStatus = $prog->status ?? 'AKTIF';
        $this->socialProgramIcon = $prog->icon ?? 'heart-handshake';
        $this->showSocialProgramModal = true;
        $this->dispatch('open-social-program-modal');
    }

    public function saveSocialProgram(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Anda tidak memiliki izin mengelola program sosial.');
            return;
        }

        $this->validate([
            'socialProgramName' => 'required|string|min:3|max:255',
            'socialProgramPeriodType' => 'required|string',
            'socialProgramStatus' => 'required|string',
        ], [
            'socialProgramName.required' => 'Nama program sosial wajib diisi.',
        ]);

        $slug = Str::slug($this->socialProgramName);
        if ($this->isEditingSocialProgram && $this->editingSocialProgramId) {
            $existing = SocialProgram::where('slug', $slug)->where('id', '!=', $this->editingSocialProgramId)->first();
            if ($existing) {
                $slug = $slug . '-' . $this->editingSocialProgramId;
            }
        } else {
            $count = SocialProgram::where('slug', 'like', $slug . '%')->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }
        }

        $data = [
            'name' => $this->socialProgramName,
            'slug' => $slug,
            'category' => $this->socialProgramCategory ?: Str::slug($this->socialProgramName),
            'description' => $this->socialProgramDescription,
            'period_type' => $this->socialProgramPeriodType,
            'status' => $this->socialProgramStatus,
            'icon' => $this->socialProgramIcon ?: 'heart-handshake',
        ];

        if ($this->isEditingSocialProgram && $this->editingSocialProgramId) {
            $prog = SocialProgram::findOrFail($this->editingSocialProgramId);
            $prog->update($data);
            $this->notify("Program sosial '{$prog->name}' berhasil diperbarui.");
        } else {
            SocialProgram::create($data);
            $this->notify("Program sosial '{$this->socialProgramName}' berhasil ditambahkan.");
        }

        $this->showSocialProgramModal = false;
        $this->dispatch('close-social-program-modal');
    }

    public function deleteSocialProgram(int $id): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        $prog = SocialProgram::find($id);
        if (! $prog) {
            $this->notify('Program sosial telah dihapus.');
            return;
        }

        $name = $prog->name;
        $prog->delete();
        $this->notify("Program sosial '{$name}' berhasil dihapus.");
    }

    public function openLumpSumModal(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        $this->lumpSumAmount = '';
        $this->lumpSumNotes = '';
        $this->lumpSumDate = Carbon::today()->format('Y-m-d');
        $this->lumpSumProgram = 'Infaq Rutin';
        $this->showLumpSumModal = true;
        $this->dispatch('open-lumpsum-modal');
    }

    public function closeLumpSumModal(): void
    {
        $this->showLumpSumModal = false;
        $this->dispatch('close-lumpsum-modal');
    }

    public function processLumpSumDeposit(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak.');
            return;
        }

        $this->lumpSumAmount = preg_replace('/\D/', '', (string) $this->lumpSumAmount);

        $this->validate([
            'lumpSumAmount' => 'required|numeric|min:10000',
            'lumpSumDate' => 'required|date',
            'lumpSumProgram' => 'required|string',
        ], [
            'lumpSumAmount.required' => 'Nominal transfer setoran wajib diisi.',
            'lumpSumDate.required' => 'Tanggal transfer kliring wajib diisi.',
        ]);

        $category = FinanceCategory::firstOrCreate(
            ['name' => 'Infaq Rutin Pegawai (Tukin)'],
            ['type' => 'pemasukan', 'color' => 'blue']
        );

        Finance::create([
            'transaction_date' => $this->lumpSumDate,
            'type' => 'pemasukan',
            'category_id' => $category->id,
            'program_name' => $this->lumpSumProgram,
            'amount' => (float) $this->lumpSumAmount,
            'description' => 'Transfer Kliring Kantor (' . $this->lumpSumProgram . '): ' . ($this->lumpSumNotes ?: 'Setoran potongan gaji/tukin kolektif'),
            'recorded_by' => Auth::id(),
        ]);

        $this->lumpSumAmount = '';
        $this->lumpSumNotes = '';
        $this->showLumpSumModal = false;
        $this->dispatch('close-lumpsum-modal');
        $this->notify('Setoran kliring potongan kantor berhasil dicatat ke Kas Masuk.');
    }

    public function signTteReport(): void
    {
        if (! Auth::user()->canManage()) {
            $this->notify('Akses ditolak: Hanya pengurus berwenang yang dapat mengesahkan TTE.');
            return;
        }

        $key = $this->getPrintReportPeriodKey();
        $periodLabel = $this->getPrintReportPeriodLabel();

        $nowSigned = TteSignatureService::toggle($key, $periodLabel, Auth::user());
        $this->tteSigned = $nowSigned;
        $this->printWithTte = $nowSigned;

        if ($nowSigned) {
            $this->notify("Laporan Kas periode ({$periodLabel}) berhasil disahkan dengan TTE Digital BSrE.");
        } else {
            $this->notify("Status pengesahan TTE periode ({$periodLabel}) dibatalkan.");
        }
    }

    public function render(PrayerTimeService $prayerService): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $tab = $this->currentTab;
        /** @var MasjidSetting $settings */
        $settings = MasjidSetting::getActive();
        $today = Carbon::today();
        $now = Carbon::now('Asia/Jakarta');
        $currentWeekNumber = PrayerDuty::getWeekOfMonth($now);
        $todayDayName = $now->translatedFormat('l');

        // Safe defaults for all variables across all tabs (avoids "Undefined variable" in Blade)
        $categories = collect([]);
        $allEvents = collect([]);
        $registrantsList = collect([]);
        $materials = collect([]);
        $activeEventsCount = 0;
        $totalJamaah = 0;
        $attendanceRate = 0;
        $todayEvents = collect([]);
        $recentRegistrants = collect([]);

        $prayers = [];
        $upcomingKajians = collect([]);
        $recentFinances = collect([]);
        $currentWeekDuties = collect([]);
        $allKajianPekanan = collect([]);
        $allKajianJumat = collect([]);
        $totalKajianCount = 0;
        $availableKajianYears = collect([(string) $today->year]);
        $kajianCurrentWeekStart = $now->copy()->startOfWeek()->format('Y-m-d');
        $kajianCurrentWeekEnd = $now->copy()->endOfWeek()->format('Y-m-d');
        $nextPekananKajianId = null;
        $nextJumatKajianId = null;

        $allPrayerDuties = collect([]);
        $totalPrayerDutiesCount = 0;
        $availableDutyYears = collect([(string) $now->year]);
        $dutyMonths = PrayerDuty::MONTHS;
        $dutyFilterYearNum = (int) $now->year;
        $dutyFilterMonthNum = (int) $now->month;
        $dutyFilterWeekNum = null;
        $fridayKajians = collect([]);
        $fridayKajiansByWeek = collect([]);
        $fridayKajiansByDate = collect([]);
        $pekananKajiansByDayWeek = collect([]);
        $pekananKajiansByDay = collect([]);
        $pekananKajiansByWeek = collect([]);
        $kajianUmumByDate = collect([]);
        $defaultPekananKajian = null;

        $allAgendas = collect([]);
        $totalAgendasCount = 0;
        $odojEntries = collect([]);
        $odojCompletedCount = 0;
        $selectedReportAgenda = null;

        $usersList = collect([]);
        $totalUsersCount = 0;
        $employeesList = collect([]);
        $totalEmployeesCount = 0;
        $activeEmployeesCount = 0;

        $financeCategories = collect([]);
        $groupedFinanceCategories = [
            'Penerimaan' => collect([]),
            'Pengeluaran Rutin' => collect([]),
            'Pengeluaran Non-Rutin' => collect([]),
        ];
        $financesList = collect([]);
        $totalFinancesCount = 0;
        $totalKasMasuk = 0;
        $totalKasKeluar = 0;
        $saldoSaatIni = 0;
        $saldoAwal = 36215031;

        $participantsList = collect([]);
        $activeParticipantsCount = 0;
        $targetTukinBulanIni = 0;
        $realisasiTukinMasuk = 0;
        $selisihTukin = 0;
        $lumpSumHistory = collect([]);
        $programFinancesSummary = collect([]);
        $programParticipantCounts = collect([]);

        $allSocialPrograms = collect([]);
        $totalSocialProgramsCount = 0;
        $socialParticipantsList = collect([]);
        $totalSocialCommitment = 0;
        $totalSocialCollectedAll = 0;

        $allCities = [];

        $statsTotalVisits = 0;
        $statsUniqueVisitors = 0;
        $statsTodayVisits = 0;
        $statsMobilePercent = 0;
        $statsDesktopPercent = 0;
        $statsDailyChart = [];
        $statsTopPages = collect([]);
        $statsRecentLogs = collect([]);
        $statsTotalFilteredLogs = 0;

        // =========================================================================
        // SMART QUERY SCOPING: Only execute heavy queries for the active tab
        // =========================================================================

        // Shared lightweight master data for modals (guarantees modal dropdowns never break)
        $categories = Category::all();
        $allSocialPrograms = SocialProgram::all();
        $totalSocialProgramsCount = $allSocialPrograms->count();
        $allAgendasForSelect = Agenda::select('id', 'title')->orderBy('title')->get();

        if ($tab === 'finance') {
            $financeCategoriesQuery = FinanceCategory::withCount('finances')
                ->withSum('finances', 'amount');
            $catSort = $this->tableSorts['finance_categories'] ?? null;
            if ($catSort && !empty($catSort['field'])) {
                $financeCategoriesQuery->orderBy($catSort['field'], $catSort['direction']);
            } else {
                $financeCategoriesQuery->orderBy('name');
            }
            $financeCategories = $financeCategoriesQuery->get();
        } else {
            $financeCategories = FinanceCategory::all();
        }
        $groupedFinanceCategories = [
            'Penerimaan' => $financeCategories->where('group', 'penerimaan'),
            'Pengeluaran Rutin' => $financeCategories->where('group', 'pengeluaran_rutin'),
            'Pengeluaran Non-Rutin' => $financeCategories->where('group', 'pengeluaran_nonrutin'),
        ];

        // Kajians resolution for syncing Friday Khatib & Pekanan Speaker on Dashboard & Petugas cards
        if ($tab === 'dashboard' || $tab === 'petugas') {
            $fridayKajians = Kajian::jumat()
                ->whereDate('date', '>=', $now->copy()->startOfMonth()->subWeeks(1)->format('Y-m-d'))
                ->whereDate('date', '<=', $now->copy()->endOfMonth()->addWeeks(2)->format('Y-m-d'))
                ->orderBy('date')
                ->get();

            if ($fridayKajians->isEmpty()) {
                $fridayKajians = Kajian::jumat()
                    ->orderBy('date', 'desc')
                    ->take(10)
                    ->get();
            }

            $fridayKajiansByWeek = $fridayKajians->keyBy(function ($k) {
                return PrayerDuty::getWeekOfMonth($k->date);
            });

            $pekananKajians = Kajian::kajianUmum()
                ->whereNotNull('speaker_name')
                ->where('speaker_name', '!=', '')
                ->whereDate('date', '>=', $now->copy()->startOfMonth()->subWeeks(1)->format('Y-m-d'))
                ->whereDate('date', '<=', $now->copy()->endOfMonth()->addWeeks(2)->format('Y-m-d'))
                ->orderBy('date')
                ->get();

            if ($pekananKajians->isEmpty()) {
                $pekananKajians = Kajian::kajianUmum()
                    ->whereNotNull('speaker_name')
                    ->where('speaker_name', '!=', '')
                    ->orderBy('date', 'desc')
                    ->take(10)
                    ->get();
            }

            $dayMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
            $pekananKajiansByDayWeek = $pekananKajians->keyBy(function ($k) use ($dayMap) {
                $tzDate = $k->date->timezone('Asia/Jakarta');
                $dayName = $dayMap[$tzDate->dayOfWeekIso] ?? $tzDate->translatedFormat('l');
                $week = PrayerDuty::getWeekOfMonth($tzDate);
                return "{$dayName}_{$week}";
            });

            // Kajians in current calendar week (Monday to Sunday)
            $currentWeekStart = $now->copy()->startOfWeek()->format('Y-m-d');
            $currentWeekEnd = $now->copy()->endOfWeek()->format('Y-m-d');
            $currentWeekKajians = $pekananKajians->filter(function ($k) use ($currentWeekStart, $currentWeekEnd) {
                $d = $k->date ? $k->date->format('Y-m-d') : '';
                return $d >= $currentWeekStart && $d <= $currentWeekEnd;
            });

            $pekananKajiansByDay = $currentWeekKajians->keyBy(function ($k) use ($dayMap) {
                $tzDate = $k->date->timezone('Asia/Jakarta');
                return $dayMap[$tzDate->dayOfWeekIso] ?? $tzDate->translatedFormat('l');
            });
            if ($pekananKajiansByDay->isEmpty()) {
                $pekananKajiansByDay = $pekananKajians->keyBy(function ($k) use ($dayMap) {
                    $tzDate = $k->date->timezone('Asia/Jakarta');
                    return $dayMap[$tzDate->dayOfWeekIso] ?? $tzDate->translatedFormat('l');
                });
            }

            $pekananKajiansByWeek = $pekananKajians->keyBy(function ($k) {
                return PrayerDuty::getWeekOfMonth($k->date);
            });

            $defaultPekananKajian = $currentWeekKajians->first() ?? $pekananKajians->first();
        }

        // 0. Ustadz Directory Collection
        $allUstadzs = collect([]);
        $totalUstadzsCount = 0;
        $ustadzListForDatalist = Ustadz::select('id', 'name', 'phone', 'photo')->orderBy('name')->get();
        $ustadzPhotoMap = $ustadzListForDatalist->whereNotNull('photo')->pluck('photo', 'name')->toArray();

        if ($tab === 'kegiatan' || $tab === 'kajian' || $tab === 'settings' || $this->showUstadzModal || $this->showKajianModal) {
            $ustadzQuery = Ustadz::withCount('kajians');
            if ($this->ustadzSearch !== '') {
                $ustadzQuery->where('name', 'like', '%' . trim($this->ustadzSearch) . '%');
            }
            $allUstadzs = $ustadzQuery->orderBy('name')->get();
            $totalUstadzsCount = Ustadz::count();
        }

        // 1. Tab Dashboard
        if ($tab === 'dashboard') {
            $prayers = $prayerService->getPrayerTimes($settings->city_id ?: '1634');
            $upcomingKajians = Kajian::where('date', '>=', $today->copy()->subDays(1))
                ->orderBy('date', 'asc')
                ->take(4)
                ->get();
            if ($upcomingKajians->isEmpty()) {
                $upcomingKajians = Kajian::orderBy('date', 'desc')->take(4)->get();
            }

            $recentFinances = Finance::with(['category', 'agenda'])
                ->latest('transaction_date')
                ->latest('id')
                ->take(5)
                ->get();

            $totalKasMasuk = Finance::pemasukan()->sum('amount');
            $totalKasKeluar = Finance::pengeluaran()->sum('amount');
            $saldoSaatIni = $totalKasMasuk - $totalKasKeluar;

            $currentWeekDuties = PrayerDuty::forWeek($currentWeekNumber)
                ->orderByRaw("CASE day_name WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 ELSE 6 END")
                ->orderBy('prayer_time', 'desc')
                ->get();

            $activeEventsCount = Event::where('status', 'TAYANG')->count();
            $totalJamaah = Registrant::count();
            $attendanceRate = $totalJamaah > 0 ? 85 : 0;
            $todayEvents = Event::whereDate('event_date', $today)->get();
            $recentRegistrants = Registrant::with('event')->latest()->take(5)->get();
        }

        // 2. Tab Kajian & Kegiatan
        if ($tab === 'kegiatan' || $tab === 'kajian') {
            $pekananQuery = Kajian::kajianUmum();
            $jumatQuery = Kajian::jumat();

            if ($this->kajianMonthFilter !== 'all' && is_numeric($this->kajianMonthFilter)) {
                $pekananQuery->whereMonth('date', (int) $this->kajianMonthFilter);
                $jumatQuery->whereMonth('date', (int) $this->kajianMonthFilter);
            }
            if ($this->kajianYearFilter !== 'all' && is_numeric($this->kajianYearFilter)) {
                $pekananQuery->whereYear('date', (int) $this->kajianYearFilter);
                $jumatQuery->whereYear('date', (int) $this->kajianYearFilter);
            }

            $pekSearch = trim($this->pekananSearch !== '' ? $this->pekananSearch : ($this->currentTab === 'kajian' ? $this->search : ''));
            if ($pekSearch !== '') {
                $s = '%' . $pekSearch . '%';
                $pekananQuery->where(function ($q) use ($s) {
                    $q->where('title', 'like', $s)
                      ->orWhere('speaker_name', 'like', $s)
                      ->orWhere('speaker_phone', 'like', $s);
                });
            }

            $jumSearch = trim($this->jumatSearch !== '' ? $this->jumatSearch : ($this->currentTab === 'kajian' ? $this->search : ''));
            if ($jumSearch !== '') {
                $s = '%' . $jumSearch . '%';
                $jumatQuery->where(function ($q) use ($s) {
                    $q->where('title', 'like', $s)
                      ->orWhere('khatib_name', 'like', $s)
                      ->orWhere('mc_name', 'like', $s)
                      ->orWhere('muadzin_name', 'like', $s)
                      ->orWhere('khatib_phone', 'like', $s)
                      ->orWhere('mc_notes', 'like', $s);
                });
            }

            $pekSort = $this->tableSorts['pekanan'] ?? null;
            if ($pekSort && !empty($pekSort['field'])) {
                $pekananQuery->orderBy($pekSort['field'], $pekSort['direction']);
            } else {
                $pekananQuery->orderBy('date');
            }

            $jumSort = $this->tableSorts['jumat'] ?? null;
            if ($jumSort && !empty($jumSort['field'])) {
                $jumatQuery->orderBy($jumSort['field'], $jumSort['direction']);
            } else {
                $jumatQuery->orderBy('date');
            }

            $allKajianPekanan = $pekananQuery->paginate(10, ['*'], 'pekananPage');
            $allKajianJumat = $jumatQuery->paginate(10, ['*'], 'jumatPage');
            $totalKajianCount = Kajian::count();

            $availableKajianYears = Kajian::whereNotNull('date')
                ->pluck('date')
                ->map(fn ($d) => (string) Carbon::parse($d)->year)
                ->filter()
                ->unique()
                ->values();

            if ($availableKajianYears->isEmpty()) {
                $availableKajianYears = collect([(string) $today->year]);
            }
            if (! $availableKajianYears->contains((string) $today->year)) {
                $availableKajianYears->push((string) $today->year);
            }
            if ($this->kajianYearFilter !== 'all' && ! $availableKajianYears->contains((string) $this->kajianYearFilter)) {
                $availableKajianYears->push((string) $this->kajianYearFilter);
            }
            $availableKajianYears = $availableKajianYears->sort()->values();
            $kajianCurrentWeekStart = $now->copy()->startOfWeek()->format('Y-m-d');
            $kajianCurrentWeekEnd = $now->copy()->endOfWeek()->format('Y-m-d');

            $nextPekananKajianId = Kajian::kajianUmum()
                ->whereDate('date', '>=', $today)
                ->orderBy('date', 'asc')
                ->value('id');

            $nextJumatKajianId = Kajian::jumat()
                ->whereDate('date', '>=', $today)
                ->orderBy('date', 'asc')
                ->value('id');
        }

        // 3. Tab Petugas
        if ($tab === 'petugas') {
            $dutyFilterMonthNum = ($this->prayerDutyFilterMonth !== 'all' && is_numeric($this->prayerDutyFilterMonth))
                ? (int) $this->prayerDutyFilterMonth
                : (int) $now->month;
            $dutyFilterWeekNum = ($this->prayerDutyFilterWeek !== 'all' && is_numeric($this->prayerDutyFilterWeek))
                ? (int) $this->prayerDutyFilterWeek
                : null;

            $dutySearch = trim($this->search);
            $dutySort = $this->tableSorts['petugas'] ?? null;

            $dutyYearsFromDb = PrayerDuty::whereNotNull('tahun')
                ->selectRaw('DISTINCT tahun')
                ->pluck('tahun')
                ->map(fn($y) => (int) $y);
            if (! $dutyYearsFromDb->contains((int) $now->year)) {
                $dutyYearsFromDb->push((int) $now->year);
            }
            $availableDutyYears = $dutyYearsFromDb->sortDesc()->values();

            if ($this->prayerDutyFilterYear !== 'all' && is_numeric($this->prayerDutyFilterYear)) {
                $yearsToRender = [(int) $this->prayerDutyFilterYear];
            } else {
                $yearsToRender = $availableDutyYears->all();
            }

            $yearDutiesQuery = PrayerDuty::query();
            if ($this->prayerDutyFilterYear !== 'all' && is_numeric($this->prayerDutyFilterYear)) {
                $selectedYear = (int) $this->prayerDutyFilterYear;
                $yearDutiesQuery->where(function ($q) use ($selectedYear, $now) {
                    $q->where('tahun', $selectedYear);
                    if ($selectedYear === (int) $now->year) {
                        $q->orWhereNull('tahun');
                    }
                });
            }
            $allYearDuties = $yearDutiesQuery->get();

            $dayMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
            $settings = MasjidSetting::getActive();
            $todayDateStr = $today->format('Y-m-d');
            $dailyItems = [];

            foreach ($yearsToRender as $dutyFilterYearNum) {
                $rangeStart = Carbon::create($dutyFilterYearNum, $dutyFilterMonthNum, 1, 0, 0, 0, 'Asia/Jakarta')->startOfMonth()->subWeeks(1);
                $rangeEnd = Carbon::create($dutyFilterYearNum, $dutyFilterMonthNum, 1, 0, 0, 0, 'Asia/Jakarta')->endOfMonth()->addWeeks(1);

                $fridayKajians = Kajian::jumat()
                    ->whereBetween('date', [$rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
                    ->orderBy('date')
                    ->get();
                $fridayKajiansByDate = $fridayKajians->keyBy(fn($k) => $k->date->format('Y-m-d'));
                $fridayKajiansByWeek = $fridayKajians->keyBy(fn($k) => PrayerDuty::getWeekOfMonth($k->date));

                $kajianUmum = Kajian::kajianUmum()
                    ->whereNotNull('speaker_name')
                    ->where('speaker_name', '!=', '')
                    ->whereBetween('date', [$rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
                    ->orderBy('date')
                    ->get();
                $kajianUmumByDate = $kajianUmum->keyBy(fn($k) => $k->date->format('Y-m-d'));

                $dutiesForYear = $allYearDuties->filter(function ($d) use ($dutyFilterYearNum, $now) {
                    if ($d->tahun !== null) {
                        return (int) $d->tahun === $dutyFilterYearNum;
                    }
                    return (int) $now->year === $dutyFilterYearNum;
                });

                if ($dutyFilterWeekNum !== null) {
                    $weekCalendarDays = PrayerDuty::getWeekCalendarDays($dutyFilterYearNum, $dutyFilterMonthNum, $dutyFilterWeekNum);
                    $weekDays = [];
                    foreach ($weekCalendarDays as $dInfo) {
                        $weekDays[] = [
                            'date' => $dInfo['date'],
                            'day_name' => $dInfo['day_name'],
                            'week_number' => $dInfo['week_number'],
                            'month' => $dInfo['month'],
                            'year' => $dInfo['year'],
                        ];
                    }
                    if (!empty($weekDays)) {
                        $lastDay = end($weekDays)['date'];
                        $satDate = $lastDay->copy()->addDay();
                        $sunDate = $lastDay->copy()->addDays(2);
                        $weekDays[] = [
                            'date' => $satDate,
                            'day_name' => 'Sabtu',
                            'week_number' => PrayerDuty::getWeekOfMonth($satDate),
                            'month' => (int) $satDate->month,
                            'year' => (int) $satDate->year,
                        ];
                        $weekDays[] = [
                            'date' => $sunDate,
                            'day_name' => 'Minggu',
                            'week_number' => PrayerDuty::getWeekOfMonth($sunDate),
                            'month' => (int) $sunDate->month,
                            'year' => (int) $sunDate->year,
                        ];
                    }
                } else {
                    $startOfMonth = Carbon::create($dutyFilterYearNum, $dutyFilterMonthNum, 1, 12, 0, 0, 'Asia/Jakarta');
                    $daysInMonth = $startOfMonth->daysInMonth;
                    $weekDays = [];
                    for ($d = 1; $d <= $daysInMonth; $d++) {
                        $dt = Carbon::create($dutyFilterYearNum, $dutyFilterMonthNum, $d, 12, 0, 0, 'Asia/Jakarta');
                        $weekDays[] = [
                            'date' => $dt,
                            'day_name' => $dayMap[$dt->dayOfWeekIso],
                            'week_number' => PrayerDuty::getWeekOfMonth($dt),
                            'month' => (int) $dt->month,
                            'year' => (int) $dt->year,
                        ];
                    }
                }

                foreach ($weekDays as $dayInfo) {
                    $date = $dayInfo['date'];
                    $dateStr = $date->format('Y-m-d');
                    $dayOfWeek = $date->dayOfWeekIso; // 1 = Monday, ..., 7 = Sunday
                    $dayName = $dayInfo['day_name'];
                    $weekNumber = $dayInfo['week_number'];
                    $isWeekend = ($dayOfWeek === 6 || $dayOfWeek === 7);
                    $isFriday = ($dayOfWeek === 5);
                    $isToday = ($dateStr === $todayDateStr);
                    $dayNumber = (int) $date->day;
                    $formattedDate = $date->translatedFormat('d F Y');

                    if ($isWeekend) {
                        $row = [
                            'date' => $dateStr,
                            'resolved_date' => $date,
                            'day_name' => $dayName,
                            'formatted_date' => $formattedDate,
                            'day_number' => $dayNumber,
                            'week_number' => $weekNumber,
                            'is_weekend' => true,
                            'is_friday' => false,
                            'is_today' => $isToday,
                            'dzuhur_imam' => '-',
                            'dzuhur_muadzin' => '-',
                            'ashar_imam' => '-',
                            'ashar_muadzin' => '-',
                            'ashar_kajian_title' => null,
                            'friday_khatib' => '-',
                            'friday_mc' => '-',
                            'friday_muadzin' => '-',
                            'dzuhur_duty' => null,
                            'ashar_duty' => null,
                            'friday_kajian' => null,
                            'kajian_on_date' => null,
                            'imam_name' => '-',
                            'muadzin_name' => '-',
                        ];
                    } elseif ($isFriday) {
                        $kajian = $fridayKajiansByDate->get($dateStr) ?? $fridayKajiansByWeek->get($weekNumber);
                        $khatib = $kajian ? ($kajian->khatib_name ?: 'Ust. M. Yasak Lc MA') : ($settings->friday_prayer_info['khatib'] ?? 'Ust. M. Yasak Lc MA');
                        $mc = $kajian ? ($kajian->mc_name ?: ($kajian->speaker_name ?: 'Alan Irfansyah')) : ($settings->friday_prayer_info['mc'] ?? 'Alan Irfansyah');
                        $muadzin = $kajian ? ($kajian->muadzin_name ?: ($kajian->description ?: 'Khodori')) : ($settings->friday_prayer_info['muadzin'] ?? 'Khodori');

                        $asharDuty = $dutiesForYear
                            ->where('day_name', 'Jumat')
                            ->where('prayer_time', 'ashar')
                            ->filter(fn($d) => $d->matchesWeek($weekNumber))
                            ->first();

                        $kajianOnDate = $kajianUmumByDate->get($dateStr);
                        $asImam = $asharDuty?->imam_name ?: '-';
                        $asMuadzin = $asharDuty?->muadzin_name ?: '-';
                        $asharKajianTitle = null;

                        if ($kajianOnDate && !empty($kajianOnDate->speaker_name)) {
                            $asImam = $kajianOnDate->speaker_name;
                            if (!empty($kajianOnDate->muadzin_name)) {
                                $asMuadzin = $kajianOnDate->muadzin_name;
                            }
                            $asharKajianTitle = $kajianOnDate->title;
                        }

                        $row = [
                            'date' => $dateStr,
                            'resolved_date' => $date,
                            'day_name' => $dayName,
                            'formatted_date' => $formattedDate,
                            'day_number' => $dayNumber,
                            'week_number' => $weekNumber,
                            'is_weekend' => false,
                            'is_friday' => true,
                            'is_today' => $isToday,
                            'dzuhur_imam' => $khatib,
                            'dzuhur_muadzin' => $muadzin,
                            'ashar_imam' => $asImam,
                            'ashar_muadzin' => $asMuadzin,
                            'ashar_kajian_title' => $asharKajianTitle,
                            'friday_khatib' => $khatib,
                            'friday_mc' => $mc,
                            'friday_muadzin' => $muadzin,
                            'dzuhur_duty' => null,
                            'ashar_duty' => $asharDuty,
                            'friday_kajian' => $kajian,
                            'kajian_on_date' => $kajianOnDate,
                            'imam_name' => $khatib ?: $asImam,
                            'muadzin_name' => $muadzin ?: $asMuadzin,
                        ];
                    } else {
                        $matchingDuties = $dutiesForYear
                            ->where('day_name', $dayName)
                            ->filter(fn($d) => $d->matchesWeek($weekNumber));

                        $dzuhurDuty = $matchingDuties->firstWhere('prayer_time', 'dzuhur');
                        $asharDuty = $matchingDuties->firstWhere('prayer_time', 'ashar');
                        $kajianOnDate = $kajianUmumByDate->get($dateStr);

                        $dzImam = $dzuhurDuty?->imam_name ?: '-';
                        $dzMuadzin = $dzuhurDuty?->muadzin_name ?: '-';

                        $asImam = $asharDuty?->imam_name ?: '-';
                        $asMuadzin = $asharDuty?->muadzin_name ?: '-';
                        $asharKajianTitle = null;

                        if ($kajianOnDate && !empty($kajianOnDate->speaker_name)) {
                            $asImam = $kajianOnDate->speaker_name;
                            if (!empty($kajianOnDate->muadzin_name)) {
                                $asMuadzin = $kajianOnDate->muadzin_name;
                            }
                            $asharKajianTitle = $kajianOnDate->title;
                        }

                        $row = [
                            'date' => $dateStr,
                            'resolved_date' => $date,
                            'day_name' => $dayName,
                            'formatted_date' => $formattedDate,
                            'day_number' => $dayNumber,
                            'week_number' => $weekNumber,
                            'is_weekend' => false,
                            'is_friday' => false,
                            'is_today' => $isToday,
                            'dzuhur_imam' => $dzImam,
                            'dzuhur_muadzin' => $dzMuadzin,
                            'ashar_imam' => $asImam,
                            'ashar_muadzin' => $asMuadzin,
                            'ashar_kajian_title' => $asharKajianTitle,
                            'friday_khatib' => '-',
                            'friday_mc' => '-',
                            'friday_muadzin' => '-',
                            'dzuhur_duty' => $dzuhurDuty,
                            'ashar_duty' => $asharDuty,
                            'friday_kajian' => null,
                            'kajian_on_date' => $kajianOnDate,
                            'imam_name' => ($dzImam !== '-' ? $dzImam : ($asImam !== '-' ? $asImam : '-')),
                            'muadzin_name' => ($dzMuadzin !== '-' ? $dzMuadzin : ($asMuadzin !== '-' ? $asMuadzin : '-')),
                        ];
                    }

                    if ($dutySearch !== '') {
                        $s = strtolower($dutySearch);
                        $searchableText = strtolower(implode(' ', [
                            $row['day_name'],
                            $row['formatted_date'],
                            $row['dzuhur_imam'],
                            $row['dzuhur_muadzin'],
                            $row['ashar_imam'],
                            $row['ashar_muadzin'],
                            $row['friday_khatib'],
                            $row['friday_mc'],
                            $row['friday_muadzin'],
                            $row['ashar_kajian_title'] ?? '',
                        ]));

                        if (!str_contains($searchableText, $s)) {
                            continue;
                        }
                    }

                    $dailyItems[] = new \Illuminate\Support\Fluent($row);
                }
            }

            if ($dutySort && !empty($dutySort['field'])) {
                $dir = $dutySort['direction'] ?? 'asc';
                $isDesc = ($dir === 'desc');
                $coll = collect($dailyItems);

                if ($dutySort['field'] === 'date') {
                    $coll = $coll->sort(function ($a, $b) use ($isDesc) {
                        $tA = $a->resolved_date ? $a->resolved_date->timestamp : 0;
                        $tB = $b->resolved_date ? $b->resolved_date->timestamp : 0;
                        return $isDesc ? ($tB <=> $tA) : ($tA <=> $tB);
                    });
                } elseif ($dutySort['field'] === 'day_name') {
                    $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
                    $coll = $coll->sort(function ($a, $b) use ($dayOrder, $isDesc) {
                        $dA = $dayOrder[$a->day_name] ?? 8;
                        $dB = $dayOrder[$b->day_name] ?? 8;
                        return $isDesc ? ($dB <=> $dA) : ($dA <=> $dB);
                    });
                } elseif (in_array($dutySort['field'], ['imam_name', 'dzuhur', 'ashar'])) {
                    $coll = $coll->sort(function ($a, $b) use ($isDesc) {
                        $vA = strtolower($a->imam_name ?? '');
                        $vB = strtolower($b->imam_name ?? '');
                        return $isDesc ? strcmp($vB, $vA) : strcmp($vA, $vB);
                    });
                }
                $dailyItems = $coll->values()->all();
            }

            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('dutyPage');
            $perPage = ($dutyFilterWeekNum !== null)
                ? max(14, count($yearsToRender) * 7)
                : max(31, count($yearsToRender) * 31);
            $currentItems = array_slice($dailyItems, ($currentPage - 1) * $perPage, $perPage);

            $allPrayerDuties = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                count($dailyItems),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'pageName' => 'dutyPage']
            );

            $dutyYearsFromDb = PrayerDuty::whereNotNull('tahun')
                ->selectRaw('DISTINCT tahun')
                ->pluck('tahun')
                ->map(fn($y) => (string) $y);
            if (! $dutyYearsFromDb->contains((string) $now->year)) {
                $dutyYearsFromDb->push((string) $now->year);
            }
            $availableDutyYears = $dutyYearsFromDb->sortDesc()->values();

            $totalPrayerDutiesCount = PrayerDuty::count();
            $currentWeekDuties = PrayerDuty::forWeek($currentWeekNumber)
                ->orderByRaw("CASE day_name WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 ELSE 6 END")
                ->orderBy('prayer_time', 'desc')
                ->get();
        }

        // 4. Tab Agenda & ODOJ / Kegiatan
        if ($tab === 'kegiatan' || $tab === 'agenda') {
            $agendaQuery = Agenda::query();
            if ($this->agendaFilterStatus !== 'all' && in_array($this->agendaFilterStatus, ['Direncanakan', 'Berjalan', 'SELESAI'])) {
                $agendaQuery->where('status', $this->agendaFilterStatus);
            }

            $agSearch = trim($this->agendaSearch !== '' ? $this->agendaSearch : ($this->currentTab === 'agenda' ? $this->search : ''));
            if ($agSearch !== '') {
                $s = '%' . $agSearch . '%';
                $agendaQuery->where(function ($q) use ($s) {
                    $q->where('title', 'like', $s)
                      ->orWhere('description', 'like', $s)
                      ->orWhere('committee_members', 'like', $s);
                });
            }

            $agendaSort = $this->tableSorts['agenda'] ?? null;
            if ($agendaSort && !empty($agendaSort['field'])) {
                $agendaQuery->orderBy($agendaSort['field'], $agendaSort['direction']);
            } else {
                $agendaQuery->orderBy('event_date', 'desc');
            }
            $allAgendas = $agendaQuery->paginate(10, ['*'], 'agendaPage');
            $totalAgendasCount = Agenda::count();

            $odojTargetDate = $this->odojDate ?: $today->format('Y-m-d');
            $odojEntries = OdojEntry::getEntriesForDate($odojTargetDate);
            $odojCompletedCount = $odojEntries->where('status', 'Selesai')->count();
            $selectedReportAgenda = $this->selectedAgendaForReportId ? Agenda::find($this->selectedAgendaForReportId) : $allAgendas->first();
        } else {
            $allAgendas = $allAgendasForSelect;
            $selectedReportAgenda = $this->selectedAgendaForReportId ? Agenda::find($this->selectedAgendaForReportId) : null;
        }

        // 5. Tab Users & Pegawai
        if ($tab === 'users') {
            $userQuery = User::query();
            if ($this->userFilterRole !== 'all') {
                $userQuery->where('role', $this->userFilterRole);
            }

            $userSearch = trim($this->userSubTab === 'pengguna' ? $this->search : '');
            if ($userSearch !== '') {
                $s = '%' . $userSearch . '%';
                $hasNipCol = Schema::hasColumn('users', 'nip');
                $userQuery->where(function ($q) use ($s, $hasNipCol) {
                    $q->where('name', 'like', $s)
                      ->orWhere('email', 'like', $s);
                    if ($hasNipCol) {
                        $q->orWhere('nip', 'like', $s);
                    }
                });
            }

            $userSort = $this->tableSorts['users'] ?? null;
            if ($userSort && !empty($userSort['field'])) {
                if ($userSort['field'] === 'role') {
                    $userQuery->orderByRaw("CASE role WHEN 'Master' THEN 1 WHEN 'Ketua' THEN 2 WHEN 'Sekretaris' THEN 3 WHEN 'Bendahara' THEN 4 WHEN 'admin' THEN 5 WHEN 'operator' THEN 6 ELSE 7 END " . $userSort['direction'])->orderBy('name');
                } else {
                    $userQuery->orderBy($userSort['field'], $userSort['direction']);
                }
            } else {
                $userQuery->orderByRaw("CASE role WHEN 'Master' THEN 1 WHEN 'Ketua' THEN 2 WHEN 'Sekretaris' THEN 3 WHEN 'Bendahara' THEN 4 WHEN 'admin' THEN 5 WHEN 'operator' THEN 6 ELSE 7 END")
                          ->orderBy('name');
            }
            $usersList = $userQuery->paginate(10, ['*'], 'userPage');
            $totalUsersCount = User::count();

            // Query Master Data Pegawai Muslim KPP Madya Malang (dengan auto-migration & schema guard)
            if ($this->ensureEmployeeTableExists()) {
                // Tautkan otomatis pegawai yang belum terhubung ke akun aplikasi jika ada kecocokan
                if ($this->userSubTab === 'pegawai') {
                    $this->autoLinkUnlinkedEmployees();
                }

                $employeeQuery = Employee::with('user');
                if ($this->employeeFilterStatus !== 'all') {
                    $employeeQuery->where('status', $this->employeeFilterStatus);
                }
                if ($this->employeeFilterLink === 'linked') {
                    $employeeQuery->whereNotNull('user_id');
                } elseif ($this->employeeFilterLink === 'unlinked') {
                    $employeeQuery->whereNull('user_id');
                }

                $empSearch = trim($this->employeeSearch ?: ($this->userSubTab === 'pegawai' ? $this->search : ''));
                if ($empSearch !== '') {
                    $employeeQuery->search($empSearch);
                }

                $empSort = $this->tableSorts['employees'] ?? null;
                if ($empSort && !empty($empSort['field'])) {
                    $employeeQuery->orderBy($empSort['field'], $empSort['direction']);
                } else {
                    $employeeQuery->orderBy('name', 'asc');
                }
                $employeesList = $employeeQuery->paginate(15, ['*'], 'employeePage');
                $totalEmployeesCount = Employee::count();
                $activeEmployeesCount = Employee::where('status', 'AKTIF')->count();
            } else {
                $employeesList = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, 1, [
                    'path' => request()->url(),
                    'pageName' => 'employeePage',
                ]);
                $totalEmployeesCount = 0;
                $activeEmployeesCount = 0;
            }
        }

        // 6. Tab Finance
        if ($tab === 'finance') {
            $financeQuery = Finance::with(['category', 'agenda']);
            if ($this->financeTypeFilter !== 'all') {
                $financeQuery->where('type', $this->financeTypeFilter);
            }
            $selectedMonths = is_array($this->financeMonthFilter)
                ? array_values(array_filter($this->financeMonthFilter, fn($m) => $m !== 'all' && !empty($m)))
                : ($this->financeMonthFilter !== 'all' && !empty($this->financeMonthFilter) ? [(string) $this->financeMonthFilter] : []);

            if (!empty($selectedMonths)) {
                $financeQuery->where(function ($q) use ($selectedMonths) {
                    foreach ($selectedMonths as $m) {
                        $q->orWhereMonth('transaction_date', (int) $m);
                    }
                });
            }
            if ($this->financeYearFilter !== 'all' && is_numeric($this->financeYearFilter)) {
                $financeQuery->whereYear('transaction_date', (int) $this->financeYearFilter);
            }

            $financeSearchTarget = trim($this->financeSearch ?: $this->search);
            if ($financeSearchTarget !== '') {
                $s = '%' . $financeSearchTarget . '%';
                $financeQuery->where(function ($q) use ($s) {
                    $q->where('description', 'like', $s)
                      ->orWhere('program_name', 'like', $s)
                      ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', $s))
                      ->orWhereHas('agenda', fn($aq) => $aq->where('title', 'like', $s));
                });
            }

            $financeSort = $this->tableSorts['finance'] ?? null;
            if ($financeSort && !empty($financeSort['field'])) {
                $financeQuery->orderBy($financeSort['field'], $financeSort['direction']);
            } else {
                $financeQuery->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');
            }
            $financesList = $financeQuery->get();
            $totalFinancesCount = Finance::count();

            $finAgg = Finance::toBase()->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'pemasukan' THEN amount ELSE 0 END), 0) as total_masuk,
                COALESCE(SUM(CASE WHEN type = 'pengeluaran' THEN amount ELSE 0 END), 0) as total_keluar
            ")->first();
            $totalKasMasuk = (float) ($finAgg->total_masuk ?? 0);
            $totalKasKeluar = (float) ($finAgg->total_keluar ?? 0);
            $saldoSaatIni = $totalKasMasuk - $totalKasKeluar;

            if (!empty($selectedMonths) && count($selectedMonths) === 1 && $this->financeYearFilter !== 'all' && is_numeric($this->financeYearFilter)) {
                $startDate = Carbon::createFromDate((int)$this->financeYearFilter, (int)$selectedMonths[0], 1)->startOfDay();
                $priorIncome = (float) Finance::pemasukan()->where('transaction_date', '<', $startDate)->sum('amount');
                $priorExpense = (float) Finance::pengeluaran()->where('transaction_date', '<', $startDate)->sum('amount');
                $saldoAwal = $priorIncome - $priorExpense;
            } else {
                $saldoAwalCategory = FinanceCategory::where('name', 'like', '%Saldo Awal%')->first();
                $saldoAwal = $saldoAwalCategory ? Finance::where('category_id', $saldoAwalCategory->id)->sum('amount') : 36215031;
            }
        }

        // 7. Tab Programs
        $totalActiveSocialParticipants = 0;
        $availablePeriods = collect([]);
        if ($tab === 'programs') {
            $periods = ProgramParticipant::distinct()->pluck('period')->filter()->values();
            $availablePeriods = $periods->sort(function ($a, $b) {
                preg_match('/(\d+)\/(\d+)/', (string) $a, $mA);
                preg_match('/(\d+)\/(\d+)/', (string) $b, $mB);
                $yearA = isset($mA[2]) ? (int) $mA[2] : 0;
                $monthA = isset($mA[1]) ? (int) $mA[1] : 0;
                $yearB = isset($mB[2]) ? (int) $mB[2] : 0;
                $monthB = isset($mB[1]) ? (int) $mB[1] : 0;
                if ($yearA !== $yearB) {
                    return $yearB <=> $yearA;
                }
                return $monthB <=> $monthA;
            })->values();
        }

        if ($tab === 'programs') {
            $isJamaah = Auth::check() && ! Auth::user()->canManage();
            $userName = Auth::user()?->name;

            if ($isJamaah && ! in_array($this->socialSubTab, ['katalog', 'pernyataan'], true)) {
                $this->socialSubTab = 'katalog';
            }

            $currentPeriod = 'Periode ' . (int) $now->month . '/' . (int) $now->year;
            $ytdPeriods = [];
            for ($m = 1; $m <= (int) $now->month; $m++) {
                $ytdPeriods[] = "Periode {$m}/{$now->year}";
            }

            if ($isJamaah && !empty($userName)) {
                $trimmedUser = strtolower(trim($userName));

                // Periode & pencarian katalog sesuai filter yang dipilih Jamaah (default: bulan berjalan)
                $catalogMonthInt = max(1, min(12, (int) ($this->catalogMonth ?: $now->month)));
                $catalogYearInt = (int) ($this->catalogYear ?: $now->year);
                $currentPeriod = 'Periode ' . $catalogMonthInt . '/' . $catalogYearInt;
                $catalogSearchTerm = trim($this->catalogSearch);

                // Nama peserta yang cocok dengan akun ini (abaikan gelar, spasi ganda, huruf besar/kecil)
                $normalizeName = function (?string $n): string {
                    $n = \App\Services\EmployeeStatusSyncService::stripAcademicTitles((string) $n);
                    return strtolower(trim((string) preg_replace('/\s+/', ' ', $n)));
                };
                $wantedNames = array_filter(array_unique([
                    $normalizeName($userName),
                    $normalizeName(Auth::user()->employee?->name),
                ]));
                $ownNames = ProgramParticipant::query()->distinct()->pluck('name')
                    ->filter(fn ($n) => in_array($normalizeName($n), $wantedNames, true))
                    ->values()->all();
                if (empty($ownNames)) {
                    $ownNames = [$userName];
                }

                $allSocialPrograms = SocialProgram::withCount(['participants as active_participants_count' => fn($q) => $q->where('period', $currentPeriod)->whereIn('name', $ownNames)])
                    ->withSum(['participants as monthly_commitment_total' => fn($q) => $q->where('period', $currentPeriod)->whereIn('name', $ownNames)], 'monthly_amount')
                    ->withSum(['participants as total_collected' => fn($q) => $q->whereIn('period', $ytdPeriods)->whereIn('name', $ownNames)], 'monthly_amount')
                    ->withSum(['finances as total_disbursed' => fn($q) => $q->where('type', 'pengeluaran')], 'amount')
                    ->when($catalogSearchTerm !== '', fn ($q) => $q->where('name', 'like', '%' . $catalogSearchTerm . '%'))
                    ->get();

                foreach ($allSocialPrograms as $prog) {
                    if (empty($prog->total_collected) || (float) $prog->total_collected === 0.0) {
                        $fallbackCollected = (float) ProgramParticipant::where(function ($q) use ($prog) {
                            $q->where('social_program_id', $prog->id)
                              ->orWhere('program_name', $prog->name)
                              ->orWhere('program_name', str_replace('Program ', '', $prog->name));
                        })
                        ->whereIn('name', $ownNames)
                        ->whereIn('period', $ytdPeriods)
                        ->sum('monthly_amount');

                        if ($fallbackCollected > 0) {
                            $prog->total_collected = $fallbackCollected;
                        }
                    }

                    if (empty($prog->monthly_commitment_total) || (float) $prog->monthly_commitment_total === 0.0) {
                        $fallbackMonthly = (float) ProgramParticipant::where(function ($q) use ($prog, $currentPeriod) {
                            $q->where('social_program_id', $prog->id)
                              ->orWhere('program_name', $prog->name)
                              ->orWhere('program_name', str_replace('Program ', '', $prog->name));
                        })
                        ->whereIn('name', $ownNames)
                        ->where('period', $currentPeriod)
                        ->sum('monthly_amount');

                        if ($fallbackMonthly > 0) {
                            $prog->monthly_commitment_total = $fallbackMonthly;
                        }
                    }

                    // Cegah accessor model menghitung ulang total seluruh peserta saat nilai user kosong (null)
                    $prog->setAttribute('monthly_commitment_total', (float) ($prog->getAttributes()['monthly_commitment_total'] ?? 0));
                    $prog->setAttribute('total_collected', (float) ($prog->getAttributes()['total_collected'] ?? 0));
                }

                $totalSocialProgramsCount = $allSocialPrograms->count();
                $totalSocialCommitment = (float) ProgramParticipant::where('period', $currentPeriod)
                    ->whereIn('name', $ownNames)
                    ->sum('monthly_amount');
                $totalSocialCollectedAll = (float) $allSocialPrograms->sum(fn ($p) => (float) $p->total_collected);
                $totalActiveSocialParticipants = 0;

                $socialParticipantsList = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
                $lumpSumHistory = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
                $targetTukinBulanIni = 0;
                $realisasiTukinMasuk = 0;
                $selisihTukin = 0;
            } else {
                $allSocialPrograms = SocialProgram::withCount(['participants as active_participants_count' => fn($q) => $q->where('period', $currentPeriod)])
                    ->withSum(['participants as monthly_commitment_total' => fn($q) => $q->where('period', $currentPeriod)], 'monthly_amount')
                    ->withSum(['participants as total_collected' => fn($q) => $q->whereIn('period', $ytdPeriods)], 'monthly_amount')
                    ->withSum(['finances as total_disbursed' => fn($q) => $q->where('type', 'pengeluaran')], 'amount')
                    ->get();

                foreach ($allSocialPrograms as $prog) {
                    if (empty($prog->total_collected) || (float) $prog->total_collected === 0.0) {
                        $fallbackCollected = (float) ProgramParticipant::where(function ($q) use ($prog, $ytdPeriods) {
                            $q->where('social_program_id', $prog->id)
                              ->orWhere('program_name', $prog->name)
                              ->orWhere('program_name', str_replace('Program ', '', $prog->name));
                        })->whereIn('period', $ytdPeriods)->sum('monthly_amount');
                        if ($fallbackCollected > 0) {
                            $prog->total_collected = $fallbackCollected;
                        }
                    }
                }
                $totalSocialProgramsCount = $allSocialPrograms->count();

                $socialParticipantsQuery = ProgramParticipant::with('socialProgram');
                if ($this->socialProgramFilter !== 'all') {
                    $socialParticipantsQuery->where(function ($q) {
                        $q->where('social_program_id', $this->socialProgramFilter)
                          ->orWhere('program_name', $this->socialProgramFilter);
                    });
                }
                $selectedPeriods = is_array($this->socialParticipantPeriodFilter)
                    ? array_values(array_filter($this->socialParticipantPeriodFilter, fn($p) => $p !== 'all' && !empty($p)))
                    : ($this->socialParticipantPeriodFilter !== 'all' && !empty($this->socialParticipantPeriodFilter) ? [$this->socialParticipantPeriodFilter] : []);

                if (!empty($selectedPeriods)) {
                    $socialParticipantsQuery->whereIn('period', $selectedPeriods);
                }
                if (trim($this->socialParticipantSearch) !== '') {
                    $sps = '%' . trim($this->socialParticipantSearch) . '%';
                    $socialParticipantsQuery->where('name', 'like', $sps);
                }
                $socSort = $this->tableSorts['social_participants'] ?? null;
                $isSqlite = DB::connection()->getDriverName() === 'sqlite';
                if ($socSort && !empty($socSort['field'])) {
                    $socialParticipantsQuery->orderBy($socSort['field'], $socSort['direction']);
                } elseif (! $isSqlite) {
                    $socialParticipantsQuery->orderByRaw('CAST(SUBSTRING_INDEX(period, "/", -1) AS UNSIGNED) DESC, CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(period, "/", 1), " ", -1) AS UNSIGNED) DESC')->orderBy('name');
                } else {
                    $socialParticipantsQuery->orderBy('period', 'desc')->orderBy('name');
                }
                $socialParticipantsList = $socialParticipantsQuery->paginate(15, ['*'], 'socialParticipantPage');
                $totalSocialCommitment = (float) ProgramParticipant::where('period', $currentPeriod)->sum('monthly_amount');
                $totalSocialCollectedAll = $allSocialPrograms->sum(fn ($p) => $p->total_collected);
                $totalActiveSocialParticipants = ProgramParticipant::where('period', $currentPeriod)->count();

                // Setoran Kantor (Penerimaan Kliring & Tukin)
                $nowMonth = (int) $now->month;
                $nowYear = (int) $now->year;
                $targetTukinBulanIni = (float) ProgramParticipant::where('period', $currentPeriod)->sum('monthly_amount');
                $realisasiTukinMasuk = (float) Finance::pemasukan()
                    ->whereMonth('transaction_date', $nowMonth)
                    ->whereYear('transaction_date', $nowYear)
                    ->where(function ($q) {
                        $q->where('description', 'like', '%tukin%')
                          ->orWhere('description', 'like', '%kolektif%')
                          ->orWhere('description', 'like', '%kliring%');
                    })
                    ->sum('amount');
                $selisihTukin = $targetTukinBulanIni - $realisasiTukinMasuk;

                $lumpSumQuery = Finance::pemasukan()
                    ->where(function ($q) {
                        $q->where('description', 'like', '%tukin potong gaji%')
                          ->orWhere('description', 'like', '%tukin%')
                          ->orWhere('description', 'like', '%kolektif%')
                          ->orWhere('description', 'like', '%kliring%');
                    });
                $lumpSort = $this->tableSorts['lump_sum'] ?? null;
                if ($lumpSort && !empty($lumpSort['field'])) {
                    $lumpSumQuery->orderBy($lumpSort['field'], $lumpSort['direction']);
                } else {
                    $lumpSumQuery->latest('transaction_date')->latest('id');
                }
                $lumpSumHistory = $lumpSumQuery->paginate(10, ['*'], 'lumpSumPage');
            }
        }

        // 8. Tab Settings
        if ($tab === 'settings') {
            $allCities = $prayerService->getCities();
        }

        // 9. Tab Statistics
        if ($tab === 'statistics' || $tab === 'statistik') {
            VisitorLog::ensureTableExists();

            try {
                $periodStart = match ($this->statsPeriod) {
                    'today' => $today->copy()->startOfDay(),
                    '7_days' => $today->copy()->subDays(6)->startOfDay(),
                    '30_days' => $today->copy()->subDays(29)->startOfDay(),
                    default => null,
                };

                $statsTotalVisits = $periodStart
                    ? VisitorLog::where('visited_at', '>=', $periodStart)->count()
                    : VisitorLog::count();

                $statsUniqueVisitors = $periodStart
                    ? VisitorLog::where('visited_at', '>=', $periodStart)->distinct('ip_address')->count('ip_address')
                    : VisitorLog::distinct('ip_address')->count('ip_address');

                $statsTodayVisits = VisitorLog::where('visited_at', '>=', $today->copy()->startOfDay())->count();

                // Device ratio
                $deviceCounts = VisitorLog::query()
                    ->when($periodStart, fn($q) => $q->where('visited_at', '>=', $periodStart))
                    ->selectRaw('device_type, count(*) as count')
                    ->groupBy('device_type')
                    ->pluck('count', 'device_type');
                $totalDev = $deviceCounts->sum();
                $mobileCount = ($deviceCounts['mobile'] ?? 0) + ($deviceCounts['tablet'] ?? 0);
                $statsMobilePercent = $totalDev > 0 ? (int) round(($mobileCount / $totalDev) * 100) : 0;
                $statsDesktopPercent = 100 - $statsMobilePercent;

                // Trend Chart
                $chartDays = in_array($this->statsPeriod, ['30_days', 'all']) ? 14 : 7;
                $chartStart = $today->copy()->subDays($chartDays - 1)->startOfDay();

                $dailyRaw = VisitorLog::where('visited_at', '>=', $chartStart)
                    ->selectRaw('DATE(visited_at) as visit_date, count(*) as total, count(distinct ip_address) as unique_total')
                    ->groupBy('visit_date')
                    ->orderBy('visit_date')
                    ->get()
                    ->keyBy('visit_date');

                $statsDailyChart = [];
                $maxVal = 1;
                for ($i = $chartDays - 1; $i >= 0; $i--) {
                    $d = $today->copy()->subDays($i);
                    $dateStr = $d->format('Y-m-d');
                    $item = $dailyRaw->get($dateStr);
                    $c = $item ? (int) $item->total : 0;
                    $u = $item ? (int) $item->unique_total : 0;
                    if ($c > $maxVal) {
                        $maxVal = $c;
                    }
                    $statsDailyChart[] = [
                        'date' => $dateStr,
                        'day_name' => $d->translatedFormat('D'),
                        'date_label' => $d->translatedFormat('d M'),
                        'count' => $c,
                        'unique' => $u,
                        'percent' => 0,
                    ];
                }
                foreach ($statsDailyChart as &$bar) {
                    $bar['percent'] = $maxVal > 0 ? max(6, (int) round(($bar['count'] / $maxVal) * 100)) : 6;
                }
                unset($bar);

                // Top 5 Visited Pages
                $statsTopPages = VisitorLog::query()
                    ->when($periodStart, fn($q) => $q->where('visited_at', '>=', $periodStart))
                    ->selectRaw('url, count(*) as total')
                    ->groupBy('url')
                    ->orderByDesc('total')
                    ->take(5)
                    ->get();

                // Logs stream with search & pagination
                $logsQuery = VisitorLog::query();
                if ($periodStart) {
                    $logsQuery->where('visited_at', '>=', $periodStart);
                }
                if (trim($this->statsSearch) !== '') {
                    $kw = '%' . trim($this->statsSearch) . '%';
                    $logsQuery->where(function ($q) use ($kw) {
                        $q->where('ip_address', 'like', $kw)
                          ->orWhere('url', 'like', $kw)
                          ->orWhere('browser', 'like', $kw)
                          ->orWhere('platform', 'like', $kw);
                    });
                }
                $statsRecentLogs = $logsQuery->latest('visited_at')->latest('id')->paginate(15, ['*'], 'statsPage');
                $statsTotalFilteredLogs = $statsRecentLogs->total();
            } catch (\Throwable $e) {
                // Fail-safe jika tabel sedang dibuat atau ada glitch database sementara
            }
        }

        $takmirDocs = $settings->getTakmirDocuments();
        $takmirStructure = $settings->getTakmirStructure();

        return view('livewire.admin.admin-dashboard', [
            'settings' => $settings,
            'takmirDocs' => $takmirDocs,
            'takmirStructure' => $takmirStructure,
            'categories' => $categories,
            'prayers' => $prayers,
            'upcomingKajians' => $upcomingKajians,
            'recentFinances' => $recentFinances,
            'currentWeekDuties' => $currentWeekDuties,
            'activeEventsCount' => $activeEventsCount,
            'totalJamaah' => $totalJamaah,
            'attendanceRate' => $attendanceRate,
            'todayEvents' => $todayEvents,
            'recentRegistrants' => $recentRegistrants,
            'allEvents' => $allEvents,
            'registrantsList' => $registrantsList,
            'materials' => $materials,
            'allKajianPekanan' => $allKajianPekanan,
            'posterConfig' => PosterSetting::getAppConfig(),
            'allKajianJumat' => $allKajianJumat,
            'totalKajianCount' => $totalKajianCount,
            'allUstadzs' => $allUstadzs,
            'totalUstadzsCount' => $totalUstadzsCount,
            'ustadzListForDatalist' => $ustadzListForDatalist,
            'ustadzPhotoMap' => $ustadzPhotoMap,
            'availableKajianYears' => $availableKajianYears,
            'kajianCurrentWeekStart' => $kajianCurrentWeekStart,
            'kajianCurrentWeekEnd' => $kajianCurrentWeekEnd,
            'nextPekananKajianId' => $nextPekananKajianId,
            'nextJumatKajianId' => $nextJumatKajianId,
            'todayDate' => $today->format('Y-m-d'),
            'allPrayerDuties' => $allPrayerDuties,
            'totalPrayerDutiesCount' => $totalPrayerDutiesCount,
            'availableDutyYears' => $availableDutyYears,
            'dutyMonths' => $dutyMonths,
            'dutyFilterYearNum' => $dutyFilterYearNum,
            'dutyFilterMonthNum' => $dutyFilterMonthNum,
            'dutyFilterWeekNum' => $dutyFilterWeekNum,
            'allAgendas' => $allAgendas,
            'totalAgendasCount' => $totalAgendasCount,
            'odojEntries' => $odojEntries,
            'odojCompletedCount' => $odojCompletedCount,
            'selectedReportAgenda' => $selectedReportAgenda,
            'odojWhatsappText' => ($tab === 'kegiatan' || $tab === 'agenda') ? $this->getOdojWhatsappText() : '',
            'currentWeekNumber' => $currentWeekNumber,
            'todayDayName' => $todayDayName,
            'fridayKajians' => $fridayKajians,
            'fridayKajiansByWeek' => $fridayKajiansByWeek,
            'fridayKajiansByDate' => $fridayKajiansByDate,
            'pekananKajiansByDayWeek' => $pekananKajiansByDayWeek,
            'pekananKajiansByDay' => $pekananKajiansByDay,
            'pekananKajiansByWeek' => $pekananKajiansByWeek,
            'kajianUmumByDate' => $kajianUmumByDate,
            'defaultPekananKajian' => $defaultPekananKajian,
            'allCities' => $allCities,
            'usersList' => $usersList,
            'totalUsersCount' => $totalUsersCount,
            'employeesList' => $employeesList,
            'totalEmployeesCount' => $totalEmployeesCount,
            'activeEmployeesCount' => $activeEmployeesCount,
            'financeCategories' => $financeCategories,
            'groupedFinanceCategories' => $groupedFinanceCategories,
            'financesList' => $financesList,
            'totalFinancesCount' => $totalFinancesCount,
            'totalKasMasuk' => $totalKasMasuk,
            'totalKasKeluar' => $totalKasKeluar,
            'saldoSaatIni' => $saldoSaatIni,
            'saldoAwal' => $saldoAwal,
            'participantsList' => $participantsList,
            'activeParticipantsCount' => $activeParticipantsCount,
            'targetTukinBulanIni' => $targetTukinBulanIni,
            'realisasiTukinMasuk' => $realisasiTukinMasuk,
            'selisihTukin' => $selisihTukin,
            'lumpSumHistory' => $lumpSumHistory,
            'programFinancesSummary' => $programFinancesSummary,
            'programParticipantCounts' => $programParticipantCounts,
            // Milestone 6
            'allSocialPrograms' => $allSocialPrograms,
            'totalSocialProgramsCount' => $totalSocialProgramsCount,
            'socialParticipantsList' => $socialParticipantsList,
            'totalSocialCommitment' => $totalSocialCommitment,
            'totalSocialCollectedAll' => $totalSocialCollectedAll,
            'totalActiveSocialParticipants' => $totalActiveSocialParticipants,
            'availablePeriods' => $availablePeriods,
            'distinctParticipantNames' => (function() {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('program_participants')) {
                        return ProgramParticipant::distinct()->orderBy('name')->pluck('name')->filter()->values();
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
                return collect();
            })(),
            'allEmployeesForPrint' => (function() {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('employees')) {
                        return Employee::where('status', 'AKTIF')->orderBy('name')->get(['id', 'name', 'nip_panjang', 'nip_pendek', 'jabatan', 'unit_kerja']);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
                return collect();
            })(),
            // Tab Galeri
            'activityGalleries' => (function() {
                ActivityGallery::ensureTableExists();
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('activity_galleries')) {
                        return ActivityGallery::query()
                            ->search($this->gallerySearch)
                            ->year($this->galleryYearFilter)
                            ->orderByDesc('event_date')
                            ->orderByDesc('id')
                            ->get();
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
                return collect();
            })(),
            'galleryYearsList' => (function() {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('activity_galleries')) {
                        $driver = DB::connection()->getDriverName();
                        $rawYear = $driver === 'sqlite' ? "strftime('%Y', event_date) as year" : "YEAR(event_date) as year";
                        return ActivityGallery::query()
                            ->selectRaw($rawYear)
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year')
                            ->filter()
                            ->map(fn($y) => (int) $y)
                            ->values()
                            ->toArray() ?: [(int) date('Y')];
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
                return [(int) date('Y')];
            })(),
            // Tab Saran & Kritik
            'saranList' => (function() {
                FeedbackSuggestion::ensureTableExists();
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('feedback_suggestions')) {
                        $query = FeedbackSuggestion::query();

                        if (!empty($this->saranSearch)) {
                            $q = '%' . trim($this->saranSearch) . '%';
                            $query->where(function ($w) use ($q) {
                                $w->where('name', 'like', $q)
                                  ->orWhere('title', 'like', $q)
                                  ->orWhere('message', 'like', $q)
                                  ->orWhere('contact', 'like', $q);
                            });
                        }

                        if ($this->saranStatusFilter !== 'all') {
                            $query->where('status', $this->saranStatusFilter);
                        }

                        if ($this->saranCategoryFilter !== 'all') {
                            $query->where('category', $this->saranCategoryFilter);
                        }

                        return $query->orderByDesc('id')->paginate(15);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            })(),
            // Tab Statistics
            'statsTotalVisits' => $statsTotalVisits,
            'statsUniqueVisitors' => $statsUniqueVisitors,
            'statsTodayVisits' => $statsTodayVisits,
            'statsMobilePercent' => $statsMobilePercent,
            'statsDesktopPercent' => $statsDesktopPercent,
            'statsDailyChart' => $statsDailyChart,
            'statsTopPages' => $statsTopPages,
            'statsRecentLogs' => $statsRecentLogs,
            'statsTotalFilteredLogs' => $statsTotalFilteredLogs,
        ])->title($this->getPageTitle());
    }
}
