<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Caminhos possíveis para o Laravel
$candidatePaths = [
    __DIR__ . '/../laravel',
    __DIR__ . '/../backend',
    __DIR__ . '/..',
    '/home2/dd173158/laravel',
];

$laravelPath = null;
foreach ($candidatePaths as $path) {
    if (is_file($path . '/vendor/autoload.php')) {
        $laravelPath = $path;
        break;
    }
}

if (!$laravelPath) {
    http_response_code(500);
    echo 'Erro: Laravel não encontrado.<br>Caminhos testados:<br>';
    foreach ($candidatePaths as $path) {
        echo '- ' . $path . '<br>';
    }
    exit(1);
}

if (file_exists($maintenance = $laravelPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravelPath . '/vendor/autoload.php';
$app = require_once $laravelPath . '/bootstrap/app.php';
$app->handleRequest(Request::capture());
