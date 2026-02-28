<?php
$env = getenv('ENV') ?: 'PROD';

// Configuración de errores: no mostrar warnings / deprecated en pantalla
if ($env === 'DEV') {
    // En desarrollo: registrar todos menos deprecated/notices/warnings, pero no mostrarlos
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
}
$origin = $_SERVER['HTTP_ORIGIN'] ?? null;
$allowedOrigins = array_filter([
    getenv('USER_MANAGER'),
    getenv('CONTROL_ROOM'),
    getenv('VIEW_COMPONENTS'),
]);
if($origin && in_array($origin, $allowedOrigins, true)){
    header("Access-Control-Allow-Origin: {$origin}");
    header("Access-Control-Allow-Credentials: true");
} elseif (!$origin) {
    $defaultOrigin = '*';
    header("Access-Control-Allow-Origin: {$defaultOrigin}");
    header("Access-Control-Allow-Credentials: true");
}else{
    http_response_code(404);
    echo '404 Not Allowed';
    die();
}

if ($env === 'DEV') {
    // Deshabilitar opcache en tiempo de ejecución
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    ini_set('opcache.enable', 0);
    ini_set('opcache.enable_cli', 0);
    
    // Opcional: deshabilitar caché de otros sistemas
    ini_set('realpath_cache_size', '0'); // cache de rutas
    ini_set('realpath_cache_ttl', '0');
}