<?php
/**
 * Router for PHP's built-in development server
 * Handles clean URLs (without .php extension)
 *
 * Usage: php -S localhost:8080 -t /path/to/site /path/to/site/router.php
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Remove trailing slash (except for root)
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// Serve static files directly
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|pdf|doc|docx)$/i', $path)) {
    return false;
}

// Root path - serve index.php
if ($path === '/') {
    return false;
}

// If the exact file exists (e.g., .php file requested directly), serve it
if (is_file(__DIR__ . $path)) {
    return false;
}

// Try adding .php extension
$phpFile = __DIR__ . $path . '.php';
if (file_exists($phpFile)) {
    chdir(__DIR__);
    require $phpFile;
    exit;
}

// 404 - file not found
http_response_code(404);
echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1><p>The page '$path' was not found.</p></body></html>";
