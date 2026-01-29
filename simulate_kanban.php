<?php
// Simulate KanbanController logic

$user = \App\Models\User::where('name', 'like', '%Thiago%')->first();
echo "User: " . ($user ? $user->name : 'Auth::user() logic needed') . "\n";

// Mimic active tenant logic
$activeTenantId = $user ? $user->tenant_id : null;
if ($activeTenantId === null) {
    // Session mock? skip.
    // Fallback logic
    $firstStore = \App\Models\Store::first();
    echo "First Store: " . ($firstStore ? $firstStore->id : 'None') . " (Tenant: " . ($firstStore ? $firstStore->tenant_id : 'n/a') . ")\n";
    $activeTenantId = $firstStore ? $firstStore->tenant_id : 1;
}

echo "Active Tenant ID: $activeTenantId\n";

// Fetch statuses
$statuses = \App\Models\Status::where('tenant_id', $activeTenantId)->orderBy('position')->get();
echo "Fetched Statuses Count: " . $statuses->count() . "\n";
echo "Status Names in DB:\n";
foreach($statuses as $s) {
    echo " - " . $s->name . "\n";
}

// Filter Logic
$defaultStatusNames = [
    'Pendente',
    'Quando não assina',
    'Inicio',
    'Fila Corte',
    'Cortado',
    'Costura',
    'Costurar Novamente',
    'Personalização',
    'Limpeza',
    'Concluído'
];

$normalizedDefaults = array_map(function($name) {
    return \Illuminate\Support\Str::slug($name);
}, $defaultStatusNames);

$selectedColumns = $statuses->filter(function($status) use ($normalizedDefaults, $defaultStatusNames) {
    // Tenta match exato primeiro
    if (in_array($status->name, $defaultStatusNames)) return true;
    
    // Tenta match normalizado
    return in_array(\Illuminate\Support\Str::slug($status->name), $normalizedDefaults);
})->pluck('id')->toArray();

echo "Selected Columns Count: " . count($selectedColumns) . "\n";
echo "Selected IDs: " . implode(', ', $selectedColumns) . "\n";
