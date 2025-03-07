<?php

namespace Modules\Product\Services;

use App\Interfaces\TagServiceInterface;
use Modules\Product\Models\Tag;

class TagService implements TagServiceInterface
{
    use ServiceTrait;

    public $model = Tag::class;
}
