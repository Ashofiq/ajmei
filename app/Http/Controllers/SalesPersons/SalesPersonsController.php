<?php

namespace App\Http\Controllers\SalesPersons;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Salespersons\CustomerSalesPersons;
use App\Models\Settings\SysInfos;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;

class SalesPersonsController extends Controller
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
      $sysinfos   = SysInfos::query()
        ->where('vComboType','=', 'Designation')
        ->orderBy('vComboName','asc')->get();

      $q = CustomerSalesPersons::query()
          ->join("sys_infos", "sys_infos.id", "=", "customer_sales_persons.sales_desig")
          ->where('sales_comp_id', $company_code )
          ->selectRaw('customer_sales_persons.id,sales_comp_id,sales_name,sales_desig,vComboName,sales_mobile,sales_email');

      $rows = $q->orderBy('customer_sales_persons.id', 'desc')->paginate(10)->setpath('');

      return view ('/salespersons/sales_person_index', compact('rows','companies','company_code','sysinfos'))
      ->render();
      //->renderSections()['content'];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function create()
     {
       $dropdownscontroller = new DropdownsController();
       $companies    = $dropdownscontroller->comboCompanyAssignList();
       $company_code = $dropdownscontroller->defaultCompanyCode();

       $q = CustomerSalesPersons::query()
            ->where('sales_comp_id', $company_code )
            ->selectRaw('sales_comp_id,sales_name,sales_desig,sales_mobile,sales_email');

       $rows = $q->orderBy('customer_sales_persons.id', 'desc')->paginate(10)->setpath('');

       $rows->appends(array(
         'company_code' => $company_code,
       ));

       return view ('/salespersons/sales_person_index', compact('rows','companies','company_code'))
       ->render();
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
            'name'   =>'required',
          //  'mobile' =>'required',
          //  'email'  =>'required',
        ]);

        $inputdata = new CustomerSalesPersons();
        $inputdata->sales_comp_id = $request->company_code;
        $inputdata->sales_name    = $request->name;
        $inputdata->sales_desig   = $request->designation;

        $inputdata->sales_mobile  = $request->mobile;
        $inputdata->sales_email   = $request->email;
        $inputdata->save();
        //return redirect()->back();
        return back()->withInput();
        //return redirect($this->redirectPath());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
      $id     = $request->id;

     // Validate the Field
       $this->validate($request,[
         'id'  =>'required',
         'name'  =>'required',
       ]);

       $inputdata = CustomerSalesPersons::find($id);
       $inputdata->sales_name    = $request->name;
       $inputdata->sales_desig   = $request->designation;
       $inputdata->sales_mobile  = $request->mobile;
       $inputdata->sales_email   = $request->email;
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
        try{
            CustomerSalesPersons::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
