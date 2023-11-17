<?php

namespace App\Http\Controllers\FinishGoods;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\FinishGoods\FinishGoodsReceives;
use App\Models\FinishGoods\FinishGoodsReceivesDetails;

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

class FinishGoodsReceivesController extends Controller
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

      $fromdate = date('01-m-Y');
      $todate = date('d-m-Y');
      $q =FinishGoodsReceives::query()  
        ->join("raw_materials_issues", "raw_materials_issues.id", "=", "f_rec_issue_prod_id")
        ->join("finish_goods_receives_details", "finish_goods_receives.id", "=", "finish_goods_receives_details.f_rec_order_id")
        ->where('finish_goods_receives.f_rec_comp_id', $company_code)
        ->select('finish_goods_receives_details.f_rec_item_pcs','finish_goods_receives.id','finish_goods_receives.f_rec_comp_id','f_rec_fin_year_id','f_rec_order_title','f_rec_type','f_rec_order_no','f_rec_order_date','f_rec_order_ref','f_rec_comments','f_rec_m_curr','f_rec_m_curr_rate','f_rec_total_qty','f_rec_total_amount','finish_goods_receives.is_confirmed','r_issue_order_ref');
      if(!$request->filled('fromdate')){
        $q->where('f_rec_order_date', $todate);
      }

      
      if($request->filled('fromdate')){
        $q->where('f_rec_order_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
          $fromdate = date('d-m-Y',strtotime($request->get('fromdate')));
      }
      if($request->filled('todate')){
        $q->where('f_rec_order_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
        $todate = date('d-m-Y',strtotime($request->get('todate')));
      }
      $rows = $q->orderBy('f_rec_order_date', 'ASC')->get();
      // $rows->appends(array( 
      //   'f_rec_order_date'  => $request->get('fromdate'),
      //   'f_rec_order_date'  => $request->get('todate'),
      // ));

      //$suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view ('/finishgoods/finish_goods_rec_index', compact('rows','fromdate','todate'));
    }  


    public function raw_modal_view($id)
    {
        $rows_m = FinishGoodsReceives::query()
          ->where('finish_goods_receives.id', $id)
          ->select('finish_goods_receives.id','finish_goods_receives.f_rec_comp_id','f_rec_fin_year_id','f_rec_order_title','f_rec_type','f_rec_order_no','f_rec_order_date','f_rec_order_ref','f_rec_comments','f_rec_m_curr','f_rec_m_curr_rate','f_rec_total_qty','f_rec_total_amount')->first();

        $rows_d = FinishGoodsReceivesDetails::query()
          ->join("items", "items.id", "=", "f_rec_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('f_rec_order_id', $id)
          ->select('item_code','item_name','f_rec_item_qty','f_rec_item_desc','f_rec_item_remarks',
          'f_rec_item_price','f_rec_item_weight','f_rec_item_pcs','itm_cat_name','f_rec_d_curr','f_rec_d_curr_rate')->get();

        return view('finishgoods.finish_goods_rec_viewmodal',compact('rows_m','rows_d'));
    }

    public function acc_modal_view($voucher_no,$fin_year_id)
    {
       if ($voucher_no != '') {
          $generalscontroller = new GeneralsController();
          $id = $generalscontroller->getFinGoodsRecIdByVoucherNo($voucher_no,$fin_year_id);
          //return $id;
          $acctranscontroller = new AccountTransController();
          $rows_m = $acctranscontroller->modal_view_m($id);
          $rows_d = $acctranscontroller->modal_view_d($id);
          return view('accounts.acctrans_viewmodal',compact('rows_m','rows_d'));
        }else{
          return 'Posting not yet done';
        }
    }

    public function yearlyReports(Request $request)
    {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $unit_list = $dropdownscontroller->comboUnitsList($company_code);

      $fromdate = date('01-m-Y');
      $todate = date('d-m-Y');
      $q = FinishGoodsReceives::query()  
        ->join("raw_materials_issues", "raw_materials_issues.id", "=", "f_rec_issue_prod_id")
        ->join("finish_goods_receives_details", "finish_goods_receives.id", "=", "finish_goods_receives_details.f_rec_order_id")
        ->where('finish_goods_receives.f_rec_comp_id', $company_code);

        if($request->filled('fromdate')){
          $q->where('finish_goods_receives.f_rec_order_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
            $fromdate = date('d-m-Y',strtotime($request->get('fromdate')));
        }
        if($request->filled('todate')){
          $q->where('finish_goods_receives.f_rec_order_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
          $todate = date('d-m-Y',strtotime($request->get('todate')));
        }


        $q->select(DB::raw('MONTH(finish_goods_receives.f_rec_order_date) as month'), DB::raw('YEAR(finish_goods_receives.f_rec_order_date) as year'), 
          DB::raw('SUM(finish_goods_receives_details.f_rec_item_pcs) qty'), DB::raw('SUM(f_rec_total_qty) as weight'))
        ->groupBy('month', 'year');
      
      $rows = $q->orderBy('year', 'asc')->get();
      // $rows->appends(array( 
      //   'f_rec_order_date'  => $request->get('fromdate'),
      //   'f_rec_order_date'  => $request->get('todate'),
      // ));

      // return $rows;
      //$suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view ('/finishgoods/report/yearly_report', compact('rows','fromdate','todate'));
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
       $dropdownscontroller = new DropdownsController();
       $company_code = $dropdownscontroller->defaultCompanyCode();
       $companies  = $dropdownscontroller->comboCompanyAssignList();
       $dist_list  = $dropdownscontroller->comboDistrictsList();
       $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
       $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
       $generalsController = new GeneralsController();
       $purchase_no = $generalsController->make_raw_issue_order_ref($company_code);
       $issue_no = ''; 
       $rows_raw =RawMaterialsIssues::query()
        ->join("raw_materials_issues_details", "raw_materials_issues.id", "=", "r_issue_order_id")  
        ->where('raw_materials_issues.id','-1') 
        ->select('raw_materials_issues.id')->get();

       $currency = Currencies::query()
         ->where('curr_comp_id', $company_code)
         ->select('id','vCurrName','dCurrValue')
         ->orderBy('id','desc')->first();
       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)  
       ->whereRaw("itm_cat_code like '20%'")
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $unit_list =  Units::query() 
       ->where('unit_comp_id', '=', $company_code)  
       ->select('id','vUnitName')
       ->orderBy('vUnitName', 'asc')->get(); 

       $issue_to_prod_list = RawMaterialsIssues::query()
       ->where('r_issue_comp_id', '=', $company_code)
       ->where('is_confirmed', '=', 1)
       ->where('is_closed', '=', 0)->get();
        
       $rec_date = date('d-m-Y');
        // $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
       return view('/finishgoods/finish_goods_rec_create',compact('companies','currency','rec_date','company_code','item_list', 'unit_list','issue_no','issue_to_prod_list','purchase_no','warehouse_list','stor_list','rows_raw'))->render();
      }

  public function getIssueProdData(Request $request)
  { 
    $id = $request->issue_no;
    $rows_raw =RawMaterialsIssues::query()
    ->join("raw_materials_issues_details", "raw_materials_issues.id", "=", "r_issue_order_id")
    ->join("items", "items.id", "=", "r_issue_item_id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")       
    ->where('raw_materials_issues.id',$id) 
    ->select('raw_materials_issues.id','itm_cat_name','raw_materials_issues.r_issue_comp_id','r_issue_fin_year_id','r_issue_order_no','r_issue_order_date','r_issue_order_ref','r_issue_total_qty','r_issue_total_amount','is_confirmed','raw_materials_issues_details.id as issueProdDetId','r_issue_order_id','r_issue_item_id','item_code','item_name','r_issue_item_qty','r_issue_item_qty_rec','r_issue_item_unit','r_issue_item_desc','r_issue_item_remarks','r_issue_item_price','itm_cat_name')->get();
 
    $dropdownscontroller = new DropdownsController();
    $company_code = $dropdownscontroller->defaultCompanyCode();
    $companies  = $dropdownscontroller->comboCompanyAssignList();
    $dist_list  = $dropdownscontroller->comboDistrictsList();
    $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
    $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
    $generalsController = new GeneralsController();
    $purchase_no = $generalsController->make_raw_issue_order_ref($company_code);
    $issue_no = $id;

    $currency = Currencies::query()
    ->where('curr_comp_id', $company_code)
    ->select('id','vCurrName','dCurrValue')
    ->orderBy('id','desc')->first();
    
    //Production Item List
    $item_list =  Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $company_code)  
      ->whereRaw("itm_cat_code like '20%'")
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

    $unit_list = Units::query() 
      ->where('unit_comp_id', '=', $company_code)  
      ->select('id','vUnitName')
      ->orderBy('vUnitName','asc')->get(); 

    $issue_to_prod_list = RawMaterialsIssues::query()
      ->where('r_issue_comp_id', '=', $company_code)
      ->where('is_confirmed', '=', 1)
      ->where('is_closed', '=', 0)->get();
       
    $rec_date = date('d-m-Y');

      return view('/finishgoods/finish_goods_rec_create',compact('companies','currency','rec_date','company_code','item_list', 'unit_list','issue_no','issue_to_prod_list','purchase_no','warehouse_list','stor_list','rows_raw'))->render();

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

      $company_code = $request->company_code;
      $rec_date = date('Y-m-d',strtotime($request->rec_date));
      $generalsController = new GeneralsController();
      $finan_yearId = $generalsController->getFinYearId($company_code,$rec_date);

      //Item Validity Check
      $msg = $this->ItemValidityCheck($request);
      //return $msg ;
      if($msg != '') {
        return redirect()->back()->with('message',$msg)->withInput();
      }
      //return 'HELLO '.$msg;
      $finishgoodsreceives = new FinishGoodsReceives();
      try{  
          //return 'HELLO '.$msg;
          $id = $finishgoodsreceives->finishGoodsReceived($request,$finan_yearId);
          //return $id;
          $acc_naration = $finishgoodsreceives->finishGoodsReceivedItem($request,$id);
          $this->finishGoodsAccounting($request,$id,$finan_yearId,$acc_naration,0);
         
      }catch (\Exception $e){
          return  $e->getMessage();
      }
      return redirect()->back()->with('message', 'Creation Successful.');
  }

  public function finishGoodsAccounting($request, $trans_id, $finan_yearId,$acc_naration,$acc_voucher_no){
     $finishgoodsreceives = new FinishGoodsReceives();
     $generalscontroller = new GeneralsController();
     $company_code = $request->company_code;
     $supplier_id = $request->supplier_id;
     $rec_date = date('Y-m-d',strtotime($request->rec_date));
     $issue_order_no = $generalscontroller->get_fin_goods_rec_orderno($trans_id);
     //update financial transaction for sales

      //Financial Transaction
    if ($acc_voucher_no > 0 ){
      $voucher_no = $acc_voucher_no;
    }else{ 
      $voucher_no = $generalscontroller->getMaxAccVoucherNo('FR',$company_code,$finan_yearId); 
      // getting max Voucher No
      $voucher_no = $voucher_no + 1;
    }
 
     // $supp_acc_id  = $generalscontroller->SupplierChartOfAccId($supplier_id);
     //$supp_name    = $generalscontroller->SupplierName($supplier_id);
    
     $records = $finishgoodsreceives->fin_transaction($trans_id);
     $recCount = $records->count();

     // Insert Transaction Master Records
     $trans_fin_id = AccTransactions::insertGetId([
       'com_ref_id'    => $company_code,
       'voucher_date'  => $rec_date,
       'trans_type'    => 'FR',
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
       if($rec->sett_cat_name == 'FIN PRODUCTION'){
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
      ->whereRaw("itm_cat_code like '20%'")
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

    $unit_list =  Units::query() 
      ->where('unit_comp_id', '=', $company_code)  
      ->select('id','vUnitName')
      ->orderBy('vUnitName','asc')->get(); 

    $rows_m =FinishGoodsReceives::query()
          ->where('f_rec_comp_id', '=', $company_code)
          ->where('finish_goods_receives.id', $id )
          ->select('finish_goods_receives.id','f_rec_comp_id','f_rec_order_title','f_rec_type','f_rec_order_no','f_rec_order_date','f_rec_order_ref','f_rec_comments','f_rec_total_qty','f_rec_m_curr','f_rec_m_curr_rate','f_rec_total_amount','f_rec_issue_prod_id')
          ->orderBy('finish_goods_receives.id', 'desc')->first();
      
      
    $sql = "select finish_goods_receives.id,finish_goods_receives.f_rec_comp_id,f_rec_order_title,f_rec_order_no,f_rec_order_date,f_rec_comments,f_rec_total_qty,f_rec_total_amount,f_rec_issu_prd_det_id,f_rec_item_id,f_rec_item_desc,f_rec_rate,f_rec_d_curr,f_rec_d_curr_rate,f_rec_item_unit,f_rec_item_price,f_rec_item_qty,f_rec_item_remarks,fin.item_code as fin_item_code,fin.item_bar_code as fin_item_bar_code,fin.item_name as fin_item_name,fin.item_desc as fin_item_desc,r_issue_item_id,raw.item_code as raw_item_code,raw.item_name as raw_item_name,raw.item_desc as raw_item_desc,r_issue_item_desc,r_issue_item_unit,r_issue_item_price,r_issue_item_qty,r_issue_item_qty_rec,f_rec_item_weight,f_rec_item_pcs from `finish_goods_receives_details`
    inner join `finish_goods_receives` on `finish_goods_receives`.`id` = `f_rec_order_id`
    inner join `raw_materials_issues` on `raw_materials_issues`.`id` = `finish_goods_receives`.`f_rec_issue_prod_id`
    inner join `raw_materials_issues_details` on `raw_materials_issues`.`id` = `r_issue_order_id` and `raw_materials_issues_details`.`id` = f_rec_issu_prd_det_id
    inner join `items` fin on `fin`.`id` = `f_rec_item_id`  
    inner join `item_categories` fin_cat on `fin_cat`.`id` = fin.`item_ref_cate_id`
    inner join `items` raw on `raw`.`id` = `r_issue_item_id` 
    inner join `item_categories` raw_cat on `raw_cat`.`id` = raw.`item_ref_cate_id`
    where `f_rec_order_id` = $id";
    $rows_d = DB::select($sql);


      // $rows_d = FinishGoodsReceivesDetails::query()
      //     ->join("items", "items.id", "=", "f_rec_item_id")
      //     ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      //     ->where('f_rec_order_id', $id)->get();

      // $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view('/finishgoods/finish_goods_rec_edit',
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
       $pur_order_title = 'FR';
       $id = $request->id;
       $company_code = $request->company_code; 
       $rec_date = date('Y-m-d',strtotime($request->rec_date));
       $generalscontroller = new GeneralsController();
       $finan_yearId = $generalscontroller->getFinYearId($company_code,$rec_date);

       // Validate the Field
       $msg = $this->ItemValidityCheck($request);
       if($msg != '') {
         return redirect()->back()->with('message',$msg)->withInput();
       }
 
       $finishgoodsreceives = new FinishGoodsReceives(); 
       
       try{
          $inputdata  = FinishGoodsReceives::find($id);
          $inputdata->f_rec_comp_id    = $company_code;
          $inputdata->f_rec_issue_prod_id = $request->res_issue_id;
          $inputdata->f_rec_type       = $request->POType;
          $inputdata->f_rec_order_date = $rec_date;
          $inputdata->f_rec_order_ref  = $request->purchase_no; 
          $inputdata->f_rec_m_curr     = 'BDT';
          $inputdata->f_rec_m_curr_rate = '1';
          $inputdata->f_rec_comments   = $request->comments;
          $inputdata->f_rec_total_qty  = ($request->total_qty=='')?'0':$request->total_qty; 
          $inputdata->f_rec_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;
          $inputdata->save();

          //Details Records
          $pur_order_no = $request->pur_order_no;
          FinishGoodsReceivesDetails::where('f_rec_order_id',$id)->delete();
          $acc_naration =  $finishgoodsreceives->finishGoodsReceivedItem($request,$id); 
          // return $inv_no;
          $inv_no  = $generalscontroller->getVoucherNoByFinGoodsRecOrderNo($id);
          $acc_trans_id = $generalscontroller->getFinGoodsRecIdByVoucherNo($inv_no,$finan_yearId);
          $acc_voucher_no = $generalscontroller->getAccVoucherNoByAccTransId($acc_trans_id);
          if($acc_trans_id>0) {
            AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
            AccTransactions::where('id',$acc_trans_id)->delete();
          } 
          $this->finishGoodsAccounting($request,$id,$finan_yearId,$acc_naration,$acc_voucher_no);

       }catch (\Exception $e){
           //return redirect()->back()->with('error',$e->getMessage())->withInput();
           return  $e->getMessage();
       }
       return redirect()->back()->with('message', 'Updated Successful.');

    }

    public function issue_confirmed($id)
    {
 
      $inputdata  = FinishGoodsReceives::find( $id);
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
          $inv_no  = $generalscontroller->getVoucherNoByFinGoodsRecOrderNo($issue_order_id);
          //return $inv_no;
          $finan_yearId  = $generalscontroller->getFinGoodsFinanYearByOrderNo($issue_order_id);
            
          $acc_trans_id = $generalscontroller->getFinGoodsRecIdByVoucherNo($inv_no,$finan_yearId);
          if($acc_trans_id>0) {
                AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
                AccTransactions::where('id',$acc_trans_id)->delete();
          }

          FinishGoodsReceivesDetails::where('f_rec_order_id',$id)->delete();
          FinishGoodsReceives::where('id',$id)->delete(); 
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }

}
