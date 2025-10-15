<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

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
            exportClass: UserExport::class,

        ));
    }

    public function destroy($id)
    {
        if( auth()->id() == $id) {
            throw new \Exception("You can't delete yourself");
        }
        $model = $this->modelClass::findOrFail($id);
        // if ($model->photo && Storage::exists($model->photo)) {
        //     Storage::delete($model->photo);
        // }
        $model->delete();

        return to_route($this->resource.'.index')->with('success', 'Deleted successfully');
    }
}
