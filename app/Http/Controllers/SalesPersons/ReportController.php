<?php

namespace App\Http\Controllers\SalesPersons;

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
use App\Models\Customers\Customers;
use App\Models\Salespersons\CustomerSalesPersons;

use PDF;
use Response;

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {

   }

   public function getSalesPersonList(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }

     $q = CustomerSalesPersons::query()
          ->join("sys_infos", "sys_infos.id", "=", "sales_desig")
          ->where('sales_comp_id', $company_code)
          ->where('vComboType', 'Designation')
          ->selectRaw('customer_sales_persons.id,sales_comp_id,sales_name,sales_desig,
          sales_mobile,sales_email,vComboType,vComboName,vComboDesc');
     $sale_per_name = '';
     if($request->filled('sale_per_name')){
       $sale_per_name = $request->get('sale_per_name');
       $q->where('sales_name','like', $sale_per_name.'%');
     }

     $rows = $q->orderBy('customer_sales_persons.id', 'desc')->get();

     // get requested action
     return view('/salespersons/reports/rpt_salespersons_list', compact('rows','companies',
     'company_code','sale_per_name'));
   }

}
