<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            AuditLog::log($model, 'created', [], $model->toArray());
        });

        static::updated(function ($model) {
            $old = $model->getOriginal();
            $new = $model->getChanges();
            AuditLog::log($model, 'updated', $old, $new);
        });

        static::deleted(function ($model) {
            AuditLog::log($model, 'deleted', $model->toArray(), []);
        });
    }
}
