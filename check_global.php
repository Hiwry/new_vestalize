<?php
echo "=== GLOBAL PERSONALIZATIONS ===\n";
$ops = \App\Models\ProductOption::withoutGlobalScopes()
    ->whereNull('tenant_id')
    ->where('type', 'personalizacao')
    ->get();

foreach ($ops as $o) {
    echo "ID: {$o->id} | Active: " . ($o->active ? "YES" : "NO") . " | Name: {$o->name}\n";
}
