<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;  
use App\Models\Customers\CustomerDeliveryInfs;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;

class CustDeliveryInfsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {

      $dropdownscontroller = new DropdownsController();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $generalscontroller = new GeneralsController();
      $cust_name  = $generalscontroller->CustomerName($id);
      $cust_code  = $generalscontroller->CustomerCode($id);
      $rows = CustomerDeliveryInfs::query()
          ->leftJoin("districts", "districts.id", "=", "deliv_dist_id")
          ->where('cust_d_ref_id', '=', $id)
          ->selectRaw('customer_delivery_infs.id,cust_d_ref_id,deliv_to,deliv_add,deliv_mobile,vCityName')
          ->orderBy('customer_delivery_infs.id', 'desc')->paginate(10);

      return view ('/customers/cust_delv_index', compact('id','rows','cust_name','cust_code','dist_list'));
    }


    public function store(Request $request)
    {
      // Validate the Field
        $this->validate($request,[
            'cust_id'   =>'required',
            'cust_code'  =>'required',
            'deliveryto' =>'required',
            'address'    =>'required',
            'district_id' =>'required',
        ]);

        $inputdata = new CustomerDeliveryInfs();
        $inputdata->cust_d_ref_id = $request->cust_id;
        $inputdata->deliv_to      = $request->deliveryto;
        $inputdata->deliv_add     = $request->address;
        $inputdata->deliv_mobile  = $request->mobile;
        $inputdata->deliv_dist_id = $request->district_id;
        $inputdata->save();

        return redirect()->back()->with('message','Delivery Inf Created Successfull !')->withInput();

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
        //return $id;
          CustomerDeliveryInfs::where('id',$id)->delete();
      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage());
      }
      return redirect()->back()->with('message','Deletetion Successfull');
    }

}
