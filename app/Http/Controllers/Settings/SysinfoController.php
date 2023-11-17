<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Settings\SysInfos;
use App\Models\Settings\DropdownTypes;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;

class SysinfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $dropdowntypes = DropdownTypes::query()->orderBy('vDropDownName','asc')->get();
      $rows = SysInfos::query()
          ->join("companies", "sys_infos.combo_company_id", "=", "companies.id")
          ->where('combo_company_id',$company_code)
          ->selectRaw("sys_infos.id,combo_company_id,vComboType,vComboName,vComboDesc,attn_bonus,
          sys_infos.level,companies.name as comp_name")
          ->orderBy('sys_infos.main_code', 'asc')
          ->orderBy('sys_infos.sub_code', 'asc')
          ->get();
          
      
      $allSysinfo = SysInfos::select(['vComboType'])->groupBy('vComboType')->get();
      return view ('/settings/sysinfo_index', compact('rows','companies','company_code','dropdowntypes', 'allSysinfo'));
    }
    
    public function search(Request $request)
    {

      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $dropdowntypes = DropdownTypes::query()->orderBy('vDropDownName','asc')->get(); 

      $search_dropdown_type = $request->get('search_dropdown_type');
     
      $rows = SysInfos::query()
      ->join("companies", "sys_infos.combo_company_id", "=", "companies.id")
      ->where('combo_company_id',$company_code)
      ->selectRaw("sys_infos.id,combo_company_id,vComboType,vComboName,vComboDesc,
      sys_infos.level,companies.name as comp_name")
      ->where('sys_infos.vComboType', $search_dropdown_type)
      ->orderBy('sys_infos.id', 'asc')->paginate(10)->setpath(''); 

      $rows->appends(array(
        'search_dropdown_type' => $search_dropdown_type,
      ));
      $collect = collect($rows);
      $allSysinfo = SysInfos::select(['vComboType'])->groupBy('vComboType')->get();

      return view('/settings/sysinfo_index', compact('rows','companies','company_code','dropdowntypes', 'allSysinfo')); 
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          return view('/company/create');
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
            'company_code'  =>'required',
            'dropdown_type' =>'required',
            'dropdown_name' =>'required',
        ]);
        $inputdata = new SysInfos();
        $inputdata->combo_company_id = $request->company_code;
        $inputdata->vComboType  = $request->dropdown_type;
        $inputdata->vComboName  = $request->dropdown_name;
        $inputdata->vComboDesc  = $request->description;
        $inputdata->level       = $request->level;
        $inputdata->attn_bonus       = $request->attn_bonus;
        $generalscontroller = new GeneralsController();
        $isRec = $generalscontroller->duplicteName_sysinfos($request->dropdown_type,$request->dropdown_name);
        if($isRec == 0){
          $inputdata->save();  
          return redirect()->back()->with('message','Created Successfull !')->withInput();
        } else{ 
          return redirect()->back()->with('message','Duplicate Enrty! Created Unsuccessfull !')->withInput();
        }
        
       // $inputdata->save();
       // return redirect()->back()->with('message','Created Successfull !')->withInput();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajax_store(Request $request)
    {

      // Validate the Field
        $this->validate($request,[
            'company_code'  =>'required',
            'dropdown_type' =>'required',
            'dropdown_name' =>'required',
        ]);
        $inputdata = new SysInfos();
        $inputdata->combo_company_id = $request->company_code;
        $inputdata->vComboType  = $request->dropdown_type;
        $inputdata->vComboName  = $request->dropdown_name;
        $inputdata->vComboDesc  = $request->description;
        $inputdata->level       = $request->level;
        $inputdata->attn_bonus       = $request->attn_bonus;
        $generalscontroller = new GeneralsController();
        $isRec = $generalscontroller->duplicteName_sysinfos($request->dropdown_type,$request->dropdown_name);
        if($isRec == 0){
          $inputdata->save();
          echo json_encode(array("statusCode"=>200));
        } else{
            echo json_encode(array("statusCode"=>201));
        }
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('/company/company_edit');
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
      
      $id = $request->id;

     // Validate the Field
       $this->validate($request,[
         'company_code'  =>'required',
         'dropdown_type' =>'required',
         'dropdown_name' =>'required',
       ]);

       $inputdata              = SysInfos::find($id);
       $inputdata->combo_company_id = $request->company_code;
       $inputdata->vComboType  = $request->dropdown_type;
       $inputdata->vComboName  = $request->dropdown_name;
       $inputdata->vComboDesc  = $request->description;
       $inputdata->level       = $request->level;
       $inputdata->attn_bonus       = $request->attn_bonus;
       $generalscontroller = new GeneralsController();
      //  $isRec = $generalscontroller->duplicteName_sysinfos($request->dropdown_type,$request->dropdown_name);
       if($inputdata->save()){
         return redirect()->back()->with('message','Updated Successfull !')->withInput(); 
       } else{ 
         return redirect()->back()->with('message','Duplicate Enrty! Updated Unsuccessfull !')->withInput(); 
       }
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try{
          // $info = SysInfos::find($id);
          // if($info->vComboName == "Designation" or $info->vComboName == "Department" or){

          // }
            SysInfos::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }

}
