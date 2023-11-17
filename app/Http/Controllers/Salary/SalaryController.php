<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Months;
use App\Models\Years;
use App\Models\Settings\SysInfos;
use App\Models\Settings\DropdownTypes;
use App\Models\Employees\Employees;
use App\Models\Attendance\WeekEndEntry;
use App\Models\Attendance\WeekEndEntryValue;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;
use Validator;
use DB;

class SalaryController extends Controller
{ 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function process()
    {   
        $month_no = '';
        $year_no = '';
        $months = Months::query()->orderBy('id','asc')->get();
        $years = Years::query()->orderBy('iYear','asc')->get();  
        $WeekEndEntry = WeekEndEntry::orderBy('id', 'DESC')->limit(8)->get();

        return view('/salary/salary_process',compact('month_no','year_no','months','years', 'WeekEndEntry'));
    }

    public function process_update($id){
      $WeekEndEntry = WeekEndEntry::where('active', 1)->first();
      if($WeekEndEntry != null){
        $WeekEndEntry->active = 0;
        $WeekEndEntry->save();
      }

      $WeekEndEntry = WeekEndEntry::find($id);
      $WeekEndEntry->active = 1;
      $WeekEndEntry->save();

      return back()->with('message', 'Successfully update');

  }

  public function process_add(Request $request){

    $validator = Validator::make($request->all(),[
      'title' => 'required',
      'fromDate' => 'required',
      'toDate' => 'required',
      'active' => 'required'
    ]);

    if ($validator->fails()) {
        return redirect('salary-process')->withErrors($validator)
                    ->withInput();
    }

    if($request->active == 1){
      $WeekEndEntry = WeekEndEntry::where('active', 1)->first();
      if($WeekEndEntry != null){
        $WeekEndEntry->active = 0;
        $WeekEndEntry->save();
      }
    }

    $WeekEndEntry = new WeekEndEntry();
    $WeekEndEntry->title = $request->title;
    $WeekEndEntry->fromDate = $request->fromDate;
    $WeekEndEntry->toDate = $request->toDate;
    $WeekEndEntry->active = $request->active;
    $WeekEndEntry->employeeType = $request->employeeType;
    $WeekEndEntry->paymentDate = $request->paymentDate;

    if($WeekEndEntry->save()){
      return back()->with('message', 'Successfully added');
    }

  }
  
  public function processedit($id){
      $data['WeekEndEntry'] = WeekEndEntry::find($id);
      return view('/salary/edit_salary_process', $data);

  }
  
  public function processUpdate($id, Request $request){
      
        $WeekEndEntry = WeekEndEntry::find($id);
        $WeekEndEntry->title = $request->title;
        $WeekEndEntry->fromDate = $request->fromDate;
        $WeekEndEntry->toDate = $request->toDate;
        $WeekEndEntry->employeeType = $request->employeeType;
        $WeekEndEntry->paymentDate = $request->paymentDate;
        
        if($WeekEndEntry->save()){
            return back()->with('message', 'Successfully Update');
        }
      
  }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function process1(Request $request)
    {
      $month_no = $request->get('month_no');
      $year_no = $request->get('year_no');
      $months = Months::query()->orderBy('id','asc')->get();
      $years = Years::query()->orderBy('iYear','asc')->get();  
 
      DB::statement('CALL salaryprocessing(?,?)',array($year_no,$month_no)); 
      return redirect()->back()->with('message','Salary Process Successfull !')->withInput();
    }
  
}
