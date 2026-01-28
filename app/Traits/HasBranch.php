<?php

namespace App\Traits;

trait HasBranch
{
    protected static function bootHasBranch()
    {
        static::addGlobalScope('branch', function ($builder) {

            if (app()->runningInConsole()) {
                return;
            }

            if (
                auth()->check() &&
                session()->has('current_branch_id')
            ) {
                $builder->where(
                    $builder->getModel()->getTable() . '.branch_id',
                    session('current_branch_id')
                );
            }
        });
    }
}
