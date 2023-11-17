<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Settings\Settings;
use App\Models\Settings\SettingsCategories;
use App\Models\Companies;
use App\Models\Chartofaccounts;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response; 
use DB;

class SettingsController extends Controller
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
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $sett_cat     = SettingsCategories::query()->orderBy('sett_cat_name','asc')->get();

      $chartofaccounts = Chartofaccounts::query()
              ->Join("companies", "chartofaccounts.company_id", "=", "companies.id")
              ->where('parent_id', 0)
              ->where('chartofaccounts.is_deleted', 0)
              ->where('company_id', $company_code)
              ->selectRaw('chartofaccounts.id,company_id,acc_code,acc_head,parent_id,acc_level,file_level,acc_origin,companies.name')
              ->orderBy('chartofaccounts.id', 'asc')->get();

      $sql = "select * from `item_categories`
      where `itm_comp_id` = $company_code and itm_cat_level = 1 Order By itm_cat_name asc";
      $itm_cat = DB::select($sql);
 
      $rows = Settings::query()
          ->Join("settings_categories", "settings.sett_mapped", "=", "settings_categories.id")
          ->where('sett_comp_id', $company_code)
          ->selectRaw('settings.id,sett_comp_id,sett_accid,sett_accname,sett_accname_origin,sett_tr_head_id,sett_tr_head_name,sett_mapped,sett_cat_name')
          ->orderBy('sett_accname_origin', 'asc')
          ->orderBy('sett_cat_name', 'ASC')
          ->get();


      return view ('/settings/mapping_index',
      compact('company_code','companies','itm_cat','sett_cat','chartofaccounts','rows'));
    }

    public function accountNameLookup($headid){
    //  return('ss'. $id);
         $dropdownscontroller = new DropdownsController();
         $acclist = $dropdownscontroller->accountNameLookup1($headid);
         return response()->json($acclist);
    }

    public function childItemCateNameLookup($headid){
        //  return('ss'. $id);
        $dropdownscontroller = new DropdownsController();
        $childlist = $dropdownscontroller->childItemCateNameLookup($headid);
        return response()->json($childlist);
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
            'acc_name_id'  =>'required',
            'sett_cat_id'  =>'required',
            'cate_name_id'  =>'required',
        ]);
        $acc_name_id = explode('@@', trim($request->acc_name_id))[0];
        $acc_name = explode('@@', trim($request->acc_name_id))[1];
        $sett_accname_origin = explode('@@', trim($request->acc_name_id))[2];
 
        $item_cat_id = explode('@@', trim($request->cate_name_id))[0];
        $item_cat = explode('@@', trim($request->cate_name_id))[1];

        $inputdata = new Settings();
        $inputdata->sett_comp_id      = $request->company_code;
        $inputdata->sett_accid        = $acc_name_id;
        $inputdata->sett_accname      = $acc_name;
        $inputdata->sett_accname_origin = $sett_accname_origin;
        
        $inputdata->sett_tr_head_id   = $item_cat_id;
        $inputdata->sett_tr_head_name = $item_cat;
        $inputdata->sett_mapped       = $request->sett_cat_id;
        $inputdata->save();

        return redirect()->back()->with('message','Created Successfull !')->withInput();
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
            Settings::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }

}
