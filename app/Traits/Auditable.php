<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAudit('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $old = [];
            $new = [];
            foreach ($model->getDirty() as $key => $value) {
                if (in_array($key, ['password', 'remember_token', 'portal_token'])) continue;
                $old[$key] = $model->getOriginal($key);
                $new[$key] = $value;
            }
            if (!empty($new)) {
                self::logAudit('updated', $model, $old, $new);
            }
        });

        static::deleted(function ($model) {
            self::logAudit('deleted', $model, $model->getAttributes(), null);
        });
    }

    protected static function logAudit($action, $model, $oldValues, $newValues)
    {
        $user = auth()->user();
        $modelName = class_basename($model);
        $identifier = $model->name ?? $model->invoice_number ?? $model->patient_id ?? $model->claim_number ?? $model->report_number ?? $model->id;

        $description = match ($action) {
            'created' => "{$modelName} \"{$identifier}\" was created",
            'updated' => "{$modelName} \"{$identifier}\" was updated",
            'deleted' => "{$modelName} \"{$identifier}\" was deleted",
            default => "{$action} {$modelName}",
        };

        AuditLog::create([
            'user_id' => $user?->id,
            'branch_id' => session('current_branch_id'),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
