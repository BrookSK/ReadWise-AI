<?php
// Configurações básicas
define('APP_NAME', 'ReadWise AI');

define('BASE_URL', (function(){
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    // Não force '/public' no BASE_URL; permita rewrite na raiz
    $base = str_replace('/public', '', $base);
    $base = rtrim($base, '/');
    return $scheme . '://' . $host . ($base ? $base . '/' : '/');
})());

// DB (preencher depois)
define('DB_HOST', 'localhost');
define('DB_NAME', 'readwise_ai');
define('DB_USER', 'root');
define('DB_PASS', '');

// Diretórios
define('ASSETS_URL', BASE_URL . 'assets/');
