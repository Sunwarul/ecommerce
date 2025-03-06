<?php

namespace Modules\Product\Services;

use App\Interfaces\BrandServiceInterface;
use Modules\Product\Models\Brand;

class BrandService implements BrandServiceInterface
{
    use ServiceTrait;

    public $model = Brand::class;
}
