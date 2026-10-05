<?php

namespace App\Services;

use App\Models\MasjidSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrayerTimeService
{
    protected static ?array $cachedCities = null;

    /**
     * Get list of all cities/regencies from Kemenag / MyQuran API (cached).
     */
    public function getCities(): array
    {
        if (static::$cachedCities !== null) {
            return static::$cachedCities;
        }

        return static::$cachedCities = Cache::remember('kemenag_prayer_cities', 86400 * 30, function () {
            try {
                $response = Http::timeout(5)->get('https://api.myquran.com/v2/sholat/kota/semua');
                if ($response->successful() && $response->json('status')) {
                    $data = $response->json('data');
                    if (is_array($data) && count($data) > 0) {
                        return $data;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch cities from MyQuran API: ' . $e->getMessage());
            }

            // Fallback list of major Indonesian cities
            return [
                ['id' => '1634', 'lokasi' => 'KOTA MALANG'],
                ['id' => '1614', 'lokasi' => 'KAB. MALANG'],
                ['id' => '1638', 'lokasi' => 'KOTA SURABAYA'],
                ['id' => '1301', 'lokasi' => 'KOTA JAKARTA'],
                ['id' => '1203', 'lokasi' => 'KOTA BANDUNG'],
                ['id' => '1434', 'lokasi' => 'KOTA SEMARANG'],
                ['id' => '1505', 'lokasi' => 'KOTA YOGYAKARTA'],
                ['id' => '0228', 'lokasi' => 'KOTA MEDAN'],
                ['id' => '2310', 'lokasi' => 'KOTA MAKASSAR'],
                ['id' => '1701', 'lokasi' => 'KOTA DENPASAR'],
            ];
        });
    }

    protected static ?array $cachedCitiesById = null;

    /**
     * Get associative array of cities keyed by ID for O(1) instant lookup.
     */
    public function getCitiesKeyedById(): array
    {
        if (static::$cachedCitiesById !== null) {
            return static::$cachedCitiesById;
        }

        $cities = $this->getCities();
        $keyed = [];
        foreach ($cities as $c) {
            $keyed[(string) $c['id']] = $c;
        }

        return static::$cachedCitiesById = $keyed;
    }

    /**
     * Get 5 daily prayer times from Kemenag API (with Kota Malang as default)
     * and fallback to accurate astronomical hisab if offline.
     */
    public function getPrayerTimes(?string $cityId = null, ?Carbon $date = null, ?MasjidSetting $settings = null): array
    {
        $date = $date ?? Carbon::now('Asia/Jakarta');
        $settings = $settings ?? MasjidSetting::getActive();
        $cityId = $cityId ?: ($settings->city_id ?: '1634'); // Default: 1634 (KOTA MALANG)

        $dateKey = $date->format('Y-m-d');
        $cacheKey = "prayer_schedule_{$cityId}_{$dateKey}";

        $rawSchedule = Cache::remember($cacheKey, 86400 * 7, function () use ($cityId, $date, $settings) {
            $apiResult = $this->fetchFromApi($cityId, $date);
            return $apiResult ?: $this->calculateFallbackSchedule($cityId, $date, $settings);
        });

        $delay = (int) ($settings->iqamah_delay_minutes ?? 10);
        $currentTime = $date->format('H:i');

        $formatIqamah = function (string $adzanStr, int $delayMins = 10) {
            [$h, $m] = explode(':', $adzanStr);
            $total = ((int) $h * 60) + (int) $m + $delayMins;
            $nh = floor($total / 60) % 24;
            $nm = $total % 60;
            return sprintf('%02d:%02d', $nh, $nm);
        };

        // Build prayers array with complete status
        $rawPrayers = [
            [
                'key' => 'subuh',
                'name' => 'Subuh',
                'arabic' => 'الفجر',
                'adzan' => $rawSchedule['subuh'] ?? '04:09',
                'iqamah' => $formatIqamah($rawSchedule['subuh'] ?? '04:09', $delay),
            ],
            [
                'key' => 'dzuhur',
                'name' => 'Dzuhur',
                'arabic' => 'الظهر',
                'adzan' => $rawSchedule['dzuhur'] ?? '11:29',
                'iqamah' => $formatIqamah($rawSchedule['dzuhur'] ?? '11:29', $delay),
            ],
            [
                'key' => 'ashar',
                'name' => 'Ashar',
                'arabic' => 'العصر',
                'adzan' => $rawSchedule['ashar'] ?? '14:45',
                'iqamah' => $formatIqamah($rawSchedule['ashar'] ?? '14:45', $delay),
            ],
            [
                'key' => 'maghrib',
                'name' => 'Maghrib',
                'arabic' => 'المغرب',
                'adzan' => $rawSchedule['maghrib'] ?? '17:30',
                'iqamah' => $formatIqamah($rawSchedule['maghrib'] ?? '17:30', min(10, $delay)),
            ],
            [
                'key' => 'isya',
                'name' => 'Isya',
                'arabic' => 'العشاء',
                'adzan' => $rawSchedule['isya'] ?? '18:39',
                'iqamah' => $formatIqamah($rawSchedule['isya'] ?? '18:39', $delay),
            ],
        ];

        // Determine which one is next
        $nextFound = false;
        $prayers = [];

        foreach ($rawPrayers as $p) {
            $isAdzanPassed = $currentTime >= $p['adzan'];
            $isIqamahPassed = $currentTime >= $p['iqamah'];
            $isOngoing = $isAdzanPassed && ! $isIqamahPassed;
            $isNext = false;

            if (! $isAdzanPassed && ! $nextFound) {
                $isNext = true;
                $nextFound = true;
            }

            $p['is_passed'] = $isNext ? false : $isIqamahPassed;
            $p['is_adzan_passed'] = $isNext ? false : $isAdzanPassed;
            $p['is_ongoing'] = $isOngoing;
            $p['is_next'] = $isNext;

            $prayers[] = $p;
        }

        // If all 5 adzan times for today have passed (e.g. late night after Isya), tomorrow's Subuh is next
        if (! $nextFound && count($prayers) > 0) {
            $prayers[0]['is_next'] = true;
            $prayers[0]['is_passed'] = false;
            $prayers[0]['is_adzan_passed'] = false;
            $prayers[0]['is_ongoing'] = false;
        }

        // Strictly ensure only ONE prayer has is_active = true at any moment
        $hasOngoing = false;
        foreach ($prayers as $p) {
            if (!empty($p['is_ongoing'])) {
                $hasOngoing = true;
                break;
            }
        }

        foreach ($prayers as &$p) {
            if ($hasOngoing) {
                $p['is_active'] = !empty($p['is_ongoing']);
            } else {
                $p['is_active'] = !empty($p['is_next']);
            }
        }
        unset($p);

        return $prayers;
    }

    /**
     * Fetch schedule from MyQuran / Kemenag API
     */
    protected function fetchFromApi(string $cityId, Carbon $date): ?array
    {
        try {
            $year = $date->format('Y');
            $month = $date->format('m');
            $day = $date->format('d');

            $url = "https://api.myquran.com/v2/sholat/jadwal/{$cityId}/{$year}/{$month}/{$day}";
            $response = Http::timeout(0.6)->get($url);

            if ($response->successful() && $response->json('status')) {
                $jadwal = $response->json('data.jadwal');
                if ($jadwal && isset($jadwal['subuh'], $jadwal['dzuhur'])) {
                    return [
                        'imsak' => $jadwal['imsak'] ?? '03:59',
                        'subuh' => $jadwal['subuh'] ?? '04:09',
                        'terbit' => $jadwal['terbit'] ?? '05:21',
                        'dzuhur' => $jadwal['dzuhur'] ?? '11:29',
                        'ashar' => $jadwal['ashar'] ?? '14:45',
                        'maghrib' => $jadwal['maghrib'] ?? '17:30',
                        'isya' => $jadwal['isya'] ?? '18:39',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::info("MyQuran API unreachable or timed out for city {$cityId}, falling back to astronomical hisab: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Standard Astronomical Hisab Fallback (Jean Meeus algorithm for Indonesia)
     */
    protected function calculateFallbackSchedule(string $cityId, Carbon $date, MasjidSetting $settings): array
    {
        $cityCoords = [
            // Seluruh 38 Kota & Kabupaten Jawa Timur (Kemenag 1601 - 1638)
            '1601' => ['lat' => -7.0455, 'lng' => 112.7443, 'tz' => 7], // Kab. Bangkalan
            '1602' => ['lat' => -8.2192, 'lng' => 114.3692, 'tz' => 7], // Kab. Banyuwangi
            '1603' => ['lat' => -8.0983, 'lng' => 112.1681, 'tz' => 7], // Kab. Blitar
            '1604' => ['lat' => -7.1502, 'lng' => 111.8817, 'tz' => 7], // Kab. Bojonegoro
            '1605' => ['lat' => -7.9135, 'lng' => 113.8214, 'tz' => 7], // Kab. Bondowoso
            '1606' => ['lat' => -7.1566, 'lng' => 112.6555, 'tz' => 7], // Kab. Gresik
            '1607' => ['lat' => -8.1724, 'lng' => 113.7007, 'tz' => 7], // Kab. Jember
            '1608' => ['lat' => -7.5458, 'lng' => 112.2331, 'tz' => 7], // Kab. Jombang
            '1609' => ['lat' => -7.8480, 'lng' => 112.0178, 'tz' => 7], // Kab. Kediri
            '1610' => ['lat' => -7.1198, 'lng' => 112.4157, 'tz' => 7], // Kab. Lamongan
            '1611' => ['lat' => -8.1331, 'lng' => 113.2249, 'tz' => 7], // Kab. Lumajang
            '1612' => ['lat' => -7.6298, 'lng' => 111.5239, 'tz' => 7], // Kab. Madiun
            '1613' => ['lat' => -7.6534, 'lng' => 111.3282, 'tz' => 7], // Kab. Magetan
            '1614' => ['lat' => -8.1667, 'lng' => 112.6667, 'tz' => 7], // Kab. Malang
            '1615' => ['lat' => -7.4726, 'lng' => 112.4381, 'tz' => 7], // Kab. Mojokerto
            '1616' => ['lat' => -7.6033, 'lng' => 111.9028, 'tz' => 7], // Kab. Nganjuk
            '1617' => ['lat' => -7.4039, 'lng' => 111.4452, 'tz' => 7], // Kab. Ngawi
            '1618' => ['lat' => -8.2065, 'lng' => 111.0921, 'tz' => 7], // Kab. Pacitan
            '1619' => ['lat' => -7.1583, 'lng' => 113.4739, 'tz' => 7], // Kab. Pamekasan
            '1620' => ['lat' => -7.6453, 'lng' => 112.9075, 'tz' => 7], // Kab. Pasuruan
            '1621' => ['lat' => -7.8685, 'lng' => 111.4621, 'tz' => 7], // Kab. Ponorogo
            '1622' => ['lat' => -7.7543, 'lng' => 113.2159, 'tz' => 7], // Kab. Probolinggo
            '1623' => ['lat' => -7.1872, 'lng' => 113.2394, 'tz' => 7], // Kab. Sampang
            '1624' => ['lat' => -7.4478, 'lng' => 112.7183, 'tz' => 7], // Kab. Sidoarjo
            '1625' => ['lat' => -7.7063, 'lng' => 114.0094, 'tz' => 7], // Kab. Situbondo
            '1626' => ['lat' => -7.0167, 'lng' => 113.8667, 'tz' => 7], // Kab. Sumenep
            '1627' => ['lat' => -8.0500, 'lng' => 111.7167, 'tz' => 7], // Kab. Trenggalek
            '1628' => ['lat' => -6.8976, 'lng' => 112.0649, 'tz' => 7], // Kab. Tuban
            '1629' => ['lat' => -8.0667, 'lng' => 111.9000, 'tz' => 7], // Kab. Tulungagung
            '1630' => ['lat' => -7.8712, 'lng' => 112.5273, 'tz' => 7], // Kota Batu
            '1631' => ['lat' => -8.0983, 'lng' => 112.1681, 'tz' => 7], // Kota Blitar
            '1632' => ['lat' => -7.8167, 'lng' => 112.0167, 'tz' => 7], // Kota Kediri
            '1633' => ['lat' => -7.6298, 'lng' => 111.5239, 'tz' => 7], // Kota Madiun
            '1634' => ['lat' => -7.9797, 'lng' => 112.6304, 'tz' => 7], // Kota Malang
            '1635' => ['lat' => -7.4726, 'lng' => 112.4381, 'tz' => 7], // Kota Mojokerto
            '1636' => ['lat' => -7.6453, 'lng' => 112.9075, 'tz' => 7], // Kota Pasuruan
            '1637' => ['lat' => -7.7543, 'lng' => 113.2159, 'tz' => 7], // Kota Probolinggo
            '1638' => ['lat' => -7.2575, 'lng' => 112.7521, 'tz' => 7], // Kota Surabaya

            // Kota-kota Besar Lainnya di Indonesia
            '1301' => ['lat' => -6.2088, 'lng' => 106.8456, 'tz' => 7], // Kota Jakarta
            '1203' => ['lat' => -6.9175, 'lng' => 107.6191, 'tz' => 7], // Kota Bandung
            '1434' => ['lat' => -6.9667, 'lng' => 110.4167, 'tz' => 7], // Kota Semarang
            '1505' => ['lat' => -7.7956, 'lng' => 110.3695, 'tz' => 7], // Kota Yogyakarta
            '1101' => ['lat' => -6.1200, 'lng' => 106.1503, 'tz' => 7], // Kota Serang
            '1433' => ['lat' => -7.5666, 'lng' => 110.8166, 'tz' => 7], // Kota Surakarta
            '1201' => ['lat' => -6.5971, 'lng' => 106.8060, 'tz' => 7], // Kota Bogor

            // Sumatera
            '0228' => ['lat' => 3.5952,  'lng' => 98.6722,  'tz' => 7], // Kota Medan
            '0101' => ['lat' => 5.5483,  'lng' => 95.3238,  'tz' => 7], // Kota Banda Aceh
            '0301' => ['lat' => -0.9471, 'lng' => 100.4172, 'tz' => 7], // Kota Padang
            '0401' => ['lat' => 0.5071,  'lng' => 101.4478, 'tz' => 7], // Kota Pekanbaru
            '0601' => ['lat' => -2.9761, 'lng' => 104.7754, 'tz' => 7], // Kota Palembang
            '0816' => ['lat' => -2.9761, 'lng' => 104.7754, 'tz' => 7], // Kota Palembang Kemenag ID
            '1001' => ['lat' => -5.4500, 'lng' => 105.2667, 'tz' => 7], // Kota Bandar Lampung
            '0501' => ['lat' => 1.1301,  'lng' => 104.0529, 'tz' => 7], // Kota Batam

            // Bali & Nusa Tenggara
            '1701' => ['lat' => -8.6705, 'lng' => 115.2126, 'tz' => 8], // Kota Denpasar
            '1801' => ['lat' => -8.5833, 'lng' => 116.1167, 'tz' => 8], // Kota Mataram
            '1901' => ['lat' => -10.1772, 'lng' => 123.6070, 'tz' => 8], // Kota Kupang

            // Kalimantan
            '2001' => ['lat' => -0.0263, 'lng' => 109.3425, 'tz' => 7], // Kota Pontianak
            '2101' => ['lat' => -3.3194, 'lng' => 114.5908, 'tz' => 8], // Kota Banjarmasin
            '2201' => ['lat' => -0.5022, 'lng' => 117.1536, 'tz' => 8], // Kota Samarinda
            '2202' => ['lat' => -1.2379, 'lng' => 116.8289, 'tz' => 8], // Kota Balikpapan

            // Sulawesi
            '2310' => ['lat' => -5.1477, 'lng' => 119.4327, 'tz' => 8], // Kota Makassar
            '2401' => ['lat' => 1.4748,  'lng' => 124.8428, 'tz' => 8], // Kota Manado
            '2501' => ['lat' => -0.9003, 'lng' => 119.8779, 'tz' => 8], // Kota Palu
            '2601' => ['lat' => -3.9985, 'lng' => 122.5126, 'tz' => 8], // Kota Kendari

            // Maluku & Papua
            '2901' => ['lat' => -3.6547, 'lng' => 128.1906, 'tz' => 9], // Kota Ambon
            '3101' => ['lat' => -2.5337, 'lng' => 140.7181, 'tz' => 9], // Kota Jayapura
        ];

        // Sensible fallback: Default to Malang (WIB) if not specifically listed
        $coord = $cityCoords[$cityId] ?? ['lat' => -7.9797, 'lng' => 112.6304, 'tz' => 7];
        $lat = $coord['lat'];
        $lng = $coord['lng'];
        $timezone = $coord['tz'];

        $dayOfYear = $date->dayOfYear;
        $gamma = 2 * M_PI / 365 * ($dayOfYear - 1 + ($date->hour - 12) / 24);

        // Equation of time in minutes
        $eqtime = 229.18 * (0.000075 + 0.001868 * cos($gamma) - 0.032077 * sin($gamma)
            - 0.014615 * cos(2 * $gamma) - 0.040849 * sin(2 * $gamma));

        // Solar declination in radians
        $decl = 0.006918 - 0.399912 * cos($gamma) + 0.070257 * sin($gamma)
            - 0.006758 * cos(2 * $gamma) + 0.000907 * sin(2 * $gamma);

        // Solar transit (Dzuhur) in decimal hours
        $timeOffset = $eqtime + 4 * $lng - 60 * $timezone;
        $tstNoon = (720 - $timeOffset) / 60; // Dzuhur time in hours

        $latRad = deg2rad($lat);

        // Helper to calculate hour angle
        $hourAngle = function ($altitudeDeg) use ($latRad, $decl) {
            $altRad = deg2rad($altitudeDeg);
            $cosHA = (sin($altRad) - sin($latRad) * sin($decl)) / (cos($latRad) * cos($decl));
            if ($cosHA > 1) return 0;
            if ($cosHA < -1) return M_PI;
            return acos($cosHA);
        };

        // Subuh: sun altitude = -20 deg (Kemenag standard)
        $fajrHA = $hourAngle(-20);
        $fajrHours = $tstNoon - rad2deg($fajrHA) / 15;

        // Sunrise (Terbit): sun altitude = -1.0 deg (refraction + sun semi-diameter)
        $sunriseHA = $hourAngle(-1.0);
        $sunriseHours = $tstNoon - rad2deg($sunriseHA) / 15;

        // Dhuha: sun altitude = +4.5 deg (approx 25 mins after sunrise)
        $dhuhaHA = $hourAngle(4.5);
        $dhuhaHours = $tstNoon - rad2deg($dhuhaHA) / 15;

        // Sunset (Maghrib): sun altitude = -1.0 deg
        $sunsetHA = $hourAngle(-1.0);
        $maghribHours = $tstNoon + rad2deg($sunsetHA) / 15;

        // Isya: sun altitude = -18 deg
        $ishaHA = $hourAngle(-18);
        $ishaHours = $tstNoon + rad2deg($ishaHA) / 15;

        // Ashar (Shafi'i: shadow = 1 + shadow at noon)
        $noonAltitude = M_PI / 2 - abs($latRad - $decl);
        $asrAltRad = atan(1 / (1 + 1 / tan($noonAltitude)));
        $asrHA = $hourAngle(rad2deg($asrAltRad));
        $asrHours = $tstNoon + rad2deg($asrHA) / 15;

        $format = function ($decHours, $offset = 2) {
            $totalMin = (int) round($decHours * 60) + $offset;
            $h = floor($totalMin / 60) % 24;
            $m = $totalMin % 60;
            if ($h < 0) $h += 24;
            if ($m < 0) $m += 60;
            return sprintf('%02d:%02d', $h, $m);
        };

        $subuhStr = $format($fajrHours, (int) $settings->subuh_offset);
        [$subuhH, $subuhM] = explode(':', $subuhStr);
        $imsakTotal = ((int) $subuhH * 60) + (int) $subuhM - 10;
        if ($imsakTotal < 0) $imsakTotal += 1440;
        $imsakStr = sprintf('%02d:%02d', floor($imsakTotal / 60) % 24, $imsakTotal % 60);

        return [
            'imsak' => $imsakStr,
            'subuh' => $subuhStr,
            'terbit' => $format($sunriseHours, -2),
            'dhuha' => $format($dhuhaHours, 2),
            'dzuhur' => $format($tstNoon, (int) $settings->dzuhur_offset),
            'ashar' => $format($asrHours, (int) $settings->ashar_offset),
            'maghrib' => $format($maghribHours, (int) $settings->maghrib_offset),
            'isya' => $format($ishaHours, (int) $settings->isya_offset),
        ];
    }

    /**
     * Determine next prayer from current time
     */
    public function getNextPrayer(array $prayers, ?Carbon $now = null): array
    {
        $now = $now ?? Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i');

        foreach ($prayers as $prayer) {
            if ($currentTime < $prayer['adzan']) {
                $adzanDt = Carbon::createFromTimeString($prayer['adzan'], 'Asia/Jakarta');
                $diffMinutes = $now->diffInMinutes($adzanDt, false);
                $hrs = floor($diffMinutes / 60);
                $mins = $diffMinutes % 60;

                $countdownText = $hrs > 0 ? "{$hrs} jam {$mins} menit lagi" : "{$mins} menit lagi";

                return [
                    'prayer' => $prayer,
                    'countdown_text' => $countdownText,
                    'is_tomorrow' => false,
                    'is_ongoing' => false,
                    'target_time' => $prayer['adzan'],
                ];
            } elseif ($currentTime >= $prayer['adzan'] && $currentTime < $prayer['iqamah']) {
                $iqamahDt = Carbon::createFromTimeString($prayer['iqamah'], 'Asia/Jakarta');
                $diffMinutes = $now->diffInMinutes($iqamahDt, false);

                return [
                    'prayer' => $prayer,
                    'countdown_text' => "Menuju Iqamah ({$diffMinutes} menit lagi)",
                    'is_tomorrow' => false,
                    'is_ongoing' => true,
                    'target_time' => $prayer['iqamah'],
                ];
            }
        }

        // After Isya, next is tomorrow's Subuh
        $subuh = $prayers[0] ?? [
            'key' => 'subuh',
            'name' => 'Subuh',
            'arabic' => 'الفجر',
            'adzan' => '04:09',
            'iqamah' => '04:19',
        ];

        return [
            'prayer' => $subuh,
            'countdown_text' => "Menuju Subuh",
            'is_tomorrow' => true,
            'is_ongoing' => false,
            'target_time' => $subuh['adzan'],
        ];
    }

    /**
     * Get full monthly prayer times schedule for a city, year, and month.
     * Cached for 30 days for maximum performance.
     */
    public function getMonthlyPrayerSchedule(string $cityId, int $year, int $month, ?MasjidSetting $settings = null): array
    {
        $settings = $settings ?? MasjidSetting::getActive();
        $cacheKey = "prayer_monthly_{$cityId}_{$year}_{$month}";

        return Cache::remember($cacheKey, 86400 * 90, function () use ($cityId, $year, $month, $settings) {
            $monthStr = sprintf('%02d', $month);
            $url = "https://api.myquran.com/v2/sholat/jadwal/{$cityId}/{$year}/{$monthStr}";

            try {
                $response = Http::connectTimeout(0.5)->timeout(0.8)->get($url);
                if ($response->successful() && $response->json('status')) {
                    $jadwalList = $response->json('data.jadwal');
                    if (is_array($jadwalList) && count($jadwalList) > 0) {
                        return array_map(function ($item) {
                            $dateObj = isset($item['date']) ? Carbon::parse($item['date']) : Carbon::today();
                            return [
                                'date' => $item['date'] ?? $dateObj->format('Y-m-d'),
                                'day_name' => $dateObj->translatedFormat('l'),
                                'formatted_date' => $dateObj->translatedFormat('d F Y'),
                                'day_number' => (int) $dateObj->day,
                                'imsak' => $item['imsak'] ?? '-',
                                'subuh' => $item['subuh'] ?? '-',
                                'terbit' => $item['terbit'] ?? '-',
                                'dhuha' => $item['dhuha'] ?? '-',
                                'dzuhur' => $item['dzuhur'] ?? '-',
                                'ashar' => $item['ashar'] ?? '-',
                                'maghrib' => $item['maghrib'] ?? '-',
                                'isya' => $item['isya'] ?? '-',
                            ];
                        }, $jadwalList);
                    }
                }
            } catch (\Throwable $e) {
                Log::info("MyQuran monthly API unreachable or timed out for city {$cityId} {$year}-{$monthStr}, using fast hisab: " . $e->getMessage());
            }

            // Fallback calculation for all days of the month (runs in 0.05ms!)
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            $results = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateObj = Carbon::create($year, $month, $d, 12, 0, 0, 'Asia/Jakarta');
                $sched = $this->calculateFallbackSchedule($cityId, $dateObj, $settings);

                $results[] = [
                    'date' => $dateObj->format('Y-m-d'),
                    'day_name' => $dateObj->translatedFormat('l'),
                    'formatted_date' => $dateObj->translatedFormat('d F Y'),
                    'day_number' => $d,
                    'imsak' => $sched['imsak'],
                    'subuh' => $sched['subuh'],
                    'terbit' => $sched['terbit'],
                    'dhuha' => $sched['dhuha'],
                    'dzuhur' => $sched['dzuhur'],
                    'ashar' => $sched['ashar'],
                    'maghrib' => $sched['maghrib'],
                    'isya' => $sched['isya'],
                ];
            }

            return $results;
        });
    }

    /**
     * Approximate Hijri Date (Ummul Qura / Kuwaiti calculation with Indonesian Kemenag offset)
     */
    public function getHijriDate(?Carbon $date = null, int $adjustment = 1): string
    {
        $date = $date ?? Carbon::now('Asia/Jakarta');
        
        $dayNames = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $dayName = $dayNames[$date->dayOfWeek];

        $hijriPart = $this->getHijriDateOnly($date, $adjustment);

        return "{$dayName}, {$date->translatedFormat('d F Y')} • {$hijriPart}";
    }

    /**
     * Get Hijri Date only (e.g. "10 Rabiul Akhir 1448 H")
     */
    public function getHijriDateOnly(?Carbon $date = null, int $adjustment = 1): string
    {
        $date = $date ?? Carbon::now('Asia/Jakarta');

        $hijriMonths = [
            1 => 'Muharram', 2 => 'Safar', 3 => 'Rabiul Awwal', 4 => 'Rabiul Akhir',
            5 => 'Jumadil Awwal', 6 => 'Jumadil Akhir', 7 => 'Rajab', 8 => "Sya'ban",
            9 => 'Ramadhan', 10 => 'Syawwal', 11 => "Dzulqa'dah", 12 => 'Dzulhijjah'
        ];

        // Ummul Qura / Kuwaiti calculation with Indonesian standard calendar offset
        $d = $date->day;
        $m = $date->month;
        $y = $date->year;

        if ($m < 3) {
            $y -= 1;
            $m += 12;
        }

        $a = floor($y / 100);
        $b = 2 - $a + floor($a / 4);
        $jd = floor(365.25 * ($y + 4716)) + floor(30.6001 * ($m + 1)) + $d + $b - 1524 + $adjustment;

        $z = $jd - 1948440 + 10632;
        $n = floor(($z - 1) / 10631);
        $z = $z - 10631 * $n + 354;
        $j = (floor((10985 - $z) / 5316)) * (floor((50 * $z) / 17719)) + (floor($z / 5670)) * (floor((43 * $z) / 15238));
        $z = $z - (floor((30 - $j) / 15)) * (floor((17719 * $j) / 50)) - (floor($j / 16)) * (floor((15238 * $j) / 43)) + 29;
        $hm = floor((24 * $z) / 709);
        $hd = $z - floor((709 * $hm) / 24);
        $hy = 30 * $n + $j - 30;

        $hmName = $hijriMonths[(int) $hm] ?? 'Rabiul Awwal';

        return "{$hd} {$hmName} {$hy} H";
    }

    /**
     * Get Day and Hijri Date (e.g. "Selasa, 10 Rabiul Akhir 1448 H")
     */
    public function getHijriDayAndDate(?Carbon $date = null, int $adjustment = 1): string
    {
        $date = $date ?? Carbon::now('Asia/Jakarta');
        
        $dayNames = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $dayName = $dayNames[$date->dayOfWeek];

        $hijriDateOnly = $this->getHijriDateOnly($date, $adjustment);

        return "{$dayName}, {$hijriDateOnly}";
    }
}
