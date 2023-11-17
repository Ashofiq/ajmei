<?php

namespace App\Models\Sales;

use App\Models\Sales\view_sales_inv_amt;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use DB;

class SalesInvoices extends Model
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

   
     public function sal_invoice($inv_id) {
      $rows_m = SalesInvoices::query()
        ->join("customers", "customers.id", "=", "inv_cust_id")
        ->leftjoin("customer_sales_persons", "customer_sales_persons.id", "=", "cust_sales_per_id")
        ->join("companies", "companies.id", "=", "inv_comp_id")
        ->join("sales_orders", "sales_orders.id", "=", "inv_sale_ord_id")
        ->where('sales_invoices.id', $inv_id)
        ->selectRaw('sales_invoices.id,inv_comp_id,inv_no,inv_date,inv_so_po_no,inv_cust_id,
          inv_sub_total,inv_itm_disc_value,inv_disc_value,inv_vat_value,inv_net_amt,inv_carring_cost,inv_labour_cost,inv_load_unload_cost,inv_service_charge,inv_other_cost,so_order_no,so_order_date,cust_code,cust_name,cust_add1,cust_add2,cust_mobile,cust_phone,sales_name,companies.name,companies.address1, cust_slno, personalMobileno, so_special_offer')->first();
        return  $rows_m;
    }

    public function sal_fin_transaction($inv_id) {
      $data = view_sales_inv_amt::query()
        ->where('inv_mas_id', $inv_id)->get();
        return  $data;
    }


}
