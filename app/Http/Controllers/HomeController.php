<?php

namespace App\Http\Controllers;

use App\Http\Controllers\General\DropdownsController;
use App\Models\Sales\SalesInvoices;
use App\Models\Items\ItemStocks;
use App\Models\Sales\SalesOrders;
use App\Models\AccTransactions;
use App\Rules\OldPasswordRule;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;

use DB;
use Response;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();

      /*$warehousestock =  ItemStocks::query()
        ->join("warehouses", "item_warehouse_id", "=", "warehouses.id")
        ->where('item_op_comp_id', $company_code )
        ->selectRaw('item_warehouse_id,ware_code,ware_name,SUM(item_op_stock) as stock')
        ->GroupBy('item_warehouse_id','ware_code','ware_name')->get();*/
    
      $warehousestock = $this->getItemStockReportByWarehouse($company_code);
    
      $totalSalesOrder = SalesOrders::query()
        ->selectRaw('sum(so_net_amt) as netOrder')
        ->where('so_comp_id', $company_code)
        ->whereDate('so_order_date', date('Y-m-d'))
        ->first()->netOrder;

      $todaySale = SalesInvoices::query()
          ->selectRaw('sum(inv_net_amt) as netBill')
          ->where('inv_comp_id', $company_code)
          ->whereDate('inv_date', date('Y-m-d'))
          ->first()->netBill;

      $monthSale = SalesInvoices::query()
              ->selectRaw('sum(inv_net_amt) as netBill')
              ->where('inv_comp_id', $company_code)
              ->whereMonth('inv_date', Carbon::now()->month)
              ->first()->netBill;

     /* $totalstock =  ItemStocks::query()
        ->join("warehouses", "item_warehouse_id", "=", "warehouses.id")
        ->where('item_op_comp_id', $company_code )
        ->selectRaw('SUM(item_op_stock) as stock')->first()->stock;*/
        
      $totalstock = $this->getItemStockReport($company_code);


       
      $todaycollection =  AccTransactions::query()
          ->join("acc_transaction_details", "acc_trans_id", "=", "acc_transactions.id")
          ->join("customers", "chart_of_acc_id", "=", "customers.cust_chartofacc_id")
          ->where('com_ref_id', $company_code )
          ->whereIn('trans_type',array('CR','BR','JV'))
          ->whereDate('voucher_date', date('Y-m-d'))
          ->selectRaw('SUM(c_amount) as collection')->first()->collection;

      $monthlycollection = AccTransactions::query()
          ->join("acc_transaction_details", "acc_trans_id", "=", "acc_transactions.id")
          ->join("customers", "chart_of_acc_id", "=", "customers.cust_chartofacc_id")
          ->where('com_ref_id', $company_code )
          ->whereIn('trans_type',array('CR','BR','JV'))
          ->whereMonth('voucher_date', Carbon::now()->month)
          ->selectRaw('SUM(c_amount) as collection')->first()->collection;

      $todayoutstanding = AccTransactions::query()
          ->join("acc_transaction_details", "acc_trans_id", "=", "acc_transactions.id")
          ->join("customers", "customers.cust_chartofacc_id", "=", "chart_of_acc_id")
          ->where('com_ref_id', $company_code )
          ->whereDate('voucher_date', date('Y-m-d'))
          ->selectRaw('SUM(d_amount)-SUM(c_amount) as outstanding')->first()->outstanding;

      $montlhyoutstanding = AccTransactions::query()
              ->join("acc_transaction_details", "acc_trans_id", "=", "acc_transactions.id")
              ->join("customers", "customers.cust_chartofacc_id", "=", "chart_of_acc_id")
              ->where('com_ref_id', $company_code )
              ->whereMonth('voucher_date', Carbon::now()->month)
              ->selectRaw('SUM(d_amount)-SUM(c_amount) as outstanding')->first()->outstanding;


      $todayLiquidCash = $this->getLiquidCashReport($company_code);
      $monthlyDatewiseSalePPUnit = $this->getInvDateWiseSummaryReport($company_code, 99); // 99 for pp unit
      $monthlyDatewiseSaleJuteUnit = $this->getInvDateWiseSummaryReport($company_code, 98); // 98 for jute unit
      $spwiseSalesSumm = $this->getSPWiseSalesReport($company_code);
      $dailyCashSheet = $this->getDailyCashSheet($company_code);
      $consubsidiaryLedger = $this->getConSubsidiaryLedger($company_code);

      $todayOrderPPUnit = $this->todayOrder($company_code, 99); // pp Unit
      $todayOrderJutenit = $this->todayOrder($company_code, 98); // Jute Unit

      $totalOrderjuteUnit = $this->totalOrder($company_code, $cat_id = 98, $month = null);
      $totalOrderPPUnit = $this->totalOrder($company_code,  $cat_id = 99, $month = null);

      $monthlyOrderJuteUnit = $this->monthlyOrder($company_code, $cat_id = 98);
      $monthlyOrderPPUnit = $this->monthlyOrder($company_code,  $cat_id = 99);

      $totalPPUnitSales = $this->totalSales($company_code, $cat_id = 99, $month = null);
      $totalJuteUnitSales = $this->totalSales($company_code, $cat_id = 98, $month = null);
      
      $monthlyPPUnitSales = $this->totalSales($company_code, $cat_id = 99, $month = 'month');
      $monthlyJuteUnitSales = $this->totalSales($company_code, $cat_id = 98, $month = 'month');

      $todayPPUnitSales = $this->totalSales($company_code, $cat_id = 99, $month = 'today');
      $todayJuteUnitSales = $this->totalSales($company_code, $cat_id = 98, $month = 'today');

      $todayOrderPPUnitCount = $this->todayOrderCount($company_code, $cat_id = 99);
      $todayOrderJuteUnitCount = $this->todayOrderCount($company_code, $cat_id = 98);
      

      //dd($dailyCashSheet);
      if ($company_code == 1
      && in_array('777', json_decode(Auth::user()->role->permissions))){
        return view('home', [
            'company_code'        => $company_code,
            'todayLiquidCash'     => $todayLiquidCash,
            'monthlyDatewiseSalePPUnit' => $monthlyDatewiseSalePPUnit,
            'monthlyDatewiseSaleJuteUnit' => $monthlyDatewiseSaleJuteUnit,
            'todayOrderPPUnit'    => $todayOrderPPUnit,
            'todayOrderJutenit'   => $todayOrderJutenit,
            'spwiseSalesSumm'     => $spwiseSalesSumm,
            'totalSalesOrder'     => $totalSalesOrder??'0.00',
            'todaySale'           => $todaySale??'0.00',
            'totalstock'          => $totalstock,

            'warehousestock'      => $warehousestock,
            'dailyCashSheet'      => $dailyCashSheet,
            'consubsidiaryLedger' => $consubsidiaryLedger, 
            'monthSale'           => $monthSale??'0.00',
            'todaycollection'     => $todaycollection??'0.00',
            'monthlycollection'   => $monthlycollection??'0.00',
            'todayoutstanding'    => $todayoutstanding??'0.00',
            'montlhyoutstanding'  => $montlhyoutstanding??'0.00',

            'totalOrderjuteUnit'  => $totalOrderjuteUnit,
            'totalOrderPPUnit'    => $totalOrderPPUnit,
            'monthlyOrderPPUnit'  => $monthlyOrderPPUnit,
            'monthlyOrderJuteUnit'=> $monthlyOrderJuteUnit,
            'totalPPUnitSales'    => $totalPPUnitSales,
            'totalJuteUnitSales'  => $totalJuteUnitSales,
            'monthlyPPUnitSales'  => $monthlyPPUnitSales,
            'monthlyJuteUnitSales'=> $monthlyJuteUnitSales,
            'todayPPUnitSales'    => $todayPPUnitSales,
            'todayJuteUnitSales'  => $todayJuteUnitSales,
            'todayOrderPPUnitCount'=> $todayOrderPPUnitCount,
            'todayOrderJuteUnitCount'=> $todayOrderJuteUnitCount

          ]);
      }else if ($company_code == 2
      && in_array('777', json_decode(Auth::user()->role->permissions))){
        return view('home_2', [
            'company_code'        => $company_code,
            'todayLiquidCash'     => $todayLiquidCash,
            'monthlyDatewiseSalePPUnit' => $monthlyDatewiseSalePPUnit,
            'monthlyDatewiseSaleJuteUnit' => $monthlyDatewiseSaleJuteUnit,
            'todayOrderPPUnit'    => $todayOrderPPUnit,
            'todayOrderJutenit'   => $todayOrderJutenit,
            'spwiseSalesSumm'     => $spwiseSalesSumm,
            'totalSalesOrder'     => $totalSalesOrder??'0.00',
            'todaySale'           => $todaySale??'0.00',
            'totalstock'          => $totalstock,

            'warehousestock'      => $warehousestock,
            'dailyCashSheet'      => $dailyCashSheet,
            'consubsidiaryLedger' => $consubsidiaryLedger, 
            'monthSale'           => $monthSale??'0.00',
            'todaycollection'     => $todaycollection??'0.00',
            'monthlycollection'   => $monthlycollection??'0.00',
            'todayoutstanding'    => $todayoutstanding??'0.00',
            'montlhyoutstanding'  => $montlhyoutstanding??'0.00',

            'totalOrderjuteUnit'  => $totalOrderjuteUnit,
            'totalOrderPPUnit'    => $totalOrderPPUnit,
            'monthlyOrderPPUnit'  => $monthlyOrderPPUnit,
            'monthlyOrderJuteUnit'=> $monthlyOrderJuteUnit,
            'totalPPUnitSales'    => $totalPPUnitSales,
            'totalJuteUnitSales'  => $totalJuteUnitSales,
            'monthlyPPUnitSales'  => $monthlyPPUnitSales,
            'monthlyJuteUnitSales'=> $monthlyJuteUnitSales,
            'todayPPUnitSales'    => $todayPPUnitSales,
            'todayJuteUnitSales'  => $todayJuteUnitSales,
            'todayOrderPPUnitCount'=> $todayOrderPPUnitCount,
            'todayOrderJuteUnitCount'=> $todayOrderJuteUnitCount
          ]);
      }else{
        return view('homep');
      }
    }

    public function todayOrderCount($company_code, $cat_id)
    {
      return SalesOrders::join("sales_orders_details", "sales_orders_details.so_order_id", "=", "sales_orders.id")
      ->join("item_categories", "item_categories.id", "=", "sales_orders_details.so_item_cat_id")
      ->where('so_item_cat_id', $cat_id)
      ->where('sales_orders.so_order_date', date('Y-m-d'))
      ->where('sales_orders.so_comp_id', $company_code)
      ->selectRaw('SUM(sales_orders.so_gross_amt) as amount')->first()->amount ?? 0;
    //   ->get();
      // ->count();
    }

    public function totalSales($company_code, $cat_id, $month)
    {
      $q = SalesInvoices::query()
        ->join("sales_orders_details", "sales_orders_details.so_order_id", "=", "sales_invoices.inv_sale_ord_id")
        ->join("item_categories", "item_categories.id", "=", "sales_orders_details.so_item_cat_id")
        ->where('inv_comp_id', $company_code)
        ->where('so_item_cat_id', $cat_id); // 99 for pp unit and 98 for jute unit
        if($month == 'month'){
          $q->whereBetween('inv_date', [date('Y-m-1'), date('Y-m-d')]);
        }

        if($month == 'today'){
          $q->where('inv_date', date('Y-m-d'));
        }

        // return $q->count();
        return $q->selectRaw('SUM(inv_net_amt) as amount')->first()->amount;
    }

    public function monthlyOrder($company_code, $cat_id)
    {
      return SalesOrders::join("sales_orders_details", "sales_orders_details.so_order_id", "=", "sales_orders.id")
        ->join("item_categories", "item_categories.id", "=", "sales_orders_details.so_item_cat_id")
        ->where('so_item_cat_id', $cat_id)
        ->whereBetween('sales_orders.so_order_date', [date('Y-m-1'), date('Y-m-d')])
        ->where('sales_orders.so_comp_id', $company_code)
        ->selectRaw('SUM(sales_orders.so_gross_amt) as amount')->first()->amount ?? 0;
        // ->count();
    }

    public function totalOrder($company_code, $cat_id, $month)
    {
      $q = SalesOrders::join("sales_orders_details", "sales_orders_details.so_order_id", "=", "sales_orders.id")
      ->join("item_categories", "item_categories.id", "=", "sales_orders_details.so_item_cat_id");
      $q->where('sales_orders_details.so_item_cat_id', $cat_id);
      $q->where('sales_orders.so_comp_id', $company_code);
      if ($month != null) {
        $q->whereBetween('sales_orders.so_order_date', [date('Y-m-1'), date('Y-m-d')]);
      }
      // return $q->count();
      return $q->selectRaw('SUM(sales_orders.so_net_amt) as amount')->first()->amount;
    }


    public function todayOrder($company_code, $cat_id)
    {
      return SalesOrders::join("sales_orders_details", "sales_orders_details.so_order_id", "=", "sales_orders.id")
        ->join("item_categories", "item_categories.id", "=", "sales_orders_details.so_item_cat_id")
        ->where('so_item_cat_id', $cat_id)
        ->where('sales_orders.so_order_date', date('Y-m-d'))
        ->where('sales_orders.so_comp_id', $company_code)
        ->selectRaw('so_item_unit, so_order_no, SUM(so_order_qty * so_item_price) as kgAmount, SUM(so_item_pcs * so_item_price) as pcsAmount, so_item_price as price,  SUM(so_item_pcs) as qty')
        // ->selectRaw('so_order_no, so_gross_amt as so_gross_amt, SUM(so_order_qty) as qty')

        ->groupBy('so_order_no', 'so_item_unit', 'so_item_price')
        ->get();
    }

    public function getInvDateWiseSummaryReport($company_code, $cat_id)
    {
      $q = SalesInvoices::query()
        ->join("sales_orders_details", "sales_orders_details.so_order_id", "=", "sales_invoices.inv_sale_ord_id")
        ->join("item_categories", "item_categories.id", "=", "sales_orders_details.so_item_cat_id")
        ->where('inv_comp_id', $company_code)
        ->where('so_item_cat_id', $cat_id) // 99 for pp unit and 98 for jute unit
        ->selectRaw('inv_date,SUM(inv_sub_total) as inv_sub_total,
          SUM(inv_itm_disc_value) as inv_itm_disc_value,
          SUM(inv_disc_value) as inv_disc_value,
          SUM(inv_vat_value) as inv_vat_value, SUM(inv_net_amt) as inv_net_amt');

      $q->whereMonth('inv_date', Carbon::now()->month);
      $q->GroupBy('inv_date');
      $rows = $q->orderBy('sales_invoices.inv_date', 'asc')->get();
      return $rows;
    }

    public function getLiquidCashReport($company_code)
    {
       $sql = "select distinct p.id as id,p.acc_head as acc_head from `chartofaccounts` c
       inner join chartofaccounts p on p.id = c.parent_id
       where c.`company_id` = $company_code and p.acc_head like 'Cash%'
       and c.`id` not in (select `parent_id` from `chartofaccounts`)
       Order By p.acc_head asc";
      $ledgers = DB::select($sql);
      $ledger_id = '0,';
      foreach($ledgers as $row){
         $ledger_id .= $row->id.",";
      }
      $sql = "SELECT acc_head, SUM(op_d_amount) as op_debit,SUM(op_c_amount) as op_credit ,
      SUM(t_d_amount) as tr_debit,SUM(t_c_amount) as tr_credit
      FROM
      (SELECT c.acc_head as acc_head,SUM(d_amount) as op_d_amount,SUM(c_amount) as op_c_amount, 0 as t_d_amount,0 as t_c_amount
      FROM acc_transactions t
      INNER JOIN acc_transaction_details on t.id = acc_trans_id
      INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      inner join chartofaccounts p on p.id = c.parent_id
      Where com_ref_id =  $company_code and (chart_of_acc_id = 75 or c.parent_id = 74)
      AND voucher_date < '". date('Y-m-d')."'
      GROUP BY c.acc_head
      UNION ALL
      SELECT c.acc_head as acc_head,0 as op_d_amount,0 as op_c_amount,SUM(d_amount) as t_d_amount,SUM(c_amount) as t_c_amount
      FROM acc_transactions t
      INNER JOIN acc_transaction_details on t.id = acc_trans_id
      INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      inner join chartofaccounts p on p.id = c.parent_id
      Where com_ref_id =  $company_code and (chart_of_acc_id = 75 or c.parent_id = 74)
      AND voucher_date BETWEEN '". date('Y-m-d')."' and '".date('Y-m-d')."'
      GROUP BY c.acc_head ) as M GROUP BY acc_head";
      $rows = DB::select($sql);
      return $rows;
    }

    public function getDailyCashSheet($company_code)
    {
      $sql ="SELECT * FROM chartofaccounts Where acc_head in ('CASH IN HAND','CASH AT BANK')
      and company_id = ".$company_code;

      $ledgers = DB::select($sql);
      $ledger_id = '';
      $CashinHand = 0;
      foreach($ledgers as $ledger){
              if ($ledger->parent_id == 0){
                $ledger_id .= $ledger->id.',';
              }else{
                $CashinHand = $ledger->id;
              }
      }
      $ledger_id .= '0';
      $ledger_id = str_replace(',0', ' ', $ledger_id);

      $op_sql = "select ifnull(sum(d_amount),0) as debit, ifnull(sum(c_amount),0) as credit from `acc_transactions`
      inner join `acc_transaction_details` on `acc_transactions`.`id` = `acc_trans_id`
      inner join `chartofaccounts` on `chartofaccounts`.`id` = `chart_of_acc_id`
      where `com_ref_id` = $company_code and `chartofaccounts`.parent_id in ($ledger_id)
      and date(`voucher_date`) < '". date('Y-m-d')."'";
      $openings = DB::select($op_sql);

      $sql1 = "select acc_head,t_narration, d_amount as d_amount,c_amount as c_amount
      from `acc_transactions`
      inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
      inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
      where `voucher_date` between '".date('Y-m-d')."' and '".date('Y-m-d')."' and `com_ref_id` = $company_code
      and trans_type in ('BR')
      AND (parent_id not in ($ledger_id))  order by file_level asc";
      $rows_bank_rec = DB::select($sql1);

      $sql1 = "select acc_head,t_narration, d_amount as d_amount,c_amount as c_amount
      from `acc_transactions`
      inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
      inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
      where `voucher_date` between '".date('Y-m-d')."' and '".date('Y-m-d')."' and `com_ref_id` = $company_code
      and trans_type in ('CR')
      AND (parent_id not in ($ledger_id))  order by file_level asc";
      $rows_cash_rec = DB::select($sql1);

      $sql1 = "select acc_head,t_narration, d_amount as d_amount,c_amount as c_amount
      from `acc_transactions`
      inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
      inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
      where `voucher_date` between '".date('Y-m-d')."' and '".date('Y-m-d')."' and `com_ref_id` = $company_code
      and trans_type in ('CP','BP','CON')
      AND (parent_id not in ($ledger_id))  order by file_level asc";
      $rows_payment = DB::select($sql1);

      //Cash in Hand
      $sql2 = "select SUM(d_amount) as d_amount,SUM(c_amount) as c_amount,SUM(d_amount)-SUM(c_amount) as CashinHand
      from `acc_transactions`
      inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
      inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
      where `voucher_date` <= '".date('Y-m-d')."' and `com_ref_id` = $company_code
      AND chart_of_acc_id = $CashinHand";
      $CashinHand = collect(\DB::select($sql2))->first();

      $data = AccTransactions::query()
        ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
        ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
        ->where('voucher_date','<=', date('Y-m-d'))
        ->where('com_ref_id', $company_code )
        ->where('is_cash_sheet', 1 )
        ->selectRaw('chart_of_acc_id,acc_head,SUM(d_amount) as d_amount, SUM(c_amount) as c_amount')
        ->groupBy('chart_of_acc_id','acc_head')
        ->orderBy('acc_code', 'asc')
        ->orderBy('trans_type', 'asc')->get();

      // get requested action
      return compact('rows_bank_rec','rows_cash_rec','rows_payment','data','openings',
      'company_code','CashinHand');

    }
    
    public function getSPWiseSalesReport($company_code)
    {

      /* $sql = "select customer_sales_persons.id,sales_name,
      SUM(inv_net_amt) as inv_net_amt,SUM(inv_vat_value) as inv_vat_value,collection,outstanding  FROM customers
      INNER JOIN sales_invoices on customers.id = inv_cust_id
      LEFT JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
      LEFT JOIN (SELECT customer_sales_persons.id,SUM(c_amount) as collection FROM acc_transactions inner join  acc_transaction_details
      on acc_trans_id = acc_transactions.id
      INNER JOIN customers on customers.cust_chartofacc_id = chart_of_acc_id
      INNER JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
      where trans_type in ('CR','BR')
      and month(voucher_date) = '".Carbon::now()->month."'
      and year(voucher_date) = '".Carbon::now()->year."'  GROUP BY customer_sales_persons.id ) as collection
      ON collection.id = customer_sales_persons.id
      LEFT JOIN (SELECT customer_sales_persons.id,SUM(d_amount)-SUM(c_amount) as outstanding FROM acc_transactions inner join  acc_transaction_details
      on acc_trans_id = acc_transactions.id
      INNER JOIN customers on customers.cust_chartofacc_id = chart_of_acc_id
      INNER JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
      GROUP BY customer_sales_persons.id ) as outstanding
      ON outstanding.id = customer_sales_persons.id
      WHERE inv_comp_id = $company_code
      and  month(inv_date) = '".Carbon::now()->month."'
      and year(inv_date) = '".Carbon::now()->year."' 
      GROUP BY  customer_sales_persons.id,sales_name,collection,outstanding
      ORDER BY customer_sales_persons.id desc";*/
      
        $sql = "SELECT * FROM
    (select IFNULL(customer_sales_persons.id,0) as id,sales_name, SUM(inv_net_amt) as inv_net_amt,
    SUM(inv_vat_value) as inv_vat_value  FROM customers
    INNER JOIN sales_invoices on customers.id = inv_cust_id
    LEFT JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
    Where inv_comp_id = $company_code
    and month(inv_date) = '".Carbon::now()->month."'
    and year(inv_date) = '".Carbon::now()->year."'
    Group BY customer_sales_persons.id,
    sales_name) AS SP
    LEFT JOIN
    (SELECT IFNULL(customer_sales_persons.id,0) as id,SUM(c_amount) as collection FROM acc_transactions
    inner join acc_transaction_details on acc_trans_id = acc_transactions.id
    INNER JOIN customers on customers.cust_chartofacc_id = chart_of_acc_id
    LEFT JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
    where trans_type in ('CR','BR') and com_ref_id = $company_code
    and month(voucher_date) = '".Carbon::now()->month."'
    and year(voucher_date) = '".Carbon::now()->year."'
    GROUP BY customer_sales_persons.id ) as collection ON collection.id = SP.id
    LEFT JOIN (SELECT IFNULL(customer_sales_persons.id,0) as id,SUM(d_amount)-SUM(c_amount) as outstanding
    FROM acc_transactions inner join acc_transaction_details on acc_trans_id = acc_transactions.id
    INNER JOIN customers on customers.cust_chartofacc_id = chart_of_acc_id
    LEFT JOIN customer_sales_persons ON customer_sales_persons.id = cust_sales_per_id
    Where com_ref_id = $company_code GROUP BY customer_sales_persons.id ) as outstanding ON outstanding.id = SP.id
    ORDER BY SP.id desc";
       $rows = DB::select($sql);

        // get requested action
        return $rows;
    }
    
    public function getItemStockReport($company_code)
    {

      $sql = "SELECT SUM(BAL) as BAL, SUM(AMOUNT) AS AMOUNT FROM (
      SELECT SUM(OP)+SUM(GR)+SUM(SA)+SUM(RT)+SUM(ST)+SUM(SR)+SUM(DA)+SUM(SH)+SUM(EX) as BAL,
      (SUM(OP)+SUM(GR)+SUM(SA)+SUM(RT)+SUM(ST)+SUM(SR)+SUM(DA)+SUM(SH)+SUM(EX)) * max(l_item_base_price) as AMOUNT
        FROM view_item_ledger
        inner join items on items.id = item_ref_id
        INNER JOIN item_categories on item_ref_cate_id=item_categories.id and itm_comp_id = item_ref_comp_id
        LEFT JOIN view_item_last_price on l_item_op_comp_id = item_ref_comp_id and l_item_lot_no = item_lot_no
        AND l_item_ref_id = item_ref_id
        Where item_op_comp_id = $company_code
        GROUP BY item_code,item_name,itm_cat_name,item_lot_no ORDER BY item_code asc) as AA";
        $rows = DB::select($sql);

      // get requested action
      return $rows;
    }
    
   public function getItemStockReportByWarehouse($company_code)
   {

     $sql = "SELECT ware_name,SUM(BAL) as BAL, SUM(AMOUNT) AS AMOUNT FROM (
      SELECT ware_name,SUM(OP)+SUM(GR)+SUM(SA)+SUM(RT)+SUM(ST)+SUM(SR)+SUM(DA)+SUM(SH)+SUM(EX) as BAL,
      (SUM(OP)+SUM(GR)+SUM(SA)+SUM(RT)+SUM(ST)+SUM(SR)+SUM(DA)+SUM(SH)+SUM(EX)) * max(l_item_base_price) as AMOUNT
      FROM view_item_ledger
      inner join items on items.id = item_ref_id
      inner join warehouses on warehouses.id = item_warehouse_id
      INNER JOIN item_categories on item_ref_cate_id=item_categories.id and itm_comp_id = item_ref_comp_id
      LEFT JOIN view_item_last_price on l_item_op_comp_id = item_ref_comp_id and l_item_lot_no = item_lot_no
      AND l_item_ref_id = item_ref_id
      Where item_op_comp_id = $company_code GROUP BY
      ware_name,item_code,item_name,itm_cat_name,item_lot_no
      ORDER BY item_code asc) as AA GROUP BY ware_name";
      $rows = DB::select($sql);

    // get requested action
    return $rows;
   }
   
   public function getConSubsidiaryLedger($company_code)
   {

        $sql = "SELECT acc_head, SUM(op_d_amount) as op_debit,SUM(op_c_amount) as op_credit ,
        SUM(t_d_amount) as tr_debit,SUM(t_c_amount) as tr_credit
        FROM
        (SELECT c.acc_head as acc_head,SUM(d_amount) as op_d_amount,SUM(c_amount) as op_c_amount, 0 as t_d_amount,0 as t_c_amount
        FROM acc_transactions t
        INNER JOIN acc_transaction_details on t.id = acc_trans_id
        INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
        inner join chartofaccounts p on p.id = c.parent_id
        Where com_ref_id =  $company_code and c.parent_id = 61
        AND voucher_date < '". date('Y-m-d') ."'
        GROUP BY c.acc_head
        UNION ALL
        SELECT c.acc_head as acc_head,0 as op_d_amount,0 as op_c_amount,SUM(d_amount) as t_d_amount,SUM(c_amount) as t_c_amount
        FROM acc_transactions t
        INNER JOIN acc_transaction_details on t.id = acc_trans_id
        INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
        inner join chartofaccounts p on p.id = c.parent_id
        Where com_ref_id =  $company_code and c.parent_id = 61
        AND voucher_date = '". date('Y-m-d')."'
        GROUP BY c.acc_head ) as M GROUP BY acc_head";
    
    
        $rows = DB::select($sql);
    
        // get requested action
        return $rows;
   }
   
   public function passwordEdit()
   {
        return view('password',['user' => auth()->user(),
                            'own' => 1]);
   }

    public function passwordUpdate(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'password' => 'required|min:4|confirmed',
            'old_password' => ['required', new OldPasswordRule($user->password)],
        ]);
        $user->update(['password'=> Hash::make($request->password)]);
        return back()->with('message', 'Password Changed!');
    }
    
}
