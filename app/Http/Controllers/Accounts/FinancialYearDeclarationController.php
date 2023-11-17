<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\FinancialYearDeclaration;
use DB;

class FinancialYearDeclarationController extends Controller
{
  public function index(Request $request)
  {
    $dropdownscontroller = new DropdownsController();
    $default_comp_code = $dropdownscontroller->defaultCompanyCode();
    $companies  = $dropdownscontroller->comboCompanyAssignList();
    $finyeardecs = FinancialYearDeclaration::query()
                  ->join("companies", "financial_year_declarations.comp_id", "=", "companies.id")
                  ->where('comp_id', $default_comp_code)
                  ->selectRaw("financial_year_declarations.id,
                  financial_year_declarations.date_from,financial_year_declarations.date_to,
                  financial_year_declarations.status,  financial_year_declarations.comp_id,
                  companies.name")
                  ->orderBy('financial_year_declarations.id', 'asc')->paginate(10);
    $collect = collect($finyeardecs);
    // get requested action
    return view('/accounts/finyeardeclaration_index', compact('finyeardecs','companies','default_comp_code'));
  }

  public function store(Request $request)
  {

    // Validate the Field
      $this->validate($request,[
          'company_code' =>'required',
          'fromdate'     =>'required',
          'todate'       =>'required',
          'status'      =>'required',
      ]);
      
      DB::update("update financial_year_declarations set status = 0
      where comp_id = ?", [$request->company_code]);

      $inputdata = new FinancialYearDeclaration();
      $inputdata->comp_id   = $request->company_code;
      $inputdata->date_from = $request->fromdate;
      $inputdata->date_to   = $request->todate;
      $inputdata->status    = $request->input('status')=='on'?1:0;
      $inputdata->save();

      //return redirect()->route('finyeardec.index')->with('message','Financial Declaration Created Successfull !');
      return back()->withInput();
  }

  public function getStatusChange($action, $id, $compid){
    
    DB::update("update financial_year_declarations set status = 0
    where comp_id = ?", [$compid]);
    
    $inputdata = FinancialYearDeclaration::find($id);
    $inputdata->status  = $action;
    $inputdata->save();
    return redirect()->route('finyeardec.index')->with('message','Updated Successfull !');

  }

  public function destroy($id)
  {
      try{
        //return $id;
        $generalscontroller = new GeneralsController();
        $acc_trans_rec = $generalscontroller->getFinYearData($id);
        if($acc_trans_rec == 0) {
           FinancialYearDeclaration::where('id',$id)->delete();
        }else{
           return redirect()->back()->with('message','Delete Unsuccessfull! Transaction Existed in this financial year');
        }
      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage());
      }
      return redirect()->back()->with('message','Financial Declaration Deleted Successfull');
  }


}
