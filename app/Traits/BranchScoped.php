<?php

namespace App\Traits;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Builder;

trait BranchScoped
{
    /**
     * Column name on models using this trait that contains branch id.
     * You can override this in any model that uses the trait:
     *   protected static $branchScopeColumn = 'shop_id';
     */
    protected static $branchScopeColumn = 'branch_id';

    /**
     * Whether to automatically add the global scope when the model boots.
     * Set to false in a specific model to disable automatic registration.
     *
     * protected static $applyBranchScope = true;
     */
    protected static $applyBranchScope = true;

    /**
     * Boot the trait and add the global branch scope conditionally.
     */
    public static function bootBranchScoped()
    {
        // Allow per-model opt-out
        if (property_exists(static::class, 'applyBranchScope') && static::$applyBranchScope === false) {
            return;
        }

        // New BranchScope instance
        static::addGlobalScope(new BranchScope());
    }

    /**
     * Local scope helper — apply branch filtering for the current auth user
     * if you prefer to call it manually:
     *    Model::query()->forCurrentBranch()->get();
     */
    public function scopeForCurrentBranch(Builder $query)
    {
        // Reuse BranchScope logic by instantiating and applying
        (new BranchScope())->apply($query, $this);
        return $query;
    }

    /**
     * Helper to get a query without the branch global scope:
     *    Model::withoutBranchScope()->get();
     */
    public static function withoutBranchScope()
    {
        return (new static)->newQueryWithoutScope(BranchScope::class);
    }

    /**
     * Helper to temporarily disable scope on an Eloquent query instance:
     *    Model::query()->withoutBranchScope()->where(...);
     *
     * (alias for convenience)
     */
    public static function withoutGlobalBranchScope()
    {
        return static::withoutBranchScope();
    }
}
