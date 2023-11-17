<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;

use App\Models\Settings\SysInfos;
use App\Models\Settings\DropdownTypes;
use App\Models\Employees\Employees;
use App\Models\Attendance\EmpManualAttendances;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;
use DB;

class AttendanceController extends Controller
{ 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function process()
    {
        $fromdate = date('Y-m-d');
        return view('/attendance/attend_process',compact('fromdate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function process1(Request $request)
    {
      $processdate = date('Y-m-d',strtotime($request->get('process_date')));
      DB::statement('CALL InOutProcess(?)',array($processdate)); 
      return redirect()->back()->with('message','Attendance Process Successfull !')->withInput();
    }
  
}
