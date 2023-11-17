<?php

namespace App\Models\Sales;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SalesInvoiceDetails extends Model
{
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        if(!App::runningInConsole())
        {
            static::creating(function ($model)
            {
                $model->fill([
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            });
            static::updating(function ($model)
            {
                $model->fill([
                    'updated_by' => Auth::id(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => Carbon::now(),
                ]);
            });
        }
    }

    public function get_sal_invoice($del_id) {
      $inv_id = SalesInvoiceDetails::query()
            ->where('inv_del_id', $del_id)
            ->select('inv_mas_id')->first()->inv_mas_id;
      return $inv_id;
    }

    public function sal_invoice_details($inv_id) {
      $rows_d =  SalesInvoiceDetails::query()
         ->join("items", "items.id", "=", "inv_item_id")
         ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
         ->where('inv_mas_id', $inv_id)
         ->selectRaw('inv_warehouse_id,itm_cat_name,item_code,item_name,item_desc,size,inv_item_id,inv_item_spec,inv_item_pcs,inv_item_size,inv_item_price,inv_lot_no,inv_qty,inv_unit,inv_itm_disc_per,inv_disc_per,inv_disc_value,inv_vat_per,inv_del_comments, inv_item_weight')->get();
        return  $rows_d;
    }
    
    public function sal_invoice_delivered_to($inv_id) {
      $rows_delv_to =  SalesInvoiceDetails::query()
         ->where('inv_mas_id', $inv_id)
         ->selectRaw('inv_del_to_cust,inv_del_add,inv_del_ref')->first();
        return  $rows_delv_to;
    }

}
