<?php
// Quick redirect to the public front controller so requests to the project root
// are forwarded to the app without changing Apache configuration.
// This is a safe, temporary convenience for development only.

// Determine base path — preserve any path info after the project root
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// If the request already targets /public, let it through
if (strpos($path, 'public') === 0) {
    // Do nothing — let Apache serve files under public directly
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: /' . ltrim($path, '/'));
    exit;
}

// Otherwise redirect to public with the original path appended
$target = '/' . trim('public/' . $path, '/');
if ($target === '/') $target = '/public/';
header('Location: ' . $target);
exit;
