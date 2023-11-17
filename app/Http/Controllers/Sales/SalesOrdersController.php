<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Customers\Customers;
use App\Models\Sales\SalesOrders;
use App\Models\Sales\SalesOrdersDetails;
use App\Models\Sales\DirectSales;
use App\Models\Sales\DirectSalesDetails;
use App\Models\Sales\SalesOrdersConfirmations;
use App\Models\Sales\SalesOrdersDirectConfirmations;
use App\Models\Sales\SalesDeliveries;
use App\Models\Sales\SalesDeliveryDetails;
use App\Models\Sales\SalesInvoices;
use App\Models\Sales\SalesInvoiceDetails;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
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

class SalesOrdersController extends Controller
{
    public $user_sp_mapping = false;
      
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  

   public function so_index(Request $request)
   {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      
      $customers = $this->so_customers();

      if(isset($request->fromdate)){
        $fromdate = $request->fromdate;
      }else{
        $fromdate = date('d-m-Y');
      }
      
      if(isset($request->todate)){
        $todate = $request->todate;
      }else{
        $todate = date('d-m-Y');
      }
      
      $q = SalesOrders::query()
           ->join("customers", "customers.id", "=", "so_cust_id")
           ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
           ->where('so_comp_id', $company_code )
           ->selectRaw('sales_orders.id,so_comp_id,so_order_no,remark,so_order_date,so_confirmed_date,so_fpo_no,so_req_del_date, so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per, so_vat_value,so_net_amt,so_carring_cost,so_labour_cost,so_load_unload_cost,so_service_charge,so_other_cost,cust_code,so_is_confirmed,cust_name,so_is_locked,so_del_done,so_direct_inv');
      if($request->filled('order_no')){
        $q->where('so_order_no', $request->get('order_no'));
      }
      if($request->filled('customer_id')){
        $q->where('so_cust_id', $request->get('customer_id'));
      }
      if($this->user_sp_mapping){
        $q->where('sales_orders.created_by', Auth::id());
      }

      if($request->filled('fromdate')){
        $formDate = date('Y-m-d', strtotime($request->fromdate)) ;
        $toDate = date('Y-m-d',strtotime($request->todate));

        $q->whereBetween('so_order_date', [$formDate, $toDate]);
      } 
      
      $rows = $q->orderBy('sales_orders.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'order_no'   => $request->get('order_no'),
        'customer_id' => $request->get('customer_id'), 
      ));
      return view ('/sales/so_index', compact('rows', 'customers', 'fromdate', 'todate'));
  } 


  public function sales_order_direct(Request $request)
  {
    
    $dropdownscontroller = new DropdownsController();
    $companies    = $dropdownscontroller->comboCompanyAssignList();
    $company_code = $dropdownscontroller->defaultCompanyCode();
    
    $customers = $this->so_customers();

    if(isset($request->fromdate)){
      $fromdate = $request->fromdate;
    }else{
      $fromdate = date('d-m-Y');
    }
    
    if(isset($request->todate)){
      $todate = $request->todate;
    }else{
      $todate = date('d-m-Y');
    }
    
    $q = DirectSales::query()
         ->join("customers", "customers.id", "=", "so_cust_id")
         ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
         ->where('so_comp_id', $company_code )
         ->selectRaw('sales_order_direct.id,so_comp_id,so_order_no,remark,so_order_date,so_confirmed_date,so_fpo_no,so_req_del_date, so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per, so_vat_value,so_net_amt,so_carring_cost,so_labour_cost,so_load_unload_cost,so_service_charge,so_other_cost,cust_code,so_is_confirmed,cust_name,so_is_locked,so_del_done,so_direct_inv');
    if($request->filled('order_no')){
      $q->where('so_order_no', $request->get('order_no'));
    }
    if($request->filled('customer_id')){
      $q->where('so_cust_id', $request->get('customer_id'));
    }
    if($this->user_sp_mapping){
      $q->where('sales_order_direct.created_by', Auth::id());
    }

    if($request->filled('fromdate')){
      $formDate = date('Y-m-d', strtotime($request->fromdate)) ;
      $toDate = date('Y-m-d',strtotime($request->todate));

      $q->whereBetween('so_order_date', [$formDate, $toDate]);
    } 
    
    $rows = $q->orderBy('sales_order_direct.id', 'desc')->paginate(10)->setpath('');
    $rows->appends(array(
      'order_no'   => $request->get('order_no'),
      'customer_id' => $request->get('customer_id'),
    ));
    return view ('/sales/so_direct_index', compact('rows', 'customers', 'fromdate', 'todate'));
  }

  public function direct_sale(Request $request)
  {
    $dropdownscontroller = new DropdownsController();
    $company_code = $dropdownscontroller->defaultCompanyCode();
    $companies  = $dropdownscontroller->comboCompanyAssignList();
    $dist_list  = $dropdownscontroller->comboDistrictsList();
    $del_list  = $dropdownscontroller->deliveryList($company_code);
    $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
    $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
    $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
    $generalsController = new GeneralsController();
    $reference_no = $generalsController->make_sales_order_ref($company_code);
    

    $sql = "select * from `item_categories`
    where `itm_comp_id` = $company_code and itm_cat_code like '20%' and `id` not in (select `parent_id` from `item_categories`)
    Order By itm_cat_name asc";
    $itm_cat = DB::select($sql);

    $itm_cat = DB::table('item_categories')->where('itm_comp_id', $company_code)
    ->whereIn('itm_cat_code', ['101', '102'])->get();

    
    $item_list =  Items::query() 
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', '=', $company_code)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','itm_cat_name')
        ->orderBy('item_name','asc')->get();
    
    $unit_list = $dropdownscontroller->comboUnitsList($company_code);
    $order_date = date('d-m-Y');
     
    $customers = $this->so_customers();

    return view('/sales/so_create_direct',compact('companies','order_date','company_code',
    'customers','itm_cat','item_list','unit_list','reference_no','del_list','courr_list','warehouse_list','stor_list'))->render();
  }

    public function direct_sale_stora(Request $request)
    {
      // return $request;

      $generalscontroller = new GeneralsController();
      $company_code = $request->company_code;
      $inv_date     = date('Y-m-d',strtotime($request->order_date));
      $yearValidation = $generalscontroller->getFinYearValidation($company_code,$inv_date);
      if($yearValidation) {
        $finan_yearId = $generalscontroller->getFinYearId($company_code,$inv_date);
      }else{
        return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
      }
      //Item Validity Check
      $msg = $this->ItemValidityCheck($request);
      if($msg != '') {
        //  return redirect()->back()->with('message',$msg)->withInput();
        //   return redirect()->route('itm.inventory.create')->with('message',$msg)->withInput();
        //   return back()->with('message',$msg)->withInput();
      } 
      
        $sales_orderno = $generalscontroller->make_sales_orderno($request->company_code,$finan_yearId);
        $del_customer = $generalscontroller->get_delivered_inf($request->delivered_to);

        $get_cust_comm = $generalscontroller->getCustomerVAT($request->company_code,$request->customer_id);

        $trans_id = DirectSales::insertGetId([
        'so_comp_id'    => $request->company_code,
        'so_fin_year_id'  => $finan_yearId,
        'so_m_warehouse_id'  => $request->itm_warehouse,
        'so_order_title'=> 'SO',
        'so_order_no'   => $sales_orderno,
        'so_order_date' => date('Y-m-d',strtotime($request->order_date)),
        'so_reference'  => $request->reference_no,
        'so_cust_id'    => $request->customer_id,
        'so_req_del_date' => date('Y-m-d',strtotime($request->delivery_date)),
        'so_del_to'       => $request->delivered_to,
        //'so_del_customer' => $del_customer->deliv_to,
        'so_del_add'      => $request->address1,
        'so_cont_no'      => $request->contact_no,
        'so_del_ref'      => $request->cust_ref,
        'so_comments'     => $request->comments,
        'so_courrier_to'   => $request->courr_id,
        'so_courrier_cond' => $request->condition_tag,
        'so_sub_total'    => ($request->n_sub_total=='')?'0':$request->n_sub_total,
        'so_disc_per'    => ($request->n_disc_per=='')?'0':$request->n_disc_per,
        'so_disc_value'  => ($request->n_discount=='')?'0':$request->n_discount,
        'so_total_disc'  => ($request->n_total_disc=='')?'0':$request->n_total_disc,
        'so_gross_amt'   => ($request->n_total_gross=='')?'0':$request->n_total_gross,
        'so_vat_per'     => ($request->n_vat_per=='')?'0':$request->n_vat_per,
        'so_vat_value'   => ($request->n_total_vat=='')?'0':$request->n_total_vat,
        'so_special_offer' => $request->special_offer,

        'so_carring_cost' => ($request->carring_cost=='')?'0':$request->carring_cost,
        'so_labour_cost'  => ($request->labour_cost=='')?'0':$request->labour_cost,
        'so_load_unload_cost' => ($request->load_unload_cost=='')?'0':$request->load_unload_cost,
        'so_service_charge'   => ($request->service_charge=='')?'0':$request->service_charge,
        'so_other_cost'   => ($request->other_cost=='')?'0':$request->other_cost, 

        'so_net_amt'     => ($request->t_n_net_amount=='')?'0':$request->t_n_net_amount,
        'remark'        => $request->remark,
        //'so_is_confirmed' => 1,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
        ]);

        //Details Records
        $detId = $request->input('ItemCodeId');
        if ($detId){
            foreach ($detId as $key => $value){
              
              if ($request->Qty[$key] > 0){
                
                //  define the commision
                $comm = $get_cust_comm->cust_overall_comm;
                if($comm <= 0 ){
                    $get_itm_comm = $generalscontroller->get_sel_item1($request->ItemCodeId[$key],$request->customer_id);
                    $comm = 0; //$get_itm_comm->cust_comm;
                    //echo 'BB'.$comm;
                }

                DirectSalesDetails::create([
                    'so_comp_id'    => $request->company_code,
                    'so_order_id'   => $trans_id,
                    'so_warehouse_id' => $request->itm_warehouse,
                    'so_storage_id' => 1,
                    'so_lot_no'     => 101, //$request->lotno[$key],
                    'so_item_id'    => $request->itemid[$key],
                    // 'so_item_spec'  => $request->ItemDesc[$key],
                    'so_item_unit'  => $request->Unit[$key],
                    'so_item_price' => $request->Price[$key],
                    'so_item_cat_id' => $request->itmcategory[$key],
                    'so_item_cat_2nd_id' => $request->catid[$key],
                    'so_item_size'  => $request->Size[$key],
                    'so_item_weight' => $request->QWeight[$key],
                    'so_item_pcs'   => $request->PCS[$key], 
                    'so_order_qty'  => $request->Qty[$key]==''?'0':$request->Qty[$key],
                    'so_order_disc' => $request->Discp[$key]==''?'0':$request->Discp[$key],
                    'so_order_comm' => $comm,
                ]);
              }
            }
        }


      return back()->with('message', 'Successfully create');
    }


    public function direct_sale_edit($id, Request $request)
    {
        $dropdownscontroller = new DropdownsController();
        $q = DirectSales::query()
             ->join("customers", "customers.id", "=", "so_cust_id")
             ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
             ->where('sales_order_direct.id', $id )
             ->selectRaw('sales_order_direct.id,so_comp_id,so_m_warehouse_id,so_order_no,so_order_date,so_reference,so_cust_id,so_req_del_date,so_del_to,deliv_to,so_del_add,so_cont_no,
             so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,so_vat_value,so_net_amt,so_carring_cost,so_labour_cost,so_load_unload_cost,
             so_service_charge,so_other_cost,so_is_confirmed,so_courrier_to,so_courrier_cond,cust_code,cust_name, so_special_offer');
        $mas = $q->orderBy('sales_order_direct.id', 'desc')->first();

        $company_id = $mas->so_comp_id;
        $companies = $dropdownscontroller->comboDefaultCompanyList($company_id);
        //$companies  = $dropdownscontroller->comboCompanyAssignList();
        $customers = Customers::query()->orderBy('cust_name','asc')->get();
        $courr_list  = $dropdownscontroller->comboCourrierList($company_id);
        $warehouse_list = $dropdownscontroller->WareHouseList($company_id);
        $stor_list  = $dropdownscontroller->comboStorageList($company_id,$mas->so_m_warehouse_id);
        $item_list =  Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_ref_comp_id', '=', $company_id)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
          'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')
          ->orderBy('item_name','asc')->get();
    
        
        $so_del_no = 0;
        $so_inv_no = 0;
      

        $sql= "select sales_orders_direct_details.*,stock as item_bal_stock,items.item_code, items.item_name, item_bar_code,item_desc,item_ref_cate_id
             from `sales_orders_direct_details`
             left join `view_item_stocks` on `item_ref_id` = `so_item_id` and `item_warehouse_id` = so_warehouse_id
             and `item_storage_loc` = so_storage_id and `so_lot_no` = item_lot_no
             inner join `items` on `items`.`id` = `so_item_id`
             inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id`
             where `so_order_id` = ".$id." order by sales_orders_direct_details.id asc";
       $det = DB::select($sql);


      $unit_list = $dropdownscontroller->comboUnitsList($company_id);  

      $itm_cat = DB::table('item_categories')->where('itm_comp_id', $company_id)
      ->whereIn('itm_cat_code', ['101', '102'])->get();
 
      return view('/sales/so_direct_edit',compact('companies','customers','item_list','unit_list','itm_cat', 'courr_list','warehouse_list','stor_list','mas','det','so_del_no','so_inv_no'))->render();
      

    }


    public function direct_sale_update($id, Request $request)
    {

        
        $generalscontroller = new GeneralsController();
        $company_code = $request->company_code;
        $inv_date     = date('Y-m-d',strtotime($request->order_date));
        $finan_yearId = $generalscontroller->getFinYearId($company_code, $inv_date);

        
        $sales_orderno = $generalscontroller->make_sales_orderno($request->company_code,$finan_yearId);
        $del_customer = $generalscontroller->get_delivered_inf($request->delivered_to);

        $get_cust_comm = $generalscontroller->getCustomerVAT($request->company_code,$request->customer_id);


        $directSale = DirectSales::find($id);
        $directSale->so_comp_id    = $request->company_code;
        $directSale->so_fin_year_id  = $finan_yearId;
        $directSale->so_m_warehouse_id  = $request->itm_warehouse;
        $directSale->so_order_title= $directSale->SO;
        $directSale->so_order_no   = $sales_orderno;
        $directSale->so_order_date = date('Y-m-d',strtotime($request->order_date));
        $directSale->so_reference  = $request->reference_no;
        $directSale->so_cust_id    = $request->customer_id;
        $directSale->so_req_del_date = date('Y-m-d',strtotime($request->delivery_date));
        $directSale->so_del_to       = $request->delivered_to;
        $directSale->so_del_add      = $request->address1;
        $directSale->so_cont_no     = $request->contact_no;
        $directSale->so_del_ref     = $request->cust_ref;
        $directSale->so_comments     = $request->comments;
        $directSale->so_courrier_to  = $request->courr_id;
        $directSale->so_courrier_cond = $request->condition_tag;
        $directSale->so_sub_total    = ($request->n_sub_total=='')?'0':$request->n_sub_total;
        $directSale->so_disc_per   = ($request->n_disc_per=='')?'0':$request->n_disc_per;
        $directSale->so_disc_value  = ($request->n_discount=='')?'0':$request->n_discount;
        $directSale->so_total_disc  = ($request->n_total_disc=='')?'0':$request->n_total_disc;
        $directSale->so_gross_amt   = ($request->n_total_gross=='')?'0':$request->n_total_gross;
        $directSale->so_vat_per   = ($request->n_vat_per=='')?'0':$request->n_vat_per;
        $directSale->so_vat_value   = ($request->n_total_vat=='')?'0':$request->n_total_vat;
        $directSale->so_special_offer = $request->special_offer;
        $directSale->so_carring_cost = ($request->carring_cost=='')?'0':$request->carring_cost;
        $directSale->so_labour_cost = ($request->labour_cost=='')?'0':$request->labour_cost;
        $directSale->so_load_unload_cost = ($request->load_unload_cost=='')?'0':$request->load_unload_cost;
        $directSale->so_service_charge   = ($request->service_charge=='')?'0':$request->service_charge;
        $directSale->so_other_cost   = ($request->other_cost=='')?'0':$request->other_cost;
        $directSale->so_net_amt    = ($request->t_n_net_amount=='')?'0':$request->t_n_net_amount;
        $directSale->remark        = $request->remark;

        $directSale->save();


        $details = DirectSalesDetails::where('so_order_id', $directSale->id)->delete();

        //Details Records
        $detId = $request->input('ItemCodeId');
        if ($detId){
            foreach ($detId as $key => $value){
              
              if ($request->Qty[$key] > 0){
                
                //  define the commision
                $comm = $get_cust_comm->cust_overall_comm;
                if($comm <= 0 ){
                  $get_itm_comm = $generalscontroller->get_sel_item1($request->ItemCodeId[$key],$request->customer_id);
                  $comm = 0; //$get_itm_comm->cust_comm;
                  //echo 'BB'.$comm;
                }

           

                DirectSalesDetails::create([
                    'so_comp_id'    => $request->company_code,
                    'so_order_id'   => $directSale->id,
                    'so_warehouse_id' => $request->itm_warehouse,
                    'so_storage_id' => 1,
                    'so_lot_no'     => 101, //$request->lotno[$key],
                    'so_item_id'    => $value, // $request->itemid[$key],
                    // 'so_item_spec'  => $request->ItemDesc[$key],
                    'so_item_unit'  => $request->Unit[$key],
                    'so_item_price' => $request->Price[$key],
                    'so_item_cat_id' => $request->itmcategory[$key],
                    'so_item_cat_2nd_id' => $request->catid[$key],
                    'so_item_size'  => $request->Size[$key],
                    'so_item_weight' => $request->QWeight[$key],
                    'so_item_pcs'   => $request->PCS[$key], 
                    'so_order_qty'  => $request->Qty[$key]==''?'0':$request->Qty[$key],
                    'so_order_disc' => $request->Discp[$key]==''?'0':$request->Discp[$key],
                    'so_order_comm' => $comm,
                ]);
              }
            }
        }

        return back()->with('message', 'successfully update');
    }

    public function so_direct_confirmed($id)
    {

      $q = DirectSales::query()
      ->where('sales_order_direct.id', $id )
      ->selectRaw('id,so_order_no,so_order_date');
      $rows = $q->first();

      $det = DirectSalesDetails::query()
      ->where('so_order_id', $id)
      ->selectRaw('id,so_comp_id,so_order_id,so_warehouse_id,so_storage_id,so_lot_no,so_item_id,so_item_unit,so_item_price,so_order_comm,so_order_qty,so_order_con_qty,so_order_bal_qty,so_order_disc,so_order_qty_done')
      ->orderBy('id', 'asc')->get();

      // sales order confirmation entry
      foreach($det as $d){
        SalesOrdersDirectConfirmations::create([
          'so_con_comp_id'    => $d->so_comp_id,
          'so_ref_order_id'   => $d->so_order_id,
          'so_ref_order_no'   => $rows->so_order_no,
          'so_ref_details_id' => $d->id,
          'so_conf_date'      => $rows->so_order_date,
          'so_conf_itm_id'    => $d->so_item_id,
          'so_order_qty'      => $d->so_order_qty,
          'so_order_conf_qty' => $d->so_order_qty,
          
        ]);
        
      }
      $lastSaleOrder = DirectSales::where('so_is_confirmed', 1)->orderBy('id', 'DESC')->limit(1)->first();

      $inputdata  = DirectSales::find($id);
      $inputdata->so_is_confirmed  = 1;
      $inputdata->so_confirmed_date  = date('d/m/Y');
      $inputdata->so_fpo_no  = ($lastSaleOrder != null) ? $lastSaleOrder->so_fpo_no + 1 : 1;
      $inputdata->save();
      return back()->with('message','SO Confirmation is Done')->withInput(); 
    }

    public function sal_direct_order_pdf($id, Request $request)
    { 
      error_reporting(0); 
      if(isset($request->remark)){
        $uSalesOrders = DirectSales::find($id);
        $uSalesOrders->remark = $request->remark;
        $uSalesOrders->save();
      }else{
        $uSalesOrders = DirectSales::find($id);
        $uSalesOrders->remark = '';
        $uSalesOrders->save();
      }

      $q = DirectSales::query()
      ->join("customers", "customers.id", "=", "so_cust_id")
      ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
      ->where('sales_order_direct.id', $id )
      ->selectRaw('sales_order_direct.id,so_comp_id,so_order_no,so_order_date,so_req_del_date,so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,so_vat_value,so_net_amt,so_carring_cost,so_labour_cost,so_load_unload_cost,so_service_charge,so_other_cost,so_is_confirmed,remark,cust_code,cust_name,cust_add1,cust_add2,cust_mobile, cust_slno, personalMobileno, so_special_offer'); 
      $rows_m = $q->orderBy('sales_order_direct.id', 'desc')->first();
      
      //   return $rows_m;

      $rows_d = DirectSalesDetails::query()
      ->join("items", "items.id", "=", "so_item_id")
      ->join("item_categories", "item_categories.id", "=", "sales_orders_direct_details.so_item_cat_id")
      ->leftjoin("item_categories as two", "two.id", "=", "sales_orders_direct_details.so_item_cat_2nd_id")
      ->where('so_order_id', $id )
      ->selectRaw('sales_orders_direct_details.id,so_order_id,so_item_id,item_code,item_bar_code,item_name,item_categories.itm_cat_name, two.itm_cat_name as nd_name, so_order_qty,so_order_con_qty,so_item_price,so_order_disc,so_item_unit,size,so_item_size,so_item_weight,so_item_pcs,so_item_spec')
      ->orderBy('sales_orders_direct_details.id', 'asc')->get();


      $fileName = "SO_".$id; 
      //return $rows_m->inv_net_amt;
      $so_amt = floor($rows_m->so_net_amt); 
      $inWordAmount = $this->convert_number_to_words($so_amt);

      // return view('/sales/reports/rpt_sales_order_pdf', compact('rows_m','rows_d','inWordAmount'));
      //   return $rows_d;
      $pdf = PDF::loadView('/sales/reports/rpt_sales_direct_order_pdf', 
      compact('rows_m','rows_d','inWordAmount'), [], [
        'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }
    
    
    public function so_customers()
    {
      $generalsController = new GeneralsController();
      $user_mapping = $generalsController->getUserMappingData(Auth::id());
      if($user_mapping > 0){
        $this->user_sp_mapping = true;
        $customers = Customers::query()
          ->Join("users_mapping_sps", "cust_sales_per_id", "=", "sp_ref_id")
          ->selectRaw('customers.*')
          ->where('u_user_id', Auth::id())
          ->orderBy('customers.cust_name', 'asc')->get();
      } else{
          $this->user_sp_mapping = false;
          $customers = Customers::query()->orderBy('cust_name','asc')->get();
      }
      return $customers;
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function so_create()
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $del_list  = $dropdownscontroller->deliveryList($company_code);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
      $generalsController = new GeneralsController();
      $reference_no = $generalsController->make_sales_order_ref($company_code);
      
 
      $sql = "select * from `item_categories`
      where `itm_comp_id` = $company_code and itm_cat_code like '20%' and `id` not in (select `parent_id` from `item_categories`)
      Order By itm_cat_name asc";
      $itm_cat = DB::select($sql);

      $itm_cat = DB::table('item_categories')->where('itm_comp_id', $company_code)
      ->whereIn('itm_cat_code', ['202', '201'])->get();
 
      
      $item_list =  Items::query() 
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_ref_comp_id', '=', $company_code)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
          'item_op_stock','item_bal_stock','itm_cat_name')
          ->orderBy('item_name','asc')->get();
      
      $unit_list = $dropdownscontroller->comboUnitsList($company_code);
      $order_date = date('d-m-Y');
       
      $customers = $this->so_customers(); 

      return view('/sales/so_create',compact('companies','order_date','company_code',
      'customers','itm_cat','item_list','unit_list','reference_no','del_list','courr_list','warehouse_list','stor_list'))->render();
    } 

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function direct_order_invoice()
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $del_list  = $dropdownscontroller->deliveryList($company_code);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);

      $generalsController = new GeneralsController();
      $reference_no = $generalsController->make_sales_order_ref($company_code);
      
    //   $item_list =  Items::query()
    //   ->where('item_ref_comp_id', '=', $company_code)
    //   ->orderBy('item_name','asc')->get();
      
      $item_list =  Items::query() 
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_ref_comp_id', '=', $company_code)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
          'item_op_stock','item_bal_stock','itm_cat_name')
          ->orderBy('item_name','asc')->get();
          
      $order_date = date('dd/mm/yyyy');
       
      $customers = $this->so_customers();
      return view('/sales/so_inv_create',compact('companies','order_date','company_code',
      'customers','item_list','reference_no','del_list','courr_list','warehouse_list','stor_list'))->render();
    }

    public function direct_order_invoice_edit($id)
    {
        return $this->so_edit($id,1);
    }

    public function so_edit($id,$isDirect=NULL)
    {
        $dropdownscontroller = new DropdownsController();
        $q = SalesOrders::query()
             ->join("customers", "customers.id", "=", "so_cust_id")
             ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
             ->where('sales_orders.id', $id )
             ->selectRaw('sales_orders.id,so_comp_id,so_m_warehouse_id,so_order_no,so_order_date,so_reference,so_cust_id,so_req_del_date,so_del_to,deliv_to,so_del_add,so_cont_no,
             so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,so_vat_value,so_net_amt,so_carring_cost,so_labour_cost,so_load_unload_cost,
             so_service_charge,so_other_cost,so_is_confirmed,so_courrier_to,so_courrier_cond,cust_code,cust_name, so_special_offer');
        $mas = $q->orderBy('sales_orders.id', 'desc')->first();

        $company_id = $mas->so_comp_id;
        $companies = $dropdownscontroller->comboDefaultCompanyList($company_id);
        //$companies  = $dropdownscontroller->comboCompanyAssignList();
        $customers = Customers::query()->orderBy('cust_name','asc')->get();
        $courr_list  = $dropdownscontroller->comboCourrierList($company_id);
        $warehouse_list = $dropdownscontroller->WareHouseList($company_id);
        $stor_list  = $dropdownscontroller->comboStorageList($company_id,$mas->so_m_warehouse_id);
        $item_list =  Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_ref_comp_id', '=', $company_id)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
          'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')
          ->orderBy('item_name','asc')->get();

    
        if($isDirect == 1){
          $so_del_inv_no =  SalesOrders::query()
            ->join("sales_deliveries", "sales_orders.id", "=", "del_sal_ord_id")
            ->join("sales_invoices", "sales_orders.id", "=", "inv_sale_ord_id")
            ->where('sales_orders.id', $id )
            ->select('so_order_no','del_no','inv_no')->first();
          $so_del_no = $so_del_inv_no->del_no;
          $so_inv_no = $so_del_inv_no->inv_no;
        } else{
          $so_del_no = 0;
          $so_inv_no = 0;
        }
      
        /*$det = SalesOrdersDetails::query()
             ->join("items", "items.id", "=", "so_item_id")
             ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
             ->where('so_order_id', $id )
             ->selectRaw('sales_orders_details.id,so_order_id,so_warehouse_id,so_storage_id,so_lot_no,so_item_id,
             item_code,item_bar_code,item_name,itm_cat_name,so_item_price,
             so_order_qty,so_order_con_qty,so_item_unit,so_order_disc,item_bal_stock')
             ->orderBy('sales_orders_details.id', 'desc')->get(); */

        $sql= "select sales_orders_details.*,stock as item_bal_stock,items.item_code, items.item_name, item_bar_code,item_desc,item_ref_cate_id
             from `sales_orders_details`
             left join `view_item_stocks` on `item_ref_id` = `so_item_id` and `item_warehouse_id` = so_warehouse_id
             and `item_storage_loc` = so_storage_id and `so_lot_no` = item_lot_no
             inner join `items` on `items`.`id` = `so_item_id`
             inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id`
             where `so_order_id` = ".$id." order by sales_orders_details.id asc";
       $det = DB::select($sql);

        // return $det;

      $unit_list = $dropdownscontroller->comboUnitsList($company_id);  

      // $sql = "select * from `item_categories`
      // where `itm_comp_id` = $company_id and itm_cat_code like '20%' and `id` not in (select `parent_id` from `item_categories`)
      // Order By itm_cat_name asc";
      
      // $itm_cat = DB::select($sql);

      $item_list =  Items::query() 
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $company_id)
      ->select('items.id', 'item_categories.id as cat_id', 'item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

      
      

      $itm_cat = DB::table('item_categories')->where('itm_comp_id', $company_id)
      ->whereIn('itm_cat_code', ['202', '201'])->get();
 
      // return $item_list;
      if($isDirect == 1){
        return view('/sales/so_inv_edit',compact('companies','customers','item_list','courr_list',
        'warehouse_list','stor_list','mas','det','so_del_no','so_inv_no'))->render();
      } 
      else if($isDirect == 2){
        return view('/sales/so_del_edit',compact('companies','customers','item_list','courr_list',
        'warehouse_list','stor_list','mas','det','so_del_no','so_inv_no'))->render();
      }else {
        return view('/sales/so_edit',compact('companies','customers','item_list','unit_list','itm_cat', 'courr_list','warehouse_list','stor_list','mas','det','so_del_no','so_inv_no'))->render();
      }

    } 
 

    public function conf_creation($id)
    {
       $customers = Customers::query()->orderBy('cust_name','asc')->get();
       $q = SalesOrders::query()
            ->join("customers", "customers.id", "=", "so_cust_id")
            ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
            ->where('sales_orders.id', $id )
            ->selectRaw('sales_orders.id,so_comp_id,so_order_no,so_order_date,so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,
            so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,
            so_vat_value,so_net_amt,so_is_confirmed,cust_code,cust_name');
       $rows = $q->orderBy('sales_orders.id', 'desc')->get();

       $det = SalesOrdersDetails::query()
            ->join("items", "items.id", "=", "so_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('so_order_id', $id )
            ->selectRaw('sales_orders_details.id,so_order_id,so_item_id,item_code,item_bar_code,item_name,itm_cat_name,so_order_qty,so_order_con_qty')
            ->orderBy('sales_orders_details.id', 'desc')->get();
      return view ('/sales/so_confirmation', compact('rows','customers','det'));
     }

    public function getItemCode(Request $request)
    {
        $compcode = $request->get('compcode');
        $custsid = $request->get('custsid');
        $dropdownscontroller = new DropdownsController();
        $itemcode = $dropdownscontroller->comboItemCodeList($custsid,$compcode);
        return ItemCodeResource::collection($itemcode);
    }
 
  public function get_sel_bar_item($barcode){
  //  return('ss'. $id);
    return new TransItemCodeResource(
      $itms = Items::query()
        ->join("units", "unit_id", "=", "units.id")
        ->join("customer_prices", "cust_item_p_id", "=", "items.id")
        ->where('items.item_bar_code','=',$barcode)
        ->where('customer_prices.p_del_flag','=',0)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock', 'item_bal_stock','vUnitName','cust_price','cust_comm')->first()
    );
  }

  public function get_sel_item($id, $custid){
    //  return('ss'. $id);
      $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->leftjoin("customer_prices", "cust_item_p_id", "=", "items.id")
          ->where('items.id','=',$id) 
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','cust_price','cust_comm')->first();
        
     return [
        'id'         =>  $itms->id,
        'item_code'  =>  $itms->item_code,
        'item_name'  =>  $itms->item_name,
        'item_desc'  => $itms->item_desc,
        'item_level' => $itms->item_level,
        'item_bar_code'   => $itms->item_bar_code,
        'item_op_stock'   => $itms->item_op_stock,
        'item_bal_stock'  => $itms->item_bal_stock,
        'item_unit'  => $itms->vUnitName,
        'item_price' => $itms->cust_price,
        'item_ord_disc' => $itms->item_ord_disc,
        'item_ord_qty'  => $itms->item_ord_qty,
        'item_comm_val' => $itms->cust_comm,

      ];
    }

    public function get_delivered_inf($id){
    //  return('ss'. $id);
         $generalsController = new GeneralsController();
         $delinf = $generalsController->get_delivered_inf($id);
         return response()->json($delinf);
    }

    
    public function ItemValidityCheck($request)
    {
        $message = '';
        $detId = $request->input('ItemCodeId');
        //return count($detId);
         if ($detId){
           $i = 0;
            foreach ($detId as $key => $value){
              if ($request->ItemCodeId[$key] != ''){ 
                    if ($request->Price[$key] == ''){
                      $message = 'Failed: Price Could Not empty';
                    }
                    else if ($request->Qty[$key] == '' || $request->Qty[$key] <= 0){
                      $message = 'Failed: Wrong Qty';
                    }
                }else{
                    $message = 'Item Id could not empty';
                }
            }
        }
        return $message;
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
        $inv_date     = date('Y-m-d',strtotime($request->order_date));
        $yearValidation = $generalscontroller->getFinYearValidation($company_code,$inv_date);
        if($yearValidation) {
          $finan_yearId = $generalscontroller->getFinYearId($company_code,$inv_date);
        }else{
          return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
        }
        //Item Validity Check
        $msg = $this->ItemValidityCheck($request);
        if($msg != '') {
          //  return redirect()->back()->with('message',$msg)->withInput();
            //return redirect()->route('itm.inventory.create')->with('message',$msg)->withInput();
            // return back()->with('message',$msg)->withInput();
        } 
        
        $orderId = $this->store_order($request,$finan_yearId);
        // return $this->sal_order_pdf($orderId, $request);
        return back()->with('orderId', $orderId);
    }

    public function store_to_invoiced(Request $request)
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

      $sales_order_id = $this->store_order($request, $finan_yearId);
      $inputdata  = SalesOrders::find($sales_order_id);
      $inputdata->so_direct_inv  = 1;
      $inputdata->save();
      $inv_no = $this->store_make_transaction(1, $request,$sales_order_id,$finan_yearId,0);
      return redirect()->back()->with('message','Sales Invoice Created Successfully >>> '.$inv_no);
  }

  public function store_make_transaction($isExecFinEntry,Request $request,$sales_order_id,$finan_yearId,$acc_voucher_no)
  {
      $inv_no = 0;
      $generalscontroller = new GeneralsController();
      $company_code = $request->company_code;
      $inv_date     = date('Y-m-d',strtotime($request->delivery_date));
      if($sales_order_id>0){
        $q = SalesOrders::query()
             ->where('sales_orders.id', $sales_order_id )
             ->selectRaw('id,so_comp_id,so_order_title,so_m_warehouse_id,so_order_no,so_order_date,so_reference,
             so_cust_id,so_req_del_date,so_del_to,so_del_customer,so_del_add,so_cont_no,
             so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,
             so_gross_amt,so_vat_per,so_vat_value,so_net_amt,so_is_confirmed,so_del_done,so_direct_inv,
             so_courrier_to,so_courrier_cond');
        $rows = $q->first();

        $det = SalesOrdersDetails::query()
             ->where('so_order_id', $sales_order_id)
             ->selectRaw('id,so_comp_id,so_order_id,so_warehouse_id,so_storage_id,so_lot_no,so_item_id,so_item_unit,so_item_price,so_order_comm,
             so_order_qty,so_order_con_qty,so_order_bal_qty,so_order_disc,so_order_qty_done')
             ->orderBy('id', 'asc')->get();

      // sales order confirmation entry
      foreach($det as $d){
        SalesOrdersConfirmations::create([
          'so_con_comp_id'    => $d->so_comp_id,
          'so_ref_order_id'   => $d->so_order_id,
          'so_ref_order_no'   => $rows->so_order_no,
          'so_ref_details_id' => $d->id,
          'so_conf_date'      => $inv_date,
          'so_conf_itm_id'    => $d->so_item_id,
          'so_order_qty'      => $d->so_order_qty,
          'so_order_conf_qty' => $d->so_order_qty,
        ]);
        $inputdata  = SalesOrdersDetails::find($d->id);
        $inputdata->so_order_con_qty  = $d->so_order_qty;
        $inputdata->so_order_bal_qty  = $d->so_order_qty;
        $inputdata->save();
      }
      $inputdata  = SalesOrders::find($sales_order_id);
      $inputdata->so_is_confirmed  = 1;
      $inputdata->save();
      // end sales order confirmation entry

      //Making Deliveries 
      if ($request->so_del_no == 0){
        $deliv_orderno = $generalscontroller->make_deliv_orderno($company_code,$finan_yearId);
      }else{
        $deliv_orderno = $request->so_del_no;
      } 

      $trans_del_id = SalesDeliveries::insertGetId([
        'del_comp_id'   => $rows->so_comp_id,
        'del_fin_year_id'   => $finan_yearId,
        'del_m_warehouse_id' => $rows->so_m_warehouse_id,
        'del_sal_ord_id'=> $sales_order_id,
        'del_title'   => 'DN',
        'del_no'      => $deliv_orderno,
        'del_date'    => $inv_date,
        'del_po_no'   => $rows->so_reference,
        'del_cust_id'  => $rows->so_cust_id,
        'del_req_date' => $inv_date,
        'del_to'       => $rows->so_del_to,
        'del_customer' => $rows->so_del_customer,
        'del_add'      => $rows->so_del_add,
        'del_cont_no'  => $rows->so_cont_no,
        'del_cust_ref'  => $rows->so_del_ref,
        'del_courrier_to'   => $rows->so_courrier_to,
        'del_courrier_cond' => $rows->so_courrier_cond,
        'del_comments'  => $rows->so_comments,
        'del_sub_total' => $rows->so_sub_total,
        'del_disc_per'  => $rows->so_disc_per,
        'del_disc_value'=> $rows->so_disc_value,
        'del_total_disc'=> $rows->so_total_disc,
        'del_gross_amt' => $rows->so_gross_amt,
        'del_vat_per'   => $rows->so_vat_per,
        'del_vat_value' => $rows->so_vat_value,
        'del_net_amt'   => $rows->so_net_amt,
        'del_is_invoiced' => $rows->so_direct_inv,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
      ]);
 
      //Making Delivery Details Records
      $acc_naration = '';
      foreach($det as $d){
        $trans_del_det_id = SalesDeliveryDetails::insertGetId([
            'del_det_comp_id'=> $rows->so_comp_id,
            'del_ref_id'     => $trans_del_id,
            'del_warehouse_id' => $d->so_warehouse_id,
            'del_item_id'    => $d->so_item_id,
            'del_storage_id' => $d->so_storage_id,
            'del_lot_no'     => $d->so_lot_no,
            'del_item_unit'  => $d->so_item_unit,
            'del_item_price' => $d->so_item_price,
            'del_qty'        => $d->so_order_qty,
            'del_disc'       => $d->so_order_disc,
            'del_comm'       => $d->so_order_comm,
            'created_by'      => Auth::id(),
            'updated_by'      => Auth::id(),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ]);
  
        // update sales order balance qty
        $so_id  = $sales_order_id;
        $itemId = $d->so_item_id;
        SalesOrdersDetails::where('so_order_id',$so_id)
        ->where('so_item_id',$itemId)
        ->update([ 'so_order_bal_qty' => 0 ]);

      // Closing Pending sales order
        $bal = SalesOrdersDetails::where('so_order_id',$so_id)
        ->where('so_item_id',$itemId)
        ->selectRaw('sum(so_order_bal_qty) as bal')->first()->bal;
        if($bal== 0){
          SalesOrders::where('id',$so_id)
          ->update([ 'so_del_done' => 1 ]);
        }

      // update item Stock qty
      $availableStock = $generalscontroller->get_item_details($itemId)->item_bal_stock;
      Items::where('id',$itemId)
      ->update([ 'item_bal_stock' => $availableStock - $d->so_order_qty ]);

      }

      //update financial transaction for sales 
      if($isExecFinEntry == 1){
        $inv_no = $this->financialTransaction($finan_yearId,$trans_del_id,$request->so_inv_no,$acc_voucher_no);
        return $inv_no;
      }
      else{
        return $deliv_orderno;
      } 
      
    }
    
  }

  public function financialTransaction($finan_yearId,$trans_del_id,$so_inv_no,$acc_voucher_no)
  {
    $rows  = SalesDeliveries::find($trans_del_id); 
    $company_code = $rows->del_comp_id;
    $customer_id = $rows->del_cust_id;
    $generalscontroller = new GeneralsController();
    
    //Making Invoices
    $inv_title   = 'SV'; 
    if ($so_inv_no == 0){
      $inv_no = $generalscontroller->make_invoice_no($company_code,$finan_yearId);
    }else{
      $inv_no  = $so_inv_no;
    }
    $trans_inv_id = SalesInvoices::insertGetId([
      'inv_comp_id'         => $rows->del_comp_id,
      'inv_fin_year_id'     => $finan_yearId,
      'inv_m_warehouse_id'  => $rows->del_m_warehouse_id,
      'inv_sale_ord_id'     => $rows->del_sal_ord_id,
      'inv_title'           => $inv_title,
      'inv_no'              => $inv_no,
      'inv_date'            => $rows->del_date,
      'inv_cust_id'         => $rows->del_cust_id,
      'inv_sub_total'       => $rows->del_sub_total,
      'inv_itm_disc_value'  => $rows->del_total_disc - $rows->del_disc_value,
      'inv_disc_value'      => $rows->del_total_disc,
      'inv_vat_value'       => $rows->del_vat_value,
      'inv_net_amt'         => $rows->del_net_amt,
      'created_by'      => Auth::id(),
      'updated_by'      => Auth::id(),
      'created_at'      => Carbon::now(),
      'updated_at'      => Carbon::now(),
    ]);
    
      //Making Delivery Details Records
      $det =  SalesDeliveryDetails::query()
      ->where('del_det_comp_id', '=', $company_code)
      ->where('del_ref_id', '=', $trans_del_id)->get();

      $acc_naration = '';
      foreach($det as $d){ 
        //Making Invoice Details Records
        SalesInvoiceDetails::create([
            'inv_det_comp_id'=> $d->del_det_comp_id,
            'inv_mas_id'     => $trans_inv_id,
            'inv_del_id'     => $trans_del_id,
            'inv_del_det_id' => $d->id,
            'inv_del_no'     => $rows->del_no,
            'inv_po_no'      => $rows->del_po_no,

            'inv_warehouse_id' => $d->del_warehouse_id,
            'inv_storage_id' => $d->del_storage_id,
            'inv_lot_no'     => $d->del_lot_no,
            'inv_item_id'    => $d->del_item_id,
            'inv_item_price' => $d->del_item_price,
            'inv_qty'        => $d->del_qty,
            'inv_unit'       => $d->del_item_unit,
            'inv_itm_disc_per' => $d->del_disc !=''?$d->del_disc:'0.00',
            'inv_comm'        => $d->del_comm !=''?$d->del_comm:'0.00',

            'inv_disc_per'    => $rows->del_disc_per,
            'inv_disc_value'  => $rows->del_disc_value,
            'inv_vat_per'     => $rows->del_vat_per,

            'inv_del_to'      => $rows->del_to,
            'inv_del_to_cust' => $rows->del_customer,
            'inv_del_add'     => $rows->del_add,
            'inv_del_contact' => $rows->del_cont_no,
            'inv_del_ref'     => $rows->del_cust_ref,
            'inv_del_comments' => $rows->del_comments,
            'inv_courrier_to'   => $rows->del_courrier_to,
            'inv_courrier_cond' => $rows->del_courrier_cond,
        ]);

      //make naration for accounting entry
        $itmname = $generalscontroller->get_item_details($d->del_item_id)->item_name;
        $qty    = $d->del_order_qty;
        $price  = $d->del_item_price;
        $amount = $qty * $price;
        $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$price.';Amount:'.$amount.'),<br/>';
    }
  //Financial Transaction
    if ($acc_voucher_no > 0 ){
      $voucher_no = $acc_voucher_no;
    }else{ 
      $voucher_no = $generalscontroller->getMaxAccVoucherNo('SV',$company_code,$finan_yearId); 
      // getting max Voucher No
      $voucher_no = $voucher_no + 1;
    }
     
    $cust_acc_id  = $generalscontroller->CustomerChartOfAccId($customer_id);
    $cust_name    = $generalscontroller->CustomerName($customer_id);
    $salesinvoices = new SalesInvoices();
    $records  = $salesinvoices->sal_fin_transaction($trans_inv_id);
    $recCount = $records->count();

    // Insert Transaction Master Records
    $trans_fin_id = AccTransactions::insertGetId([
      'com_ref_id'    => $company_code,
      'voucher_date'  => $rows->del_date,
      'trans_type'    => $inv_title,
      'voucher_no'    => $voucher_no,
      't_narration'   => $acc_naration,
      'fin_ref_id'    => $finan_yearId,
      'created_by'    => Auth::id(),
      'updated_by'    => Auth::id(),
      'created_at'    => Carbon::now(),
      'updated_at'    => Carbon::now(),
    ]);

    AccTransactionDetails::create([
        'acc_trans_id'    => $trans_fin_id,
        'd_amount'        => $rows->del_net_amt,
        'chart_of_acc_id' => $cust_acc_id,
        'acc_invoice_no'  => $inv_no,
    ]);

    $total_vat = 0;
    foreach ($records as $rec){
      $sub_total = $rec->sub_total;

      //$inv_disc  = $rec->inv_disc_value/$recCount;
      //$gr_total  = $sub_total-$inv_disc;
      //$net_total = $gr_total + ($gr_total*$rec->inv_vat_per)/100;

      $net_total  = $sub_total-$rec->inv_disc_value;
      $total_vat += ($sub_total - $rec->inv_disc_value)*$rec->inv_vat_per/100;
      AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'c_amount'        => $net_total,
          'chart_of_acc_id' => $rec->sett_accid,
      ]);
    }
    //VAT entry
    if($total_vat > 0) {
      AccTransactionDetails::create([
          'acc_trans_id'    => $trans_fin_id,
          'c_amount'        => $total_vat,
          'chart_of_acc_id' => 638,
      ]);
    }

    return $inv_no;

  }

  public function store_order(Request $request,$finan_yearId)
  {
        $generalsController = new GeneralsController();
        $sales_orderno = $generalsController->make_sales_orderno($request->company_code,$finan_yearId);
        $del_customer = $generalsController->get_delivered_inf($request->delivered_to);

        $get_cust_comm = $generalsController->getCustomerVAT($request->company_code,$request->customer_id);

        $trans_id = 0;


        $trans_id = SalesOrders::insertGetId([
        'so_comp_id'    => $request->company_code,
        'so_fin_year_id'  => $finan_yearId,
        'so_m_warehouse_id'  => $request->itm_warehouse,
        'so_order_title'=> 'SO',
        'so_order_no'   => $sales_orderno,
        'so_order_date' => date('Y-m-d',strtotime($request->order_date)),
        'so_reference'  => $request->reference_no,
        'so_cust_id'    => $request->customer_id,
        'so_req_del_date' => date('Y-m-d',strtotime($request->delivery_date)),
        'so_del_to'       => $request->delivered_to,
        //'so_del_customer' => $del_customer->deliv_to,
        'so_del_add'      => $request->address1,
        'so_cont_no'      => $request->contact_no,
        'so_del_ref'      => $request->cust_ref,
        'so_comments'     => $request->comments,
        'so_courrier_to'   => $request->courr_id,
        'so_courrier_cond' => $request->condition_tag,
        'so_sub_total'    => ($request->n_sub_total=='')?'0':$request->n_sub_total,
        'so_disc_per'    => ($request->n_disc_per=='')?'0':$request->n_disc_per,
        'so_disc_value'  => ($request->n_discount=='')?'0':$request->n_discount,
        'so_total_disc'  => ($request->n_total_disc=='')?'0':$request->n_total_disc,
        'so_gross_amt'   => ($request->n_total_gross=='')?'0':$request->n_total_gross,
        'so_vat_per'     => ($request->n_vat_per=='')?'0':$request->n_vat_per,
        'so_vat_value'   => ($request->n_total_vat=='')?'0':$request->n_total_vat,
        'so_special_offer' => $request->special_offer,

        'so_carring_cost' => ($request->carring_cost=='')?'0':$request->carring_cost,
        'so_labour_cost'  => ($request->labour_cost=='')?'0':$request->labour_cost,
        'so_load_unload_cost' => ($request->load_unload_cost=='')?'0':$request->load_unload_cost,
        'so_service_charge'   => ($request->service_charge=='')?'0':$request->service_charge,
        'so_other_cost'   => ($request->other_cost=='')?'0':$request->other_cost, 

        'so_net_amt'     => ($request->t_n_net_amount=='')?'0':$request->t_n_net_amount,
        'remark'        => $request->remark,
        //'so_is_confirmed' => 1,
        'created_by'      => Auth::id(),
        'updated_by'      => Auth::id(),
        'created_at'      => Carbon::now(),
        'updated_at'      => Carbon::now(),
        ]);

        //Details Records
        $detId = $request->input('ItemCodeId');
        if ($detId){
            foreach ($detId as $key => $value){
              
              if ($request->Qty[$key] > 0){
                
                //  define the commision
                $comm = $get_cust_comm->cust_overall_comm;
                if($comm <= 0 ){
                    $get_itm_comm = $generalsController->get_sel_item1($request->ItemCodeId[$key],$request->customer_id);
                    $comm = 0; //$get_itm_comm->cust_comm;
                    //echo 'BB'.$comm;
                }

                $item = Items::latest()->first();

                $newitem = Items::where('item_name', $request->newItemName[$key])->first();
                if ($newitem == NULL) {
                  $newitem = Items::create([
                    'item_ref_comp_id' => 1, 
                    'item_ref_cate_id' => $request->catid[$key],
                    'item_code' => $item->item_code + 1,
                    'item_name' => $request->newItemName[$key],
                    'unit_id' => ($request->Unit[$key] == 'KG') ? 1 : 2
                  ]);
                }

                SalesOrdersDetails::create([
                    'so_comp_id'    => $request->company_code,
                    'so_order_id'   => $trans_id,
                    'so_warehouse_id' => $request->itm_warehouse,
                    'so_storage_id' => 1,
                    'so_lot_no'     => 101, //$request->lotno[$key],
                    'so_item_id'    => $newitem->id,
                    // 'so_item_spec'  => $request->ItemDesc[$key],
                    'so_item_unit'  => $request->Unit[$key],
                    'so_item_price' => $request->Price[$key],
                    'so_item_cat_id' => $request->itmcategory[$key],
                    'so_item_cat_2nd_id' => $request->catid[$key],
                    'so_item_size'  => $request->Size[$key],
                    'so_item_weight' => $request->QWeight[$key],
                    'so_item_pcs'   => $request->PCS[$key], 
                    'so_order_qty'  => $request->Qty[$key]==''?'0':$request->Qty[$key],
                    'so_order_disc' => $request->Discp[$key]==''?'0':$request->Discp[$key],
                    'so_order_comm' => $comm,
                ]);
              }
            }
        }
        return $trans_id;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function so_update(Request $request)
    {   
      // return $request;
       // Validate the Field
         $this->validate($request,[]);
         //Delete Sales Order Details Records
         $sales_order_id = $request->so_id;
        //  SalesOrdersConfirmations::where('so_ref_order_id', $sales_order_id)->delete();

         SalesOrdersDetails::where('so_order_id',$sales_order_id)->delete();
         $sale = $this->store_order_update($request);
         return back()->with('orderId', $sale->id);
        //  return redirect()->route('sales.order.edit')->with('message','New Sales Order Created Successfull !');
         return redirect()->route('sales.order.index')->with('message','New Sales Order Update Successfull !');
    }

    public function so_update_to_invoiced(Request $request)
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
      $sales_order_id = $request->so_id;
      //delete financial transaction
      $inv_no  = $generalscontroller->getVoucherNoByOrderNo($sales_order_id);
     // return $inv_no;
      $acc_trans_id = $generalscontroller->getIdByVoucherNo($inv_no,$finan_yearId);
      $acc_voucher_no = $generalscontroller->getAccVoucherNoByAccTransId($acc_trans_id);
      if($acc_trans_id>0) {
        AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
        AccTransactions::where('id',$acc_trans_id)->delete();
      } 
      //delete Sales Invoice transaction
      $inv_id = $generalscontroller->getInvoiceIdByOrderNo($sales_order_id);
      SalesInvoiceDetails::where('inv_mas_id',$inv_id)->delete();
      SalesInvoices::where('id',$inv_id)->delete();
      //delete Delivery transaction
      $del_id = $generalscontroller->getDeliveryIdByOrderNo($sales_order_id);
      SalesDeliveryDetails::where('del_ref_id',$del_id)->delete();
      SalesDeliveries::where('id',$del_id)->delete();
      //delete Order Confirmation transaction
      SalesOrdersConfirmations::where('so_ref_order_id',$sales_order_id)->delete();
      //Delete Sales Order Details Records
      SalesOrdersDetails::where('so_order_id',$sales_order_id)->delete();

      $this->store_order_update($request);
      $inputdata  = SalesOrders::find($sales_order_id);
      $inputdata->so_direct_inv  = 1;
      $inputdata->save();
      $inv_no = $this->store_make_transaction(1,$request,$sales_order_id,$finan_yearId,$acc_voucher_no);
      return redirect()->back()->with('message','Sales Invoice Updated Successfully >>> '.$inv_no);
    }

    public function store_order_update(Request $request)
    {
      // Validate the Field
        $this->validate($request,[]);
        $id = $request->so_id;
        $generalsController = new GeneralsController();
        $del_customer = $generalsController->get_delivered_inf($request->delivered_to);
        $get_cust_comm = $generalsController->getCustomerVAT($request->company_code,$request->customer_id);

        $inputdata  = SalesOrders::find($id);
        $inputdata->so_order_date = date('Y-m-d',strtotime($request->order_date));
        $inputdata->so_m_warehouse_id  = $request->itm_warehouse;
        $inputdata->so_reference  = $request->reference_no;
        $inputdata->so_cust_id    = $request->customer_id;
        $inputdata->so_req_del_date = date('Y-m-d',strtotime($request->delivery_date));
        $inputdata->so_del_to       = $request->delivered_to;
       // $inputdata->so_del_customer = $del_customer->deliv_to;
        $inputdata->so_del_add      = $request->address1;
        $inputdata->so_cont_no      = $request->contact_no;
        $inputdata->so_del_ref      = $request->cust_ref;
        $inputdata->so_courrier_to    = $request->courr_id;
        $inputdata->so_courrier_cond  = $request->condition_tag;
        $inputdata->so_comments     = $request->comments;
        $inputdata->so_sub_total    = $request->n_sub_total;
        $inputdata->so_disc_per    = $request->n_disc_per;
        $inputdata->so_disc_value  = $request->n_discount;
        $inputdata->so_total_disc  = $request->n_total_disc;
        $inputdata->so_gross_amt   = $request->n_total_gross;
        $inputdata->so_vat_per     = $request->n_vat_per;
        $inputdata->so_vat_value   = $request->n_total_vat;
        $inputdata->so_special_offer = $request->special_offer;

        $inputdata->so_carring_cost  = $request->carring_cost;
        $inputdata->so_labour_cost  = $request->labour_cost;
        $inputdata->so_load_unload_cost   = $request->load_unload_cost;
        $inputdata->so_service_charge     = $request->service_charge;
        $inputdata->so_other_cost   = $request->other_cost;
  
        $inputdata->so_net_amt     = $request->t_n_net_amount;
        //  $inputdata->so_is_confirmed = 1;
        $inputdata->save();

        $detId = $request->input('ItemCodeId');
        if ($detId){
            foreach ($detId as $key => $value){
              if ($request->Qty[$key] > 0){
                //define the commision
                $comm = $get_cust_comm->cust_overall_comm;
                if($comm <= 0 ){
                  $get_itm_comm = $generalsController->get_sel_item1($request->ItemCodeId[$key],$request->customer_id);
                  $comm = 0; //$get_itm_comm->cust_comm;
                  //echo 'BB'.$comm;
               }

               $item = Items::where('item_name', $request->newItemName[$key])->first();


               SalesOrdersDetails::create([
                 'so_comp_id'    => $request->company_code,
                 'so_order_id'   => $id,
                 'so_warehouse_id' => $request->itm_warehouse,
                 'so_storage_id' => $request->Storage[$key],
                 'so_lot_no'     => 101, //$request->lotno[$key],
                 'so_item_id'    => $item->id,
                 'so_item_cat_id'=> $request->itmcategory[$key],
                 'so_item_cat_2nd_id'=> $request->catid[$key],
                //  'so_item_spec'  => $request->ItemDesc[$key],
                 'so_item_unit'  => $request->Unit[$key],
                 'so_item_price' => $request->Price[$key],
                 'so_item_size'  => $request->Size[$key],
                 'so_item_weight' => $request->QWeight[$key],
                 'so_item_pcs'   => $request->PCS[$key], 
                 'so_order_qty'  => $request->Qty[$key],
                 'so_order_disc' => $request->Discp[$key],
                 'so_order_comm' => $comm,
                ]);
              }
            }
        }

        return $inputdata;
    }


    public function so_confirmed1($id)
    {
     
      $q = SalesOrders::query()
      ->where('sales_orders.id', $id )
      ->selectRaw('id,so_order_no,so_order_date');
      $rows = $q->first();

      $det = SalesOrdersDetails::query()
      ->where('so_order_id', $id)
      ->selectRaw('id,so_comp_id,so_order_id,so_warehouse_id,so_storage_id,so_lot_no,so_item_id,so_item_unit,so_item_price,so_order_comm,so_order_qty,so_order_con_qty,so_order_bal_qty,so_order_disc,so_order_qty_done')
      ->orderBy('id', 'asc')->get();

      // sales order confirmation entry
      foreach($det as $d){
        SalesOrdersConfirmations::create([
          'so_con_comp_id'    => $d->so_comp_id,
          'so_ref_order_id'   => $d->so_order_id,
          'so_ref_order_no'   => $rows->so_order_no,
          'so_ref_details_id' => $d->id,
          'so_conf_date'      => $rows->so_order_date,
          'so_conf_itm_id'    => $d->so_item_id,
          'so_order_qty'      => $d->so_order_qty,
          'so_order_conf_qty' => $d->so_order_qty,
          
        ]);
        // $inputdata  = SalesOrdersDetails::find($d->id);
        // $inputdata->so_order_con_qty  = $d->so_order_qty;
        // $inputdata->so_order_bal_qty  = $d->so_order_qty;
        // $inputdata->save();
      }
      $lastSaleOrder = SalesOrders::where('so_is_confirmed', 1)->orderBy('id', 'DESC')->first();

      $inputdata  = SalesOrders::find($id);
      $inputdata->so_is_confirmed  = 1;
      $inputdata->so_confirmed_date  = date('d/m/Y');
      $inputdata->so_fpo_no = $lastSaleOrder->so_fpo_no + 1;
      $inputdata->save();
      return back()->with('message','SO Confirmation is Done')->withInput(); 
    }

    public function so_confirmed(Request $request)
    {
      $detId = $request->input('so_order_det_id');
      if ($detId){
          foreach ($detId as $key => $value){
            if ($request->ConfirmQty[$key] > 0){
              SalesOrdersConfirmations::create([
                  'so_con_comp_id'    => $request->comp_id[$key],
                  'so_ref_order_id'   => $request->so_order_id[$key],
                  'so_ref_order_no'   => $request->orderno[$key],
                  'so_ref_details_id' => $request->so_order_det_id[$key],
                  'so_conf_date'      => date('Y-m-d'),
                  'so_conf_itm_id'    => $request->so_item_id[$key],
                  'so_order_qty'      => $request->ActualQty[$key],
                  'so_order_conf_qty' => $request->ConfirmQty[$key],
              ]);

            //   $inputdata  = SalesOrdersDetails::find($request->so_order_det_id[$key]);
            //   $inputdata->so_order_con_qty  = $request->ConfirmQty[$key];
            //   $inputdata->so_order_bal_qty  = $request->ConfirmQty[$key];
            //   $inputdata->save();
            }
          }
          $inputdata  = SalesOrders::find($request->so_order_id[$key]);
          $inputdata->so_is_confirmed  = 1;
          $inputdata->save();
      }
      return back()->withInput();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id,$isconfirmed)
    {
        try{
          $sales_order_id = $id;
          //return $isconfirmed;
          $generalscontroller = new GeneralsController();
          if ($isconfirmed == 1 ){
            //delete financial transaction
            $inv_no  = $generalscontroller->getVoucherNoByOrderNo($sales_order_id);
            //return $inv_no;
            $finan_yearId  = $generalscontroller->getFinanYearByOrderNo($sales_order_id);
            
            $acc_trans_id = $generalscontroller->getIdByVoucherNo($inv_no,$finan_yearId);

            if($acc_trans_id>0) {
                AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
                AccTransactions::where('id',$acc_trans_id)->delete();
            }
            //delete Sales Invoice transaction
            $inv_id = $generalscontroller->getInvoiceIdByOrderNo($sales_order_id);
            SalesInvoiceDetails::where('inv_mas_id',$inv_id)->delete();
            SalesInvoices::where('id',$inv_id)->delete();
            //delete Delivery transaction
            $del_id = $generalscontroller->getDeliveryIdByOrderNo($sales_order_id);
            SalesDeliveryDetails::where('del_ref_id',$del_id)->delete();
            SalesDeliveries::where('id',$del_id)->delete();
            //delete Order Confirmation transaction
            SalesOrdersConfirmations::where('so_ref_order_id',$sales_order_id)->delete();
          }

          //Delete Sales Order Details Records
          SalesOrdersDetails::where('so_order_id',$sales_order_id)->delete();
          SalesOrders::where('id',$sales_order_id)->delete();

        }catch (\Exception $e){
            return redirect()->back()->with('message',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }


     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function direct_order_delivery()
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $del_list  = $dropdownscontroller->deliveryList($company_code);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);

      $generalsController = new GeneralsController();
      $reference_no = $generalsController->make_sales_order_ref($company_code);
      
    //   $item_list =  Items::query()
    //   ->where('item_ref_comp_id', '=', $company_code)
    //   ->orderBy('item_name','asc')->get();
      
      $item_list =  Items::query() 
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('item_ref_comp_id', '=', $company_code)
          ->select('items.id','item_code','item_name','item_desc','item_bar_code',
          'item_op_stock','item_bal_stock','itm_cat_name')
          ->orderBy('item_name','asc')->get();
          
      $order_date = date('dd/mm/yyyy');
       
      $customers = $this->so_customers();
      return view('/sales/so_del_create',compact('companies','order_date','company_code',
      'customers','item_list','reference_no','del_list','courr_list','warehouse_list','stor_list'))->render();
    }

    public function store_to_delivery(Request $request)
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

      $sales_order_id = $this->store_order($request, $finan_yearId);
      $inputdata  = SalesOrders::find($sales_order_id);
      $inputdata->so_direct_inv  = 0;
      $inputdata->save();
      $del_no = $this->store_make_transaction(2, $request,$sales_order_id,$finan_yearId,0);
      return redirect()->back()->with('message','Sales Delivery Created Successfully >>> '.$del_no);
  }

  public function direct_order_delivery_edit($id)
  {
    return $this->so_edit($id,2);
  }

  public function so_update_to_delivery(Request $request)
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
      $sales_order_id = $request->so_id;

     /* //delete financial transaction
      $inv_no  = $generalscontroller->getVoucherNoByOrderNo($sales_order_id);
     // return $inv_no;
      $acc_trans_id = $generalscontroller->getIdByVoucherNo($inv_no,$finan_yearId);
      $acc_voucher_no = $generalscontroller->getAccVoucherNoByAccTransId($acc_trans_id);
      if($acc_trans_id>0) {
        AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
        AccTransactions::where('id',$acc_trans_id)->delete();
      }  /

      //delete Sales Invoice transaction
      $inv_id = $generalscontroller->getInvoiceIdByOrderNo($sales_order_id);
      SalesInvoiceDetails::where('inv_mas_id',$inv_id)->delete();
      SalesInvoices::where('id',$inv_id)->delete(); */

      //delete Delivery transaction
      $del_id = $generalscontroller->getDeliveryIdByOrderNo($sales_order_id);
      SalesDeliveryDetails::where('del_ref_id',$del_id)->delete();
      SalesDeliveries::where('id',$del_id)->delete();
      //delete Order Confirmation transaction
      SalesOrdersConfirmations::where('so_ref_order_id',$sales_order_id)->delete();
      //Delete Sales Order Details Records
      SalesOrdersDetails::where('so_order_id',$sales_order_id)->delete();

      $this->store_order_update($request);
      $inputdata  = SalesOrders::find($sales_order_id);
      $inputdata->so_direct_inv  = 0;
      $inputdata->save();
      $del_no = $this->store_make_transaction(2,$request,$sales_order_id,$finan_yearId,0);
      return redirect()->back()->with('message','Sales Delivery Updated Successfully >>> '.$del_no);
    }

    public function sal_order_pdf($id, Request $request)
    { 
      error_reporting(0); 
      if(isset($request->remark)){
        $uSalesOrders = SalesOrders::find($id);
        $uSalesOrders->remark = $request->remark;
        $uSalesOrders->save();
      }else{
        $uSalesOrders = SalesOrders::find($id);
        $uSalesOrders->remark = '';
        $uSalesOrders->save();
      }

      $q = SalesOrders::query()
      ->join("customers", "customers.id", "=", "so_cust_id")
      ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
      ->where('sales_orders.id', $id )
      ->selectRaw('sales_orders.id,so_comp_id,so_order_no,so_order_date,so_req_del_date,so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,so_vat_value,so_net_amt,so_carring_cost,so_labour_cost,so_load_unload_cost,so_service_charge,so_other_cost,so_is_confirmed,remark,cust_code,cust_name,cust_add1,cust_add2,cust_mobile, cust_slno, personalMobileno, so_special_offer'); 
      $rows_m = $q->orderBy('sales_orders.id', 'desc')->first();
      
      //   return $rows_m;

      $rows_d = SalesOrdersDetails::query()
      ->join("items", "items.id", "=", "so_item_id")
      ->join("item_categories", "item_categories.id", "=", "sales_orders_details.so_item_cat_id")
      ->leftjoin("item_categories as two", "two.id", "=", "sales_orders_details.so_item_cat_2nd_id")
      ->where('so_order_id', $id )
      ->selectRaw('sales_orders_details.id,so_order_id,so_item_id,item_code,item_bar_code,item_name,item_categories.itm_cat_name, two.itm_cat_name as nd_name, so_order_qty,so_order_con_qty,so_item_price,so_order_disc,so_item_unit,size,so_item_size,so_item_weight,so_item_pcs,so_item_spec')
      ->orderBy('sales_orders_details.id', 'asc')->get();
      // return $rows_d;
      $fileName = "SO_".$id; 
      //return $rows_m->inv_net_amt;
      $so_amt = floor($rows_m->so_net_amt); 
      $inWordAmount = $this->convert_number_to_words($so_amt);

      // return view('/sales/reports/rpt_sales_order_pdf', compact('rows_m','rows_d','inWordAmount'));
      //   return $rows_d;
      $pdf = PDF::loadView('/sales/reports/rpt_sales_order_pdf', 
      compact('rows_m','rows_d','inWordAmount'), [], [
        'title' => $fileName,
      ]);
      
      // return $rows_d;
      return $pdf->stream($fileName,'.pdf');
    }
    
    public function sal_order_prod_pdf($id,$tag)
    {
      $q = SalesOrders::query()
      ->join("customers", "customers.id", "=", "so_cust_id")
      ->leftjoin("customer_delivery_infs", "customer_delivery_infs.id", "=", "so_del_to")
      ->where('sales_orders.id', $id ) 
      ->selectRaw('sales_orders.id,so_comp_id,so_order_no,so_order_date,so_req_del_date,so_reference,so_cust_id,deliv_to,so_del_add,so_cont_no,so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per,so_vat_value,so_net_amt,so_carring_cost,so_labour_cost,so_load_unload_cost,so_service_charge,so_other_cost,so_is_confirmed,so_confirmed_date,so_fpo_no,cust_code,cust_name,cust_add1,cust_add2,cust_mobile,cust_slno'); 
      $rows_m = $q->orderBy('sales_orders.id', 'desc')->first();

      $d = SalesOrdersDetails::query()
      ->join("items", "items.id", "=", "so_item_id")
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('so_order_id', $id )
      ->selectRaw('sales_orders_details.id,so_order_id,so_item_id,item_code,item_bar_code,item_name,itm_cat_name,so_order_qty,so_order_con_qty,so_item_price,so_order_disc,so_item_unit,size,so_item_size,so_item_weight,so_item_pcs,so_item_spec');
     
      if($tag == 'J') $d->whereRaw("itm_cat_code like '201%'");
      else if($tag == 'PP') $d->whereRaw("itm_cat_code like '202%'");

      $rows_d = $d->orderBy('sales_orders_details.id', 'desc')->get();

      $fileName = "SO_".$id;

      $so_amt = floor($rows_m->so_net_amt);
      $inWordAmount = $this->convert_number_to_words($so_amt);
      $pdf = PDF::loadView('/productions/reports/rpt_sales_order_prod_pdf',
      compact('rows_m','rows_d','inWordAmount',), [], [
        'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
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
