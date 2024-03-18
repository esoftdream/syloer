<?php

/**
 * Mengambil nama domain dari URL yang diberikan atau domain permintaan saat ini.
 *
 * @param string|null $url              URL untuk mengekstrak nama domain. Jika tidak diberikan, domain permintaan saat ini akan digunakan.
 * @param bool        $only_main_domain Jika diatur sebagai true, hanya nama domain utama yang akan dikembalikan, tanpa subdomain.
 *
 * @return string|null Nama domain yang diekstrak dari URL, atau null jika URL tidak valid.
 */
function getDomainName($url = null, bool $only_main_domain = false)
{
    // Jika URL tidak diberikan, gunakan domain saat ini
    if (! $url) {
        $host = gethostname();

        if (! empty($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        }

        $urlParsed = parse_url($host);

        if (isset($urlParsed['path'])) {
            return $urlParsed['path'];
        } else {
            return $urlParsed['host'];
        }
    }

    // Parse URL untuk menangani berbagai format
    $urlParsed = parse_url($url);

    // Jika komponen host tidak diatur, URL tidak valid
    if (! isset($urlParsed['host'])) {
        log_message('warning', 'URL tidak valid');

        return null;
    }

    if ($only_main_domain) {
        $domain = $urlParsed['host'] ?? '';

        // Mencocokkan domain yang mengandung 2 kata contoh co.id, co.uk, dll
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }

        log_message('info', 'Domain utama tidak ditemukan');

        return null;
    }

    return $urlParsed['host'];
}
