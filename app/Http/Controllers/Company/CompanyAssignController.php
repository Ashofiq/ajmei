<?php

namespace App\Http\Controllers\Company;

use App\Models\CompaniesAssigns;
use App\Http\Controllers\General\DropdownsController;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;

class CompanyAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $dropdownscontroller = new DropdownsController();
      $companieslist  = $dropdownscontroller->comboCompanyList(0);
      $userslist  = $dropdownscontroller->comboUsersList(0);

      $companies = CompaniesAssigns::query()
            ->join("companies", "companies_assigns.comp_id", "=", "companies.id")
            ->join("users", "users.id", "=", "companies_assigns.user_id")
            ->where('companies_assigns.is_deleted', 0)
            ->where('companies.is_deleted', 0)
            ->selectRaw("companies_assigns.id,companies_assigns.comp_id,companies_assigns.default,companies.name as comp_name,user_id,users.name")
            ->orderBy('companies_assigns.id', 'desc')->paginate(10);
      //$collect = collect($companies);
      // get requested action
      return view('/company/company_assign_index',compact('companies','companieslist','userslist'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
            'user_id' =>'required',
        ]);

        $inputdata = new CompaniesAssigns();
        $inputdata->comp_id   = $request->company_code;
        $inputdata->user_id   = $request->user_id;
        $inputdata->default   = $request->input('default')=='on'?1:0;
        $inputdata->save();

        return redirect()->route('companyassign.index')->with('message','Company Assigned Successfull !');
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
        //
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
         'company_code' =>'required',
         'user_id' =>'required',

       ]);

       $inputdata            = CompaniesAssigns::find($id);
       $inputdata->comp_id   = $request->company_code;
       $inputdata->user_id   = $request->user_id;
       $inputdata->default   = $request->input('default')=='on'?1:0;
       $inputdata->save();
       return redirect()->route('companyassign.index')->with('message','Company Assigned Updated Successfull !');
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
          //identifiy the company code of user

          $inputdata  = CompaniesAssigns::find($id);
          $inputdata->is_deleted = 1;
          $inputdata->save();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Company Assigned Deleted Successful');
    }
}
