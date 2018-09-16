<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Book extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var \App\Book $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'year' => $this->year,
            'description' => $this->description,
            'image' => new Image($this->image),
        ];
    }
}
