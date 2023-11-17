<?php

namespace App\Http\Controllers\Salary;

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
 
use App\Models\Companies;
use App\Models\Months;
use App\Models\Years;

use App\Models\Attendance\EmpAttendhistories;
use App\Models\Salary\EmpSalaryHistories;
use App\Models\Employees\Employees;

use PDF;
use Response;

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {

   }

   public function getMonthlySalaryData(Request $request)
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

      $months = Months::query()->orderBy('id','asc')->get();
      $years = Years::query()->orderBy('iYear','asc')->get();  

      $employees = Employees::query()
      //->where('emp_com_id','=',$company_code)
      ->orderBy('emp_com_id','asc')->get();

      $q = EmpSalaryHistories::query()
        ->join("employees as emp", "vEmpId", "=", "emp.id")
        ->leftjoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
        ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
        ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
        ->selectRaw('emp_salary_histories.*,TIME_TO_SEC(emp_salary_histories.dOTHour) as ot_hour_sec, emp_id_no,emp_joining_dt,emp_name,desig.vComboName as designation,sec.vComboName as section,dept.vComboName as department');
    
      $q->where('iSalCompId','=',$company_code);
        
      $employee_id = '';
      $month = '';
      $year = '';
      if($request->filled('employee_id')){ 
        $employee_id = $request->get('employee_id');
        $q->where('emp_id_no','=',$employee_id);
      }
      if($request->filled('month')){
        $month = $request->get('month');
        $q->where('iMonth','=', $month);
      }
      if($request->filled('year')){ 
        $year = $request->get('year');
        $q->where('iYear','=', $year);
      }
    
    $q->orderBy('dept.vComboName', 'asc'); 
    $q->orderBy('sec.vComboName', 'asc');
    $rows = $q->orderBy('vEmpIdNo', 'asc')->get();
    
    if ($request->input('submit') == "pdf"){
      $fileName = 'Monthly_Salary_Report';
      $pdf = PDF::loadView('/salary/reports/rpt_monthly_salary_data_pdf',
      compact('rows','companies','company_code','comp_name','months','month','years','year','employee_id','employees',), [], [
        'title' => $fileName,
        'format' => 'A4-L',
        'orientation' => 'L',
        ]);
        $pdf->shrink_tables_to_fit=1;
        return $pdf->stream($fileName,'.pdf');
    }

    $collect = collect($rows);
    // get requested action
    return view('/salary/reports/rpt_monthly_salary_data', compact('rows','companies','company_code','comp_name','months','month','years','year','employee_id','employees'));

  }
 
   

}
