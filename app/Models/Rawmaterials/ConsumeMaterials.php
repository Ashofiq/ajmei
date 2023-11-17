<?php

namespace App\Models\Rawmaterials;

use App\Models\Rawmaterials\view_mat_consume_amt;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Rawmaterials\ConsumeMaterialsDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ConsumeMaterials extends Model
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

    public function raw_fin_transaction($raw_id) {
      $data = view_mat_consume_amt::query()
        ->where('r_cons_order_id', $raw_id)->get();
        return  $data;
    }

    public function rawMaterialIssued($request,$finan_yearId){
      $company_code = $request->company_code;
      $supplier_id  = $request->supplier_id;
      $issue_date = date('Y-m-d',strtotime($request->issue_date));
      $generalsController = new GeneralsController();
      $pur_order_no = $generalsController->make_consumable_orderno($company_code);
      //return $pur_order_no;
      $trans_data = ConsumeMaterials::create([
        'r_cons_comp_id'     => $company_code,
        'r_cons_fin_year_id' => $finan_yearId,
        'r_cons_order_title' => 'CI',
        'r_cons_type'        => $request->POType,
        'r_cons_order_no'    => $pur_order_no,
        'r_cons_order_date'  => $issue_date,
        'r_cons_order_ref'   => $request->purchase_no, 
        'r_cons_m_warehouse_id' => 1, //$request->itm_warehouse, 
        'r_cons_comments'    => $request->comments,
        'r_cons_total_qty'   => ($request->total_qty=='')?'0':$request->total_qty, 
        'r_cons_total_amount'=> ($request->total_amount=='')?'0':$request->total_amount,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);
      return $trans_data;

    }

    public function rawMaterialIssuedItem($request,$id){
        $generalsController = new GeneralsController();
        $trans_id = $id;
        $company_code = $request->company_code;
        $supplier_id  = $request->supplier_id;
        $issue_date = date('Y-m-d',strtotime($request->issue_date));
       // $finan_yearId = $generalsController->getFinYearId($company_code,$purchase_date);
        // $pur_order_no = $generalsController->get_purchase_orderno1($id);
        //$storage_id = $generalsController->get_storage_id($request->itm_warehouse);

        //Details Records
        $detId = $request->input('ItemCodeId');
        $acc_naration = '';
        if ($detId){
           $i = 0; 
            foreach ($detId as $key => $value){
              $i = $i + 1;
              //print_r($detId);
              //return 'AA:'.$request->Qty[$key];
              //$exp_date = $request->input('exp_date_'.$i);
              if ($request->Qty[$key] > 0){
                ConsumeMaterialsDetails::create([
                  'r_cons_comp_id'    => $company_code,
                  'r_cons_order_id'   => $trans_id,
                  'r_cons_item_id'    => $request->ItemCodeId[$key],
                  'r_cons_item_unit'  => $request->Unit[$key],
                  'r_cons_item_desc'  => $request->ItemDesc[$key],
                  //'po_item_exp_dt' => date('Y-m-d',strtotime($exp_date)),
                  'r_cons_warehouse_id' => 1,
                  'r_cons_storage_id'  => 1,
                  'r_cons_lot_no'      => 101, 
                  'r_cons_item_price'  => $request->Rate[$key] == ''?'0':$request->Rate[$key],
                  'r_cons_item_qty'    => $request->Qty[$key] == ''?'0':$request->Qty[$key],  
                  'r_cons_item_remarks' => $request->Remarks[$key],
                ]);

                //make naration for accounting entry
                $itmname = $generalsController->get_item_details($request->ItemCodeId[$key])->item_name;
                $qty    = $request->Qty[$key];
                $price  = $request->Rate[$key];
                $amount = $qty * $price;
                $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$request->Rate[$key].';Amount:'.$amount.'),<br/>'; 
              }
            }
          }
         return $acc_naration; 
    }

}
