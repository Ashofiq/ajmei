<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;

use App\Models\Settings\SysInfos;
use App\Models\Settings\DropdownTypes;
use App\Models\Employees\Employees;
use App\Models\Attendance\EmpManualAttendances;
use App\Models\Attendance\WeekEndEntry;
use App\Models\Attendance\WeekEndEntryValue;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;
use PDF;
use Helper;

class ManualAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) 
    {   
        $dropdownscontroller = new DropdownsController();
        $company_code = $dropdownscontroller->defaultCompanyCode();
        $rows = EmpManualAttendances::query()
          ->Join("employees as employees", "employees.id", "=", "vEmpId")
          ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
          ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
          ->selectRaw('emp_manual_attendances.id,vEmpId,dtAttInTime,dtAttOutTime,vAttType,emp_id_no,emp_uid_no_old,emp_old_id_no,emp_secret_id,emp_name,emp_bank_acc_no,emp_national_id,emp_skill_grade,emp_birth_dt,emp_joining_dt,emp_joining_salary,emp_present_salary,emp_actual_salary,emp_others_salary,emp_sal_grade,emp_promo_date,emp_out_date,desig.vComboName as designation,dept.vComboName as department')
          ->where('emp_manual_attendances.iatt_comp_id', $company_code)->orderBy('employees.id', 'desc')->paginate(10);

        $employees = Employees::query()->where('emp_com_id', $company_code)->orderBy('emp_name','asc')
          ->where('emp_status', 1)->get();

        $departments = SysInfos::where('vComboType', 'Department')->get();
        $sections = SysInfos::where('vComboType', 'Section')->get();

        $results = [];
        $departmentId = 0;
        $sectionId = 0;
        if (isset($request->departmentId)) {
          $departmentId = $request->departmentId;
          $sectionId = $request->sectionId;

          if (isset($request->new)) {
            
          }else{
            $results = Employees::with('section', 'department', 'designation')
            ->where('emp_dept_ref_id', $departmentId)
            ->where('emp_sec_ref_id', $sectionId)
            ->where('emp_outmark_ref_id', 'N')->get();
          }

        }else{

        }

        if (isset($request->new)) {

          $doneIds = WeekEndEntryValue::where('departmentId', $request->departmentId)
          ->where('sectionId', $request->sectionId)->where('fromDate', $request->fromdate)
          ->where('toDate', $request->todate)->select('empId')->get();
          $allReadyDoneIds = [];
          foreach ($doneIds as $key => $value) {
            $allReadyDoneIds[] = $value->empId;
          }

          $results = Employees::with('section', 'department', 'designation')
          ->where('emp_dept_ref_id', $departmentId)
          ->whereNotIn('id', $allReadyDoneIds)
          ->where('emp_sec_ref_id', $sectionId)
          ->where('emp_outmark_ref_id', 'N')->get();
        }

        $lastEntry = WeekEndEntry::with('department', 'section')->where('active', 1)->first();

        $emp_ottype_ref_id = '';
        if ($lastEntry->employeeType == 'worker') {
          $emp_ottype_ref_id = 'Y';
        }else{
          $emp_ottype_ref_id = 'N';
        }

        $results = Employees::with('section', 'department', 'designation', 'week_entry_value')
        ->where('emp_outmark_ref_id', 'N')
        ->where('emp_ottype_ref_id', $emp_ottype_ref_id)
        ->orderBy('emp_sec_ref_id')->get();

        
        if($lastEntry == null){
          $results = [];
          $lastEntryMessage = 'Please Add Process Salary';
          return view ('/attendance/index', compact('rows','employees', 'departments', 'sections', 'results', 'departmentId', 'sectionId', 'lastEntry', 'lastEntryMessage'));
        }
        
        $empData = [];
        foreach ($results as $key => $value) {

          $WeekEndEntryValue = WeekEndEntryValue::where('empId', $value->id)
                                                  ->where('fromDate', $lastEntry->fromDate)
                                                  ->where('toDate', $lastEntry->toDate)
                                                  ->first();

          $empData[] = array(
            'id' => $value->id,
            'idNo' => $value->emp_id_no,
            'name' => $value->emp_name,
            'department' => $value->department->vComboName,
            'section' => $value->section->vComboName ?? '',
            'designation' => $value->designation->vComboName,
            'salary' => $value->emp_present_salary,
            'day' => ($WeekEndEntryValue != null) ? $WeekEndEntryValue->day : (($lastEntry->employeeType == 'worker') ? 0 : 0),
            'previousDays' => ($WeekEndEntryValue != null) ? $WeekEndEntryValue->previousDays : 0,
            'hour' => ($WeekEndEntryValue != null) ? $WeekEndEntryValue->hour : (($lastEntry->employeeType == 'worker') ? 0 : 0),
            'otHour' => ($WeekEndEntryValue != null) ? $WeekEndEntryValue->otHour : (($lastEntry->employeeType == 'worker') ? 0 : 0),
            'adv_deduction' => (isset($value->week_entry_value)) ? $value->week_entry_value->adv_deduction : 0,
            'attnBonus' => $value->designation->attn_bonus,
            'departmentId' => $value->department->id,
            'sectionId' => $value->section->id ?? 0,
            'yMark' => ($WeekEndEntryValue != null) ? $WeekEndEntryValue->yMark : '',
            'unitId' => $value->emp_unit_ref_id,
            'weekendValueId' => ($WeekEndEntryValue != null) ? $WeekEndEntryValue->id : null
          ); 
        }


      $results = $empData;

        
      return view ('/attendance/index', compact('rows','employees', 'departments', 'sections', 'results', 'departmentId', 'sectionId', 'lastEntry'));
    }


    public function WeekEndEntry(Request $request){
      // WeekEndEntry
      
      // if (!isset($request->new)) {
      //   $check = WeekEndEntry::where('fromDate', $request->from)
      //     ->orWhere('toDate', $request->to)->first();
      //   if ($check != null) {
      //     return false;
      //   }
      // }
      
      // $WeekEndEntry = new WeekEndEntry();
      // $WeekEndEntry->fromDate = $request->from;
      // $WeekEndEntry->toDate = $request->to;
      // $WeekEndEntry->title = $request->title;
      
      // if($WeekEndEntry->save()){

        foreach ($request->data as $key => $value) {

         

          $check = WeekEndEntryValue::where('fromDate', $request->from)
            ->where('toDate', $request->to)
            ->where('empId', $value['id'])->first();
         
          $day = $value['day'];
          $previousDays = $value['previousDays'];
          $attnBonus = 0;
          if($value['yMark'] == 'y' or $value['yMark'] == 'Y'){
            $attnBonus = ($value['bonus'] / 6) * $day;
          }else{
            $attnBonus = ($day == 6) ? $value['bonus'] : 0;
          }


          if ($check == null) {
            $WeekEndEntryValue = new WeekEndEntryValue();
            $WeekEndEntryValue->weekEndEntryId = $request->activeEntry;
            $WeekEndEntryValue->empId = $value['id'];
            $WeekEndEntryValue->day = $value['day'];
            $WeekEndEntryValue->previousDays = $value['previousDays'];
            $WeekEndEntryValue->hour = $value['hour'];
            $WeekEndEntryValue->otHour = $value['otHour'];
            $WeekEndEntryValue->adv_deduction = $value['adv_deduction'];
            $WeekEndEntryValue->attnBonus =  $attnBonus;
            $WeekEndEntryValue->amount = 100;
            $WeekEndEntryValue->fromDate = $request->from;
            $WeekEndEntryValue->toDate = $request->to;  
            $WeekEndEntryValue->departmentId = $value['departmentId'];
            $WeekEndEntryValue->sectionId = $value['sectionId'];
            $WeekEndEntryValue->yMark = $value['yMark'];
            $WeekEndEntryValue->unitId = $value['unitId'];

            if ($value['day'] != 0) { 
              $WeekEndEntryValue->save();  
            }

          }else{
            $updateWeekEndEntryValue = WeekEndEntryValue::where('weekEndEntryId', $request->activeEntry)->where('empId', $value['id'])->where('fromDate', $request->from)->where('toDate', $request->to)->first();
            // $newUpdate = WeekEndEntryValue::find($updateWeekEndEntryValue->id);

            $newUpdate = WeekEndEntryValue::find($value['weekendValueId']);

            $newUpdate->weekEndEntryId = $request->activeEntry;
            $newUpdate->empId = $value['id'];
            $newUpdate->day = $value['day'];
            $newUpdate->previousDays = $value['previousDays'];
            $newUpdate->hour = $value['hour'];
            $newUpdate->otHour = $value['otHour'];
            $newUpdate->adv_deduction = $value['adv_deduction'];
            $newUpdate->attnBonus =  $attnBonus;
            $newUpdate->amount = 100;
            $newUpdate->fromDate = $request->from;
            $newUpdate->toDate = $request->to;
            $newUpdate->departmentId = $value['departmentId'];
            $newUpdate->sectionId = $value['sectionId'];
            $newUpdate->yMark = $value['yMark'];
            $newUpdate->unitId = $value['unitId'];
             
            if ($value['day'] == 0) { 
               // when 0 data delete 
               WeekEndEntryValue::find($newUpdate->id)->delete();
            }else{
              $newUpdate->save(); 
            }
          }
          
        
        }
        
      return true;
    }

    public function list()
    {
      $data['lists'] = WeekEndEntry::orderBy('id', 'DESC')->get();
      $data['sections']   = SysInfos::query()->whereIn('vComboType', ['Section'])->get(); 

      return view('/attendance/list', $data);
    }

    public function details($id, $sectionId){

      $WeekEndEntry = WeekEndEntry::with('department', 'section')->find($id);

      $fileName = $WeekEndEntry->title;

      $j = 1;
      $emps = WeekEndEntryValue::with('department', 'section', 'employee', 'unit')
        ->Join("employees as employees", "employees.id", "=", "empId")
        ->leftJoin("sys_infos as desig", "desig.id", "=", "employees.emp_desig_ref_id")
        ->where('sectionId', $sectionId)->where('weekEndEntryId', $WeekEndEntry->id)->get();

      $index = [];
      for ($i=1; $i < COUNT($emps); $i++) { 
          $index[] = $i * 10;
      }
      

      $data['index'] = $index;
        
        $data['totalCount'] = COUNT($emps);
        
        $empdata = [];
        $total = 0;
        $otTime = 0;
        $dutyTime = 0;
        $attnBonus = 0;

        $pageTotal = 0;
        $pageOtTime = 0;
        $pageDutyTime = 0;
        $pageAttnBonus = 0;

        $i = 1;
        $new = [];
        $new1 = [];
        foreach ($emps as $key1 => $value1) {
          $totalWages = 0;
          
          // $absenceDay = 30 - $value1->day;
          // $totalWages = round(($value1->employee->emp_present_salary / 30)  * $value1->day -  $value1->adv_deduction);
          if ($WeekEndEntry->employeeType == 'worker') {
            $totalWages = round(($value1->employee->emp_present_salary / 8) * ( $value1->hour + $value1->otHour ) + $value1->attnBonus) - $value1->adv_deduction;
          }else{
            $absenceDay = 30 - $value1->day;
            $totalWages = round(($value1->employee->emp_present_salary / 30)  * $value1->day -  $value1->adv_deduction);
          }

          $empdata[] = array(
            'sl' => $i, 
            'name' => $value1->employee->emp_name,
            'designation' => $value1->vComboName,
            'cartNo' => $value1->emp_id_no,
            'wages' => $value1->employee->emp_present_salary,
            'attendanceDay' => $value1->day,
            'absenceDay' => (($WeekEndEntry->employeeType == 'worker') ? 6 - $value1->day : 30 - $value1->day),
            'adv_deduction' => $value1->adv_deduction,
            'previousDays' => $value1->previousDays,
            'dutyTime' => $value1->hour,
            'otTime' => $value1->otHour,
            'attnBonus' => $value1->attnBonus,
            'totalWages' => $totalWages
          );

          $pageTotal += $totalWages;
          $pageOtTime += $value1->otHour;
          $pageDutyTime += $value1->hour;
          $pageAttnBonus += $value1->attnBonus;

          if(in_array($i, $index)){

            $new[] = array(
              'empdata' => array_values($empdata),
              'pageTotal' => $pageTotal,
              'pageOtTime' => $pageOtTime,
              'pageDutyTime' => $pageDutyTime,
              'pageAttnBonus' => $pageAttnBonus
            );

            $pageTotal = 0;
            $pageOtTime = 0;
            $pageDutyTime = 0;
            $pageAttnBonus = 0;
            $empdata = [];
            $new1 = [];
          }else{
            $new1[] = array(
              'empdata' => array_values($empdata),
              'pageTotal' => $pageTotal,
              'pageOtTime' => $pageOtTime,
              'pageDutyTime' => $pageDutyTime,
              'pageAttnBonus' => $pageAttnBonus
            );
          }

          $total += $totalWages;
          $otTime += $value1->otHour;
          $dutyTime += $value1->hour;
          $attnBonus += $value1->attnBonus;

          $i++;
        }
        
        $j++;

        if (COUNT($new1) != 0) {
          $new[] = $new1[COUNT($new1) -1];
        }
        
        $sectionData = array(
          'sectionName' => (isset($value1)) ? $value1->section->vComboName : '',
          'unitName' => (isset($value1)) ? $emps[0]->unit->vComboName : '',
          'totalWages' => $total,
          'totalOtTime' => $otTime,
          'totalDutyTime' => $dutyTime,
          'totalAttnBonus' => $attnBonus,
          'new' => $new
        );

      $data['sectionData'] = $sectionData;

      $data['from'] = $this->getDateFormate($WeekEndEntry->fromDate);
      $data['to'] = $this->getDateFormate($WeekEndEntry->toDate);

      //  return $data;
      $data['info'] = $WeekEndEntry;
      
      // return $data;
      $pdf = PDF::loadView('/attendance/details',
        $data, [], [
          'title' => $fileName,
          'orientation' => 'L'
        ]);
        
       // return view('/attendance/details',  $data);

      return $pdf->stream($fileName.'.pdf','.pdf');
    }

    public function wages_sheet($id){

      $WeekEndEntry = WeekEndEntry::with('department', 'section')->find($id);

      $sectionIds = WeekEndEntryValue::select(['sectionId'])->groupBy('sectionId')->get();
      
      $data['lists'] = WeekEndEntryValue::with('department', 'section', 'employee')
      ->Join("employees as employees", "employees.id", "=", "empId")
      ->leftJoin("sys_infos as desig", "desig.id", "=", "employees.emp_desig_ref_id")
      ->where('weekEndEntryId', $id)
      ->select(['sectionId'])->groupBy('sectionId')
      ->get();

      
      $lists = [];
      $j = 1;
      $sectionData = [];
      foreach ($data['lists'] as $key => $value) {
        $emps = WeekEndEntryValue::with('department', 'section', 'employee')
        ->Join("employees as employees", "employees.id", "=", "empId")
        ->leftJoin("sys_infos as desig", "desig.id", "=", "employees.emp_desig_ref_id")
        ->where('weekEndEntryId', $id)
        ->where('sectionId', $value->sectionId)->get();

        $advance = 0;
        $total = 0;
        foreach ($emps as $key1 => $value1) {
          // $totalWages = round(($value1->employee->emp_present_salary / 8) * ( $value1->hour + $value1->otHour ) + $value1->attnBonus);
          $Employee = Employees::where('id', $value1->empId)->first();

          if ($WeekEndEntry->employeeType == 'worker') {
            $totalWages = round(($Employee->emp_present_salary / 8) * ( $value1->hour + $value1->otHour ) + $value1->attnBonus) - $value1->adv_deduction;
          }else{
            $absenceDay = 30 - $value1->day;
            $totalWages = round(($value1->employee->emp_present_salary / 30)  * $value1->day -  $value1->adv_deduction);
          }
          $total += $totalWages;

          $advance += $value1->adv_deduction;
        }
        
        $sectionData[] = array(
          'sl' => $j,
          'sectionName' => $value->section->vComboName,
          'amount' => $total,
          'advance' => $advance,
          'person' => count($emps),
        );
        
        $j++;
      }

      $data['lists'] = $sectionData;
      $data['from'] = $this->getDateFormate($WeekEndEntry->fromDate);
      $data['to'] = $this->getDateFormate($WeekEndEntry->toDate);
      $data['paymentDate'] = $this->getDateFormate($WeekEndEntry->paymentDate);

      $pdf = PDF::loadView('/attendance/wages-sheet',
      $data, [], [
        'title' => $WeekEndEntry->title,
      ]);

      return $pdf->stream($WeekEndEntry->title.'.pdf','.pdf');

      
    }

    public function getDateFormate($data){
      $time = strtotime($data);
      $newformat = date('d/m/Y', $time);
      return $newformat;
    }

    public function attendanceEdit($id){
      $data['lists'] =  WeekEndEntryValue::with('department', 'section', 'employee')->where('weekEndEntryId', $id)->get();
      return view('attendance/attendance-edit', $data);
    }


    public function search(Request $request)
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $employee_id = $request->get('employee_id');
      $employees = Employees::query()->where('emp_com_id', $company_code)->orderBy('emp_name','asc')
      ->where('emp_status', 1)->get(); 
      $rows = EmpManualAttendances::query()
          ->Join("employees as employees", "employees.id", "=", "vEmpId")
          ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
          ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
          ->selectRaw('emp_manual_attendances.id,vEmpId,dtAttInTime,dtAttOutTime,vAttType,emp_id_no,emp_uid_no_old,emp_old_id_no,emp_secret_id,emp_name,emp_bank_acc_no,emp_national_id,emp_skill_grade,emp_birth_dt,emp_joining_dt,emp_joining_salary,emp_present_salary,emp_actual_salary,emp_others_salary,emp_sal_grade,emp_promo_date,emp_out_date,desig.vComboName as designation,dept.vComboName as department') 
        ->where('employees.id', $employee_id)
        ->orderBy('employees.id', 'desc')->paginate(10)->setpath('');

        $rows->appends(array(
          'employee_id' => $employee_id,
        ));
        $collect = collect($rows);
        return view ('/attendance/index', compact('rows','employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dropdownscontroller = new DropdownsController();
        $company_code = $dropdownscontroller->defaultCompanyCode();
        $companies  = $dropdownscontroller->comboCompanyAssignList(); 
        $employees = Employees::query()->where('emp_com_id', $company_code)->orderBy('emp_name','asc')
        ->where('emp_status', 1)->get();
        return view('/attendance/create',compact('companies','company_code','employees'));
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
        $this->validate($request,[
            'company_code' =>'required',
            'employee_id' =>'required',  
            'att_type'    =>'required',
        ]);

        $inputdata = new EmpManualAttendances(); 
        $inputdata->iatt_comp_id  = $request->company_code;
        $inputdata->vEmpId      = $request->employee_id;   
        $inputdata->dtAttDate   = date('Y-m-d',strtotime($request->inDate));
        $inputdata->dtAttInTime = $request->inDate ==''?null:date('Y-m-d H:i:s',strtotime($request->inDate));  
        $inputdata->dtAttOutTime = $request->outDate ==''?null:date('Y-m-d H:i:s',strtotime($request->outDate));  
        $inputdata->vAttType = $request->att_type;
        $inputdata->vRemarks = 'Manual Entry'; 
        $inputdata->save();

        return redirect()->back()->with('message','Attendance Created Successfull !')->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

      $dropdownscontroller = new DropdownsController();
      $generalscontroller = new GeneralsController();

      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
        
      $employees = Employees::query()->orderBy('emp_name','asc')
      ->where('emp_status', 1)->get();

      $row = EmpManualAttendances::query()
        ->Join("employees as employees", "employees.id", "=", "vEmpId")
        ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
        ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
        ->leftJoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
        ->selectRaw('emp_manual_attendances.*,emp_id_no,desig.vComboName as designation,dept.vComboName as department,sec.vComboName as section')
        ->where('emp_manual_attendances.id', $id)->first();  
      return view('/attendance/edit',compact('company_code','row','companies','employees'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
      $id = $request->employee_edit_id;

      $this->validate($request,[
          'company_code' =>'required',
          'employee_id' =>'required',  
          'att_type'    =>'required',
      ]);

      $inputdata = EmpManualAttendances::find($id); 
      $inputdata->iatt_comp_id  = $request->company_code;
      $inputdata->vEmpId      = $request->employee_id; 
      $inputdata->dtAttDate   = date('Y-m-d',strtotime($request->inDate));  
      $inputdata->dtAttInTime = $request->inDate ==''?null:date('Y-m-d H:i:s',strtotime($request->inDate));  
      $inputdata->dtAttOutTime = $request->outDate ==''?null:date('Y-m-d H:i:s',strtotime($request->outDate));  
      $inputdata->vAttType = $request->att_type;
      $inputdata->vRemarks = 'Manual Entry'; 
      $inputdata->save(); 
      return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        //return  $id;
        try{
          EmpManualAttendances::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
