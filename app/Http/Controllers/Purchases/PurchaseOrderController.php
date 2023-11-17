<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Purchases\PurchaseOrders;
use App\Models\Purchases\PurchaseOrderDetails;
use App\Models\Purchases\Currencies;
use App\Models\Items\Items;

use App\Http\Resources\TransItemCodeResource;

use Illuminate\Http\Request;
use App\Http\Requests;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;
use PDF;

class PurchaseOrderController extends Controller
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
      $q =PurchaseOrders::query()
        ->join("suppliers", "suppliers.id", "=", "po_supp_id")
        ->where('purchase_orders.po_comp_id', $company_code)
        ->select('purchase_orders.id','purchase_orders.po_comp_id','po_order_title','po_type',
        'po_order_no','po_order_date','po_order_ref','po_pi_no','po_supp_id',
        'po_comments','po_m_curr','po_m_curr_rate','po_total_qty','po_total_amount',
        'po_total_amount_BDT','is_approved','supp_name');

      if($request->filled('supplier_id')){
        $q->where('po_supp_id', $request->get('supplier_id'));
      }
      if($request->filled('fromdate')){
        $q->where('po_order_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
          $fromdate = date('d-m-Y',strtotime($request->get('fromdate')));
      }
      if($request->filled('todate')){
        $q->where('po_order_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
        $todate = date('d-m-Y',strtotime($request->get('todate')));
      }
      $rows = $q->orderBy('purchase_orders.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'po_supp_id'     => $request->get('supplier_id'),
        'po_order_date'  => $request->get('fromdate'),
        'po_order_date'  => $request->get('todate'),
      ));
      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view ('/purchases/purchase_order_index', compact('rows','suppliers','fromdate','todate'));
    }


    public function purchase_modal_view($id)
    {
        $rows_m = PurchaseOrders::query()
          ->join("suppliers", "suppliers.id", "=", "po_supp_id")
          ->where('purchase_orders.id', $id)
          ->select('purchase_orders.id','purchase_orders.po_comp_id','po_order_title','po_type',
          'po_order_no','po_order_date','po_order_ref','po_pi_no','po_supp_id',
          'po_comments','po_m_curr','po_m_curr_rate','po_total_qty','po_total_amount',
          'po_total_amount_BDT','supp_name','supp_add1','supp_add2','supp_mobile')->first();

        $rows_d = PurchaseOrderDetails::query()
          ->join("items", "items.id", "=", "po_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('po_order_id', $id)
          ->select('item_code','item_name','po_item_qty','po_item_remarks',
          'po_item_price','itm_cat_name','po_d_curr','po_d_curr_rate')->get();

        return view('purchases.purchase_order_item_viewmodal',compact('rows_m','rows_d'));
    }

    public function purchase_order_form($pur_id,$currency)
    {
      $rows_m = PurchaseOrders::query()
        ->join("suppliers", "suppliers.id", "=", "po_supp_id")
        ->where('purchase_orders.id', $pur_id)
        ->select('purchase_orders.id','purchase_orders.po_comp_id','po_order_title','po_type',
        'po_order_no','po_order_date','po_order_ref','po_pi_no','po_supp_id',
        'po_comments','po_m_curr','po_m_curr_rate','po_total_qty','po_total_amount',
        'po_total_amount_BDT','supp_name','supp_add1', 'supp_add2','supp_mobile')->first();

      $rows_d = PurchaseOrderDetails::query()
          ->join("items", "items.id", "=", "po_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('po_order_id', $pur_id)
          ->select('item_code','item_name','po_item_qty','po_item_remarks',
          'po_item_price','itm_cat_name','po_d_curr','po_d_curr_rate')->get();

      $fileName = "purchase_".$pur_id;
      if ($currency == 1){
        $pdf = PDF::loadView('/purchases/reports/rpt_purchase_voucher_pdf',
        compact('rows_m','rows_d',), [], [
          'title' => $fileName,
        ]);
      }
      else{
        $pdf = PDF::loadView('/purchases/reports/rpt_purchase_voucher_bdt_pdf',
        compact('rows_m','rows_d',), [], [
          'title' => $fileName,
        ]);
      }
      return $pdf->stream($fileName,'.pdf');
    }


    public function get_pur_item($id){
    // return('ss'. $id);
      return new TransItemCodeResource(
        $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->where('items.id','=',$id)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName')->first()
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
       $purchase_no = $generalsController->make_purchase_order_ref($company_code);
       $currency = Currencies::query()
         ->where('curr_comp_id', $company_code)
         ->select('id','vCurrName','dCurrValue')
         ->orderBy('id','desc')->first();
       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $purchase_date = date('d-m-Y');
       $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
       return view('/purchases/purchase_order_create',compact('companies','currency','purchase_date','company_code',
       'suppliers','item_list','purchase_no','warehouse_list','stor_list'))->render();
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
      public function approved($pur_id)
      {
        try{
          $inputdata  = PurchaseOrders::find($pur_id);
          $inputdata->is_approved    = 1;
          $inputdata->is_approved_dt = Carbon::now();
          $inputdata->save();

        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage())->withInput();
        }
        return redirect()->back()->with('message', 'Purchase Approved Successful.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      $company_code = $request->company_code;
      $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
      $generalsController = new GeneralsController();
      $finan_yearId = $generalsController->getFinYearId($company_code,$purchase_date);

      //Item Validity Check
      $msg = $this->ItemValidityCheck($request);
      //return $msg ;
      if($msg != '') {
        return redirect()->back()->with('message',$msg)->withInput();
      }
      //return 'HELLO '.$msg;
      $purchaseorders = new PurchaseOrders();
      try{
          //return 'HELLO '.$msg;
          $id = $purchaseorders->storePurchaseOrder($request);
          //return $id;
          $purchaseorders->storePurchaseOrderItem($request,$id);
      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage())->withInput();
      }
      return redirect()->back()->with('message', 'Purchase Order Creation Successful.');
  }

  public function ItemValidityCheck($request)
  {
    $message = '';
    $detId = $request->input('ItemCodeId');
    //return count($detId);
     if ($detId){
       $i = 0;
        foreach ($detId as $key => $value){
          //return $request->ItemCodeId[$key];
          if ($request->ItemCodeId[$key] != ''){
            if ($request->Rate[$key] == ''){
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
      $generalsController = new GeneralsController();

      $item_list =  Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $company_code)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

      $rows_m =PurchaseOrders::query()
          ->where('po_comp_id', '=', $company_code)
          ->where('purchase_orders.id', $id )
          ->select('purchase_orders.id','po_comp_id','po_order_title','po_type','po_order_no',
          'po_order_date','po_order_ref','po_pi_no','po_supp_id','po_comments','po_total_qty',
          'po_total_amount','po_total_amount_BDT','po_m_curr','po_m_curr_rate')
          ->orderBy('purchase_orders.id', 'desc')->first();

      /*$row_wid = ItemPurchasesDetails::query()
          ->where('pur_order_id', $id)
          ->select('pur_warehouse_id','pur_storage_id')->first();*/

      $rows_d = PurchaseOrderDetails::query()
          ->join("items", "items.id", "=", "po_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('po_order_id', $id)->get();

      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view('/purchases/purchase_order_edit',
      compact('rows_m','rows_d','companies','item_list','suppliers'));

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
       $pur_order_title = 'PO';
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

       $purchaseorders = new PurchaseOrders();
       try{
          $inputdata  = PurchaseOrders::find($id);
          $inputdata->po_comp_id    = $company_code;
          $inputdata->po_type       = $request->POType;
          $inputdata->po_order_date = $purchase_date;
          $inputdata->po_order_ref  = $request->purchase_no;
          $inputdata->po_pi_no      = $request->pi_no;
          $inputdata->po_supp_id    = $supplier_id;
          $inputdata->po_m_curr     = $request->POType=='0'?'BDT':$request->currencyName;
          $inputdata->po_m_curr_rate = $request->POType=='0'?'1':$request->currencyValue;
          $inputdata->po_comments   = $request->comments;
          $inputdata->po_total_qty  = ($request->total_qty=='')?'0':$request->total_qty;
          $inputdata->po_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;
          $inputdata->po_total_amount_BDT  = ($request->total_amount_bdt=='')?'0':$request->total_amount_bdt;
          $inputdata->save();

          //Details Records
          $pur_order_no = $request->pur_order_no;
          PurchaseOrderDetails::where('po_order_id',$id)->delete();
          $purchaseorders->storePurchaseOrderItem($request,$id);

       }catch (\Exception $e){
           return redirect()->back()->with('error',$e->getMessage())->withInput();
       }
       return redirect()->back()->with('message', 'Purchase Order Updated Successful.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try{

            PurchaseOrderDetails::where('po_order_id',$id)->delete();
            PurchaseOrders::where('id',$id)->delete();


        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
