@php
    $tenant = auth()->user()?->tenant;
    $logoUrl = asset('vestalize.svg');
    if ($tenant && $tenant->logo_path) {
        if (str_starts_with($tenant->logo_path, 'http')) {
            $logoUrl = $tenant->logo_path;
        } elseif (file_exists(public_path($tenant->logo_path))) {
            $logoUrl = asset($tenant->logo_path);
        } elseif (file_exists(public_path('storage/' . $tenant->logo_path))) {
            $logoUrl = asset('storage/' . $tenant->logo_path);
        }
    }
@endphp
<img src="{{ $logoUrl }}" alt="{{ $tenant->name ?? 'Logo' }}" {{ $attributes }}>
