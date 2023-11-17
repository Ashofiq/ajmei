<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Customers\Customers;
use App\Models\Sales\SalesOrders;
use App\Models\Sales\SalesOrdersDetails;
use App\Models\Sales\SalesOrdersConfirmations;
use App\Models\Sales\SalesOrdersProdConfirmHistory;
use App\Models\Sales\SalesOrdersProdConfirmations;
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

class SalesProductionController extends Controller
{
    public $user_sp_mapping = false;
      
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
   public function so_prod_index(Request $request)
   {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
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
      
      $customers = $this->so_customers();
      
      $q = SalesOrders::query()
           ->join("customers", "customers.id", "=", "so_cust_id")
           ->join("sales_orders_details", "sales_orders.id", "=", "so_order_id")
           ->join("items", "items.id", "=", "so_item_id")
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->leftjoin("view_item_stocks", "item_ref_id", "=", "so_item_id") 
           ->where('sales_orders.so_comp_id', $company_code)
           ->where('so_is_confirmed', 1)
           ->whereRaw("so_item_pcs > so_order_con_qty")
           ->selectRaw('sales_orders.id,sales_orders.so_comp_id,so_order_no,so_order_date,so_req_del_date, so_reference,so_cust_id,so_cont_no,so_del_ref,so_comments,so_sub_total,
           so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per, so_vat_value,so_net_amt,cust_code,so_is_confirmed,so_is_production,cust_name,so_is_locked,so_del_done,
           so_direct_inv,so_item_id,so_item_spec,so_item_size, so_item_weight, so_item_pcs,so_item_unit,so_order_qty,item_code,item_bar_code,item_name,itm_cat_name,stock, so_fpo_no');
      if($request->filled('order_no')){
        $q->where('so_order_no', $request->get('order_no'));
      }
      if($request->filled('customer_id')){
        $q->where('so_cust_id', $request->get('customer_id'));
      }
      
      if($request->filled('fromdate')){
        $formDate = date('Y-m-d', strtotime($request->fromdate)) ;
        $toDate = date('Y-m-d',strtotime($request->todate));

        $q->whereBetween('so_order_date', [$formDate, $toDate]);
      }
      
      
      $rows = $q->orderBy('sales_orders.id', 'desc')->get();

     
      // $rows->appends(array(
      //   'order_no'   => $request->get('order_no'),
      //   'customer_id' => $request->get('customer_id'),
      // ));
      
      
      return view ('/productions/so_prod_index', compact('rows','customers', 'fromdate', 'todate'));
    }
     
  public function so_pp_prod_index(Request $request)
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
      
      $customers = $this->so_customers();
      
    $q = SalesOrders::query()
      ->join("customers", "customers.id", "=", "so_cust_id")
      ->join("sales_orders_details", "sales_orders.id", "=", "so_order_id")
      ->join("items", "items.id", "=", "so_item_id")
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->leftjoin("view_item_stocks", "item_ref_id", "=", "so_item_id") 
      ->where('sales_orders.so_comp_id', $company_code) 
      ->where('so_is_confirmed', 1) 
      ->whereRaw("itm_cat_code like '202%'")
      ->whereRaw("so_item_pcs > so_order_con_qty")
      ->selectRaw('sales_orders.id,sales_orders_details.id as so_order_det_id, sales_orders.so_comp_id,so_order_no,so_order_date,so_req_del_date, so_reference,so_cust_id,so_cont_no,so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per, so_vat_value,so_net_amt,cust_code,so_is_confirmed,so_is_production,cust_name,so_is_locked,so_del_done,so_direct_inv,so_item_id,so_item_spec,so_item_size, so_item_weight, so_item_pcs,so_item_unit,so_order_qty,so_order_con_qty,so_order_bal_qty,item_code,item_bar_code,item_name,itm_cat_name,stock, so_fpo_no, production_agree');
      if($request->filled('order_no')){
        $q->where('so_order_no', $request->get('order_no'));
      }
      if($request->filled('customer_id')){
        $q->where('so_cust_id', $request->get('customer_id'));
      }
      
      if($request->filled('fromdate')){
        $formDate = date('Y-m-d', strtotime($request->fromdate)) ;
        $toDate = date('Y-m-d',strtotime($request->todate));
        
        $q->whereBetween('so_order_date', [$formDate, $toDate]); 
      }
    
      $rows = $q->orderBy('sales_orders.id', 'desc')->get(); 
      // return $rows;
      return view ('/productions/so_pp_prod_index', compact('rows','customers', 'fromdate', 'todate'));
  }

  public function so_jute_prod_index(Request $request)
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
      ->join("sales_orders_details", "sales_orders.id", "=", "so_order_id")
      ->join("items", "items.id", "=", "so_item_id")
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->leftjoin("view_item_stocks", "item_ref_id", "=", "so_item_id") 
      ->where('sales_orders.so_comp_id', $company_code) 
      ->where('so_is_confirmed', 1) 
      ->whereRaw("itm_cat_code like '201%'")
      ->whereRaw("so_item_pcs > so_order_con_qty")
      ->selectRaw('sales_orders.id,sales_orders_details.id as so_order_det_id, sales_orders.so_comp_id,so_order_no,so_order_date,so_req_del_date, so_reference,so_cust_id,so_cont_no,so_del_ref,so_comments,so_sub_total,so_disc_per,so_disc_value,so_total_disc,so_gross_amt,so_vat_per, so_vat_value,so_net_amt,cust_code,so_is_confirmed,so_is_production,cust_name,so_is_locked,so_del_done,so_direct_inv,so_item_id,so_item_spec,so_item_size, so_item_weight, so_item_pcs,so_item_unit,so_order_qty,so_order_con_qty,so_order_bal_qty,item_code,item_bar_code,item_name,itm_cat_name,stock, production_agree');
      if($request->filled('order_no')){
        $q->where('so_order_no', $request->get('order_no'));
      }
      if($request->filled('customer_id')){
        $q->where('so_cust_id', $request->get('customer_id'));
      }
      
    if($request->filled('fromdate')){
        $formDate = date('Y-m-d', strtotime($request->fromdate)) ;
        $toDate = date('Y-m-d',strtotime($request->todate));

        $q->whereBetween('so_order_date', [$formDate, $toDate]);
      }
    
      $rows = $q->orderBy('sales_orders.id', 'desc')->get();
      
      return view ('/productions/so_jute_prod_index', compact('rows','customers', 'fromdate', 'todate'));
  }

  public function  so_jute_prod_agree($id){
    $sales_order = SalesOrders::find($id);
    $sales_order->production_agree = 1;
    if ($sales_order->save()) {
      return back()->with('message', 'Production agree ');
    }
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
  
  public function so_confirmed(Request $request)
    {  
      // return $request;
      $detId = $request->input('so_order_det_id');
      if ($detId){
          foreach ($detId as $key => $value){
            if ($request->ConfirmQty[$key] > 0){
              SalesOrdersProdConfirmations::create([
                  'so_pd_con_comp_id'    => $request->comp_id[$key],
                  'so_pd_ref_order_id'   => $request->so_order_id[$key],
                  'so_pd_ref_order_no'   => $request->orderno[$key],
                  'so_pd_ref_details_id' => $request->so_order_det_id[$key],
                  'so_pd_conf_date'      => date('Y-m-d'),
                  'so_pd_conf_itm_id'    => $request->so_item_id[$key],
                  'so_pd_order_qty'      => $request->ActualQty[$key],
                  'so_pd_order_conf_qty' => $request->ConfirmQty[$key], 
                  'so_pd_order_conf_weight' => $request->ConfirmWeight[$key], 
                  'created_by'  => Auth::id(),
                  'updated_by'  => Auth::id(),
                  'created_at'  => Carbon::now(),
                  'updated_at'  => Carbon::now(),
              ]);

              SalesOrdersProdConfirmHistory::create([
                'so_comp_id'    => $request->comp_id[$key],
                'so_ref_order_id'   => $request->so_order_id[$key],
                'so_ref_order_no'   => $request->orderno[$key],
                'so_ref_details_id' => $request->so_order_det_id[$key],
                'so_item_unit' => SalesOrdersDetails::where('id',$request->so_order_det_id[$key])->first()->so_item_unit,
                'date'                  => date('Y-m-d'),
                'so_itm_id'    => $request->so_item_id[$key],
                'so_order_qty'      => $request->ActualQty[$key],
                'so_conf_qty' => $request->ConfirmQty[$key], 
                'so_conf_weight' => $request->ConfirmWeight[$key], 
              ]);
              



              $bal = SalesOrdersDetails::where('id',$request->so_order_det_id[$key]) 
              ->selectRaw('sum(so_order_bal_qty) as bal')->first()->bal;

              DB::update("update sales_orders_details set so_order_con_qty = so_order_con_qty + ". $request->ConfirmQty[$key]. " where id = ?", [$request->so_order_det_id[$key]]);

              DB::update("update sales_orders_details set so_order_bal_qty = so_order_bal_qty + ". $request->ConfirmQty[$key]. " where id = ?", [$request->so_order_det_id[$key]]);

              DB::update("update sales_orders_details set so_pd_order_conf_weight = so_pd_order_conf_weight + ". $request->ConfirmWeight[$key]. " where id = ?", [$request->so_order_det_id[$key]]);

              $inputdata  = SalesOrders::find($request->so_order_id[$key]);
              $inputdata->so_is_production  = 1;
              $inputdata->so_is_production_dt  = Carbon::now();
              $inputdata->save();

              // $inputdata  = SalesOrdersDetails::find($request->so_order_det_id[$key]);
              // $inputdata->so_order_con_qty = so_order_con_qty + $request->ConfirmQty[$key];
              // $inputdata->so_order_bal_qty = so_order_bal_qty + $request->ConfirmQty[$key];
              // $inputdata->save(); 
            }
          } 
      }
      return back()->with('message','Production confirmation is Done')->withInput(); 

    }

  public function so_prod_confirmed($id)
  { 
    $inputdata  = SalesOrders::find($id);
    $inputdata->so_is_production  = 1;
    $inputdata->so_is_production_dt  = Carbon::now();
    $inputdata->save();
    return back()->with('message','Production Ok is Done')->withInput(); 
  }


  public function so_prod_report_daily(Request $request)
  {   
    $fromdate=  date('Y-m-d');
    $todate=  date('Y-m-d');
    $customer_id = $request->customer_id;
    $customer_sql = '';
    $data = '';
    if($request->filled('fromdate') && $request->filled('todate')){
      $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
      $todate=  date('Y-m-d',strtotime($request->input('todate')));
      $data = 'and so_order_date BETWEEN '.$fromdate.' and '.$todate.'';
    }else{
      $data = 'and so_order_date BETWEEN '.$fromdate.' and '.$todate.'';
    }

    if($request->filled('customer_id') ){
      $customer_sql = 'and sales_orders.so_cust_id = '. $request->customer_id;
    }


    // $sql = "SELECT * FROM view_daily_production_report";
    $sql = "SELECT
    `sales_orders_details`.`so_item_id` AS `itemId`,
    `sales_orders`.`so_fpo_no` AS `fpo`,
    `sales_orders`.`so_order_no` AS `order_no`,
    `sales_orders`.`so_confirmed_date` AS `fpoDate`,
    `customers`.`cust_name` AS `party`,
    `sales_orders_details`.`so_item_size` AS `size`,
    `sales_orders_details`.`so_item_spec` AS `spec`,
    `sales_orders_details`.`so_item_weight` AS `perPcsWeight`,
    `sales_orders_details`.`so_item_pcs` AS `orderPcs`,
    `sales_orders_details`.`so_order_qty` AS `orderKg`,
    (
    SELECT
        SUM(
            `sales_orders_prod_confirm_histories`.`so_conf_weight`
        ) AS `so_conf_qty`
    FROM
        `sales_orders_prod_confirm_histories`
    WHERE
        CURDATE() > `sales_orders_prod_confirm_histories`.`date` 
        AND `sales_orders_prod_confirm_histories`.`so_itm_id` = `sales_orders_details`.`so_item_id` 
        AND `sales_orders_prod_confirm_histories`.`so_ref_order_id` = `sales_orders`.`id`) AS `prevKg`,
        (
        SELECT
            SUM(
                `sales_orders_prod_confirm_histories`.`so_conf_qty`
            ) AS `so_conf_qty`
        FROM
            `sales_orders_prod_confirm_histories`
        WHERE
            CURDATE() > `sales_orders_prod_confirm_histories`.`date` 
            AND `sales_orders_prod_confirm_histories`.`so_itm_id` = `sales_orders_details`.`so_item_id` 
            AND `sales_orders_prod_confirm_histories`.`so_ref_order_id` = `sales_orders`.`id` 
            AND `sales_orders_prod_confirm_histories`.`so_item_unit` = 'Pcs.') AS `prevPcs`,
            (
            SELECT
                SUM(
                    `sales_orders_prod_confirm_histories`.`so_conf_weight`
                ) AS `so_conf_qty`
            FROM
                `sales_orders_prod_confirm_histories`
            WHERE
                CURDATE() = `sales_orders_prod_confirm_histories`.`date` 
                AND `sales_orders_prod_confirm_histories`.`so_itm_id` = `sales_orders_details`.`so_item_id` 
                AND `sales_orders_prod_confirm_histories`.`so_ref_order_id` = `sales_orders`.`id`) 
                AS `todayProdKg`,
                (
                SELECT
                    SUM(
                        `sales_orders_prod_confirm_histories`.`so_conf_qty`
                    ) AS `so_conf_qty`
                FROM
                    `sales_orders_prod_confirm_histories`
                WHERE
                    CURDATE() = `sales_orders_prod_confirm_histories`.`date` 
                    AND `sales_orders_prod_confirm_histories`.`so_itm_id` = `sales_orders_details`.`so_item_id` 
                    AND `sales_orders_prod_confirm_histories`.`so_ref_order_id` = `sales_orders`.`id`) 
                    AS `todayProdPcs`
                FROM `sales_orders_details`
                  JOIN `sales_orders` ON (`sales_orders`.`id` = `sales_orders_details`.`so_order_id`)
                  JOIN `customers` ON (`customers`.`id` = `sales_orders`.`so_cust_id`)
                WHERE
                    `sales_orders`.`so_is_production` = 1 $customer_sql 
                    and so_order_date BETWEEN '".$fromdate."' and '".$todate."' ";

    $rows = DB::select($sql);
    // return $rows;
    $customers = $this->so_customers();
    return view ('/productions/reports/daily_production_report', compact('rows', 'customer_id', 'customers', 'fromdate', 'todate' ));
  }

  public function sd_prod_report_daily(Request $request)
  {
    // $sql = "SELECT * FROM view_daily_production_report";
    // $sql = "SELECT so_item_weight, so_item_pcs, sales_orders.id as so_ref_order_id,
    // so_order_no as so_ref_order_no, sales_orders_details.id as so_ref_details_id,
    // sales_orders_details.so_item_unit as so_item_unit, sales_orders.so_order_date as date,
    // sales_orders_details.so_item_id as so_itm_id, sales_orders_details.so_order_qty as so_order_qty,
    // sales_orders_details.so_order_qty as so_conf_qty, sales_orders_details.so_item_weight as so_conf_weight
    // from sales_orders 
    // JOIN sales_orders_details ON sales_orders_details.so_order_id = sales_orders.id";

    // $rows = DB::select($sql);

    // foreach ($rows as $key => $value) {
    //   SalesOrdersProdConfirmHistory::create([
    //     'so_comp_id'    => 1,
    //     'so_ref_order_id'   => $value->so_ref_order_id,
    //     'so_ref_order_no'   => $value->so_ref_order_no,
    //     'so_ref_details_id' => $value->so_ref_details_id,
    //     'so_item_unit' => $value->so_item_unit,
    //     'date'                  => $value->date,
    //     'so_itm_id'    => $value->so_itm_id,
    //     'so_order_qty'      => $value->so_order_qty,
    //     'so_conf_qty' => $value->so_conf_qty, 
    //     'so_conf_weight' => $value->so_conf_weight, 
    //   ]);
    // }
    // return $rows;
    // exit();

    // return $request;

    $fromdate=  date('Y-m-d');
    $todate=  date('Y-m-d');
    $customer_id = $request->customer_id;
    $customer_sql = '';
    $data = '';
    if($request->filled('fromdate') && $request->filled('todate')){
      $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
      $todate=  date('Y-m-d',strtotime($request->input('todate')));
      $data = 'and so_order_date BETWEEN '.$fromdate.' and '.$todate.'';
    }else{
      $data = 'and so_order_date BETWEEN '.$fromdate.' and '.$todate.'';
    }

    if($request->filled('customer_id') ){
      $customer_sql = 'and sales_orders.so_cust_id = '. $request->customer_id;
    }

    $sql = "SELECT
    `sales_orders_details`.`so_item_id` AS `itemId`,
    `sales_orders`.`so_fpo_no` AS `fpo`,
    `sales_orders`.`so_order_no` AS `order_no`,
    `sales_orders`.`so_confirmed_date` AS `fpoDate`,
    `customers`.`cust_name` AS `party`,
    `sales_orders_details`.`so_item_size` AS `size`,
    `sales_orders_details`.`so_item_spec` AS `spec`,
    `sales_orders_details`.`so_item_weight` AS `perPcsWeight`,
    `sales_orders_details`.`so_item_pcs` AS `orderPcs`,
    `sales_orders_details`.`so_order_qty` AS `orderKg`,
    (SELECT
        SUM(
            `sales_delivery_details`.`del_item_pcs`
        ) AS `item_pcs`
    FROM
        `sales_delivery_details`
    WHERE
        `sales_delivery_details`.`del_item_id` = `sales_orders_details`.`so_item_id` 
        AND `sales_delivery_details`.`del_ref_id` = `sales_deliveries`.`id`) AS `del_item_pcs`,
    
    (SELECT
        SUM(
            `sales_delivery_details`.`del_item_weight`
        ) AS `item_kg`
    FROM
        `sales_delivery_details`
    WHERE
        `sales_delivery_details`.`del_item_id` = `sales_orders_details`.`so_item_id` 
        AND `sales_delivery_details`.`del_ref_id` = `sales_deliveries`.`id`) AS `del_item_kg`

    FROM
        (
            (
              `sales_orders_details`
            JOIN `sales_orders` ON
                (
                    `sales_orders`.`id` = `sales_orders_details`.`so_order_id`
                )
            )
          JOIN `customers` ON
            (
                `customers`.`id` = `sales_orders`.`so_cust_id`
            )
          JOIN `sales_deliveries` ON
            (
                `sales_deliveries`.`del_sal_ord_id` = `sales_orders`.`id`
            )

          JOIN `sales_delivery_details` ON
            (
                `sales_delivery_details`.`del_ref_id` = `sales_deliveries`.`id`
            )
        
        )
    where so_order_date BETWEEN '".$fromdate."' and '".$todate."' $customer_sql ";
    $rows = DB::select($sql);
    // return $rows;
    $customers = $this->so_customers();
    return view ('/productions/reports/daily_delivery_report', compact('rows', 'customer_id', 'customers', 'fromdate', 'todate' ));
  }
 
}
