<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * This scope expects models to have a branch column name defined by:
     *    protected static $branchScopeColumn = 'branch_id';
     *
     * The "skip" logic is delegated to the trait via a method on user:
     *    - If Auth::user() is missing or has admin flags, scope may be skipped.
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();

        // if no auth, do not apply (caller may want to enforce differently for CLI jobs)
        if (! $user) {
            return;
        }

        // allow model to define the branch column name (defaults to 'branch_id')
        $branchColumn = $this->getBranchColumn($model);

        // If user should bypass branch scoping (admin/super admin), do nothing
        if ($this->userSkipsScope($user)) {
            return;
        }

        // If user has a branches() relation (many branches)
        if (method_exists($user, 'branches')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();

            if (empty($branchIds)) {
                // Safe default: user has no branches -> no rows
                $builder->whereRaw('0 = 1');
            } else {
                $builder->whereIn($model->getTable() . '.' . $branchColumn, $branchIds);
            }

            return;
        }

        // Otherwise check single branch_id on user
        if (isset($user->branch_id) && $user->branch_id !== null) {
            $builder->where($model->getTable() . '.' . $branchColumn, $user->branch_id);
            return;
        }

        // no branch info and not admin -> no rows
        $builder->whereRaw('0 = 1');
    }

    protected function getBranchColumn(Model $model): string
    {
        return $model::$branchScopeColumn ?? 'branch_id';
    }

    /**
     * Decide whether the authenticated user should bypass the branch scope.
     * Adjust this logic to fit your app (roles, gates, flags).
     */
    protected function userSkipsScope($user): bool
    {
        // Example checks — replace with your app's checks (roles, abilities, flags)
        if (property_exists($user, 'is_super_admin') && $user->is_super_admin) {
            return true;
        }

        if (property_exists($user, 'is_admin') && $user->is_admin) {
            return true;
        }

        // Example: use a method if your User has it
        if (method_exists($user, 'isGlobalAdmin') && $user->isGlobalAdmin()) {
            return true;
        }

        // Example: use Gate ability (requires use Illuminate\Support\Facades\Gate;)
        // if (Gate::allows('view-any-branchless')) { return true; }

        return false;
    }
}
