<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ZipCodeResource extends JsonResource
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
            'zip_code' => $this->zip_code,
            'locality' => $this->locality,
            'federal_entity' => new FederalEntityResource($this->whenLoaded('federalEntity')),
            'settlements' => SettlementResource::collection($this->whenLoaded('settlements')),
            'municipality' => new MunicipalityResource($this->whenLoaded('municipality')),
        ];
    }
}
