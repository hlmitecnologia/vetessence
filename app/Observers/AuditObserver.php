<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    protected $userId = null;
    protected $ipAddress = null;

    public function __construct()
    {
        if (auth()->check()) {
            $this->userId = auth()->id();
        }
        $this->ipAddress = Request::ip();
    }

    public function created(Model $model)
    {
        $this->log($model, 'created', null, $this->getAttributes($model));
    }

    public function updated(Model $model)
    {
        $oldValues = [];
        $newValues = [];

        foreach ($model->getChanges() as $key => $value) {
            if ($key !== 'updated_at') {
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $value;
            }
        }

        if (!empty($oldValues)) {
            $this->log($model, 'updated', $oldValues, $newValues);
        }
    }

    public function deleted(Model $model)
    {
        $this->log($model, 'deleted', $this->getAttributes($model), null);
    }

    protected function log(Model $model, string $action, $oldValues = null, $newValues = null)
    {
        $modelName = class_basename($model);
        
        if (in_array($modelName, ['AuditLog'])) {
            return;
        }

        AuditLog::create([
            'user_id' => $this->userId,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'changes' => ['old' => $oldValues, 'new' => $newValues],
            'ip_address' => $this->ipAddress,
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    protected function getAttributes(Model $model)
    {
        $attributes = $model->getAttributes();
        unset($attributes['password'], $attributes['remember_token']);
        return $attributes;
    }
}
