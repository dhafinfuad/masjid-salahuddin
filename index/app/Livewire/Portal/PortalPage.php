<?php

namespace App\Livewire\Portal;

use App\Models\Agenda;
use App\Models\Category;
use App\Models\Event;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use App\Models\ProgramParticipant;
use App\Models\Registrant;
use App\Models\SocialProgram;
use App\Services\PrayerTimeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Portal Jamaah — Masjid Salahuddin')]
class PortalPage extends Component
{
    public string $selectedCategory = 'all';
    public string $search = '';

    public string $cityId = '1634';
    public string $cityName = 'Kota Malang';

    public array $prayers = [];
    public array $nextPrayer = [];
    public string $hijriDate = '';

    // Milestone 6: Keikutsertaan Saya State
    public string $searchParticipantQuery = '';
    public ?array $myParticipations = null;
    public bool $hasSearchedParticipation = false;

    // Milestone 6: Pendaftaran Program Sosial Modal
    public bool $showSocialRegisterModal = false;
    public ?int $selectedSocialProgramId = null;
    public string $socialParticipantName = '';
    public string $socialParticipantProgram = 'Santunan Anak Yatim';
    public string $socialParticipantAmount = '250000';
    public string $socialParticipantPeriod = 'Bulanan';
    public bool $socialRegisterSuccess = false;
    public string $socialRegisterMessage = '';

    public function mount(PrayerTimeService $prayerService): void
    {
        /** @var MasjidSetting $settings */
        $settings = MasjidSetting::getActive();
        $this->cityId = $settings->city_id ?: '1634';
        $this->cityName = $settings->city_name ? ucwords(strtolower($settings->city_name)) : 'Kota Malang';
        $this->loadPrayers($prayerService);
        $this->hijriDate = $prayerService->getHijriDayAndDate();
    }

    public function updatedCityId(PrayerTimeService $prayerService): void
    {
        $cities = $prayerService->getCities();
        foreach ($cities as $c) {
            if ($c['id'] == $this->cityId) {
                $this->cityName = ucwords(strtolower($c['lokasi']));
                break;
            }
        }
        $this->loadPrayers($prayerService);
    }

    public function selectCity(string $id, string $name, PrayerTimeService $prayerService): void
    {
        $this->cityId = $id;
        $this->cityName = ucwords(strtolower($name));
        $this->loadPrayers($prayerService);
    }

    public function loadPrayers(PrayerTimeService $prayerService): void
    {
        $settings = MasjidSetting::getActive();
        $this->prayers = $prayerService->getPrayerTimes($this->cityId, null, $settings);
        $this->nextPrayer = $prayerService->getNextPrayer($this->prayers);
    }

    public function setCategory(string $slug): void
    {
        $this->selectedCategory = $slug;
    }

    // Milestone 6: Keikutsertaan Saya Methods
    public function checkMyParticipation(): void
    {
        $query = trim($this->searchParticipantQuery);
        if (strlen($query) < 3) {
            $this->addError('searchParticipantQuery', 'Masukkan minimal 3 karakter (Nama Lengkap).');
            return;
        }

        $results = ProgramParticipant::with('socialProgram')
            ->where('name', 'like', "%{$query}%")
            ->get();

        $this->myParticipations = $results->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'program_name' => $p->socialProgram->name ?? $p->program_name,
                'monthly_amount' => (float) $p->monthly_amount,
                'period' => $p->period,
                'created_at' => $p->created_at ? $p->created_at->format('d M Y') : '-',
            ];
        })->toArray();

        $this->hasSearchedParticipation = true;
    }

    public function resetParticipationCheck(): void
    {
        $this->searchParticipantQuery = '';
        $this->myParticipations = null;
        $this->hasSearchedParticipation = false;
        $this->resetErrorBag('searchParticipantQuery');
    }

    // Milestone 6: Pendaftaran Program Sosial
    public function openSocialRegisterModal(?int $programId = null): void
    {
        $this->selectedSocialProgramId = $programId;
        if ($programId) {
            $prog = SocialProgram::find($programId);
            $this->socialParticipantProgram = $prog ? $prog->name : 'Santunan Anak Yatim';
        } else {
            $this->socialParticipantProgram = 'Santunan Anak Yatim';
        }

        $this->socialParticipantName = '';
        $this->socialParticipantAmount = '250000';
        $this->socialParticipantPeriod = 'Bulanan';
        $this->socialRegisterSuccess = false;
        $this->socialRegisterMessage = '';
        $this->showSocialRegisterModal = true;
    }

    public function closeSocialRegisterModal(): void
    {
        $this->showSocialRegisterModal = false;
        $this->reset([
            'selectedSocialProgramId',
            'socialParticipantName',
            'socialParticipantAmount',
            'socialRegisterSuccess',
            'socialRegisterMessage'
        ]);
    }

    public function updatedSocialParticipantAmount($val): void
    {
        $this->socialParticipantAmount = preg_replace('/\D/', '', (string) $val);
    }

    public function submitSocialRegistration(): void
    {
        $this->socialParticipantAmount = preg_replace('/\D/', '', (string) $this->socialParticipantAmount);

        $this->validate([
            'socialParticipantName' => 'required|string|min:3|max:150',
            'socialParticipantProgram' => 'required|string',
            'socialParticipantAmount' => 'required|numeric|min:1000',
        ], [
            'socialParticipantName.required' => 'Nama lengkap jamaah/pegawai wajib diisi.',
            'socialParticipantProgram.required' => 'Program sosial wajib dipilih.',
            'socialParticipantAmount.required' => 'Nominal komitmen bulanan wajib diisi.',
        ]);

        $prog = SocialProgram::where('name', $this->socialParticipantProgram)->first();

        ProgramParticipant::create([
            'social_program_id' => $prog?->id,
            'name' => $this->socialParticipantName,
            'program_name' => $this->socialParticipantProgram,
            'monthly_amount' => (float) $this->socialParticipantAmount,
            'period' => $this->socialParticipantPeriod ?: ('Periode ' . now()->month . '/' . now()->year),
        ]);

        $this->socialRegisterSuccess = true;
        $this->socialRegisterMessage = "Alhamdulillah! Pendaftaran komitmen program sosial '{$this->socialParticipantProgram}' berhasil tersimpan.";
    }

    public function render()
    {
        $settings = MasjidSetting::getActive();
        $now = now();
        $currentPeriod = 'Periode ' . (int) $now->month . '/' . (int) $now->year;
        $ytdPeriods = [];
        for ($m = 1; $m <= (int) $now->month; $m++) {
            $ytdPeriods[] = "Periode {$m}/{$now->year}";
        }

        $socialPrograms = SocialProgram::active()
            ->withCount(['participants as active_participants_count' => fn($q) => $q->where('period', $currentPeriod)])
            ->withSum(['participants as monthly_commitment_total' => fn($q) => $q->where('period', $currentPeriod)], 'monthly_amount')
            ->withSum(['participants as total_collected' => fn($q) => $q->whereIn('period', $ytdPeriods)], 'monthly_amount')
            ->withSum(['finances as total_disbursed' => fn($q) => $q->where('type', 'pengeluaran')], 'amount')
            ->get();

        foreach ($socialPrograms as $prog) {
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

        // Jadwal Kajian & Khutbah Jumat Terdekat (Riil)
        Kajian::ensureNotulaColumnExists();
        $today = Carbon::today();
        $kajians = Kajian::where('date', '>=', $today->copy()->subDays(1))
            ->orderBy('date', 'asc')
            ->take(8)
            ->get();
        if ($kajians->isEmpty()) {
            $kajians = Kajian::orderBy('date', 'desc')->take(8)->get();
        }

        // Khutbah Jumat Terdekat / Hari Ini untuk Sinkronisasi Petugas Sholat Jumat
        $targetFridayDate = $today->isFriday() ? $today->copy() : $today->copy()->next(Carbon::FRIDAY);
        $currentFridayKajian = Kajian::jumat()
            ->whereDate('date', $targetFridayDate)
            ->first();
        if (!$currentFridayKajian) {
            $currentFridayKajian = Kajian::jumat()
                ->whereDate('date', '>=', $today)
                ->orderBy('date', 'asc')
                ->first();
        }

        // Agenda Kegiatan Besar Masjid (Riil)
        $agendas = Agenda::orderBy('event_date', 'asc')->get();

        // Target Petugas Sholat: Adaptif terhadap jam dan hari
        $now = Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i');

        // Batas waktu:
        // - Waktu Shalat Jumat selesai: 14:00 WIB
        // - Waktu Shalat Ashar selesai: waktu Maghrib (default 17:30 WIB)
        $maghribTime = '17:30';
        if (!empty($this->prayers)) {
            foreach ($this->prayers as $p) {
                if (($p['key'] ?? '') === 'maghrib' && !empty($p['adzan'])) {
                    $maghribTime = $p['adzan'];
                    break;
                }
            }
        }

        $isAfterAshar = ($currentTime >= $maghribTime);
        $isFridayToday = $now->isFriday();

        // Tentukan tanggal jadwal yang akan ditampilkan ($dutyDate):
        // Jika malam hari (setelah Shalat Ashar selesai / maghrib ke atas), otomatis beralih memuat jadwal H+1
        if ($isAfterAshar) {
            if ($isFridayToday || $now->isSaturday() || $now->isSunday()) {
                // Setelah Ashar Jumat atau saat akhir pekan: hari dinas aktif berikutnya adalah Senin depan
                $dutyDate = $now->copy()->next(Carbon::MONDAY);
            } else {
                // Senin s.d. Kamis malam: beralih ke esok hari (H+1)
                $dutyDate = $now->copy()->addDay();
            }
        } else {
            // Sebelum waktu Ashar selesai:
            if ($now->isSaturday() || $now->isSunday()) {
                $dutyDate = $now->copy()->next(Carbon::MONDAY);
            } else {
                $dutyDate = $now->copy();
            }
        }

        $isFridayDuty = $dutyDate->isFriday();

        // Di Hari Jumat:
        // - Sebelum / saat waktu Shalat Jumat (sebelum pukul 14.00 WIB): Tampilkan petugas Khutbah & Shalat Jumat (Khatib, MC, Bilal).
        // - Setelah waktu Shalat Jumat selesai (mulai 14.00 WIB): Kartu otomatis berganti menampilkan Petugas Shalat Ashar Jumat (Imam & Muadzin Ashar).
        // Catatan: Jika sedang memuat jadwal Jumat H+1 (misal dibuka Kamis malam), tetap tampilkan Khutbah Jumat.
        $isFridayAsharDuty = ($isFridayToday && $dutyDate->isSameDay($now) && $currentTime >= '14:00' && !$isAfterAshar);

        // Penyesuaian title dinamis kartu Petugas Shalat: Dhuhur / Ashar / Jumat
        if ($isFridayDuty && !$isFridayAsharDuty) {
            $prayerDutyTypeTitle = 'Jumat';
        } elseif ($isFridayDuty && $isFridayAsharDuty) {
            $prayerDutyTypeTitle = 'Ashar';
        } else {
            // Hari Senin - Kamis:
            // Jika hari ini dan sudah masuk waktu Ashar (pukul 14.00 s.d. maghrib): Ashar
            // Selain itu (pagi / sebelum 14.00, atau preview H+1): Dhuhur
            $prayerDutyTypeTitle = (!$dutyDate->isFriday() && $dutyDate->isSameDay($now) && $currentTime >= '14:00' && !$isAfterAshar)
                ? 'Ashar'
                : 'Dhuhur';
        }

        $dutyTitle = 'Petugas ' . $prayerDutyTypeTitle;

        $dayMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat'];
        $dutyDayName = $dayMap[$dutyDate->dayOfWeekIso] ?? 'Senin';
        $dutyWeekNumber = PrayerDuty::getWeekOfMonth($dutyDate);

        $dailyDuties = PrayerDuty::forDay($dutyDayName)->forWeek($dutyWeekNumber)->get();
        $dzuhurDuty = $dailyDuties->firstWhere('prayer_time', 'dzuhur');
        $asharDuty = $dailyDuties->firstWhere('prayer_time', 'ashar');

        $kajianToday = Kajian::kajianUmum()->whereDate('date', $dutyDate->format('Y-m-d'))->first();
        if ($kajianToday && !empty($kajianToday->speaker_name)) {
            if ($asharDuty) {
                $asharDuty->imam_name = $kajianToday->speaker_name;
                if (!empty($kajianToday->muadzin_name)) {
                    $asharDuty->muadzin_name = $kajianToday->muadzin_name;
                }
            }
            if ($dzuhurDuty && stripos($dzuhurDuty->imam_name, 'kajian') !== false) {
                $dzuhurDuty->imam_name = $kajianToday->speaker_name;
            }
        }

        // Friday duty fallbacks if Friday
        $targetFridayDate = $dutyDate->isFriday() ? $dutyDate->copy() : $dutyDate->copy()->next(Carbon::FRIDAY);
        $currentFridayKajian = Kajian::jumat()->whereDate('date', $targetFridayDate)->first();
        if (!$currentFridayKajian) {
            $currentFridayKajian = Kajian::jumat()->whereDate('date', '>=', $dutyDate)->orderBy('date', 'asc')->first();
        }

        $fridayKhatib = $currentFridayKajian ? ($currentFridayKajian->khatib_name ?: 'Ust. M. Yasak Lc MA') : ($settings->friday_prayer_info['khatib'] ?? 'Ust. M. Yasak Lc MA');
        $fridayMc = $currentFridayKajian ? ($currentFridayKajian->mc_name ?: ($currentFridayKajian->speaker_name ?: 'Alan Irfansyah')) : ($settings->friday_prayer_info['mc'] ?? 'Alan Irfansyah');
        $fridayMuadzin = $currentFridayKajian ? ($currentFridayKajian->muadzin_name ?: ($currentFridayKajian->description ?: 'Khodori')) : ($settings->friday_prayer_info['muadzin'] ?? 'Khodori');

        // Waktu Sholat Dzuhur & Ashar hari ini untuk card Petugas Sholat
        $dzuhurPrayerTime = '11:45';
        $asharPrayerTime = '15:00';
        if (!empty($this->prayers)) {
            foreach ($this->prayers as $p) {
                if (($p['key'] ?? '') === 'dzuhur' && !empty($p['adzan'])) {
                    $dzuhurPrayerTime = $p['adzan'];
                }
                if (($p['key'] ?? '') === 'ashar' && !empty($p['adzan'])) {
                    $asharPrayerTime = $p['adzan'];
                }
            }
        }

        return view('livewire.portal.portal-page', [
            'settings' => $settings,
            'kajians' => $kajians,
            'currentFridayKajian' => $currentFridayKajian,
            'agendas' => $agendas,
            'cities' => app(PrayerTimeService::class)->getCities(),
            'socialPrograms' => $socialPrograms,
            'dutyDate' => $dutyDate,
            'dutyTitle' => $dutyTitle,
            'prayerDutyTypeTitle' => $prayerDutyTypeTitle,
            'isFridayDuty' => $isFridayDuty,
            'isFridayAsharDuty' => $isFridayAsharDuty,
            'dzuhurDuty' => $dzuhurDuty,
            'asharDuty' => $asharDuty,
            'dzuhurPrayerTime' => $dzuhurPrayerTime,
            'asharPrayerTime' => $asharPrayerTime,
            'fridayKhatib' => $fridayKhatib,
            'fridayMc' => $fridayMc,
            'fridayMuadzin' => $fridayMuadzin,
            'hijriDate' => $this->hijriDate ?: app(PrayerTimeService::class)->getHijriDayAndDate(),
        ]);
    }
}
