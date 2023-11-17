<?php

namespace App\Models\Purchases;

use App\Models\Inventory\view_purchase_vou_amt;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Purchases\PurchaseOrderDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class PurchaseOrders extends Model
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

    public function pur_fin_transaction($pur_id) {
      $data = view_purchase_vou_amt::query()
        ->where('pur_order_id', $pur_id)->get();
        return  $data;
    }

    public function storePurchaseOrder($request){
      $company_code = $request->company_code;
      $supplier_id  = $request->supplier_id;
      $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
      $generalsController = new GeneralsController();
      $pur_order_no = $generalsController->make_purchase_orderno1($company_code);
      //return $pur_order_no;
      $trans_id = PurchaseOrders::insertGetId([
        'po_comp_id'     => $company_code,
        'po_order_title' => 'PO',
        'po_type'        => $request->POType,
        'po_order_no'    => $pur_order_no,
        'po_order_date'  => $purchase_date,
        'po_order_ref'   => $request->purchase_no,
        'po_pi_no'       => $request->pi_no,
        'po_supp_id'     => $request->supplier_id,
        //'po_m_warehouse_id' => $request->itm_warehouse,
        'po_m_curr'      => $request->POType=='0'?'BDT':$request->currencyName,
        'po_m_curr_rate' => $request->POType=='0'?'1':$request->currencyValue,
        'po_comments'    => $request->comments,
        'po_total_qty'   => ($request->total_qty=='')?'0':$request->total_qty,
        'po_total_amount'=> ($request->total_amount=='')?'0':$request->total_amount,
        'po_total_amount_BDT'=> ($request->total_amount_bdt=='')?'0':$request->total_amount_bdt,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);
      return $trans_id;

    }

    public function storePurchaseOrderItem($request,$id){
        $generalsController = new GeneralsController();
        $trans_id = $id;
        $company_code = $request->company_code;
        $supplier_id  = $request->supplier_id;
        $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
        $finan_yearId = $generalsController->getFinYearId($company_code,$purchase_date);
        $pur_order_no = $generalsController->get_purchase_orderno1($id);
        //$storage_id = $generalsController->get_storage_id($request->itm_warehouse);

        //Details Records
        $detId = $request->input('ItemCodeId');
        if ($detId){
           $i = 0;
            foreach ($detId as $key => $value){
              $i = $i + 1;
              //$exp_date = $request->input('exp_date_'.$i);
              if ($request->Qty[$key] > 0){
                PurchaseOrderDetails::create([
                    'po_comp_id'    => $company_code,
                    'po_order_id'   => $trans_id,
                    'po_item_id'    => $request->ItemCodeId[$key],
                    'po_item_unit'  => $request->Unit[$key],
                    //'po_item_exp_dt' => date('Y-m-d',strtotime($exp_date)),
                    //'po_warehouse_id' => $request->itm_warehouse,
                    //'po_storage_id'  => $request->result_storage_id,
                    //'po_lot_no'      => $request->LotNo[$key] == ''?'0':$request->LotNo[$key],
                    'po_item_price'  => $request->Rate[$key] == ''?'0':$request->Rate[$key],
                    'po_item_qty'    => $request->Qty[$key] == ''?'0':$request->Qty[$key],
                    'po_d_curr'       => $request->POType=='0'?'BDT':$request->currencyName,
                    'po_d_curr_rate'  => $request->POType=='0'?'1':$request->currencyValue,
                    'po_item_remarks' => $request->Remarks[$key],
                ]);

              }
            }
          }

    }

}
