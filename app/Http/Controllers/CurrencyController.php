<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Traits\HasCrud;
// use App\Exports\CurrencyExport;
use App\Models\Currency;
use App\Utils\CrudConfig;
use Illuminate\Http\Request;
use App\Http\Requests\CurrencyStoreRequest;
use App\Http\Requests\CurrencyUpdateRequest;

class CurrencyController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'currencies',
            modelClass: Currency::class,
            storeRequestClass: CurrencyStoreRequest::class,
            updateRequestClass: CurrencyUpdateRequest::class,
            componentPath: 'Currencies/Index',
            searchColumns: [],
            // exportClass: CurrencyExport::class,
            withRelations: [],
        ));
    }


    public function index(Request $request)
    {
        $this->ensureModelClass();

        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = $this->modelClass::query();

        if (! empty($this->withRelations)) {
            $query->with($this->withRelations);
        }

        $query->when($search, function ($query, $search) {
            if (isset($this->searchColumns) && ! empty($this->searchColumns)) {
                $query->where(function ($query) use ($search) {
                    foreach ($this->searchColumns as $column) {
                        $query->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }
        });

        if ($request->has('trashed')) {
            $query->when($request->trashed, fn($query) => $query->onlyTrashed());
        }

        $query = $this->modifyQuery($query);
        $items = $query->latest()->paginate($perPage);

        $dataArray = [
            'items' => $items,
            'filters' => ['search' => $search],
            'config' => $this->makeConfig(),
            ...$this->addProps(),
        ];

        // dd($dataArray);

        return Inertia::render($this->componentPath, $dataArray);
    }
}
