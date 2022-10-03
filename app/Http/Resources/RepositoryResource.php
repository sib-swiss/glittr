<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepositoryResource extends JsonResource
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
            'url' => (string) $this->url,
            'website' => (string) $this->website,
            'description' => $this->description,
            'tags' => $this->tags->pluck('name')->implode(', '),
            'days_since_last_push' => $this->days_since_last_push,
            'stargazers' => $this->stargazers,
            'author' => new AuthorResource($this->whenLoaded('author')),
        ];
    }
}
