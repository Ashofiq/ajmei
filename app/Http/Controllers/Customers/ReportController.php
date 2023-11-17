<?php

namespace App\Http\Controllers\Customers;

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

use App\Models\CompaniesAssigns;
use App\Models\Chartofaccounts;
use App\Models\Customers\Customers; 
use App\Models\Salespersons\CustomerSalesPersons;
use App\Models\Customers\CustomerPrices;

use PDF;
use Response;

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {

   }

   public function getCustomerList(Request $request)
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
     $comp_add = $generalscontroller->CompanyAddress($company_code);
     
     $q = Customers::query()
          ->leftjoin("chartofaccounts", "chartofaccounts.id", "=", "cust_chartofacc_id")
          ->where('cust_com_id', $company_code)
          ->selectRaw('cust_code,cust_slno,file_level,cust_name,cust_add1,cust_add2,cust_mobile,cust_phone');

     $customer_name = '';
     if($request->filled('customer_name')){
       $customer_name = $request->get('customer_name');
       $q->where('cust_name','like', $customer_name.'%');
     }

     $rows = $q->orderBy('cust_slno', 'asc')->get();


     if ($request->input('submit') == "pdf"){
         $fileName = 'customer_list';
         $pdf = PDF::loadView('/customers/reports/rpt_customers_list_pdf',
         compact('rows','companies','company_code','customer_name','comp_name',), [], [
           'title' => $fileName,
     ]);
     return $pdf->stream($fileName,'.pdf');
    }

    $collect = collect($rows);
    // get requested action
    return view('/customers/reports/rpt_customers_list', compact('rows','companies',
    'company_code','customer_name','comp_name'));

  }
  
  public function getPendingSPCustList(Request $request)
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
    $comp_add = $generalscontroller->CompanyAddress($company_code);

    $q = Customers::query()
         ->join("chartofaccounts", "chartofaccounts.id", "=", "cust_chartofacc_id")
         ->leftjoin("customer_sales_persons", "customer_sales_persons.id", "=", "cust_sales_per_id")
         ->where('cust_com_id', $company_code)
         ->whereNull('cust_sales_per_id')
         ->selectRaw('cust_code,cust_slno,file_level,cust_name,cust_add1,cust_add2,
         cust_mobile,cust_phone');

    $customer_name = '';
    if($request->filled('customer_name')){
      $customer_name = $request->get('customer_name');
      $q->where('cust_name','like', $customer_name.'%');
    }

   $rows = $q->orderBy('customers.cust_code', 'asc')->get();

   $collect = collect($rows);
   // get requested action
   return view('/customers/reports/rpt_pending_sp_cust_list', compact('rows','companies',
   'company_code','customer_name','comp_name'));

 }
  
  public function getSpWiseCustomerList(Request $request)
  {
    $dropdownscontroller = new DropdownsController();
    $companies    = $dropdownscontroller->comboCompanyAssignList();
    if($request->filled('company_code')){
      $company_code = $request->get('company_code');
    }else{
      $company_code = $dropdownscontroller->defaultCompanyCode();
    }

    $salespersons = CustomerSalesPersons::query()->orderBy('sales_name','asc')->get();
    $generalscontroller = new GeneralsController();
    $comp_name = $generalscontroller->CompanyName($company_code);
    $comp_add = $generalscontroller->CompanyAddress($company_code);
    $sales_person_data = $generalscontroller->getSalesPersonInf($company_code,1);
    $q = Customers::query()
         ->join("chartofaccounts", "chartofaccounts.id", "=", "cust_chartofacc_id")
         ->where('cust_com_id', $company_code)
         ->selectRaw('cust_code,cust_slno,file_level,cust_name,cust_add1,cust_add2,
         cust_mobile,cust_phone');

    $salesperson_id = '';
    if($request->filled('salesperson_id')){
      $salesperson_id = $request->get('salesperson_id');
      $q->where('cust_sales_per_id','=', $salesperson_id);
      $sales_person_data = $generalscontroller->getSalesPersonInf($company_code,$salesperson_id);
    }

    $rows = $q->orderBy('customers.cust_code', 'asc')->get();


    if ($request->input('submit') == "pdf"){
        $fileName = 'customer_list';
        $pdf = PDF::loadView('/customers/reports/rpt_sp_wise_customers_list_pdf',
        compact('rows','companies','company_code','sales_person_data','comp_name',), [], [
          'title' => $fileName,
    ]);
    return $pdf->stream($fileName,'.pdf');
   }

   $collect = collect($rows);
   // get requested action
   return view('/customers/reports/rpt_sp_wise_customers_list', compact('rows','companies',
   'company_code','comp_name','salespersons','salesperson_id','sales_person_data'));

 }

 public function getCustWisePriceList(Request $request)
 {
   $dropdownscontroller = new DropdownsController();
   $companies    = $dropdownscontroller->comboCompanyAssignList();
   if($request->filled('company_code')){
     $company_code = $request->get('company_code');
   }else{
     $company_code = $dropdownscontroller->defaultCompanyCode();
   }

   $customers = Customers::query()->orderBy('cust_name','asc')->get();
   $generalscontroller = new GeneralsController();
   $comp_name = $generalscontroller->CompanyName($company_code);
   $comp_add = $generalscontroller->CompanyAddress($company_code);
   $customer_data = $generalscontroller->getCustomerInf(0);

   $q = CustomerPrices::query()
      ->Join("items", "items.id", "=", "cust_item_p_id")
      ->join("units", "units.id", "=", "unit_id")
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('p_del_flag', '=', 0)
      ->where('cust_p_com_ref_id', '=', $company_code)
      ->selectRaw('customer_prices.id, cust_price,p_valid_from,p_valid_to,p_del_flag,
      item_code,item_name,itm_cat_name,vUnitName,packing_id,size');

   $customer_id = '';
   if($request->filled('customer_id')){
     $customer_id = $request->get('customer_id');
     $q->where('cust_p_ref_id','=', $customer_id);
     $customer_data = $generalscontroller->getCustomerInf($customer_id);
   }else{
      $q->where('cust_p_ref_id','=', '0');
   }
   $rows = $q->orderBy('customer_prices.id', 'asc')->get();

   if ($request->input('submit') == "pdf"){
       $fileName = 'customer_list';
       $pdf = PDF::loadView('/customers/reports/rpt_cust_wise_price_list_pdf',
       compact('rows','companies','company_code','customer_data','comp_name',), [], [
         'title' => $fileName,
   ]);
   return $pdf->stream($fileName,'.pdf');
  }

  $collect = collect($rows);
  // get requested action
  return view('/customers/reports/rpt_cust_wise_price_list', compact('rows','companies',
  'company_code','comp_name','customers','customer_id','customer_data'));

 }
 

}
