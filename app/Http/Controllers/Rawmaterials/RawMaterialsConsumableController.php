<?php

namespace App\Http\Controllers\Rawmaterials;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Rawmaterials\ConsumeMaterials;
use App\Models\Rawmaterials\ConsumeMaterialsDetails;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use App\Models\Purchases\Currencies;
use App\Models\Items\Items;
use App\Models\Items\Units;
use App\Models\Chartofaccounts;
use App\Http\Resources\TransItemCodeResource;

use Illuminate\Http\Request;
use App\Http\Requests;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Validator;
use Response; 
use DB;
use PDF;

class RawMaterialsConsumableController extends Controller
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
      $q =ConsumeMaterials::query()
        ->leftjoin("suppliers", "suppliers.id", "=", "r_cons_supp_id")
        ->where('consume_materials.r_cons_comp_id', $company_code)
        ->select('consume_materials.id','consume_materials.r_cons_comp_id','r_cons_fin_year_id','r_cons_order_title','r_cons_type','r_cons_order_no','r_cons_order_date','r_cons_order_ref','r_cons_pi_no','r_cons_supp_id','r_cons_comments','r_cons_total_qty','r_cons_total_amount','is_cons_confirmed','supp_name');

      if($request->filled('supplier_id')){
        $q->where('r_cons_supp_id', $request->get('supplier_id'));
      }
      if($request->filled('fromdate')){
        $q->where('r_cons_order_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
          $fromdate = date('d-m-Y',strtotime($request->get('fromdate')));
      }
      if($request->filled('todate')){
        $q->where('r_cons_order_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
        $todate = date('d-m-Y',strtotime($request->get('todate')));
      }
      $rows = $q->orderBy('consume_materials.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'r_cons_supp_id'     => $request->get('supplier_id'),
        'r_cons_order_date'  => $request->get('fromdate'),
        'r_cons_order_date'  => $request->get('todate'),
      ));
      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view ('/rawmaterials/consume_rawmaterials_index', compact('rows','suppliers','fromdate','todate'));
    }


    public function raw_modal_view($id)
    {
        $rows_m = ConsumeMaterials::query()
          ->where('consume_materials.id', $id)
          ->select('consume_materials.id','consume_materials.r_cons_comp_id','r_cons_fin_year_id','r_cons_order_title','r_cons_type','r_cons_order_no','r_cons_order_date','r_cons_order_ref','r_cons_pi_no','r_cons_supp_id','r_cons_comments','r_cons_total_qty','r_cons_total_amount')->first();

        $rows_d = ConsumeMaterialsDetails::query()
          ->join("items", "items.id", "=", "r_cons_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('r_cons_order_id', $id)
          ->select('item_code','item_name','r_cons_item_qty','r_cons_item_desc','r_cons_item_remarks',
          'r_cons_item_price','itm_cat_name')->get();

        return view('rawmaterials.consume_order_item_viewmodal',compact('rows_m','rows_d'));
    }

    public function acc_modal_view($voucher_no,$fin_year_id)
    {
       if ($voucher_no != '') {
          $generalscontroller = new GeneralsController();
          $id = $generalscontroller->getItemConsumeIdByVoucherNo($voucher_no,$fin_year_id);
          //return $id;
          $acctranscontroller = new AccountTransController();
          $rows_m = $acctranscontroller->modal_view_m($id);
          $rows_d = $acctranscontroller->modal_view_d($id);
          return view('accounts.acctrans_viewmodal',compact('rows_m','rows_d'));
        }else{
          return 'Posting not yet done';
        }
    }

      
    public function get_pur_item($id){
    // return('ss'. $id);
      return new TransItemCodeResource(
        $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("item_categories", "items.item_ref_cate_id", "=", "item_categories.id")
          ->where('items.id','=', $id) 
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
     //  return "sdg";
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
      $generalsController = new GeneralsController();
      $purchase_no = $generalsController->make_consumable_order_ref($company_code);
      $currency = Currencies::query()
        ->where('curr_comp_id', $company_code)
        ->select('id','vCurrName','dCurrValue')
        ->orderBy('id','desc')->first();
      $item_list = Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $company_code)  
     //  ->whereRaw("itm_cat_code like '30%' or itm_cat_code like '40%' or itm_cat_code like '50%'")
      ->select('items.id','item_code','item_name','item_desc','item_bar_code','itm_cat_origin','itm_cat_name',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();


      $unit_list =  Units::query() 
      ->where('unit_comp_id', '=', $company_code)  
      ->select('id','vUnitName')
      ->orderBy('vUnitName','asc')->get(); 

      $issue_date = date('d-m-Y');
      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();

      $debitHead = Chartofaccounts::whereIn('parent_id', [2887, 2890, 2893])->get();


      return view('/rawmaterials/consume_rawmaterials_create',compact('companies', 'debitHead', 'currency','issue_date','company_code','suppliers','item_list', 'unit_list','purchase_no','warehouse_list','stor_list'))->render();
     }

     public function create_old()  
     {
      //  return "sdg";
       $dropdownscontroller = new DropdownsController();
       $company_code = $dropdownscontroller->defaultCompanyCode();
       $companies  = $dropdownscontroller->comboCompanyAssignList();
       $dist_list  = $dropdownscontroller->comboDistrictsList();
       $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
       $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
       $generalsController = new GeneralsController();
       $purchase_no = $generalsController->make_consumable_order_ref($company_code);
       $currency = Currencies::query()
         ->where('curr_comp_id', $company_code)
         ->select('id','vCurrName','dCurrValue')
         ->orderBy('id','desc')->first();
       $item_list = Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)  
      //  ->whereRaw("itm_cat_code like '30%' or itm_cat_code like '40%' or itm_cat_code like '50%'")
       ->select('items.id','item_code','item_name','item_desc','item_bar_code','itm_cat_origin','itm_cat_name',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();


       $unit_list =  Units::query() 
       ->where('unit_comp_id', '=', $company_code)  
       ->select('id','vUnitName')
       ->orderBy('vUnitName','asc')->get(); 

       $issue_date = date('d-m-Y');
       $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
       return view('/rawmaterials/consume_rawmaterials_create',compact('companies','currency','issue_date','company_code','suppliers','item_list', 'unit_list','purchase_no','warehouse_list','stor_list'))->render();
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
      // return $request;
      $validator = Validator::make($request->all(),[
        'comments' => 'required',
      ]);
    
      if ($validator->fails()) {
          return redirect('itm-consumable-create')
                      ->withErrors($validator)
                      ->withInput();
      }

      $company_code = $request->company_code;
      $issue_date = date('Y-m-d',strtotime($request->issue_date));
      $generalsController = new GeneralsController();
      $finan_yearId = $generalsController->getFinYearId($company_code,$issue_date);

      //Item Validity Check
      $msg = $this->ItemValidityCheck($request);
      //return $msg ;
      if($msg != '') {
        return redirect()->back()->with('message',$msg)->withInput();
      }
      //return 'HELLO '.$msg;
      $rawmaterialsissues = new ConsumeMaterials();
      try{  
          //return 'HELLO '.$msg;
          $transaction = $rawmaterialsissues->rawMaterialIssued($request,$finan_yearId);
          //return $id;
          $acc_naration = $rawmaterialsissues->rawMaterialIssuedItem($request, $transaction->id);
          $this->ConsumeAccounting($request, $transaction->id, $finan_yearId,$acc_naration,0, $transaction, $request->debitHead);
         
      }catch (\Exception $e){
          return  $e->getMessage();
      }
      return redirect()->back()->with('message', 'Creation Successful.');
  }

  public function ConsumeAccounting($request, $trans_id, $finan_yearId,$acc_naration,$acc_voucher_no, $transaction, $debitHead){
    $rawmaterialsissues = new ConsumeMaterials();
    $generalscontroller = new GeneralsController();
    $company_code = $request->company_code;
    $supplier_id = $request->supplier_id;
    $issue_date = date('Y-m-d',strtotime($request->issue_date));
    $issue_order_no = $generalscontroller->get_consume_issue_orderno($trans_id);
    //update financial transaction for sales

     //Financial Transaction
   if ($acc_voucher_no > 0 ){
     $voucher_no = $acc_voucher_no;
   }else{ 
     $voucher_no = $generalscontroller->getMaxAccVoucherNo('CI',$company_code,$finan_yearId); 
     // getting max Voucher No
     $voucher_no = $voucher_no + 1;
   }

    // $supp_acc_id  = $generalscontroller->SupplierChartOfAccId($supplier_id);
    //$supp_name    = $generalscontroller->SupplierName($supplier_id);
   
    $records = $rawmaterialsissues->raw_fin_transaction($trans_id);
    $recCount = $records->count();

    // Insert Transaction Master Records
    $trans_fin_id = AccTransactions::insertGetId([
      'com_ref_id'    => $company_code,
      'voucher_date'  => $issue_date,
      'trans_type'    => 'CI',
      'voucher_no'    => $voucher_no,
      't_narration'   => $acc_naration,
      'fin_ref_id'    => $finan_yearId,
      'created_by'      => Auth::id(),
      'updated_by'      => Auth::id(),
      'created_at'      => Carbon::now(),
      'updated_at'      => Carbon::now(),
    ]);



    // AccTransactionDetails::create([
    //   'acc_trans_id'    => $trans_fin_id,
    //   'c_amount'        => $transaction->r_cons_total_amount,
    //   'chart_of_acc_id' => 2885,
    //   'acc_invoice_no'  => $transaction->r_cons_order_no,
    // ]);

    // AccTransactionDetails::create([
    //   'acc_trans_id'    => $trans_fin_id,
    //   'd_amount'        => $transaction->r_cons_total_amount,
    //   'chart_of_acc_id' => $debitHead,
    //   'acc_invoice_no'  => $transaction->r_cons_order_no,
    // ]);

    $total_vat = 0;
    foreach ($records as $rec){
      $sub_total = $rec->issue_value; 
      if($rec->sett_cat_name == 'CONSUME EXP'){
         AccTransactionDetails::create([
           'acc_trans_id'    => $trans_fin_id,
           'd_amount'        => $sub_total,
           'chart_of_acc_id' => $rec->sett_accid,
           'acc_invoice_no'  => $issue_order_no,
       ]);
      }else if($rec->sett_cat_name == 'CONSUME ASSET'){
         AccTransactionDetails::create([
           'acc_trans_id'    => $trans_fin_id,
           'd_amount'        => $sub_total,
           'chart_of_acc_id' => $rec->sett_accid,
           'acc_invoice_no'  => $issue_order_no,
       ]);
     }else{
         AccTransactionDetails::create([
           'acc_trans_id'    => $trans_fin_id,
           'c_amount'        => $sub_total,
           'chart_of_acc_id' => $rec->sett_accid,
           'acc_invoice_no'  => $issue_order_no,
         ]);
      } 
    }
 }

  public function rawMaterialAccounting($request, $trans_id, $finan_yearId,$acc_naration,$acc_voucher_no){
     $rawmaterialsissues = new ConsumeMaterials();
     $generalscontroller = new GeneralsController();
     $company_code = $request->company_code;
     $supplier_id = $request->supplier_id;
     $issue_date = date('Y-m-d',strtotime($request->issue_date));
     $issue_order_no = $generalscontroller->get_consume_issue_orderno($trans_id);
     //update financial transaction for sales

      //Financial Transaction
    if ($acc_voucher_no > 0 ){
      $voucher_no = $acc_voucher_no;
    }else{ 
      $voucher_no = $generalscontroller->getMaxAccVoucherNo('CI',$company_code,$finan_yearId); 
      // getting max Voucher No
      $voucher_no = $voucher_no + 1;
    }
 
     // $supp_acc_id  = $generalscontroller->SupplierChartOfAccId($supplier_id);
     //$supp_name    = $generalscontroller->SupplierName($supplier_id);
    
     $records = $rawmaterialsissues->raw_fin_transaction($trans_id);
     $recCount = $records->count();

     // Insert Transaction Master Records
     $trans_fin_id = AccTransactions::insertGetId([
       'com_ref_id'    => $company_code,
       'voucher_date'  => $issue_date,
       'trans_type'    => 'CI',
       'voucher_no'    => $voucher_no,
       't_narration'   => $acc_naration,
       'fin_ref_id'    => $finan_yearId,
       'created_by'      => Auth::id(),
       'updated_by'      => Auth::id(),
       'created_at'      => Carbon::now(),
       'updated_at'      => Carbon::now(),
     ]);
 
     $total_vat = 0;
     foreach ($records as $rec){
       $sub_total = $rec->issue_value; 
       if($rec->sett_cat_name == 'CONSUME EXP'){
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'd_amount'        => $sub_total,
            'chart_of_acc_id' => $rec->sett_accid,
            'acc_invoice_no'  => $issue_order_no,
        ]);
       }else if($rec->sett_cat_name == 'CONSUME ASSET'){
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'd_amount'        => $sub_total,
            'chart_of_acc_id' => $rec->sett_accid,
            'acc_invoice_no'  => $issue_order_no,
        ]);
      }else{
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'c_amount'        => $sub_total,
            'chart_of_acc_id' => $rec->sett_accid,
            'acc_invoice_no'  => $issue_order_no,
          ]);
       } 
     }
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
      ->join("view_item_stocks", "items.id", "=", "item_ref_id") 
      ->where('item_ref_comp_id', '=', $company_code)
      // ->whereRaw("itm_cat_code like '30%' or itm_cat_code like '40%' or itm_cat_code like '50%'")
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','view_item_stocks.stock as item_bal_stock','itm_cat_name','itm_cat_origin')
      ->orderBy('item_name','asc')->get();

      $unit_list =  Units::query() 
      ->where('unit_comp_id', '=', $company_code)  
      ->select('id','vUnitName')
      ->orderBy('vUnitName','asc')->get(); 

      $rows_m =ConsumeMaterials::query()
          ->where('r_cons_comp_id', '=', $company_code)
          ->where('consume_materials.id', $id )
          ->select('consume_materials.id','r_cons_comp_id','r_cons_order_title','r_cons_type','r_cons_order_no',
          'r_cons_order_date','r_cons_order_ref','r_cons_pi_no','r_cons_supp_id','r_cons_comments','r_cons_total_qty','r_cons_total_amount')
          ->orderBy('consume_materials.id', 'desc')->first();
 
      $rows_d = ConsumeMaterialsDetails::query()
          ->join("items", "items.id", "=", "r_cons_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('r_cons_order_id', $id)->get();

      // $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view('/rawmaterials/consume_rawmaterials_edit',
      compact('rows_m','rows_d','companies','item_list','unit_list'));

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
       $pur_order_title = 'CI';
       $id = $request->id;
       $company_code = $request->company_code; 
       $issue_date = date('Y-m-d',strtotime($request->issue_date));
       $generalscontroller = new GeneralsController();
       $finan_yearId = $generalscontroller->getFinYearId($company_code,$issue_date);

       // Validate the Field
       $msg = $this->ItemValidityCheck($request);
       if($msg != '') {
         return redirect()->back()->with('message',$msg)->withInput();
       }
 
       $rawmaterialsissues = new ConsumeMaterials();

       try{
          $inputdata  = ConsumeMaterials::find($id);
          $inputdata->r_cons_comp_id    = $company_code;
          $inputdata->r_cons_type       = $request->POType;
          $inputdata->r_cons_order_date = $issue_date;
          $inputdata->r_cons_order_ref  = $request->purchase_no; 
          $inputdata->r_cons_comments   = $request->comments;
          $inputdata->r_cons_total_qty  = ($request->total_qty=='')?'0':$request->total_qty; 
          $inputdata->r_cons_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;
          $inputdata->save();

          //Details Records
          $pur_order_no = $request->pur_order_no;
          ConsumeMaterialsDetails::where('r_cons_order_id',$id)->delete();
          $acc_naration =  $rawmaterialsissues->rawMaterialIssuedItem($request,$id); 
          // return $inv_no;
          $inv_no  = $generalscontroller->getVoucherNoByConsumeItemOrderNo($id);
          $acc_trans_id = $generalscontroller->getItemConsumeIdByVoucherNo($inv_no,$finan_yearId);
          $acc_voucher_no = $generalscontroller->getAccVoucherNoByAccTransId($acc_trans_id);
          if($acc_trans_id>0) {
            AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
            AccTransactions::where('id',$acc_trans_id)->delete();
          } 
          $this->rawMaterialAccounting($request,$id,$finan_yearId,$acc_naration,$acc_voucher_no);

       }catch (\Exception $e){
           //return redirect()->back()->with('error',$e->getMessage())->withInput();
           return  $e->getMessage();
       }
       return redirect()->back()->with('message', 'Updated Successful.');

    }

    public function issue_confirmed($id)
    {
 
      $inputdata  = ConsumeMaterials::find($id);
      $inputdata->is_cons_confirmed  = 1;
      $inputdata->is_cons_confirmed_dt  = Carbon::now();        
      $inputdata->save();
      return back()->with('message', 'Confirmation Successful.');
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

          $issue_order_id = $id;
          //return $isconfirmed;
          $generalscontroller = new GeneralsController(); 
          //delete financial transaction
          $inv_no  = $generalscontroller->getVoucherNoByConsumeItemOrderNo($issue_order_id);
          //return $inv_no;
          $finan_yearId  = $generalscontroller->getConsumeItemFinanYearByOrderNo($issue_order_id);
            
          $acc_trans_id = $generalscontroller->getItemConsumeIdByVoucherNo($inv_no,$finan_yearId);
          if($acc_trans_id>0) {
                AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
                AccTransactions::where('id',$acc_trans_id)->delete();
          }

          ConsumeMaterialsDetails::where('r_cons_order_id',$id)->delete();
          ConsumeMaterials::where('id',$id)->delete(); 
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }

}
