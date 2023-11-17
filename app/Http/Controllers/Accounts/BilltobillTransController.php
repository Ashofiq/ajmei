<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;

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
use App\Models\FinancialYearDeclaration;

use App\Http\Resources\AccHeadResource;
use App\Http\Resources\TransAccHeadResource;
use DB;
use Response;

class BilltobillTransController extends Controller
{

  public $trans_type;  // Cash & Bank Received
  public $title = 'Bill To Bill Received';

  public function billtobill_cash_create()
  {
      $this->trans_type = 3; // Cash Received
      $trans_date = $this->getDateAttribute(date('d-m-Y'));
      $trans_type = $this->trans_type;
      $mytrans_type = explode(',', $trans_type);
      $title = "Bill To Bill Cash Received";
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $accdoctype   = $dropdownscontroller->comboAccDocTypeList1($mytrans_type, $company_code); // voucher type list 1 is for JV
      foreach ($accdoctype as $value) {
          $doctype =  $value->doc_type;
      }
      $accounttranscontroller = new AccountTransController();
      $accounthead = $accounttranscontroller->accheadLookup($company_code,$trans_type,"yes");

      $generalscontroller = new GeneralsController(); 
      $finan_yearId = $generalscontroller->getActiveFinYearId($company_code);
      $voucher_no = $doctype.'-'.$generalscontroller->getMaxAccVoucherNo($doctype,$company_code,$finan_yearId);
      $finan_year = $generalscontroller->getCombinFinancialYear($company_code);
      // get requested action
      return view('/accounts/billtobill_create',
      compact('title','accounthead','trans_type','trans_date','companies','accdoctype','company_code','voucher_no','finan_year'));
  }

  public function billtobill_bank_create()
  {
      $this->trans_type = 5; // Cash Received
      $trans_date = $this->getDateAttribute(date('d-m-Y'));
      $trans_type = $this->trans_type;
      $mytrans_type = explode(',', $trans_type);
      $title = "Bill To Bill Bank Received";
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $accdoctype   = $dropdownscontroller->comboAccDocTypeList1($mytrans_type, $company_code); // voucher type list 1 is for JV
      foreach ($accdoctype as $value) {
          $doctype =  $value->doc_type;
      }

      //$accounttranscontroller = new AccountTransController();
      $accounthead = $this->accheadLookup($company_code,$trans_type,"yes");
      $bankaccounthead = $this->bankaccheadLookup($company_code,$trans_type-1,"yes");
    //  return $bankaccounthead;
      $generalscontroller = new GeneralsController();
      $finan_yearId = $generalscontroller->getActiveFinYearId($company_code);
      $voucher_no = $doctype.'-'.$generalscontroller->getMaxAccVoucherNo($doctype,$company_code,$finan_yearId);
      $finan_year = $generalscontroller->getCombinFinancialYear($company_code);
      // get requested action
      return view('/accounts/billtobill_create',
      compact('title','accounthead','bankaccounthead','trans_type','trans_date','companies','accdoctype','company_code','voucher_no','finan_year'));
  }

  public function billtobill_store(Request $request)
  {
    $company_code = $request->company_code;
    $trans_type   = $request->transtype;
    $doctype      = $request->doc_type;
    $trans_date   = date('Y-m-d',strtotime($request->trans_date));

    $dropdownscontroller = new DropdownsController(); 
    $accdoctype = $dropdownscontroller->comboAccDocTypeList($trans_type,$company_code); // voucher type list
    $companies  = $dropdownscontroller->comboCompanyAssignList();

    $generalscontroller = new GeneralsController();
    $finan_yearId = $generalscontroller->getActiveFinYearId($company_code);
   // $doctype    = $generalscontroller->getAccTransDocType($trans_type,$company_code);
    $voucher_no = $generalscontroller->getMaxAccVoucherNo($doctype,$company_code,$finan_yearId); // getting max Voucher No
    $voucher_no = $voucher_no + 1;
    $param = '';
    if ($trans_type == 3){
      $param = 'Cash In%';
      $chart_of_acc_id  = $generalscontroller->getChartofAccId($company_code,$param);
    }else{
      $chart_of_acc_id  = $request->BankAccountHead;
    }

    $total_credit_in = $request->total_credit_in;
    $total_debit_in  = $request->total_debit_in;
    //return $total_credit_in .'-'.$chart_of_acc_id;
    if($total_credit_in >0 && $chart_of_acc_id > 0)
    {
      // checking Fin year Declaration
      $yearValidation = $generalscontroller->getFinYearValidation($company_code,$trans_date);

      if($yearValidation) {
        $finan_yearId = $generalscontroller->getFinYearId($company_code,$trans_date);
      // Insert Transaction Master Records
      $trans_id = AccTransactions::insertGetId([
        'com_ref_id'    => $request->company_code,
        'voucher_date'  => $trans_date,
        'trans_type'    => $doctype,
        'voucher_no'    => $voucher_no,
        't_narration'   => $request->narration,
        'fin_ref_id'    => $finan_yearId,
        ]);
      //return $trans_id;

      // Insert Details Records
      $detId = $request->input('AccInvoiceNo');
      //dd($detId);
      if ($detId){
          foreach ($detId as $key => $value){
            if ($request->AccInvoiceNo[$key] != ''){
              if ($request->Credit[$key] > 0){
              //$trans_type == 3 & 5 is for Cash received & Bank received
              //$trans_type == 4 & 6 is for Cash payment & Bank payment
                AccTransactionDetails::create([
                    'acc_trans_id'    => $trans_id,
                    'c_amount'        => $request->Credit[$key],
                    'chart_of_acc_id' => $request->AccountHead,
                    'acc_invoice_no'  => $request->AccInvoiceNo[$key],
                ]);
            }
          }
          }
      }
      AccTransactionDetails::create([
          'acc_trans_id'    => $trans_id,
          'd_amount'        => $total_credit_in,
          'chart_of_acc_id' => $chart_of_acc_id,
          'acc_invoice_no' => 0,
      ]);

      $voucher = $request->doctype .'-'. $voucher_no;
      return redirect()->back()->with('message','Voucher Created Successfull >>> '.$voucher)->withInput();
      //return back()->withInput(Input::all());
    }else{
      return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
    }
  }else{
    return back()->with('message','Total Credit Value Can not Zero(0.00)/Account Head Not maintain properly')->withInput();
  }
    //return redirect()->route('acctrans.jv.index')->with('message','Journal Created Successfull >>> '.$voucher);
}


public function billtobill_jv_create()
{
    $trans_date = $this->getDateAttribute(date('d-m-Y'));
    $trans_type = 1;
    $title    = $this->title;
    $dropdownscontroller = new DropdownsController();
    $companies    = $dropdownscontroller->comboCompanyAssignList();
    $company_code = $dropdownscontroller->defaultCompanyCode();
    $accdoctype   = $dropdownscontroller->comboAccDocTypeList($trans_type, $company_code); // voucher type list 1 is for JV
    foreach ($accdoctype as $value) {
        $doctype =  $value->doc_type;
    }
    $generalscontroller = new GeneralsController();
    $finan_yearId = $generalscontroller->getActiveFinYearId($company_code);
    $voucher_no = $doctype.'-'.$generalscontroller->getMaxAccVoucherNo($doctype,$company_code,$finan_yearId);
    $finan_year = $generalscontroller->getCombinFinancialYear($company_code);
    // get requested action
    return view('/accounts/billtobill_jv_create',
    compact('title','trans_type','trans_date','companies','accdoctype','company_code','voucher_no','finan_year'));
}

public function accInvoiceJVLookup($compcode,$accid,$transtype,$edit=NULL)
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

  if ($is_gift->rec == 0 and $transtype == 1 ) {
    /*$sql = "SELECT IFNULL(acc_invoice_no,0) as acc_invoice_no,sum(d_amount) as dr,sum(c_amount) as cr,
    sum(d_amount) - sum(c_amount) as balance
    FROM acc_transaction_details where chart_of_acc_id = $accid
    GROUP BY acc_invoice_no having (sum(d_amount) - sum(c_amount)) > 0";*/

    $sql = "SELECT IFNULL(acc_invoice_no,0) as acc_invoice_no,inv_date,sum(d_amount) as dr,
    sum(c_amount) as cr, sum(d_amount) - sum(c_amount) as balance
    FROM acc_transactions
    INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
    LEFT JOIN sales_invoices on sales_invoices.inv_no = acc_invoice_no
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


public function billtobill_jv_store(Request $request)
{
  $company_code = $request->company_code;
  $doctype      = $request->doc_type;
  $trans_type   = $request->transtype;
  $trans_date   = date('Y-m-d',strtotime($request->trans_date));

  $dropdownscontroller = new DropdownsController();
  $accdoctype = $dropdownscontroller->comboAccDocTypeList($trans_type,$company_code); // voucher type list
  $companies  = $dropdownscontroller->comboCompanyAssignList();

  $generalscontroller = new GeneralsController();
  $finan_yearId = $generalscontroller->getActiveFinYearId($company_code);
  $voucher_no = $generalscontroller->getMaxAccVoucherNo($doctype,$company_code,$finan_yearId); // getting max Voucher No
  $voucher_no = $voucher_no + 1;


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
    // Insert Transaction Master Records
    $trans_id = AccTransactions::insertGetId([
      'com_ref_id'    => $request->company_code,
      'voucher_date'  => $trans_date,
      'trans_type'    => $request->doc_type,
      'voucher_no'    => $voucher_no,
      't_narration'   => $request->narration,
      'fin_ref_id'    => $finan_yearId,
      'is_billtobill' => 1,
      ]);
    //return $trans_id;

    // Insert Details Records
    $detId = $request->input('AccHead');
    //dd($detId);
    if ($detId){
        foreach ($detId as $key => $value){
          if ($request->AccHead[$key] != ''){
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

public function billtobill_jv_edit($type, $id)
{
    $trans_type = $type;
    if($type == 1) $title = 'Edit::Journal Voucher';


    // Gets Master Information-----------------
    $accounttranscontroller = new AccountTransController();
    $rows_m = $accounttranscontroller->modal_view_m($id);
    foreach ($rows_m as $key => $value) {
      $mas['company_code'] = $value->com_ref_id;
      $mas['name']         = $value->name;
      $mas['doc_type']     = $value->trans_type;
      $mas['voucher_no']   = $value->voucher_no;
      $mas['t_narration']  = $value->t_narration;
      $mas['voucher_date'] = date('d-m-Y',strtotime($value->voucher_date));
    }
    $doc_type = $mas['doc_type'];
    $voucher_date = date('d-m-Y',strtotime($value->voucher_date));
    $generalscontroller = new GeneralsController();
  //  $trans_type = $generalscontroller->accTransDocTypeNo($doc_type,$mas['company_code']);
    $finan_yearId = $generalscontroller->getActiveFinYearId($mas['company_code']);
    $voucher_no = $doc_type.'-'.$generalscontroller->getMaxAccVoucherNo($doc_type,$mas['company_code'],$finan_yearId);
    $finan_year = $generalscontroller->getCombinFinancialYear($mas['company_code']);
    $acc_list = $this->accheadLookup($mas['company_code'],$trans_type,true);
    //$acc_list = $this->accInvoiceLookup($mas['company_code'],$trans_type,true);

    //accInvoiceLookup($compcode,$accid,$transtype,$edit=NULL)

   // return $acc_list;
    // Gets Details Information-----------------
    $rows_d = $accounttranscontroller->modal_view_d($id);
    // get requested action
    return view('/accounts/billtobill_jv_edit',
    compact('id','title','trans_type','finan_year','voucher_no','mas','rows_d','acc_list'));
}

public function billtobill_jv_update(Request $request)
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

public function bankaccheadLookup($compcode,$transtype,$edit=NULL)
{
  $dropdownscontroller = new DropdownsController();
  $acc_head_id = $dropdownscontroller->comboAccHeadCateList($transtype,$compcode);
  $acc_head_id = $acc_head_id->acc_id;
  $myArray = explode(',', $acc_head_id);
  $count = count($myArray);

  $q = '';
  if ( $count > 0 and $transtype) {
    $q = '(';
    for($i = 0; $i < $count; $i++) {
      if($i == 0){
        $q .= " c.acc_code like '".$myArray[$i]."%'";
      } else if($i != 0) {
        $q .= " and c.acc_code like '".$myArray[$i]."%'";
      }
    }
    $q .= ')';
  }

  $sql = "select c.id,c.acc_head,p.acc_head as p_acc_head from `chartofaccounts` c
  left join chartofaccounts p on c.parent_id = p.id
  where c.`company_id` = $compcode and c.`id` not in (select `parent_id` from `chartofaccounts`)
  and ".$q." Order By acc_head asc";

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
  if ( $count > 0 and $transtype) {
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
  if ( $count > 0 and $transtype == 5) {
    $Bank= $transtype + 1;
    $q .= " and  c.acc_code not like '".$Bank."%'";
  }

  $sql = "select c.id,c.acc_head,p.acc_head as p_acc_head from `chartofaccounts` c
  left join chartofaccounts p on c.parent_id = p.id
  where c.`company_id` = $compcode and c.`id` not in (select `parent_id` from `chartofaccounts`)
  and ".$q." Order By acc_head asc";

  $data = DB::select($sql);
  if($edit) return $data;
  else return response()->json($data);
}

  public function getDateAttribute($value)
  {
        return Carbon::parse($value)->format('Y-m-d');
  }


}
