<?php

namespace App\Models\Rawmaterials;

use App\Models\Rawmaterials\view_raw_mat_vou_amt;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Rawmaterials\RawMaterialsReceivesDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RawMaterialsReceives extends Model
{
    protected $guarded = [];

    public function details(){
      return $this->belongsTo(RawMaterialsReceivesDetails::class, 'id', 'raw_order_id');
    }

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
      $data = view_raw_mat_vou_amt::query()
        ->where('raw_order_id', $raw_id)->get();
        return  $data;
    }

    public function rawMaterialReceived($request,$finan_yearId){
      $company_code = $request->company_code;
      $supplier_id  = $request->supplier_id;
      $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
      $generalsController = new GeneralsController();
      $pur_order_no = $generalsController->make_raw_orderno($company_code);
      //return $pur_order_no;
      $trans_id = RawMaterialsReceives::insertGetId([
        'raw_comp_id'     => $company_code,
        'raw_fin_year_id' => $finan_yearId,
        'raw_order_title' => 'GR',
        'raw_type'        => $request->POType,
        'raw_order_no'    => $pur_order_no,
        'raw_order_date'  => $purchase_date,
        'raw_order_ref'   => $request->purchase_no,
        'raw_pi_no'       => $request->pi_no,
        'raw_supp_id'     => $request->supplier_id,
        'raw_m_warehouse_id' => $request->itm_warehouse,
        'raw_m_curr'      => $request->POType=='0'?'BDT':$request->currencyName,
        'raw_m_curr_rate' => $request->POType=='0'?'1':$request->currencyValue,
        'raw_comments'    => $request->comments,
        'raw_total_qty'   => ($request->total_qty=='')?'0':$request->total_qty, 
        'raw_total_amount'=> ($request->total_amount=='')?'0':$request->total_amount,
        'purchaseImages'  => $request->multiImage,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);
      return $trans_id;

    }

    public function rawMaterialReceivedItem($request,$id){
        $generalsController = new GeneralsController();
        $trans_id = $id;
        $company_code = $request->company_code;
        $supplier_id  = $request->supplier_id;
        $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
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
                RawMaterialsReceivesDetails::create([
                    'raw_comp_id'    => $company_code,
                    'raw_order_id'   => $trans_id,
                    'raw_item_id'    => $request->ItemCodeId[$key],
                    'raw_item_unit'  => $request->Unit[$key],
                    'raw_item_desc'  => $request->ItemDesc[$key],
                    //'po_item_exp_dt' => date('Y-m-d',strtotime($exp_date)),
                    'raw_warehouse_id' => 1,
                    'raw_storage_id'  => 1,
                    'raw_lot_no'      => 101, 
                    'raw_item_price'  => $request->Rate[$key] == ''?'0':$request->Rate[$key],
                    'raw_item_qty'    => $request->Qty[$key] == ''?'0':$request->Qty[$key], 
                    'raw_d_curr'      => 'BDT',
                    'raw_d_curr_rate' => 1,
                    'raw_item_remarks' => $request->Remarks[$key],
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
