<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            addProps: $this->addProps(),
            withRelations: ['roles:id,name'],
        ));
    }
    protected function addProps(): array
    {
        $roles = Role::all();
        $branches = Branch::active()->get();
        return [
            'roles' => $roles,
            'branches' => $branches
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
        $model->fill($validatedData);
        $model->save();

        // Assign roles if provided
        if (isset($validatedData['roles'])) {
            $model->roles()->sync($validatedData['roles']);
        }
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
        $res = $model->update($validatedData);

        // Update roles if provided
        if (isset($validatedData['roles'])) {
            $model->roles()->sync($validatedData['roles']);
        }

        return to_route(str_replace('_', '-', $this->resource) . '.index')->with('success', 'Updated successfully');
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
            ->update([
                'status' => 'closed',
                'closed_at' => now(),
                'note' => DB::raw("CONCAT(IFNULL(note, ''), ' [Auto-closed on branch switch]')"),
            ]);

        // Optional: permission check
        // abort_if(!$user->branches()->where('id', $request->branch_id)->exists(), 403);

        $user->update([
            'branch_id' => $branchId,
        ]);

        // Also store in session (useful for queries)
        session(['current_branch_id' => $branchId]);

        return back();
    }
}
