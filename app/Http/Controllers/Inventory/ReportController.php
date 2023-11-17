<?php

namespace App\Http\Controllers\Inventory;

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
use App\Models\Rawmaterials\RawMaterialsReceives;
use App\Models\CompaniesAssigns;
use App\Models\Customers\Customers;
use App\Models\Suppliers\Suppliers;
use App\Models\Items\Items;
use App\Models\Items\ItemCategories;
use App\Models\Items\ItemStocks;
use App\Models\Sales\SalesDeliveries;
use App\Models\Sales\SalesDeliveryDetails;
use App\Models\Rawmaterials\RawMaterialsReceivesDetails;
use App\Models\Inventory\ItemPurchases;
use App\Models\Inventory\ItemPurchasesDetails;
use App\Models\Inventory\ItemDamages;
use App\Models\Inventory\ItemDamagesDetails;
use App\Models\Inventory\ItemShortages;
use App\Models\Inventory\ItemShortagesDetails;
use App\Models\Inventory\ItemExpires;
use App\Models\Inventory\ItemExpiresDetails;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use PDF;
use Response;

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {

   }
   
  public function getItemTreeViewReport(Request $request)
  {
    //identifiy the company code of user
    $dropdownscontroller = new DropdownsController();
    $companycode = $dropdownscontroller->defaultCompanyCode();
    $companies  = $dropdownscontroller->comboCompanyAssignList();

    $sql = "select * from `item_categories`
    where `itm_comp_id` = $companycode Order By itm_cat_name asc";
    $itm_cat = DB::select($sql);
    // get requested action
    return view('/inventory/reports/rpt_itm_tree_view', compact('companycode','itm_cat'));
  }

  public function getItemOpeningReport(Request $request)
  {

    $dropdownscontroller = new DropdownsController();
    $companies    = $dropdownscontroller->comboCompanyAssignList();
    if($request->filled('company_code')){
      $company_code = $request->get('company_code');
    }else{
      $company_code = $dropdownscontroller->defaultCompanyCode();
    }
    $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
    $generalscontroller = new GeneralsController();
    $comp_name = $generalscontroller->CompanyName($company_code);
    $comp_add = $generalscontroller->CompanyAddress($company_code);

    $customers = Customers::query()->orderBy('cust_name','asc')->get();
    $fromdate = date('Y-m-d');
    $todate  = date('Y-m-d');
    $itm_warehouse = '';
    $ware_name = '';
    $item_id = '';

    $item_list =  Items::query()
      ->join("units", "unit_id", "=", "units.id")
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $company_code)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
      ->orderBy('item_name','asc')->get();

      if($request->filled('itm_warehouse')) {
       $itm_warehouse = $request->get('itm_warehouse');
       $ware_name = $generalscontroller->WarehouseName($itm_warehouse);
      }
      if($request->filled('item_id')) {
       $item_id = $request->get('item_id');
      }
      if ($request->filled('fromdate') && $request->filled('todate')){
       $fromdate = date('Y-m-d',strtotime($request->fromdate));
       $todate = date('Y-m-d',strtotime($request->todate));
      }

    $sql = "select `item_code`, `item_name`, `itm_cat_name`, `item_warehouse_id`, `ware_name`, `item_storage_loc`, `item_lot_no`,
   `item_op_dt`, `item_stocks`.`item_op_stock`,item_base_price from `item_stocks` inner join `items` on `item_ref_id` = `items`.`id`
    inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id` and `itm_comp_id` = item_ref_comp_id
    inner join `warehouses` on `item_warehouse_id` = `warehouses`.`id`";

  $where = " where item_trans_desc = 'OP'";
  $where .= " and `item_stocks`.`item_op_dt` between '$fromdate' and '$todate'";
  if($item_id != '') $where .= ' and `item_ref_id` = '.$item_id;
  if($itm_warehouse != '')  $where .= ' and `item_warehouse_id` ='.$itm_warehouse;
  
 $sql .= $where;
 $sql .= ' Order by item_code asc';
 $rows = DB::select($sql);
    if ($request->input('submit') == "pdf"){
        $fileName = 'item_opening';
        $pdf = PDF::loadView('/inventory/reports/rpt_item_opening_pdf',
        compact('rows','companies','fromdate','todate','company_code','comp_name',
        'ware_name','itm_warehouse','item_id',), [], [
        'title' => $fileName,
    ]);
    return $pdf->stream($fileName,'.pdf');
  }

    // get requested action
    return view('/inventory/reports/rpt_item_opening',
    compact('rows','companies','fromdate','todate', 'company_code','warehouse_list','item_list',
    'itm_warehouse','item_id'));
  }
  
   public function getItemStockReport(Request $request)
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
     $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
     $customers = Customers::query()->orderBy('cust_name','asc')->get();
     $fromdate = date('Y-m-d');
     $todate  = date('Y-m-d');
     $itm_warehouse = '';
     $item_list =  ItemCategories::query()  
       ->where('itm_comp_id', '=', $company_code)
       ->where('parent_id', '=', 0) 
       ->select('item_categories.id','itm_cat_code','itm_cat_name','itm_cat_origin')
       ->orderBy('itm_cat_name','asc')->get();

      $sql =  "SELECT item_code,item_name,itm_cat_name,itm_cat_origin,max(l_item_base_price) as price,
     SUM(OP) as OP,SUM(GR) as GR,SUM(GI) as GI,SUM(FR) as FR,SUM(SA) as SA,SUM(CI) as CI,SUM(RT) as RT,SUM(SR) as SR,SUM(DA) as DA,SUM(SH) as SH,SUM(EX) as EX
      FROM ( SELECT item_code,item_name,itm_cat_name,itm_cat_origin,max(l_item_base_price) as l_item_base_price,SUM(OP)+SUM(GR)+SUM(GI)+SUM(FR)+SUM(SA)+SUM(CI)+ SUM(RT)+ SUM(EX)+SUM(DA)+SUM(SH) as OP,0 as GR,0 as GI, 0 as FR, 0 as SA,0 as CI,0 as RT,0 as SR,0 as DA,0 as SH,0 as EX
      FROM view_item_ledger
      inner join items on items.id = item_ref_id
      INNER JOIN item_categories on item_ref_cate_id=item_categories.id and itm_comp_id = item_ref_comp_id
      LEFT JOIN view_item_last_price on l_item_op_comp_id = item_ref_comp_id and l_item_lot_no = item_lot_no
      AND l_item_ref_id = item_ref_id
      Where item_op_comp_id = $company_code and item_op_dt < '$fromdate' GROUP BY item_code,item_name,itm_cat_name,itm_cat_origin
      UNION ALL
      SELECT item_code,item_name,itm_cat_name,itm_cat_origin,max(l_item_base_price) as l_item_base_price,SUM(OP) as OP,SUM(GR) as GR,SUM(GI) as GI,SUM(FR) as FR,SUM(SA) as SA,SUM(CI) as CI,SUM(RT) as RT,SUM(SR) as SR,SUM(DA) as DA,SUM(SH) as SH,SUM(EX) as EX
      FROM view_item_ledger
      inner join items on items.id = item_ref_id
      INNER JOIN item_categories on item_ref_cate_id=item_categories.id and itm_comp_id = item_ref_comp_id
      LEFT JOIN view_item_last_price on l_item_op_comp_id = item_ref_comp_id and l_item_lot_no = item_lot_no  AND l_item_ref_id = item_ref_id
      Where item_op_comp_id = $company_code and item_op_dt BETWEEN '$fromdate' and '$todate' GROUP BY
      item_code,item_name,itm_cat_name,itm_cat_origin ) AS MAIN GROUP BY
      itm_cat_origin,  itm_cat_name, item_code, item_name";

    //  echo $sql;
      $rows = DB::select($sql);

      $order_no = '';
      $item_id='';
      $item_s = '';
    // if($request->filled('item_id')){
    //   $item_id = $request->get('item_id');
    //   $item_s = ' and item_ref_id = '.$item_id;
    // }

    if($request->filled('item_id')){
      $item_id = $request->get('item_id');
      $item_s = ' and itm_cat_code like "'.$item_id.'%"';
    }

    if($request->filled('itm_warehouse')){
       $itm_warehouse = $request->get('itm_warehouse');
       $ware_name = $generalscontroller->WarehouseName($itm_warehouse);
       $fromdate = date('Y-m-d',strtotime($request->fromdate));
       $todate = date('Y-m-d',strtotime($request->todate));

      $sql =  "SELECT item_code,item_name,itm_cat_name,itm_cat_origin,max(l_item_base_price) as price,
       SUM(OP) as OP,SUM(GR) as GR,SUM(GI) as GI,SUM(FR) as FR,SUM(SA) as SA,SUM(CI) as CI,SUM(RT) as RT,SUM(SR) as SR,SUM(DA) as DA,SUM(SH) as SH,SUM(EX) as EX
       FROM (SELECT item_code,item_name,itm_cat_name,itm_cat_origin,max(l_item_base_price) as l_item_base_price,SUM(OP)+SUM(GR)+SUM(GI)+SUM(FR)+SUM(SA)+SUM(CI)+ SUM(RT)+ SUM(SR)+SUM(DA)+SUM(SH) as OP, 0 as GR,0 as GI, 0 as FR, 0 as SA,0 as CI,0 as RT,0 as SR,0 as DA,0 as SH,0 as EX
       FROM view_item_ledger
       inner join items on items.id = item_ref_id
       INNER JOIN item_categories on item_ref_cate_id=item_categories.id and itm_comp_id = item_ref_comp_id
       LEFT JOIN view_item_last_price on l_item_op_comp_id = item_ref_comp_id and l_item_lot_no = item_lot_no
       AND l_item_ref_id = item_ref_id
       Where item_op_comp_id = $company_code and item_warehouse_id = $itm_warehouse
       and item_op_dt < '$fromdate' $item_s GROUP BY item_code,item_name,itm_cat_name,itm_cat_origin 
       UNION ALL
       SELECT item_code,item_name,itm_cat_name,itm_cat_origin, max(l_item_base_price) as l_item_base_price,SUM(OP) as OP,SUM(GR) as GR,SUM(GI) as GI,SUM(FR) as FR,SUM(SA) as SA,SUM(CI) as CI,SUM(RT) as RT,SUM(SR) as SR,SUM(DA) as DA,SUM(SH) as SH,SUM(EX) as EX
       FROM view_item_ledger
       inner join items on items.id = item_ref_id
       INNER JOIN item_categories on item_ref_cate_id = item_categories.id and itm_comp_id = item_ref_comp_id
       LEFT JOIN view_item_last_price on l_item_op_comp_id = item_ref_comp_id and l_item_lot_no = item_lot_no
       AND l_item_ref_id = item_ref_id
       Where item_op_comp_id = $company_code and item_warehouse_id = $itm_warehouse
       and item_op_dt BETWEEN '$fromdate' and '$todate' $item_s GROUP BY
       item_code,item_name,itm_cat_name,itm_cat_origin order by itm_cat_name asc) AS MAIN GROUP BY
      item_code,item_name,itm_cat_name,itm_cat_origin ";
       
      /* having
       SUM(OP) > -1  or SUM(GR) > 0 or SUM(ST) > 0 or SUM(SR) > 0 or SUM(SA) > 0 or
       SUM(RT) > 0 or SUM(DA) > 0 or SUM(SH) > 0 or SUM(EX) > 0 */
       
       $rows = DB::select($sql);

     }

     if ($request->input('submit') == "pdf"){
            $fileName = 'item_opening';
            $pdf = PDF::loadView('/inventory/reports/rpt_item_stock_pdf',
            compact('rows','companies','fromdate','todate','company_code','comp_name',
            'comp_name','ware_name','itm_warehouse','item_id',), [], [
            'title' => $fileName,
        ]);
        return $pdf->stream($fileName,'.pdf');
     }
     // get requested action
     return view('/inventory/reports/rpt_item_stock', compact('rows','companies','fromdate','todate',
     'company_code','item_list','item_id','itm_warehouse','warehouse_list'));
     
   } 
   
   public function getItemStockLedgerDetailsReport(Request $request)
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

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
      $item_id = '';
      $opening = DB::select('SELECT * FROM item_stocks Where item_ref_id = -0');
      $transactions = DB::select('SELECT * FROM item_stocks Where item_ref_id = -0');

    if($request->filled('item_id') && $request->filled('fromdate')
    && $request->filled('todate')){
       $item_id = $request->get('item_id');
       $fromdate = date('Y-m-d',strtotime($request->fromdate));
       $todate = date('Y-m-d',strtotime($request->todate));
       $generalscontroller = new GeneralsController();
       $comp_name = $generalscontroller->CompanyName($company_code);
       $comp_add = $generalscontroller->CompanyAddress($company_code);
       $item = $generalscontroller->get_item_details_more($item_id);

       $sql =  "SELECT SUM(item_op_stock) as op FROM item_stocks
       Where item_ref_id = $item_id and item_op_comp_id = $company_code
       and item_op_dt < '".$fromdate."'";
       $opening = DB::select($sql);

       $sql =  "SELECT item_trans_ref_no,item_op_dt,item_trans_desc,item_trans_ref,
       sum(item_op_stock) as qty
       FROM item_stocks Where item_ref_id = $item_id and item_op_comp_id = $company_code
       and item_op_dt BETWEEN '".$fromdate."' and '".$todate."'
       Group By item_trans_ref_no,item_op_dt,item_trans_desc,item_trans_ref,item_trans_ref_no
       order by item_op_dt asc";
       $transactions = DB::select($sql);
     }

     if ($request->input('submit') == "pdf"){
         $fileName = 'item_ledger';
         $pdf = PDF::loadView('/inventory/reports/rpt_item_ledger_details_pdf',
         compact('opening','transactions','comp_name','fromdate','todate','company_code',
         'item',), [], [
         'title' => $fileName,
     ]);
     return $pdf->stream($fileName,'.pdf');
   }
   return view('/inventory/reports/rpt_item_ledger_details',
   compact('opening','transactions','companies','fromdate','todate','company_code',
   'item_id','item_list'));
 }
 
 
   public function getItemStockLedgerReport(Request $request)
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

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
      $item_id = '';
      $opening = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = 1111');
      $transactions = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = 1111');

    if($request->filled('item_id') && $request->filled('fromdate')
    && $request->filled('todate')){
       $item_id = $request->get('item_id');
       $fromdate = date('Y-m-d',strtotime($request->fromdate));
       $todate = date('Y-m-d',strtotime($request->todate));
       $generalscontroller = new GeneralsController();
       $comp_name = $generalscontroller->CompanyName($company_code);
       $comp_add = $generalscontroller->CompanyAddress($company_code);
       $item = $generalscontroller->get_item_details($item_id);

       $sql =  "SELECT item_ref_id,item_op_dt,SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,
       SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA FROM view_item_ledger
       Where item_ref_id = $item_id AND item_op_comp_id = $company_code
       AND item_op_dt < '".$fromdate."' GROUP BY item_ref_id,item_op_dt";
       $opening = DB::select($sql);

       $sql =  "SELECT item_ref_id,item_op_dt,SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,
       SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA  FROM view_item_ledger
       Where item_ref_id = $item_id AND item_op_comp_id = $company_code
       AND item_op_dt BETWEEN '".$fromdate."' and '".$todate."'
       GROUP BY item_ref_id,item_op_dt";
       $transactions = DB::select($sql);
     }

     if ($request->input('submit') == "pdf"){
         $fileName = 'item_ledger';
         $pdf = PDF::loadView('/inventory/reports/rpt_item_ledger_pdf',
         compact('opening','transactions','comp_name','fromdate','todate','company_code',
         'item',), [], [
         'title' => $fileName,
     ]);
     return $pdf->stream($fileName,'.pdf');
   }
   return view('/inventory/reports/rpt_item_ledger',
   compact('opening','transactions','companies','fromdate','todate','company_code',
   'item_id','item_list'));
 }

 public function getItemStockLedgerReportDateGroup(Request $request)
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
    
    $fromdate = date('Y-m-d');
    $todate  = date('Y-m-d');
    $item_id = '';
    $opening = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = -0');
    $transactions = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = -0');
    
    if($request->filled('item_id') && $request->filled('fromdate') && $request->filled('todate')){
     $item_id = $request->get('item_id');
     $fromdate = date('Y-m-d',strtotime($request->fromdate));
     $todate = date('Y-m-d',strtotime($request->todate));
     $generalscontroller = new GeneralsController();
     $comp_name = $generalscontroller->CompanyName($company_code);
     $comp_add = $generalscontroller->CompanyAddress($company_code);
     $item = $generalscontroller->get_item_details_more($item_id);
    
    $sql =  "SELECT SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR,SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA FROM view_item_ledger Where item_ref_id = $item_id AND item_op_comp_id = $company_code AND item_op_dt < '".$fromdate."'";
    
    $opening = DB::select($sql);
    
    $sql =  "SELECT item_ref_id,item_warehouse_id,item_trans_ref_no,item_op_dt,item_trans_desc,cust_name,
     SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR, SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA  FROM view_item_ledger
     left join sales_deliveries on del_no = item_trans_ref_no
     left join customers on customers.id = del_cust_id
     Where item_ref_id = $item_id AND item_op_comp_id = $company_code
     AND item_op_dt BETWEEN '".$fromdate."' and '".$todate."'
     GROUP BY item_ref_id,item_warehouse_id,item_trans_ref_no,item_op_dt,item_trans_desc,cust_name
     order by item_op_dt,item_trans_order asc";
    $transactions = DB::select($sql);


    }
    
    if ($request->input('submit') == "pdf"){
       $fileName = 'item_ledger';
       $pdf = PDF::loadView('/inventory/reports/rpt_item_ledger1_pdf',
       compact('opening','transactions','comp_name','fromdate','todate','company_code',
       'item',), [], [
       'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }

    return view('/inventory/reports/rpt_item_ledger_date_group_report',
    compact('opening','transactions','companies','fromdate','todate','company_code',
    'item_id','item_list'));
  }


  public function month_wise_report(Request $request)
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
    
    $fromdate = date('Y-m-d');
    $todate  = date('Y-m-d');
   
    $item_id = 557;
    $opening = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = -0');
    $transactions = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = -0');
    
    if($request->filled('item_id') && $request->filled('fromdate') && $request->filled('todate')){
     $item_id = $request->get('item_id');
     $fromdate = date('Y-m-d',strtotime($request->fromdate));
     $todate = date('Y-m-d',strtotime($request->todate));
     $generalscontroller = new GeneralsController();
     $comp_name = $generalscontroller->CompanyName($company_code);
     $comp_add = $generalscontroller->CompanyAddress($company_code);
     $item = $generalscontroller->get_item_details_more($item_id);
    
    $sql =  "SELECT SUM(OP) as OP, SUM(GR) as GR,SUM(SA) as SA, SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR,SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA FROM view_item_ledger Where item_ref_id = $item_id AND item_op_comp_id = $company_code AND item_op_dt < '".$fromdate."'";
    
    $opening = DB::select($sql); 
    
    $sql =  "SELECT t_date, SUM(qty) as qty  (SELECT SUM(tosa_.qty) as tosa_qty FROM view_tosa_kutting as tosa_ WHERE item_id = '557') as tosa
      FROM view_tosa_kutting
      where t_date BETWEEN '".$fromdate."' and '".$todate."'
     GROUP BY t_date";

    $n =  "SELECT t_date,
          (SELECT SUM(qty) 
          FROM view_tosa_kutting
          WHERE item_id = 557 and t_date > $fromdate and title = 'OP') as tosa_opening,

          (SELECT SUM(qty) 
          FROM view_tosa_kutting
          WHERE item_id = 737 and t_date > $fromdate and title = 'OP') as kutting_opening,
          
        --  (SELECT SUM(qty) 
        --   FROM view_tosa_kutting
        --  WHERE item_id = 737) as kutting_qty,
         (SELECT SUM(qty) 
          FROM view_tosa_kutting
         WHERE title = 'GR' and item_id = 557 and t_date = t.t_date) as tosa_purchase ,
         (SELECT SUM(qty) 
          FROM view_tosa_kutting
         WHERE title = 'GR' and item_id = 737 and t_date = t.t_date) as kutting_purchase,
         (SELECT SUM(qty) 
          FROM view_tosa_kutting
          WHERE title = 'GI' and item_id = 557 and t_date = t.t_date) as tosa_issue,
          (SELECT SUM(qty) 
          FROM view_tosa_kutting
          WHERE title = 'GI' and item_id = 737 and t_date = t.t_date) as kutting_issue
      FROM view_tosa_kutting t  WHERE t_date BETWEEN '".$fromdate."' and '".$todate."' 
      GROUP BY t_date";

    // $sql = "SELECT * from view_tosa_kutting where t_date BETWEEN '".$fromdate."' and '".$todate."";

    $transactions = DB::select($n);
    
    // return $transactions;
  }
    
    if ($request->input('submit') == "pdf"){
       $fileName = 'item_ledger';
       $pdf = PDF::loadView('/inventory/reports/rpt_item_ledger1_pdf',
       compact('opening','transactions','comp_name','fromdate','todate','company_code',
       'item',), [], [
       'title' => $fileName, 
      ]);
      return $pdf->stream($fileName,'.pdf');
    }
    return view('/inventory/reports/month_wise_report',
    compact('opening','transactions','companies','fromdate','todate','company_code',
    'item_id','item_list'));
  }

 
 public function getItemStockLedgerReport1(Request $request)
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
    
    $fromdate = date('Y-m-d');
    $todate  = date('Y-m-d');
    $item_id = '';
    $opening = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = -0');
    $transactions = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = -0');
    
    if($request->filled('item_id') && $request->filled('fromdate') && $request->filled('todate')){
     $item_id = $request->get('item_id');
     $fromdate = date('Y-m-d',strtotime($request->fromdate));
     $todate = date('Y-m-d',strtotime($request->todate));
     $generalscontroller = new GeneralsController();
     $comp_name = $generalscontroller->CompanyName($company_code);
     $comp_add = $generalscontroller->CompanyAddress($company_code);
     $item = $generalscontroller->get_item_details_more($item_id);
    
    $sql =  "SELECT SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR,SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA FROM view_item_ledger Where item_ref_id = $item_id AND item_op_comp_id = $company_code AND item_op_dt < '".$fromdate."'";
    
    $opening = DB::select($sql);
    
    $sql =  "SELECT raw_materials_receives.purchaseImages, item_ref_id,item_warehouse_id,item_trans_ref_no,item_op_dt,item_trans_desc,cust_name,
     SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR, SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA  FROM view_item_ledger
     left join sales_deliveries on del_no = item_trans_ref_no
     left join raw_materials_receives on raw_order_no = item_trans_ref_no
     left join customers on customers.id = del_cust_id
     Where item_ref_id = $item_id AND item_op_comp_id = $company_code
     AND item_op_dt BETWEEN '".$fromdate."' and '".$todate."'
     GROUP BY  raw_materials_receives.purchaseImages, item_ref_id,item_warehouse_id,item_trans_ref_no,item_op_dt,item_trans_desc,cust_name
     order by item_op_dt,item_trans_order asc";
    $transactions = DB::select($sql);

    }
    
    if ($request->input('submit') == "pdf"){
       $fileName = 'item_ledger';
       $pdf = PDF::loadView('/inventory/reports/rpt_item_ledger1_pdf',
       compact('opening','transactions','comp_name','fromdate','todate','company_code',
       'item',), [], [
       'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }
    return view('/inventory/reports/rpt_item_ledger1',
    compact('opening','transactions','companies','fromdate','todate','company_code',
    'item_id','item_list'));
  }
 
  //  copy from upper
  public function getItemStockLedgerReport2(Request $request)
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
     ->where('item_ref_cate_id', '=', 103)
     ->select('items.id','item_code','item_name','item_desc','item_bar_code',
     'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin', 'item_ref_cate_id')
     ->orderBy('item_name','asc')->get();
    
    $fromdate = date('Y-m-d');
    $todate  = date('Y-m-d');
    $item_id = '';
    $opening = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = -0');
    $transactions = DB::select('SELECT * FROM view_item_ledger Where item_ref_id = -0');
    
    if($request->filled('item_id') && $request->filled('fromdate') && $request->filled('todate')){
     $item_id = $request->get('item_id');
     $fromdate = date('Y-m-d',strtotime($request->fromdate));
     $todate = date('Y-m-d',strtotime($request->todate));
     $generalscontroller = new GeneralsController();
     $comp_name = $generalscontroller->CompanyName($company_code);
     $comp_add = $generalscontroller->CompanyAddress($company_code);
     $item = $generalscontroller->get_item_details_more($item_id);
      
      $sql =  "SELECT SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR,SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA FROM view_item_ledger Where item_ref_id = $item_id AND item_op_comp_id = $company_code AND item_op_dt < '".$fromdate."'";
      
      $opening = DB::select($sql);
      
      $sql =  "SELECT item_ref_id,item_warehouse_id,item_trans_ref_no,item_op_dt,item_trans_desc,cust_name,
      SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR, SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA  FROM view_item_ledger
      left join sales_deliveries on del_no = item_trans_ref_no
      left join customers on customers.id = del_cust_id
      Where item_ref_id = $item_id AND item_op_comp_id = $company_code
      AND item_op_dt BETWEEN '".$fromdate."' and '".$todate."'
      GROUP BY item_ref_id,item_warehouse_id,item_trans_ref_no,item_op_dt,item_trans_desc,cust_name
      order by item_op_dt,item_trans_order asc";
      $transactions = DB::select($sql);

    }
    
    if ($request->input('submit') == "pdf"){
       $fileName = 'item_ledger';
       $pdf = PDF::loadView('/inventory/spReport/rpt_item_ledger1_pdf',
       compact('opening','transactions','comp_name','fromdate','todate','company_code',
       'item',), [], [
       'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }
    return view('/inventory/spReport/rpt_item_ledger1',
    compact('opening','transactions','companies','fromdate','todate','company_code',
    'item_id','item_list'));
  }
  
  public function getItemWisePurchaseReport(Request $request)
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

      $item_list =  Items::query()
        ->join("units", "unit_id", "=", "units.id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', '=', $company_code)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
        ->orderBy('item_name','asc')->get();

       $fromdate = date('Y-m-d');
       $todate  = date('Y-m-d');
       $item_id = '';
       if($request->filled('item_id') && $request->filled('fromdate')
        && $request->filled('todate')){
        $item_id = $request->get('item_id');
        $fromdate = date('Y-m-d',strtotime($request->fromdate));
        $todate = date('Y-m-d',strtotime($request->todate));
      }

      
      $q = ItemPurchases::query()
          ->join("item_purchases_details", "pur_order_id", "=", "item_purchases.id")
          ->join("warehouses", "warehouses.id", "=", "pur_warehouse_id")
          ->join("suppliers", "suppliers.id", "=", "pur_supp_id")
          ->join("items", "items.id", "=", "pur_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_purchases.pur_comp_id', '=', $company_code);
        
      if($item_id != null) $q->where('pur_item_id', '=', $item_id);

      $q->whereBetween('item_purchases.pur_order_date', [$fromdate,$todate])
          ->select('pur_order_no','pur_order_date','pur_warehouse_id','ware_name',
          'pur_storage_id','itm_cat_name','items.item_code','items.item_name','pur_lot_no',
          'pur_item_price','pur_item_qty');

      $rows = $q->orderBy('item_name','asc')->get();

      $receives = [];

      if(isset($request->item_id)){
        $fromdate = date('Y-m-d',strtotime($request->fromdate));
        $todate = date('Y-m-d',strtotime($request->todate));

        $receives = RawMaterialsReceivesDetails::with('item', 'rawmetarial')
          ->join("raw_materials_receives", "raw_materials_receives.id", "=", "raw_materials_receives_details.raw_order_id")
          ->join("suppliers", "suppliers.id", "=", "raw_materials_receives.raw_supp_id")
          ->where('raw_item_id', $request->item_id)
          ->whereBetween('raw_materials_receives.raw_order_date', [$fromdate,$todate])
          ->orderBy('raw_materials_receives.raw_order_date', 'ASC')
          ->get();
        
      }

      if ($request->input('submit') == "pdf"){
          $fileName = 'item_purchase_rpt';
          $pdf = PDF::loadView('/inventory/reports/rpt_item_wise_purchase_pdf',
          compact('rows', 'receives', 'comp_name','fromdate','todate','company_code'), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }

    // return  $receives;
    return view('/inventory/reports/rpt_item_wise_purchase',
    compact('companies','rows','comp_name','fromdate','todate','company_code','item_id','item_list',  'receives'));
  }

  public function getItemSuppWisePurchaseReport(Request $request) 
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

      $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();;

       $fromdate = date('Y-m-d');
       $todate  = date('Y-m-d');
       $supp_id = ''; 
       $cat_id = ''; 
      
       if($request->filled('supp_id')) {
        $supp_id = $request->get('supp_id');
       }

       if($request->filled('cat_id')) {
        $cat_id = $request->get('cat_id');
       }

       if ($request->filled('fromdate') && $request->filled('todate')){
        $fromdate = date('Y-m-d',strtotime($request->fromdate));
        $todate = date('Y-m-d',strtotime($request->todate));
       }

       $receives = [];
      //  if(isset($request->supp_id)){
   
      //   $receives = RawMaterialsReceives::join("suppliers", "raw_supp_id", "=", "suppliers.id")
      //   ->join("raw_materials_receives_details", "raw_materials_receives.id", "=", "raw_materials_receives_details.raw_order_id")
      //   ->join("items", "items.id", "=", "raw_materials_receives_details.raw_item_id")
      //   ->whereBetween('raw_materials_receives.raw_order_date', [$fromdate,$todate])
      //  //  ->where('raw_supp_id', $request->supp_id)
      //   ->get();
  
      // }

      //  category
      $categories = ItemCategories::where('id', $request->cat_id)->get();
      $catId = [];
      foreach ($categories as $key => $value) {
          $catId[] = $value->id;
          
          $subCat1 = ItemCategories::where('parent_id', $value->id)->get();
          foreach ($subCat1 as $key => $value1) {
              $catId[] = $value1->id;

              $subCat2 = ItemCategories::where('parent_id', $value1->id)->get();
              foreach ($subCat2 as $key => $value2) {
                  $catId[] = $value2->id;                  
              }
          }
      }

      

       $receives = RawMaterialsReceives::join("suppliers", "raw_supp_id", "=", "suppliers.id")
       ->join("raw_materials_receives_details", "raw_materials_receives.id", "=", "raw_materials_receives_details.raw_order_id")
       ->join("items", "items.id", "=", "raw_materials_receives_details.raw_item_id")
       ->join("item_categories", "item_categories.id", "=", "items.item_ref_cate_id")
       ->whereBetween('raw_materials_receives.raw_order_date', [$fromdate,$todate]);

      //  return $request->cat_id;
       if(isset($request->cat_id)){
        $receives->whereIn('items.item_ref_cate_id', $catId);
       }

       if(isset($request->supp_id)){
        $receives->where('raw_supp_id', $request->supp_id);
       }
       $receives->orderBy('raw_materials_receives.raw_order_date', 'ASC');
       $receives = $receives->get();


      //  return $receives;
      $q =  ItemPurchases::query()
          ->join("item_purchases_details", "pur_order_id", "=", "item_purchases.id")
          ->join("suppliers", "pur_supp_id", "=", "suppliers.id")
          ->join("warehouses", "warehouses.id", "=", "pur_warehouse_id")
          ->join("items", "items.id", "=", "pur_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_purchases.pur_comp_id', '=', $company_code);
      if($supp_id != '') $q->where('pur_supp_id', '=', $supp_id);
      $q->whereBetween('item_purchases.pur_order_date', [$fromdate,$todate])
          ->select('pur_order_no','pur_order_date','pur_warehouse_id','ware_name',
          'pur_storage_id','itm_cat_name','items.item_code','items.item_name','pur_lot_no',
          'pur_item_price','pur_item_qty','supp_name');

      $rows = $q->orderBy('pur_order_date','desc')->get();

      if ($request->input('submit') == "pdf"){
          $fileName = 'item_purchase_rpt';
          $pdf = PDF::loadView('/inventory/reports/rpt_item_supp_wise_purchase_pdf',
          compact('rows','comp_name','fromdate','todate','company_code', 'receives'), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }

    $categories = ItemCategories::where('itm_cat_level', 2)->get(); 

    return view('/inventory/reports/rpt_item_supp_wise_purchase',
    compact('companies','categories', 'rows','comp_name','fromdate','todate','company_code','supp_id', 'cat_id', 'suppliers', 'receives'));
  }

  public function getDateWiseDamagesReport(Request $request)
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

      $item_list =  Items::query()
        ->join("units", "unit_id", "=", "units.id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', '=', $company_code)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
        ->orderBy('item_name','asc')->get();

       $fromdate = date('Y-m-d');
       $todate  = date('Y-m-d');
       $item_id = '';
       if($request->filled('item_id')) {
        $item_id = $request->get('item_id');
       }
       if ($request->filled('fromdate') && $request->filled('todate')){
        $fromdate = date('Y-m-d',strtotime($request->fromdate));
        $todate = date('Y-m-d',strtotime($request->todate));
       }
      $q =ItemDamages::query()
          ->join("item_damages_details", "item_damages.id", "=", "dam_ref_id")
          ->join("items", "items.id", "=", "dam_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_damages.dam_comp_id', '=', $company_code);

      if($item_id != '') $q->where('dam_item_id', '=', $item_id);

      $q->whereBetween('item_damages.dam_date', [$fromdate,$todate])
         ->select('item_damages.id','item_damages.dam_comp_id','dam_title','dam_date',
          'dam_comments','dam_total_qty','dam_total_amount', 'item_code','item_name',
          'itm_cat_name','dam_lot_no','dam_item_qty','dam_item_remarks',
          'dam_item_price');

      $rows = $q->orderBy('dam_date','asc')->get();

      if ($request->input('submit') == "pdf"){
          $fileName = 'item_damages_rpt';
          $pdf = PDF::loadView('/inventory/reports/rpt_date_wise_damages_pdf',
          compact('rows','comp_name','fromdate','todate','company_code',), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }
    return view('/inventory/reports/rpt_date_wise_damages',
    compact('companies','rows','comp_name','fromdate','todate','company_code','item_id','item_list'));
  }

  public function getDateWiseShortagesReport(Request $request)
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

      $item_list =  Items::query()
        ->join("units", "unit_id", "=", "units.id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', '=', $company_code)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
        ->orderBy('item_name','asc')->get();

       $fromdate = date('Y-m-d');
       $todate  = date('Y-m-d');
       $item_id = '';
    
      if($request->filled('item_id')) {
        $item_id = $request->get('item_id');
       }
       if ($request->filled('fromdate') && $request->filled('todate')){
        $fromdate = date('Y-m-d',strtotime($request->fromdate));
        $todate = date('Y-m-d',strtotime($request->todate));
       }
       
      $q =ItemShortages::query()
          ->join("item_shortages_details", "item_shortages.id", "=", "short_ref_id")
          ->join("items", "items.id", "=", "short_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_shortages.short_comp_id', '=', $company_code);

      if($item_id != '') $q->where('short_item_id', '=', $item_id);

      $q->whereBetween('item_shortages.short_date', [$fromdate,$todate])
         ->select('item_shortages.id','item_shortages.short_comp_id','short_title','short_date',
          'short_comments','short_total_qty','short_total_amount', 'item_code','item_name',
          'itm_cat_name','short_lot_no','short_item_qty','short_item_remarks','short_item_price');

      $rows = $q->orderBy('short_date','asc')->get();

      if ($request->input('submit') == "pdf"){
          $fileName = 'item_shortages_rpt';
          $pdf = PDF::loadView('/inventory/reports/rpt_date_wise_shortages_pdf',
          compact('rows','comp_name','fromdate','todate','company_code',), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }
    return view('/inventory/reports/rpt_date_wise_shortages',
    compact('companies','rows','comp_name','fromdate','todate','company_code','item_id','item_list'));
  }

  public function getDateWiseExpiredReport(Request $request)
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

      $item_list =  Items::query()
        ->join("units", "unit_id", "=", "units.id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', '=', $company_code)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
        ->orderBy('item_name','asc')->get();

       $fromdate = date('Y-m-d');
       $todate  = date('Y-m-d');
       $item_id = '';
    
       if($request->filled('item_id')) {
        $item_id = $request->get('item_id');
       }
       if ($request->filled('fromdate') && $request->filled('todate')){
        $fromdate = date('Y-m-d',strtotime($request->fromdate));
        $todate = date('Y-m-d',strtotime($request->todate));
       }
       
      $q =ItemExpires::query()
          ->join("item_expires_details", "item_expires.id", "=", "exp_ref_id")
          ->join("items", "items.id", "=", "exp_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_expires.exp_comp_id', '=', $company_code);

      if($item_id != '') $q->where('exp_item_id', '=', $item_id);

      $q->whereBetween('item_expires.exp_date', [$fromdate,$todate])
         ->select('item_expires.id','item_expires.exp_comp_id','exp_title','exp_date',
          'exp_comments','exp_total_qty','exp_total_amount', 'item_code','item_name',
          'itm_cat_name','exp_lot_no','exp_item_qty','exp_item_remarks',
          'exp_item_price');

      $rows = $q->orderBy('exp_date','asc')->get();

      if ($request->input('submit') == "pdf"){
          $fileName = 'item_damages_rpt';
          $pdf = PDF::loadView('/inventory/reports/rpt_date_wise_expired_pdf',
          compact('rows','comp_name','fromdate','todate','company_code',), [], [
          'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }
    return view('/inventory/reports/rpt_date_wise_expired',
    compact('companies','rows','comp_name','fromdate','todate','company_code','item_id','item_list'));
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

     if($request->filled('company_code')
      && $request->filled('fromdate') && $request->filled('todate')){
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $ledger_id  = $request->input('ledger_id');
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
        // $ledgername = $generalscontroller->accountNameLookup($ledger_id);
        $cust_data = $generalscontroller->getCustomerInfByLedgerId($ledger_id);
      }

      //ledger opening balance
      $opening = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->selectRaw('sum(d_amount) as debit, sum(c_amount) as credit')
      ->where('com_ref_id', $default_comp_code )
      ->where('chart_of_acc_id', $ledger_id )
      ->whereDate('voucher_date','<' ,date('Y-m-d', strtotime($fromdate)))->first();


      $sql = "SELECT `cust_name` as party,
       `trans_type` as trans_type, `voucher_date` as voucher_date, 
      `t_narration` as t_narration, c_amount as c_amount, d_amount as d_amount 
      FROM `acc_transactions` JOIN acc_transaction_details ON 
      acc_transaction_details.acc_trans_id = acc_transactions.id
      JOIN customers ON customers.cust_chartofacc_id = acc_transaction_details.chart_of_acc_id  
      where voucher_date BETWEEN '".$fromdate."' and '".$todate."'
      union all SELECT `cust_name` as party, so_order_title as trans_type, so_order_date as voucher_date, 
      so_reference as t_narration, so_net_amt as c_amount, so_gross_amt as d_amount
      FROM sales_orders JOIN customers ON customers.id = sales_orders.so_cust_id 
      where so_order_date BETWEEN '".$fromdate."' and '".$todate."' 
      and sales_orders.deleted_at IS null
      ORDER BY voucher_date";

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
     return view('/inventory/reports/rpt_daily_order_statement',
     compact('rows','opening','companies','default_comp_code',
     'customers','ledger_id','fromdate', 'todate'));

   }

}
