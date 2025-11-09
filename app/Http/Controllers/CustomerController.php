<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
// use App\Exports\CustomerExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Currency;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'customers',
            modelClass: Customer::class,
            storeRequestClass: CustomerStoreRequest::class,
            updateRequestClass: CustomerUpdateRequest::class,
            componentPath: 'Customers/Index',
            searchColumns: [],
            // exportClass: CustomerExport::class,
            // withRelations: [],
            addProps: $this->addProps(),
        ));
    }

    protected function addProps(): array
    {
        $currencies = Currency::select('id', 'title')->get();

        return [
            'currencies' => $currencies,
        ];
    }

    public function store(Request $request)
    {

        $this->ensureModelClass();
        $validatedData = app($this->storeRequestClass)->validated();
        if ($request->file('photo')) {
            $validatedData['photo'] = $request->file('photo')->store($this->resource);
        }
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store($this->resource);
        }
        $model = new $this->modelClass;
        $model->fill($validatedData);
        $model->created_by = auth()->user()->id;
        $model->save();

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
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store($this->resource);
            if ($model->file && Storage::fileExists($model->file)) {
                Storage::delete($model->file);
            }
        }
        $res = $model->update($validatedData);


        return to_route(str_replace('_', '-', $this->resource) . '.index')->with('success', 'Updated successfully');
    }

}
