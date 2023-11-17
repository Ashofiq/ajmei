<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectSalesDetails extends Model
{
    use HasFactory;
    protected $fillable = ['so_comp_id','so_comp_id'   ,
    'so_order_id'  ,
    'so_warehouse_id',
    'so_storage_id',
    'so_lot_no'    ,
    'so_item_id'   ,
    'so_item_unit' ,
    'so_item_price',
    'so_item_cat_id',
    'so_item_cat_2nd_id',
    'so_item_size' ,
    'so_item_weight',
    'so_item_pcs'  ,
    'so_order_qty' ,
    'so_order_disc',
    'so_order_comm'];


    protected $table = 'sales_orders_direct_details';
}
