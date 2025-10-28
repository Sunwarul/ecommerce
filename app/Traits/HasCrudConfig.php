<?php

namespace App\Traits;

use App\Utils\CrudConfig;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait HasCrudConfig
{
    public string $resource;

    public string $modelClass;

    public string $storeRequestClass;

    public string $updateRequestClass;

    public array $searchColumns;

    public string $exportClass;

    public string $componentPath;

    public array $withRelations = [];

    public function init(CrudConfig $config): void
    {
        $this->resource = $config->resource ?? $this->resource;
        $this->modelClass = $config->modelClass ?? $this->modelClass;
        $this->storeRequestClass = $config->storeRequestClass ?? $this->storeRequestClass;
        $this->updateRequestClass = $config->updateRequestClass ?? $this->updateRequestClass;
        $this->searchColumns = $config->searchColumns ?? $this->searchColumns;
        $this->exportClass = $config->exportClass ?? $this->exportClass;
        $this->componentPath = $config->componentPath ?? $this->componentPath;
        $this->withRelations = $config->withRelations ?? $this->withRelations;
        $this->addProps = $config->addProps ?? $this->addProps();
    }

    private function makeConfig()
    {
        $modelRawName = class_basename($this->modelClass);
        $modelLowerCase = Str::snake($modelRawName);

        $routes = [
            'indexRoute' => $this->resource.'.index',
            'indexRouteTrashed' => $this->resource.'.index',
            'storeRoute' => $this->resource.'.store',
            'updateRoute' => $this->resource.'.update',
            'deleteRoute' => $this->resource.'.destroy',
            'bulkDeleteRoute' => $this->resource.'.bulk-destroy',
            'bulkRestoreRoute' => $this->resource.'.bulk-restore',
            'bulkForceDeleteRoute' => $this->resource.'.bulk-force-delete',
            'exportRoute' => $this->resource.'.export',
        ];

        $config = [
            'title' => Str::of($this->resource)->title()->replace('_', ' '),
            'modelSingular' => $modelLowerCase,
            'modelRaw' => $modelRawName,
            'resource' => $this->resource,
        ];

        foreach ($routes as $key => $route) {
            $sanitizedRoute = str_replace('_', '-', $route);
            if ($key === 'indexRouteTrashed' && Route::has($sanitizedRoute)) {
                $config[$key] = route($sanitizedRoute, ['trashed' => true]);
            } elseif (in_array($key, ['updateRoute', 'deleteRoute']) && Route::has($sanitizedRoute)) {
                $config[$key] = route($sanitizedRoute, [$modelLowerCase => '__ID__']);
            } elseif ($key === 'exportRoute') {
                $config[$key] = Route::has($sanitizedRoute) ? route($sanitizedRoute) : '';
                $config[$key] = isset($this->exportClass) && ! empty($this->exportClass) ? $config[$key] : '';
            } else {
                $config[$key] = Route::has($sanitizedRoute) ? route($sanitizedRoute) : '';
            }
        }

        return $config;
    }
}
