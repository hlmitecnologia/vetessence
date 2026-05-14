<?php

namespace App\Scopes;

use App\Services\BranchContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (BranchContext::hasBranch()) {
            $builder->where($model->getTable() . '.branch_id', BranchContext::get());
        }
    }
}
