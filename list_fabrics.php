<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tecidos = App\Models\Tecido::where('active', true)->get(['id', 'name']);
foreach ($tecidos as $t) {
    echo $t->id . ':' . $t->name . PHP_EOL;
}
