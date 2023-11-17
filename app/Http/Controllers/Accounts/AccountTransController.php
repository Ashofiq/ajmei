<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

use App\Models\CompaniesAssigns;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use App\Models\Chartofaccounts;

use App\Http\Resources\AccHeadResource;
use App\Http\Resources\TransAccHeadResource;
use DB;

class AccountTransController extends Controller
{
  public $comp_code = 0;
  public $trans_type = 0;

  public function acc_index($company_code,$trans_type)
  {

    $this->com_code = $company_code;
    $this->trans_type = $trans_type;
    /*$journals = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->join("chartofaccounts",'chartofaccounts.id', '=', 'chart_of_acc_id')
      ->join("acc_trans_doc_types",'acc_transactions.trans_type', '=', 'doc_type')
      ->join("companies", "com_ref_id", "=", "companies.id")
      ->where('com_ref_id', $company_code)
      ->where('trans_type_no', $trans_type)
      ->selectRaw("acc_transactions.id, acc_transactions.trans_type,acc_transactions.voucher_no,
          acc_transactions.voucher_date,  acc_transactions.t_narration,companies.name,
          acc_code,acc_head,acc_origin,d_amount,c_amount")
      ->orderBy('voucher_no', 'desc')->paginate(10);
      $collect = collect($journals);
      return $journals; */

    $journals = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->join('chartofaccounts', function ($join) {
          $join->on('chartofaccounts.id', '=', 'chart_of_acc_id')
             ->where('com_ref_id', '=', $this->com_code);
           })
      ->join('acc_trans_doc_types', function ($join) {
          $join->on('acc_transactions.trans_type', '=', 'doc_type')
             ->where('doc_comp_id', '=', $this->com_code)
             ->where('trans_type_no', '=',   $this->trans_type);
        })
      ->join("companies", "com_ref_id", "=", "companies.id")
      ->where('com_ref_id', $this->com_code)
      ->selectRaw("acc_transactions.id, acc_transactions.trans_type,acc_transactions.voucher_no,
          acc_transactions.voucher_date,  acc_transactions.t_narration,companies.name,
          acc_code,acc_head,acc_origin,d_amount,c_amount,is_billtobill")
      ->orderBy('acc_transaction_details.id', 'desc')->paginate(10);
      $collect = collect($journals);
      return $journals; ;

  }

  public function acc_search($company_code,$trans_type,$fromdate,$todate,$acc_id,$voucherNo)
  {
    $this->com_code = $company_code;
    $this->trans_type = $trans_type;
   /* $q = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->join("chartofaccounts",'chartofaccounts.id', '=', 'chart_of_acc_id')
      ->join("acc_trans_doc_types",'acc_transactions.trans_type', '=', 'doc_type')
      ->join("companies", "com_ref_id", "=", "companies.id")
      ->where('com_ref_id', $company_code)
      ->where('trans_type_no', $trans_type); */

      $q = AccTransactions::query()
         ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
         ->join('chartofaccounts', function ($join) {
             $join->on('chartofaccounts.id', '=', 'chart_of_acc_id')
                ->where('com_ref_id', '=', $this->com_code);
              })
         ->join('acc_trans_doc_types', function ($join) {
             $join->on('acc_transactions.trans_type', '=', 'doc_type')
                ->where('doc_comp_id', '=', $this->com_code)
                ->where('trans_type_no', '=',   $this->trans_type);
           })
         ->join("companies", "com_ref_id", "=", "companies.id")
         ->where('com_ref_id', $this->com_code);

    if($fromdate != '' and $todate == '') $q->whereBetween('acc_transactions.voucher_date', [$fromdate,$fromdate] );
    if($fromdate != '' and $todate != '') $q->whereBetween('acc_transactions.voucher_date', [$fromdate,$todate] );
    if($acc_id != '') $q->where('acc_transaction_details.chart_of_acc_id', $acc_id);
    if($voucherNo != '') $q->where('acc_transactions.voucher_no', $voucherNo);


    $vouchers =  $q->selectRaw("acc_transactions.id, acc_transactions.trans_type,acc_transactions.voucher_no,
      acc_transactions.voucher_date,  acc_transactions.t_narration,companies.name,
      acc_code,acc_head,acc_origin,d_amount,c_amount,is_billtobill")
      ->orderBy('acc_transaction_details.id', 'desc')->paginate(10)->setpath('');

       $vouchers->appends(array(
        'company_code' => $company_code,
        'fromdate'     => $fromdate,
        'todate'      => $todate,
        'acc_id'      => $acc_id,
       ));
     $collect = collect($vouchers);
     return $vouchers;
  }

  public function acctrans_edit($type, $id)
  {
      $trans_type = $type;
      if($type == 1) $title = 'Edit::Journal Voucher';
      if($type == 2) $title = 'Edit::Contra Voucher';
      if($type == 3) $title = 'Edit::Cash Received';
      if($type == 5) $title = 'Edit::Bank Received';
      if($type == 4) $title = 'Edit::Cash Payment';
      if($type == 6) $title = 'Edit::Bank Payment';

      // Gets Master Information-----------------
      $rows_m = $this->modal_view_m($id);
      foreach ($rows_m as $key => $value) {
        $mas['company_code'] = $value->com_ref_id;
        $mas['name']         = $value->name;
        $mas['doc_type']     = $value->trans_type;
        $mas['voucher_no']   = $value->voucher_no;
        $mas['t_narration']  = $value->t_narration;
        $mas['voucher_date'] = date('d-m-Y',strtotime($value->voucher_date));
      }
      $doc_type = $mas['doc_type'];
      $generalscontroller = new GeneralsController();
    //  $trans_type = $generalscontroller->accTransDocTypeNo($doc_type,$mas['company_code']);
     $finan_yearId = $generalscontroller->getActiveFinYearId($mas['company_code']);

      $voucher_no = $doc_type.'-'.$generalscontroller->getMaxAccVoucherNo($doc_type,$mas['company_code'],$finan_yearId);
      $finan_year = $generalscontroller->getCombinFinancialYear($mas['company_code']);
      $acc_list = $this->accheadLookup($mas['company_code'],$trans_type,true);
     // return $acc_list;
      // Gets Details Information-----------------
      $rows_d = $this->modal_view_d($id);
      // get requested action
      return view('/accounts/acctrans_edit',
      compact('id','title','trans_type','finan_year','voucher_no','mas','rows_d','acc_list'));
  }

  public function acctrans_update(Request $request)
  {
    $trans_id     = $request->trans_id;
    $u_voucher_no = $request->u_voucher_no;
    $company_code = $request->company_code;
    $trans_date   = date('Y-m-d',strtotime($request->trans_date));
   // return $request->trans_date;
    $voucher_type = $request->doc_type;
    $trans_type   = $request->transtype;
    $last_voucher = $request->lastVoucher;
    $narration    = $request->narration;

    $total_credit_in = $request->total_credit_in;
    $total_debit_in  = $request->total_debit_in;

    // checking Debit & Crdit equality
    $chkValue = $total_debit_in - $total_credit_in;
    if($chkValue == '0' && $total_debit_in > 0 && $total_credit_in > 0)
    {
        // checking Fin year Declaration
        $generalscontroller = new GeneralsController();
        $yearValidation = $generalscontroller->getFinYearValidation($company_code,$trans_date);
        if($yearValidation) {
          // update Transaction Master Records
          AccTransactions::where('id',$trans_id)->update([
            'voucher_date'  => $trans_date,
            't_narration'   => $request->narration,
          ]);

          // delete old Details Records
          AccTransactionDetails::where('acc_trans_id', $trans_id)->delete();

          // Insert Details Records
          $detId = $request->input('AccHead');
          //dd($detId);
          if ($detId){
            foreach ($detId as $key => $value){
              if ($request->AccHead[$key] != ''){
                if ($trans_type != 1 && $trans_type != 2){
                  AccTransactionDetails::create([
                      'acc_trans_id'    => $trans_id,
                      'd_amount'        => $request->Debit[$key] == '' ? 0:$request->Debit[$key],
                      'c_amount'        => $request->Credit[$key] == '' ? 0:$request->Credit[$key],
                      'chart_of_acc_id' => $request->AccHead[$key],
                      'acc_invoice_no'  => $request->AccInvoice[$key],
                  ]);

                  //update salesinvoice table
                  if ($trans_type == 4 || $trans_type == 6){
                    DB::update("update sales_invoices set inv_gift_paid = '1'
                    where inv_no = ?", [$request->AccInvoice[$key]]);
                  }
                }
                else
                {
                  AccTransactionDetails::create([
                      'acc_trans_id'    => $trans_id,
                      'd_amount'        => $request->Debit[$key] == '' ? 0:$request->Debit[$key],
                      'c_amount'        => $request->Credit[$key] == '' ? 0:$request->Credit[$key],
                      'chart_of_acc_id' => $request->AccHead[$key],
                      'acc_invoice_no'  => 0,
                  ]);
                }
             }
          }
        }

        $voucher = $u_voucher_no;
        return redirect()->back()->with('message','Voucher Updated Successfull >>> '.$voucher)->withInput();
        //return back()->withInput(Input::all());
      }else{
        return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
      }
    }else{
      return back()->with('message','Debit & Credit Does not match')->withInput();
    }
 }

  public function modal_view_m($id)
  {

       $rows_m = AccTransactions::query()
         ->join("companies", "com_ref_id", "=", "companies.id")
         ->where('acc_transactions.id', $id)
         ->selectRaw("com_ref_id,companies.name,trans_type,voucher_no,t_narration,t_narration_1,voucher_date")
         ->get();
        return $rows_m;
  }

  public function modal_view_d($id)
  {

     $rows_d = AccTransactionDetails::query()
      ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
      ->where('acc_trans_id', $id)
      ->selectRaw("chart_of_acc_id,acc_code,acc_head,acc_origin,acc_level,
      d_amount,c_amount,acc_invoice_no")
      ->orderBy('acc_trans_id', 'desc')
      ->get();
    return $rows_d;
  }


    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function acctrans_store(Request $request)
    {
      $generalscontroller = new GeneralsController();
      $company_code = $request->company_code;
      $doctype      = $request->doc_type;
      $trans_type   = $request->transtype;
      $trans_date   = date('Y-m-d',strtotime($request->trans_date));

      $dropdownscontroller = new DropdownsController();
      $accdoctype = $dropdownscontroller->comboAccDocTypeList($trans_type,$company_code); // voucher type list
      $companies  = $dropdownscontroller->comboCompanyAssignList();

      $total_credit_in = $request->total_credit_in;
      $total_debit_in  = $request->total_debit_in;

      // checking Debit & Crdit equality
      $chkValue = $total_debit_in - $total_credit_in;
      if($chkValue == '0' && $total_debit_in > 0 && $total_credit_in > 0)
      {
        // checking Fin year Declaration
        $yearValidation = $generalscontroller->getFinYearValidation($company_code,$trans_date);

        if($yearValidation) {
          $finan_yearId = $generalscontroller->getFinYearId($company_code,$trans_date);
        
         $voucher_no = $generalscontroller->getMaxAccVoucherNo($doctype,$company_code,$finan_yearId); // getting max Voucher No
         $voucher_no = $voucher_no + 1;

        // Insert Transaction Master Records
        $trans_id = AccTransactions::insertGetId([
          'com_ref_id'    => $request->company_code,
          'voucher_date'  => $trans_date,
          'trans_type'    => $request->doc_type,
          'voucher_no'    => $voucher_no,
          't_narration'   => $request->narration,
          'fin_ref_id'    => $finan_yearId,
          'created_by'      => Auth::id(),
          'updated_by'      => Auth::id(),
          'created_at'      => Carbon::now(),
          'updated_at'      => Carbon::now(),
          ]);
        //return $trans_id;

        // Insert Details Records
        $detId = $request->input('AccHead');
        //dd($detId);
        if ($detId){
            foreach ($detId as $key => $value){
              if ($request->AccHead[$key] != ''){
                //$trans_type == 3 & 5 is for Cash received & Bank received
                //$trans_type == 4 & 6 is for Cash payment & Bank payment
                if ($trans_type != 1 && $trans_type != 2){
                  AccTransactionDetails::create([
                      'acc_trans_id'    => $trans_id,
                      'd_amount'        => $request->Debit[$key] == '' ? 0:$request->Debit[$key],
                      'c_amount'        => $request->Credit[$key] == '' ? 0:$request->Credit[$key],
                      'chart_of_acc_id' => $request->AccHead[$key],
                      'acc_invoice_no'  => $request->AccInvoice[$key],
                  ]);

                  //update salesinvoice table 
                  $inv_acc_voucher = $request->doc_type.'-'.$voucher_no;
                  if ($trans_type == 4 || $trans_type == 6){
                    DB::update("update sales_invoices set inv_gift_paid = '1',
                    inv_acc_voucher = '".$inv_acc_voucher."' where inv_no = ?", [$request->AccInvoice[$key]]);
                  } 

                }
                else
                {
                  AccTransactionDetails::create([
                      'acc_trans_id'    => $trans_id,
                      'd_amount'        => $request->Debit[$key] == '' ? 0:$request->Debit[$key],
                      'c_amount'        => $request->Credit[$key] == '' ? 0:$request->Credit[$key],
                      'chart_of_acc_id' => $request->AccHead[$key],
                      'acc_invoice_no'  => 0,
                  ]);
                }
              }
            }
        }

        $voucher = $request->doc_type .'-'. $voucher_no;
        return redirect()->back()->with('message','Voucher Created Successfull >>> '.$voucher)->withInput();
        //return back()->withInput(Input::all());
      }else{
        return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
      }
    }else{
      return back()->with('message','Debit & Credit Does not match')->withInput();
    }
      //return redirect()->route('acctrans.jv.index')->with('message','Journal Created Successfull >>> '.$voucher);
  }


  /*public function accInvoiceLookup($compcode,$accid,$transtype,$edit=NULL)
  {
    $sql = "SELECT count(*) as rec FROM (
    SELECT @a AS _id,
    (SELECT @a := parent_id FROM chartofaccounts WHERE id = _id) AS parent_id
     FROM
    (SELECT @a := $accid) vars, chartofaccounts h
    WHERE @a <> 0) MAIN INNER JOIN settings on parent_id = settings.sett_accid
    inner join settings_categories on settings_categories.id = sett_mapped
    Where sett_cat_name = 'GIFT' AND sett_comp_id = $compcode";
    $is_gift = collect(\DB::select($sql))->first();
  //  $is_gift = DB::select($sql)->first();

    if ($is_gift->rec == 0 and ($transtype == 3 || $transtype == 5)) { 
      $sql = "SELECT IFNULL(acc_invoice_no,0) as acc_invoice_no,inv_date,sum(d_amount) as dr,
      sum(c_amount) as cr, sum(d_amount) - sum(c_amount) as balance
      FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      LEFT JOIN sales_invoices on sales_invoices.inv_no = acc_invoice_no AND fin_ref_id=inv_fin_year_id
      Where trans_type in ('JV','SV','CR','BR') and chart_of_acc_id = $accid
      GROUP BY acc_invoice_no,inv_date having (sum(d_amount) - sum(c_amount)) > 0";
      
    }else if ($is_gift->rec > 0 and ($transtype == 4 || $transtype == 6)) {
      $sql = "SELECT inv_no as acc_invoice_no,inv_date,inv_net_amt as balance
      FROM sales_invoices where inv_gift_paid = 0";
      
    }else {
      $sql = "SELECT 0 as acc_invoice_no,0 as balance ";
    }
  //return $sql;

    $data = DB::select($sql);
    if($edit) return $data;
    else return response()->json($data);
  }
  
  public function accInvoiceBalanceLookup($compcode,$accid,$invoiceno,$edit=NULL)
  {

      $sql = "SELECT acc_invoice_no,sum(d_amount) as dr,sum(c_amount) as cr,
      sum(d_amount) - sum(c_amount) as balance  FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      Where trans_type in ('SV','CR','BR','JV') and chart_of_acc_id = $accid
      and acc_invoice_no = $invoiceno  GROUP BY acc_invoice_no";
      //return $sql;
      $data = DB::select($sql);
      if($edit) return $data;
      else return response()->json($data);
  }*/

  public function accPurchaseBalanceLookup($compcode,$accid,$invoiceno,$edit=NULL)
  {
      if ($invoiceno == 0){
        $sql = "SELECT acc_invoice_no,0 as dr,0 as cr,0 as balance
        FROM acc_transactions
        INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
        Where trans_type in ('PV','CP','BP') and chart_of_acc_id = $accid
        and acc_invoice_no = $invoiceno  GROUP BY acc_invoice_no";
      }else{
        $sql = "SELECT acc_invoice_no,sum(d_amount) as dr,sum(c_amount) as cr,
        sum(c_amount)-sum(d_amount) as balance  FROM acc_transactions
        INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
        Where trans_type in ('PV','CP','BP') and chart_of_acc_id = $accid
        and acc_invoice_no = $invoiceno  GROUP BY acc_invoice_no";
      }

      //return $sql;
      $data = DB::select($sql);
      if($edit) return $data;
      else return response()->json($data);
  }

  public function accInvoiceBalanceLookup($compcode,$accid,$invoiceno,$edit=NULL)
  {

    if ($invoiceno == 0){
      $sql = "SELECT acc_invoice_no,0 as dr,0 as cr, 0 as balance
      FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      Where trans_type in ('SV','CR','BR','JV') and chart_of_acc_id = $accid
      and acc_invoice_no = $invoiceno  GROUP BY acc_invoice_no";
    }else{
      $sql = "SELECT acc_invoice_no,sum(d_amount) as dr,sum(c_amount) as cr,
      sum(d_amount) - sum(c_amount) as balance  FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      Where trans_type in ('SV','CR','BR','JV') and chart_of_acc_id = $accid
      and acc_invoice_no = $invoiceno  GROUP BY acc_invoice_no";
    }


      //return $sql;
      $data = DB::select($sql);
      if($edit) return $data;
      else return response()->json($data);
  }

  public function accInvoiceLookup($compcode,$accid,$transtype,$edit=NULL)
  {

    $sql = "SELECT count(*) as rec FROM (
    SELECT @a AS _id,
    (SELECT @a := parent_id FROM chartofaccounts WHERE id = _id) AS parent_id
     FROM
    (SELECT @a := $accid) vars, chartofaccounts h
    WHERE @a <> 0) MAIN INNER JOIN settings on parent_id = settings.sett_accid
    inner join settings_categories on settings_categories.id = sett_mapped
    Where sett_cat_name = 'GIFT' AND sett_comp_id = $compcode";
    $is_gift = collect(\DB::select($sql))->first();
  //  $is_gift = DB::select($sql)->first();

    if ($is_gift->rec == 0 and ($transtype == 3 || $transtype == 5)) {
      /*$sql = "SELECT IFNULL(acc_invoice_no,0) as acc_invoice_no,sum(d_amount) as dr,sum(c_amount) as cr,
      sum(d_amount) - sum(c_amount) as balance
      FROM acc_transaction_details where chart_of_acc_id = $accid
      GROUP BY acc_invoice_no having (sum(d_amount) - sum(c_amount)) > 0";

      $sql = "SELECT IFNULL(acc_invoice_no,0) as acc_invoice_no,inv_date,sum(d_amount) as dr,
      sum(c_amount) as cr, sum(d_amount) - sum(c_amount) as balance
      FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      LEFT JOIN sales_invoices on sales_invoices.inv_no = acc_invoice_no
      Where trans_type in ('JV','SV','CR','BR') and chart_of_acc_id = $accid
      GROUP BY acc_invoice_no,inv_date having (sum(d_amount) - sum(c_amount)) > 0"; */
      
      $sql = "SELECT * FROM 
      (SELECT IFNULL(acc_invoice_no,0) as acc_invoice_no,inv_date,sales_invoices.id as sal_inv_id,
      sum(d_amount) as dr, sum(c_amount) as cr, sum(d_amount) - sum(c_amount) as balance 
      FROM acc_transactions 
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id 
      LEFT JOIN sales_invoices on sales_invoices.inv_no = acc_invoice_no  
      Where trans_type in ('JV','SV','CR','BR') 
      and chart_of_acc_id = $accid GROUP BY acc_invoice_no,inv_date,sales_invoices.id
      having (sum(d_amount) - sum(c_amount)) > 0 ) MAIN 
      LEFT JOIN (SELECT ret_sal_inv_id, SUM(ret_net_amt) AS RET_AMT FROM sales_returns 
      GROUP BY ret_sal_inv_id) AS RET ON MAIN.sal_inv_id = RET.ret_sal_inv_id";

    }else if ($is_gift->rec == 0 and ($transtype == 4 || $transtype == 6)) {
      $sql = "SELECT IFNULL(acc_invoice_no,0) as acc_invoice_no,pur_order_date as inv_date,sum(d_amount) as dr,
      sum(c_amount) as cr, sum(c_amount)-sum(d_amount) as balance
      FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      LEFT JOIN item_purchases on item_purchases.pur_order_no = acc_invoice_no
      Where trans_type in ('PV') and chart_of_acc_id = $accid
      GROUP BY acc_invoice_no,pur_order_date having ( sum(c_amount)-sum(d_amount)) > 0";
    }else if ($is_gift->rec > 0) {
      $sql = "SELECT inv_no as acc_invoice_no,inv_date,inv_net_amt as balance
      FROM sales_invoices where inv_gift_paid = 0";
    }else {
      $sql = "SELECT 0 as acc_invoice_no,0 as balance ";
    }
  //return $sql;

    $data = DB::select($sql);
    if($edit) return $data;
    else return response()->json($data);
  }
  
  public function accheadLookup($compcode,$transtype,$edit=NULL)
  {
    $dropdownscontroller = new DropdownsController();
    $acc_head_id = $dropdownscontroller->comboAccHeadCateList($transtype,$compcode);
    $acc_head_id = $acc_head_id->acc_id;
    $myArray = explode(',', $acc_head_id);
    $count = count($myArray);

    $q = '';
    if ( $count > 0 ) {
      $q = '(';
      for($i = 0; $i < $count; $i++) {
        if($i == 0){
          $q .= " c.acc_code not like '".$myArray[$i]."%'";
        } else if($i != 0) {
          $q .= " and c.acc_code not like '".$myArray[$i]."%'";
        }
      }
      $q .= ')';
    }

    /*$sql = "select id,acc_head from `chartofaccounts`
    where `company_id` = $compcode and `id` not in (select `parent_id` from `chartofaccounts`)
    and ".$q." Order By acc_head asc";*/

    $sql = "select c.id,c.acc_head,p.acc_head as p_acc_head from `chartofaccounts` c
    left join chartofaccounts p on c.parent_id = p.id
    where c.`company_id` = $compcode and c.`id` not in (select `parent_id` from `chartofaccounts`)
    and ".$q." Order By acc_head asc";

    $data = DB::select($sql);
    if($edit) return $data;
    else return response()->json($data);
  }

  public function getAccHead(Request $request)
  {
      $acc_type = $request->get('acc_type');
      $compcode = $request->get('compcode');

      $dropdownscontroller = new DropdownsController();
      $acc_head_id = $dropdownscontroller->comboAccHeadCateList($acc_type,$compcode);
      $acc_head_id = $acc_head_id->acc_id;
      $myArray = explode(',', $acc_head_id);
      $count = count($myArray);

      /* $sql = Chartofaccounts::where('company_id', '=', $request->get('compcode'));
      $sql->where('acc_head', 'LIKE', '%'.$request->get('item').'%');

      if ( $count > 0 ) {
        for($i = 0; $i < $count; $i++) {
          $sql->where('acc_code', 'NOT LIKE', $myArray[$i].'%');
        }
      }

      $sql->whereNotIn('id', function($q){
          $q->select('parent_id')->from('chartofaccounts');
      });
      $data = $sql->get();*/

      $q = '';
      if ( $count > 0 ) {
        $q = '(';
        for($i = 0; $i < $count; $i++) {
          if($i == 0){
            $q .= " acc_code not like '".$myArray[$i]."%'";
          } else if($i != 0) {
            $q .= " and acc_code not like '".$myArray[$i]."%'";
          }
        }
        $q .= ')';
      }

      $sql = "select * from `chartofaccounts`
      where `company_id` = $compcode and `id` not in (select `parent_id` from `chartofaccounts`) and
      acc_head like '%".$request->get('item')."%' and ".$q." Order By acc_head asc";
      $data = DB::select($sql);
      return AccHeadResource::collection($data);
  }

  public function ghani($id){
      return new TransAccHeadResource(Chartofaccounts::where('id',$id)->with('childs')
      ->first());
  }
  
  public function acctrans_checking()
  {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $this->com_code = $dropdownscontroller->defaultCompanyCode();
      /*$rows = AccTransactions::query()
        ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
        ->join('acc_trans_doc_types', function ($join) {
            $join->on('acc_transactions.trans_type', '=', 'doc_type')
               ->where('doc_comp_id', '=', $this->com_code);
            })
        ->where('com_ref_id', $this->com_code)
        ->selectRaw('acc_transactions.id,acc_transactions.trans_type,voucher_no,trans_type_no,SUM(d_amount) as DR,SUM(c_amount) AS CR,
          SUM(d_amount)-SUM(c_amount) as DIFF')
        ->groupBy('acc_transactions.id','acc_transactions.trans_type','voucher_no','trans_type_no')
        ->havingRaw('SUM(d_amount)-SUM(c_amount) <> 0')
        ->get();*/
        
        $sql = "SELECT id,trans_type,voucher_no,trans_type_no,SUM(acc_invoice_no) as acc_invoice_no,
      SUM(DR) as DR,SUM(CR) AS CR, SUM(DIFF) as DIFF FROM (
      select acc_transactions.id as id,acc_transactions.trans_type as trans_type,voucher_no,trans_type_no,IFNULL(acc_invoice_no,0) as acc_invoice_no,
      SUM(d_amount) as DR,SUM(c_amount) AS CR, SUM(d_amount)-SUM(c_amount) as DIFF
      from `acc_transactions` inner join `acc_transaction_details` on `acc_transactions`.`id` = `acc_trans_id`
      left join `sales_invoices` on inv_no = acc_invoice_no
      inner join `acc_trans_doc_types` on `acc_transactions`.`trans_type` = `doc_type` and `doc_comp_id` = $this->com_code
      where `com_ref_id` = $this->com_code
      group by `acc_transactions`.`id`, `acc_transactions`.`trans_type`, `voucher_no`, `trans_type_no`,acc_invoice_no ) MAIN
      group by id,trans_type,voucher_no,trans_type_no having SUM(DR)-SUM(CR) <> 0 order by trans_type,voucher_no asc";
      $rows = DB::select($sql);
      
       return view ('/accounts/trans_check_index', compact('rows'));

    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try{
            AccTransactionDetails::where('acc_trans_id',$id)->delete();
            AccTransactions::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
