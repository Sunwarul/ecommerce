<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyStoreRequest;
use App\Http\Requests\CurrencyUpdateRequest;
// use App\Exports\CurrencyExport;
use App\Models\Currency;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            $query->when($request->trashed, fn ($query) => $query->onlyTrashed());
        }

        $query = $this->modifyQuery($query);
        $items = $query->latest()->paginate($perPage);

        $methodProps = method_exists($this, 'addProps') ? $this->addProps() : [];
        $configProps = $this->addPropsConfig ?? [];
        $mergedProps = array_merge($methodProps, $configProps);

        $dataArray = [
            'items' => $items,
            'filters' => ['search' => $search],
            'config' => $this->makeConfig(),
            ...$mergedProps,
        ];

        return Inertia::render($this->componentPath, $dataArray);
    }
}
