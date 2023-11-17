<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use Carbon\Carbon;

use App\Models\Employees\Employees;
use PDF;
use Response;

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {

   }

   public function getDeptSectWiseList(Request $request)
   {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      if($request->filled('company_code')){
        $company_code = $request->get('company_code');
      }else{
        $company_code = $dropdownscontroller->defaultCompanyCode();
      } 
      $generalscontroller  = new GeneralsController();
      $comp_name = $generalscontroller->CompanyName($company_code);
     
      $q = Employees::query()
        ->join("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
        ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
        ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
        ->where('emp_com_id', $company_code)
        ->selectRaw('emp_id_no,emp_name,emp_joining_dt,emp_joining_salary,emp_present_salary,desig.vComboName as designation,sec.vComboName as section,dept.vComboName as department');

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
            
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
        $q->where('emp_joining_dt','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $todate  = date('Y-m-d',strtotime($request->get('todate')));
        $q->where('emp_joining_dt','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }

     $q->orderBy('sec.vComboName', 'asc');
     $rows = $q->orderBy('dept.vComboName', 'asc')->get();


     if ($request->input('submit') == "pdf"){
      $fileName = 'customer_list';
      $pdf = PDF::loadView('/employees/reports/rpt_dept_sec_wise_list_pdf',
      compact('rows','companies','company_code','comp_name',), [], [
           'title' => $fileName,
     ]);
     return $pdf->stream($fileName,'.pdf');
    }

    $collect = collect($rows);
    // get requested action
    return view('/employees/reports/rpt_dept_sec_wise_list', compact('rows','companies','company_code','comp_name','fromdate','todate'));

  }
  
  public function getPendMachineList(Request $request)
  {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      if($request->filled('company_code')){
        $company_code = $request->get('company_code');
      }else{
        $company_code = $dropdownscontroller->defaultCompanyCode();
      } 
      $generalscontroller  = new GeneralsController();
      $comp_name = $generalscontroller->CompanyName($company_code);
     
      $q = Employees::query()
        ->join("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
        ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
        ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
        ->where('emp_com_id', $company_code)
        ->where('emp_secret_id','=',null)
        ->selectRaw('emp_id_no,emp_name,emp_joining_dt,emp_joining_salary,emp_present_salary,desig.vComboName as designation,sec.vComboName as section,dept.vComboName as department');

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
            
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
        $q->where('emp_joining_dt','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $todate  = date('Y-m-d',strtotime($request->get('todate')));
        $q->where('emp_joining_dt','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }

     $q->orderBy('sec.vComboName', 'asc');
     $rows = $q->orderBy('dept.vComboName', 'asc')->get();


    //  if ($request->input('submit') == "pdf"){
    //   $fileName = 'customer_list';
    //   $pdf = PDF::loadView('/employees/reports/rpt_dept_sec_wise_list_pdf',
    //   compact('rows','companies','company_code','comp_name',), [], [
    //        'title' => $fileName,
    //  ]);
    //  return $pdf->stream($fileName,'.pdf');
    // }

    $collect = collect($rows);
    // get requested action
    return view('/employees/reports/rpt_pend_machine_list', compact('rows','companies','company_code','comp_name','fromdate','todate'));

  }
   

}
