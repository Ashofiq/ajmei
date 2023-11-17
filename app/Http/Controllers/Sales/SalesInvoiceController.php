<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;
use App\Http\Controllers\Sales\SalesDeliveryController;

use App\Models\Companies;
use App\Models\Customers\Customers;
use App\Models\Sales\SalesOrders;
use App\Models\Sales\SalesOrdersDetails;
use App\Models\Sales\SalesOrdersConfirmations;
use App\Models\Sales\SalesDeliveries;
use App\Models\Sales\SalesDeliveryDetails;
use App\Models\Sales\SalesInvoices;
use App\Models\Sales\SalesInvoiceDetails;
use App\Models\Items\Items;

use App\Http\Resources\ItemCodeResource;
use App\Http\Resources\TransItemCodeResource;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use Response;
use DB;
use PDF;

class SalesInvoiceController extends Controller
{
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
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $generalscontroller = new GeneralsController();
     $finan_yearId = $generalscontroller->getActiveFinYearId($company_code); 
     $invoices = SalesInvoices::query()->where('inv_comp_id', $company_code)
        //->where('inv_fin_year_id', $finan_yearId)
        ->orderBy('inv_no','asc')->get();
        
    /* $invoices = SalesInvoices::query()->where('inv_comp_id', $company_code )
        ->orderBy('inv_no','asc')->get();*/
        
     $customers = Customers::query()->orderBy('cust_name','asc')->get();
     $q = SalesInvoices::query()
          ->join("customers", "customers.id", "=", "inv_cust_id")
          ->join("sales_orders", "sales_orders.id", "=", "inv_sale_ord_id")
          ->where('inv_comp_id', $company_code )
          ->selectRaw('sales_invoices.id,inv_comp_id,inv_no,inv_fin_year_id,inv_date,inv_so_po_no,inv_cust_id,so_order_no,
          inv_sub_total,inv_disc_value,inv_vat_value,inv_net_amt,inv_acc_doc,cust_code,cust_name');

      $inv_no = '';
      $fromdate = '';
      $todate  = '';
       if($request->filled('inv_no')){
          $q->where('inv_no', $request->get('inv_no'));
        }
        if($request->filled('customer_id')){
          $q->where('inv_cust_id', $request->get('customer_id'));
        }
        if($request->filled('fromdate')){
          $fromdate =  date('Y-m-d',strtotime($request->get('fromdate')));
          $q->where('inv_date','>=', $fromdate);
        }
        if($request->filled('todate')){
            $todate  = date('Y-m-d',strtotime($request->get('todate')));
          $q->where('inv_date','<=', $todate );
        }
       $rows = $q->orderBy('sales_invoices.id', 'desc')->paginate(10)->setpath('');
       $rows->appends(array(
          'inv_no' => $request->get('inv_no'),
          'inv_cust_id' => $request->get('inv_cust_id'),
          'fromdate' => $fromdate,
          'todate' => $todate,
        ));
       // get requested action
       return view('/sales/inv_index', compact('rows','customers','companies',
       'company_code','inv_no','invoices','fromdate','todate' ));

    }

    public function locked(Request $request)
    {
        $dropdownscontroller = new DropdownsController();
        $companies    = $dropdownscontroller->comboCompanyAssignList();
        if($request->filled('company_code')){
          $company_code = $request->get('company_code');
        }else{
          $company_code = $dropdownscontroller->defaultCompanyCode();
        }
        $customers = Customers::query()->orderBy('cust_name','asc')->get();
        $invoices = SalesInvoices::query()->where('inv_comp_id', $company_code )
           ->orderBy('inv_no','asc')->get();

        $q = SalesInvoices::query()
             ->join("customers", "customers.id", "=", "inv_cust_id")
             ->join("sales_orders", "sales_orders.id", "=", "inv_sale_ord_id")
             ->where('inv_comp_id', $company_code )
             ->selectRaw('sales_invoices.id,inv_comp_id,inv_no,inv_date,inv_so_po_no,inv_cust_id,so_order_no,so_is_locked,
             inv_sub_total,inv_disc_value,inv_vat_value,inv_net_amt,inv_acc_doc,cust_code,cust_name');

         $inv_no = '';
         $fromdate = date('Y-m-d');
         $todate  = date('Y-m-d');
         if($request->filled('inv_no')){
           $q->where('inv_no', $request->get('inv_no'));
         }
         if($request->filled('customer_id')){
           $q->where('inv_cust_id', $request->get('customer_id'));
         }
         if($request->filled('fromdate')){
           $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
           $q->where('inv_date','>=',  date('Y-m-d',strtotime($request->get('fromdate'))));
         }else{
           $q->where('inv_date','>=',  date('Y-m-d',strtotime($fromdate)));
         }
         if($request->filled('todate')){
           $todate  = date('Y-m-d',strtotime($request->get('todate')));
           $q->where('inv_date','<=',  date('Y-m-d',strtotime($request->get('todate'))));
         }else {
           $q->where('inv_date','<=',  date('Y-m-d',strtotime($todate)));
         }

         $rows = $q->orderBy('sales_invoices.id', 'desc')->get();

          // get requested action
          return view('/sales/inv_locked_index', compact('rows','customers','companies',
          'company_code','inv_no','invoices','fromdate','todate' ));
     }

   public function locking(Request $request)
   {
       $so_order_no = $request->input('so_order_no');
       //dd($con_name);
       if ($so_order_no){
         foreach ($so_order_no as $key => $value){
           if ($request->so_order_no[$key] != ''){
             $affected = DB::table('sales_orders')
              ->where('so_order_no', $request->so_order_no[$key])
              ->update(['so_is_locked' => 1]);
           }
         }
       }
       return redirect()->route('sales.invoice.locked')->with('message','Sales Order is Successfully Locked !');
   }
   
    public function sales_modal_view($inv_id)
    {
      //  del_challan
      $generalscontroller = new GeneralsController();
      $del_id = $generalscontroller->get_del_id_by_invid($inv_id); 
      return $this->invoice_modal_view($del_id);
    }
    
    public function invoice_modal_view($id)
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

    public function sales_invoice($del_id)
    {
        // dd($sale);
        $rows_m = SalesDeliveries::query()
          ->join("customers", "customers.id", "=", "del_cust_id")
          ->leftjoin("customer_sales_persons", "customer_sales_persons.id", "=", "cust_sales_per_id")
          ->where('sales_deliveries.id', $del_id)
          ->selectRaw('sales_deliveries.id,del_comp_id,del_no,del_date,del_po_no,del_cust_id,del_to,del_customer,del_add,
            del_cont_no,del_cust_ref,del_comments,del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,
            del_vat_value,del_net_amt,del_is_invoiced,
            cust_code,cust_name,cust_add1,cust_add2,cust_mobile,cust_phone,sales_name')->first();

       $salseOrder = SalesOrders::where('so_order_no', $rows_m->so_order_no)->first();


         $rows_d = SalesDeliveryDetails::query()
            ->join("items", "items.id", "=", "del_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('del_ref_id', $del_id)
            ->selectRaw('itm_cat_name,item_code,item_name,del_item_id,del_item_price,
              del_qty,del_disc')->get();

        $fileName = "SalesInvoice_".$del_id;

        $pdf = PDF::loadView('/sales/reports/rpt_delivery_challan_pdf',
        compact('rows_m','rows_d', 'salseOrder'), [], [
          'title' => $fileName,
        ]);
        return $pdf->stream($fileName,'.pdf');
    }

    public function acc_modal_view($voucher_no,$fin_year_id)
    {
       if ($voucher_no != '') {
          $generalscontroller = new GeneralsController();
          $id = $generalscontroller->getIdByVoucherNo($voucher_no,$fin_year_id);
          //return $id;
          $acctranscontroller = new AccountTransController();
          $rows_m = $acctranscontroller->modal_view_m($id);
          $rows_d = $acctranscontroller->modal_view_d($id);
          return view('accounts.acctrans_viewmodal',compact('rows_m','rows_d'));
        }else{
          return 'Posting not yet done';
        }
    }
    
    public function sales_invoice_view($inv_no,$finan_yearId)
    {
      //  sales_invoice
      $generalscontroller = new GeneralsController();
     // $company_code = $generalscontroller->get_inv_company_id_by_invno($inv_no);
     // $finan_yearId = $generalscontroller->getActiveFinYearId($company_code);
      $inv_id = $generalscontroller->get_inv_id_by_invno($inv_no,$finan_yearId);
      return $this->sal_invoice($inv_id);
    }
    
    public function sal_challan($inv_id)
    {
      //  del_challan
      $generalscontroller = new GeneralsController();
      $del_id = $generalscontroller->get_del_id_by_invid($inv_id);
      $salesdeliverycontroller = new SalesDeliveryController(); 
      $salesdeliverycontroller->del_challan($del_id);
    }
    
    public function sal_invoice($inv_id) 
    {
      $SalesInvoices = new SalesInvoices();
      $rows_m = $SalesInvoices->sal_invoice($inv_id);
      $salesinvoicesdetails = new SalesInvoiceDetails();
      $rows_d = $salesinvoicesdetails->sal_invoice_details($inv_id);
      $rows_delv_to = $salesinvoicesdetails->sal_invoice_delivered_to($inv_id);

      $salseOrder = SalesOrders::where('so_order_no', $rows_m->so_order_no)->first();


      $fileName = "Invoice_".$inv_id;
      $inWordAmount = $this->convert_number_to_words($rows_m->inv_net_amt);
      $pdf = PDF::loadView('/sales/reports/rpt_sales_invoice_pdf',
      compact('rows_m', 'salseOrder', 'rows_d','rows_delv_to','inWordAmount',), [], [
        'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    } 
    
    public function convert_number_to_words($amount) {
        // $hyphen      = ' ';
        // $conjunction = ' ';
        // $separator   = ', ';
        // $negative    = 'negative ';
        // $decimal     = ' Taka ';
        // $dictionary  = array(
        //     0                   => 'Zero',
        //     1                   => 'One',
        //     2                   => 'Two',
        //     3                   => 'Three',
        //     4                   => 'Four',
        //     5                   => 'Five',
        //     6                   => 'Six',
        //     7                   => 'Seven',
        //     8                   => 'Eight',
        //     9                   => 'Nine',
        //     10                  => 'Ten',
        //     11                  => 'Eleven',
        //     12                  => 'Twelve',
        //     13                  => 'Thirteen',
        //     14                  => 'Fourteen',
        //     15                  => 'Fifteen',
        //     16                  => 'Sixteen',
        //     17                  => 'Seventeen',
        //     18                  => 'Eighteen',
        //     19                  => 'Nineteen',
        //     20                  => 'Twenty',
        //     30                  => 'Thirty',
        //     40                  => 'Fourty',
        //     50                  => 'Fifty',
        //     60                  => 'Sixty',
        //     70                  => 'Seventy',
        //     80                  => 'Eighty',
        //     90                  => 'Ninety',
        //     100                 => 'Hundred',
        //     1000                => 'Thousand',
        //     1000000             => 'Million',
        //     1000000000          => 'Billion',
        //     1000000000000       => 'Trillion',
        //     1000000000000000    => 'Quadrillion',
        //     1000000000000000000 => 'Quintillion'
        // );

        // if (!is_numeric($number)) {
        //     return false;
        // }

        // if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        //     // overflow
        //     trigger_error(
        //         'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
        //         E_USER_WARNING
        //     );
        //     return false;
        // }

        // if ($number < 0) {
        //     return $negative . Self::convert_number_to_words(abs($number));
        // }

        // $string = $fraction = null;

        // if (strpos($number, '.') !== false) {
        //     list($number, $fraction) = explode('.', $number);
        // }

        // switch (true) {
        //     case $number < 21:
        //         $string = $dictionary[$number];
        //         break;
        //     case $number < 100:
        //         $tens   = ((int) ($number / 10)) * 10;
        //         $units  = $number % 10;
        //         $string = $dictionary[$tens];
        //         if ($units) {
        //             $string .= $hyphen . $dictionary[$units];
        //         }
        //         break;
        //     case $number < 1000:
        //         $hundreds  = $number / 100;
        //         $remainder = $number % 100;
        //         $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
        //         if ($remainder) {
        //             $string .= $conjunction . Self::convert_number_to_words($remainder);
        //         }
        //         break;
        //     default:
        //         $baseUnit = pow(1000, floor(log($number, 1000)));
        //         $numBaseUnits = (int) ($number / $baseUnit);
        //         $remainder = $number % $baseUnit;
        //         $string = Self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
        //         if ($remainder) {
        //             $string .= $remainder < 100 ? $conjunction : $separator;
        //             $string .= Self::convert_number_to_words($remainder);
        //         }
        //         break;
        // }

        // if (null !== $fraction && is_numeric($fraction)) {
        //     $string .= $decimal.' and ';
        //     $string .= Self::convert_number_to_words(abs($fraction));
        //     $string .= ' Paisa';
        // }
        // return $string;


        $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
        // Check if there is any number after decimal
        $amt_hundred = null;
        $count_length = strlen($num);
        $x = 0;
        $string = array();
        $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
          3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
          7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
          10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
          13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
          16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
          19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
          40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
          70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
        $here_digits = array('', 'Hundred','Thousand','Lac', 'Crore');
        while( $x < $count_length ) {
            $get_divider = ($x == 2) ? 10 : 100;
            $amount = floor($num % $get_divider);
            $num = floor($num / $get_divider);
            $x += $get_divider == 10 ? 1 : 2;
            if ($amount) {
              $add_plural = (($counter = count($string)) && $amount > 9) ? '' : null;
              $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
              $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
              '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
              '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
              }else $string[] = null;
            }
        $implode_to_Rupees = implode('', array_reverse($string));
        $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
        " . $change_words[$amount_after_decimal % 10]) . ' Paisa' : '';
        return ($implode_to_Rupees ? $implode_to_Rupees . 'TK ' : '') . $get_paise;
    }

}
