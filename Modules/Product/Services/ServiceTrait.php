<?php

namespace Modules\Product\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

trait ServiceTrait
{
    public function all(): LengthAwarePaginator
    {
        return $this->model::paginate();
    }

    public function store(array $attribute): Model
    {
        return $this->model::create($attribute);
    }

    public function update(array $attributes, Model|int $bindingObject): Model|bool
    {
        if (is_int($bindingObject)) {
            $bindingObject = $this->model::find($bindingObject);
            $bindingObject->update($attributes);

            return $bindingObject;
        }

        $bindingObject->update($attributes);

        return $bindingObject;
    }

    public function find(Model|int $bindingObject): Model|bool
    {
        if (is_int($bindingObject)) {
            return $this->model::find($bindingObject);
        }

        return $bindingObject;
    }

    public function delete(Model|int $bindingObject): Model|bool
    {
        if (is_int($bindingObject)) {
            $bindingObject = $this->model::find($bindingObject);
            $bindingObject->delete();
        } else {
            $bindingObject->delete();
        }

        return $bindingObject;
    }
}
