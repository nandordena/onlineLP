<?php
$env = getenv('ENV') ?: 'PROD'; // valor por defecto

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

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}