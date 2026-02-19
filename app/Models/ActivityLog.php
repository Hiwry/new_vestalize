<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
        'event',
        'tenant_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Helper para criar logs programaticamente similar ao spatie/activitylog
     */
    public static function log(string $description, ?Model $subject = null, ?array $properties = null, string $event = 'custom')
    {
        return self::create([
            'description' => $description,
            'subject_id' => $subject?->id,
            'subject_type' => $subject ? get_class($subject) : null,
            'causer_id' => auth()->id(),
            'causer_type' => auth()->check() ? get_class(auth()->user()) : null,
            'properties' => $properties,
            'event' => $event,
            'tenant_id' => auth()->user()->tenant_id ?? session('current_tenant_id'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
