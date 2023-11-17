<?php

namespace App\Models\Rawmaterials;

use App\Models\Rawmaterials\view_raw_mat_issue_amt;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Rawmaterials\RawMaterialsIssuesDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RawMaterialsIssues extends Model
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
      $data = view_raw_mat_issue_amt::query()
        ->where('r_issue_order_id', $raw_id)->get();
        return  $data;
    }

    public function rawMaterialIssued($request,$finan_yearId){
      $company_code = $request->company_code;
      $supplier_id  = $request->supplier_id;
      $issue_date = date('Y-m-d',strtotime($request->issue_date));
      $generalsController = new GeneralsController();
      $pur_order_no = $generalsController->make_raw_issue_orderno($company_code);
      //return $pur_order_no;
      $total_qty = str_replace(',', '', $request->total_qty);
      $total_amount = str_replace(',', '', $request->total_amount);
      $trans_id = RawMaterialsIssues::insertGetId([
        'r_issue_comp_id'     => $company_code,
        'r_issue_fin_year_id' => $finan_yearId,
        'r_issue_order_title' => 'GI',
        'r_issue_type'        => $request->POType,
        'r_issue_order_no'    => $pur_order_no,
        'r_issue_order_date'  => $issue_date,
        'r_issue_order_ref'   => $request->purchase_no, 
        'r_issue_m_warehouse_id' => 1, //$request->itm_warehouse,
        'r_issue_m_curr'      => $request->POType=='0'?'BDT':$request->currencyName,
        'r_issue_m_curr_rate' => $request->POType=='0'?'1':$request->currencyValue,
        'r_issue_comments'    => $request->comments,
        'r_issue_total_qty'   => ($total_qty=='')?'0':$total_qty, 
        'r_issue_total_amount'=> ($total_amount=='')?'0':$total_amount,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);
      return $trans_id;

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
                $rate = str_replace(',', '', $request->Rate[$key]);
                $qty = str_replace(',', '', $request->Qty[$key]);

                RawMaterialsIssuesDetails::create([
                    'r_issue_comp_id'    => $company_code,
                    'r_issue_order_id'   => $trans_id,
                    'r_issue_item_id'    => $request->ItemCodeId[$key],
                    'r_issue_item_unit'  => $request->Unit[$key],
                    'r_issue_item_desc'  => $request->ItemDesc[$key],
                    //'po_item_exp_dt' => date('Y-m-d',strtotime($exp_date)),
                    'r_issue_warehouse_id' => 1,
                    'r_issue_storage_id'  => 1,
                    'r_issue_lot_no'      => 101, 
                    'r_issue_item_price'  => $rate == ''?'0':$rate,
                    'r_issue_item_qty'    => $qty == ''?'0':$qty, 
                    'r_issue_d_curr'      => 'BDT',
                    'r_issue_d_curr_rate' => 1,
                    'r_issue_item_remarks' => $request->Remarks[$key],
                ]);

                //make naration for accounting entry
                $itmname = $generalsController->get_item_details($request->ItemCodeId[$key])->item_name;
                $qty    = $qty;
                $price  = $rate;
                $amount = $qty * $price;
                $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$rate.';Amount:'.$amount.'),<br/>'; 
              }
            }
          }
         return $acc_naration; 
    }

}
