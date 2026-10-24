<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'users',
            modelClass: User::class,
            storeRequestClass: UserStoreRequest::class,
            updateRequestClass: UserUpdateRequest::class,
            componentPath: 'Admin/Users/Index',
            searchColumns: ['name', 'email'],
            withRelations: ['roles:id,name'],
        ));
    }

    protected function addProps(): array
    {
        $roles = Role::all();
        $branches = Branch::active()->get();

        return [
            'roles' => $roles,
            'branches' => $branches,
        ];
    }

    public function store(Request $request)
    {

        $this->ensureModelClass();
        $validatedData = app($this->storeRequestClass)->validated();
        if ($request->file('photo')) {
            $validatedData['photo'] = $request->file('photo')->store($this->resource);
        }
        $model = new $this->modelClass;
        $validatedData['branch_id'] = $validatedData['branch_id'] ?? 1;
        $model->fill(collect($validatedData)->except('roles')->all());
        $model->email_verified_at = now(); // created by an admin
        $model->save();

        if (isset($validatedData['roles'])) {
            $this->syncRoles($model, $validatedData['roles']);
        }

        return to_route('users.index')->with('success', __('Created successfully'));
    }

    /**
     * Only Super Admins may grant or revoke the Super Admin role, and nobody may change their own roles
     * (prevents privilege escalation by anyone who only has "User Update").
     */
    protected function syncRoles(User $user, array $roleIds): void
    {
        $actor = Auth::user();
        $superAdminId = Role::where('name', 'Super Admin')->value('id');
        $actorIsSuper = $actor->hasRole('Super Admin');

        if ($user->is($actor) && ! $actorIsSuper) {
            abort(403, __('You cannot change your own roles.'));
        }

        $requested = collect($roleIds)->map(fn ($id) => (int) $id);
        $hadSuper = $user->roles()->whereKey($superAdminId)->exists();
        if (! $actorIsSuper && ($requested->contains($superAdminId) || $hadSuper)) {
            abort(403, __('Only a Super Admin can assign or remove the Super Admin role.'));
        }
        if ($hadSuper && ! $requested->contains($superAdminId)
            && Role::findById($superAdminId)->users()->count() <= 1) {
            abort(422, __('At least one Super Admin must remain.'));
        }

        $user->roles()->sync($requested->all());
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        cache()->forever('auth_version', time());
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        abort_if($user->is(Auth::user()), 422, __('You cannot delete your own account here.'));
        abort_if($user->hasRole('Super Admin') && ! Auth::user()->hasRole('Super Admin'), 403);
        if ($user->hasRole('Super Admin') && Role::findByName('Super Admin')->users()->count() <= 1) {
            abort(422, __('At least one Super Admin must remain.'));
        }
        $user->delete();

        return to_route('users.index')->with('success', __('Deleted successfully'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = app($this->updateRequestClass)->validated();
        $model = $this->modelClass::findOrFail($id);
        if ($request->file('photo')) {
            $validatedData['photo'] = $request->file('photo')->store($this->resource);
            if ($model->photo && Storage::fileExists($model->photo)) {
                Storage::delete($model->photo);
            }
        }
        abort_if($model->hasRole('Super Admin') && ! Auth::user()->hasRole('Super Admin'), 403);

        if (empty($validatedData['password'])) {
            unset($validatedData['password']);
        }
        $model->update(collect($validatedData)->except('roles')->all());

        if (array_key_exists('roles', $validatedData)) {
            $this->syncRoles($model, $validatedData['roles'] ?? []);
        }

        return to_route(str_replace('_', '-', $this->resource).'.index')->with('success', __('Updated successfully'));
    }

    public function switch(Request $request)
    {
        $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $user = Auth::user();
        $branchId = $request->branch_id;

        // Automatically close any open POS session for this user
        \App\Models\PosSession::where('user_id', $user->id)
            ->where('status', 'open')
            ->get()
            ->each(fn ($session) => $session->update([
                'status' => 'closed',
                'closed_at' => now(),
                'note' => trim(($session->note ?? '').' [Auto-closed on branch switch]'),
            ]));

        $user->update([
            'branch_id' => $branchId,
        ]);

        // Also store in session (useful for queries)
        session(['current_branch_id' => $branchId]);

        return back();
    }
}
