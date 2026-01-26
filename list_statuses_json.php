<?php

use App\Models\Status;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$statuses = Status::all()->map(function($s) {
    return [
        'id' => $s->id,
        'name' => $s->name,
        'slug' => $s->slug,
        'position' => $s->position,
    ];
});

echo json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
