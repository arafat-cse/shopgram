<?php
namespace App\Services;

use App\Models\AdminActivityLog;

class ActivityLogService
{
    public static function log(
        string  $action,
        string  $description,
        ?string $modelType = null,
        ?int    $modelId   = null,
        ?array  $meta      = null
    ): void {
        if (!auth()->check()) return;

        AdminActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'meta'        => $meta,
            'ip_address'  => request()->ip(),
            'created_at'  => now(),
        ]);
    }

    public static function created(string $modelType, int $modelId, string $description, array $meta = []): void
    {
        static::log('created', $description, $modelType, $modelId, $meta ?: null);
    }

    public static function updated(string $modelType, int $modelId, string $description, array $meta = []): void
    {
        static::log('updated', $description, $modelType, $modelId, $meta ?: null);
    }

    public static function deleted(string $modelType, int $modelId, string $description, array $meta = []): void
    {
        static::log('deleted', $description, $modelType, $modelId, $meta ?: null);
    }

    public static function statusChanged(string $modelType, int $modelId, string $description, array $meta = []): void
    {
        static::log('status_changed', $description, $modelType, $modelId, $meta ?: null);
    }
}
