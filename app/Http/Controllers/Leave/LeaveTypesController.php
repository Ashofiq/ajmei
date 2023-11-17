<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;

use App\Models\Settings\SysInfos;
use App\Models\Settings\DropdownTypes;
use App\Models\Employees\Employees;
use App\Models\Leave\EmpLeaveTypes;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;

class LeaveTypesController extends Controller
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

      $rows = EmpLeaveTypes::query()->selectRaw('emp_leave_types.*,companies.name')
        ->Join("companies", "ltype_comp_id", "=", "companies.id")
        ->orderBy('emp_leave_types.id', 'desc')->paginate(10); 
      return view ('/leave/create_types', compact('rows','company_code','companies'));
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
            'leave_type'  =>'required',  
            'leave_desc'  =>'required', 
            'leave_days'  =>'required', 
        ]);
   
        $inputdata = new EmpLeaveTypes(); 
        $inputdata->ltype_comp_id  = $request->company_code;
        $inputdata->leave_type     = $request->leave_type;   
        $inputdata->leave_desc     = $request->leave_desc; 
        $inputdata->days           = $request->leave_days; 
        $inputdata->save();

        return redirect()->back()->with('message','Leave Types Created Successfull !')->withInput();
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
          EmpLeaveTypes::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
