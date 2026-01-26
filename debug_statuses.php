<?php

use App\Models\Status;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$statuses = Status::all();

foreach ($statuses as $status) {
    echo "ID: {$status->id} | Name: {$status->name} | Position: {$status->position} | Slug: {$status->slug}\n";
}
