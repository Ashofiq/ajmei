<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use Carbon\Carbon;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use App\Models\Chartofaccounts;
use App\Models\CompaniesAssigns;
use App\Models\Customers\Customers;
use App\Models\Salespersons\CustomerSalesPersons;
use App\Models\Items\Items;
use App\Models\Sales\SalesOrders;
use App\Models\Sales\SalesOrdersDetails;
use App\Models\Sales\SalesDeliveries;
use App\Models\Sales\SalesDeliveryDetails;
use App\Models\Sales\SalesInvoices;
use App\Models\Sales\SalesInvoiceDetails;

use PDF;
use Response; 

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {
    error_reporting(0);
   }


   public function getSOReport(Request $request)
   {

     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();

     $q = SalesOrders::query()
          ->join("customers", "customers.id", "=", "so_cust_id")
          ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
          ->where('so_comp_id', $company_code )
          ->selectRaw('sales_orders.id,so_comp_id,so_order_no,so_order_date,so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,
          so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,
          so_vat_value,so_net_amt,cust_code,so_is_confirmed,cust_name');

     $order_no = '';
     $fromdate = date('Y-m-d');
     $todate  = date('Y-m-d');
     if($request->filled('order_no')){
       $order_no = $request->get('order_no');
       $q->where('so_order_no', $order_no);
     }
     if($request->filled('customer_id')){
       $q->where('so_cust_id', $request->get('customer_id'));
     }
     if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
        $q->where('so_order_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate')));
        $q->where('so_order_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
     $rows = $q->orderBy('sales_orders.id', 'desc')->get();


     if ($request->input('submit') == "pdf"){
      $fileName = 'rpt_so_list.pdf';
        $pdf = PDF::loadView('/sales/reports/rpt_so_list_pdf',
        compact('rows','customers','companies',
        'company_code','order_no','fromdate','todate' ), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }

     // get requested action
     return view('/sales/reports/rpt_so_list', compact('rows','customers','companies',
     'company_code','order_no','fromdate','todate' ));
   }

   public function getDelReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();
     $q = SalesDeliveries::query()
          ->join("customers", "customers.id", "=", "del_cust_id")
          ->where('del_comp_id', $company_code )
          ->selectRaw('sales_deliveries.id,del_comp_id,del_no,del_date,del_po_no,del_cust_id,del_to,del_customer,del_add,
          del_cont_no,del_cust_ref,del_comments,del_sub_total,del_disc_per,del_disc_value,del_total_disc,del_gross_amt,del_vat_per,
          del_vat_value,del_net_amt,del_is_invoiced,cust_code,cust_name');

    $del_no = '';
    $fromdate = date('Y-m-d');
    $todate  = date('Y-m-d');
     if($request->filled('delivery_no')){
        $q->where('del_no', $request->get('delivery_no'));
      }
      if($request->filled('customer_id')){
        $q->where('del_cust_id', $request->get('customer_id'));
      }
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
       
      }
      if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate')));
       
      }
      
      $q->where('del_date','>=', date('Y-m-d',strtotime($fromdate)));
      $q->where('del_date','<=', date('Y-m-d',strtotime($todate)));
     $rows = $q->orderBy('sales_deliveries.id', 'desc')->get();


     if ($request->input('submit') == "pdf"){
      $fileName = 'rpt_del_list_pdf.pdf';
        $pdf = PDF::loadView('/sales/reports/rpt_del_list_pdf',
        compact('rows','customers','companies',
        'company_code','del_no','fromdate','todate'), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }

     // get requested action
     return view('/sales/reports/rpt_del_list', compact('rows','customers','companies',
     'company_code','del_no','fromdate','todate' ));

   }
   
   public function getInvDateWiseReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();

     $q = SalesInvoices::query()
      ->join("sales_invoice_details", "inv_mas_id", "=", "sales_invoices.id")
      ->join("customers", "customers.id", "=", "sales_invoices.inv_cust_id")
      ->join("items", "items.id", "=", "sales_invoice_details.inv_item_id")
      ->join("item_categories", "item_categories.id", "=", "items.item_ref_cate_id")
      ->where('inv_comp_id', $company_code )
      ->selectRaw('inv_date,inv_no,cust_name,cust_add1,cust_add2,inv_lot_no,
          item_name,itm_cat_name,itm_cat_origin,inv_item_price,inv_qty,inv_unit,
          sales_invoice_details.inv_disc_value,inv_net_amt');

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
        $q->where('inv_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate')));
        $q->where('inv_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
     $rows = $q->orderBy('sales_invoices.inv_date', 'asc')->get();

      if ($request->input('submit') == "pdf"){
        $fileName = 'datewise_summary_rpt';
        $pdf = PDF::loadView('/sales/reports/rpt_inv_datewise_list_pdf', 
        compact('rows','customers','companies','company_code','inv_no','fromdate','todate'), [], [
          'title' => $fileName,
        ]);
        return $pdf->stream($fileName,'.pdf');
      }

     // get requested action
     return view('/sales/reports/rpt_inv_datewise_list',
     compact('rows','customers','companies','company_code','inv_no','fromdate','todate' ));
   }
   
   public function getInvDateWiseSummaryReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();

     $q = SalesInvoices::query()
      ->where('inv_comp_id', $company_code )
      ->selectRaw('inv_date,SUM(inv_sub_total) as inv_sub_total,
      SUM(inv_itm_disc_value) as inv_itm_disc_value,
      SUM(inv_disc_value) as inv_disc_value,
      SUM(inv_vat_value) as inv_vat_value,SUM(inv_net_amt) as inv_net_amt');

    $inv_no = '';
    if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate'))); 
    }else{
       $fromdate = date('Y-m-d');
    }
    if($request->filled('todate')){
        $todate  = date('Y-m-d',strtotime($request->get('todate')));
    }else{
        $todate  = date('Y-m-d');
    }
    $q->where('inv_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
    $q->where('inv_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
    $q->GroupBy('inv_date');
    $rows = $q->orderBy('sales_invoices.inv_date', 'asc')->get();

    if ($request->input('submit') == "pdf"){
        $fileName = 'datewise_summary_rpt';
        $pdf = PDF::loadView('/sales/reports/rpt_inv_datewise_summary_list_pdf',
        compact('rows','customers','companies','company_code','inv_no','fromdate','todate',), [], [
          'title' => $fileName,
    ]);
    return $pdf->stream($fileName,'.pdf');
   }

     // get requested action
     return view('/sales/reports/rpt_inv_datewise_summary_list',
     compact('rows','customers','companies','company_code','inv_no','fromdate','todate' ));
   }

   public function getInvReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();
     $q = SalesInvoices::query()
          ->join("customers", "customers.id", "=", "inv_cust_id")
          ->where('inv_comp_id', $company_code )
          ->selectRaw('sales_invoices.id,inv_comp_id,inv_no,inv_date,inv_so_po_no,inv_cust_id,
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
      }
      if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate'))); 
      }
      
      $q->where('inv_date','>=', date('Y-m-d',strtotime($fromdate)));
      $q->where('inv_date','<=', date('Y-m-d',strtotime($todate)));
      $rows = $q->orderBy('sales_invoices.id', 'desc')->get();

      if ($request->input('submit') == "pdf"){
        $fileName = 'rpt_inv_list_pdf.pdf';
          $pdf = PDF::loadView('/sales/reports/rpt_inv_list_pdf',
          compact('rows','customers','companies','company_code','inv_no','fromdate','todate' ), [], [
            'title' => $fileName,
        ]);
        return $pdf->stream($fileName,'.pdf');
      }
      
      // get requested action
      return view('/sales/reports/rpt_inv_list', compact('rows','customers','companies',
      'company_code','inv_no','fromdate','todate' ));
   }
    
    
   public function getInvItemWiseReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }

     $item_list =  Items::query()
       ->join("units", "unit_id", "=", "units.id")
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
       ->orderBy('item_name','asc')->get();

     $q = SalesInvoices::query()
      ->join("sales_invoice_details", "inv_mas_id", "=", "sales_invoices.id")
      ->join("customers", "customers.id", "=", "sales_invoices.inv_cust_id")
      ->join("items", "items.id", "=", "sales_invoice_details.inv_item_id")
      ->join("item_categories", "item_categories.id", "=", "items.item_ref_cate_id")
      ->where('inv_comp_id', $company_code )
      ->selectRaw('inv_date,inv_no,cust_name,cust_add1,cust_add2,inv_lot_no,
          item_name,itm_cat_name,itm_cat_origin,inv_item_price,inv_qty,inv_unit,
          sales_invoice_details.inv_disc_value,inv_net_amt');


      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');

      if($request->filled('item_id')){
        $q->where('inv_item_id', $request->get('item_id'));
      }
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate'))); 
      }
      if($request->filled('todate')){
         $todate  = date('Y-m-d',strtotime($request->get('todate'))); 
      }
      $q->where('inv_date','>=', date('Y-m-d',strtotime($fromdate)));
      $q->where('inv_date','<=', date('Y-m-d',strtotime($todate)));
      $rows = $q->orderBy('sales_invoices.inv_date', 'asc')->get();


      if ($request->input('submit') == "pdf"){
        $fileName = 'rpt_inv_itemwise_list_pdf.pdf';
          $pdf = PDF::loadView('/sales/reports/rpt_inv_itemwise_list_pdf',
          compact('rows','item_list','companies','company_code','fromdate','todate' ), [], [
            'title' => $fileName,
        ]);
        return $pdf->stream($fileName,'.pdf');
      }

     // get requested action
     return view('/sales/reports/rpt_inv_itemwise_list',
     compact('rows','item_list','companies','company_code','fromdate','todate' ));
   }

   public function getInvCustWiseReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();

     $item_list =  Items::query()
       ->join("units", "unit_id", "=", "units.id")
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
       ->orderBy('item_name','asc')->get();

     $q = SalesInvoices::query()
      ->join("sales_invoice_details", "inv_mas_id", "=", "sales_invoices.id")
      ->join("customers", "customers.id", "=", "sales_invoices.inv_cust_id")
      ->join("items", "items.id", "=", "sales_invoice_details.inv_item_id")
      ->join("item_categories", "item_categories.id", "=", "items.item_ref_cate_id")
      ->where('inv_comp_id', $company_code )
      ->selectRaw('inv_date,inv_no,cust_name,cust_add1,cust_add2,inv_lot_no,
          item_name,itm_cat_name,itm_cat_origin,inv_item_price,inv_qty,inv_unit,
          sales_invoice_details.inv_disc_value,inv_net_amt');

     $fromdate = date('Y-m-d');
     $todate  = date('Y-m-d');
      if($request->filled('customer_id')){
        $q->where('inv_cust_id', $request->get('customer_id'));
      }
      if($request->filled('item_id')){
        $q->where('inv_item_id', $request->get('item_id'));
      }
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate'))); 
      }
      if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate'))); 
      }
     
     $q->where('inv_date','>=', date('Y-m-d',strtotime($fromdate)));
     $q->where('inv_date','<=', date('Y-m-d',strtotime($todate)));
     $rows = $q->orderBy('sales_invoices.inv_date', 'asc')->get();

     if ($request->input('submit') == "pdf"){
      $fileName = 'rpt_inv_itemwise_list_pdf.pdf';
        $pdf = PDF::loadView('/sales/reports/rpt_inv_itemwise_list_pdf',
        compact('rows','customers','item_list','companies','company_code','fromdate','todate' ), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }

     // get requested action
     return view('/sales/reports/rpt_inv_custwise_list',
     compact('rows','customers','item_list','companies','company_code','fromdate','todate' ));
   }
   
   public function getCondInvReport(Request $request)
   {
     $this->trans_type = 'CR';
     $dropdownscontroller = new DropdownsController();
     $generalscontroller  = new GeneralsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $comp_name = $generalscontroller->CompanyName($company_code);
     $customers = Customers::query()->orderBy('cust_name','asc')->get();

     $inv_no   = '';
     $fromdate = date('Y-m-d');
     $todate   = date('Y-m-d');

     $rows = '';
     $report_type = $request->get('report_type');
     if ($request->input('report_type') == "pending"){
      $sql = "select distinct inv_title,inv_no,inv_date,customers.id as cust_id,cust_code,cust_name, inv_del_to,inv_del_to_cust,inv_del_add,
      inv_del_contact,inv_del_ref,inv_courrier_to, courrier_to,inv_courrier_cond,inv_del_comments,
      inv_sub_total,sales_invoices.inv_disc_value, inv_vat_value,inv_net_amt,trans_type,voucher_no from `sales_invoices`
      inner join `sales_invoice_details` on `sales_invoices`.`id` = `inv_mas_id`
      left join `sales_courrier_infs` on `sales_courrier_infs`.`id` = `inv_courrier_to`
      left join `view_acc_trans_invoices` on `inv_no` = `acc_invoice_no` and trans_type = 'CR'
      inner join `customers` on `customers`.`id` = `inv_cust_id`
      where `inv_comp_id` = $company_code and `inv_courrier_cond` = 'Condition'
      and trans_type is null";
      if($request->filled('inv_no')){
        $sql .= " and inv_no ='".$request->get('inv_no')."'";
      }
      if($request->filled('customer_id')){
        $sql .= " and inv_cust_id ='".$request->get('customer_id')."'";
      }
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
        $sql .= " and inv_date >='".$fromdate."'";
      }
      if($request->filled('todate')){
        $todate  = date('Y-m-d',strtotime($request->get('todate')));
        $sql .= " and inv_date <='".$todate."'";
      }
      $sql .= "order by courrier_to,`sales_invoices`.`id` desc";
      $tag = 'Pending';
      $rows = DB::select($sql);
    }else if ($request->input('report_type') == "paid"){
      $sql = "select distinct inv_title,inv_no,inv_date,customers.id as cust_id,cust_code,cust_name, inv_del_to,inv_del_to_cust,inv_del_add,
      inv_del_contact,inv_del_ref,inv_courrier_to, courrier_to,inv_courrier_cond,inv_del_comments,
      inv_sub_total,sales_invoices.inv_disc_value, inv_vat_value,inv_net_amt,trans_type,voucher_no from `sales_invoices`
      inner join `sales_invoice_details` on `sales_invoices`.`id` = `inv_mas_id`
      left join `sales_courrier_infs` on `sales_courrier_infs`.`id` = `inv_courrier_to`
      left join `view_acc_trans_invoices` on `inv_no` = `acc_invoice_no` and trans_type = 'CR'
      inner join `customers` on `customers`.`id` = `inv_cust_id`
      where `inv_comp_id` = $company_code and `inv_courrier_cond` = 'Condition'
      and trans_type is not null";
      if($request->filled('inv_no')){
        $sql .= " and inv_no ='".$request->get('inv_no')."'";
      }
      if($request->filled('customer_id')){
        $sql .= " and inv_cust_id ='".$request->get('customer_id')."'";
      }
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
        $sql .= " and inv_date >='".$fromdate."'";
      }
      if($request->filled('todate')){
        $todate  = date('Y-m-d',strtotime($request->get('todate')));
        $sql .= " and inv_date <='".$todate."'";
      }
      $sql .= "order by courrier_to,`sales_invoices`.`id` desc";
      $tag = 'Paid';
      $rows = DB::select($sql);
    }else{
        $sql = "select distinct inv_title,inv_no,inv_date,customers.id as cust_id,cust_code,cust_name, inv_del_to,inv_del_to_cust,inv_del_add,
        inv_del_contact,inv_del_ref,inv_courrier_to, courrier_to,inv_courrier_cond,inv_del_comments,
        inv_sub_total,sales_invoices.inv_disc_value, inv_vat_value,inv_net_amt,trans_type,voucher_no from `sales_invoices`
        inner join `sales_invoice_details` on `sales_invoices`.`id` = `inv_mas_id`
        left join `sales_courrier_infs` on `sales_courrier_infs`.`id` = `inv_courrier_to`
        left join `view_acc_trans_invoices` on `inv_no` = `acc_invoice_no` and trans_type = 'CR'
        inner join `customers` on `customers`.`id` = `inv_cust_id`
        where `inv_comp_id` = $company_code and `inv_courrier_cond` = 'Condition'";
        if($request->filled('inv_no')){
          $sql .= " and inv_no ='".$request->get('inv_no')."'";
        }
        if($request->filled('customer_id')){
          $sql .= " and inv_cust_id ='".$request->get('customer_id')."'";
        }
        if($request->filled('fromdate')){
          $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
          $sql .= " and inv_date >='".$fromdate."'";
        }
        if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate')));
          $sql .= " and inv_date <='".$todate."'";
        }
        $sql .= "order by courrier_to,`sales_invoices`.`id` desc";
        $tag = 'ALL';
        $rows = DB::select($sql);
    }

    if ($request->input('submit') == "pdf"){
        $fileName = 'sales_conditional_report.pdf';
        $pdf = PDF::loadView('/sales/reports/rpt_cond_inv_list_pdf',
        compact('rows','companies','company_code','comp_name','fromdate','todate',), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }

     // get requested action
     return view('/sales/reports/rpt_cond_inv_list', compact('rows','customers','companies',
     'company_code','inv_no','fromdate','todate','tag','report_type'));
   }
   
   public function getInvCommissionReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies = $dropdownscontroller->comboCompanyAssignList();
     $customers = Customers::query()->orderBy('cust_name','asc')->get();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
      $customer_id = '';
     if ($request->input('submit') == "html_1"){
        $sql = "select inv_date,inv_no,trans_type,voucher_no,cust_name,inv_acc_voucher,
        SUM((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100)) as inv_netamt,
        case when cust_overall_comm<=0 then SUM(inv_qty*inv_comm) else
        SUM(((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100))* inv_comm/100)  end as commission
        from `sales_invoices`
        inner join `sales_invoice_details` on `inv_mas_id` = `sales_invoices`.`id`
        left join `view_acc_trans_invoices` on `inv_no` = `acc_invoice_no` and trans_type = 'SV'
        inner join `customers` on `customers`.`id` = `sales_invoices`.`inv_cust_id`
        where `inv_comp_id` = $company_code and `inv_comm` > 0 and inv_acc_voucher is not null";
        $tag = 'Paid';

      }elseif ($request->input('submit') == "html_2"  ||
                $request->input('submit') == "pdf" ){
        $sql = "select inv_date,inv_no,trans_type,voucher_no,cust_name,inv_acc_voucher,
        SUM((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100)) as inv_netamt,
        case when cust_overall_comm<=0 then SUM(inv_qty*inv_comm) else
        SUM(((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100))* inv_comm/100)  end as commission
        from `sales_invoices`
        inner join `sales_invoice_details` on `inv_mas_id` = `sales_invoices`.`id`
        left join `view_acc_trans_invoices` on `inv_no` = `acc_invoice_no` and trans_type = 'SV'
        inner join `customers` on `customers`.`id` = `sales_invoices`.`inv_cust_id`
        where `inv_comp_id` = $company_code and `inv_comm` > 0 and inv_acc_voucher is null";
        $tag = 'Paid';
      }else{
        $sql = "select inv_date,inv_no,trans_type,voucher_no,cust_name,inv_acc_voucher,
        SUM((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100)) as inv_netamt,
        case when cust_overall_comm<=0 then SUM(inv_qty*inv_comm) else
        SUM(((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100))* inv_comm/100)  end as commission
        from `sales_invoices`
        inner join `sales_invoice_details` on `inv_mas_id` = `sales_invoices`.`id`
        left join `view_acc_trans_invoices` on `inv_no` = `acc_invoice_no` and trans_type = 'SV'
        inner join `customers` on `customers`.`id` = `sales_invoices`.`inv_cust_id`
        where `inv_comp_id` = $company_code and `inv_comm` > 0";
        $tag = 'ALL';
    }
    if($request->filled('customer_id')){
        $customer_id = $request->get('customer_id');
        $sql .= " and inv_cust_id ='".$customer_id."'";
    }
    if($request->filled('fromdate')){
      $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
      $sql .= " and inv_date >='".$fromdate."'";
    }
    if($request->filled('todate')){
      $todate  = date('Y-m-d',strtotime($request->get('todate')));
      $sql .= " and inv_date <='".$todate."'";
    }
    $sql .= " GROUP BY inv_date,inv_no,trans_type,voucher_no,cust_name,inv_acc_voucher,cust_overall_comm";
    $sql .= " order by `cust_name` asc";
    $rows = DB::select($sql);
    
    if ($request->input('submit') == "pdf"){
        $fileName = 'sales_commission_report.pdf';
        $pdf = PDF::loadView('/sales/reports/rpt_inv_commission_list_pdf',
        compact('rows','companies','company_code','fromdate','todate',), [], [
          'title' => $fileName,
    ]);
    return $pdf->stream($fileName,'.pdf');
   }
   
     // get requested action
     return view('/sales/reports/rpt_inv_commission_list',
     compact('rows','companies','company_code','customers','customer_id','fromdate','todate' ));
   }
   
   public function getCustWiseSalesReport(Request $request)
   {
        $dropdownscontroller = new DropdownsController();
        $generalscontroller = new GeneralsController();
        $companies    = $dropdownscontroller->comboCompanyAssignList();
        if($request->filled('company_code')){
        $company_code = $request->get('company_code');
        }else{
        $company_code = $dropdownscontroller->defaultCompanyCode();
        }
        $comp_name = $generalscontroller->CompanyName($company_code);

        $salespersons = CustomerSalesPersons::query()->orderBy('sales_name','asc')->get();

        $fromdate = date('Y-m-d');
        $todate   = date('Y-m-d');
        $sp_id = 0;
        $sp_name = '';
        if($request->filled('fromdate')){
            $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
        }
        if($request->filled('fromdate')){
            $todate = date('Y-m-d',strtotime($request->get('todate')));
        }

        $sql = "SELECT customers.id,IFNULL(sales_name,'Unknown') as sales_name,cust_chartofacc_id,cust_name,cust_add1,cust_add2,SUM(inv_net_amt) as inv_net_amt,
        SUM(inv_vat_value) as inv_vat_value,SUM(collection) as collection ,SUM(outstanding) as outstanding
        FROM customers
        LEFT JOIN (SELECT inv_cust_id, SUM(inv_net_amt) as inv_net_amt,SUM(inv_vat_value) as inv_vat_value from sales_invoices
        where inv_date between '".$fromdate."' and '".$todate."' GROUP BY inv_cust_id) as sales ON customers.id = inv_cust_id
        LEFT JOIN (SELECT chart_of_acc_id,SUM(c_amount) as collection FROM acc_transactions inner join acc_transaction_details on acc_trans_id = acc_transactions.id
        where trans_type in ('CR','BR') and voucher_date between '".$fromdate."' and '".$todate."' GROUP BY chart_of_acc_id ) as collection ON collection.chart_of_acc_id = cust_chartofacc_id
        LEFT JOIN (SELECT chart_of_acc_id,SUM(d_amount)-SUM(c_amount) as outstanding FROM acc_transactions
        inner join acc_transaction_details on acc_trans_id = acc_transactions.id GROUP BY chart_of_acc_id ) as outstanding ON outstanding.chart_of_acc_id = cust_chartofacc_id
        LEFT JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
        Where cust_com_id = $company_code ";
        if($request->filled('sp_id')){
          $sp_id = $request->get('sp_id');
          $sp_name = $generalscontroller->getSalesPersonName($sp_id);
          $sql .= " and cust_sales_per_id =".$sp_id;
        }
        $sql .= " GROUP BY customers.id,sales_name,cust_chartofacc_id,cust_name,cust_add1,cust_add2 order by sales_name asc";
        $rows = DB::select($sql);
    
        if ($request->input('submit') == "pdf"){
            $fileName = 'sales_person_wise_sales.pdf';
            $pdf = PDF::loadView('/sales/reports/rpt_cust_wise_sales_summary_pdf',
            compact('rows','salespersons','sp_id','companies','company_code','comp_name','fromdate','todate','sp_name',), [], [
              'title' => $fileName,
        ]);
        return $pdf->stream($fileName,'.pdf');
        }
        // get requested action
        return view('/sales/reports/rpt_cust_wise_sales_summary',
        compact('rows','salespersons','sp_id','companies','company_code','fromdate','todate' ));
   }

   public function getSPWiseSalesReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }

    $salespersons = CustomerSalesPersons::query()->orderBy('sales_name','asc')->get();

    $fromdate = date('Y-m-d');
    $todate   = date('Y-m-d');
    $sp_id = 0;

    if($request->filled('fromdate')){
      $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
    }
    if($request->filled('fromdate')){
      $todate = date('Y-m-d',strtotime($request->get('todate')));
    }

  $sql = "SELECT * FROM
  (select IFNULL(customer_sales_persons.id,0) as id,sales_name, SUM(inv_net_amt) as inv_net_amt,
  SUM(inv_vat_value) as inv_vat_value  FROM customers
  INNER JOIN sales_invoices on customers.id = inv_cust_id
  LEFT JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
  Where inv_comp_id = $company_code and inv_date between '".$fromdate."' AND '".$todate."' Group BY customer_sales_persons.id,
  sales_name) AS SP
  LEFT JOIN
  (SELECT IFNULL(customer_sales_persons.id,0) as id,SUM(c_amount) as collection FROM acc_transactions
  inner join acc_transaction_details on acc_trans_id = acc_transactions.id
  INNER JOIN customers on customers.cust_chartofacc_id = chart_of_acc_id
  LEFT JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
  where trans_type in ('CR','BR') and com_ref_id = $company_code and voucher_date between '".$fromdate."' AND '".$todate."'
  GROUP BY customer_sales_persons.id ) as collection ON collection.id = SP.id
  LEFT JOIN (SELECT IFNULL(customer_sales_persons.id,0) as id,SUM(d_amount)-SUM(c_amount) as outstanding
  FROM acc_transactions inner join acc_transaction_details on acc_trans_id = acc_transactions.id
  INNER JOIN customers on customers.cust_chartofacc_id = chart_of_acc_id
  LEFT JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
  Where com_ref_id = $company_code GROUP BY customer_sales_persons.id ) as outstanding ON outstanding.id = SP.id
  ORDER BY SP.id desc";
  
  $rows = DB::select($sql);
    
  if ($request->input('submit') == "pdf"){
        $fileName = 'sp_wise_sales_summary.pdf';
        $pdf = PDF::loadView('/sales/reports/rpt_sp_wise_sales_summary_pdf',
        compact('rows','companies','company_code','fromdate','todate',), [], [
          'title' => $fileName,
    ]);
    return $pdf->stream($fileName,'.pdf');
   }
   
     // get requested action
     return view('/sales/reports/rpt_sp_wise_sales_summary',
     compact('rows','salespersons','sp_id','companies','company_code','fromdate','todate' ));
   }
   
   public function getTopItemSalesQty(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $generalscontroller = new GeneralsController();
     $comp_name = $generalscontroller->CompanyName($company_code);


     $q = SalesInvoices::query()
      ->join("sales_invoice_details", "inv_mas_id", "=", "sales_invoices.id")
      ->join("customers", "customers.id", "=", "sales_invoices.inv_cust_id")
      ->join("items", "items.id", "=", "sales_invoice_details.inv_item_id")
      ->join("item_categories", "item_categories.id", "=", "items.item_ref_cate_id")
      ->where('inv_comp_id', $company_code )
      ->selectRaw('itm_cat_name,item_name,inv_unit,SUM(inv_qty) as inv_qty,
      SUM(inv_qty*inv_item_price-(inv_qty*inv_item_price)*inv_itm_disc_per/100-(inv_qty*inv_item_price)*inv_disc_per/100 +
      (inv_qty*inv_item_price)*inv_vat_per/100) as inv_net_amt')
      ->GroupBy('itm_cat_name','item_name','inv_unit');

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
      if($request->filled('fromdate')){
          $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
          $q->where('inv_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
            $todate  = date('Y-m-d',strtotime($request->get('todate')));
          $q->where('inv_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }

     $rows = $q->orderBy('inv_qty', 'desc')->limit(20)->get();

     if ($request->input('submit') == "pdf"){
         $fileName = 'top_item_sales_by_qty.pdf';
         $pdf = PDF::loadView('/sales/reports/rpt_top_item_sales_qty_pdf',
         compact('rows','companies','company_code','comp_name','fromdate','todate',), [], [
           'title' => $fileName,
     ]);
     return $pdf->stream($fileName,'.pdf');
    }

     // get requested action
     return view('/sales/reports/rpt_top_item_sales_qty',
     compact('rows','companies','company_code','comp_name','fromdate','todate' ));
   }

   public function getTopItemSalesVolume(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $generalscontroller = new GeneralsController();
     $comp_name = $generalscontroller->CompanyName($company_code);

      $q = SalesInvoices::query()
       ->join("sales_invoice_details", "inv_mas_id", "=", "sales_invoices.id")
       ->join("customers", "customers.id", "=", "sales_invoices.inv_cust_id")
       ->join("items", "items.id", "=", "sales_invoice_details.inv_item_id")
       ->join("item_categories", "item_categories.id", "=", "items.item_ref_cate_id")
       ->where('inv_comp_id', $company_code )
       ->selectRaw('itm_cat_name,item_name,inv_unit,SUM(inv_qty) as inv_qty,
       SUM(inv_qty*inv_item_price-(inv_qty*inv_item_price)*inv_itm_disc_per/100-(inv_qty*inv_item_price)*inv_disc_per/100 +
       (inv_qty*inv_item_price)*inv_vat_per/100) as inv_net_amt')
       ->GroupBy('itm_cat_name','item_name','inv_unit');

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
      if($request->filled('fromdate')){
           $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
           $q->where('inv_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
             $todate  = date('Y-m-d',strtotime($request->get('todate')));
           $q->where('inv_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }

    $rows = $q->orderBy('inv_net_amt', 'desc')->limit(20)->get();

     if ($request->input('submit') == "pdf"){
         $fileName = 'top_item_sales_by_volume.pdf';
         $pdf = PDF::loadView('/sales/reports/rpt_top_item_sales_volume_pdf',
         compact('rows','companies','company_code','comp_name','fromdate','todate',), [], [
           'title' => $fileName,
        ]);
        return $pdf->stream($fileName,'.pdf');
      }

     // get requested action
     return view('/sales/reports/rpt_top_item_sales_volume',
     compact('rows','companies','company_code','comp_name','fromdate','todate' ));
   }
   
   public function getInvWiseProfitLossReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();

     $item_list =  Items::query()
       ->join("units", "unit_id", "=", "units.id")
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
       ->orderBy('item_name','asc')->get();

    /* $q = SalesInvoices::query()
      ->join("sales_invoice_details", "inv_mas_id", "=", "sales_invoices.id")
      ->join("customers", "customers.id", "=", "sales_invoices.inv_cust_id")
      ->join("items", "items.id", "=", "sales_invoice_details.inv_item_id")
      ->join("item_categories", "item_categories.id", "=", "items.item_ref_cate_id")
      ->where('inv_comp_id', $company_code )
      ->selectRaw('inv_date,inv_no,cust_name,cust_add1,cust_add2,inv_lot_no,
          item_name,itm_cat_name,itm_cat_origin,inv_item_price,inv_qty,inv_unit,
          sales_invoice_details.inv_disc_value,inv_net_amt, 
          case when cust_overall_comm<=0 then (inv_qty*inv_comm) else
          ((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100))* inv_comm/100 end as commission'); */

     $fromdate = date('Y-m-d');
     $todate  = date('Y-m-d');
     $overheadCost = '0';
     $freightOthers = '0';

      if($request->filled('overheadCost')){
        $overheadCost = $request->get('overheadCost');
      }
      if($request->filled('overheadCost')){
        $freightOthers = $request->get('freightOthers');
      }

      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate'))); 
      }
      if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate'))); 
      }
      
      $sql = "SELECT inv_date,inv_no,cust_name,cust_add1,cust_add2,item_name,itm_cat_name,itm_cat_origin,inv_item_price, inv_qty,inv_unit, 
      ((inv_qty*inv_item_price)*inv_itm_disc_per/100)-((inv_qty*inv_item_price)*inv_disc_per/100)  as inv_disc_value,inv_itm_disc_per,inv_disc_per,inv_net_amt as total_inv_net_amt,
      ((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100)-((inv_qty*inv_item_price)*inv_disc_per/100)) as inv_net_amt, 
      l_item_avg_price, case when cust_overall_comm<=0 then (inv_qty*inv_comm) else
      ((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100))* inv_comm/100 end as commission from `sales_invoices` 
      inner join `sales_invoice_details` on `inv_mas_id` = `sales_invoices`.`id` 
      inner join `customers` on `customers`.`id` = `sales_invoices`.`inv_cust_id` 
      inner join `items` on `items`.`id` = `sales_invoice_details`.`inv_item_id` 
      inner join `item_categories` on `item_categories`.`id` = `items`.`item_ref_cate_id` 
      inner join `view_item_avg_price` on `view_item_avg_price`.`l_item_ref_id` = `items`.`id` 
      where `inv_comp_id` = $company_code and `inv_date` >= '".$fromdate."' 
      and `inv_date` <= '".$todate ."' ";
      
      if($request->filled('customer_id')){
        $customer_id = $request->get('customer_id');
        $sql .= " AND sales_invoices.inv_cust_id = '".$customer_id."'";
      }
      if($request->filled('item_id')){
        $item_id = $request->get('item_id'); 
        $sql .= " AND items.id = '".$item_id."'";
      } 

    $sql .= "order by `sales_invoices`.`inv_date` asc";
  
    $rows = DB::select($sql);
    

     // get requested action
     return view('/sales/reports/rpt_inv_wise_profit_loss_list',
     compact('rows','customers','item_list','companies','company_code','fromdate','todate','overheadCost','freightOthers' ));
   }
   
   public function getItemWiseProfitLossReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();

     $item_list =  Items::query()
       ->join("units", "unit_id", "=", "units.id")
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
       ->orderBy('item_name','asc')->get();
 
      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
      $overheadCost = '0';
      $freightOthers = '0';

      if($request->filled('overheadCost')){
        $overheadCost = $request->get('overheadCost');
      }
      if($request->filled('overheadCost')){
        $freightOthers = $request->get('freightOthers');
      }

      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate'))); 
      }
      if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate'))); 
      }
      
      $sql = "select item_name,itm_cat_name,itm_cat_origin,avg(inv_item_price) as inv_item_price,
      SUM(inv_qty) as inv_qty,SUM(sales_invoice_details.inv_disc_value) AS inv_disc_value,
      SUM(((inv_qty*inv_item_price)*inv_itm_disc_per/100)-((inv_qty*inv_item_price)*inv_disc_per/100))as inv_disc_value, 
      SUM(((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100)-((inv_qty*inv_item_price)*inv_disc_per/100))) AS inv_net_amt, avg(l_item_avg_price) as l_item_avg_price,
      case when cust_overall_comm<=0 then SUM(inv_qty*inv_comm) else
      SUM(((inv_qty*inv_item_price)-((inv_qty*inv_item_price)*inv_itm_disc_per/100))* inv_comm/100) end as commission from `sales_invoices` 
      inner join `sales_invoice_details` on `inv_mas_id` = `sales_invoices`.`id` 
      inner join `customers` on `customers`.`id` = `sales_invoices`.`inv_cust_id` 
      inner join `items` on `items`.`id` = `sales_invoice_details`.`inv_item_id` 
      inner join `item_categories` on `item_categories`.`id` = `items`.`item_ref_cate_id` 
      inner join `view_item_avg_price` on `view_item_avg_price`.`l_item_ref_id` = `items`.`id` 
      where `inv_comp_id` =  $company_code and `inv_date` >= '".$fromdate."' and `inv_date` <= '".$todate ."' ";

      if($request->filled('customer_id')){
        $customer_id = $request->get('customer_id');
        $sql .= " AND sales_invoices.inv_cust_id = '".$customer_id."'";
      }
      if($request->filled('item_id')){
        $item_id = $request->get('item_id'); 
        $sql .= " AND items.id = '".$item_id."'";
      } 

     $sql .= " GROUP BY item_name,itm_cat_name,itm_cat_origin,cust_overall_comm 
            order by `item_name` asc";
  
    $rows = DB::select($sql);
    

     // get requested action
     return view('/sales/reports/rpt_item_wise_profit_loss_list',
     compact('rows','customers','item_list','companies','company_code','fromdate','todate','overheadCost','freightOthers' ));
   }
   
   
   public function getDateAttribute($value)
   {
       return Carbon::parse($value)->format('Y-m-d');
   }

   public function salesOrderListPending(Request $request)
   {  
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      if($request->filled('company_code')){
        $company_code = $request->get('company_code');
      }else{
        $company_code = $dropdownscontroller->defaultCompanyCode();
      }
      $customers = Customers::query()->orderBy('cust_name','asc')->get();

      $fromdate = date('Y-m-1');
      $todate  = date('Y-m-d');

      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate'))); 
      }
      if($request->filled('todate')){
          $todate  = date('Y-m-d',strtotime($request->get('todate'))); 
      }

      $customer_id = null;
      
    //   $sql = "SELECT
    //   `sales_orders_details`.`so_item_id` AS `itemId`,
    //   sales_orders.so_order_date as so_order_date,
    //   `sales_orders`.`so_fpo_no` AS `fpo`,
    //   `sales_orders`.`so_order_no` AS `order_no`,
    //   `sales_orders`.`so_confirmed_date` AS `fpoDate`,
    //   `customers`.`cust_name` AS `party`,
    //   `sales_orders_details`.`so_item_size` AS `size`,
    //   `sales_orders_details`.`so_item_spec` AS `spec`,
    //   `sales_orders_details`.`so_item_weight` AS `perPcsWeight`,
    //   `sales_orders_details`.`so_item_pcs` AS `orderPcs`,
    //   `sales_orders_details`.`so_order_qty` AS `orderKg`,
    //   `sales_orders`.`so_req_del_date` AS `expDate`,

    // (SELECT
    //     SUM(
    //         `sales_delivery_details`.`del_item_pcs`
    //     ) AS `item_pcs`
    // FROM
    //     `sales_delivery_details`
    // JOIN `sales_deliveries` ON
    //         (
    //             `sales_deliveries`.`del_sal_ord_id` = `sales_orders`.`id`
    //         )
    // WHERE
    //     `sales_delivery_details`.`del_item_id` = `sales_orders_details`.`so_item_id` 
    //     AND `sales_delivery_details`.`del_ref_id` = `sales_deliveries`.`id`) AS `del_item_pcs`,
    
    // (SELECT
    //     SUM(
    //         `sales_delivery_details`.`del_item_weight`
    //     ) AS `item_kg`
    // FROM
    //     `sales_delivery_details`
    // JOIN `sales_deliveries` ON
    //         (
    //             `sales_deliveries`.`del_sal_ord_id` = `sales_orders`.`id`
    //         )
    // WHERE
    //     `sales_delivery_details`.`del_item_id` = `sales_orders_details`.`so_item_id` 
    //     AND `sales_delivery_details`.`del_ref_id` = `sales_deliveries`.`id`) AS `del_item_kg`

    // FROM `sales_orders`
    //     JOIN `sales_orders_details` ON `sales_orders_details`.`so_order_id` = `sales_orders`.`id`
    //     JOIN `customers` ON `customers`.`id` = `sales_orders`.`so_cust_id`  

    // where so_order_date BETWEEN '".$fromdate."' and '".$todate."'";
      
    // $pendingItems = DB::select($sql);

    if($request->filled('customer_id')){
      $customer_id = $request->customer_id;
    }


    $pendingItems = SalesOrders::query()
    ->with('items', 'customer')
    ->whereBetween('so_order_date', [$fromdate, $todate]);
    if($request->filled('customer_id')){
      $pendingItems->where('so_cust_id', $request->customer_id);
    }
    $pendingItems = $pendingItems->orderBy('id', 'DESC')->get();

    // return $pendingItems;

      if ($request->input('submit') == "pdf"){
        $fileName = 'top_item_sales_by_volume.pdf';
        $pdf = PDF::loadView('sales/reports/rpt_sales_order_pending_pdf',
          compact('company_code','customers', 'companies', 'pendingItems', 
          'fromdate', 'todate'), [], [
            'title' => 'sales_order_pending',
        ]);
        return $pdf->stream($fileName,'.pdf');  
      }


     return view('/sales/reports/rpt_sales_order_pending', compact('company_code','customers', 'customer_id', 'companies', 'pendingItems', 
     'fromdate', 'todate'));
   }
 
   public function getCustomerStatementReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
     $companies = $dropdownscontroller->comboCompanyAssignList();

     $customers = Customers::query()->orderBy('cust_name','asc')->get();

     $ledger_id = 0;
     $ledgername = '';
     $acc_Ledger = '';
     $fromdate  = date('Y-m-d');
     $todate    = date('Y-m-d');

     if($request->filled('company_code') && $request->filled('ledger_id')
      && $request->filled('fromdate') && $request->filled('todate')){
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $ledger_id  = $request->input('ledger_id');
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
        $ledgername = $generalscontroller->accountNameLookup($ledger_id);
        $cust_data = $generalscontroller->getCustomerInfByLedgerId($ledger_id);
      }


      // $customer_id = Chartofaccounts::where('id', $ledger_id)->first()->customerId;

      //ledger opening balance
      $opening = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->selectRaw('sum(d_amount) as debit, sum(c_amount) as credit')
      ->where('com_ref_id', $default_comp_code )
      ->where('chart_of_acc_id', $ledger_id )
      ->whereDate('voucher_date','<' ,date('Y-m-d', strtotime($fromdate)))->first();

      // return $rows;
      $order = SalesOrders::join("chartofaccounts", "chartofaccounts.customerId", "=", "sales_orders.so_cust_id")
        ->leftjoin("acc_transaction_details", "acc_transaction_details.chart_of_acc_id", "=", "chartofaccounts.id")
        ->where('acc_transaction_details.chart_of_acc_id', $ledger_id)
        ->selectRaw('so_order_date, so_net_amt, so_order_no, SUM(c_amount) as c_amount, SUM(d_amount), acc_head')
        ->groupBy('sales_orders.so_order_date', 'so_net_amt', 'so_order_no', 
        'acc_head')
        ->get();

      // $rows = AccTransactions::query()
      //   ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      //   ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
      //   // ->join("sales_orders", "sales_orders.so_cust_id", "=", "chartofaccounts.customerId")
      //   ->whereBetween('voucher_date', [$fromdate,$todate])
      //   ->where('com_ref_id', $default_comp_code )
      //   ->where('chart_of_acc_id', $ledger_id )
      //   ->selectRaw('trans_type,voucher_no,voucher_date,t_narration,d_amount,c_amount,
      //   acc_invoice_no, chart_of_acc_id')
      //   ->orderBy('voucher_date', 'asc')
      //   ->get();  ->join("customers", "customers.id", "=", "so_cust_id")

      $sql = "SELECT `trans_type` as trans_type, `voucher_date` as voucher_date, 
      `t_narration` as t_narration, c_amount as c_amount, d_amount as d_amount 
      FROM `acc_transactions` JOIN acc_transaction_details ON 
      acc_transaction_details.acc_trans_id = acc_transactions.id
      where chart_of_acc_id = '".$ledger_id."' and voucher_date BETWEEN '".$fromdate."' and '".$todate."'
      union all SELECT so_order_title as trans_type, so_order_date as voucher_date, 
      so_reference as t_narration, so_net_amt as c_amount, so_gross_amt as d_amount
      FROM sales_orders JOIN customers ON customers.id = sales_orders.so_cust_id 
      where so_order_date BETWEEN '".$fromdate."' and '".$todate."' 
      and customers.cust_chartofacc_id ='".$ledger_id."' and sales_orders.deleted_at IS null 
      ORDER BY voucher_date ";
      
      $rows = DB::select($sql);

      // return $rows;
      if ($request->input('submit') == "pdf"){
            $fileName = 'subsidiary_ledger';

        $pdf = PDF::loadView('/accounts/reports/rpt_custmer_statement_pdf',
            compact('comp_name','comp_add','rows','opening','companies','default_comp_code',
            'cust_data','ledgername','fromdate', 'todate',), [], [
              'title' => $fileName,
          ]);
        return $pdf->stream($fileName,'.pdf');
       }
    
     // get requested action
     return view('/sales/reports/rpt_custmer_order_statement',
     compact('rows','opening','companies','default_comp_code',
     'customers','ledger_id','fromdate', 'todate'));

   }

}
