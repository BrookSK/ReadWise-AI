<?php
// Autoloader simples PSR-4 like
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';
    $class = str_replace('\\', '/', $class);
    $paths = [
        $baseDir . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/../config/config.php';

// Sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
