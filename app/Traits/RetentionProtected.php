<?php

namespace App\Traits;

trait RetentionProtected
{
    public static function bootRetentionProtected()
    {
        static::deleting(function ($model) {
            if ($model->retention_until && $model->retention_until->isFuture()) {
                throw new \Exception('Este registro não pode ser excluído pois está dentro do período de retenção obrigatório.');
            }
        });
    }

    public function scopeRetained($query)
    {
        return $query->whereDate('retention_until', '>=', now());
    }

    public function scopeExpiredRetention($query)
    {
        return $query->whereDate('retention_until', '<', now());
    }
}
