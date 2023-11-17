<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Settings\Currencies;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;
use DB;

class CurrencyController extends Controller
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

      $rows = Currencies::query()
              ->selectRaw('currencies.*')
              ->orderBy('currencies.id', 'asc')->get();
      return view ('/settings/currencies_index',
      compact('company_code','companies','rows'));
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
            'acc_currency'  =>'required',
            'acc_currency_val' =>'required',
        ]);

        $inputdata = new Currencies();
        $inputdata->curr_comp_id   = $request->company_code;
        $inputdata->vCurrName      = $request->acc_currency;
        $inputdata->dCurrValue     = $request->acc_currency_val;
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
            Currencies::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }

}
