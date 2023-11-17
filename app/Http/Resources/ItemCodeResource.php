<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       
        return [
            'id'       => $this->id,
            'item_code' =>  $this->item_code,
            'item_name' =>  $this->item_name,
            'item_bar_code' => $this->item_bar_code,
        ];
    }
}
