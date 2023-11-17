<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;

use App\Models\Companies;
use App\Models\Customers\Customers;
use App\Models\Sales\SalesOrders;
use App\Models\Sales\SalesOrdersDetails;

use App\Models\Sales\SalesInvoices;
use App\Models\Sales\SalesInvoiceDetails;

use App\Models\Sales\SalesReturns;
use App\Models\Sales\SalesReturnDetails;

use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;

use App\Models\Items\Items;

use App\Http\Resources\ItemCodeResource;
use App\Http\Resources\TransItemCodeResource;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use Response;
use DB;
use PDF;

class SalesReturnController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
   public function index(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $companies    = $dropdownscontroller->comboCompanyAssignList();
     if($request->filled('company_code')){
       $company_code = $request->get('company_code');
     }else{
       $company_code = $dropdownscontroller->defaultCompanyCode();
     }
     $customers = Customers::query()->orderBy('cust_name','asc')->get();
     $invoices = SalesInvoices::query()->where('inv_comp_id', $company_code )
        ->orderBy('inv_no','asc')->get();

     $q = SalesReturns::query()
        ->join("customers", "customers.id", "=", "ret_cust_id")
        ->leftjoin("sales_invoices", "sales_invoices.id", "=", "ret_sal_inv_id")
        ->join("sales_orders", "sales_orders.id", "=", "ret_sal_ord_id")
        ->join("acc_transaction_details", "acc_invoice_no", "=", "ret_no")
        ->join("acc_transactions", "acc_transactions.id", "=", "acc_trans_id")  
        ->where('ret_comp_id', $company_code )
        ->selectRaw('sales_returns.id,ret_comp_id,ret_no,inv_no,ret_date,inv_so_po_no,ret_cust_id,so_order_no,trans_type,voucher_no,ret_sub_total,ret_itm_disc_per,ret_itm_disc_value,ret_inv_disc_per,
        ret_inv_disc_value,ret_vat_per,ret_vat_value,ret_net_amt,ret_vat_value,ret_net_amt,cust_code,cust_name');

      $inv_no = '';
      $fromdate = '';
      $todate  = '';
       if($request->filled('inv_no')){
          $q->where('inv_no', $request->get('inv_no'));
        }
        if($request->filled('customer_id')){
          $q->where('ret_cust_id', $request->get('customer_id'));
        }
        if($request->filled('fromdate')){
          $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
          $q->where('ret_date','>=', $fromdate);
        }
        if($request->filled('todate')){
           $todate = date('Y-m-d',strtotime($request->get('todate')));
           $q->where('ret_date','<=', $todate);
        }
       $rows = $q->orderBy('sales_returns.id', 'desc')->paginate(10)->setpath('');
       $rows->appends(array(
          'inv_no' => $request->get('inv_no'),
          'customer_id' => $request->get('customer_id'),
          'fromdate' => $fromdate,
          'todate' => $todate,
        ));
       // get requested action
       return view('/sales/return_index', compact('rows','customers','companies',
       'company_code','inv_no','invoices','fromdate','todate' ));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $item_list = $dropdownscontroller->itemOrderLookup($id);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);

      $rows = SalesInvoices::query()
          ->join("customers", "customers.id", "=", "inv_cust_id")
          ->join("sales_orders", "sales_orders.id", "=", "inv_sale_ord_id")
          ->where('sales_invoices.id', $id )
          ->selectRaw('sales_invoices.id,inv_sale_ord_id,inv_comp_id,inv_no,inv_date,inv_so_po_no,inv_cust_id,so_order_no,
          inv_sub_total,inv_disc_value,inv_vat_value,inv_net_amt,inv_acc_doc,cust_code,cust_name')->first();

     /*$rows_d =  SalesInvoiceDetails::query()
        ->join("items", "items.id", "=", "inv_item_id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('inv_mas_id', $id)
        ->selectRaw('sales_invoice_details.id,inv_warehouse_id,inv_del_id,inv_storage_id,itm_cat_name,item_code,
         item_bar_code,item_name,inv_item_id,inv_item_price, inv_lot_no,inv_qty,inv_unit,
         inv_itm_disc_per,inv_disc_per,inv_disc_value,inv_vat_per,
         (inv_qty*inv_item_price*inv_itm_disc_per)/100 as item_disc_value,
         (inv_qty*inv_item_price)-(inv_qty*inv_item_price*inv_itm_disc_per)/100 as item_value,
         inv_del_comments')->get();*/

     $sql = "select sales_invoice_details.id,inv_warehouse_id,inv_del_id,inv_storage_id,itm_cat_name,item_code, item_bar_code,item_name,item_desc,
      inv_item_id,inv_item_price, inv_lot_no,inv_qty,inv_unit, inv_itm_disc_per,
      inv_disc_per,inv_disc_value,inv_vat_per,
      (inv_qty*inv_item_price*inv_itm_disc_per)/100 as item_disc_value,
      (inv_qty*inv_item_price)-(inv_qty*inv_item_price*inv_itm_disc_per)/100 as item_value,
      inv_del_comments,ret_item_id,ret_lot_no,ret_qty
      from `sales_invoice_details`
      left join (SELECT ret_item_id,ret_lot_no,SUM(ret_qty) as ret_qty from sales_return_details
      INNER JOIN sales_returns ON ret_order_id = sales_returns.id Where ret_sal_inv_id = $id
      group by ret_item_id,ret_lot_no) as RET on ret_item_id = inv_item_id and inv_lot_no = ret_lot_no
      inner join `items` on `items`.`id` = `inv_item_id`
      inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id`
      where `inv_mas_id` = $id";
      $rows_d = DB::select($sql);

      $return_date = date('d-m-Y');

      return view('/sales/ret_create',compact('companies','return_date','company_code',
      'item_list','rows','rows_d','courr_list','warehouse_id','stor_list'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // Validate the Field
        $this->validate($request,[]);
        $this->store_return($request);
        return redirect()->route('sales.return.index')->with('message','Sales Return Created Successfull !');
    }

    public function store_return(Request $request)
    {
        $generalscontroller = new GeneralsController();
        $ret_date = date('Y-m-d',strtotime($request->return_date));
        $yearValidation = $generalscontroller->getFinYearValidation($request->company_code,$ret_date);
        if($yearValidation) {
          $finan_yearId = $generalscontroller->getFinYearId($request->company_code,$ret_date);
        }else{
            return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
        }
        $return_orderno = $generalscontroller->make_sales_returnno($request->company_code,$finan_yearId);
        $trans_id = SalesReturns::insertGetId([
        'ret_comp_id'    => $request->company_code,
        'ret_fin_year_id' => $finan_yearId,
        'ret_m_warehouse_id'  => $request->itm_warehouse,
        'ret_title'       => 'RV',
        'ret_sal_ord_id'  => $request->so_id,
        'ret_sal_del_id'  => $request->del_id,
        'ret_sal_inv_id'  => $request->inv_id,
        'ret_no'          => $return_orderno,
        'ret_date'        => date('Y-m-d',strtotime($request->return_date)),
        'ret_cust_id'    => $request->customer_id,
        'ret_comments'   => $request->comments,
        'ret_sub_total'  => ($request->n_sub_total=='')?'0':(float) str_replace(',', '', $request->n_sub_total),
        'ret_itm_disc_per'    => ($request->n_disc_per=='')?'0':(float) str_replace(',', '', $request->n_disc_per),
        'ret_itm_disc_value'  => ($request->total_discount=='')?'0':(float) str_replace(',', '', $request->total_discount),
        'ret_inv_disc_per'    => ($request->n_disc_per=='')?'0':(float) str_replace(',', '', $request->n_disc_per),
        'ret_inv_disc_value'  => ($request->n_discount=='')?'0':(float) str_replace(',', '', $request->n_discount),
        'ret_vat_per'     => ($request->n_vat_per=='')?'0': (float) str_replace(',', '', $request->n_vat_per),
        'ret_vat_value'   => ($request->n_total_vat=='')?'0': (float) str_replace(',', '', $request->n_total_vat),
        'ret_net_amt'     => ($request->n_net_amount=='')?'0': (float) str_replace(',', '', $request->n_net_amount),
        'created_by'     => Auth::id(),
        'updated_by'     => Auth::id(),
        'created_at'     => Carbon::now(),
        'updated_at'     => Carbon::now(),
        ]);
        //Details Records
        $acc_naration = '';
        $detId = $request->input('ItemCodeId');
        if ($detId){
            foreach ($detId as $key => $value){
              if ($request->Qty[$key] > 0){
                SalesReturnDetails::create([
                    'ret_comp_id'    => $request->company_code,
                    'ret_order_id'   => $trans_id,
                    'ret_warehouse_id' => $request->itm_warehouse,
                    'ret_storage_id' => $request->Storage[$key],
                    'ret_lot_no'     => $request->lotno[$key],
                    'ret_item_id'    => $request->ItemCodeId[$key],
                    'ret_item_unit'  => $request->Unit[$key],
                    'ret_item_price' => $request->Price[$key],
                    'ret_qty'        => $request->Qty[$key]==''?'0':$request->Qty[$key],
                    'ret_disc_per'   => $request->Discp[$key]==''?'0': (float) str_replace(',', '', $request->Discp[$key]),
                    'ret_disc_value' => $request->Discount[$key]==''?'0': (float) str_replace(',', '', $request->Discount[$key]),
                ]);
                //make naration for accounting entry
                $itmname = $generalscontroller->get_item_details($request->ItemCodeId[$key])->item_name;
                $qty    = $request->Qty[$key];
                $price  = $request->Price[$key];
                $amount = $qty * $price;
                $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$price.';Amount:'.$amount.'),<br/>';

              }
            }
            //update financial transaction for sales
            $company_code = $request->company_code;
            $customer_id = $request->customer_id;
            $voucher_no = $generalscontroller->getMaxAccVoucherNo('RV',$company_code,$finan_yearId); // getting max Voucher No
            $voucher_no = $voucher_no + 1;

            $cust_acc_id  = $generalscontroller->CustomerChartOfAccId($customer_id);
            $cust_name    = $generalscontroller->CustomerName($customer_id);
            $salesreturns = new SalesReturns();
            $records  = $salesreturns->return_fin_transaction($trans_id);
            $recCount = $records->count();

            // Insert Transaction Master Records
            $trans_fin_id = AccTransactions::insertGetId([
              'com_ref_id'    => $company_code,
              'voucher_date'  => date('Y-m-d',strtotime($request->return_date)),
              'trans_type'    => 'RV',
              'voucher_no'    => $voucher_no,
              't_narration'   => $acc_naration,
              'fin_ref_id'    => $finan_yearId,
            ]);

            AccTransactionDetails::create([
                'acc_trans_id'    => $trans_fin_id,
                'c_amount'        => $request->n_net_amount,
                'chart_of_acc_id' => $cust_acc_id,
                'acc_invoice_no'  => $return_orderno,
            ]);
            $total_vat = 0;
            foreach ($records as $rec){
              $sub_total = $rec->sub_total;
              $net_total  = $sub_total - $rec->inv_disc_value;
              $total_vat += ($sub_total - $rec->inv_disc_value)*$rec->inv_vat_per/100;
              AccTransactionDetails::create([
                  'acc_trans_id'    => $trans_fin_id,
                  'd_amount'        => $net_total,
                  'chart_of_acc_id' => $rec->sett_accid,
              ]);
            }
            //VAT entry
            if($total_vat > 0) {
              AccTransactionDetails::create([
                  'acc_trans_id'    => $trans_fin_id,
                  'd_amount'        => $total_vat,
                  'chart_of_acc_id' => 638,
              ]);
            }
        }
        return $trans_id;
    }

    public function return_modal_view($id)
    {
       $rows_m = SalesReturns::query()
          ->join("customers", "customers.id", "=", "ret_cust_id")
          ->where('sales_returns.id', $id)
          ->selectRaw('sales_returns.id,ret_comp_id,ret_no,ret_date,ret_cust_id,ret_comments,
          ret_sub_total,ret_itm_disc_per,ret_itm_disc_value,ret_inv_disc_per,ret_inv_disc_value,
          ret_vat_per,ret_vat_value,ret_net_amt,cust_code,cust_name,
          cust_add1,cust_add2,cust_mobile,cust_phone')->first();

        $rows_d = SalesReturnDetails::query()
          ->join("items", "items.id", "=", "ret_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('ret_order_id', $id)
          ->selectRaw('itm_cat_name,item_code,item_name,ret_item_id,ret_item_price,
          ret_qty,ret_disc_per,ret_disc_value')->get();

        return view('sales.ret_item_viewmodal',compact('rows_m','rows_d'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $item_list = $dropdownscontroller->itemOrderLookup($id);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);

    $rows = SalesReturns::query()
         ->join("sales_invoices", "sales_invoices.id", "=", "ret_sal_inv_id")
         ->join("sales_orders", "sales_orders.id", "=", "ret_sal_ord_id")
         ->join("customers", "customers.id", "=", "ret_cust_id")
         ->where('sales_returns.id', $id)
         ->selectRaw('sales_returns.id,so_order_no,inv_no,ret_comp_id,ret_no,ret_date,ret_cust_id,ret_comments,
         ret_m_warehouse_id,ret_sal_ord_id,ret_sal_del_id,ret_sal_inv_id,
         ret_title,ret_no,ret_date,ret_cust_id,ret_req_date,ret_comments,
         ret_sub_total,ret_itm_disc_per,ret_itm_disc_value,
         ret_inv_disc_per,ret_inv_disc_value,
         ret_vat_per,ret_vat_value,ret_net_amt,cust_code,cust_name,
         cust_add1,cust_add2,cust_mobile,cust_phone')->first();

    $sql = "SELECT itm_cat_name,item_code, item_bar_code,item_name,item_desc,
      sales_return_details.ret_comp_id,ret_order_id,ret_warehouse_id,ret_storage_id,
      sales_return_details.ret_lot_no,sales_return_details.ret_item_id,ret_item_unit,
      ret_item_price,ret_qty,ret_item_unit,ret_disc_per,ret_disc_value,
      inv_qty,total_ret_qty
      FROM sales_return_details
      INNER JOIN sales_returns ON sales_returns.id = ret_order_id
      INNER JOIN sales_invoice_details ON inv_mas_id = ret_sal_inv_id and inv_item_id = ret_item_id and inv_lot_no = ret_lot_no
      INNER join (SELECT ret_item_id,ret_lot_no,SUM(ret_qty) as total_ret_qty from sales_return_details
      INNER JOIN sales_returns ON ret_order_id = sales_returns.id Where ret_order_id = $id
      group by ret_item_id,ret_lot_no) as RET on RET.ret_item_id = sales_return_details.ret_item_id
      and sales_return_details.ret_lot_no = RET.ret_lot_no
      inner join `items` on `items`.`id` = `inv_item_id`
      inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id`
      Where ret_order_id = $id";

      $rows_d = DB::select($sql);

      return view('/sales/ret_edit',compact('companies','company_code',
      'item_list','rows','rows_d','courr_list','warehouse_id','stor_list'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
         $generalscontroller = new GeneralsController();
         // Validate the Field
         $this->validate($request,[]);
         //Delete Sales Order Details Records
         $ret_id    = $request->id;
         $ret_date  = date('Y-m-d',strtotime($request->return_date));
         $yearValidation = $generalscontroller->getFinYearValidation($request->company_code,$ret_date);
         if($yearValidation) {
           $finan_yearId = $generalscontroller->getFinYearId($request->company_code,$ret_date);
         }else{
             return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
         }
         SalesReturnDetails::where('ret_order_id',$ret_id)->delete();
         $ret_no = $generalscontroller->getRetVoucherNoByRetOrderNo($ret_id);
         $acc_trans_id = $generalscontroller->getReturnIdByVoucherNo($ret_no);
         $acc_voucher_no = $generalscontroller->getAccVoucherNoByAccTransId($acc_trans_id);
         if($acc_trans_id>0) {
               AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
               AccTransactions::where('id',$acc_trans_id)->delete();
         }
         $this->store_return_update($request,$finan_yearId,$acc_voucher_no);
         return redirect()->route('sales.return.edit',$ret_id)->with('message','Update Sales Return is Successfull !');
    }

    public function store_return_update(Request $request,$finan_yearId,$acc_voucher_no)
    {
      // Validate the Field
        $this->validate($request,[]);
        $id = $request->id;
        $generalscontroller = new GeneralsController();

        $inputdata  = SalesReturns::find($id);
        $inputdata->ret_comp_id       = $request->company_code;
        $inputdata->ret_m_warehouse_id  = $request->itm_warehouse;
        $inputdata->ret_sal_ord_id  = $request->so_id;
        $inputdata->ret_sal_del_id  = $request->del_id;
        $inputdata->ret_sal_inv_id  = $request->inv_id;
        //$inputdata->ret_no           = $return_orderno;
        $inputdata->ret_date        = date('Y-m-d',strtotime($request->return_date));
        $inputdata->ret_cust_id    = $request->customer_id;
        $inputdata->ret_comments   = $request->comments;
        $inputdata->ret_sub_total  = ($request->n_sub_total=='')?'0':$request->n_sub_total;
        $inputdata->ret_itm_disc_per    = ($request->n_disc_per=='')?'0':$request->n_disc_per;
        $inputdata->ret_itm_disc_value  = ($request->total_discount=='')?'0':$request->total_discount;
        $inputdata->ret_inv_disc_per    = ($request->n_disc_per=='')?'0':$request->n_disc_per;
        $inputdata->ret_inv_disc_value  = ($request->n_discount=='')?'0':$request->n_discount;
        $inputdata->ret_vat_per    = ($request->n_vat_per=='')?'0':$request->n_vat_per;
        $inputdata->ret_vat_value   = ($request->n_total_vat=='')?'0':$request->n_total_vat;
        $inputdata->ret_net_amt    = ($request->n_net_amount=='')?'0':$request->n_net_amount;
        $inputdata->updated_by     = Auth::id();
        $inputdata->updated_at     = Carbon::now();
        $inputdata->save();

        $detId = $request->input('ItemCodeId');
        $acc_naration = '';
        if ($detId){
            foreach ($detId as $key => $value){
              if ($request->Qty[$key] > 0){
               SalesReturnDetails::create([
                 'ret_comp_id'    => $request->company_code,
                 'ret_order_id'   => $id,
                 'ret_warehouse_id' => $request->itm_warehouse,
                 'ret_storage_id' => $request->Storage[$key],
                 'ret_lot_no'     => $request->lotno[$key],
                 'ret_item_id'    => $request->ItemCodeId[$key],
                 'ret_item_unit'  => $request->Unit[$key],
                 'ret_item_price' => $request->Price[$key],
                 'ret_qty'        => $request->Qty[$key]==''?'0':(float) str_replace(',', '', $request->Qty[$key]),
                 'ret_disc_per'   => $request->Discp[$key]==''?'0':(float) str_replace(',', '', $request->Discp[$key]),
                 'ret_disc_value' => $request->Discount[$key]==''?'0':(float) str_replace(',', '', $request->Discount[$key]),
                ]);

                //make naration for accounting entry
                $itmname = $generalscontroller->get_item_details($request->ItemCodeId[$key])->item_name;
                $qty    = $request->Qty[$key];
                $price  = $request->Price[$key];
                $amount = $qty * $price;
                $acc_naration .= '('.$itmname.';Qty:'.$qty.';Rate:'.$price.';Amount:'.$amount.'),<br/>';
              }
            }
            //update financial transaction for sales
            $company_code = $request->company_code;
            $customer_id = $request->customer_id;
            
            if($acc_voucher_no>0){
              $voucher_no = $acc_voucher_no;
            }else{
              $voucher_no = $generalscontroller->getMaxAccVoucherNo('RV',$company_code,$finan_yearId); // getting max Voucher No
              $voucher_no = $voucher_no + 1;
            }

            $cust_acc_id  = $generalscontroller->CustomerChartOfAccId($customer_id);
            $cust_name    = $generalscontroller->CustomerName($customer_id);
            $salesreturns = new SalesReturns();
            $records  = $salesreturns->return_fin_transaction($id);
            $recCount = $records->count();

            // Insert Transaction Master Records
            $trans_fin_id = AccTransactions::insertGetId([
              'com_ref_id'    => $company_code,
              'voucher_date'  => date('Y-m-d',strtotime($request->return_date)),
              'trans_type'    => 'RV',
              'voucher_no'    => $voucher_no,
              't_narration'   => $acc_naration,
              'fin_ref_id'    => $finan_yearId,
            ]);

            AccTransactionDetails::create([
                'acc_trans_id'    => $trans_fin_id,
                'c_amount'        => $request->n_net_amount,
                'chart_of_acc_id' => $cust_acc_id,
                'acc_invoice_no'  => $request->return_orderno,
            ]);
            $total_vat = 0;
            foreach ($records as $rec){
              $sub_total = $rec->sub_total;
              $net_total  = $sub_total - $rec->inv_disc_value;
              $total_vat += ($sub_total - $rec->inv_disc_value)*$rec->inv_vat_per/100;
              AccTransactionDetails::create([
                  'acc_trans_id'    => $trans_fin_id,
                  'd_amount'        => $net_total,
                  'chart_of_acc_id' => $rec->sett_accid,
              ]);
            }
            //VAT entry
            if($total_vat > 0) {
              AccTransactionDetails::create([
                  'acc_trans_id'    => $trans_fin_id,
                  'd_amount'        => $total_vat,
                  'chart_of_acc_id' => 638,
              ]);
            }
        }
    }

    public function acc_modal_view($voucher_no)
    {
       if ($voucher_no != '') {
          $generalscontroller = new GeneralsController();
          $id = $generalscontroller->getReturnIdByVoucherNo($voucher_no);
          //return $id;
          $acctranscontroller = new AccountTransController();
          $rows_m = $acctranscontroller->modal_view_m($id);
          $rows_d = $acctranscontroller->modal_view_d($id);
          return view('accounts.acctrans_viewmodal',compact('rows_m','rows_d'));
        }else{
          return 'Posting not yet done';
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id,$retno)
    {
        try{
          $ret_order_id = $id;
          //return $isconfirmed;
          $generalscontroller = new GeneralsController();
          //delete financial transaction
          $ret_no  = $retno;//$generalscontroller->getVoucherNoByOrderNo($sales_order_id);
          //return $ret_no;
          $acc_trans_id = $generalscontroller->getReturnIdByVoucherNo($ret_no);

          if($acc_trans_id>0) {
                AccTransactionDetails::where('acc_trans_id',$acc_trans_id)->delete();
                AccTransactions::where('id',$acc_trans_id)->delete();
          }

          //Delete Sales Order Details Records
          SalesReturnDetails::where('ret_order_id',$ret_order_id)->delete();
          SalesReturns::where('id',$ret_order_id)->delete();

        }catch (\Exception $e){
            return redirect()->back()->with('message',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
