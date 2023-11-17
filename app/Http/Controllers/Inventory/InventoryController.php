<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Inventory\ItemPurchases;
use App\Models\Inventory\ItemPurchasesDetails;
use App\Models\Items\Items;
use App\Models\Purchases\PurchaseOrders;
use App\Models\Purchases\PurchaseOrderDetails;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;

use App\Http\Resources\TransItemCodeResource;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;
use PDF;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $unit_list = $dropdownscontroller->comboUnitsList($company_code);
      $fromdate = date('d-m-Y');
      $todate = date('d-m-Y');
      $q =ItemPurchases::query()
        ->join("suppliers", "suppliers.id", "=", "pur_supp_id")
        ->where('item_purchases.pur_comp_id', $company_code)
        ->select('item_purchases.id','item_purchases.pur_comp_id','pur_order_title',
        'pur_order_no','pur_order_date','pur_order_refid','pur_order_ref','pur_pi_no','pur_supp_id',
        'pur_comments','pur_m_curr_rate','pur_total_qty','pur_total_amount','supp_name');

      if($request->filled('supplier_id')){
        $q->where('pur_supp_id', $request->get('supplier_id'));
      }
      if($request->filled('pur_order_no')){
        $q->where('pur_order_no', $request->get('pur_order_no'));
      }
      if($request->filled('fromdate')){
        $q->where('pur_order_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $q->where('pur_order_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
      $rows = $q->orderBy('item_purchases.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'pur_supp_id'     => $request->get('supplier_id'),
        'pur_order_date'  => $request->get('fromdate'),
        'pur_order_date'  => $request->get('todate'),
      ));
      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view ('/inventory/itm_inventory_index', compact('rows','suppliers','fromdate','todate'));
    }


    public function inventory_modal_view($id)
    {
        $rows_m = ItemPurchases::query()
          ->join("suppliers", "suppliers.id", "=", "pur_supp_id")
          ->where('item_purchases.id', $id)
          ->select('item_purchases.id','item_purchases.pur_comp_id','pur_order_title',
          'pur_order_no','pur_order_date','pur_order_ref','pur_pi_no','pur_supp_id',
          'pur_comments','pur_total_qty','pur_total_amount','supp_name','supp_add1',
          'supp_add2','supp_mobile')->first();

        $rows_d = ItemPurchasesDetails::query()
          ->join("items", "items.id", "=", "pur_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->join("warehouses", "warehouses.id", "=", "pur_warehouse_id")
          ->where('pur_order_id', $id)
          ->select('item_code','item_name','pur_lot_no','pur_item_qty','pur_item_remarks',
          'pur_item_price','itm_cat_name','ware_name')->get();

        return view('inventory.inv_purchase_item_viewmodal',compact('rows_m','rows_d'));
    }

    public function purchase_voucher($pur_id)
    {
      $rows_m = ItemPurchases::query()
        ->join("suppliers", "suppliers.id", "=", "pur_supp_id")
        ->where('item_purchases.id', $pur_id)
        ->select('item_purchases.id','item_purchases.pur_comp_id','pur_order_title',
        'pur_order_no','pur_order_date','pur_order_ref','pur_pi_no','pur_supp_id',
        'pur_comments','pur_total_qty','pur_total_amount','supp_name','supp_add1',
        'supp_add2','supp_mobile')->first();

      $rows_d = ItemPurchasesDetails::query()
          ->join("warehouses", "warehouses.id", "=", "pur_warehouse_id")
          ->join("items", "items.id", "=", "pur_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('pur_order_id', $pur_id)
          ->select('item_code','item_name','pur_lot_no','pur_item_qty','pur_item_remarks',
          'pur_item_price','itm_cat_name','ware_name')->get();

      $fileName = "purchase_".$pur_id;

      $pdf = PDF::loadView('/inventory/reports/rpt_purchase_voucher_pdf',
      compact('rows_m','rows_d',), [], [
        'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }


    public function get_pur_item($id,$poid){
    // return('ss'. $id);
      return new TransItemCodeResource(
        $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("purchase_order_details", "items.id", "=", "po_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('items.id','=', $id)
          ->where('po_order_id', $poid)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
          'po_item_qty as item_ord_qty','po_item_qty_rec as item_bal_stock',
          'vUnitName','po_item_price as cust_price','purchase_order_details.id as item_level')->first()
      );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function create()
     {
       $dropdownscontroller = new DropdownsController();
       $company_code = $dropdownscontroller->defaultCompanyCode();
       $companies  = $dropdownscontroller->comboCompanyAssignList();
       $dist_list  = $dropdownscontroller->comboDistrictsList();
       $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
       $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
       $generalsController = new GeneralsController();
       $purchase_no = ''; //$generalsController->make_sales_order_ref($company_code);

       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $rows_m =PurchaseOrders::query()
           ->where('purchase_orders.id', -1 )
           ->select('purchase_orders.id','po_comp_id','po_order_title','po_order_no',
           'po_order_date','po_order_ref','po_pi_no','po_supp_id','po_comments','po_total_qty',
           'po_total_amount','po_total_amount_BDT','po_m_curr','po_m_curr_rate')
           ->orderBy('purchase_orders.id', 'desc')->first();
       $rows_d = PurchaseOrderDetails::query()
           ->join("items", "items.id", "=", "po_item_id")
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->where('po_order_id', -1)
           ->select('purchase_order_details.id','po_item_id','item_code','item_name',
           'item_desc','item_bar_code','po_storage_id','po_item_unit','po_item_qty',
           'po_item_qty_rec','po_item_qty','po_item_price')->get();

      $purchase_date = date('d-m-Y');
      $supplier_id='';
      $pi_no = '';
      $po_total_amount_BDT= "0.00";
      $po_m_curr_rate = "0.00";
      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();

      $po_list = PurchaseOrders::query()
       ->where('po_comp_id', '=', $company_code)
       ->where('is_approved', '=', 1)
       ->where('is_closed', '=', 0)->get();
       return view('/inventory/itm_inventory_create',compact('rows_m','rows_d','companies','purchase_date','company_code',
       'suppliers','supplier_id','pi_no','item_list','purchase_no',
       'warehouse_list','stor_list','po_list','po_total_amount_BDT','po_m_curr_rate'))->render();
    }

      public function get_storageId($compid,$wid){
         //return('ss'. $wid.'::'.$compid);
           $generalsController = new GeneralsController();
           $storage_inf = $generalsController->get_storageId($compid,$wid);
           return response()->json($storage_inf);
      }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $company_code   = $request->company_code;
      $purchase_date  = date('Y-m-d',strtotime($request->purchase_date));
      $generalsController = new GeneralsController();
      $finan_yearId = $generalsController->getFinYearId($company_code,$purchase_date);

      //Item Validity Check
      $msg = $this->ItemValidityCheck($request);
      if($msg != '') {
      //  return redirect()->back()->with('message',$msg)->withInput();
        return redirect()->route('itm.inventory.create')->with('message',$msg)->withInput();
      }
      $itempurchases = new ItemPurchases();
      $id = $itempurchases->storePurchase($request);
      $itempurchases->storePurchaseItem($request,$id,0);
      return redirect()->route('itm.inventory.create')->with('message','Purchase Creation Successful !');
  }

  public function ItemValidityCheck($request)
  {
    $message = '';
    $detId = $request->input('ItemCodeId');
    //return count($detId);
     if ($detId){
       $i = 0;
        foreach ($detId as $key => $value){
          if ($request->ItemCodeId[$key] != ''){
            if ($request->LotNo[$key] == ''){
              $message = 'Failed: Lot No Could Not empty';
            }
            else if ($request->Rate[$key] == ''){
              $message = 'Failed: Rate Could Not empty';
            }
            else if ($request->Qty[$key] == '' || $request->Qty[$key] <= 0){
              $message = 'Failed: Wrong Qty';
            }
          }
        }
     }
     return $message;
  }

  public function getPOData(Request $request)
  {
    $id = $request->purchase_no;
    $rows_m =PurchaseOrders::query()
        ->where('purchase_orders.id', $id )
        ->select('purchase_orders.id','po_comp_id','po_order_title','po_order_no',
        'po_order_date','po_order_ref','po_pi_no','po_supp_id','po_comments','po_total_qty',
        'po_total_amount','po_total_amount_BDT','po_m_curr','po_m_curr_rate')
        ->orderBy('purchase_orders.id', 'desc')->first();

    $dropdownscontroller = new DropdownsController();
    $company_code = $rows_m->po_comp_id;
    $companies    = $dropdownscontroller->comboCompanyAssignList();
    $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
    $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
    $generalsController = new GeneralsController();

    $item_list =  Items::query()
    ->join("purchase_order_details", "items.id", "=", "po_item_id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->where('item_ref_comp_id', '=', $company_code)
    ->where('po_order_id', $id)
    ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    'item_op_stock','item_bal_stock','itm_cat_name')
    ->orderBy('item_name','asc')->get();

    $rows_d = PurchaseOrderDetails::query()
        ->join("items", "items.id", "=", "po_item_id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('po_order_id', $id)
        ->whereRaw('po_item_qty-po_item_qty_rec > 0')
        ->select('purchase_order_details.id','po_item_id','item_code','item_name','item_desc','item_bar_code',
        'po_storage_id','po_item_unit','po_item_qty','po_item_qty_rec','po_item_qty',
        'po_item_price','po_d_curr_rate')->get();

    $purchase_date = date('d-m-Y');
    $purchase_no   = $id;
    $supplier_id = $rows_m->po_supp_id;
    $pi_no = $rows_m->po_pi_no;
    $po_total_amount_BDT = $rows_m->po_total_amount_BDT;
    $po_m_curr_rate = $rows_m->po_m_curr_rate;

    $suppliers    = Suppliers::query()->where('id', $supplier_id)->orderBy('supp_name','asc')->get();
    $po_list      = PurchaseOrders::query()
    ->where('po_comp_id', '=', $company_code)
    ->where('is_approved', '=', 1)
    ->where('is_closed', '=', 0)->get();
    return view('/inventory/itm_inventory_create',compact('rows_m','rows_d','companies','purchase_date','company_code',
    'suppliers','supplier_id','pi_no','item_list','purchase_no',
    'warehouse_list','stor_list','po_list','po_total_amount_BDT','po_m_curr_rate'))->render();

  }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$poid)
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
      $generalsController = new GeneralsController();

      $item_list =  Items::query()
      ->join("purchase_order_details", "items.id", "=", "po_item_id")
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $company_code)
      ->where('po_order_id', $poid)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

      $row_wid = ItemPurchasesDetails::query()
        ->where('pur_order_id', $id)
        ->select('pur_warehouse_id','pur_storage_id')->first();

      $rows_m =ItemPurchases::query()
          ->where('pur_comp_id', '=', $company_code)
          ->where('item_purchases.id', $id )
          ->select('item_purchases.id','pur_comp_id','pur_order_title','pur_order_no','pur_m_warehouse_id',
          'pur_order_date','pur_order_ref','pur_order_refid','pur_pi_no','pur_supp_id','pur_comments','pur_total_qty',
          'pur_m_curr_rate','pur_total_amount','pur_total_amount_bdt')
          ->orderBy('item_purchases.id', 'desc')->first();

      $sql="select item_purchases.id,item_purchases.pur_comp_id,pur_order_title,pur_order_no,
      pur_order_date,pur_order_refid,pur_order_ref,pur_pi_no,pur_supp_id,pur_comments,
      pur_m_warehouse_id,pur_total_qty,pur_total_amount,pur_warehouse_id,pur_storage_id,
      pur_order_det_id, pur_item_id,pur_lot_no,pur_item_unit,pur_item_exp_dt, pur_d_item_curr_rate,
      pur_item_price, pur_item_qty, pur_item_remarks,po_item_qty, po_item_qty_rec,item_code,item_bar_code,
      item_desc from `item_purchases_details`
      inner join `item_purchases` on `item_purchases`.`id` = `pur_order_id`
      inner join `purchase_orders` on `purchase_orders`.`id` = `item_purchases`.`pur_order_refid`
      inner join `purchase_order_details` on `purchase_orders`.`id` = `po_order_id` and `purchase_order_details`.`id` = pur_order_det_id
      inner join `items` on `items`.`id` = `pur_item_id` inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id`
      where `pur_order_id` = $id";
      $rows_d = DB::select($sql);
      /*$rows_d = ItemPurchasesDetails::query()
        ->join("item_purchases", "item_purchases.id", "=", "pur_order_id")
        ->join("purchase_orders", "purchase_orders.id", "=", "item_purchases.pur_order_refid")
        ->join('purchase_order_details', function ($join) {
            $join->on('purchase_orders.id', '=', 'po_order_id')
               ->where('purchase_order_details.id', '=', 'pur_order_det_id');
             })
        ->join("items", "items.id", "=", "pur_item_id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('pur_order_id', $id)
        ->selectRaw('item_purchases.id,item_purchases.pur_comp_id,pur_order_title,pur_order_no,
          pur_order_date,pur_order_refid,pur_order_ref,pur_pi_no,pur_supp_id,pur_comments,
          pur_m_warehouse_id,pur_total_qty,pur_total_amount,pur_warehouse_id,pur_storage_id,
          pur_order_det_id,pur_item_id,pur_lot_no,pur_item_unit,pur_item_exp_dt,
          pur_item_price,pur_item_qty,pur_item_remarks,po_item_qty,
          po_item_qty_rec,item_code,item_bar_code,item_desc')->get();
          $collect = collect($rows_d);*/

      $row_acc_vh = ItemPurchases::query()
              ->join("acc_transaction_details", "pur_order_no", "=", "acc_invoice_no")
              ->join("acc_transactions", "acc_transactions.id", "=", "acc_trans_id")
              ->select('voucher_no')
              ->where('trans_type', 'PV')
              ->where('item_purchases.id', $id)->first();
    

      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      $suppliers = Suppliers::query()->where('id', $rows_m->pur_supp_id)->orderBy('supp_name','asc')->get();
      return view('/inventory/itm_inventory_edit',
      compact('rows_m','rows_d','companies','item_list','stor_list','warehouse_list','suppliers',
      'row_wid', 'row_acc_vh'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {   
       $pur_order_title = 'PV';
       $id = $request->id;
       $company_code = $request->company_code;
       $supplier_id  = $request->supplier_id;
       $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
       $generalsController = new GeneralsController();
       $finan_yearId = $generalsController->getFinYearId($company_code,$purchase_date);

       // Validate the Field
       $msg = $this->ItemValidityCheck($request);
       if($msg != '') {
         return redirect()->back()->with('message',$msg)->withInput();
       }

       $itempurchases = new ItemPurchases();
       try{
          $inputdata  = ItemPurchases::find($id);
          $inputdata->pur_comp_id    = $company_code;
          $inputdata->pur_order_date = $purchase_date;
          $inputdata->pur_order_refid = $request->res_purchase_no;
          $inputdata->pur_order_ref  = $request->purchase_no;
          $inputdata->pur_pi_no      = $request->pi_no;
          $inputdata->pur_supp_id    = $supplier_id;
          $inputdata->pur_m_warehouse_id = $request->itm_warehouse;
          $inputdata->pur_m_curr_rate = $request->currencyValue;
          $inputdata->pur_comments   = $request->comments;
          $inputdata->pur_total_qty  = ($request->total_qty=='')?'0':$request->total_qty;
          $inputdata->pur_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;
          $inputdata->pur_total_amount_bdt  = ($request->total_amount_bdt=='')?'0':$request->total_amount_bdt;
          $inputdata->save();

          //Details Records
          $pur_order_no = $request->pur_order_no;
          ItemPurchasesDetails::where('pur_order_id',$id)->delete();
          //delete financial transaction
          $acc_trans_id = $generalsController->getPOIdByVoucherNo($pur_order_no);
          if($acc_trans_id>0) {
            AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
            AccTransactions::where('id',$acc_trans_id)->delete();
          }
          $voucher_no  = $request->acc_voucher_no;
          $itempurchases->storePurchaseItem($request,$id,$voucher_no);

       }catch (\Exception $e){
           return redirect()->back()->with('error',$e->getMessage())->withInput();
       }
       
       return redirect()->back()->with('message', 'Purchase Update Successful.');

    }

    public function acc_modal_view($voucher_no)
    {
       if ($voucher_no != '') {
          $generalscontroller = new GeneralsController();
          $id = $generalscontroller->getPOIdByVoucherNo($voucher_no);
          //return $id;
          $acctranscontroller = new AccountTransController();
          $rows_m = $acctranscontroller->modal_view_m($id);
          $rows_d = $acctranscontroller->modal_view_d($id);
          return view('accounts.acctrans_viewmodal',compact('rows_m','rows_d'));
        }else{
          return 'Posting not yet done';
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id,$PONo)
    {
        try{

            ItemPurchasesDetails::where('pur_order_id',$id)->delete();
            ItemPurchases::where('id',$id)->delete();

            //delete financial transaction
            $generalsController = new GeneralsController();
            $acc_trans_id = $generalsController->getPOIdByVoucherNo($PONo);
            if($acc_trans_id>0) {
              AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
              AccTransactions::where('id',$acc_trans_id)->delete();
            }
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
