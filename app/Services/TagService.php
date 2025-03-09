<?php

namespace App\Services;

use App\Interfaces\TagServiceInterface;
use App\Models\Tag;

class TagService implements TagServiceInterface
{
    use ServiceTrait;

    public $model = Tag::class;
}
