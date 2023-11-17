<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Sales\SalesOrdersController;

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

use App\Models\Loans\SalesLoanReturns;
use App\Models\Loans\SalesLoanReturnDetails;

use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
//use App\Models\Sales\AccBillToBillList;
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
use PDF;

class SalesLoanReturnController extends Controller
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
      $customers = Customers::query()->orderBy('cust_name','asc')->get();

      $q = SalesLoanReturns::query()
      ->join("sales_loan_return_details", "sales_loan_returns.id", "=", "loan_r_ref_id")
      ->join("customers", "customers.id", "=", "loan_r_cust_id")
      ->join("sales_loans", "sales_loans.id", "=", "loan_r_loan_ord_id") 
      ->join('sales_loans_details', function ($join) {
        $join->on('sales_loans.id', '=', 'loan_i_order_id');
        $join->on('loan_r_loan_det_id', '=', 'sales_loans_details.id');
        })
      ->join("items", "items.id", "=", "loan_r_item_id")
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->selectRaw('sales_loan_returns.id,loan_r_title,loan_r_no,loan_r_ref_no,loan_r_date,loan_r_cust_id,loan_r_req_date,loan_r_total_qty,loan_r_comments,loan_r_loan_lot_no,loan_r_lot_no,loan_r_qty,loan_r_item_unit,loan_i_order_no,loan_i_qty,loan_i_bal_qty,item_code,item_bar_code,item_name,item_desc,itm_cat_name,cust_code,cust_name')
      ->orderBy('sales_loan_returns.id', 'desc');

      if($request->filled('delivery_no')){
         $q->where('loan_r_no', $request->get('delivery_no'));
      }
      if($request->filled('customer_id')){
         $q->where('loan_r_cust_id', $request->get('customer_id'));
      }
      $rows = $q->orderBy('sales_loan_returns.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
         'delivery_no' => $request->get('delivery_no'),
         'customer_id' => $request->get('customer_id'),
      ));
      return view ('/salesloanreturn/return_index', compact('rows','customers'));
    }

    public function delivery_modal_view($id)
    {
        $rows_m = SalesDeliveries::query()
          ->join("customers", "customers.id", "=", "del_cust_id")
          ->where('sales_deliveries.id', $id)
          ->selectRaw('sales_deliveries.id,del_comp_id,del_no,del_date,del_po_no,del_cust_id,del_to,del_customer,del_add,
            del_cont_no,del_cust_ref,del_comments,del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,
            del_vat_value,del_net_amt,del_is_invoiced,
            cust_code,cust_name,cust_add1,cust_add2,cust_mobile,cust_phone')->first();

        $rows_d = SalesDeliveryDetails::query()
          ->join("items", "items.id", "=", "del_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('del_ref_id', $id)
          ->selectRaw('itm_cat_name,item_code,item_name,del_item_id,del_item_price,
          del_qty,del_disc')->get();

        return view('sales.del_item_viewmodal',compact('rows_m','rows_d'));
    }

    public function del_challan($del_id)
    {
        // dd($sale);
        $rows_m = SalesDeliveries::query()
          ->join("customers", "customers.id", "=", "del_cust_id")
          ->leftjoin("customer_sales_persons", "customer_sales_persons.id", "=", "cust_sales_per_id")
          ->leftjoin("sales_courrier_infs", "sales_courrier_infs.id", "=", "del_courrier_to")
          ->join("companies", "companies.id", "=", "del_comp_id")
          ->where('sales_deliveries.id', $del_id)
          ->selectRaw('sales_deliveries.id,del_comp_id,del_no,del_date,del_po_no,del_cust_id,del_to,del_customer,del_add,
            del_cont_no,del_cust_ref,del_comments,del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,
            del_vat_value,del_net_amt,del_is_invoiced,del_courrier_to,del_courrier_cond,
            cust_code,cust_name,cust_add1,cust_add2,cust_mobile,cust_phone,sales_name,
            companies.name,companies.address1,courrier_to,del_courrier_cond')->first();


         $rows_d = SalesDeliveryDetails::query()
            ->join("items", "items.id", "=", "del_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('del_ref_id', $del_id)
            ->selectRaw('itm_cat_name,item_code,del_lot_no,item_name,del_item_id,del_item_unit,del_item_price,
              del_qty,del_disc')->get();

        $fileName = "DeliveryNote_".$del_id;

        $pdf = PDF::loadView('/sales/reports/rpt_delivery_challan_pdf',
        compact('rows_m','rows_d',), [], [
          'title' => $fileName,
        ]);
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

      $fileName = "Invoice_".$inv_id;
      //return $rows_m->inv_net_amt;
      $inWordAmount = $this->convert_number_to_words($rows_m->inv_net_amt);
      $pdf = PDF::loadView('/sales/reports/rpt_sales_invoice_pdf',
      compact('rows_m','rows_d','rows_delv_to','inWordAmount',), [], [
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
            ->join("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
            ->where('so_comp_id', $company_code )
            ->where('so_is_confirmed', 1)
            ->where('so_del_done', 0 )
            ->selectRaw('sales_orders.id,so_comp_id,so_order_no,so_order_date,so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,
            so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,
            so_vat_value,so_net_amt,cust_code,so_is_confirmed,cust_name');
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $item_list = $dropdownscontroller->itemLoanOrderLookup($id);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);

      $rows = SalesLoans::query()
      ->join("customers", "customers.id", "=", "loan_i_cust_id") 
      ->where('sales_loans.id', $id )
      ->selectRaw('sales_loans.id,loan_i_comp_id,loan_i_m_warehouse_id,loan_i_order_no,loan_i_order_date,loan_i_reference,loan_i_cust_id,loan_i_del_to,loan_i_comments,loan_i_total_qty,loan_i_total_bal_qty,loan_i_done,cust_code,cust_name')->first();

      $rows_d = SalesLoansDetails::query()
        ->join("items", "items.id", "=", "loan_i_item_id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('loan_i_order_id', $id )
        ->selectRaw('sales_loans_details.id,loan_i_order_id,loan_i_item_id,item_code,item_bar_code,item_name,item_desc,itm_cat_name,loan_i_item_unit,loan_i_lot_no,loan_i_item_price,loan_i_qty,loan_i_bal_qty')
        ->orderBy('sales_loans_details.id', 'desc')->get();

      $delivery_date = date('d-m-Y');
      $customers = Customers ::query()
           ->join("sales_loans", "customers.id", "=", "loan_i_cust_id")
           ->where('sales_loans.id', $id )
           ->selectRaw('customers.id,cust_code,cust_name')->get();

      $make_invoice = 1;
      return view('/salesloanreturn/return_create',compact('companies','delivery_date','company_code',
      'customers','item_list','rows','rows_d','make_invoice','courr_list',
      'warehouse_id','stor_list'))->render();
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
      
        // checking Fin year Declaration
        $yearValidation = $generalscontroller->getFinYearValidation($company_code,$inv_date);
        if($yearValidation) {
          $finan_yearId = $generalscontroller->getFinYearId($company_code,$inv_date);
        }else{
            return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
        }

        // Validate the Field
        $this->validate($request,[]);
        $deliv_orderno = $generalscontroller->make_loan_return_orderno($company_code,$finan_yearId);
  
        //Making Deliveries
        $trans_del_id = SalesLoanReturns::insertGetId([
        'loan_r_comp_id'   => $request->company_code,
        'loan_r_fin_year_id' => $finan_yearId,
        'loan_r_loan_ord_id' => $request->so_id,
        'loan_r_title'   => 'LD',  // Loan Delivery
        'loan_r_no'      => $deliv_orderno,
        'loan_r_m_warehouse_id' => $request->itm_warehouse,
        'loan_r_date'    => date('Y-m-d',strtotime($request->delivery_date)),
        'loan_r_ref_no'   => $request->reference_no,
        'loan_r_cust_id'  => $request->customer_id,
        'loan_r_req_date' => date('Y-m-d',strtotime($request->delivery_date)),
         
        'loan_r_comments'  => NULL,
        'loan_r_total_qty' => $request->total_qty,
         
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);
 
        //Making Delivery Details Records 
        $detId = $request->input('ItemCodeId');
        if ($detId){
            foreach ($detId as $key => $value){
              if ($request->Qty[$key] > 0){  
                $trans_del_det_id = SalesLoanReturnDetails::insertGetId([
                    'loan_r_comp_id'=> $request->company_code,
                    'loan_r_ref_id'     => $trans_del_id,
                    'loan_r_warehouse_id' => $request->itm_warehouse,
                    'loan_r_item_id'    => $request->ItemCodeId[$key],
                    'loan_r_storage_id' => $request->Storage[$key],
                    'loan_r_loan_det_id' => $request->loan_det_id[$key],
                    'loan_r_loan_lot_no' => $request->loan_lot_no[$key],
                    'loan_r_lot_no'     => $request->lotno[$key],
                    'loan_r_item_unit'  => $request->Unit[$key],
                    'loan_r_item_price' => 1,
                    'loan_r_qty'        => $request->Qty[$key], 
                    'created_by'      => Auth::id(),
                    'updated_by'      => Auth::id(),
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ]);
 
              
              // update sales order balance qty
                $so_id  = $request->so_id;
                $itemId = $request->ItemCodeId[$key];
                $loanlotno = $request->loan_lot_no[$key];
                SalesLoansDetails::where('loan_i_order_id',$so_id)
                ->where('loan_i_item_id',$itemId)
                ->where('loan_i_lot_no',$loanlotno) 
                ->update([ 'loan_i_bal_qty' => $request->Del[$key] + $request->Qty[$key] ]);
              }
            }
            // Closing Pending sales order
            $bal = SalesLoanReturnDetails::where('loan_r_loan_ord_id',$so_id) 
            ->join("sales_loan_returns", "sales_loan_returns.id", "=", "loan_r_ref_id")
            ->selectRaw('sum(loan_r_qty) as bal')->first()->bal;
            if($bal > 0){
              SalesLoans::where('id',$so_id)
              ->update([ 'loan_i_total_bal_qty' => $bal,
                'loan_i_done' => 1 ]);
            } 
        } 
        return redirect()->route('sales.delivery.index')->with('message','Loan Return Created Successfull !');
      //return back()->withInput();
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

    public function edit()
    {
      return redirect()->route('sales.delivery.index')->with('message','Edit Option Has not Developed yet');
    }

}
