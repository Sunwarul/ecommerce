<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'code' => $this->code,
            'categories' => $this->categories,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'type' => $this->type,
            'discount_type' => $this->discount_type,
            'amount' => $this->amount,
            'minimum_order' => $this->minimum_order,
            'use_limit' => $this->use_limit,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
