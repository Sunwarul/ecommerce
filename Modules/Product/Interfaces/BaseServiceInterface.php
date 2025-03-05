<?php

namespace Modules\Product\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseServiceInterface
{
    public function all(): LengthAwarePaginator;

    public function store(array $attribute): Model;

    public function update(array $attribute, Model|int $id): Model|bool;

    public function find(Model|int $id): Model|bool;

    public function delete(Model|int $id): Model|bool;
}
