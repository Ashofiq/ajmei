<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrdersProdConfirmHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_comp_id',
        'so_ref_order_id'  ,
        'so_ref_order_no'  ,
        'so_ref_details_id',
        'date'                 ,
        'so_itm_id'   ,
        'so_order_qty'     ,
        'so_conf_qty',
        'so_conf_weight',
        'so_item_unit'
    ];
}
