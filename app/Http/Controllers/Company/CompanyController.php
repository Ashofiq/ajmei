<?php

namespace App\Http\Controllers\Company;

use App\Models\Companies;
use App\Models\Chartofaccounts;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $companies = Companies::query()
                  ->where('is_deleted', 0)
                  ->orderBy('id', 'asc')->get();

      return view ('/company/index', compact('companies'));
    }

    public function fetch_onlinepricetable(){
      $companies = Companies::query()
                  ->where('is_deleted', 0)
                  ->orderBy('id', 'asc')->simplePaginate(10);
      //$collect = collect($companies);
      // get requested action
       foreach($companies as $row) {
         $data[] = array(
                    'id' => $row["id"],
                    'name' => $row["name"],
                    'description' => $row["description"],
                    'address1' => $row["address1"],
                    'address2' => $row["address2"],
                    'level' => $row["level"]
          );
       }
       echo json_encode($data,JSON_UNESCAPED_UNICODE);
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
            'companyname' =>'required',
            'address1'    =>'required',
            'level'       =>'required',
        ]);

        $inputdata = new Companies();
        $inputdata->name        = $request->companyname;
        $inputdata->description = $request->descirption;
        $inputdata->address1    = $request->address1;
        $inputdata->address2    = $request->address2;
        $inputdata->level       = $request->level;
        $inputdata->save();

        return redirect()->route('company.index')->with('message','Company Created Successfull !');
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
           'companyname'  =>'required',
           'address1'     =>'required',
           'level'        =>'required',
       ]);

       $inputdata              = Companies::find($id); 
       $inputdata->name        = $request->companyname;
       $inputdata->description = $request->descirption;
       $inputdata->address1    = $request->address1;
       $inputdata->address2    = $request->address2;
       $inputdata->level       = $request->level;
       $inputdata->save();
       return redirect()->route('company.index')->with('message','Company Updated Successfull !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id,$comid)
    {
        try{
          $isExist = Chartofaccounts::where('company_id', '=',$comid)->get()->count();
          if($isExist > 0){
            return redirect()->back()->with('message','Can Not Delete.. Company Chart of Account is already existed');
          } else {
            $inputdata  = Companies::find($id);
            $inputdata->is_deleted = 1;
            $inputdata->save();
          }
          //Companies::where('id',$id)->delete();
        }catch (\Exception $e){
            //return redirect()->back()->with('error',$e->getMessage());
            return response()->json(['error'=>$validator->errors()->all()]);
        }
        //return redirect()->back()->with('message','Company Deleted Successful');
        return response()->json(['success'=>'done']);
    }

}
