<?php
// Autoloader simples PSR-4 like
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';
    $prefixes = [
        'Core\\' => 'core/',
        'App\\'  => 'app/',
    ];
    foreach ($prefixes as $prefix => $dir) {
        if (strncmp($class, $prefix, strlen($prefix)) === 0) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . $dir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) { require_once $file; return; }
        }
    }
    // Fallback genérico
    $fallback = $baseDir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($fallback)) { require_once $fallback; }
});

require_once __DIR__ . '/../config/config.php';

// Sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
