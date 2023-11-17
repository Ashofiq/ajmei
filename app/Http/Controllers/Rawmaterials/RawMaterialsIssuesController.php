<?php

namespace App\Http\Controllers\Rawmaterials;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Rawmaterials\RawMaterialsIssues;
use App\Models\Rawmaterials\RawMaterialsIssuesDetails;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use App\Models\Purchases\Currencies;
use App\Models\Items\Items;
use App\Models\Items\Units;

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

class RawMaterialsIssuesController extends Controller
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
      $item_id = '';

      $fromdate = date('d-m-Y');
      $todate = date('d-m-Y');
      $q = RawMaterialsIssues::query()
        ->leftjoin("suppliers", "suppliers.id", "=", "r_issue_supp_id")
        ->where('raw_materials_issues.r_issue_comp_id', $company_code)
        ->select('raw_materials_issues.id', 
          'raw_materials_issues.r_issue_comp_id',
          'r_issue_fin_year_id','r_issue_order_title','r_issue_type','r_issue_order_no',
          'r_issue_order_date','r_issue_order_ref','r_issue_pi_no','r_issue_supp_id',
          'r_issue_comments','r_issue_m_curr','r_issue_m_curr_rate','r_issue_total_qty',
          'r_issue_total_amount','is_confirmed','supp_name');

      if($request->filled('supplier_id')){
        $q->where('r_issue_supp_id', $request->get('supplier_id'));
      }
      
      if($request->filled('item_id')){
        $item_id = $request->get('item_id');
        $q->join("raw_materials_issues_details", "raw_materials_issues_details.r_issue_order_id", "=", "raw_materials_issues.id");
        $q->where('r_issue_item_id', $request->get('item_id'));
      }

      if($request->filled('fromdate')){
        $q->where('r_issue_order_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
          $fromdate = date('d-m-Y',strtotime($request->get('fromdate')));
      }
      if($request->filled('todate')){
        $q->where('r_issue_order_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
        $todate = date('d-m-Y',strtotime($request->get('todate')));
      }

      $uniqueRawId = DB::table('raw_materials_issues_details')
        ->select("*")
        ->leftjoin("items", "items.id", "=", "r_issue_item_id")
        ->get()
        ->unique('r_issue_item_id');


      if($request->filled('fromdate')){
        $rows = $q->orderBy('r_issue_order_date', 'ASC')->paginate(5000)->setpath('');
      }else{
          $rows = $q->orderBy('r_issue_order_date', 'ASC')->paginate(10)->setpath('');
      }
      
      // return $rows;
      
      $rows->appends(array(
        'r_issue_supp_id'     => $request->get('supplier_id'),
        'r_issue_order_date'  => $request->get('fromdate'),
        'r_issue_order_date'  => $request->get('todate'),
      ));
      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view ('/rawmaterials/issue_rawmaterials_index', compact('rows', 'item_id', 'uniqueRawId', 'suppliers','fromdate','todate'));
    }


    public function raw_modal_view($id)
    {
        $rows_m = RawMaterialsIssues::query()
          ->where('raw_materials_issues.id', $id)
          ->select('raw_materials_issues.id','raw_materials_issues.r_issue_comp_id','r_issue_fin_year_id','r_issue_order_title','r_issue_type','r_issue_order_no','r_issue_order_date','r_issue_order_ref','r_issue_pi_no','r_issue_supp_id',
          'r_issue_comments','r_issue_m_curr','r_issue_m_curr_rate','r_issue_total_qty','r_issue_total_amount')->first();

        $rows_d = RawMaterialsIssuesDetails::query()
          ->join("items", "items.id", "=", "r_issue_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('r_issue_order_id', $id)
          ->select('item_code','item_name','r_issue_item_qty','r_issue_item_desc','r_issue_item_remarks',
          'r_issue_item_price','itm_cat_name','r_issue_d_curr','r_issue_d_curr_rate')->get();

        return view('rawmaterials.issue_order_item_viewmodal',compact('rows_m','rows_d'));
    }

    public function acc_modal_view($voucher_no,$fin_year_id)
    {
       if ($voucher_no != '') {
          $generalscontroller = new GeneralsController();
          $id = $generalscontroller->getRawIssueIdByVoucherNo($voucher_no,$fin_year_id);
          //return $id;
          $acctranscontroller = new AccountTransController();
          $rows_m = $acctranscontroller->modal_view_m($id);
          $rows_d = $acctranscontroller->modal_view_d($id);
          return view('accounts.acctrans_viewmodal', compact('rows_m','rows_d'));
        }else{
          return 'Posting not yet done';
        }
    }

      
    public function get_pur_item($id){
      $price = 0;
      $qty = 0;
      $m = DB::table('raw_materials_receives_details')
      ->selectRaw('raw_item_id, COUNT(raw_item_id) as count, SUM(raw_item_qty) as qty, 
        SUM(raw_item_price) as price')
      ->where('raw_item_id', $id)
      ->groupBy('raw_item_id')
      ->get();

      $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("item_categories", "items.item_ref_cate_id", "=", "item_categories.id")
          ->where('items.id','=', $id) 
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName')->first();
      if (COUNT($m) > 0) {
        $price = $m[0]->price;
        $qty = $m[0]->qty;
        $itms->averagePrice = $price / $m[0]->count;
      }else{
        $itms->averagePrice = 0;
      }
      
      return new TransItemCodeResource(
        $itms
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
       $purchase_no = $generalsController->make_raw_issue_order_ref($company_code);
       $currency = Currencies::query()
         ->where('curr_comp_id', $company_code)
         ->select('id','vCurrName','dCurrValue')
         ->orderBy('id','desc')->first();
       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)  
      ->whereRaw("itm_cat_code not like '20%'")
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $unit_list =  Units::query()  
       ->where('unit_comp_id', '=', $company_code)  
       ->select('id','vUnitName') 
       ->orderBy('vUnitName','asc')->get();  

       $issue_date = date('d-m-Y');
       $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
       return view('/rawmaterials/issue_rawmaterials_create',compact('companies','currency','issue_date','company_code','suppliers','item_list', 'unit_list','purchase_no','warehouse_list','stor_list'))->render();
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
      $rawmaterialsissues = new RawMaterialsIssues();
      try{  
          //return 'HELLO '.$msg;
          $id = $rawmaterialsissues->rawMaterialIssued($request,$finan_yearId);
          //return $id;
          $acc_naration = $rawmaterialsissues->rawMaterialIssuedItem($request,$id);
          $this->rawMaterialAccounting($request,$id,$finan_yearId,$acc_naration,0);
         
      }catch (\Exception $e){
          return  $e->getMessage();
      }
      return redirect()->back()->with('message', 'Creation Successful.');
  }

  public function rawMaterialAccounting($request, $trans_id, $finan_yearId,$acc_naration,$acc_voucher_no){
     $rawmaterialsissues = new RawMaterialsIssues();
     $generalscontroller = new GeneralsController();
     $company_code = $request->company_code;
     $supplier_id = $request->supplier_id;
     $issue_date = date('Y-m-d',strtotime($request->issue_date));
     $issue_order_no = $generalscontroller->get_raw_issue_orderno($trans_id);
     //update financial transaction for sales

      //Financial Transaction
    if ($acc_voucher_no > 0 ){
      $voucher_no = $acc_voucher_no;
    }else{ 
      $voucher_no = $generalscontroller->getMaxAccVoucherNo('GI',$company_code,$finan_yearId); 
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
       'trans_type'    => 'GI',
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
       if($rec->sett_cat_name == 'RAW PRODUCTION'){
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
      ->where('item_ref_comp_id', '=', $company_code)
      ->whereRaw("itm_cat_code not like '20%'")
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

      $unit_list =  Units::query() 
      ->where('unit_comp_id', '=', $company_code)  
      ->select('id','vUnitName')
      ->orderBy('vUnitName','asc')->get(); 

      $rows_m = RawMaterialsIssues::query()
          ->where('r_issue_comp_id', '=', $company_code)
          ->where('raw_materials_issues.id', $id )
          ->select('raw_materials_issues.id','r_issue_comp_id','r_issue_order_title','r_issue_type','r_issue_order_no',
          'r_issue_order_date','r_issue_order_ref','r_issue_pi_no','r_issue_supp_id','r_issue_comments','r_issue_total_qty','r_issue_m_curr','r_issue_m_curr_rate','r_issue_total_amount')
          ->orderBy('raw_materials_issues.id', 'desc')->first();
 
      $rows_d = RawMaterialsIssuesDetails::query()
          ->join("items", "items.id", "=", "r_issue_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('r_issue_order_id', $id)->get();

      // $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view('/rawmaterials/issue_rawmaterials_edit',
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
       $pur_order_title = 'GI';
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
 
       $rawmaterialsissues = new RawMaterialsIssues();

       try{
          $inputdata  = RawMaterialsIssues::find($id);
          $inputdata->r_issue_comp_id    = $company_code;
          $inputdata->r_issue_type       = $request->POType;
          $inputdata->r_issue_order_date = $issue_date;
          $inputdata->r_issue_order_ref  = $request->purchase_no; 
          $inputdata->r_issue_m_curr     = 'BDT';
          $inputdata->r_issue_m_curr_rate = '1';
          $inputdata->r_issue_comments   = $request->comments;
          $inputdata->r_issue_total_qty  = ($request->total_qty=='')?'0':$request->total_qty; 
          $inputdata->r_issue_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;
          $inputdata->save();

          //Details Records
          $pur_order_no = $request->pur_order_no;
          RawMaterialsIssuesDetails::where('r_issue_order_id',$id)->delete();
          $acc_naration =  $rawmaterialsissues->rawMaterialIssuedItem($request,$id); 
          // return $inv_no;
          $inv_no  = $generalscontroller->getVoucherNoByRawIssueOrderNo($id);
          $acc_trans_id = $generalscontroller->getRawIssueIdByVoucherNo($inv_no,$finan_yearId);
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
      $inputdata  = RawMaterialsIssues::find( $id);
      $inputdata->is_confirmed  = 1;
      $inputdata->is_confirmed_dt  = Carbon::now();        
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
          $inv_no  = $generalscontroller->getVoucherNoByRawIssueOrderNo($issue_order_id);

          //return $inv_no;
          $finan_yearId  = $generalscontroller->getRawIssueFinanYearByOrderNo($issue_order_id);
         // return $finan_yearId;  
          $acc_trans_id = $generalscontroller->getRawIssueIdByVoucherNo($inv_no,$finan_yearId);
         // return $acc_trans_id; 
          if($acc_trans_id>0) {
              AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
              AccTransactions::where('id',$acc_trans_id)->delete();
          }

         RawMaterialsIssuesDetails::where('r_issue_order_id',$id)->delete();
         RawMaterialsIssues::where('id',$id)->delete(); 
        }catch (\Exception $e){
           // return redirect()->back()->with('error',$e->getMessage());
             return  $e->getMessage();
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }

}
