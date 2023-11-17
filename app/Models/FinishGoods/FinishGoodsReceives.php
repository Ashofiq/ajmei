<?php

namespace App\Models\FinishGoods;

use App\Models\FinishGoods\view_fin_goods_rec_amt;
use App\Http\Controllers\General\GeneralsController;

use App\Models\FinishGoods\FinishGoodsReceivesDetails;
use App\Models\Rawmaterials\RawMaterialsIssuesDetails;
use App\Models\Rawmaterials\RawMaterialsIssues;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class FinishGoodsReceives extends Model
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

    public function fin_transaction($rec_id) {
      $data = view_fin_goods_rec_amt::query()
        ->where('f_rec_order_id', $rec_id)->get();
        return  $data;
    }

    public function finishGoodsReceived($request,$finan_yearId){
      $company_code = $request->company_code;
      $supplier_id  = $request->supplier_id;
      $rec_date = date('Y-m-d',strtotime($request->rec_date));
      $generalsController = new GeneralsController();
      $pur_order_no = $generalsController->make_fin_goods_rec_orderno($company_code);
      //return $pur_order_no;
      $trans_id = FinishGoodsReceives::insertGetId([
        'f_rec_comp_id'     => $company_code,
        'f_rec_issue_prod_id' => $request->res_issue_id,
        'f_rec_fin_year_id' => $finan_yearId,
        'f_rec_order_title' => 'FR',
        'f_rec_type'        => $request->POType,
        'f_rec_order_no'    => $pur_order_no,
        'f_rec_order_date'  => $rec_date,
        'f_rec_order_ref'   => $request->purchase_no, 
        'f_rec_m_warehouse_id' => 1, //$request->itm_warehouse,
        'f_rec_m_curr'      => 'BDT',
        'f_rec_m_curr_rate' => '1',
        'f_rec_comments'    => $request->comments,
        'f_rec_total_qty'   => ($request->total_qty=='')?'0':$request->total_qty, 
        'f_rec_total_amount'=> ($request->total_amount=='')?'0':$request->total_amount,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);
      return $trans_id;

    }

    public function finishGoodsReceivedItem($request,$id){
        $generalsController = new GeneralsController();
        $trans_id = $id;
        $company_code = $request->company_code;
        $supplier_id  = $request->supplier_id;
        $rec_date = date('Y-m-d',strtotime($request->rec_date));
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
                  FinishGoodsReceivesDetails::create([
                    'f_rec_comp_id'    => $company_code,
                    'f_rec_order_id'   => $trans_id,
                    'f_rec_issu_prd_det_id' => $request->IssueProdDetId[$key],
                    'f_rec_item_id'    => $request->ItemCodeId[$key],
                    'f_rec_item_unit'  => $request->Unit[$key],
                    'f_rec_item_desc'  => $request->ItemDesc[$key],
                    //'po_item_exp_dt' => date('Y-m-d',strtotime($exp_date)),
                    'f_rec_warehouse_id' => 1,
                    'f_rec_storage_id'  => 1,
                    'f_rec_lot_no'      => 101, 
                    'f_rec_item_price'  => $request->Rate[$key] == ''?'0':$request->Rate[$key],
                    'f_rec_item_qty'    => $request->Qty[$key] == ''?'0':$request->Qty[$key], 
                    'f_rec_item_weight' => $request->QWeight[$key] == ''?'0':$request->QWeight[$key], 
                    'f_rec_item_pcs'    => $request->PCS[$key] == ''?'0':$request->PCS[$key], 
                    'f_rec_d_curr'      => 'BDT',
                    'f_rec_d_curr_rate' => 1,
                    'f_rec_item_remarks' => $request->Remarks[$key],
                ]);

                 //update receive qty into purchase order details table
                 $inputdata  = RawMaterialsIssuesDetails::find($request->IssueProdDetId[$key]);
                 $inputdata->r_issue_item_qty_rec = $inputdata->r_issue_item_qty_rec + $request->Qty[$key];
                 $inputdata->save();

                //make naration for accounting entry
                $itmname = $generalsController->get_item_details($request->ItemCodeId[$key])->item_name;
                $qty    = $request->Qty[$key];
                $price  = $request->Rate[$key];
                $amount = $qty * $price;
                $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$request->Rate[$key].';Amount:'.$amount.'),<br/>'; 
              }
            }
            if($acc_naration != ''){
              // Closing Pending Issue Production order
              $bal = RawMaterialsIssuesDetails::where('r_issue_order_id',$request->res_issue_id)
              ->selectRaw('(sum(r_issue_item_qty) - sum(r_issue_item_qty_rec)) as bal')->first()->bal;
              if($bal == 0){
                RawMaterialsIssues::where('id',$request->res_issue_id)
                ->update([ 'is_closed' => 1 ]);
              }
            }
  
          }
         return $acc_naration; 
    }

}
