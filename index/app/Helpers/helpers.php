<?php

if (!function_exists('format_phone_for_wa')) {
    /**
     * Format raw phone number into standard international WhatsApp MSISDN format (e.g. 628123456789).
     *
     * @param string|null $phone
     * @return string
     */
    function format_phone_for_wa(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Keep only digits
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (empty($digits)) {
            return '';
        }

        // Convert Indonesian domestic prefixes (08xx -> 628xx, 8xx -> 628xx)
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}

if (!function_exists('wa_link')) {
    /**
     * Generate standard WhatsApp chat link (https://wa.me/628xxx?text=...).
     *
     * @param string|null $phone
     * @param string|null $message
     * @return string
     */
    function wa_link(?string $phone, ?string $message = null): string
    {
        $clean = format_phone_for_wa($phone);
        if (empty($clean)) {
            return '#';
        }

        $url = 'https://wa.me/' . $clean;
        if (!empty($message)) {
            $url .= '?text=' . urlencode($message);
        }

        return $url;
    }
}

if (!function_exists('format_short_month_id')) {
    /**
     * Dapatkan singkatan 3 huruf nama bulan dalam Bahasa Indonesia (standar EYD/KBBI).
     * Contoh: Jan, Feb, Mar, Apr, Mei, Jun, Jul, Agu, Sep, Okt, Nov, Des.
     *
     * @param int|\Carbon\CarbonInterface $month
     * @return string
     */
    function format_short_month_id($month): string
    {
        $m = $month instanceof \Carbon\CarbonInterface ? (int) $month->format('n') : (int) $month;
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
        return $months[$m] ?? '';
    }
}

if (!function_exists('format_date_id_short')) {
    /**
     * Format tanggal lengkap dengan hari, tanggal 2 digit, singkatan bulan 3 huruf Indonesia, dan tahun.
     * Contoh: "Senin, 02 Nov 2026"
     *
     * @param \Carbon\CarbonInterface|string|null $date
     * @return string
     */
    function format_date_id_short($date): string
    {
        if (empty($date)) {
            return '';
        }
        if (!$date instanceof \Carbon\CarbonInterface) {
            $date = \Carbon\Carbon::parse($date);
        }
        $dayName = $date->locale('id')->translatedFormat('l');
        $dayNum = $date->format('d');
        $monthShort = format_short_month_id($date);
        $year = $date->format('Y');

        return "{$dayName}, {$dayNum} {$monthShort} {$year}";
    }
}
