<?php
declare(strict_types=1);

/**
 * Autoloader
 * Charge automatiquement les classes selon leur namespace.
 */
spl_autoload_register(function (string $class): void {

    $namespaceMap = [
        'App\\Controllers\\' => ROOT_PATH . '/app/Controllers/',
        'App\\Models\\'      => ROOT_PATH . '/app/Models/',
        'Core\\'             => ROOT_PATH . '/core/',
    ];

    foreach ($namespaceMap as $namespace => $directory) {
        if (str_starts_with($class, $namespace)) {
            $relative = substr($class, strlen($namespace));
            $file     = $directory . str_replace('\\', '/', $relative) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});
