<?php

namespace App\Http\Controllers\General;

use App\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Companies;
use App\Models\Employees\Employees;
use App\Models\Leave\EmpLeaveTypes;
use App\Models\Chartofaccounts;
use App\Models\CompaniesAssigns;
use App\Models\AccTransDocTypes;
use App\Models\Districts;
use App\Models\Items\Items;
use App\Models\Items\ItemStocks;
use App\Models\Customers\CustomerDeliveryInfs;
use App\Models\Sales\SalesOrdersDetails;
use App\Models\Loans\SalesLoansDetails;
use App\Models\Sales\SalesDeliveries;
use App\Models\Sales\SalesCourrierInfs;
use App\Models\Warehouse\WarehouseAssigns;
use App\Models\Warehouse\StorageLocations;
use App\Models\Items\Packings;
use App\Models\Items\Units;
use App\Models\Settings\SysInfos;
use App\Models\Rawmaterials\RawMaterialsReceivesDetails;

use DB;

class DropdownsController extends Controller
{

  public function comboDistrictsList()
  {
    $dist_list = Districts::query()
              ->select('id', 'vCityName')
              ->orderBy('vCityName','asc')->get();
    return $dist_list;
  }

  public function comboCompanyList($id)
  {
    $companies = companies::query()
              ->where('is_deleted','=','0')
              ->select('id', 'name')->get();
    return $companies;
  }

  public function comboDefaultCompanyList($company_code)
  {
    $companies = companies::query()
              ->where('id','=',$company_code)
              ->where('is_deleted','=','0')
              ->select('id', 'name')->get();
    return $companies;
  }

  public function comboCompanyAssignList()
  {
    $companies = CompaniesAssigns::query()
              ->Join("companies", "companies_assigns.comp_id", "=", "companies.id")
              ->where('user_id',auth()->user()->id)
              ->where('companies_assigns.is_deleted','=','0')
              ->where('companies.is_deleted','=','0')
              ->select('companies_assigns.comp_id', 'name')->get();
    return $companies;
  }

  public function defaultCompanyCode()
  {
      $comp_id = CompaniesAssigns::query()
                   ->selectRaw('comp_id')
                   ->where('is_deleted', 0)
                   ->where('user_id',auth()->user()->id)
                   ->where('default',1)->first()->comp_id;
     return $comp_id;
  }

  public function comboUsersList($id)
  {
    $companies = User::query()
              ->where('is_deleted','0')
              ->select('id', 'name')->get();
    return $companies;
  }

  public function comboAccDocTypeList($type, $compid)
  {
    $accdoctype = AccTransDocTypes::query()
              ->where('trans_type_no', $type)
              ->where('doc_comp_id', $compid)
              ->select('doc_type', 'doc_type')->get();
    return $accdoctype;
  }

  public function comboAccDocTypeList1($type, $compid)
  {
    $accdoctype = AccTransDocTypes::query()
              ->wherein('trans_type_no', $type)
              ->where('doc_comp_id', $compid)
              ->select('trans_type_no', 'doc_type')->get();
    return $accdoctype;
  }
  
  public function comboAccHeadCateList($type, $compid)
  {
    $myfield = AccTransDocTypes::where('trans_type_no', $type)
              ->where('doc_comp_id', $compid)
              ->selectRaw('trans_acc_head as acc_id')->first();
    return $myfield;
  }

  public function comboAcc_DocTypeList($compid)
  {
    $acctrans_DocTypes = AccTransDocTypes::query()
              ->where('doc_comp_id','=',$compid)
              ->select('doc_type','trans_type','trans_type_no')->get();
    return $acctrans_DocTypes;
  }


  public function comboUnitsList($compid)
  {
    $unit_list = units::query()
        ->where('unit_comp_id','=',$compid)
        ->select('id','vUnitName')
        ->orderBy('vUnitName','asc')->get();
    return $unit_list;
  }


  public function comboPackingsList($compid)
  {
    $packing_list = packings::query()
        ->where('pack_comp_id','=',$compid)
        ->select('id','vPackingName')
        ->orderBy('vPackingName','asc')->get();
    return $packing_list;
  }

  public function comboItemCodeList($custsid,$compid)
  {
    $myfield = Items::where('item_ref_comp_id', $compid)
            ->join("customer_prices", "cust_item_p_id", "=", "items.id")
            ->where('cust_p_ref_id', $custsid)
            ->where('p_del_flag', 0)
            ->where('item_ref_comp_id', $compid)
            ->selectRaw('items.id,item_code,item_name,item_bar_code')->get();
    return $myfield;
  }

  public function comboItemCodeList1($compid,$itemid)
  {
    $myfield = Items::where('item_ref_comp_id', $compid)
            ->where('item_ref_comp_id', $compid)
            ->where('item_code', 'LIKE', $itemid.'%')
            ->selectRaw('items.id,item_code,item_name,item_bar_code')->get();
    return $myfield;
  }

  public function categoryLookup($compid)
  {
    $sql = "select * from `item_categories`
    where `itm_comp_id` = $compid and itm_cat_code like '20%' and `id` not in (select `parent_id` from `item_categories`)
    Order By itm_cat_name asc";
    $itm_cat = DB::select($sql); 
    $itm_cat = DB::table('item_categories')->where('itm_comp_id', $compid)
      ->whereIn('itm_cat_code', ['202', '201'])->get();

    return response()->json($itm_cat); 
  }

  public function rawMetarilasCategory($compid)
  {
    $sql = "select * from `item_categories`
    where `itm_comp_id` = $compid and itm_cat_code like '20%' and `id` not in (select `parent_id` from `item_categories`)
    Order By itm_cat_name asc";
    $itm_cat = DB::select($sql); 
    $itm_cat = DB::table('item_categories')->where('itm_comp_id', $compid)
      ->whereIn('itm_cat_code', ['101', '102'])->get();

    return response()->json($itm_cat); 
  }

  public function catitemLookup($compid,$catid)
  {
    $cat = DB::table('item_categories')->where('parent_id', $catid)->get();
    
    $catIds = [];
    foreach ($cat as $key => $value) {
      $catIds[] = $value->id;
    }

    $itms = Items::query() 
    ->where('item_ref_cate_id', $catid) 
    ->get();

    // $itms = Items::query() 
    // ->join("units", "unit_id", "=", "units.id") 
    // ->where('item_ref_cate_id', '=', $catid) 
    // ->where('item_ref_comp_id','=',$compid)
    // ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    // 'item_op_stock','item_bal_stock','vUnitName','base_price as cust_price')->get();

    return response()->json($itms); 
  }

  public function getChildCat($catid)
  {
    $cat = DB::table('item_categories')->where('parent_id', $catid)->get();
   
    // $itms = Items::query() 
    // ->join("units", "unit_id", "=", "units.id") 
    // ->where('item_ref_cate_id', '=', $catid) 
    // ->where('item_ref_comp_id','=',$compid)
    // ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    // 'item_op_stock','item_bal_stock','vUnitName','base_price as cust_price')->get();

    return response()->json($cat); 
  }
  
  public function itemLookup($compid,$custid)
  {
    $itms = Items::query()
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->join("units", "unit_id", "=", "units.id")
    ->join("customer_prices", "cust_item_p_id", "=", "items.id")
    ->where('cust_p_ref_id','=',$custid)
    ->where('p_del_flag','=',0)
    ->where('item_ref_comp_id','=',$compid)
    ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    'item_op_stock','item_bal_stock','vUnitName','cust_price','itm_cat_name')->get();
    return response()->json($itms);
    //return $branches;
  }

  public function itemOrderLookup($soid)
  {
    $itms = SalesOrdersDetails::query()
    ->join("items", "items.id", "=", "so_item_id")
    ->join("units", "unit_id", "=", "units.id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->where('so_order_id','=',$soid)
    ->where('so_order_bal_qty','>',0)
    ->select("so_item_id as id",'itm_cat_name','item_code','item_name','item_desc',
    'vUnitName','so_item_price','so_order_disc','so_order_qty','so_order_con_qty',
    'so_order_bal_qty','item_bal_stock')->get();
    return $itms;
    //return $branches;
  }

  public function itemLoanOrderLookup($soid)
  {
    $itms = SalesLoansDetails::query()
    ->join("items", "items.id", "=", "loan_i_item_id")
    ->join("units", "unit_id", "=", "units.id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->where('loan_i_order_id','=',$soid)
    ->whereRaw('loan_i_bal_qty < loan_i_qty')
    ->select("loan_i_item_id as id",'itm_cat_name','item_code','item_name','item_desc',
    'vUnitName','loan_i_item_price','loan_i_lot_no','loan_i_qty','loan_i_bal_qty',
    'item_bal_stock')->get();
    return $itms;
    //return $branches;
  }
  
  public function comboItemOrderCodeList($soid)
  {
    $myfield = SalesOrdersDetails::where('so_order_id', $soid)
            ->join("items", "items.id", "=", "so_item_id")
            ->selectRaw('items.id,item_code,item_name,item_bar_code')->get();
    return $myfield;
  }

  public function itemLookup1($compid)
  {
    $itms = Items::query()
    ->join("units", "unit_id", "=", "units.id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->where('item_ref_comp_id','=',$compid)
    ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')->get();
    return response()->json($itms);
    //return $branches;
  }

  public function rawitemLookup($compid)
  {
    $itms = Items::query()
    ->join("units", "unit_id", "=", "units.id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->where('item_ref_comp_id','=',$compid)
    // ->whereRaw("itm_cat_code like '10%'")
    ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')->get();
    return response()->json($itms);
    //return $branches;
  }

 public function rawitemissueLookup($compid)
  {
    $itms = Items::query()
    ->join("units", "unit_id", "=", "units.id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->where('item_ref_comp_id','=',$compid)
    ->whereRaw("itm_cat_code not like '20%'")
    ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')->get();
    return response()->json($itms);
    //return $branches;
  }
  

  public function consitemLookup($compid)
  {
    $itms = Items::query()
    ->join("units", "unit_id", "=", "units.id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->where('item_ref_comp_id','=',$compid)
    ->whereRaw("itm_cat_code like '30%' or itm_cat_code like '40%' or itm_cat_code like '40%'")
    ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')->get();
    // $itms = Items::query()
    // ->join("raw_materials_receives_details", "raw_materials_receives_details.raw_item_id", "=", "items.id")
    // ->where('item_ref_comp_id','=',$compid)
    // ->join("units", "unit_id", "=", "units.id")
    // ->get();



    return response()->json($itms);
    //return $branches;
  }
  
  public function finishgoodsLookup($compid)
  {
    $itms = Items::query()
    ->join("units", "unit_id", "=", "units.id")
    ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    ->where('item_ref_comp_id','=',$compid)
    ->whereRaw("itm_cat_code like '20%'")
    ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')->get();
    return response()->json($itms);
    //return $branches;
  }

  public function rawUnitLookup($compid)
  {
    $itms = Units::query() 
    ->where('unit_comp_id','=',$compid) 
    ->select('units.id','vUnitName')->get();
    return response()->json($itms);
    //return $branches;
  }

  public function deliveredToLookup($custid)
  {
    $list = CustomerDeliveryInfs::query()
    ->where('cust_d_ref_id','=',$custid)
    ->select('customer_delivery_infs.id','cust_d_ref_id','deliv_to',
    'deliv_add','deliv_mobile','deliv_dist_id')
    ->orderBy('deliv_to','asc')->get();
    return response()->json($list);
    //return $branches;
  }

  public function accountNameLookup($headid)
  {
    $list = Chartofaccounts::query()
    ->where('parent_id','=',$headid)
    ->select('id','acc_code','acc_head')
    ->orderBy('acc_head','asc')->get();
    return $list;
  }


  public function accountNameLookup1($headid)
  { 
    $sql = "SELECT * FROM 
    (select  id,
            acc_head,
            parent_id,acc_origin,acc_level,@pv
    from    (select * from chartofaccounts
             order by parent_id, id) chartofaccounts,
            (select @pv := '$headid') initialisation
    where   find_in_set(parent_id, @pv) > 0
    and     @pv := concat(@pv, ',', id) ) MAIN Where id not in (select parent_id 
    from    (select * from chartofaccounts
             order by parent_id, id) chartofaccounts,
            (select @pv := '$headid') initialisation
    where   find_in_set(parent_id, @pv) > 0
    and     @pv := concat(@pv, ',', id) )";
    $list = DB::select($sql); 
    return $list;
  }


  public function childItemCateNameLookup($cateid)
  { 
    $sql = "SELECT * FROM 
    (select  id,
            itm_cat_name,
            parent_id,itm_cat_origin,@pv
    from    (select * from item_categories
             order by parent_id, id) item_categories,
            (select @pv := '$cateid') initialisation
    where   find_in_set(parent_id, @pv) > 0
    and     @pv := concat(@pv, ',', id) ) MAIN Where id not in (select parent_id 
    from    (select * from item_categories
             order by parent_id, id) item_categories,
            (select @pv := '$cateid') initialisation
    where   find_in_set(parent_id, @pv) > 0
    and     @pv := concat(@pv, ',', id) )";
    $list = DB::select($sql); 
    return $list;
  }
  
  public function deliveryList($compid)
  {
    $del_list = SalesDeliveries::query()
    ->join("customers", "del_cust_id", "=", "customers.id")
    ->join("sales_orders", "sales_orders.id", "=", "del_sal_ord_id")
    ->where('so_comp_id','=',$compid)
    ->selectRaw('sales_deliveries.id,del_no,del_date,so_order_no,so_order_date,so_reference,cust_name')
    ->orderBy('sales_deliveries.id','desc')->get();
    return $del_list;
    //return $branches;
  }

  public function comboCourrierList($compid)
  {
    $myfield = SalesCourrierInfs::where('cour_com_id', $compid)
            ->selectRaw('id,courrier_to,courrier_add,courrier_mobile')->get();
    return $myfield;
  }
  
  public function courrierToLookup($compid)
  {
    $list = SalesCourrierInfs::where('cour_com_id', $compid)
    ->selectRaw('id,courrier_to,courrier_add,courrier_mobile')
    ->orderBy('courrier_to','asc')->get();
    return response()->json($list);
  }

  public function defaultWareHouseCode($compid)
  {
      $warehouse_id = WarehouseAssigns::query()
        ->selectRaw('w_ref_id')
        ->where('w_a_comp_id',$compid)
        ->where('w_user_id',auth()->user()->id)
        ->where('default',1)->first()->w_ref_id;
     return $warehouse_id;
  }

  public function WareHouseList($compid)
  {
      $warehouse_list = WarehouseAssigns::query()
        ->join("warehouses", "w_ref_id", "=", "warehouses.id")
        ->selectRaw('w_ref_id,ware_code,ware_name,ware_desc')
        ->where('w_a_comp_id',$compid)
        ->where('w_user_id',auth()->user()->id)
        ->get();
     return $warehouse_list;
  }

  public function warehouseLookup($compid)
  {
      $warehouse_list = WarehouseAssigns::query()
        ->join("warehouses", "w_ref_id", "=", "warehouses.id")
        ->selectRaw('w_ref_id,ware_code,ware_name,ware_desc')
        ->where('w_a_comp_id',$compid)
        ->where('w_user_id',auth()->user()->id)
        ->get();
     return $warehouse_list;
  }
  
  public function warehouseLookup1($compid)
  {
      $warehouse_list = WarehouseAssigns::query()
        ->join("warehouses", "w_ref_id", "=", "warehouses.id")
        ->selectRaw('w_ref_id,ware_code,ware_name,ware_desc')
        ->where('w_a_comp_id',$compid)
        ->where('w_user_id',auth()->user()->id)
        ->get(); 
     return response()->json($warehouse_list);
  }

  public function comboStorageList($compid,$wid)
  {
    $myfield = StorageLocations::where('stor_comp_id', $compid)
            ->where('stor_warehouse_id', $wid)
            ->selectRaw('id,stor_code,stor_name')->get();
    return $myfield;
  }

  public function storageLookup($compid,$wid)
  {
    $myfield = StorageLocations::where('stor_comp_id', $compid)
            ->where('stor_warehouse_id', $wid)
            ->selectRaw('id,stor_code,stor_name')->get();
    return response()->json($myfield);
  }

  public function LotLookup($itemid,$storage_id)
  {
    $itms = ItemStocks::query()
    ->where('item_ref_id','=',$itemid)
    ->where('item_storage_loc','=',$storage_id)
    ->selectRaw('item_lot_no, SUM(item_op_stock) as stock')
    ->groupBy('item_lot_no')
    ->having('stock', '>', 0)
    ->get();
    return response()->json($itms);
  }
  
  public function comboToLookup($company_code,$type)
  {
    $list = SysInfos::query()
      ->where('vComboType','=', $type)
      ->where('combo_company_id',$company_code)
      ->orderBy('vComboName','asc')->get(); 
    return response()->json($list);
    //return $branches;
  }
  
  public function employeeToLookup($company_code)
  {
    $list = Employees::query()
      ->where('emp_com_id', $company_code)
      ->where('emp_status', 1)
      ->orderBy('emp_name','asc')->get(); 
    return response()->json($list); 
  }

  public function leaveTypeToLookup($company_code)
  {
    $list = EmpLeaveTypes::query()
      ->where('ltype_comp_id', $company_code)  
      ->orderBy('leave_desc','asc')->get(); 
    return response()->json($list); 
  }


}
