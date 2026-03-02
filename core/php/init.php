<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$origin = $_SERVER['HTTP_ORIGIN'] ?? null;
$allowedOrigins = array_filter([
    getenv('USER_MANAGER'),
    getenv('CONTROL_ROOM'),
    getenv('VIEW_COMPONENTS'),
]);
if (!($origin && in_array($origin, $allowedOrigins, true)) && $origin) {
    http_response_code(404);
    echo '404 Not Allowed';
    die();
}

$env = getenv('ENV') ?: 'PROD';

if ($env === 'DEV') {
    //Error reporting
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);

    //Opcache reset
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    ini_set('opcache.enable', 0);
    ini_set('opcache.enable_cli', 0);
    
    ini_set('realpath_cache_size', '0');
    ini_set('realpath_cache_ttl', '0');
}