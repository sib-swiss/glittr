<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepositoryFullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'name' => $this->name,
            'url' => (string) $this->url,
            'website' => (string) $this->website,
            'description' => $this->description,
            'author' => new AuthorResource($this->whenLoaded('author')),
        ];

        if ($this->relationLoaded('tags')) {
            $data['tags'] = TagResource::collection($this->tags->sortBy('pivot.order_column'));
        }

        $data += [
            'days_since_last_push' => $this->days_since_last_push,
            'stargazers' => $this->stargazers,
            'license' => $this->license,
        ];

        return $data;
    }
}
