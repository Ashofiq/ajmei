<?php

namespace App\Http\Controllers\Rawmaterials;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Rawmaterials\RawMaterialsReceives;
use App\Models\Rawmaterials\RawMaterialsReceivesDetails;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
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

class RawMaterialsStockController extends Controller
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
      $q = RawMaterialsReceives::query()
        ->join("suppliers", "suppliers.id", "=", "raw_supp_id")
        ->where('raw_materials_receives.raw_comp_id', $company_code)
        ->select('raw_materials_receives.id','raw_materials_receives.raw_comp_id','raw_fin_year_id','raw_order_title','raw_type','raw_order_no','raw_order_date','raw_order_ref','raw_pi_no','raw_supp_id',
        'raw_comments','raw_m_curr','raw_m_curr_rate','raw_total_qty','raw_total_amount','is_approved', 'purchaseImages','supp_name');

      if($request->filled('supplier_id')){
        $q->where('raw_supp_id', $request->get('supplier_id'));
      }
      if($request->filled('fromdate')){
        $q->where('raw_order_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
          $fromdate = date('d-m-Y',strtotime($request->get('fromdate')));
      }
      if($request->filled('todate')){
        $q->where('raw_order_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
        $todate = date('d-m-Y',strtotime($request->get('todate')));
      }
      
      if($request->filled('fromdate')){
        $rows = $q->orderBy('raw_order_date', 'DESC')->paginate(5000)->setpath('');
      }else{
          $rows = $q->orderBy('raw_order_date', 'DESC')->paginate(10)->setpath('');
      }
      
       
      $rows->appends(array(
        'raw_supp_id'     => $request->get('supplier_id'),
        'raw_order_date'  => $request->get('fromdate'),
        'raw_order_date'  => $request->get('todate'), 
      ));
      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();

      // return $rows;

      return view ('/rawmaterials/rawmaterials_index', compact('rows','suppliers','fromdate','todate'));
    }


    public function raw_modal_view($id)
    {
        $rows_m = RawMaterialsReceives::query()
          ->join("suppliers", "suppliers.id", "=", "raw_supp_id")
          ->where('raw_materials_receives.id', $id)
          ->select('raw_materials_receives.id','raw_materials_receives.raw_comp_id','raw_fin_year_id','raw_order_title','raw_type','raw_order_no','raw_order_date','raw_order_ref','raw_pi_no','raw_supp_id',
          'raw_comments','raw_m_curr','raw_m_curr_rate','raw_total_qty','raw_total_amount','supp_name','supp_add1','supp_add2','supp_mobile')->first();

        $rows_d = RawMaterialsReceivesDetails::query()
          ->join("items", "items.id", "=", "raw_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('raw_order_id', $id)
          ->select('item_code','item_name','raw_item_qty','raw_item_desc','raw_item_remarks',
          'raw_item_price','itm_cat_name','raw_d_curr','raw_d_curr_rate')->get();

        return view('rawmaterials.raw_order_item_viewmodal',compact('rows_m','rows_d'));
    }

    public function raw_mrr_view($id)
    {
        $rows_m = RawMaterialsReceives::query()
          ->join("suppliers", "suppliers.id", "=", "raw_supp_id")
          ->where('raw_materials_receives.id', $id)
          ->select('raw_materials_receives.id','raw_materials_receives.raw_comp_id','raw_fin_year_id','raw_order_title','raw_type','raw_order_no','raw_order_date','raw_order_ref','raw_pi_no','raw_supp_id',
          'raw_comments','raw_m_curr','raw_m_curr_rate','raw_total_qty','raw_total_amount','supp_name','supp_add1','supp_add2','supp_mobile')->first();

        $rows_d = RawMaterialsReceivesDetails::query()
          ->join("items", "items.id", "=", "raw_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('raw_order_id', $id)
          ->select('item_code','item_name','raw_item_qty','raw_item_desc','raw_item_remarks',
          'raw_item_price','itm_cat_name','raw_d_curr','raw_d_curr_rate')->get();

          $fileName = 'mrr';

          $pdf = PDF::loadView('rawmaterials.raw_mrr',
          compact('rows_m','rows_d'), [], [
            'title' => $fileName,
          ]);
          return $pdf->stream($fileName,'.pdf');
        return view('rawmaterials.raw_mrr',compact('rows_m','rows_d'));
    }

    public function acc_modal_view($voucher_no,$fin_year_id)
    {
       if ($voucher_no != '') {
          $generalscontroller = new GeneralsController();
          $id = $generalscontroller->getRawIdByVoucherNo($voucher_no,$fin_year_id);
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
        'item_op_stock','item_bal_stock','base_price as item_base_price', 'vUnitName')->first()
      );

      // return 1;
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
       $purchase_no = $this->makeRawOrderRef(); // old $generalsController->make_purchase_order_ref($company_code);
       $currency = Currencies::query()
         ->where('curr_comp_id', $company_code)
         ->select('id','vCurrName','dCurrValue')
         ->orderBy('id','desc')->first();
       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)  
    //   ->whereRaw("itm_cat_code like '10%'")
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       
       $purchase_date = date('d-m-Y'); 
       $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      
       return view('/rawmaterials/rawmaterials_create',compact('companies','currency','purchase_date','company_code',
       'suppliers','item_list','purchase_no','warehouse_list','stor_list'))->render();
      }

      public function makeRawOrderRef(){
        $raw_order_ref = DB::table('raw_materials_receives')->orderBy('id', 'DESC')->first()->raw_order_ref;
        $raw_order_ref = explode("/", $raw_order_ref);
        $raw_order_ref = intval($raw_order_ref[1]) + 1;
        return 'PO-'.date('y').'/'.$raw_order_ref;
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
      $rawmaterialsreceives = new RawMaterialsReceives();
      try{  
          //return 'HELLO '.$msg;
          $id = $rawmaterialsreceives->rawMaterialReceived($request,$finan_yearId);
          //return $id;
          $acc_naration = $rawmaterialsreceives->rawMaterialReceivedItem($request,$id);
          $this->rawMaterialAccounting($request,$id,$finan_yearId,$acc_naration,0);
         
      }catch (\Exception $e){
          return  $e->getMessage();
      }
      return redirect()->back()->with('message', 'Creation Successful.');
  }

  public function rawMaterialAccounting($request, $trans_id, $finan_yearId,$acc_naration,$acc_voucher_no){
     $rawmaterialsreceives = new RawMaterialsReceives();
     $generalscontroller = new GeneralsController();
     $company_code = $request->company_code;
     $supplier_id = $request->supplier_id;
     $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
     $raw_order_no = $generalscontroller->get_raw_orderno($trans_id);
     //update financial transaction for sales

       //Financial Transaction
    if ($acc_voucher_no > 0 ){
      $voucher_no = $acc_voucher_no;
    }else{ 
      $voucher_no = $generalscontroller->getMaxAccVoucherNo('GR',$company_code,$finan_yearId); // getting max Voucher No
      $voucher_no = $voucher_no + 1;
    }
 
     $supp_acc_id  = $generalscontroller->SupplierChartOfAccId($supplier_id);
     $supp_name    = $generalscontroller->SupplierName($supplier_id);
    
     $records = $rawmaterialsreceives->raw_fin_transaction($trans_id);
     $recCount = $records->count();

     // Insert Transaction Master Records
     $trans_fin_id = AccTransactions::insertGetId([
       'com_ref_id'    => $company_code,
       'voucher_date'  => $purchase_date,
       'trans_type'    => 'GR',
       'voucher_no'    => $voucher_no,
       't_narration'   => $acc_naration,
       'fin_ref_id'    => $finan_yearId,
       'created_by'      => Auth::id(),
       'updated_by'      => Auth::id(),
       'created_at'      => Carbon::now(),
       'updated_at'      => Carbon::now(),
     ]);

     AccTransactionDetails::create([
         'acc_trans_id'    => $trans_fin_id,
         'c_amount'        => ($request->total_amount=='')?'0':$request->total_amount,
         'chart_of_acc_id' => $supp_acc_id,
         'acc_invoice_no'  => $raw_order_no,
     ]);
     
     $total_vat = 0;
     foreach ($records as $rec){
       $sub_total = $rec->pur_value; 
       AccTransactionDetails::create([
           'acc_trans_id'    => $trans_fin_id,
           'd_amount'        => $sub_total,
           'chart_of_acc_id' => $rec->sett_accid,
       ]);
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
    //   ->whereRaw("itm_cat_code like '10%'")
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

      $rows_m =RawMaterialsReceives::query()
          ->where('raw_comp_id', '=', $company_code)
          ->where('raw_materials_receives.id', $id )
          ->select('raw_materials_receives.id','raw_comp_id','raw_order_title','raw_type','raw_order_no',
          'raw_order_date','raw_order_ref','raw_pi_no','raw_supp_id','raw_comments','raw_total_qty','raw_m_curr','raw_m_curr_rate','raw_total_amount', 'raw_materials_receives.purchaseImages')
          ->orderBy('raw_materials_receives.id', 'desc')->first();
 
      $rows_d = RawMaterialsReceivesDetails::query()
          ->join("items", "items.id", "=", "raw_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('raw_order_id', $id)->get();
      // return $rows_m;

      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
      return view('/rawmaterials/rawmaterials_edit',
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
      $pur_order_title = 'GR';
       $id = $request->id;
       $company_code = $request->company_code;
       $supplier_id  = $request->supplier_id;
       $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
       $generalscontroller = new GeneralsController();
       $finan_yearId = $generalscontroller->getFinYearId($company_code,$purchase_date);

       // Validate the Field
       $msg = $this->ItemValidityCheck($request);
       if($msg != '') {
         return redirect()->back()->with('message',$msg)->withInput();
       }
 
       $rawmaterialsreceives = new RawMaterialsReceives();

       try{
          $inputdata  = RawMaterialsReceives::find($id);
          $inputdata->raw_comp_id    = $company_code;
          $inputdata->raw_type       = $request->POType;
          $inputdata->raw_order_date = $purchase_date;
          $inputdata->raw_order_ref  = $request->purchase_no;
          $inputdata->raw_pi_no      = $request->pi_no;
          $inputdata->raw_supp_id    = $supplier_id;
          $inputdata->raw_m_curr     = 'BDT';
          $inputdata->raw_m_curr_rate = '1';
          $inputdata->purchaseImages  = $request->multiImage;
          $inputdata->raw_comments   = $request->comments;
          $inputdata->raw_total_qty  = ($request->total_qty=='')?'0':$request->total_qty; 
          $inputdata->raw_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;
          $inputdata->save();

          //Details Records
          $pur_order_no = $request->pur_order_no;
          RawMaterialsReceivesDetails::where('raw_order_id',$id)->delete();
          $acc_naration = $rawmaterialsreceives->rawMaterialReceivedItem($request,$id);
           // return $inv_no;
           $inv_no  = $generalscontroller->getVoucherNoByRawRecOrderNo($id);
           $acc_trans_id = $generalscontroller->getRawIdByVoucherNo($inv_no,$finan_yearId);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try{ 

          $raw_order_id = $id;
          //return $isconfirmed;
          $generalscontroller = new GeneralsController(); 
          //delete financial transaction
          $inv_no  = $generalscontroller->getVoucherNoByRawOrderNo($raw_order_id);
          //return $inv_no;
          $finan_yearId  = $generalscontroller->getRawFinanYearByOrderNo($raw_order_id);
            
          $acc_trans_id = $generalscontroller->getRawIdByVoucherNo($inv_no,$finan_yearId);
          if($acc_trans_id>0) {
                AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
                AccTransactions::where('id',$acc_trans_id)->delete();
          }

          RawMaterialsReceivesDetails::where('raw_order_id',$id)->delete();
          RawMaterialsReceives::where('id',$id)->delete(); 
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }

    public function uploadImage($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://127.0.0.1:8000/uploader',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // CURLOPT_POSTFIELDS => array('image'=> new CURLFILE(''),'imageType' => 'product', 'sourceType' => 'base64'),
            CURLOPT_POSTFIELDS => ['image' => $data, 'imageType' => 'ajmeri', 'sourceType' => 'base64'],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

}
