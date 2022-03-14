<?php

namespace App\Http\Resources;

use App\Models\SettlementType;
use Illuminate\Http\Resources\Json\JsonResource;

class SettlementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'zone_type' => $this->zone_type,
            'settlement_type' => new SettlementTypeResource($this->whenLoaded('settlementType'))
        ];
    }
}
