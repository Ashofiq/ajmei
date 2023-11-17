<?php

namespace App\Models\Inventory;

use App\Models\Inventory\view_purchase_vou_amt;
use App\Http\Controllers\General\GeneralsController;

use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use App\Models\Inventory\ItemPurchasesDetails;

use App\Models\Purchases\PurchaseOrders;
use App\Models\Purchases\PurchaseOrderDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ItemPurchases extends Model
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

    public function storePurchase($request){
      $company_code = $request->company_code;
      $supplier_id  = $request->supplier_id;
      $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
      $generalsController = new GeneralsController();
      $pur_order_ref = $generalsController->make_purchase_refno($request->res_purchase_no);
      $finan_yearId = $generalsController->getFinYearId($company_code,$purchase_date);
      $pur_order_no = $generalsController->make_purchase_orderno($company_code,$finan_yearId);
      $trans_id = ItemPurchases::insertGetId([
        'pur_comp_id'     => $company_code,
        'pur_fin_year_id' => $finan_yearId,
        'pur_order_title' => 'PV',
        'pur_order_no'    => $pur_order_no,
        'pur_order_date'  => $purchase_date,
        'pur_order_refid' => $request->res_purchase_no,
        'pur_order_ref'   => $pur_order_ref,
        'pur_pi_no'       => $request->pi_no,
        'pur_supp_id'     => $request->supplier_id,
        'pur_m_curr_rate' => $request->currencyValue,
        'pur_m_warehouse_id' => $request->itm_warehouse,
        'pur_comments'    => $request->comments,
        'pur_total_qty'   => ($request->total_qty=='')?'0':$request->total_qty,
        'pur_total_amount'=> ($request->total_amount=='')?'0':$request->total_amount,
        'pur_total_amount_bdt'=> ($request->total_amount_bdt=='')?'0':$request->total_amount_bdt,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);
      return $trans_id;

    }

    public function storePurchaseItem($request,$id,$voucher_no){
        $generalsController = new GeneralsController();
        $trans_id = $id;
        $company_code = $request->company_code;
        $supplier_id  = $request->supplier_id;
        $pur_m_curr_rate = $request->currencyValue;
        $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
        $finan_yearId = $generalsController->getFinYearId($company_code,$purchase_date);
        $pur_order_no = $generalsController->get_purchase_orderno($id);
        //$storage_id = $generalsController->get_storage_id($request->itm_warehouse);

        //Details Records
        $acc_naration = '';
        $detId = $request->input('ItemCodeId');
        if ($detId){
           $i = 0;
            foreach ($detId as $key => $value){
              $i = $i + 1;
              $exp_date = $request->input('exp_date_'.$i);
              if ($request->Qty[$key] > 0 && $request->LotNo[$key] != ''){
                ItemPurchasesDetails::create([
                    'pur_comp_id'    => $company_code,
                    'pur_order_id'   => $trans_id,
                    'pur_order_det_id' => $request->PurorderDetId[$key],
                    'pur_item_id'    => $request->ItemCodeId[$key],
                    'pur_item_unit'  => $request->Unit[$key],
                    'pur_item_exp_dt' => date('Y-m-d',strtotime($exp_date)),
                    'pur_d_item_curr_rate' => $pur_m_curr_rate,
                    'pur_warehouse_id' => $request->itm_warehouse,
                    'pur_storage_id'  => $request->result_storage_id,
                    'pur_lot_no'      => $request->LotNo[$key] == ''?'0':$request->LotNo[$key],
                    'pur_item_price'  => $request->Rate[$key] == ''?'0':$request->Rate[$key],
                    'pur_item_qty'    => $request->Qty[$key] == ''?'0':$request->Qty[$key],
                    'pur_item_remarks' => $request->Remarks[$key],
                ]);

                //update receive qty into purchase order details table
                $inputdata  = PurchaseOrderDetails::find($request->PurorderDetId[$key]);
                $inputdata->po_item_qty_rec = $inputdata->po_item_qty_rec + $request->Qty[$key];
                $inputdata->save();

                //make naration for accounting entry
                $itmname = $generalsController->get_item_details($request->ItemCodeId[$key])->item_name;
                $qty    = $request->Qty[$key];
                $price  = $request->Rate[$key];
                $amount = $qty * $price;
                $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$request->Rate[$key].';Amount:'.$amount.'),';

              }
            }
          }
          if($acc_naration != ''){
            // Closing Pending Purchase order
            $bal = PurchaseOrderDetails::where('po_order_id',$request->res_purchase_no)
              ->selectRaw('(sum(po_item_qty) - sum(po_item_qty_rec)) as bal')->first()->bal;
              if($bal == 0){
                PurchaseOrders::where('id',$request->res_purchase_no)
                ->update([ 'is_closed' => 1 ]);
            }

          //update financial transaction for Purchase
          if($voucher_no == 0) {
            $voucher_no   = $generalsController->getMaxAccVoucherNo('PV',$company_code,$finan_yearId); // getting max Voucher No
            $voucher_no   = $voucher_no + 1;
          }
          $supp_acc_id  = $generalsController->SupplierChartOfAccId($supplier_id);
          $supp_name    = $generalsController->SupplierName($supplier_id);

          $records  = $this->pur_fin_transaction($trans_id);
          $recCount = $records->count();

          // Insert Transaction Master Records
          $trans_fin_id = AccTransactions::insertGetId([
            'com_ref_id'    => $company_code,
            'voucher_date'  => $purchase_date,
            'trans_type'    => 'PV',
            'voucher_no'    => $voucher_no,
            't_narration'   => $acc_naration,
            'fin_ref_id'    => $finan_yearId,
          ]);

          AccTransactionDetails::create([
              'acc_trans_id'    => $trans_fin_id,
              'c_amount'        => $request->total_amount_bdt,
              'chart_of_acc_id' => $supp_acc_id,
              'acc_invoice_no'  => $pur_order_no,
          ]);

          foreach ($records as $rec){
          //  $net_total = $rec->pur_value;
              $net_total = $rec->pur_value_bdt;
            AccTransactionDetails::create([
                'acc_trans_id'    => $trans_fin_id,
                'd_amount'        => $net_total,
                'chart_of_acc_id' => $rec->sett_accid,
            ]);
          }
       }
    }

}
