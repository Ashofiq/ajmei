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

  public function default_comp_code()
  {
    $dropdownscontroller = new DropdownsController();
    $default_comp_code = $dropdownscontroller->defaultCompanyCode();
    return $default_comp_code;
  }

  public function jv_index(Request $request)
  {
    $dropdownscontroller = new DropdownsController();
    $default_comp_code = $dropdownscontroller->defaultCompanyCode();
    $companies  = $dropdownscontroller->comboCompanyAssignList();
    $journals = AccTransactions::query()
      ->join("acc_transaction_details", "acc_transactions.id", "=", "acc_trans_id")
      ->join('chartofaccounts', function ($join) {
          $join->on('chartofaccounts.id', '=', 'chart_of_acc_id')
             ->where('com_ref_id', '=', $this->default_comp_code());
           })
      ->join('acc_trans_doc_types', function ($join) {
          $join->on('acc_transactions.trans_type', '=', 'doc_type')
             ->where('doc_comp_id', '=', $this->default_comp_code())
             ->where('trans_type_no', '=', $this->default_comp_code());
        })
      ->join("companies", "com_ref_id", "=", "companies.id")
      ->where('com_ref_id', $default_comp_code)
      ->selectRaw("acc_transactions.id, acc_transactions.trans_type,acc_transactions.voucher_no,
          acc_transactions.voucher_date,  acc_transactions.t_narration,companies.name,
          SUM(d_amount) as d_amount ,SUM(c_amount) as c_amount")
      ->groupBy('acc_transactions.id', 'acc_transactions.trans_type','acc_transactions.voucher_no',
          'acc_transactions.voucher_date',  'acc_transactions.t_narration','companies.name')
      ->orderBy('acc_transactions.id', 'desc')->paginate(10);

    $collect = collect($journals);
    // get requested action
    return view('/accounts/journal_index', compact('journals','companies','default_comp_code'));
  }

    public function jv_create(Request $request)
    {
      $trans_date = date('d-m-Y');
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $accdoctype   = $dropdownscontroller->comboAccDocTypeList(1, $company_code); // voucher type list 1 is for JV
      foreach ($accdoctype as $value) {
          $doctype =  $value->doc_type;
      }
      $generalscontroller = new GeneralsController();
      $voucher_no = $doctype.'-'.$generalscontroller->getMaxAccVoucherNo($doctype,$company_code);
      $finan_year = $generalscontroller->getCombinFinancialYear($company_code);
      // get requested action
      return view('/accounts/journal_create',
      compact('trans_date','companies','accdoctype','company_code','voucher_no','finan_year'));
    }

    public function jv_modal_view($id)
    {

       $rows_m = AccTransactions::query()
         ->join("companies", "com_ref_id", "=", "companies.id")
         ->where('acc_transactions.id', $id)
         ->selectRaw("com_ref_id,companies.name,trans_type,voucher_no,t_narration,voucher_date")
         ->get();

        $rows_d = AccTransactionDetails::query()
          ->join("chartofaccounts", "chartofaccounts.id", "=", "chart_of_acc_id")
          ->where('acc_trans_id', $id)
          ->selectRaw("acc_code,acc_head,acc_origin,acc_level,d_amount,c_amount")
          ->orderBy('acc_trans_id', 'desc')
          ->get();

      return view('accounts.jv_viewmodal',compact('rows_m','rows_d'));
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function acctrans_store(Request $request)
    {
      $company_code = $request->company_code;
      $doctype      = $request->doc_type;
      $trans_type   = $request->jv;
      $trans_date   = $this->getDateAttribute($request->trans_date);

      $dropdownscontroller = new DropdownsController();
      $accdoctype = $dropdownscontroller->comboAccDocTypeList($trans_type,$company_code); // voucher type list
      $companies  = $dropdownscontroller->comboCompanyAssignList();

      $generalscontroller = new GeneralsController();
      $voucher_no = $generalscontroller->getMaxAccVoucherNo($doctype,$company_code); // getting max Voucher No
      $voucher_no = $voucher_no + 1;

      // checking Fin year Declaration
      $yearValidation = $generalscontroller->getFinYearValidation($company_code,$trans_date);
      if($yearValidation) {

      // Insert Transaction Master Records
      $trans_id = AccTransactions::insertGetId([
        'com_ref_id'    => $request->company_code,
        'voucher_date'  => $trans_date,
        'trans_type'    => $request->doc_type,
        'voucher_no'    => $voucher_no,
        't_narration'   => $request->narration,
        ]);
      //return $trans_id;

      // Insert Details Records
      $detId = $request->input('AccHeadCodeId');
      //dd($detId);
      if ($detId){
          foreach ($detId as $key => $value){
            if ($request->AccHeadCodeId[$key] != ''){
              AccTransactionDetails::create([
                  'acc_trans_id'    => $trans_id,
                  'd_amount'        => $request->Debit[$key] == '' ? 0:$request->Debit[$key],
                  'c_amount'        => $request->Crebit[$key] == '' ? 0:$request->Crebit[$key],
                  'chart_of_acc_id' => $request->AccHeadCodeId[$key],
              ]);
            }
          }
      }

      $voucher = $request->doc_type .'-'. $voucher_no;
      return redirect()->back()->with('message','Journal Created Successfull >>> '.$voucher)->withInput();
      //return back()->withInput(Input::all());
    }else{
      return back()->with('message','System Does not allow Posting. Due to Accounting Period')->withInput();
    }
      //return redirect()->route('acctrans.jv.index')->with('message','Journal Created Successfull >>> '.$voucher);
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
          if($i == 0 and $count < 2 ){
            $q .= " acc_code like '".$myArray[$i]."%'";
          }if($i == 0 and $count > 2 ) {
            $q .= " acc_code like '".$myArray[$i]."%'";
          }else if($i != 0) {
            $q .= " or acc_code like '".$myArray[$i]."%'";
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

}
