<?php

/**
 * Vercel serverless entry point.
 *
 * The deployment filesystem is read-only except for /tmp, so Laravel's
 * storage directory and bootstrap caches are redirected there before
 * handing the request to the regular front controller.
 */

$defaults = [
    'LARAVEL_STORAGE_PATH' => '/tmp/storage',
    'APP_CONFIG_CACHE' => '/tmp/bootstrap/config.php',
    'APP_EVENTS_CACHE' => '/tmp/bootstrap/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/bootstrap/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/services.php',
    'LOG_CHANNEL' => 'stderr',
    'QUEUE_CONNECTION' => 'sync',
    'RECEIPT_DISK' => 's3',
    'MYSQL_ATTR_SSL_CA' => '/etc/pki/tls/certs/ca-bundle.crt',
];

foreach ($defaults as $key => $value) {
    if (getenv($key) === false) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $_SERVER[$key] = $value;
    }
}

foreach (['framework/views', 'framework/cache/data', 'framework/sessions', 'logs', 'fonts'] as $dir) {
    $path = $_ENV['LARAVEL_STORAGE_PATH'].'/'.$dir;
    is_dir($path) || mkdir($path, 0755, true);
}
is_dir('/tmp/bootstrap') || mkdir('/tmp/bootstrap', 0755, true);

require __DIR__.'/../public/index.php';
