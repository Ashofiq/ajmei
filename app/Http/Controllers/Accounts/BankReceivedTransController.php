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

class BankReceivedTransController extends Controller
{

  public $trans_type = 5;
  public $title = 'Bank Received';
  public function br_index(Request $request)
  {
    $trans_type = $this->trans_type;
    $title = $this->title;
    $acc_id = ''; 
    $dropdownscontroller = new DropdownsController();
    $default_comp_code = $dropdownscontroller->defaultCompanyCode();
    $companies  = $dropdownscontroller->comboCompanyAssignList();
    $fin_date = FinancialYearDeclaration::query()
      ->where('comp_id','=',$default_comp_code)
      ->where('status','=',1)
      ->select('date_from','date_to')->first();

    $fromdate = $fin_date->date_from;
    $todate =  $fin_date->date_to;
    
    $acctranscontroller = new AccountTransController();
    $acc_list = $acctranscontroller->accheadLookup($default_comp_code,$trans_type,true);
    $vouchers = $acctranscontroller->acc_index($default_comp_code,$this->trans_type);

    // get requested action
    return view('/accounts/acctrans_index', compact('title', 'trans_type','vouchers','companies','default_comp_code','fromdate','todate','acc_id','acc_list'));
  }

  public function br_search(Request $request)
  {
    $trans_type = $this->trans_type;
    $title = $this->title;
    $default_comp_code = $request->input('company_code');
    $fromdate   = date('Y-m-d',strtotime($request->input('fromdate')));
    $todate     = date('Y-m-d',strtotime($request->input('todate')));
    $acc_id     = $request->input('acc_id');
    $voucherNo  = $request->input('voucherNo');
    
    $dropdownscontroller = new DropdownsController();
    $companies  = $dropdownscontroller->comboCompanyAssignList();

    $acctranscontroller = new AccountTransController();
    $acc_list = $acctranscontroller->accheadLookup($default_comp_code,$trans_type,true);
    $vouchers = $acctranscontroller->acc_search($default_comp_code,$this->trans_type,$fromdate,$todate,$acc_id,$voucherNo);

    // get requested action
    return view('/accounts/acctrans_index', compact('title','trans_type','vouchers','companies','default_comp_code','fromdate','todate','acc_id','acc_list'));
  }

  public function br_create(Request $request)
  {
      $trans_date = $this->getDateAttribute(date('d-m-Y'));
      $trans_type = $this->trans_type;
      $title      = $this->title;
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $accdoctype   = $dropdownscontroller->comboAccDocTypeList($this->trans_type, $company_code); // voucher type list 1 is for JV
      foreach ($accdoctype as $value) {
          $doctype =  $value->doc_type;
      }
      $generalscontroller = new GeneralsController();
        $finan_yearId = $generalscontroller->getActiveFinYearId($company_code,$trans_date);
      $voucher_no = $doctype.'-'.$generalscontroller->getMaxAccVoucherNo($doctype,$company_code,$finan_yearId);
      $finan_year = $generalscontroller->getCombinFinancialYear($company_code);

      $voucherNo = $generalscontroller->getMaxAccVoucherNo($doctype, $company_code, $finan_yearId);

      $accTransactionId = AccTransactions::where('voucher_no', $voucherNo)->where('trans_type', $doctype)->first();

      $accTransactionId = $accTransactionId ? $accTransactionId->id : 0;
      // get requested action
      return view('/accounts/acctrans_create',
      compact('title','trans_type', 'accTransactionId', 'trans_date','companies','accdoctype','company_code','voucher_no','finan_year'));
  }

  public function br_modal_view($id)
  {
      $acctranscontroller = new AccountTransController();
      $rows_m = $acctranscontroller->modal_view_m($id);
      $rows_d = $acctranscontroller->modal_view_d($id);
      return view('accounts.acctrans_viewmodal',compact('rows_m','rows_d'));
  }

  public function getDateAttribute($value)
  {
        return Carbon::parse($value)->format('Y-m-d');
  }


}
