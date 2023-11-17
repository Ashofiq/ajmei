<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransItemCodeResource extends JsonResource
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
            'id'         =>  $this->id,
            'item_code'  =>  $this->item_code,
            'item_name'  =>  $this->item_name,
            'item_desc'  => $this->item_desc,
            'item_level' => $this->item_level,
            'item_bar_code'   => $this->item_bar_code,
            'item_op_stock'   => $this->item_op_stock,
            'item_bal_stock'  => $this->item_bal_stock,
            'item_unit'  => $this->vUnitName,
            'item_price' => $this->cust_price,
            'item_ord_disc' => $this->item_ord_disc,
            'item_ord_qty'  => $this->item_ord_qty,
            'item_comm_val' => $this->cust_comm,
            'averagePrice' => $this->averagePrice
        ];
    }
}
