<?php

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$fullPath = __DIR__ . $path;

if ($path !== '/' && file_exists($fullPath) && !is_dir($fullPath)) {
    return false;
}

if ($path === '/' || $path === '/index.php') {
    require __DIR__ . '/index.php';
    return true;
}

if (str_starts_with($path, '/api/')) {
    require __DIR__ . '/api/index.php';
    return true;
}

if (file_exists(__DIR__ . $path . '.php')) {
    require __DIR__ . $path . '.php';
    return true;
}

http_response_code(404);
echo 'Not Found';
