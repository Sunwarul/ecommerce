<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
// use App\Exports\SettingExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\SettingStoreRequest;
use App\Http\Requests\SettingUpdateRequest;

class SettingController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'settings',
            modelClass: Setting::class,
            storeRequestClass: SettingStoreRequest::class,
            updateRequestClass: SettingUpdateRequest::class,
            componentPath: 'Settings/Index',
            searchColumns: [],
            // exportClass: SettingExport::class,
            withRelations: [],
        ));
    }

    
}
