<?php
echo "=== PRODUCT OPTIONS SUMMARY ===\n";
$summary = \App\Models\ProductOption::withoutGlobalScopes()
    ->select('tenant_id', 'type', \DB::raw('count(*) as total'))
    ->groupBy('tenant_id', 'type')
    ->get();

foreach ($summary as $s) {
    echo "Tenant: " . ($s->tenant_id ?? "NULL") . " | Type: " . str_pad($s->type, 15) . " | Count: {$s->total}\n";
}
