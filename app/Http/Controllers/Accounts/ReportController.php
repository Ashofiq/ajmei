<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;
use App\Http\Controllers\Accounts\AccountTransController;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use Carbon\Carbon;

use App\Models\CompaniesAssigns;
use App\Models\AccTransactions;
use App\Models\AccTransactionDetails;
use App\Models\Chartofaccounts;
use App\Models\Customers\Customers;

use App\Http\Resources\AccHeadResource;
use App\Http\Resources\TransAccHeadResource;
use PDF;
use Response;
use Helper;

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {
    error_reporting(0);
   }

   public function getVoucherReport(Request $request)
   {

     $dropdownscontroller = new DropdownsController();
     $default_comp_code = $dropdownscontroller->defaultCompanyCode();
     $companies  = $dropdownscontroller->comboCompanyAssignList();
     $acc_doctypes = $dropdownscontroller->comboAcc_DocTypeList($default_comp_code);

     $voucher_type = '';
     $fromdate = date('Y-m-d'); 
     $todate = date('Y-m-d');

     if($request->filled('company_code')
      && $request->filled('fromdate') && $request->filled('todate')){ 
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $default_comp_code = $request->input('company_code');

        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
      }
      if ($request->filled('voucher_type')) {
        $voucher_type = $request->input('voucher_type');
      }

      $q = AccTransactions::query()
           ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
           ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
           ->whereBetween('acc_transactions.voucher_date', [$fromdate,$todate])
           ->where('com_ref_id', $default_comp_code );
      if ($voucher_type != '') {
           $q->where('trans_type', $voucher_type );
      }
      $vouchers = $q->selectRaw("trans_type,voucher_no,t_narration,voucher_date,acc_head,d_amount,c_amount,acc_invoice_no")
           ->orderBy('voucher_date', 'asc')->get();

      if ($request->input('submit') == "pdf"){
          
        $fileName = 'voucher_list';
          $pdf = PDF::loadView('/accounts/reports/rpt_voucher_list_pdf',
          compact('comp_name','comp_add','vouchers','companies','fromdate', 'todate','voucher_type',), [], [
            'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
      
      return view('/accounts/reports/rpt_voucher_list_pdf', compact('comp_name','comp_add','vouchers','companies','fromdate', 'todate','voucher_type'));
      
     }

     $collect = collect($vouchers);
     // get requested action
     return view('/accounts/reports/rpt_voucher_list', compact('acc_doctypes','vouchers','companies','default_comp_code','fromdate', 'todate','voucher_type'));
   }

   public function getSubsidiaryLedger(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
     $companies = $dropdownscontroller->comboCompanyAssignList();
     
     /*$sql = "select * from `chartofaccounts`
     where `company_id` = $default_comp_code and `id` not in (select `parent_id` from `chartofaccounts`)
     Order By acc_head asc";*/
     
     $sql = "select c.id,c.acc_head,p.acc_head as p_acc_head from `chartofaccounts` c
     left join chartofaccounts p on c.parent_id = p.id
     where c.`company_id` = $default_comp_code and c.`id` not in (select `parent_id` from `chartofaccounts`)
     Order By acc_head asc";
     
     $ledgers = DB::select($sql);
     
     $ledger_id = 0;
     $ledgername = '';
     $acc_Ledger = '';
     $fromdate  = date('Y-m-d');
     $todate    = date('Y-m-d');
     $cust_data = '';
     if($request->filled('company_code') && $request->filled('ledger_id')
      && $request->filled('fromdate') && $request->filled('todate')){ 
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $ledger_id  = $request->input('ledger_id');
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
        $ledgername = $generalscontroller->accountNameLookup($ledger_id);
        $cust_data = $generalscontroller->getCustomerInfByLedgerId($ledger_id);
      }

      //ledger opening balance
      $opening = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->selectRaw('sum(d_amount) as debit, sum(c_amount) as credit')
      ->where('com_ref_id', $default_comp_code )
      ->where('chart_of_acc_id', $ledger_id )
      ->whereDate('voucher_date','<' ,date('Y-m-d', strtotime($fromdate)))->first();

      $rows = AccTransactions::query()
        ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
        ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
        ->whereBetween('voucher_date', [$fromdate,$todate])
        ->where('com_ref_id', $default_comp_code )
        ->where('chart_of_acc_id', $ledger_id )
        ->selectRaw('trans_type,voucher_no,voucher_date,t_narration, t_narration_1, d_amount,c_amount,acc_invoice_no')
        ->orderBy('voucher_date', 'asc')->get();

        // if($request->filled('fromdate')){
        //     return $rows;
        // }
        
      if ($request->input('submit') == "pdf"){
            $fileName = 'subsidiary_ledger';

            $pdf = PDF::loadView('/accounts/reports/rpt_sub_ledger_list_pdf',
            compact('comp_name','comp_add','rows','opening','companies','default_comp_code','ledgername','cust_data','fromdate', 'todate',), [], [
              'title' => $fileName,
            ]); 
        return $pdf->stream($fileName,'.pdf');
       }
 
     // get requested action
     return view('/accounts/reports/rpt_sub_ledger_list', compact('rows','opening','companies','default_comp_code','ledgers','ledger_id','cust_data','fromdate', 'todate'));

   }

   public function getSubsidiaryLedger1($sdate,$compid,$custid)
   {
     $generalscontroller = new GeneralsController();
     $default_comp_code  = $compid;
     $ledger_id = $generalscontroller->CustomerChartOfAccId($custid);
     $fromdate  = date('Y-m-d',strtotime($sdate));
     $todate    = date('Y-m-d');

     $sql = "select c.id,c.acc_head,p.acc_head as p_acc_head from `chartofaccounts` c
     left join chartofaccounts p on c.parent_id = p.id
     where c.`company_id` = $default_comp_code and c.`id` not in (select `parent_id` from `chartofaccounts`)
     Order By acc_head asc";

     $ledgers = DB::select($sql);
     $comp_name = $generalscontroller->CompanyName($default_comp_code);
     $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
     $ledgername = $generalscontroller->accountNameLookup($ledger_id);
     $cust_data = $generalscontroller->getCustomerInfByLedgerId($ledger_id);

      //ledger opening balance
      $opening = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->selectRaw('sum(d_amount) as debit, sum(c_amount) as credit')
      ->where('com_ref_id', $default_comp_code )
      ->where('chart_of_acc_id', $ledger_id )
      ->whereDate('voucher_date','<' ,date('Y-m-d', strtotime($fromdate)))->first();

      $rows = AccTransactions::query()
        ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
        ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
        ->whereBetween('voucher_date', [$fromdate, $todate])
        ->where('com_ref_id', $default_comp_code )
        ->where('chart_of_acc_id', $ledger_id )
        ->selectRaw('trans_type,voucher_no,voucher_date,t_narration,d_amount,c_amount')
        ->orderBy('voucher_date', 'asc')->get();

        return view('/accounts/reports/rpt_sub_ledger_list_pdf',
        compact('comp_name','comp_add','rows','opening','default_comp_code',
        'ledgername','cust_data','fromdate', 'todate'));
   }
   
   public function getConSubsidiaryLedger(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
     $companies = $dropdownscontroller->comboCompanyAssignList();
     $company_code = $default_comp_code;
     
     /*$sql = "select * from `chartofaccounts`
     where `company_id` = $company_code and `parent_id` = 0
     Order By acc_head asc"; */
     
     $sql = "select distinct p.id as id,p.acc_head as acc_head from `chartofaccounts` c
     inner join chartofaccounts p on p.id = c.parent_id
     where c.`company_id` = $company_code
     and c.`id` not in (select `parent_id` from `chartofaccounts`)
     Order By p.acc_head asc";
     
     $conledgers = DB::select($sql);
     $ledger_id = 0;
     $acc_Ledger = '';
     $fromdate  = date('Y-m-d');
     $todate    = date('Y-m-d');

     if($request->filled('company_code') && $request->filled('ledger_id')
      && $request->filled('fromdate') && $request->filled('todate')){
        $company_code = $request->input('company_code');
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $ledger_id  = $request->input('ledger_id');
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
      }

      $sql = "SELECT acc_head, SUM(op_d_amount) as op_debit,SUM(op_c_amount) as op_credit ,
      SUM(t_d_amount) as tr_debit,SUM(t_c_amount) as tr_credit
      FROM
      (SELECT c.acc_head as acc_head,SUM(d_amount) as op_d_amount,SUM(c_amount) as op_c_amount, 0 as t_d_amount,0 as t_c_amount
      FROM acc_transactions t
      INNER JOIN acc_transaction_details on t.id = acc_trans_id
      INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      inner join chartofaccounts p on p.id = c.parent_id
      Where com_ref_id =  $company_code and c.parent_id = ".$ledger_id." AND voucher_date < '". date('Y-m-d', strtotime($fromdate))."'
      GROUP BY c.acc_head
      UNION ALL
      SELECT c.acc_head as acc_head,0 as op_d_amount,0 as op_c_amount,SUM(d_amount) as t_d_amount,SUM(c_amount) as t_c_amount
      FROM acc_transactions t
      INNER JOIN acc_transaction_details on t.id = acc_trans_id
      INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      inner join chartofaccounts p on p.id = c.parent_id
      Where com_ref_id =  $company_code and c.parent_id = ".$ledger_id." AND voucher_date BETWEEN '". date('Y-m-d', strtotime($fromdate))."' and '".date('Y-m-d', strtotime($todate))."'
      GROUP BY c.acc_head ) as M GROUP BY acc_head";

    
      $rows = DB::select($sql);

      if ($request->input('submit') == "pdf"){
            $fileName = 'subsidiary_ledger';

            $pdf = PDF::loadView('/accounts/reports/rpt_con_sub_ledger_list_pdf',
            compact('comp_name','comp_add','rows','companies','default_comp_code','fromdate', 'todate',), [], [
              'title' => $fileName,
            ]);
        return $pdf->stream($fileName,'.pdf');
       }
       
       if ($request->input('submit') == "pdf2"){
             $fileName = 'subsidiary_ledger';

           $pdf = PDF::loadView('/accounts/reports/rpt_con_sub_ledger_trans_list_pdf',
             compact('comp_name','comp_add','rows','companies','default_comp_code','fromdate',
              'todate',), [], [
               'title' => $fileName,
             ]);
         return $pdf->stream($fileName,'.pdf');

        }
        

     // get requested action
     return view('/accounts/reports/rpt_con_sub_ledger_list', compact('rows','companies','default_comp_code','conledgers','ledger_id','fromdate', 'todate'));

   }

   public function getTrialBalanceSql($company_code, $fromdate, $todate)
   {
      //  $sql = "SELECT parent_id, p_acc_head,acc_head, acc_code, SUM(op_d_amount) as op_debit,SUM(op_c_amount) as op_credit ,
      //  SUM(t_d_amount) as tr_debit,SUM(t_c_amount) as tr_credit
      //  FROM
      //  (SELECT p.parent_id as parent_id, p.acc_head as p_acc_head, p.acc_code as acc_code, c.acc_head as acc_head,SUM(d_amount) as op_d_amount,SUM(c_amount) as op_c_amount, 0 as t_d_amount,0 as t_c_amount
      //  FROM acc_transactions t
      //  INNER JOIN acc_transaction_details on t.id = acc_trans_id
      //  INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      //  inner join chartofaccounts p on p.id = c.parent_id
      //  Where com_ref_id =  $company_code and voucher_date < '". date('Y-m-d', strtotime($fromdate))."'
      //  GROUP BY p.parent_id, p.acc_head, p.acc_code, c.acc_head
      //  UNION ALL
      //  SELECT p.parent_id as parent_id, p.acc_head as p_acc_head, p.acc_code as acc_code, c.acc_head as acc_head,0 as op_d_amount,0 as op_c_amount,SUM(d_amount) as t_d_amount,SUM(c_amount) as t_c_amount
      //  FROM acc_transactions t
      //  INNER JOIN acc_transaction_details on t.id = acc_trans_id
      //  INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      //  inner join chartofaccounts p on p.id = c.parent_id
      //  Where com_ref_id =  $company_code and voucher_date BETWEEN '". date('Y-m-d', strtotime($fromdate))."' and '".date('Y-m-d', strtotime($todate))."'
      //  GROUP BY p.parent_id, p.acc_head, p.acc_code, c.acc_head ) as M GROUP BY parent_id, p_acc_head, acc_code, acc_head ORDER BY parent_id = 'ASC'";
       

       $sql = "SELECT acc_origin, order_by, parent_id, p_acc_head,acc_head, acc_code, SUM(op_d_amount) as op_debit,SUM(op_c_amount) as op_credit ,
       SUM(t_d_amount) as tr_debit,SUM(t_c_amount) as tr_credit
       FROM
       (SELECT c.acc_origin as acc_origin, p.order_by as order_by, p.parent_id as parent_id, p.acc_head as p_acc_head, p.acc_code as acc_code, c.acc_head as acc_head,SUM(d_amount) as op_d_amount,SUM(c_amount) as op_c_amount, 0 as t_d_amount,0 as t_c_amount
       FROM acc_transactions t
       INNER JOIN acc_transaction_details on t.id = acc_trans_id
       INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
       inner join chartofaccounts p on p.id = c.parent_id
       
       Where com_ref_id =  $company_code and voucher_date < '". date('Y-m-d', strtotime($fromdate))."'
       GROUP BY c.acc_origin, p.order_by, p.parent_id, p.acc_head, p.acc_code, c.acc_head
       UNION ALL
       SELECT c.acc_origin as acc_origin, p.order_by as order_by, p.parent_id as parent_id, p.acc_head as p_acc_head, p.acc_code as acc_code, c.acc_head as acc_head,0 as op_d_amount,0 as op_c_amount,SUM(d_amount) as t_d_amount,SUM(c_amount) as t_c_amount
       FROM acc_transactions t
       INNER JOIN acc_transaction_details on t.id = acc_trans_id
       INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
       inner join chartofaccounts p on p.id = c.parent_id
       Where com_ref_id =  $company_code and voucher_date BETWEEN '". date('Y-m-d', strtotime($fromdate))."' and '".date('Y-m-d', strtotime($todate))."'
       GROUP BY c.acc_origin, p.order_by, p.parent_id, p.acc_head, p.acc_code, c.acc_head ) as M 
       GROUP BY acc_origin, order_by, parent_id, p_acc_head, acc_code, acc_head 
       ORDER BY order_by,acc_origin  ASC";

       $rows = DB::select($sql);
       return $rows;
   }

   public function getTrialBalance1(Request $request)
   {

       $dropdownscontroller = new DropdownsController();
       $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
       $companies = $dropdownscontroller->comboCompanyAssignList();
       $company_code = $default_comp_code;
       $ledger_id = 0;
       $acc_Ledger = '';
       $fromdate  = $this->getDateAttribute(date('Y-m-d'));
       $todate    = $this->getDateAttribute(date('Y-m-d'));

       if($request->filled('company_code') && $request->filled('fromdate')
          && $request->filled('todate')){
          $company_code = $request->input('company_code');
          $fromdate =  date('Y-m-d',strtotime($request->input('fromdate')));
          $todate =  date('Y-m-d',strtotime($request->input('todate')));
          $ledger_id  = $request->input('ledger_id');
          $default_comp_code = $request->input('company_code');
          $generalscontroller = new GeneralsController();
          $comp_name = $generalscontroller->CompanyName($default_comp_code);
          $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
        }

        $rows = $this->getTrialBalanceSql($company_code, $fromdate, $todate);

        if ($request->input('submit') == "pdf"){
          $fileName = 'subsidiary_ledger';

          $pdf = PDF::loadView('/accounts/reports/rpt_trial_balance_pdf1',
          compact('comp_name','comp_add','rows','companies','default_comp_code','fromdate', 'todate',), [], [
            'title' => $fileName,
          ]);
          return $pdf->stream($fileName,'.pdf');
        }

        // $sql = "SELECT chartofaccounts.id as acc_id, acc_origin, company_id, order_by, parent_id ,acc_head, acc_code, 
        // SUM(acc_transaction_details.d_amount) as d_debit FROM chartofaccounts 
        // INNER JOIN acc_transaction_details on acc_transaction_details.chart_of_acc_id = chartofaccounts.id
        // Where company_id = 1 
        // GROUP BY order_by, acc_id, acc_origin, company_id, order_by, parent_id ,acc_head, acc_code 
        // order by order_by ASC";

        // $rows = DB::select($sql);

        // return $rows;
       // get requested action
       return view('/accounts/reports/rpt_trial_balance1', compact('rows','companies','default_comp_code','fromdate', 'todate'));
   }

   public function getTrialBalance2(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
     $companies = $dropdownscontroller->comboCompanyAssignList();
     $company_code = $default_comp_code;
     $ledger_id = 0;
     $acc_Ledger = '';
     $fromdate  = $this->getDateAttribute(date('Y-m-d'));
     $todate    = $this->getDateAttribute(date('Y-m-d'));

     if($request->filled('company_code') && $request->filled('fromdate')
        && $request->filled('todate')){
        $company_code = $request->input('company_code');
        $fromdate =  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate =  date('Y-m-d',strtotime($request->input('todate')));
        $ledger_id  = $request->input('ledger_id');
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
      }

      $rows= $this->getTrialBalanceSql($company_code, $fromdate, $todate);

      if ($request->input('submit') == "pdf"){
            $fileName = 'subsidiary_ledger';

            $pdf = PDF::loadView('/accounts/reports/rpt_trial_balance_pdf2',
            compact('comp_name','comp_add','rows','companies','default_comp_code','fromdate', 'todate',), [], [
              'title' => $fileName,
            ]);
        return $pdf->stream($fileName,'.pdf');
       }

     // get requested action
     return view('/accounts/reports/rpt_trial_balance2', compact('rows','companies','default_comp_code','fromdate', 'todate'));

   }

  public function getDailyCashSheet(Request $request)
  {
    $dropdownscontroller = new DropdownsController();
   $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
   $companies = $dropdownscontroller->comboCompanyAssignList();

   $compcode = $request->input('company_code');

  /* $sql = "select * from `chartofaccounts`
   where `company_id` = $default_comp_code and (acc_head like 'Cash In%' or acc_head like 'Bank At%')
   and `id` not in (select `parent_id` from `chartofaccounts`)
   Order By acc_head asc"; */

   /*$sql = "select * from `chartofaccounts` where `company_id` = $default_comp_code and (acc_head like 'Cash In%')
   and `id` not in (select `parent_id` from `chartofaccounts`)
   UNION ALL
   select d2.* from `chartofaccounts` d1 inner join
   `chartofaccounts` d2 on `d1`.`id` = `d2`.`parent_id`
    where d1.`company_id` = $default_comp_code and (d1.acc_head like 'CASH AT%')
   and  d2.`parent_id` <> 0    Order By acc_head asc";*/

   $sql ="SELECT * FROM chartofaccounts Where acc_head in ('CASH IN HAND','CASH AT BANK')
    and company_id = ".$default_comp_code;

   $ledgers = DB::select($sql);

   $acc_Ledger = '';
   $ledgername = '';
   $fromdate  = date('Y-m-d');

   if($request->filled('company_code') && $request->filled('fromdate')){
      $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
      $default_comp_code = $request->input('company_code');
      $generalscontroller = new GeneralsController();
      $comp_name = $generalscontroller->CompanyName($default_comp_code);
      $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
      $ledgername = '';
    }

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

   //return $ledger_id;
    //ledger opening balance
    /*$opening = AccTransactions::query()
    ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
    ->selectRaw('sum(d_amount) as debit, sum(c_amount) as credit')
    ->where('com_ref_id', $default_comp_code )
    ->where('chart_of_acc_id', $ledger_id )
    ->whereDate('voucher_date','<' ,date('Y-m-d', strtotime($fromdate)))->first();

   $op_sql = "select ifnull(sum(d_amount),0) as debit, ifnull(sum(c_amount),0) as credit from `acc_transactions`
   inner join `acc_transaction_details` on `acc_transactions`.`id` = `acc_trans_id` where `com_ref_id` = $default_comp_code
   and `chart_of_acc_id` in (75) and date(`voucher_date`) < '". date('Y-m-d', strtotime($fromdate))."'";*/

   $op_sql = "select ifnull(sum(d_amount),0) as debit, ifnull(sum(c_amount),0) as credit from `acc_transactions`
   inner join `acc_transaction_details` on `acc_transactions`.`id` = `acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `chart_of_acc_id`
   where `com_ref_id` = $default_comp_code and `chartofaccounts`.parent_id in ($ledger_id)
   and date(`voucher_date`) < '". date('Y-m-d', strtotime($fromdate))."'";

   $openings = DB::select($op_sql);

   /* $rows = AccTransactions::query()
      ->join("acc_transaction_details as d1", "acc_transactions.id", "=", "d1.acc_trans_id")
      ->join("acc_transaction_details as d2", "d1.acc_trans_id", "=", "d2.acc_trans_id")
      ->join("chartofaccounts", "chartofaccounts.id", "=", "d2.chart_of_acc_id")
      ->whereBetween('voucher_date', [$fromdate,$fromdate])
      ->where('com_ref_id', $default_comp_code )
      ->whereIn('d1.chart_of_acc_id',[$ledger_id] )
      ->whereNotIn('d2.chart_of_acc_id', [$ledger_id] )
      ->selectRaw('d2.*,acc_head,t_narration,voucher_date')
      ->orderBy('voucher_date', 'asc')->get();*/

  /* $sql1 = "select acc_head,SUM(d2.d_amount) as d_amount,SUM(d2.c_amount) as c_amount from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `acc_transaction_details` as `d2` on `d1`.`acc_trans_id` = `d2`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d2`.`chart_of_acc_id`
   where `voucher_date` between '$fromdate' and '$fromdate' and `com_ref_id` = $default_comp_code
   and `d1`.`chart_of_acc_id` in ($ledger_id)
   and `d2`.`chart_of_acc_id` not in ($ledger_id) GROUP BY acc_head
   order by `voucher_date` asc";*/

   $sql1 = "select acc_head,t_narration, d_amount as d_amount,c_amount as c_amount
   from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
   where `voucher_date` between '$fromdate' and '$fromdate' and `com_ref_id` = $default_comp_code
   and trans_type in ('BR')
   AND (parent_id not in ($ledger_id))  order by file_level asc";
   $rows_bank_rec = DB::select($sql1);

   $sql1 = "select acc_head,t_narration, d_amount as d_amount,c_amount as c_amount
   from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
   where `voucher_date` between '$fromdate' and '$fromdate' and `com_ref_id` = $default_comp_code
   and trans_type in ('CR')
   AND (parent_id not in ($ledger_id))  order by file_level asc";
   $rows_cash_rec = DB::select($sql1);

   $sql1 = "select acc_head,t_narration, d_amount as d_amount,c_amount as c_amount
   from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
   where `voucher_date` between '$fromdate' and '$fromdate' and `com_ref_id` = $default_comp_code
   and trans_type in ('CP','BP','CON')
   AND (parent_id not in ($ledger_id))  order by file_level asc";
   $rows_payment = DB::select($sql1);

   //Cash in Hand
   $sql2 = "select SUM(d_amount) as d_amount,SUM(c_amount) as c_amount,SUM(d_amount)-SUM(c_amount) as CashinHand
   from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
   where `voucher_date` <= '$fromdate' and `com_ref_id` = $default_comp_code
   AND chart_of_acc_id = $CashinHand";
   $CashinHand = collect(\DB::select($sql2))->first();

   $data = AccTransactions::query()
     ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
     ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
     ->where('voucher_date','<=', $fromdate)
     ->where('com_ref_id', $default_comp_code)
     ->where('is_cash_sheet', 1 )
     ->selectRaw('chart_of_acc_id,acc_head,sum(d_amount) as d_amount, sum(c_amount) as c_amount')
     ->groupBy('chart_of_acc_id','acc_head')
     ->orderBy('acc_code', 'asc')
     ->orderBy('trans_type', 'asc')->get();

   /*$data = AccTransactions::query()
        ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
        ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
        ->where('voucher_date',"<=", $fromdate)
        ->where('com_ref_id', $default_comp_code )
        ->where('is_cash_sheet', 1 )
        ->selectRaw('trans_type,voucher_no,voucher_date,t_narration,d_amount,
        c_amount,chart_of_acc_id,acc_head')
        ->orderBy('voucher_date', 'asc')->get();*/

    if ($request->input('submit') == "pdf"){
          $fileName = 'daily_cash';

          $pdf = PDF::loadView('/accounts/reports/rpt_daily_cash_sheet_pdf',
          compact('comp_name','comp_add','rows_bank_rec','rows_cash_rec','rows_payment','data','openings','companies','default_comp_code','ledgername','fromdate','CashinHand',), [], [
            'title' => $fileName,
          ]);
      return $pdf->stream($fileName,'.pdf');
     }


    // get requested action
    return view('/accounts/reports/rpt_daily_cash_sheet', compact('rows_bank_rec','rows_cash_rec','rows_payment','data','openings','companies','default_comp_code','ledgers','fromdate','CashinHand'));

  }
  
   
 public function getDailyCashSheetSummary(Request $request)
 {
   $dropdownscontroller = new DropdownsController();
   $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
   $companies = $dropdownscontroller->comboCompanyAssignList();

   $compcode = $request->input('company_code');

   $sql ="SELECT * FROM chartofaccounts Where acc_head in ('CASH IN HAND','CASH AT BANK')
    and company_id = ".$default_comp_code;

   $ledgers = DB::select($sql);

   $acc_Ledger = '';
   $ledgername = '';
   $fromdate  = date('Y-m-d');

   if($request->filled('company_code') && $request->filled('fromdate')){
      $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
      $default_comp_code = $request->input('company_code');
      $generalscontroller = new GeneralsController();
      $comp_name = $generalscontroller->CompanyName($default_comp_code);
      $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
      $ledgername = '';
    }

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
   where `com_ref_id` = $default_comp_code and `chartofaccounts`.parent_id in ($ledger_id)
   and date(`voucher_date`) < '". date('Y-m-d', strtotime($fromdate))."'";

   $openings = DB::select($op_sql);

   $sql1 = "select acc_head,ifnull(sum(d_amount),0) as d_amount,ifnull(sum(c_amount),0) as c_amount
   from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
   where `voucher_date` between '$fromdate' and '$fromdate' and `com_ref_id` = $default_comp_code
   and trans_type in ('BR')
   AND (parent_id not in ($ledger_id)) group by acc_head  order by file_level asc";
   $rows_bank_rec = DB::select($sql1);

   $sql1 = "select acc_head,ifnull(sum(d_amount),0) as d_amount,ifnull(sum(c_amount),0) as c_amount
   from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
   where `voucher_date` between '$fromdate' and '$fromdate' and `com_ref_id` = $default_comp_code
   and trans_type in ('CR')
   AND (parent_id not in ($ledger_id)) group by acc_head order by file_level asc";
   $rows_cash_rec = DB::select($sql1);

   $sql1 = "select acc_head, ifnull(sum(d_amount),0) as d_amount,ifnull(sum(c_amount),0) as c_amount
   from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
   where `voucher_date` between '$fromdate' and '$fromdate' and `com_ref_id` = $default_comp_code
   and trans_type in ('CP','BP','CON')
   AND (parent_id not in ($ledger_id)) group by acc_head  order by file_level asc";
   $rows_payment = DB::select($sql1);

   //Cash in Hand
   $sql2 = "select SUM(d_amount) as d_amount,SUM(c_amount) as c_amount,SUM(d_amount)-SUM(c_amount) as CashinHand
   from `acc_transactions`
   inner join `acc_transaction_details` as `d1` on `acc_transactions`.`id` = `d1`.`acc_trans_id`
   inner join `chartofaccounts` on `chartofaccounts`.`id` = `d1`.`chart_of_acc_id`
   where `voucher_date` <= '$fromdate' and `com_ref_id` = $default_comp_code
   AND chart_of_acc_id = $CashinHand";
   $CashinHand = collect(\DB::select($sql2))->first();
   
    $data = AccTransactions::query()
     ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
     ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
     ->where('voucher_date','<=', $fromdate)
     ->where('com_ref_id', $default_comp_code )
     ->where('is_cash_sheet', 1 )
     ->selectRaw('chart_of_acc_id,acc_head,sum(d_amount) as d_amount, sum(c_amount) as c_amount')
     ->groupBy('chart_of_acc_id','acc_head')
     ->orderBy('acc_code', 'asc')->get();

    if ($request->input('submit') == "pdf"){
          $fileName = 'daily_cash';

          $pdf = PDF::loadView('/accounts/reports/rpt_daily_cash_sheet_summary_pdf',
          compact('comp_name','comp_add','rows_bank_rec','rows_cash_rec','rows_payment','openings','data','companies','default_comp_code','ledgername','fromdate','CashinHand',), [], [
            'title' => $fileName,
          ]);
      return $pdf->stream($fileName,'.pdf');
     }

   // get requested action
   return view('/accounts/reports/rpt_daily_cash_sheet_summary', compact('rows_bank_rec','rows_cash_rec','rows_payment','openings','data','companies','default_comp_code','ledgers','fromdate','CashinHand'));

 }
 
   public function getLedgerHead(Request $request)
   {
     $compcode = $request->get('compcode');

     $sql = "select * from `chartofaccounts`
     where `company_id` = $compcode and `id` not in (select `parent_id` from `chartofaccounts`) and
     acc_head like '%".$request->get('item')."%' Order By acc_head asc";
     $data = DB::select($sql);
     return AccHeadResource::collection($data);
   }

   public function getControlLedgerHead(Request $request)
   {
     $compcode = $request->get('compcode');

     $sql = "select * from `chartofaccounts`
     where `company_id` = $compcode and `id` in (select `parent_id` from `chartofaccounts`) and
     acc_head like '%".$request->get('item')."%' Order By acc_head asc";
     $data = DB::select($sql);
     return AccHeadResource::collection($data);
   }

   public function print($voucher_id)
   {
       // dd($sale);
       $generalscontroller = new GeneralsController();
       $comp_code = $generalscontroller->CompanyRefId($voucher_id);
       $comp_name = $generalscontroller->CompanyName($comp_code);
       $comp_add  = $generalscontroller->CompanyAddress($comp_code);

       $acctranscontroller = new AccountTransController();
       $rows_m = $acctranscontroller->modal_view_m($voucher_id);
       $rows_d = $acctranscontroller->modal_view_d($voucher_id);
       $fileName = $voucher_id;

       $pdf = PDF::loadView('/accounts/reports/voucher_print',
       compact('comp_name','rows_m','rows_d','comp_name','comp_add',), [], [
         'title' => $fileName,
       ]);
      return $pdf->stream($fileName,'.pdf', '.pdf');

       return view('/accounts/reports/voucher_print', ['rows_m' =>$rows_m,'rows_d' =>$rows_d,
     'comp_name'=>$comp_name,'comp_add'=>$comp_add]);
   }

   public function journalVoucherPrint($voucher_id)
   {
       $generalscontroller = new GeneralsController();
       $comp_code = $generalscontroller->CompanyRefId($voucher_id);
       $comp_name = $generalscontroller->CompanyName($comp_code);
       $comp_add  = $generalscontroller->CompanyAddress($comp_code);

       $acctranscontroller = new AccountTransController();
       $rows_m = $acctranscontroller->modal_view_m($voucher_id);
       $rows_d = $acctranscontroller->modal_view_d($voucher_id);
       $fileName = $voucher_id;

       $pdf = PDF::loadView('/accounts/reports/journal_voucher_pdf',
       compact('comp_name','rows_m','rows_d','comp_name','comp_add',), [], [
         'title' => $fileName,
       ]);
      return $pdf->stream($fileName,'.pdf', '.pdf');

      return view('/accounts/reports/voucher_print', ['rows_m' => $rows_m,'rows_d' =>$rows_d,
     'comp_name'=>$comp_name,'comp_add'=>$comp_add]);
   }

   public function cashReceiverPrint($voucher_id)
   {
       $generalscontroller = new GeneralsController();
       $comp_code = $generalscontroller->CompanyRefId($voucher_id);
       $comp_name = $generalscontroller->CompanyName($comp_code);
       $comp_add  = $generalscontroller->CompanyAddress($comp_code);

       $acctranscontroller = new AccountTransController();
       $rows_m = $acctranscontroller->modal_view_m($voucher_id);
       $rows_d = $acctranscontroller->modal_view_d($voucher_id);
       $fileName = $voucher_id;

      $slNo = 0;
      foreach($rows_d as $row){
                    
        if($row->d_amount == 0){
          $head = $row->acc_head;
          $slNo = Chartofaccounts::find($row->chart_of_acc_id)->customerId;
        }          
      } 
       
      $pdf = PDF::loadView('/accounts/reports/money_voucher_pdf1', 
      compact('comp_name', 'slNo', 'rows_m','rows_d','comp_name','comp_add',), [], [
        'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf', '.pdf');

      // old
      return view('/accounts/reports/voucher_print', ['rows_m' => $rows_m,'rows_d' =>$rows_d,
     'comp_name'=>$comp_name,'comp_add'=>$comp_add]);
   }

   public function voucher()
   {
      return view('/accounts/reports/voucher');
   }

   public function getLiquidCashReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
     $companies = $dropdownscontroller->comboCompanyAssignList();
     $company_code = $default_comp_code;

     /*$sql = "select * from `chartofaccounts`
     where `company_id` = $company_code and `parent_id` = 0
     Order By acc_head asc"; */

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
     $ledger_id .= 0;
     $acc_Ledger = '';
     $fromdate  = date('Y-m-d');
     $todate    = date('Y-m-d');

     if($request->filled('company_code') && $request->filled('fromdate')
      && $request->filled('todate')){
        $company_code = $request->input('company_code');
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
      }

      $sql = "SELECT acc_head, SUM(op_d_amount) as op_debit,SUM(op_c_amount) as op_credit ,
      SUM(t_d_amount) as tr_debit,SUM(t_c_amount) as tr_credit
      FROM
      (SELECT c.acc_head as acc_head,SUM(d_amount) as op_d_amount,SUM(c_amount) as op_c_amount, 0 as t_d_amount,0 as t_c_amount
      FROM acc_transactions t
      INNER JOIN acc_transaction_details on t.id = acc_trans_id
      INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      inner join chartofaccounts p on p.id = c.parent_id
      Where com_ref_id =  $company_code and (chart_of_acc_id = 75 or c.parent_id = 74) AND voucher_date < '". date('Y-m-d', strtotime($fromdate))."'
      GROUP BY c.acc_head
      UNION ALL
      SELECT c.acc_head as acc_head,0 as op_d_amount,0 as op_c_amount,SUM(d_amount) as t_d_amount,SUM(c_amount) as t_c_amount
      FROM acc_transactions t
      INNER JOIN acc_transaction_details on t.id = acc_trans_id
      INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      inner join chartofaccounts p on p.id = c.parent_id
      Where com_ref_id =  $company_code and (chart_of_acc_id = 75 or c.parent_id = 74) AND voucher_date BETWEEN '". date('Y-m-d', strtotime($fromdate))."' and '".date('Y-m-d', strtotime($todate))."'
      GROUP BY c.acc_head ) as M GROUP BY acc_head";
      $rows = DB::select($sql);

      if ($request->input('submit') == "pdf"){
            $fileName = 'liquid_report';

          $pdf = PDF::loadView('/accounts/reports/rpt_liquid_cash_pdf',
            compact('comp_name','comp_add','rows','companies','default_comp_code','fromdate',
             'todate',), [], [
              'title' => $fileName,
            ]);
        return $pdf->stream($fileName,'.pdf');

       }

     // get requested action
     return view('/accounts/reports/rpt_liquid_cash', compact('rows','companies','default_comp_code','ledger_id','fromdate', 'todate'));

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

     if($request->filled('company_code') && $request->filled('ledger_id')
      && $request->filled('fromdate') && $request->filled('todate')){
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $ledger_id  = $request->input('ledger_id');
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
        $ledgername = $generalscontroller->accountNameLookup($ledger_id);
        $cust_data = $generalscontroller->getCustomerInfByLedgerId($ledger_id);
      }

      //ledger opening balance
      $opening = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->selectRaw('sum(d_amount) as debit, sum(c_amount) as credit')
      ->where('com_ref_id', $default_comp_code )
      ->where('chart_of_acc_id', $ledger_id )
      ->whereDate('voucher_date','<' ,date('Y-m-d', strtotime($fromdate)))->first();

      $rows = AccTransactions::query()
        ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
        ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
        ->whereBetween('voucher_date', [$fromdate,$todate])
        ->where('com_ref_id', $default_comp_code )
        ->where('chart_of_acc_id', $ledger_id )
        ->selectRaw('trans_type,voucher_no,voucher_date,t_narration,d_amount,c_amount,acc_invoice_no')
        ->orderBy('voucher_date', 'asc')->get();

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
     return view('/accounts/reports/rpt_custmer_statement',
     compact('rows','opening','companies','default_comp_code',
     'customers','ledger_id','fromdate', 'todate'));

   }
   
   public function getCustomerSummaryStatementReport(Request $request)
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

     if($request->filled('company_code') && $request->filled('ledger_id')
      && $request->filled('fromdate') && $request->filled('todate')){
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $ledger_id  = $request->input('ledger_id');
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
        $ledgername = $generalscontroller->accountNameLookup($ledger_id);
        $cust_data = $generalscontroller->getCustomerInfByLedgerId($ledger_id);
      }

      //ledger opening balance
      $opening = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->selectRaw('sum(d_amount) as debit, sum(c_amount) as credit')
      ->where('com_ref_id', $default_comp_code )
      ->where('chart_of_acc_id', $ledger_id )
      ->whereDate('voucher_date','<' ,date('Y-m-d', strtotime($fromdate)))->first();

      $rows = AccTransactions::query()
        ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
        ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
        ->whereBetween('voucher_date', [$fromdate,$todate])
        ->where('com_ref_id', $default_comp_code )
        ->where('chart_of_acc_id', $ledger_id )
        ->selectRaw('voucher_date,SUM(d_amount) as d_amount,SUM(c_amount) as c_amount')
        ->groupBy('voucher_date')
        ->orderBy('voucher_date', 'asc')->get();

      if ($request->input('submit') == "pdf"){
            $fileName = 'subsidiary_ledger';

      $pdf = PDF::loadView('/accounts/reports/rpt_custmer_statement_summary_pdf',
            compact('comp_name','comp_add','rows','opening','companies','default_comp_code',
            'cust_data','ledgername','fromdate', 'todate',), [], [
              'title' => $fileName,
            ]);
        return $pdf->stream($fileName,'.pdf');
       }

     // get requested action
     return view('/accounts/reports/rpt_custmer_statement_summary',
     compact('rows','opening','companies','default_comp_code',
     'customers','ledger_id','fromdate', 'todate'));

   }
   
   public function getCustomerDuesReport(Request $request)
   {
     $dropdownscontroller = new DropdownsController();
     $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
     $companies = $dropdownscontroller->comboCompanyAssignList();

     $sql = "select c.id,c.acc_head,p.acc_head as p_acc_head from `chartofaccounts` c
     left join chartofaccounts p on c.parent_id = p.id
     where c.`company_id` = $default_comp_code and c.`id` not in (select `parent_id` from `chartofaccounts`)
     Order By acc_head asc";

     $ledgers = DB::select($sql);

     $ledger_id = 0;
     $ledgername = '';
     $acc_Ledger = '';
     $fromdate  = date('Y-m-d');
     $todate    = date('Y-m-d');
     $cust_data = '';
     if($request->filled('company_code') && $request->filled('ledger_id')
      && $request->filled('fromdate') && $request->filled('todate')){
        $fromdate=  date('Y-m-d',strtotime($request->input('fromdate')));
        $todate=  date('Y-m-d',strtotime($request->input('todate')));
        $ledger_id  = $request->input('ledger_id');
        $default_comp_code = $request->input('company_code');
        $generalscontroller = new GeneralsController();
        $comp_name = $generalscontroller->CompanyName($default_comp_code);
        $comp_add = $generalscontroller->CompanyAddress($default_comp_code);
        $ledgername = $generalscontroller->accountNameLookup($ledger_id);
        $cust_data = $generalscontroller->getCustomerInfByLedgerId($ledger_id);
      }

      //ledger opening balance
      /*$sql="SELECT voucher_date,acc_invoice_no,trans_type,voucher_no,d_amount,cr_amount FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      LEFT JOIN (SELECT acc_invoice_no as acc_inv_no,SUM(c_amount) as cr_amount FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      WHERE chart_of_acc_id = $ledger_id and trans_type in ('CR','BR')
      and acc_invoice_no = 0  GROUP BY acc_invoice_no)
      AS REC ON REC.acc_inv_no = acc_invoice_no
      WHERE chart_of_acc_id = $ledger_id and trans_type = 'JV'
      and voucher_date between '$fromdate' and '$todate'";*/
      
      $sql="SELECT voucher_date,acc_invoice_no,trans_type,voucher_no,d_amount,c_amount as cr_amount FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      WHERE chart_of_acc_id = $ledger_id and trans_type in ('JV','CR','BR')
      and ( acc_invoice_no = 0 or acc_invoice_no is NULL)
      and voucher_date between '$fromdate' and '$todate'";
      $opening = DB::select($sql);

      $sql = "SELECT voucher_date,acc_invoice_no,trans_type,voucher_no,d_amount,cr_amount FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      INNER JOIN sales_invoices on sales_invoices.inv_no = acc_invoice_no
      LEFT JOIN (SELECT acc_invoice_no as acc_inv_no,SUM(c_amount) as cr_amount FROM acc_transactions
      INNER JOIN acc_transaction_details on acc_transactions.id = acc_trans_id
      WHERE chart_of_acc_id = $ledger_id and trans_type in ('CR','BR')
      and acc_invoice_no > 0  GROUP BY acc_invoice_no)
      AS REC ON REC.acc_inv_no = acc_invoice_no
      WHERE chart_of_acc_id = $ledger_id and trans_type = 'SV' and acc_invoice_no > 0
      and  voucher_date between '$fromdate' and '$todate'";
      $rows = DB::select($sql);

      if ($request->input('submit') == "pdf"){
            $fileName = 'subsidiary_ledger';

            $pdf = PDF::loadView('/accounts/reports/rpt_cust_due_list',
            compact('comp_name','comp_add','rows','opening','companies','default_comp_code',
            'ledgername','cust_data','fromdate', 'todate',), [], [
              'title' => $fileName,
            ]);
            return $pdf->stream($fileName,'.pdf');

       }

     // get requested action
     return view('/accounts/reports/rpt_cust_due_list',
     compact('rows','opening','companies','default_comp_code','ledgers','ledger_id','cust_data','fromdate', 'todate'));

   }
   
   
   public function getDateAttribute($value)
   {
       return Carbon::parse($value)->format('Y-m-d');
   }


   public function getTrialBalance3(Request $request){

    $dropdownscontroller = new DropdownsController();
    $default_comp_code   = $dropdownscontroller->defaultCompanyCode();
    $companies = $dropdownscontroller->comboCompanyAssignList();
    $company_code = $default_comp_code;
    $ledger_id = 0;
    $acc_Ledger = '';
    $fromdate  = $this->getDateAttribute(date('Y-m-d'));
    $todate    = $this->getDateAttribute(date('Y-m-d'));


    if(isset($request->fromdate)){

      $company_code = $request->input('company_code');
      $ledger_id  = $request->input('ledger_id');
      $default_comp_code = $request->input('company_code');
      $generalscontroller = new GeneralsController();
      $comp_name = $generalscontroller->CompanyName($default_comp_code);
      $comp_add = $generalscontroller->CompanyAddress($default_comp_code);


      $dropdownscontroller = new DropdownsController();
      $company_code   = $dropdownscontroller->defaultCompanyCode();
      $fromdate =  date('Y-m-d',strtotime($request->input('fromdate')));
      $todate =  date('Y-m-d',strtotime($request->input('todate')));

        
      $sql = "SELECT parent_id, acc_code, SUBSTR(acc_code, 1,1) as tmpCOde, p_acc_head, SUM(op_d_amount) as op_debit,SUM(op_c_amount) as op_credit ,
      SUM(t_d_amount) as tr_debit,SUM(t_c_amount) as tr_credit
      FROM
      (SELECT p.acc_code, SUBSTR(p.acc_code, 1,1) as tmpCOde, p.parent_id, p.acc_head as p_acc_head,SUM(d_amount) as op_d_amount,SUM(c_amount) as op_c_amount, 0 as t_d_amount,0 as t_c_amount
      FROM acc_transactions t
      INNER JOIN acc_transaction_details on t.id = acc_trans_id
      INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
      inner join chartofaccounts p on p.id = c.parent_id
      where com_ref_id =  $company_code
      and voucher_date  BETWEEN '". date('Y-m-d', strtotime($fromdate))."' and '". date('Y-m-d', strtotime($todate))."' 
      
      GROUP BY p.acc_head, p.acc_code, p.parent_id
      UNION ALL
      SELECT p.acc_code, SUBSTR(p.acc_code, 1,1) as tmpCOde, p.parent_id, p.acc_head as p_acc_head,0 as op_d_amount,0 as op_c_amount,SUM(d_amount) as t_d_amount,SUM(c_amount) as t_c_amount 
      FROM acc_transactions t 
      INNER JOIN acc_transaction_details on t.id = acc_trans_id 
      INNER JOIN chartofaccounts c on c.id = chart_of_acc_id 
      inner join chartofaccounts p on p.id = c.parent_id 
      Where com_ref_id = $company_code and voucher_date 
      BETWEEN '". date('Y-m-d', strtotime($fromdate))."' and '". date('Y-m-d', strtotime($todate))."' 
      GROUP BY p.acc_head, p.acc_code, p.parent_id ) 
      as M GROUP BY p_acc_head, acc_code, parent_id";

      $rows =  DB::select($sql);
      

      $Suchpense = DB::table('acc_transaction_details')
      ->join('chartofaccounts', 'chartofaccounts.id', '=', 'acc_transaction_details.chart_of_acc_id')
      ->whereIn('acc_transaction_details.chart_of_acc_id', [429])
      ->select([DB::raw("SUM(d_amount) as d_amount"), DB::raw("SUM(c_amount) as c_amount"), 'acc_head'])
      ->groupBy('acc_head')
      ->get();

      $long_term_liabilities = DB::table('acc_transaction_details')
      ->join('chartofaccounts', 'chartofaccounts.id', '=', 'acc_transaction_details.chart_of_acc_id')
      ->whereIn('acc_transaction_details.chart_of_acc_id', [645, 646,647,648,649])
      ->select([DB::raw("SUM(d_amount) as d_amount"), DB::raw("SUM(c_amount) as c_amount"), 'acc_head'])
      ->groupBy('acc_head')
      ->get();

      $long = 0;
      foreach ($long_term_liabilities as $key => $value) {
          $long += $value->d_amount - $value->c_amount;
      }


      $ddd = DB::table('acc_transaction_details')
      ->join('acc_transactions', 'acc_transactions.id', '=', 'acc_trans_id')
      ->join('chartofaccounts', 'chartofaccounts.id', '=', 'chart_of_acc_id')
      ->leftJoin('chartofaccounts as sub', 'sub.id', '=', 'chartofaccounts.id')
      ->leftJoin('chartofaccounts as sub_t', 'sub_t.id', '=', 'sub.id')
      ->select([DB::raw("SUM(d_amount) as d_amount"), DB::raw("SUM(c_amount) as c_amount"), 
      'sub_t.acc_head', 'sub_t.parent_id', 'sub_t.acc_level', 'sub_t.id'])
      ->groupBy('sub_t.acc_head', 'sub_t.parent_id', 'sub_t.acc_level', 'sub_t.id')
      ->whereBetween('acc_transactions.voucher_date', [$fromdate, $todate])
      ->get();

      $current = 0;
      foreach ($ddd as $key => $value) {
          if($value->acc_level == 4 and  in_array($value->parent_id, array(1658, 640, 641, 665))){
            $current += $value->d_amount - $value->c_amount;
          }
      }


      // opening value
      $opSql = "select `item_code`, `item_name`, `itm_cat_name`, `item_warehouse_id`, `ware_name`, `item_storage_loc`, `item_lot_no`,
      `item_op_dt`, `item_stocks`.`item_op_stock`,item_base_price from `item_stocks` inner join `items` on `item_ref_id` = `items`.`id`
       inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id` and `itm_comp_id` = item_ref_comp_id
       inner join `warehouses` on `item_warehouse_id` = `warehouses`.`id`";
   
      $where = " where item_trans_desc = 'OP'";
      $where .= " and `item_stocks`.`item_op_dt` between '$fromdate' and '$todate'";      
      $opSql .= $where;
      $opSql .= ' Order by item_code asc'; 
      $opening = DB::select($opSql);

      $ops = 0;
      foreach ($opening as $key => $op) {
        $ops += $op->item_op_stock * $op->item_base_price;
      } 

      // purchase total
      $purchase = DB::table('item_purchases')
          ->whereBetween('pur_order_date', [$fromdate, $todate])
          ->sum('pur_total_amount');

      $incomes = Helper::trialBalance($company_code, $fromdate, $todate, $income = 1);
      $stocks = Helper::trialBalance($company_code, $fromdate, $todate, $stock = 7);
      $expenses = Helper::trialBalance($company_code, $fromdate, $todate, $expense = 2);

      $cashAtBank = Helper::trialBalance($company_code, $fromdate, $todate, $cashAtBank = 6);
      $cashInHand = Helper::trialBalance($company_code, $fromdate, $todate, $cashInHand = 5);
      // $assets = Helper::trialBalance($company_code, $fromdate, $todate, $assets = 3);
      // $liabilities = Helper::trialBalance($company_code, $fromdate, $todate, $liabilities = 4);

      $liaSql = "SELECT acc_origin, SUM(c_amount) as c_amount, 
                  SUM(d_amount) as d_amount, SUM(d_amount)- SUM(c_amount) as balance 
                  FROM acc_transaction_details 
                  JOIN chartofaccounts ON chartofaccounts.id = acc_transaction_details.chart_of_acc_id 
                  JOIN acc_transactions ON acc_transactions.id = acc_transaction_details.acc_trans_id 
                  where order_by = 4 
                  and voucher_date between '$fromdate' and '$todate'
                  GROUP BY acc_origin";
      $liabilities = DB::select($liaSql);      

      $assetSql = "SELECT acc_origin, SUM(c_amount) as c_amount, 
                SUM(d_amount) as d_amount, SUM(d_amount) - SUM(c_amount) as balance 
                FROM acc_transaction_details 
                JOIN chartofaccounts ON chartofaccounts.id = acc_transaction_details.chart_of_acc_id 
                JOIN acc_transactions ON acc_transactions.id = acc_transaction_details.acc_trans_id 
                where order_by = 3 and voucher_date between '$fromdate' and '$todate'
                GROUP BY acc_origin";

      $assets = DB::select($assetSql);      
      
      // closing balance 
      $COsql =  "SELECT item_code,item_name,itm_cat_name,item_lot_no,max(l_item_base_price) as price,
      SUM(OP) as OP,SUM(GR) as GR,SUM(ST) as ST,SUM(SR) as SR,SUM(SA) as SA,SUM(RT) as RT,SUM(DA) as DA,SUM(SH) as SH,SUM(EX) as EX
      FROM ( SELECT item_code,item_name,itm_cat_name,item_lot_no,max(l_item_base_price) as l_item_base_price,
      SUM(OP)+SUM(GR)+SUM(ST)+SUM(SR)+SUM(SA)+SUM(RT)+SUM(DA)+SUM(SH)+SUM(EX) as OP,0 as GR,0 as SA,0 as RT,0 as ST,0 as SR,0 as DA,0 as SH,0 as EX
      FROM view_item_ledger
      inner join items on items.id = item_ref_id
      INNER JOIN item_categories on item_ref_cate_id=item_categories.id and itm_comp_id = item_ref_comp_id
      LEFT JOIN view_item_last_price on l_item_op_comp_id = item_ref_comp_id and l_item_lot_no = item_lot_no
      AND l_item_ref_id = item_ref_id
      Where item_op_comp_id = $company_code and item_op_dt < '$fromdate' GROUP BY item_code,item_name,itm_cat_name,item_lot_no
      UNION ALL
      SELECT item_code,item_name,itm_cat_name,item_lot_no,max(l_item_base_price) as l_item_base_price,
      SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(ST) as ST,SUM(SR) as SR,SUM(DA) as DA,SUM(SH) as SH,SUM(EX) as EX
      FROM view_item_ledger
      inner join items on items.id = item_ref_id
      INNER JOIN item_categories on item_ref_cate_id=item_categories.id and itm_comp_id = item_ref_comp_id
      LEFT JOIN view_item_last_price on l_item_op_comp_id = item_ref_comp_id and l_item_lot_no = item_lot_no
      AND l_item_ref_id = item_ref_id
      Where item_op_comp_id = $company_code and item_op_dt BETWEEN '$fromdate' and '$todate' GROUP BY
      item_code,item_name,itm_cat_name,item_lot_no ORDER BY item_code asc ) AS MAIN GROUP BY
      item_code,item_name,itm_cat_name,item_lot_no";

      $COsql = DB::select($COsql);
      $clBalance = 0;
      foreach ($COsql as $key => $row) {
        $bal = $row->OP + $row->GR + $row->SA + $row->RT + $row->ST + $row->SR +
        $row->SH + $row->EX + $row->DA; 
        $clBalance += $bal*$row->price;
      }

      $Suchpense = $Suchpense[0]->d_amount - $Suchpense[0]->c_amount;
      return view('/accounts/reports/rpt_trial_balance3', compact('rows', 'liabilities', 'assets', 'cashAtBank', 'cashInHand', 'expenses', 'incomes', 'stocks', 'Suchpense', 'long', 'current', 'ops', 'purchase', 'clBalance', 'companies','default_comp_code','fromdate', 'todate'));

    }

    $ops = 0;
    $purchase = 0;
    $rows =[];
    $clBalance = 0;
    $current = 0;
    $long = 0;
    $Suchpense = 0;



    return view('/accounts/reports/rpt_trial_balance3', compact('rows', 'Suchpense', 'long', 'current', 'ops', 'purchase', 'clBalance', 'companies','default_comp_code','fromdate', 'todate'));
    
  }

}
