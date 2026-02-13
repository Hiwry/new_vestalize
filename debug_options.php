<?php
use App\Models\ProductOption;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$colors = ProductOption::where('type', 'cor')
    ->with('parents')
    ->limit(20)
    ->get()
    ->map(function($o) {
        return [
            'id' => $o->id,
            'name' => $o->name,
            'parents' => $o->parents->map(function($p) {
                return ['id' => $p->id, 'name' => $p->name, 'type' => $p->type];
            })->toArray()
        ];
    });

echo json_encode($colors, JSON_PRETTY_PRINT);
