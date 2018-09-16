<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Image extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var \App\Image $this */

        return [
            'url' => \Images::getImageUrl($this->system_sub_path),
            'thumbnail' => \Images::getSmallImageUrl($this->system_sub_path),
        ];
    }
}
