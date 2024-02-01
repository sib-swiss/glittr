<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'name' => $this->name,
            'category' => $this->category->name ?? '',
            'ontology' => $this->ontology->name ?? '',
            'ontology_class' => $this->ontology_class ?? '',
            'link' => $this->link ?? '',
            'api_url' => route('api.tags.show', $this->id),
        ];
    }
}
