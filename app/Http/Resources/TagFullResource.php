<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagFullResource extends JsonResource
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
            'description' => $this->description,
            'repositories' => RepositoryFullResource::collection($this->whenLoaded('repositories')),
        ];
    }
}
