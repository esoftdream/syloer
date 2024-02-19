<?php

/**
 * Retrieve the domain name from a given URL or the current request's domain.
 *
 * @param string|null $url The URL to extract the domain from. If not provided, the current request's domain will be used.
 *
 * @return string|null The domain name extracted from the URL, or null if the URL is invalid.
 */
function getDomainName($url = null)
{
    // If URL is not provided, use the current domain
    if (! $url) {
        $host = 'localhost';

        if (! empty($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
        $port     = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] !== '8080' ? ':' . $_SERVER['SERVER_PORT'] : '';

        return str_replace('\\', '/', $protocol . $host . $port);
    }

    // Parse the URL to handle various formats
    $urlComponents = parse_url($url);

    // If the host component is not set, the URL is invalid
    if (! isset($urlComponents['host'])) {
        log_message('warning', 'URL is invalid');

        return null;
    }

    // Extract the host from the URL components
    $host = $urlComponents['host'];

    // Remove subdomains if necessary (optional)
    if (strpos($host, '.') !== false) {
        // Split the host by dot and extract the last part (top-level domain)
        $hostParts = explode('.', $host);
        $domain    = end($hostParts);
    } else {
        // If no subdomains, the domain is the same as the host
        $domain = $host;
    }

    return $domain;
}
