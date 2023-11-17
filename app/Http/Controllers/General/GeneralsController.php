<?php

namespace App\Http\Controllers\General;

use App\User;
use Carbon\Carbon;

use App\Http\Resources\ItemCodeResource;
use App\Http\Resources\TransItemCodeResource;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Role;

use App\Models\AccTransDocTypes;
use App\Models\Companies;
use App\Models\Chartofaccounts;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use App\Models\FinancialYearDeclaration;
use App\Models\Customers\Customers;
use App\Models\Suppliers\Suppliers;
use App\Models\Items\Items;
use App\Models\Items\ItemStocks;
use App\Models\Customers\CustomerDeliveryInfs;
use App\Models\Salespersons\CustomerSalesPersons;
use App\Models\Sales\SalesOrders;
use App\Models\Sales\SalesDeliveries;
use App\Models\Sales\SalesInvoices;
use App\Models\Sales\SalesQuotations;
use App\Models\Sales\SalesReturns;

use App\Models\Loans\SalesLoans;

use App\Models\Inventory\ItemPurchases;
use App\Models\Purchases\PurchaseOrders;

use App\Models\Rawmaterials\ConsumeMaterials;
use App\Models\Rawmaterials\ConsumeMaterialsDetails;

use App\Models\Rawmaterials\RawMaterialsReceives;
use App\Models\Rawmaterials\RawMaterialsReceivesDetails;

use App\Models\Rawmaterials\RawMaterialsIssues;
use App\Models\Rawmaterials\RawMaterialsIssuesDetails;

use App\Models\FinishGoods\FinishGoodsReceives;
use App\Models\FinishGoods\FinishGoodsReceivesDetails;

use App\Models\Employees\Employees;
use App\Models\Leave\EmpLeaveTypes; 
use App\Models\Leave\EmpLeaves; 

use App\Models\Warehouse\StorageLocations;
use App\Models\Warehouse\Warehouses;
use App\Models\UsersMappingSps;
use App\Models\view_item_avg_price;

use App\Models\Settings\SysInfos;

use DB;

class GeneralsController extends Controller 
{

  public function getMaxAccVoucherNo($tag, $comp_id,$finan_yearId)
  {
    $getMaxNo = AccTransactions::query()
              ->where('com_ref_id',$comp_id)
              ->where('trans_type',$tag)
              ->where('fin_ref_id',$finan_yearId)
              ->selectRaw('IFNULL(MAX(voucher_no),0) as MaxVoucherNo')->first()->MaxVoucherNo;
    return $getMaxNo;
  }

  public function getCustomerOutstanding($compid, $ledgerid)
  {
    $getBalance = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->where('com_ref_id',$compid)
      ->where('chart_of_acc_id', $ledgerid) 
      ->selectRaw('IFNULL(sum(d_amount),0) - IFNULL(sum(c_amount),0) as outstanding')->first();
      return $getBalance;
  }
  
  public function getCustomerCourrier($compid, $custid)
  {

    $getCourrierID = Customers::query()
      ->where('cust_com_id',$compid)
      ->where('id', $custid)
      ->selectRaw('cust_courrier_id')->first();
      return $getCourrierID;
  }
  
  public function getCustomerVAT($compid, $custid)
  {
    $getVAT = Customers::query()
      ->where('cust_com_id',$compid)
      ->where('id', $custid)
      ->selectRaw('cust_VAT,cust_own_comm,cust_overall_comm')->first();
      return $getVAT;
  }

  public function getDeliveryIdByOrderNo($sales_order_id)
  {
    $getId = SalesDeliveries::query()
           ->where('del_sal_ord_id',$sales_order_id)
           ->selectRaw('id')->first()->id;
    return $getId;
  }

  public function getInvoiceIdByOrderNo($sales_order_id)
  {
    $getId = SalesInvoices::query()
           ->where('inv_sale_ord_id',$sales_order_id)
           ->selectRaw('id')->first()->id;
    return $getId;
  }

  public function getVoucherNoByOrderNo($sales_order_id)
  {
    $getId = SalesInvoices::query()
           ->where('inv_sale_ord_id',$sales_order_id)
           ->selectRaw('inv_no')->first()->inv_no;
    return $getId;
  }
  
  public function getFinanYearByOrderNo($sales_order_id)
  {
    $getId = SalesOrders::query()
           ->where('id',$sales_order_id)
           ->selectRaw('so_fin_year_id')->first()->so_fin_year_id;
    return $getId;
  }
  
  public function getRetVoucherNoByRetOrderNo($ret_order_id)
  {
    $getret_no = SalesReturns::query()
           ->where('id',$ret_order_id)
           ->selectRaw('ret_no')->first()->ret_no;
    return $getret_no;
  }

  public function getVoucherNoByRawOrderNo($raw_order_id)
  {
    $getraw_no = RawMaterialsReceives::query()
           ->where('id',$raw_order_id)
           ->selectRaw('raw_order_no')->first()->raw_order_no;
    return $getraw_no;
  }
  
  public function getAccVoucherNoByAccTransId($acc_trans_id)
  {
   $voucher_no = 0;
    $getId = AccTransactions::query()
           ->where('id',$acc_trans_id)
           ->selectRaw('IFNULL(voucher_no,0) as voucher_no')
           ->get();

    foreach ($getId as $key => $value) {
          $voucher_no =  $value->voucher_no;
    }
    return $voucher_no;
  }

  public function getIdByVoucherNo($voucher_no,$fin_year_id)
  {
    /*$getId = AccTransactionDetails::query()
           ->where('acc_invoice_no',$voucher_no)
           ->selectRaw('acc_trans_id')->first()->acc_trans_id;
    return $getId;*/
    
    $acc_trans_id = 0;
    $getId = AccTransactions::query()
           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
           ->where('acc_invoice_no',$voucher_no)
           ->where('fin_ref_id',$fin_year_id)
           ->where('trans_type','SV')
           ->selectRaw('IFNULL(acc_trans_id,0) as acc_trans_id')->get();
    
    foreach ($getId as $key => $value) {
         $acc_trans_id =  $value->acc_trans_id;
    }
     
    return $acc_trans_id;
    
  }

  public function getRawIdByVoucherNo($voucher_no,$fin_year_id)
  {
    $acc_trans_id = 0;
    $getId = AccTransactions::query()
           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
           ->where('acc_invoice_no',$voucher_no)
           ->where('fin_ref_id',$fin_year_id)
           ->where('trans_type','GR')
           ->selectRaw('IFNULL(acc_trans_id,0) as acc_trans_id')->get();
    
    foreach ($getId as $key => $value) {
         $acc_trans_id =  $value->acc_trans_id;
    }
     
    return $acc_trans_id;
    
  }

  public function getRawIssueIdByVoucherNo($voucher_no,$fin_year_id)
  {
    $acc_trans_id = 0;
    $getId = AccTransactions::query()
           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
           ->where('acc_invoice_no',$voucher_no)
           ->where('fin_ref_id',$fin_year_id)
           ->where('trans_type','GI')
           ->selectRaw('IFNULL(acc_trans_id,0) as acc_trans_id')->get();
    
    foreach ($getId as $key => $value) {
         $acc_trans_id =  $value->acc_trans_id;
    }
     
    return $acc_trans_id; 
  }
  
  
  public function getItemConsumeIdByVoucherNo($voucher_no,$fin_year_id)
  {
    $acc_trans_id = 0;
    $getId = AccTransactions::query()
           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
           ->where('acc_invoice_no',$voucher_no)
           ->where('fin_ref_id',$fin_year_id)
           ->where('trans_type','CI')
           ->selectRaw('IFNULL(acc_trans_id,0) as acc_trans_id')->get();
    
    foreach ($getId as $key => $value) {
         $acc_trans_id =  $value->acc_trans_id;
    }
     
    return $acc_trans_id;
  }
  
//   public function getRawFinishRecByVoucherNo($voucher_no,$fin_year_id)
//   {
//     $acc_trans_id = 0;
//     $getId = AccTransactions::query()
//           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
//           ->where('acc_invoice_no',$voucher_no)
//           ->where('fin_ref_id',$fin_year_id)
//           ->where('trans_type','FR')
//           ->selectRaw('IFNULL(acc_trans_id,0) as acc_trans_id')->get();
    
//     foreach ($getId as $key => $value) {
//          $acc_trans_id =  $value->acc_trans_id;
//     }
     
//     return $acc_trans_id; 
//   }
  

  public function getFinGoodsRecIdByVoucherNo($voucher_no,$fin_year_id)
  {
    $acc_trans_id = 0;
    $getId = AccTransactions::query()
           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
           ->where('acc_invoice_no',$voucher_no)
           ->where('fin_ref_id',$fin_year_id)
           ->where('trans_type','FR')
           ->selectRaw('IFNULL(acc_trans_id,0) as acc_trans_id')->get();
    
    foreach ($getId as $key => $value) {
         $acc_trans_id =  $value->acc_trans_id;
    }
     
    return $acc_trans_id;
    
  }
    
 public function getVoucherNoByConsumeItemOrderNo($issue_order_id)
  {
    $getraw_no = ConsumeMaterials::query()
           ->where('id',$issue_order_id)
           ->selectRaw('r_cons_order_no')->first()->r_cons_order_no;
    return $getraw_no;
  }


  public function getConsumeItemFinanYearByOrderNo($raw_order_id)
  {
    $getId = ConsumeMaterials::query()
           ->where('id',$raw_order_id)
           ->selectRaw('r_cons_fin_year_id')->first()->r_cons_fin_year_id;
    return $getId;
  }
  
  public function getVoucherNoByRawRecOrderNo($rec_order_id)
  {
    $getrec_no = RawMaterialsReceives::query()
           ->where('id',$rec_order_id)
           ->selectRaw('raw_order_no')->first()->raw_order_no;
    return $getrec_no;
  }

    
  public function getVoucherNoByRawIssueOrderNo($issue_order_id)
  {
    $getraw_no = RawMaterialsIssues::query()
           ->where('id',$issue_order_id)
           ->selectRaw('r_issue_order_no')->first()->r_issue_order_no;
    return $getraw_no;
  }
  
  public function getRawIssueFinanYearByOrderNo($raw_order_id)
  {
    $getId = RawMaterialsIssues::query()
           ->where('id',$raw_order_id)
           ->selectRaw('r_issue_fin_year_id')->first()->r_issue_fin_year_id;
    return $getId;
  }

  public function getRawFinanYearByOrderNo($raw_order_id)
  {
    $getId = RawMaterialsReceives::query()
           ->where('id',$raw_order_id)
           ->selectRaw('raw_fin_year_id')->first()->raw_fin_year_id;
    return $getId;
  }
  
  public function getVoucherNoByFinGoodsRecOrderNo($rec_order_id)
  {
    $getfin_no = FinishGoodsReceives::query()
           ->where('id',$rec_order_id)
           ->selectRaw('f_rec_order_no')->first()->f_rec_order_no;
    return $getfin_no;
  }

  public function getFinGoodsFinanYearByOrderNo($rec_order_id)
  {
    $getId = FinishGoodsReceives::query()
           ->where('id',$rec_order_id)
           ->selectRaw('f_rec_fin_year_id')->first()->f_rec_fin_year_id;
    return $getId;
  }

  public function getReturnIdByVoucherNo($voucher_no)
  {
    $acc_trans_id = 0;
    $getId = AccTransactions::query()
           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
           ->where('acc_invoice_no',$voucher_no)
           ->where('trans_type','RV')
           ->selectRaw('IFNULL(acc_trans_id,0) as acc_trans_id')
           ->get();

    foreach ($getId as $key => $value) {
          $acc_trans_id =  $value->acc_trans_id;
    }
    return $acc_trans_id;
  }
  
  public function getPOIdByVoucherNo($voucher_no)
  {
    $acc_trans_id = 0;
    $getId = AccTransactions::query()
           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
           ->where('acc_invoice_no',$voucher_no)
           ->where('trans_type','PV')
           ->selectRaw('IFNULL(acc_trans_id,0) as acc_trans_id')
           ->get();

    foreach ($getId as $key => $value) {
          $acc_trans_id =  $value->acc_trans_id;
    }
    return $acc_trans_id;
  }

  public function getFinYearValidation($comp_id,$u_date)
  {
    $getRec = FinancialYearDeclaration::query()
              ->where('comp_id',$comp_id)
              ->where('date_from','<=',$u_date)
              ->where('date_to','>=',$u_date)
              ->where('status','=',1) 
              ->selectRaw('count(id) as rec')->first()->rec;
    if ($getRec == 0) return false;
    else return true;
    //return $getMaxNo;
  }

  public function getFinYearId($comp_id,$u_date)
  {
    $getRec = FinancialYearDeclaration::query()
              ->where('comp_id',$comp_id)
              ->where('date_from','<=',$u_date)
              ->where('date_to','>=',$u_date)
              ->where('status','=',1) 
              ->selectRaw('IFNULL(id,0) as rec')->first()->rec;
    return $getRec;
    //return $getMaxNo;
  }
  
  public function getActiveFinYearId($comp_id)
  {
    $getRec = FinancialYearDeclaration::query()
              ->where('comp_id',$comp_id)
              ->where('status','=',1)
              ->selectRaw('id as rec')->first()->rec;
    return $getRec;
    //return $getMaxNo;
  }
  
  public function getActiveFinYearSerialNo($active_fin_year_id)
  {
    $getSerial = FinancialYearDeclaration::query()
              ->where('id','=',$active_fin_year_id)
              ->selectRaw('serial as serial')->first()->serial;
    return $getSerial;
    //return $getMaxNo;
  }

  public function getCombinFinancialYear($comp_id)
  {
    $getFinYear = FinancialYearDeclaration::query()
              ->where('comp_id',$comp_id)
              ->selectRaw('Concat(date_from," To ",date_to) as finyear')->first()->finyear;
    return $getFinYear;
  }
  
  public function UserName($id)
  {
      $name = User::query()
            ->selectRaw('name')
            ->where('id', $id)->first()->name;
      return $name;
  }
  
  public function getUserMappingData($id)
  {
      $rec = UsersMappingSps::query()
            ->selectRaw("count(id) as rec")
            ->where('u_user_id', $id)->first()->rec;
      return $rec;
  }

  public function CompanyName($comp_code)
  {
      $name = companies::query()
            ->selectRaw('name')
            ->where('id', $comp_code)->first()->name;
      return $name;
  }

  public function CompanyAddress($comp_code)
  {
      $address = companies::query()
            ->selectRaw("concat(address1,' ',address2) as address")
            ->where('id', $comp_code)->first()->address;
      return $address;
  }

  public function WarehouseName($wid)
  {
      $name = Warehouses::query()
            ->selectRaw('ware_name')
            ->where('id', $wid)->first()->ware_name;
      return $name;
  }
  
  public function getFinYearData($id)
  {
      $rec = AccTransactions::query()
            ->selectRaw("count(id) as rec")
            ->where('fin_ref_id', $id)->first()->rec;
      return $rec;
  }
  
  public function CompanyRefId($id)
  {
      $com_ref_id = AccTransactions::query()
            ->selectRaw("com_ref_id")
            ->where('id', $id)->first()->com_ref_id;
      return $com_ref_id;
  }

  public function getSalesPersonName($id)
  {
      $sp_data = CustomerSalesPersons::query()
            ->join("sys_infos", "sys_infos.id", "=", "customer_sales_persons.sales_desig")
            ->selectRaw("sales_name,sales_desig,vComboName")
            ->where('customer_sales_persons.id', $id)->first();
      return $sp_data;
  }
  
  public function getSalesPersonInf($company_code,$sales_per_id)
  {
      $data = CustomerSalesPersons::query()
            ->join("sys_infos", "sys_infos.id", "=", "customer_sales_persons.sales_desig")
            ->selectRaw('customer_sales_persons.id,sales_comp_id,sales_name,sales_desig,vComboName,sales_mobile,sales_email')
            ->where('combo_company_id', $company_code)
            ->where('customer_sales_persons.id', $sales_per_id)->get();
      return $data;
  }

  public function getCustomerInf($customer_id)
  {
      $data = Customers::query()
            ->leftJoin("customer_sales_persons", "cust_sales_per_id", "=", "customer_sales_persons.id")
            ->leftJoin("districts", "cust_dist_id", "=", "districts.id")
            ->selectRaw('customers.*,customer_sales_persons.sales_name,districts.vCityName')
            ->where('customers.id', $customer_id)->get();
      return $data;
  }
  
  public function CustomersCompanyId($id)
  {
      $cust_com_id = Customers::query()
            ->selectRaw("cust_com_id")
            ->where('id', $id)->first()->cust_com_id;
      return $cust_com_id;
  }
  
  public function getCustomerInfByLedgerId($ledger_id)
  {
      $data = Customers::query()
              ->where('cust_chartofacc_id', $ledger_id)
              ->select('cust_name','cust_add1','cust_add2')->first();
      return $data;
  }
  
  /*public function accTransDocTypeNo($type, $compid)
  {
    $accdocno = AccTransDocTypes::query()
              ->where('trans_type_no', $type)
              ->where('doc_comp_id', $compid)
              ->select('trans_type_no')->first()->trans_type_no;
    return $accdocno;
  }*/

  public function getAccTransDocType($type, $compid)
  {
    $accdocno = AccTransDocTypes::query()
              ->where('trans_type_no', $type)
              ->where('doc_comp_id', $compid)
              ->select('doc_type')->first()->doc_type;
    return $accdocno;
  }
  
  public function makeCustomerCode($compid)
  {
    $length = strlen($compid)+1;
    $maxCode = customers::where('cust_com_id', '=', $compid)
    ->selectRaw('max(substring(cust_id,'.$length.')) as MaxCode')->first()->MaxCode;
    $cust_code = $maxCode?$maxCode+1:1;
    if ($cust_code == 1) {
        $cust_code = $compid .'1'. sprintf("%04d", 1);
    }else{
        $cust_code = $compid.$cust_code;
    }
    return $cust_code;
  }
  
  public function makeCustomerSLNo($compid)
  { 
    $maxCode = customers::where('cust_com_id', '=', $compid)
    ->selectRaw('MAX(cust_slno) as MaxCode')->first()->MaxCode;
    $cust_slno = $maxCode+1; 
    return $cust_slno;
  }

  public function CustomerName($cust_id)
  {
      $name = customers::query()
            ->where('id', $cust_id)
            ->select('cust_name')->first()->cust_name;
      return $name;
  }

  public function CustomerCode($cust_id)
  {
      $code = customers::query()
            ->where('id', $cust_id)
            ->select('cust_code')->first()->cust_code;
      return $code;
  }

  public function CustomerId($cust_code)
  {
      $cust_id = customers::query()
            ->where('cust_code', $cust_code)
            ->select('id')->first()->id;
      return $cust_id;
  }

  public function CustomerAddress($cust_id)
  {

  }

  public function maxItemCode($cust_id)
  {
      $items = new Items();
      $code =  $items->makeItemCode($cust_id);
      return $code;
  }

  public function maxItemQRCode($cust_id)
  {
      $items = new Items();
      $code =  $items->makeitemqrcode($cust_id);
      return $code;
  }

  public function makeItemBarCode($cust_id)
  {
      $items = new Items();
      $code =  $items->makeitembarcode($cust_id);
      return $code;
  }

  public function getDateAttribute($value)
  {
      return Carbon::parse($value)->format('Y-m-d');
  }

  public function get_item_details($id){
      //return('ss'. $id);
      return new TransItemCodeResource(
        $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->where('items.id','=',$id)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','base_price as cust_price','vUnitName')->first()
      );
    }

    public function get_item_details_more($id){
        //return('ss'. $id);
      return  $itms =  Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('items.id', '=', $id)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
          'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
          ->orderBy('item_name','asc')->first();
      }
      
    public function getItemCode(Request $request)
    {
        $compcode = $request->get('compcode');
        $itemcode = $request->get('itemcode');
        $dropdownscontroller = new DropdownsController();
        $itemcode = $dropdownscontroller->comboItemCodeList1($compcode,$itemcode);
        return ItemCodeResource::collection($itemcode);
    }

    public function get_delivered_inf($id){
    //  return('ss'. $id);
        $data = CustomerDeliveryInfs::query()
          ->where('id','=',$id)
          ->select('id','cust_d_ref_id','deliv_to','deliv_add',
          'deliv_mobile','deliv_dist_id')->first();
        return $data;
    }

    public function get_delivered_id_default($custid){
      //  return('ss'. $id);
      $del_to_id = CustomerDeliveryInfs::query()
      ->where('cust_d_ref_id','=',$custid)
      ->select('customer_delivery_infs.id','cust_d_ref_id','deliv_to',
      'deliv_add','deliv_mobile','deliv_dist_id')
      ->orderBy('deliv_to','asc')->first()->id;
      return $del_to_id;
    }
    
    public function get_delivered_id_by_name($custid,$name){
      //  return('ss'. $id);
      $rec = CustomerDeliveryInfs::query()
      ->where('cust_d_ref_id','=',$custid)
      ->where('deliv_to','=',$name)
      ->selectRaw('count(id) as rec')->first()->rec;
      return $rec;
    }
    
    public function get_storageId($compid,$wid)
    {
      //return 'SSS';
      $data = StorageLocations::query()
              ->where('stor_comp_id', $compid)
              ->where('stor_warehouse_id', $wid)
              ->select('id','stor_code','stor_name')->first();
      return $data;
    }
    
    public function make_sales_orderno($compid,$finan_yearId)
    {
      $length = strlen($compid)+2;
      $getSerialNo = $this->getActiveFinYearSerialNo($finan_yearId);
      $maxCode = SalesOrders::where('so_comp_id', '=', $compid)
      ->where('so_fin_year_id', '=', $finan_yearId)
      ->selectRaw('max(substring(so_order_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $SO_No = $maxCode?$maxCode+1:1;
      if ($SO_No == 1) {
          $SO_No = '8'.$compid.$getSerialNo. sprintf("%03d", 1);
      }else{
          $SO_No = '8'.$compid.$SO_No;
      }
      return $SO_No;
    }
    
    public function make_sales_returnno($compid,$finan_yearId)
    {
      $length = strlen($compid)+2;
      $getSerialNo = $this->getActiveFinYearSerialNo($finan_yearId);
      $maxCode = SalesReturns::where('ret_comp_id', '=', $compid)
      ->where('ret_fin_year_id', '=', $finan_yearId)
      ->selectRaw('max(substring(ret_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $ret_No = $maxCode?$maxCode+1:1;
      if ($ret_No == 1) {
          $ret_No = '4'.$compid.$getSerialNo. sprintf("%03d", 1);
      }else{
          $ret_No = '4'.$compid.$ret_No;
      }
      return $ret_No;
    }
    
    public function make_sales_order_ref($compid)
    {
      $length = 9;

      $maxCode = SalesOrders::where('so_comp_id', '=', $compid)
      ->selectRaw('MAX(CONVERT(substring(so_reference,'.$length.'),int)) as MaxRef')->first()->MaxRef;

      $ref_No = $maxCode?$maxCode+1:1;
      if ($ref_No == 1) {
          $ref_No = 'REF/'.date('y').'-'.$compid.sprintf("%03d", 1);
      }else{
          $ref_No = 'REF/'.date('y').'-'.$compid.$ref_No;
      }
      return $ref_No;
    }
    
    public function make_sales_loan_orderno($compid,$finan_yearId)
    {
      $length = strlen($compid)+2;
      $getSerialNo = $this->getActiveFinYearSerialNo($finan_yearId);
      $maxCode = SalesLoans::where('loan_i_comp_id', '=', $compid)
      ->where('loan_i_fin_year_id', '=', $finan_yearId)
      ->selectRaw('max(substring(loan_i_order_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $SO_No = $maxCode?$maxCode+1:1;
      if ($SO_No == 1) {
          $SO_No = '3'.$compid.$getSerialNo. sprintf("%03d", 1);
      }else{
          $SO_No = '3'.$compid.$SO_No;
      }
      return $SO_No;
    }
    
    public function make_sales_loan_ref($compid)
    {
      $length = 10;

      $maxCode = SalesLoans::where('loan_i_comp_id', '=', $compid)
      ->selectRaw('MAX(CONVERT(substring(loan_i_reference,'.$length.'),int)) as MaxRef')->first()->MaxRef;

      $ref_No = $maxCode?$maxCode+1:1;
      if ($ref_No == 1) {
          $ref_No = 'Loan/'.date('y').'-'.$compid.sprintf("%03d", 1);
      }else{
          $ref_No = 'Loan/'.date('y').'-'.$compid.$ref_No;
      }
      return $ref_No;
    }


    public function make_deliv_orderno($compid,$finan_yearId)
    {
      $length = strlen($compid)+2;
      $getSerialNo = $this->getActiveFinYearSerialNo($finan_yearId);
      $maxCode = SalesDeliveries::where('del_comp_id', '=', $compid)
      ->where('del_fin_year_id', '=', $finan_yearId)
      ->selectRaw('max(substring(del_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $del_No = $maxCode?$maxCode+1:1;
      if ($del_No == 1) {
          $del_No = '7'.$compid.$getSerialNo. sprintf("%03d", 1);
      }else{
          $del_No = '7'.$compid.$del_No;
      }
      return $del_No;
    }

    public function make_invoice_no($compid,$finan_yearId)
    {
      $length = strlen($compid)+2;
      $getSerialNo = $this->getActiveFinYearSerialNo($finan_yearId);
      $maxCode = SalesInvoices::where('inv_comp_id', '=', $compid)
      ->where('inv_fin_year_id', '=', $finan_yearId)
      ->selectRaw('max(substring(inv_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $inv_No = $maxCode?$maxCode+1:1;
      if ($inv_No == 1) {
          $inv_No = '6'.$compid.$getSerialNo. sprintf("%03d", 1);
      }else{
          $inv_No = '6'.$compid.$inv_No;
      }
      return $inv_No;
    }
    
    

  public function CustomerChartOfAccId($cust_id)
  {
      $code = customers::query()
            ->where('id', $cust_id)
            ->select('cust_chartofacc_id')->first()->cust_chartofacc_id;
      return $code;
  }


  public function accountNameLookup($headid)
  {
    $name = Chartofaccounts::query()
    ->where('id','=',$headid)
    ->select('acc_head')->first()->acc_head;
    return $name;
  }


  public function getChartofAccId($comapny_id,$param)
  {
      $acc_id = Chartofaccounts::query()
      ->where('company_id','=',$comapny_id)
      ->where('parent_id','<>',0)
      ->where('acc_head','like',$param.'%')
      ->select('id')->first()->id;
      return $acc_id;
  }
  
   public function get_del_id_by_invid($id){
      $del_id = SalesInvoices::query()
        ->join("sales_invoice_details", "sales_invoices.id", "=", "inv_mas_id")
        ->where('sales_invoices.id','=',$id)
        ->select('inv_del_id')->first()->inv_del_id;
      return $del_id;

    }
    
    public function get_inv_id_by_invno($invno,$finan_yearId){
       $inv_id = SalesInvoices::query()
         ->where('inv_no','=',$invno) 
         ->select('id')->first()->id;
       return $inv_id;
    }
     
     public function get_inv_company_id_by_invno($invno){
        $inv_id = SalesInvoices::query()
          ->where('inv_no','=',$invno)
          ->selectRaw('MAX(inv_comp_id) as inv_comp_id')->first()->inv_comp_id;
        return $inv_id;
      }
     
    public function get_stockLookup($storgaeid,$itemid,$lotno)
    {
      $stock = ItemStocks::query()
      ->where('item_ref_id','=',$itemid)
      ->where('item_storage_loc','=',$storgaeid)
      ->where('item_lot_no','=',$lotno)
      ->selectRaw('IFNULL(SUM(item_op_stock),0) as stock')->first();
      return response()->json($stock);
    }

    public function get_stock_wh_Lookup($wid,$itemid)
    {
      $stock = ItemStocks::query()
      ->where('item_ref_id','=',$itemid)
      ->where('item_warehouse_id','=',$wid)
      ->selectRaw('IFNULL(SUM(item_op_stock),0) as stock')->first();
      return response()->json($stock);
    }

    public function get_avg_price_comp_Lookup($compid,$itemid)
    {
      $avgprice = view_item_avg_price::query()
      ->where('l_item_ref_id','=',$itemid)
      ->where('l_item_op_comp_id','=',$compid)
      ->selectRaw('IFNULL(SUM(l_item_avg_price),0) as avgprice')->first();
      return response()->json($avgprice);
    }

    
    public function make_purchase_orderno($compid,$finan_yearId)
    {
      $length = strlen($compid)+2;
      $getSerialNo = $this->getActiveFinYearSerialNo($finan_yearId);
      $maxCode = ItemPurchases::where('pur_comp_id', '=', $compid)
      ->where('pur_fin_year_id', '=', $finan_yearId)
      ->selectRaw('max(substring(pur_order_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $pur_no = $maxCode?$maxCode+1:1;
      if ($pur_no == 1) {
          $pur_no = '2'.$compid.$getSerialNo. sprintf("%03d", 1);
      }else{
          $pur_no = '2'.$compid.$pur_no;
      }
      return $pur_no;
    }
    
    public function get_purchase_orderno($supp_id)
    {
        $po_no = ItemPurchases::query()
              ->where('id', $supp_id)
              ->select('pur_order_no')->first()->pur_order_no;
        return $po_no;
    }
    
    public function make_purchase_orderno1($compid)
    {
      $length = strlen($compid)+2;
      $maxCode = PurchaseOrders::where('po_comp_id', '=', $compid)
      ->selectRaw('max(substring(po_order_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $pur_no = $maxCode?$maxCode+1:1;
      if ($pur_no == 1) {
          $pur_no = '3'.$compid.'1'. sprintf("%03d", 1);
      }else{
          $pur_no = '3'.$compid.$pur_no;
      }
      return $pur_no;
    }

    public function get_purchase_orderno1($pur_id)
    {
        $po_no = PurchaseOrders::query()
              ->where('id', $pur_id)
              ->select('po_order_no')->first()->po_order_no;
        return $po_no;
    }
    
    public function make_purchase_refno($pur_id)
    {
        $po_no = PurchaseOrders::query()
              ->where('id', $pur_id)
              ->select('po_order_ref')->first()->po_order_ref;
        return $po_no;
    }
    
    public function make_raw_orderno($compid)
    {
      $length = strlen($compid)+2;
      $maxCode = RawMaterialsReceives::where('raw_comp_id', '=', $compid)
      ->selectRaw('max(substring(raw_order_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $pur_no = $maxCode?$maxCode+1:1;
      if ($pur_no == 1) {
          $pur_no = '1'.$compid.'1'. sprintf("%03d", 1);
      }else{
          $pur_no = '1'.$compid.$pur_no;
      }
      return $pur_no;
    }

    public function get_raw_orderno($raw_id)
    {
        $raw_no = RawMaterialsReceives::query()
              ->where('id', $raw_id)
              ->select('raw_order_no')->first()->raw_order_no;
        return $raw_no;
    }

    public function make_raw_issue_orderno($compid)
    {
      $length = strlen($compid)+2;
      $maxCode = RawMaterialsIssues::where('r_issue_comp_id', '=', $compid)
      ->selectRaw('max(substring(r_issue_order_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $pur_no = $maxCode?$maxCode+1:1;
      if ($pur_no == 1) {
          $pur_no = '3'.$compid.'1'. sprintf("%03d", 1);
      }else{
          $pur_no = '3'.$compid.$pur_no;
      }
      return $pur_no;
    }
    
    public function make_consumable_orderno($compid)
    {
      $length = strlen($compid)+2;
      $maxCode = ConsumeMaterials::where('r_cons_comp_id', '=', $compid)
      ->selectRaw('max(substring(r_cons_order_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $pur_no = $maxCode?$maxCode+1:1;
      if ($pur_no == 1) {
          $pur_no = '2'.$compid.'1'. sprintf("%03d", 1);
      }else{
          $pur_no = '2'.$compid.$pur_no;
      }
      return $pur_no;
    }

    public function make_consumable_order_ref($compid)
    {
      $length = 7;
      $maxCode = ConsumeMaterials::where('r_cons_comp_id', '=', $compid)
      ->selectRaw('MAX(CONVERT(substring(r_cons_order_ref,'.$length.'),int)) as MaxRef')->first()->MaxRef;

      $ref_No = $maxCode?$maxCode+1:1;
      if ($ref_No == 1) {
          $ref_No = 'PD-'.date('y').'/'.$compid.sprintf("%02d", 1);
      }else{
          $ref_No = 'PD-'.date('y').'/'.$ref_No;
      }
      return $ref_No;
    }


    public function get_consume_issue_orderno($raw_id)
    {
        $raw_no = ConsumeMaterials::query()
              ->where('id', $raw_id)
              ->select('r_cons_order_no')->first()->r_cons_order_no;
        return $raw_no;
    }
    
    public function get_raw_issue_orderno($raw_id)
    {
        $raw_no = RawMaterialsIssues::query()
              ->where('id', $raw_id)
              ->select('r_issue_order_no')->first()->r_issue_order_no;
        return $raw_no;
    }

    public function make_fin_goods_rec_orderno($compid)
    {
      $length = strlen($compid)+2;
      $maxCode = FinishGoodsReceives::where('f_rec_comp_id', '=', $compid)
      ->selectRaw('max(substring(f_rec_order_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $pur_no = $maxCode?$maxCode+1:1;
      if ($pur_no == 1) {
          $pur_no = '5'.$compid.'1'. sprintf("%03d", 1);
      }else{
          $pur_no = '5'.$compid.$pur_no;
      }
      return $pur_no;
    }

    public function get_fin_goods_rec_orderno($fin_id)
    {
        $fin_no = FinishGoodsReceives::query()
              ->where('id', $fin_id)
              ->select('f_rec_order_no')->first()->f_rec_order_no;
        return $fin_no;
    }

    public function SupplierChartOfAccId($supp_id)
    {
        $code = Suppliers::query()
              ->where('id', $supp_id)
              ->select('supp_chartofacc_id')->first()->supp_chartofacc_id;
        return $code;
    }

    public function SupplierName($supp_id)
    {
        $name = Suppliers::query()
              ->where('id', $supp_id)
              ->select('supp_name')->first()->supp_name;
        return $name;
    }
    
    public function make_purchase_order_ref($compid)
    {
      $length = 7;
      $maxCode = PurchaseOrders::where('po_comp_id', '=', $compid)
      ->selectRaw('MAX(CONVERT(substring(po_order_ref,'.$length.'),int)) as MaxRef')->first()->MaxRef;

      $ref_No = $maxCode?$maxCode+1:1;
      if ($ref_No == 1) {
          $ref_No = 'PO-'.date('y').'/'.$compid.sprintf("%02d", 1);
      }else{
          $ref_No = 'PO-'.date('y').'/'.$compid.$ref_No;
      }
      return $ref_No;
    }

    public function make_raw_issue_order_ref($compid)
    {
      $length = 7;
      $maxCode = RawMaterialsIssues::where('r_issue_comp_id', '=', $compid)
      ->selectRaw('MAX(CONVERT(substring(r_issue_order_ref,'.$length.'),int)) as MaxRef')->first()->MaxRef;

      $ref_No = $maxCode?$maxCode+1:1;
      if ($ref_No == 1) {
          $ref_No = 'PD-'.date('y').'/'.$compid.sprintf("%02d", 1);
      }else{
          $ref_No = 'PD-'.date('y').'/'.$ref_No;
      }
      return $ref_No;
    }
    
    public function makeQuotationNo($compname,$compid)
    {
      $length = strlen($compname)+2;
      $maxCode = SalesQuotations::where('quot_comp_id', '=', $compid)
      ->selectRaw('max(substring(quot_ref_no,'.$length.')) as MaxCode')->first()->MaxCode;
      $quot_max_ref_no = $maxCode?$maxCode+1:1;
      $quot_ref_code = $compname .'/'. $quot_max_ref_no;
      return $quot_ref_code;
    }
    
    public function get_sel_item1($id,$custid){
    //  return('ss'. $id);
        $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("customer_prices", "cust_item_p_id", "=", "items.id")
          ->where('items.id','=',$id)
          ->where('customer_prices.cust_p_ref_id','=',$custid)
          ->where('customer_prices.p_del_flag','=',0)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','cust_price','cust_comm')->first();
        return $itms;
    }
    
    public function RoleName($id)
    {
        $name = Role::query()
              ->selectRaw('name')
              ->where('id', $id)->first()->name;
        return $name;
    }
    
     public function duplicteName_sysinfos($type,$name){
      //  return('ss'. $id);
      $rec = SysInfos::query()
      ->where('vComboType','=',$type)
      ->where('vComboName','=',$name)
      ->selectRaw('count(id) as rec')->first()->rec;
      return $rec;
    }
    
  public function getEmployeeDetailsInf($compid, $empid)
  {
    $getData = Employees::query() 
      ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
      ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
      ->leftJoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
      ->where('emp_com_id',$compid)
      ->where('employees.id', $empid)
      ->selectRaw('emp_id_no,emp_name,desig.vComboName as designation,dept.vComboName as department,
      sec.vComboName as section')->first();
      return $getData;
  }

  public function get_leave_stock($compid,$leavetypeid)
  {
      $days = EmpLeaveTypes::query()
      ->where('ltype_comp_id','=',$compid)
      ->where('id','=',$leavetypeid)
      ->selectRaw('IFNULL(SUM(days),0) as days')->first();
      return $days;
  }

  public function get_leave_availed($compid,$empid,$leavetypeid)
  {
      $availed = EmpLeaves::query()
      ->where('leave_comp_id','=',$compid)
      ->where('leave_emp_id','=',$empid)
      ->where('leave_type_id','=',$leavetypeid)
      ->selectRaw('IFNULL(SUM(leave_days),0) as availed')->first();
      return $availed;
  }

}