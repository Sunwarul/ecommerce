<?php

namespace Modules\Product\Services;

use App\Interfaces\ColorServiceInterface;
use Modules\Product\Models\Color;

class ColorService implements ColorServiceInterface
{
    use ServiceTrait;

    public $model = Color::class;
}
