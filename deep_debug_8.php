<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenantId = 8;
echo "--- TermsCondition for Tenant $tenantId ---\n";
$terms = \App\Models\TermsCondition::withoutGlobalScopes()->where('tenant_id', $tenantId)->get();
echo "Count: " . $terms->count() . "\n";
foreach ($terms as $t) {
    echo "ID: {$t->id}, Active: " . ($t->active ? 'Y' : 'N') . ", PType: " . ($t->personalization_type ?? 'NULL') . ", FType: " . ($t->fabric_type_id ?? 'NULL') . ", Len: " . strlen($t->content ?? '') . "\n";
}

echo "\n--- CompanySetting for Tenant $tenantId ---\n";
$settings = \App\Models\CompanySetting::where('tenant_id', $tenantId)->get();
echo "Count: " . $settings->count() . "\n";
foreach ($settings as $s) {
    echo "ID: {$s->id}, Store: " . ($s->store_id ?? 'NULL') . ", Terms Len: " . strlen($s->terms_conditions ?? '') . "\n";
}
