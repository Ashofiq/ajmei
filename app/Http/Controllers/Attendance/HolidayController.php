<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;

use App\Models\Settings\SysInfos;
use App\Models\Settings\DropdownTypes;
use App\Models\Employees\Employees;
use App\Models\Attendance\EmpHolidays;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $dropdownscontroller = new DropdownsController();
        $company_code = $dropdownscontroller->defaultCompanyCode();
        $companies  = $dropdownscontroller->comboCompanyAssignList(); 
        $sysinfos   = SysInfos::query() 
        ->where('vComboType', 'Shift')
        ->orderBy('vComboName','asc')->get();

        $rows = EmpHolidays::query()
          ->selectRaw('*')
          ->orderBy('emp_holidays.id', 'desc')->paginate(10);
        return view ('/holidays/index', compact('rows','sysinfos'));
    }

    public function search(Request $request)
    {
        $iShiftId  = $request->get('shift_id');
        $dropdownscontroller = new DropdownsController();
        $company_code = $dropdownscontroller->defaultCompanyCode();
        $companies  = $dropdownscontroller->comboCompanyAssignList(); 
        $sysinfos   = SysInfos::query() 
        ->where('vComboType', 'Shift')
        ->orderBy('vComboName','asc')->get();

        $rows = EmpHolidays::query()
          ->Join("sys_infos as infos", "infos.id", "=", "iShiftId") 
          ->selectRaw('emp_timesheets.*,vComboType,vComboName,vComboDesc') 
          ->where('emp_timesheets.iShiftId', $iShiftId)
          ->orderBy('emp_timesheets.id', 'desc')->paginate(10)->setpath('');

        $rows->appends(array(
          'shift_id' => $iShiftId,
        ));
        $collect = collect($rows);
        return view ('/holidays/index', compact('rows','sysinfos'));
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
        $sysinfos   = SysInfos::query() 
        ->where('vComboType', 'Shift')
        ->orderBy('vComboName','asc')->get();
        return view('/holidays/create',compact('companies','company_code','sysinfos'));
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
            'holiday_date' =>'required', 
            'remarks'      =>'required',  
        ]); 

        $inputdata = new EmpHolidays(); 
        $inputdata->hol_comp_id = $request->company_code; 
        $inputdata->dtDate      = date('Y-m-d',strtotime($request->get('holiday_date')));  
        $inputdata->vRemarks    = $request->remarks;  
        $inputdata->save();

        return redirect()->back()->with('message','Hoilday Created Successfull !')->withInput();
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
        
        $row = EmpHolidays::query() 
        ->selectRaw('emp_holidays.*')
        ->where('emp_holidays.id', $id)->first();  
        
      return view('/holidays/edit',compact('company_code','row','companies'));
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
      $id = $request->edit_id;

      $this->validate($request,[
        'company_code' =>'required',
        'holiday_date' =>'required', 
        'remarks'      =>'required',  
      ]);

      $inputdata = EmpHolidays::find($id); 
      $inputdata->hol_comp_id = $request->company_code;
      $inputdata->dtDate      = date('Y-m-d',strtotime($request->get('holiday_date')));  
      $inputdata->vRemarks    = $request->remarks;  
      $inputdata->save(); 
      return back()->with('message','Holiday Update Successfull !')->withInput();
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
          EmpHolidays::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
