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
