<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransAccHeadResource extends JsonResource
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
            'id'        =>  $this->id,
            'acc_code'  =>  $this->acc_code,
            'acc_head'  =>  $this->acc_head,
            'acc_origin' => $this->acc_origin,
            'acc_level'  => $this->acc_level,
            'parent_id'  => $this->parent_id,
        ];
    }
}
