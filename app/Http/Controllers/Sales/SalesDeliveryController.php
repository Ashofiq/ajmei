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
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
//use App\Models\Sales\AccBillToBillList;
use App\Models\Items\Items;
use App\Models\Chartofaccounts;
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
use PDF;
use Helper;

class SalesDeliveryController extends Controller
{
  public $comp_code = 0;
  public $del_sale_ord_id = 0;

  public function __construct()
  {
   error_reporting(0);
  }

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
      $customers = Customers::query()->orderBy('cust_name','asc')->get();
      $q = SalesDeliveries::query()
           ->join("customers", "customers.id", "=", "del_cust_id")
           ->join("sales_orders", "sales_orders.id", "=", "del_sal_ord_id")
           ->where('del_comp_id', $company_code )
           ->selectRaw('sales_deliveries.id,del_comp_id,del_fin_year_id,del_no,del_date,del_po_no,del_cust_id,del_to,del_customer,del_add,del_m_warehouse_id,
           del_cont_no,del_cust_ref,del_courrier_to,del_courrier_cond,del_comments,
           del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,
           del_vat_value,del_carring_cost,del_labour_cost,del_load_unload_cost,del_service_charge,del_other_cost,del_net_amt,del_is_invoiced,cust_code,cust_name,so_order_no');

      if($request->filled('delivery_no')){
         $q->where('del_no', $request->get('delivery_no')); 
       }
       if($request->filled('customer_id')){
         $q->where('del_cust_id', $request->get('customer_id'));
       }
      $rows = $q->orderBy('sales_deliveries.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
         'delivery_no' => $request->get('delivery_no'), 
         'customer_id' => $request->get('customer_id'),
       ));
      //  return $rows;
      return view ('/sales/del_index', compact('rows','customers'));
    }

    public function delivery_modal_view($id)
    {
        $rows_m = SalesDeliveries::query()
          ->join("customers", "customers.id", "=", "del_cust_id")
          ->where('sales_deliveries.id', $id)
          ->selectRaw('sales_deliveries.id,del_comp_id,del_no,del_date,del_po_no,del_cust_id,del_to,del_customer,del_add,
            del_cont_no,del_cust_ref,del_comments,del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,
            del_vat_value,del_net_amt,del_carring_cost,del_labour_cost,del_load_unload_cost,del_service_charge,del_other_cost,del_is_invoiced,
            cust_code,cust_name,cust_add1,cust_add2,cust_mobile,cust_phone')->first();

        $rows_d = SalesDeliveryDetails::query()
          ->join("items", "items.id", "=", "del_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('del_ref_id', $id)
          ->selectRaw('itm_cat_name,item_code,item_name,del_item_id,del_item_price,del_item_unit,
          del_qty,del_disc,del_item_size,del_item_weight,del_item_pcs')->get();

        return view('sales.del_item_viewmodal',compact('rows_m','rows_d'));
    }
    
    public function del_challan($del_id)
    {
        // dd($sale);
        $rows_m = SalesDeliveries::query()
          ->join("customers", "customers.id", "=", "del_cust_id")
          ->join("sales_orders", "sales_orders.id", "=", "del_sal_ord_id")
          ->leftjoin("customer_sales_persons", "customer_sales_persons.id", "=", "cust_sales_per_id")
          ->leftjoin("sales_courrier_infs", "sales_courrier_infs.id", "=", "del_courrier_to")
          ->join("companies", "companies.id", "=", "del_comp_id")
          ->where('sales_deliveries.id', $del_id)
          ->selectRaw('sales_deliveries.id,del_comp_id,del_no,del_date,del_po_no,del_cust_id,del_to,del_customer,del_add,del_cont_no,del_cust_ref,del_comments,del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,
          del_vat_value,del_net_amt,del_is_invoiced,del_courrier_to,del_courrier_cond,
          cust_code,cust_name,cust_add1,cust_add2,cust_mobile,cust_phone,sales_name,
          companies.name,companies.address1,courrier_to,del_courrier_cond,so_order_no,
          so_order_date, sales_orders.remark, cust_slno, personalMobileno')->first();
        
        $salseOrder = SalesOrders::where('so_order_no', $rows_m->so_order_no)->first();


        $rows_d = SalesDeliveryDetails::query()
            ->join("sales_deliveries", "sales_deliveries.id", "=", "del_ref_id")
            // ->rightJoin("sales_invoice_details", "sales_invoice_details.inv_del_no", "=", "sales_deliveries.del_no")
            ->join("items", "items.id", "=", "del_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('del_ref_id', $del_id)
            ->selectRaw('itm_cat_name,item_code,del_lot_no,item_name,item_desc,del_item_id,del_item_pcs,del_item_unit,del_item_price,del_qty,del_disc,size, del_item_size as inv_item_size, del_item_weight')->get();

        $fileName = "DeliveryNote_".$del_id;
        $pdf = PDF::loadView('/sales/reports/rpt_delivery_challan_pdf',
        compact('rows_m','rows_d', 'salseOrder'), [], [
          'title' => $fileName,
        ]);

        // return $rows_d;
        return $pdf->stream($fileName,'.pdf');
    }

    public function gate_pass($del_id)
    {
        // dd($sale);
        $rows_m = SalesDeliveries::query()
          ->join("customers", "customers.id", "=", "del_cust_id")
          ->join("sales_orders", "sales_orders.id", "=", "del_sal_ord_id")
          ->leftjoin("customer_sales_persons", "customer_sales_persons.id", "=", "cust_sales_per_id")
          ->leftjoin("sales_courrier_infs", "sales_courrier_infs.id", "=", "del_courrier_to")
          ->join("companies", "companies.id", "=", "del_comp_id")
          ->where('sales_deliveries.id', $del_id)
          ->selectRaw('sales_deliveries.id,del_comp_id,del_no,del_date,del_po_no,del_cust_id,del_to,del_customer,del_add,del_cont_no,del_cust_ref,del_comments,del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,
          del_vat_value,del_net_amt,del_is_invoiced,del_courrier_to,del_courrier_cond,
          cust_code,cust_name,cust_add1,cust_add2,cust_mobile,cust_phone,sales_name,
          companies.name,companies.address1,courrier_to,del_courrier_cond,so_order_no,
          so_order_date,sales_orders.remark, cust_slno')->first();

        $salseOrder = SalesOrders::where('so_order_no', $rows_m->so_order_no)->first();

         $rows_d = SalesDeliveryDetails::query()
            ->join("sales_deliveries", "sales_deliveries.id", "=", "del_ref_id")
            // ->join("sales_invoice_details", "sales_invoice_details.inv_del_no", "=", "sales_deliveries.del_no")
            ->join("items", "items.id", "=", "del_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('del_ref_id', $del_id)
            ->selectRaw('itm_cat_name,item_code,del_lot_no,item_name,item_desc,del_item_id,del_item_unit,del_item_price,del_item_pcs,del_qty,del_disc,size, del_item_size as inv_item_size, del_item_weight')->get();

        $fileName = "DeliveryNote_".$del_id;

        // return $rows_d;
        $pdf = PDF::loadView('/sales/reports/rpt_gate_pass_pdf', 
        compact('rows_m','rows_d', 'salseOrder'), [], [
          'title' => $fileName,
        ]); 
        // return $rows_d;
        return $pdf->stream($fileName,'.pdf');
    }

    public function sal_invoice($del_id)
    {
      $salesinvoiceDetails = new SalesInvoiceDetails();
      $inv_id = $salesinvoiceDetails->get_sal_invoice($del_id);

      $SalesInvoices = new SalesInvoices();
      $rows_m = $SalesInvoices->sal_invoice($inv_id);
      $rows_delv_to = $salesinvoiceDetails->sal_invoice_delivered_to($inv_id);
      $rows_d = $salesinvoiceDetails->sal_invoice_details($inv_id); 

      $salseOrder = SalesOrders::where('so_order_no', $rows_m->so_order_no)->first();

      $fileName = "Invoice_".$inv_id;
      //return $rows_m->inv_net_amt;
      $inv_net_amt = $rows_m->inv_net_amt + $rows_m->inv_carring_cost + $rows_m->inv_labour_cost + $rows_m->inv_load_unload_cost+$rows_m->inv_service_charge+$rows_m->inv_other_cost;
      $inv_net_amt = floor($inv_net_amt);
      $inWordAmount = $this->convert_number_to_words($inv_net_amt); 
      $pdf = PDF::loadView('/sales/reports/rpt_sales_invoice_pdf',
      compact('rows_m','rows_d','rows_delv_to','inWordAmount', 'salseOrder'), [], [
        'title' => $fileName, 
      ]);  

      return $pdf->stream($fileName,'.pdf');
    }

    public function so_pending(Request $request)
    {
       $dropdownscontroller = new DropdownsController();
       $companies    = $dropdownscontroller->comboCompanyAssignList();
       $company_code = $dropdownscontroller->defaultCompanyCode();
       $customers = Customers::query()->orderBy('cust_name','asc')->get();
       $q = SalesOrders::query()
            ->join("customers", "customers.id", "=", "so_cust_id")
            ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
            ->where('so_comp_id', $company_code )
            ->where('so_is_production', 1) 
            ->where('so_del_done', 0 )
            ->selectRaw('sales_orders.id,so_comp_id,so_order_no,so_fpo_no, so_order_date,so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,so_req_del_date,
            so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per, so_vat_value,so_net_amt,cust_code,so_is_confirmed,cust_name');
        if($request->filled('order_no')){
          $q->where('so_order_no', $request->get('order_no'));
        }
        if($request->filled('customer_id')){
          $q->where('so_cust_id', $request->get('customer_id'));
        }
        $rows = $q->orderBy('sales_orders.id', 'desc')->paginate(10)->setpath('');
        $rows->appends(array(
          'order_no'   => $request->get('order_no'),
          'so_cust_id' => $request->get('customer_id'),
        ));
       return view ('/sales/so_pending_index', compact('rows','customers'));
     }
 

     public function so_pending_item(){
       $data['pendingItems'] = SalesOrdersDetails::where('so_order_bal_qty', '>', 0)
       ->join('sales_orders', 'sales_orders.id', '=', 'so_order_id')
       ->join('customers', 'customers.id', '=', 'sales_orders.so_cust_id')
       ->join('items', 'items.id', '=', 'so_item_id')
       ->selectRaw('item_name, so_order_bal_qty, so_order_no, so_req_del_date, so_fpo_no, cust_name')
       ->get();

       return view('/sales/so_pending_item', $data);

     }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    { 
      if ($id == -1){
        $id = SalesOrders::orderBy('id', 'DESC')->first()->id;
        $sale_order = SalesOrders::where('id', $id)->first();
      }else{
        $id = $id;
        $sale_order = SalesOrders::where('id', $id)->first();
      }

      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $item_list = $dropdownscontroller->itemOrderLookup($id);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);
      $sales_order_id = $id;
      $rows = SalesOrders::query()
           ->leftjoin("customers", "customers.id", "=", "so_cust_id")
           ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
           ->where('sales_orders.id', $id )
           ->selectRaw('sales_orders.id,so_comp_id,so_order_no,so_order_date,so_reference,so_cust_id,so_del_to,deliv_to,so_del_add,so_cont_no,
           so_del_ref,so_req_del_date,so_comments,so_courrier_to,so_courrier_cond,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,so_vat_value,so_net_amt,so_carring_cost,so_labour_cost, so_load_unload_cost,so_service_charge,
           so_other_cost,so_is_confirmed,customers.id as cust_code,cust_name')->first();
      
      $rows_d = SalesOrdersDetails::query()
           ->join("items", "items.id", "=", "so_item_id")
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->leftjoin("view_item_stocks", "view_item_stocks.item_ref_id", "=", "so_item_id")
           ->where('so_order_id', $id)
           ->selectRaw('sales_orders_details.id,so_order_id,so_item_id,item_code,item_bar_code,item_name,item_desc,itm_cat_name,so_item_spec,so_order_qty,so_order_con_qty,so_item_size,so_item_weight,so_item_pcs, so_order_bal_qty,so_item_unit,so_order_disc,so_item_price,item_bal_stock,stock,itm_cat_origin,itm_cat_name, so_pd_order_conf_weight')
           ->orderBy('sales_orders_details.id', 'desc')->get();

          
      $so_list = SalesOrders::query()
        ->join("customers", "customers.id", "=", "sales_orders.so_cust_id")
        ->where('so_comp_id', '=', $company_code)
        ->where('so_is_production', '=', 1)
        ->selectRaw('customers.cust_name as cust_name, so_order_no, sales_orders.id as id')
        // ->where('so_del_done', '=', 0)
        ->get();

      // return $so_list;
      
      $delivery_date = date('d-m-Y');
      //$customers = Customers::query()->orderBy('cust_name','asc')->get();
      $customers = Customers ::query()
           ->join("sales_orders", "customers.id", "=", "so_cust_id")
           ->where('sales_orders.id', $id )
           ->selectRaw('customers.id,cust_code,cust_name, cust_slno')->get();

      $chartOfAcc = Chartofaccounts::where('customerId', $customers[0]->cust_slno)->first();

      $cust_balacne = AccTransactionDetails::query()
        ->where('chart_of_acc_id', $chartOfAcc->id)
        ->selectRaw("SUM(d_amount) as d_amount, SUM(c_amount) as c_amount")
        ->groupBy('chart_of_acc_id')
        ->get(); 

      $cust_balacne = $cust_balacne[0]->d_amount - $cust_balacne[0]->c_amount;

      // return $customers;

      $make_invoice = 1; 
      return view('/sales/del_create',compact('companies', 'cust_balacne', 'delivery_date','company_code',
      'customers','item_list','rows','rows_d','make_invoice','courr_list',
      'warehouse_id','stor_list','so_list','sales_order_id'))->render();  
    }

    public function sales_select(Request $request){
        return redirect()->route('sales.delivery.create', $request->get_sales_order_no);
    }

    public function getDelItemCode(Request $request)
    {
      $custsid = $request->get('custsid');
      $soid = $request->get('soid');
      $dropdownscontroller = new DropdownsController();
      $itemcode = $dropdownscontroller->comboItemOrderCodeList($soid);
      return ItemCodeResource::collection($itemcode);
    }

    public function get_so_del_item($itmid,$soid){
    //  return('ss'. $id);
      return new TransItemCodeResource(
        $itms = SalesOrdersDetails::query()
        ->join("items", "items.id", "=", "so_item_id")
        ->join("units", "unit_id", "=", "units.id")
        ->where('so_order_id','=',$soid)
        ->where('items.id','=',$itmid)
        ->select('so_item_id as id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','so_item_price as cust_price',
        'so_order_disc as item_ord_disc','so_order_bal_qty as item_ord_qty')->first()
      );
    }

    public function get_delivered_inf($id){
    //  return('ss'. $id);
         $generalsController = new GeneralsController();
         $delinf = $generalsController->get_delivered_inf($id);
         return response()->json($delinf);
    }

    public function get_delivered_inf_default($custid){
        //return('ss'. $custid);
         $generalsController = new GeneralsController();
         $del_to_id = $generalsController->get_delivered_id_default($custid);
        // return('ss'. $del_to_id);
         $delinf = $generalsController->get_delivered_inf($del_to_id);
         return response()->json($delinf);
    }

    public function get_outstanding_inf($compid,$custid){
        //return('ss'. $custid);
         $generalsController = new GeneralsController();
         $ledgerid = $generalsController->CustomerChartOfAccId($custid);
         $getoutstanding = $generalsController->getCustomerOutstanding($compid,$ledgerid);
         return response()->json($getoutstanding);
    }
    
    public function get_courrier_inf($compid,$custid){
        //return('ss'. $custid);
         $generalsController = new GeneralsController();
         $getcourrierid = $generalsController->getCustomerCourrier($compid,$custid);
         return response()->json($getcourrierid);
    }


    public function get_invoice_inf($invid){
      //  return('ss'. $invid);
        $data = SalesInvoices::query()
             ->join("customers", "customers.id", "=", "inv_cust_id")
             ->where('inv_no', $invid )
             ->select('inv_no','inv_date','cust_name','inv_disc_value',
             'inv_vat_value','inv_net_amt')->first();
         return response()->json($data);
    }
    
    public function get_vat_inf($compid,$custid){
        //return('ss'. $custid);
         $generalsController = new GeneralsController();
         $getvat = $generalsController->getCustomerVAT($compid,$custid);
         return response()->json($getvat);
    }
    
    public function itemOrderLookup($soid)
    {
      $dropdownsController = new DropdownsController();
      $itms = $dropdownsController->itemOrderLookup($soid);
      return response()->json($itms);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   

        $generalscontroller = new GeneralsController();
        $company_code = $request->company_code;
        $customer_id  = $request->customer_id;
        $inv_date     = date('Y-m-d',strtotime($request->delivery_date));
        $get_cust_comm = $generalscontroller->getCustomerVAT($request->company_code,$request->customer_id);

        // checking Fin year Declaration
        $yearValidation = $generalscontroller->getFinYearValidation($company_code,$inv_date);
        if($yearValidation) {
          $finan_yearId = $generalscontroller->getFinYearId($company_code,$inv_date);
        }else{
            return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
        }

        // Validate the Field
        $this->validate($request,[]);
        $deliv_orderno = $generalscontroller->make_deliv_orderno($company_code,$finan_yearId);
       // $del_customer = $generalscontroller->get_delivered_inf($request->delivered_to);
      
       // Get Carring Cost Value
       $carring_cost = ($request->carring_cost=='')?'0':$request->carring_cost;
       $labour_cost  = ($request->labour_cost=='')?'0':$request->labour_cost;
       $load_unload_cost = ($request->load_unload_cost=='')?'0':$request->load_unload_cost;
       $service_charge   = ($request->service_charge=='')?'0':$request->service_charge;
       $other_cost   = ($request->other_cost=='')?'0':$request->other_cost;

        //Making Deliveries
        $trans_del_id = SalesDeliveries::insertGetId([
        'del_comp_id'   => $request->company_code,
        'del_fin_year_id'   => $finan_yearId,
        'del_sal_ord_id'  => $request->so_id,
        'del_m_warehouse_id' => $request->itm_warehouse,
        'del_title'   => 'DN',
        'del_no'      => $deliv_orderno,
        'del_date'    => date('Y-m-d',strtotime($request->delivery_date)),
        'del_po_no'   => $request->reference_no,
        'del_cust_id'  => $request->customer_id,
        'del_req_date' => date('Y-m-d',strtotime($request->delivery_req_date)),
        'del_to'       => $request->delivered_to,
        'del_customer' => NULL, //$del_customer->deliv_to,
        //'del_add'      => $request->address1,
        //'del_cont_no'  => $request->contact_no,
        //'del_cust_ref'  => $request->cust_ref,
        //'del_courrier_to'   => $request->courr_id,
        //'del_courrier_cond' => $request->condition_tag,
        'del_comments'  => $request->comments,
        'del_sub_total' => $request->n_sub_total,
        'del_disc_per'  => $request->n_disc_per,
        'del_disc_value'=> (float) str_replace(',', '', $request->n_discount),
        'del_total_disc'=> $request->n_total_disc,
        'del_gross_amt' => $request->n_total_gross,
        'del_vat_per'   => $request->n_vat_per,
        'del_vat_value' => $request->n_total_vat,

        'del_carring_cost' => $request->carring_cost,
        'del_labour_cost'  => $request->labour_cost,
        'del_load_unload_cost' => $request->load_unload_cost,
        'del_service_charge'   => $request->service_charge,
        'del_other_cost'   => $request->other_cost, 

        'del_net_amt'   => $request->n_net_amount,
        'del_is_invoiced' => $request->make_invoice,
        'remarks' => $request->remarks,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
        ]);

        //Making Invoices
        $inv_title   = 'SV';
        $inv_no = $generalscontroller->make_invoice_no($company_code,$finan_yearId);
        $trans_inv_id = SalesInvoices::insertGetId([
        'inv_comp_id'   => $request->company_code,
        'inv_fin_year_id'   => $finan_yearId,
        'inv_sale_ord_id'=> $request->so_id,
        'inv_title'     => $inv_title,
        'inv_no'        => $inv_no,
        'inv_date'      => $inv_date,
        'inv_cust_id'   => $customer_id,
        'inv_sub_total' => $request->n_sub_total,
        'inv_itm_disc_value'=> $request->n_discount,
        'inv_disc_value'=> $request->n_total_disc,
        'inv_vat_value' => $request->n_total_vat,

        'inv_carring_cost' => $request->carring_cost,
        'inv_labour_cost'  => $request->labour_cost,
        'inv_load_unload_cost' => $request->load_unload_cost,
        'inv_service_charge'   => $request->service_charge,
        'inv_other_cost'   => $request->other_cost, 

        'inv_net_amt'   => $request->n_net_amount,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
        ]);

        //Making Delivery Details Records
        $acc_naration = '';
        $detId = $request->input('ItemCodeId');
        if ($detId){
            foreach ($detId as $key => $value){
              if ($request->Qty[$key] > 0){
                //  define the commision
                $comm = 0;
                if($comm <= 0 ){
                  //$get_itm_comm = $generalscontroller->get_sel_item1($request->ItemCodeId[$key],$request->customer_id);
                  $comm = 0; //$get_itm_comm->cust_comm;
                  //echo 'BB'.$comm;
                }
                
                $trans_del_det_id = SalesDeliveryDetails::insertGetId([
                    'del_det_comp_id'=> $request->company_code,
                    'del_ref_id'     => $trans_del_id,
                    'del_warehouse_id' => $request->itm_warehouse,
                    'del_item_id'    => $request->ItemCodeId[$key],
                    'del_storage_id' => 1, //$request->Storage[$key],
                    'del_lot_no'     => 101, //$request->lotno[$key],
                    'del_item_spec'  => $request->ItemDesc[$key],
                    'del_item_unit'  => $request->Unit[$key],
                    'del_item_price' => $request->Price[$key],
                    'del_item_size'  => $request->Size[$key],
                    'del_item_weight' => $request->QWeight[$key],
                    'del_item_pcs'   => $request->PCS[$key], 
                    'del_qty'        => $request->Qty[$key],
                    'del_disc'       => $request->Discp[$key],
                    'del_comm'       => $comm,
                    'created_by'      => Auth::id(),
                    'updated_by'      => Auth::id(),
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ]);

                //Making Invoice Details Records
                SalesInvoiceDetails::create([
                    'inv_det_comp_id'=> $request->company_code,
                    'inv_mas_id'     => $trans_inv_id,
                    'inv_del_id'     => $trans_del_id,
                    'inv_del_det_id' => $trans_del_det_id,
                    'inv_del_no'     => $deliv_orderno,
                    'inv_po_no'      => $request->reference_no,

                    'inv_warehouse_id' => $request->itm_warehouse,
                    'inv_storage_id' => 1, //$request->Storage[$key],
                    'inv_lot_no'     => 101, //$request->lotno[$key],
                    'inv_item_spec'  => $request->ItemDesc[$key],
                    'inv_item_id'    => $request->ItemCodeId[$key],
                    'inv_item_price' => $request->Price[$key],
                    'inv_item_size'  => $request->Size[$key],
                    'inv_item_weight' => $request->QWeight[$key],
                    'inv_item_pcs'   => $request->PCS[$key],  
                    'inv_qty'        => $request->Qty[$key],
                    'inv_unit'       => $request->Unit[$key],
                    'inv_itm_disc_per' => $request->Discp[$key],
                    'inv_comm'        => $comm,

                    'inv_disc_per'    => $request->n_disc_per,
                    'inv_disc_value'  => $request->n_discount,
                    'inv_vat_per'     => $request->n_vat_per,
                    'inv_del_to'      => $request->delivered_to,
                   // 'inv_del_to_cust' => $del_customer->deliv_to,
                   // 'inv_del_add'     => $request->address1,
                   // 'inv_del_contact' => $request->contact_no,
                   // 'inv_del_ref'     => $request->cust_ref,
                   // 'inv_del_comments' => $request->comments,
                   // 'inv_courrier_to'   => $request->courr_id,
                   // 'inv_courrier_cond' => $request->condition_tag,
                ]);

              //make naration for accounting entry
                $itmname = $generalscontroller->get_item_details($request->ItemCodeId[$key])->item_name;
                $qty    = $request->Qty[$key];
                $price  = $request->Price[$key];
                $amount = $qty * $price;
                $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$request->Price[$key].';Amount:'.$amount.'),<br/>';

              // update sales order balance qty
                $so_id  = $request->so_id;
                $itemId = $request->ItemCodeId[$key];
                SalesOrdersDetails::where('so_order_id',$so_id)
                ->where('so_item_id',$itemId)
                ->update([ 'so_order_bal_qty' => $request->SQty[$key] - $request->PCS[$key] ]);

                SalesOrdersDetails::where('so_order_id',$so_id)
                ->where('so_item_id',$itemId)
                ->update([ 'so_pd_order_conf_weight' => 0 ]);

              // Closing Pending sales order
                $bal = SalesOrdersDetails::where('so_order_id',$so_id)
                ->where('so_item_id',$itemId)
                ->selectRaw('sum(so_order_bal_qty) as bal')->first()->bal;
                if($bal == 0){
                  SalesOrders::where('id',$so_id)
                  ->update([ 'so_del_done' => 1 ]);
                }else{
                  SalesOrders::where('id',$so_id)
                  ->update([ 'so_del_done' => 0 ]);
                }

              // update item Stock qty
                Items::where('id',$itemId)
                ->update([ 'item_bal_stock' => $request->Stock[$key] - $request->Qty[$key] ]);
              }
            }
        } 

        //update financial transaction for sales
        $voucher_no = $generalscontroller->getMaxAccVoucherNo('SV',$company_code,$finan_yearId); // getting max Voucher No
        $voucher_no = $voucher_no + 1;

        $cust_acc_id  = $generalscontroller->CustomerChartOfAccId($customer_id);
        $cust_name    = $generalscontroller->CustomerName($customer_id);
        $salesinvoices = new SalesInvoices();
        $records = $salesinvoices->sal_fin_transaction($trans_inv_id);
        $recCount = $records->count();

      $acc_naration_1 = ''; 
      if ($carring_cost > 0 ) $acc_naration_1 .=  'Carring Cost: '.$carring_cost.'<br/>';
      if ($labour_cost > 0 ) $acc_naration_1 .=  'Labour Cost: '.$labour_cost.'<br/>';
      if ($load_unload_cost > 0 ) $acc_naration_1 .=  'Load/Unload Cost: '.$load_unload_cost.'<br/>';
      if ($service_charge > 0 ) $acc_naration_1 .=  'Service Charge: '.$service_charge.'<br/>';
      if ($other_cost > 0 ) $acc_naration_1 .=  'Other Cost: '.$other_cost.'<br/>'; 
        

        // Insert Transaction Master Records
        $trans_fin_id = AccTransactions::insertGetId([
          'com_ref_id'    => $company_code,
          'voucher_date'  => $inv_date,
          'trans_type'    => $inv_title,
          'voucher_no'    => $voucher_no,
          't_narration'   => $acc_naration,
          't_narration_1' => $acc_naration_1,
          'fin_ref_id'    => $finan_yearId,
          'created_by'      => Auth::id(),
          'updated_by'      => Auth::id(),
          'created_at'      => Carbon::now(),
          'updated_at'      => Carbon::now(),
        ]);

        AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'd_amount'        => $request->t_n_net_amount,
            'chart_of_acc_id' => $cust_acc_id,
            'acc_invoice_no'  => $inv_no,
        ]);
        
        $total_vat = 0;
        foreach ($records as $rec){
          $sub_total = $rec->sub_total;
          
         // $inv_disc = $rec->inv_disc_value/$recCount;
         // $gr_total = $sub_total-$inv_disc;
         // $net_total = $gr_total + ($gr_total)*$rec->inv_vat_per/100;
          
          $net_total  = $sub_total-$rec->inv_disc_value;
          $total_vat += ($sub_total - $rec->inv_disc_value)*$rec->inv_vat_per/100;
          
          AccTransactionDetails::create([
              'acc_trans_id'    => $trans_fin_id,
              'c_amount'        => $sub_total,
              'chart_of_acc_id' => $rec->sett_accid,
          ]);
        }


        if ($carring_cost > 0) {
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'd_amount'        => $carring_cost,
            'chart_of_acc_id' => $cust_acc_id,
            'acc_invoice_no'  => $inv_no,
          ]);
  
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'c_amount'        => $carring_cost,
            'chart_of_acc_id' => 3098,  // Carring Cost acc ID
            'acc_invoice_no'  => $inv_no,
          ]);
        }
  
        if ($labour_cost > 0) {
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'd_amount'        => $labour_cost,
            'chart_of_acc_id' => $cust_acc_id,
            'acc_invoice_no'  => $inv_no,
          ]);
  
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'c_amount'        => $labour_cost,
            'chart_of_acc_id' => 3099,  // labour cost acc ID
            'acc_invoice_no'  => $inv_no,
          ]);
        }
        if ($load_unload_cost > 0 ) {
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'd_amount'        => $load_unload_cost,
            'chart_of_acc_id' => $cust_acc_id,
            'acc_invoice_no'  => $inv_no,
          ]);
  
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'c_amount'        => $load_unload_cost,
            'chart_of_acc_id' => 3100,  // load unload cost acc ID
            'acc_invoice_no'  => $inv_no,
          ]);
        }
        if ($service_charge > 0 ) {
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'd_amount'        => $service_charge,
            'chart_of_acc_id' => $cust_acc_id,
            'acc_invoice_no'  => $inv_no,
          ]);
  
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'c_amount'        => $service_charge,
            'chart_of_acc_id' => 3101,  // service charge acc ID
            'acc_invoice_no'  => $inv_no,
          ]);
        }
        if ($other_cost > 0 ) {
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'd_amount'        => $other_cost,
            'chart_of_acc_id' => $cust_acc_id,
            'acc_invoice_no'  => $inv_no,
          ]);
  
  
          AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'c_amount'        => $other_cost,
            'chart_of_acc_id' => 3102,  // other cost acc ID
            'acc_invoice_no'  => $inv_no,
          ]);
        }
        
        //Carring Cost Entry
        // $misc_cost = $carring_cost + $labour_cost + $load_unload_cost + 
        //              $service_charge + $other_cost;
        
        // if ($misc_cost > 0) {
        //   AccTransactionDetails::create([
        //     'acc_trans_id'    => $trans_fin_id,
        //     'd_amount'        => $misc_cost,
        //     'chart_of_acc_id' => $cust_acc_id,
        //     'acc_invoice_no'  => $inv_no,
        //   ]);
  
        //   AccTransactionDetails::create([
        //     'acc_trans_id'    => $trans_fin_id,
        //     'c_amount'        => $misc_cost,
        //     'chart_of_acc_id' => $cust_acc_id, // 75 is for Cash in HAND 
        //     'acc_invoice_no'  => $inv_no,
        //   ]);

        // }
        
        

        //VAT entry
        // if($total_vat > 0) {
        //   AccTransactionDetails::create([
        //       'acc_trans_id'    => $trans_fin_id,
        //       'c_amount'        => $total_vat,
        //       'chart_of_acc_id' => 638,
        //   ]);
        // }

        return redirect()->route('sales.delivery.index')->with('message','New Delivery Created Successfull !');
      //return back()->withInput();
    }
    
    public function edit($id)
    { 
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $rows_inv = SalesInvoices::query() 
           ->join("sales_invoice_details", "sales_invoices.id", "=", "inv_mas_id") 
           ->where('inv_del_id', $id )
           ->selectRaw('sales_invoices.id as inv_id, inv_sale_ord_id,inv_no')->first(); 
      $this->com_code = $company_code;
      $this->del_sale_ord_id =$rows_inv->inv_sale_ord_id;
      $item_list = $dropdownscontroller->itemOrderLookup($rows_inv->inv_sale_ord_id);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);
      $sales_order_id = $id;
      $rows = SalesDeliveries::query()
           ->join("customers", "customers.id", "=", "del_cust_id")  
           ->join("sales_orders", "sales_orders.id", "=", "del_sal_ord_id") 
           ->where('sales_deliveries.id', $id )
           ->selectRaw('sales_deliveries.id,so_order_no,sales_orders.id as so_id,del_comp_id,del_no,del_date,del_cust_id,del_to,del_add,del_cont_no,del_po_no,del_cust_ref,del_req_date,del_comments,del_courrier_to,del_m_warehouse_id,del_courrier_cond,del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,del_vat_value,del_net_amt,del_carring_cost,del_labour_cost, del_load_unload_cost,del_service_charge,del_other_cost,del_is_invoiced,customers.id as cust_code,cust_name')->first();
       
      $rows_d = SalesDeliveryDetails::query()
           ->join("items", "items.id", "=", "del_item_id")
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->leftjoin("view_item_stocks", "view_item_stocks.item_ref_id", "=", "del_item_id")
           ->leftjoin('view_so_bal_qty', function ($join) {
                $join->on('so_item_id', '=',  'del_item_id')  
                  ->where('so_comp_id', '=', $this->com_code)
                  ->where('view_so_bal_qty.id', '=', $this->del_sale_ord_id); 
             }) 
           ->where('del_ref_id', $id )
           ->selectRaw('sales_delivery_details.id,del_ref_id,del_item_id,item_code,item_bar_code,item_name,item_desc,itm_cat_name,del_qty,del_item_spec,del_item_unit,del_disc,del_item_price,item_bal_stock,stock,so_order_bal_qty,itm_cat_origin,del_item_size,del_item_weight,  del_item_pcs')
           ->orderBy('sales_delivery_details.id', 'desc')->get();

     
      $delivery_date = date('d-m-Y');
      //$customers = Customers::query()->orderBy('cust_name','asc')->get();
      $customers = Customers ::query()
           ->join("sales_deliveries", "customers.id", "=", "del_cust_id")
           ->where('sales_deliveries.id', $id )
           ->selectRaw('customers.id,cust_code,cust_name')->get();

      $make_invoice = 1;
      return view('/sales/del_edit',compact('companies','delivery_date','company_code',
      'customers','item_list','rows','rows_d','make_invoice','courr_list',
      'warehouse_id','stor_list','sales_order_id','rows_inv'))->render(); 
    }

    public function update(Request $request)
    {

      $this->validate($request,[]);
      // checking Fin year Declaration
      $generalscontroller = new GeneralsController();
      $company_code = $request->company_code;
      $inv_date     = date('Y-m-d',strtotime($request->delivery_date));
      $yearValidation = $generalscontroller->getFinYearValidation($company_code,$inv_date);
      if($yearValidation) {
        $finan_yearId = $generalscontroller->getFinYearId($company_code,$inv_date);
      }else{
          return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
      }
      $del_id = $request->del_id;
      //delete financial transaction
      $inv_no  = $request->so_inv_no ; //$generalscontroller->getVoucherNoByOrderNo($sales_order_id);
      // return $inv_no;
      $acc_trans_id = $generalscontroller->getIdByVoucherNo($inv_no, $finan_yearId);
      $acc_voucher_no = $generalscontroller->getAccVoucherNoByAccTransId($acc_trans_id);
      if($acc_trans_id>0) {
        AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
        AccTransactions::where('id',$acc_trans_id)->delete();
      }

      //delete Sales Invoice transaction
      $inv_id =  $request->so_inv_id; //$generalscontroller->getInvoiceIdByOrderNo($sales_order_id);
      SalesInvoiceDetails::where('inv_mas_id',$inv_id)->delete();
      // SalesInvoices::where('id',$inv_id)->delete();

      SalesDeliveryDetails::where('del_ref_id',$del_id)->delete();

      // Get Carring Cost Value
      $carring_cost = ($request->carring_cost=='')?'0':$request->carring_cost;
      $labour_cost  = ($request->labour_cost=='')?'0':$request->labour_cost;
      $load_unload_cost = ($request->load_unload_cost=='')?'0':$request->load_unload_cost;
      $service_charge   = ($request->service_charge=='')?'0':$request->service_charge;
      $other_cost   = ($request->other_cost=='')?'0':$request->other_cost;

      //Update Sales Delivery transaction
      $inputdata  = SalesDeliveries::find($del_id);
      $inputdata->del_comp_id   = $request->company_code;
      $inputdata->del_fin_year_id   = $finan_yearId;
      $inputdata->del_sal_ord_id  = $request->so_id;
      $inputdata->del_m_warehouse_id = $request->itm_warehouse;
      $inputdata->del_title   = 'DN';
      $inputdata->del_no      = $request->del_no;
      $inputdata->del_date    = date('Y-m-d',strtotime($request->delivery_date));
      $inputdata->del_po_no   = $request->reference_no;
      $inputdata->del_cust_id  = $request->customer_id;
      $inputdata->del_req_date = date('Y-m-d',strtotime($request->delivery_req_date));
      $inputdata->del_to       = $request->delivered_to;
      $inputdata->del_customer = NULL; //$del_customer->deliv_to; 
      $inputdata->del_comments  = $request->comments;
      $inputdata->del_sub_total = $request->n_sub_total;
      $inputdata->del_disc_per  = $request->n_disc_per;
      $inputdata->del_disc_value = (float) str_replace(',', '', $request->n_discount);
      $inputdata->del_total_disc = (float) str_replace(',', '', $request->n_total_disc); 
      $inputdata->del_gross_amt = (float) str_replace(',', '', $request->n_total_gross);
      $inputdata->del_vat_per   = $request->n_vat_per;
      $inputdata->del_vat_value = $request->n_total_vat;

      $inputdata->del_carring_cost = $carring_cost;
      $inputdata->del_labour_cost = $labour_cost;
      $inputdata->del_load_unload_cost = $load_unload_cost;
      $inputdata->del_service_charge   = $service_charge;
      $inputdata->del_other_cost = $other_cost;
 
      $inputdata->del_net_amt   = $request->n_net_amount;
      $inputdata->del_is_invoiced = $request->make_invoice; 
      $inputdata->updated_by      = Auth::id();
      $inputdata->updated_at      = Carbon::now();
      $inputdata->save();

      //Update Sales Invoice transaction
      $inputdata  = SalesInvoices::find($inv_id); 
      $inputdata->inv_comp_id   = $request->company_code;
      $inputdata->inv_fin_year_id  = $finan_yearId;
      $inputdata->inv_sale_ord_id= $request->so_id;
      $inputdata->inv_title     = 'SV';
      $inputdata->inv_no        = $inv_no;
      $inputdata->inv_date      = $inv_date;
      $inputdata->inv_cust_id   = $request->customer_id;
      $inputdata->inv_sub_total = $request->n_sub_total;
      $inputdata->inv_itm_disc_value = (float) str_replace(',', '', $request->n_discount);  
      $inputdata->inv_disc_value = (float) str_replace(',', '', $request->n_total_disc);
      $inputdata->inv_vat_value = (float) str_replace(',', '', $request->n_total_vat);  

      $inputdata->inv_carring_cost = $carring_cost;
      $inputdata->inv_labour_cost = $labour_cost;
      $inputdata->inv_load_unload_cost = $load_unload_cost;
      $inputdata->inv_service_charge   = $service_charge;
      $inputdata->inv_other_cost = $other_cost;

      $inputdata->inv_net_amt   = $request->n_net_amount;
      $inputdata->updated_by    = Auth::id(); 
      $inputdata->updated_at    = Carbon::now();
      $inputdata->save();

      //Making Delivery Details Records
      $acc_naration = '';
      $detId = $request->input('ItemCodeId');
      if ($detId){
          foreach ($detId as $key => $value){
            if ($request->Qty[$key] > 0){
              //  define the commision
              $comm = 0; 
              $trans_del_det_id = SalesDeliveryDetails::insertGetId([
              'del_det_comp_id'=> $request->company_code,
              'del_ref_id'     => $del_id,
              'del_warehouse_id' => $request->itm_warehouse,
              'del_item_id'    => $request->ItemCodeId[$key],
              'del_storage_id' => 1, //$request->Storage[$key],
              'del_lot_no'     => 101, //$request->lotno[$key],
              'del_item_spec'  => $request->ItemDesc[$key],
              'del_item_unit'  => $request->Unit[$key],
              'del_item_price' => $request->Price[$key],

              'del_item_size'  => $request->Size[$key],
              'del_item_weight' => $request->QWeight[$key],
              'del_item_pcs'   => $request->PCS[$key], 

              'del_qty'        => $request->Qty[$key],
              'del_disc'       => $request->Discp[$key],
              'del_comm'       => $comm,
              'created_by'      => Auth::id(),
              'updated_by'      => Auth::id(),
              'created_at'      => Carbon::now(),
              'updated_at'      => Carbon::now(),
            ]);

              //Making Invoice Details Records
            SalesInvoiceDetails::create([
            'inv_det_comp_id'=> $request->company_code,
            'inv_mas_id'     => $inv_id,
            'inv_del_id'     => $del_id,
            'inv_del_det_id' => $trans_del_det_id,
            'inv_del_no'     => $request->del_no,
            'inv_po_no'      => $request->reference_no, 
            'inv_warehouse_id' => $request->itm_warehouse,
            'inv_storage_id' => 1, //$request->Storage[$key],
            'inv_lot_no'     => 101, //$request->lotno[$key],
            'inv_item_spec'  => $request->ItemDesc[$key],
            'inv_item_id'    => $request->ItemCodeId[$key],
            'inv_item_price' => $request->Price[$key],
            'inv_item_size'  => $request->Size[$key],
            'inv_item_weight' => $request->QWeight[$key],
            'inv_item_pcs'   => $request->PCS[$key],  
            'inv_qty'        => $request->Qty[$key],
            'inv_unit'       => $request->Unit[$key],
            'inv_itm_disc_per' => $request->Discp[$key],
            'inv_comm'        => $comm, 
            'inv_disc_per'    => (float) str_replace(',', '', $request->n_disc_per),  
            'inv_disc_value'  => (float) str_replace(',', '', $request->n_discount),
            'inv_vat_per'     => (float) str_replace(',', '', $request->n_vat_per),  
            ]);

            //make naration for accounting entry
              $itmname = $generalscontroller->get_item_details($request->ItemCodeId[$key])->item_name;
              $qty    = $request->Qty[$key];
              $price  = $request->Price[$key];
              $amount = $qty * $price;
              $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$request->Price[$key].';Amount:'.$amount.'),<br/>';
            // update sales order balance qty
              $so_id  = $request->so_id;
              $itemId = $request->ItemCodeId[$key];
              SalesOrdersDetails::where('so_order_id',$so_id)
              ->where('so_item_id',$itemId)
              ->update([ 'so_order_bal_qty' => $request->SQty[$key] - $request->PCS[$key] ]);

            // Closing Pending sales order
              $bal = SalesOrdersDetails::where('so_order_id',$so_id)
              ->where('so_item_id',$itemId)
              ->selectRaw('sum(so_order_bal_qty) as bal')->first()->bal;
              if($bal == 0) {
                SalesOrders::where('id',$so_id)
                ->update([ 'so_del_done' => 1 ]);
              }else{
                SalesOrders::where('id',$so_id)
                ->update([ 'so_del_done' => 0 ]);
              }

            // update item Stock qty
              // Items::where('id',$itemId)
              // ->update([ 'item_bal_stock' => $request->Stock[$key] - $request->Qty[$key] ]);
            }
          }
      }

      //update financial transaction for sales
      $voucher_no = $acc_voucher_no;  
      $cust_acc_id  = $generalscontroller->CustomerChartOfAccId($request->customer_id);
      $cust_name    = $generalscontroller->CustomerName($request->customer_id);
      $salesinvoices = new SalesInvoices();
      $records = $salesinvoices->sal_fin_transaction($inv_id);
      $recCount = $records->count();

      $acc_naration_1 = ''; 
      if ($carring_cost > 0 ) $acc_naration_1 .=  'Carring Cost: '.$carring_cost.'<br/>';
      if ($labour_cost > 0 ) $acc_naration_1 .=  'Labour Cost: '.$labour_cost.'<br/>';
      if ($load_unload_cost > 0 ) $acc_naration_1 .=  'Load/Unload Cost: '.$load_unload_cost.'<br/>';
      if ($service_charge > 0 ) $acc_naration_1 .=  'Service Charge: '.$service_charge.'<br/>';
      if ($other_cost > 0 ) $acc_naration_1 .=  'Other Cost: '.$other_cost.'<br/>'; 

      // Insert Transaction Master Records
      $trans_fin_id = AccTransactions::insertGetId([
        'com_ref_id'    => $request->company_code,
        'voucher_date'  => $inv_date,
        'trans_type'    => 'SV',
        'voucher_no'    => $voucher_no,
        't_narration'   => $acc_naration,
        't_narration_1' => $acc_naration_1,
        'fin_ref_id'    => $finan_yearId,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);

      AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'd_amount'        => $request->t_n_net_amount,
          'chart_of_acc_id' => $cust_acc_id,
          'acc_invoice_no'  => $inv_no,
      ]);
       
      $total_vat = 0;
      foreach ($records as $rec){
        $sub_total = $rec->sub_total; 
        $net_total  = $sub_total-$rec->inv_disc_value;
        $total_vat += ($sub_total - $rec->inv_disc_value)*$rec->inv_vat_per/100;
        
        AccTransactionDetails::create([
            'acc_trans_id'    => $trans_fin_id,
            'c_amount'        => $sub_total,
            'chart_of_acc_id' => $rec->sett_accid,
        ]);
      }

      if ($carring_cost > 0) {
        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'd_amount'        => $carring_cost,
          'chart_of_acc_id' => $cust_acc_id,
          'acc_invoice_no'  => $inv_no,
        ]);

        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'c_amount'        => $carring_cost,
          'chart_of_acc_id' => 3098,  // Carring Cost acc ID
          'acc_invoice_no'  => $inv_no,
        ]);
      }

      if ($labour_cost > 0) {
        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'd_amount'        => $labour_cost,
          'chart_of_acc_id' => $cust_acc_id,
          'acc_invoice_no'  => $inv_no,
        ]);

        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'c_amount'        => $labour_cost,
          'chart_of_acc_id' => 3099,  // labour cost acc ID
          'acc_invoice_no'  => $inv_no,
        ]);
      }
      if ($load_unload_cost > 0 ) {
        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'd_amount'        => $load_unload_cost,
          'chart_of_acc_id' => $cust_acc_id,
          'acc_invoice_no'  => $inv_no,
        ]);

        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'c_amount'        => $load_unload_cost,
          'chart_of_acc_id' => 3100,  // load unload cost acc ID
          'acc_invoice_no'  => $inv_no,
        ]);
      }
      if ($service_charge > 0 ) {
        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'd_amount'        => $service_charge,
          'chart_of_acc_id' => $cust_acc_id,
          'acc_invoice_no'  => $inv_no,
        ]);

        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'c_amount'        => $service_charge,
          'chart_of_acc_id' => 3101,  // service charge acc ID
          'acc_invoice_no'  => $inv_no,
        ]);
      }
      if ($other_cost > 0 ) {
        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'd_amount'        => $other_cost,
          'chart_of_acc_id' => $cust_acc_id,
          'acc_invoice_no'  => $inv_no,
        ]);


        AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'c_amount'        => $other_cost,
          'chart_of_acc_id' => 3102,  // other cost acc ID
          'acc_invoice_no'  => $inv_no,
        ]);
      }
      
      //Carring Cost Entry
      // $misc_cost = $carring_cost + $labour_cost + $load_unload_cost + 
      // $service_charge + $other_cost;

      // if ($misc_cost > 0) {
      //   AccTransactionDetails::create([
      //   'acc_trans_id'    => $trans_fin_id,
      //   'd_amount'        => $misc_cost,
      //   'chart_of_acc_id' => $cust_acc_id,
      //   'acc_invoice_no'  => $inv_no,
      //   ]);

      //   AccTransactionDetails::create([
      //     'acc_trans_id'    => $trans_fin_id,
      //     'c_amount'        => $misc_cost,
      //     'chart_of_acc_id' => $cust_acc_id, // 75 is for Cash in HAND 
      //     'acc_invoice_no'  => $inv_no,
      //   ]);
      // }

      // //VAT entry
      // if($total_vat > 0) {
      //   AccTransactionDetails::create([
      //       'acc_trans_id'    => $trans_fin_id,
      //       'c_amount'        => $total_vat,
      //       'chart_of_acc_id' => 638,
      //   ]);
      // }

    return redirect()->route('sales.delivery.index')->with('message','Delivery Updated Successfull !');
         
  }

    public function generateInvoice($delid, $finyearid) {
      $salesorderscontroller = new SalesOrdersController();
      $inv_no = $salesorderscontroller->financialTransaction($finyearid,$delid,0,0); 
      if($inv_no > 0) {
        SalesDeliveries::where('id',$delid)->update([ 'del_is_invoiced' => 1 ]);
      }
      return redirect()->back()->with('message','Sales Invoice Created Successfully >>> '.$inv_no); 
    } 


    public function convert_number_to_words($number) {
        $hyphen      = ' ';
        $conjunction = ' ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' Taka ';
        $dictionary  = array(
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Fourty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion',
            1000000000000       => 'Trillion',
            1000000000000000    => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . Self::convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . Self::convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = Self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= Self::convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal.' and ';
            $string .= Self::convert_number_to_words(abs($fraction));
            $string .= ' Paisa';
        }
        return $string;
    }

    

}
