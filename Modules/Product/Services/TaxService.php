<?php

namespace Modules\Product\Services;

use App\Interfaces\TaxServiceInterface;
use Modules\Product\Models\Tax;

class TaxService implements TaxServiceInterface
{
    use ServiceTrait;

    public $model = Tax::class;
}
