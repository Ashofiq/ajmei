<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Items\Items;
use App\Models\Companies;
use App\Models\Items\ItemCategories;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use Carbon\Carbon;


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
          ->join("chartofaccounts", "chartofaccounts.id", "=", "cust_chartofacc_id")
          ->where('cust_com_id', $company_code)
          ->selectRaw('cust_code,cust_slno,file_level,cust_name,cust_add1,cust_add2,
          cust_mobile,cust_phone');

     $customer_name = '';
     if($request->filled('customer_name')){
       $customer_name = $request->get('customer_name');
       $q->where('cust_name','like', $customer_name.'%');
     }

     $rows = $q->orderBy('customers.cust_code', 'asc')->get();


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

  public function getItemBarcodeList(Request $request)
  {
    $item_id = $request->get('item_id');
    $dropdownscontroller = new DropdownsController();
    $companies    = $dropdownscontroller->comboCompanyAssignList();
    $company_code = $dropdownscontroller->defaultCompanyCode();
    $unit_list = $dropdownscontroller->comboUnitsList($company_code);

    $item_list =  Items::query()
      ->join("units", "unit_id", "=", "units.id")
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $company_code)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')
      ->orderBy('item_name','asc')->get();

    $q = Items::query()
         ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
         ->join("units", "units.id", "=", "unit_id")
          ->where('item_ref_comp_id', $company_code );

    if($item_id>0)
      $q->where('items.id', $item_id );

    $q->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
         item_desc,item_level,item_qr_code,item_bar_code,item_origin,
         itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price');

      if ($request->input('submit') == "html_1"){
          $rows = $q->orderBy('items.id', 'desc')->get();
          return view ('/barcode/reports/rpt_itm_barcode_list',
          compact('rows','item_list','unit_list'));
       }else{
         $rows = $q->orderBy('items.id', 'desc')->paginate(10)->setpath('');
         $rows->appends(array(
           'item_id' => $item_id,
         ));
       }

    return view ('/barcode/itm_barcode_list', compact('rows','item_list','unit_list'));
    //->renderSections()['content'];
  }


}
