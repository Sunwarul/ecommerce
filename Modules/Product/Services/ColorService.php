<?php

namespace Modules\Product\Services;

use App\Interfaces\ColorServiceInterface;
use App\Models\Color;

class ColorService implements ColorServiceInterface
{
    use ServiceTrait;

    public $model = Color::class;
}
