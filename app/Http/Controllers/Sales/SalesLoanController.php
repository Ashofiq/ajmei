<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Customers\Customers;
use App\Models\Sales\SalesOrders;
use App\Models\Sales\SalesOrdersDetails;
use App\Models\Sales\SalesOrdersConfirmations;
use App\Models\Sales\SalesDeliveries;
use App\Models\Sales\SalesDeliveryDetails;
use App\Models\Sales\SalesInvoices;
use App\Models\Sales\SalesInvoiceDetails;

use App\Models\Loans\SalesLoans;
use App\Models\Loans\SalesLoansDetails;

use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use App\Models\Items\Items;

use App\Http\Resources\ItemCodeResource;
use App\Http\Resources\TransItemCodeResource;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Response;
use DB;

class SalesLoanController extends Controller
{
    public $user_sp_mapping = false;
      
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
   public function issue_index(Request $request)
   {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
       
      $customers = Customers::query()->orderBy('cust_name','asc')->get();

      $q = SalesLoans::query()
           ->join("customers", "customers.id", "=", "loan_i_cust_id")
           ->where('loan_i_comp_id', $company_code )
           ->selectRaw('sales_loans.id,loan_i_comp_id,loan_i_m_warehouse_id,loan_i_order_no,loan_i_order_date,loan_i_reference,loan_i_cust_id,loan_i_del_to,loan_i_comments,loan_i_total_qty,loan_i_total_bal_qty,cust_code,loan_i_done,cust_name');
      if($request->filled('order_no')){
        $q->where('loan_i_order_no', $request->get('order_no'));
      }
      if($request->filled('customer_id')){
        $q->where('loan_i_cust_id', $request->get('customer_id'));
      }
      if($this->user_sp_mapping){
        $q->where('sales_loans.created_by', Auth::id());
      }
      $rows = $q->orderBy('sales_loans.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'order_no'   => $request->get('order_no'),
        'customer_id' => $request->get('customer_id'),
      ));
      return view ('/salesloan/issue_index', compact('rows','customers'));
    }
    
     
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function issue_create()
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      // $dist_list  = $dropdownscontroller->comboDistrictsList();
      // $del_list  = $dropdownscontroller->deliveryList($company_code);
      // $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
      $generalsController = new GeneralsController();
      $reference_no = $generalsController->make_sales_loan_ref($company_code);
      
      $item_list =  Items::query()
      ->where('item_ref_comp_id', '=', $company_code)
      ->orderBy('item_name','asc')->get();
      $order_date = date('d-m-Y');
       
      $customers = Customers::query()->orderBy('cust_name','asc')->get();

      return view('/salesloan/issue_create',compact('companies','order_date','company_code',
      'customers','item_list','reference_no','warehouse_list','stor_list'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function issue_store(Request $request)
    {
      // Validate the Field
        $this->validate($request,[]);
        // checking Fin year Declaration
        $generalscontroller = new GeneralsController();
        $company_code = $request->company_code;
        $inv_date     = date('Y-m-d',strtotime($request->order_date));
        $yearValidation = $generalscontroller->getFinYearValidation($company_code,$inv_date);
        if($yearValidation) {
          $finan_yearId = $generalscontroller->getFinYearId($company_code,$inv_date);
        }else{
          return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
        }
       
      $sales_orderno = $generalscontroller->make_sales_loan_orderno($request->company_code,$finan_yearId);

      $trans_id = SalesLoans::insertGetId([
        'loan_i_comp_id'  => $request->company_code,
        'loan_i_fin_year_id'  => $finan_yearId,
        'loan_i_m_warehouse_id' => $request->itm_warehouse,
        'loan_i_order_title' => 'LOAN',
        'loan_i_order_no'    => $sales_orderno,
        'loan_i_order_date'  => date('Y-m-d',strtotime($request->order_date)),
        'loan_i_reference'   => $request->reference_no,
        'loan_i_cust_id'     => $request->customer_id,
        'loan_i_req_del_date' => date('Y-m-d',strtotime($request->order_date)),
        //'loan_i_del_to'       => $request->delivered_to,
        //'loan_i_del_customer' => $del_customer->deliv_to, 
       // 'loan_i_comments'   => $request->comments,
        'loan_i_done'   => 0, 
        'loan_i_total_qty'    => ($request->total_qty=='')?'0':$request->total_qty, 
        'loan_i_total_bal_qty' => 0, //($request->total_qty=='')?'0':$request->total_qty, 
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
        ]);

        //Details Records
        $detId = $request->input('ItemCodeId');
        if ($detId){
            foreach ($detId as $key => $value){
              
              if ($request->Qty[$key] > 0){  
                SalesLoansDetails::create([
                    'loan_i_comp_id'    => $request->company_code,
                    'loan_i_order_id'   => $trans_id,
                    'loan_i_warehouse_id' => $request->itm_warehouse,
                    'loan_i_storage_id' => $request->Storage[$key],
                    'loan_i_lot_no'     => $request->lotno[$key],
                    'loan_i_item_id'    => $request->ItemCodeId[$key],
                    'loan_i_item_unit'  => $request->Unit[$key],
                    'loan_i_item_price' => 1,
                    'loan_i_qty'        => $request->Qty[$key]==''?'0':$request->Qty[$key],
                    'loan_i_bal_qty'    => 0, //$request->Qty[$key]==''?'0':$request->Qty[$key], 
                ]);
              }
            }
        }
         
      // return redirect()->route('sales.loan.issue')->with('message', $sales_orderno.' : New Loan Created Successfull !');
      return redirect()->back()->with('message','Loan Received Created Successfull !'); 
    }

    public function issue_edit($id)
    { 
        $dropdownscontroller = new DropdownsController();
        $q = SalesLoans::query()
             ->join("customers", "customers.id", "=", "loan_i_cust_id") 
             ->where('sales_loans.id', $id )
             ->selectRaw('sales_loans.id,loan_i_comp_id,loan_i_m_warehouse_id,loan_i_order_no,loan_i_order_date,loan_i_reference,loan_i_cust_id,loan_i_del_to,loan_i_reference,loan_i_comments,loan_i_total_qty,loan_i_total_bal_qty,loan_i_done,cust_code,cust_name');
        $mas = $q->orderBy('sales_loans.id', 'desc')->first();

        $company_id = $mas->loan_i_comp_id;
        $companies = $dropdownscontroller->comboDefaultCompanyList($company_id); 
        $customers = Customers::query()->orderBy('cust_name','asc')->get(); 
        $warehouse_list = $dropdownscontroller->WareHouseList($company_id);
        $stor_list  = $dropdownscontroller->comboStorageList($company_id,$mas->loan_i_m_warehouse_id);
        $item_list =  Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_ref_comp_id', '=', $company_id)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
          'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')
          ->orderBy('item_name','asc')->get();
    
          
        $sql= "select sales_loans_details.*,stock as item_bal_stock,items.item_code,item_bar_code,item_desc from `sales_loans_details` left join `view_item_stocks` on `item_ref_id` = `loan_i_item_id` and `item_warehouse_id` = loan_i_warehouse_id and `item_storage_loc` = loan_i_storage_id and `loan_i_lot_no` = item_lot_no inner join `items` on `items`.`id` = `loan_i_item_id` inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id` where `loan_i_order_id` = ".$id." order by sales_loans_details.id asc";
       $det = DB::select($sql);

      return view('/salesloan/issue_edit',compact('companies','customers','item_list',
      'warehouse_list','stor_list','mas','det'))->render();
       
    }

  /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
  */
  public function issue_update(Request $request)
  {
      // Validate the Field
      $this->validate($request,[]); 
      $id = $request->loan_id;
      
      $inputdata  = SalesLoans::find($id);
      $inputdata->loan_i_order_date = date('Y-m-d',strtotime($request->order_date));
      $inputdata->loan_i_m_warehouse_id  = $request->itm_warehouse;
      $inputdata->loan_i_reference  = $request->reference_no;
      $inputdata->loan_i_cust_id    = $request->customer_id; 
      $inputdata->loan_i_req_del_date = date('Y-m-d',strtotime($request->order_date)); 
      $inputdata->loan_i_total_qty  =  ($request->total_qty=='')?'0':$request->total_qty;
      $inputdata->loan_i_total_bal_qty =  0; //($request->total_qty=='')?'0':$request->total_qty;
      $inputdata->save();

      //Delete Loan Order Details Records
      SalesLoansDetails::where('loan_i_order_id',$id)->delete();  

      $detId = $request->input('ItemCodeId');
      if ($detId)
      {
          foreach ($detId as $key => $value){
            if ($request->Qty[$key] > 0){ 
              SalesLoansDetails::create([ 
                'loan_i_comp_id'    => $request->company_code,
                'loan_i_order_id'   => $id,
                'loan_i_warehouse_id' => $request->itm_warehouse,
                'loan_i_storage_id'  => $request->Storage[$key],
                'loan_i_lot_no'      => $request->lotno[$key],
                'loan_i_item_id'     => $request->ItemCodeId[$key],
                'loan_i_item_unit'   => $request->Unit[$key],
                'loan_i_item_price'  => 1,
                'loan_i_qty'         => $request->Qty[$key],
                'loan_i_bal_qty'     => 0,//$request->Qty[$key], 
              ]);
            }
          }
        }
        return redirect()->back()->with('message','Loan Updated Updated Successfull !'); 
    }
 
    public function getItemCode(Request $request)
    {
        $compcode = $request->get('compcode');
        $custsid = $request->get('custsid');
        $dropdownscontroller = new DropdownsController();
        $itemcode = $dropdownscontroller->comboItemCodeList($custsid,$compcode);
        return ItemCodeResource::collection($itemcode);
    }



  public function get_sel_bar_item($barcode){
  //  return('ss'. $id);
    return new TransItemCodeResource(
      $itms = Items::query()
        ->join("units", "unit_id", "=", "units.id")
        ->join("customer_prices", "cust_item_p_id", "=", "items.id")
        ->where('items.item_bar_code','=',$barcode)
        ->where('customer_prices.p_del_flag','=',0)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','vUnitName','cust_price','cust_comm')->first()
    );
  }

  public function get_sel_item($id,$custid){
    //  return('ss'. $id);
      return new TransItemCodeResource(
        $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("customer_prices", "cust_item_p_id", "=", "items.id")
          ->where('items.id','=',$id)
          ->where('customer_prices.cust_p_ref_id','=',$custid)
          ->where('customer_prices.p_del_flag','=',0)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','cust_price','cust_comm')->first()
      );
    }

    public function get_delivered_inf($id){
    //  return('ss'. $id);
         $generalsController = new GeneralsController();
         $delinf = $generalsController->get_delivered_inf($id);
         return response()->json($delinf);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function issue_destroy($id,$isReceived)
    {
        try{
          $loan_order_id = $id; 
          SalesLoansDetails::where('loan_i_order_id',$loan_order_id)->delete();
          SalesLoans::where('id',$loan_order_id)->delete();

        }catch (\Exception $e){
            return redirect()->back()->with('message',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }
 
}
