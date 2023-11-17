<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Chartofaccounts;
use App\Models\Customers\Customers;
use App\Models\Customers\CustomerContacts;
use App\Models\Salespersons\CustomerSalesPersons;
use App\Models\Customers\CustomerDeliveryInfs;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      $rows = Customers::query()
            ->leftJoin("customer_sales_persons", "cust_sales_per_id", "=", "customer_sales_persons.id")
            ->leftJoin("districts", "cust_dist_id", "=", "districts.id")
            ->leftJoin("sales_courrier_infs", "cust_courrier_id", "=", "sales_courrier_infs.id")
            ->selectRaw('customers.*,customer_sales_persons.sales_name,districts.vCityName,sales_courrier_infs.courrier_to')
            ->orderBy('customers.cust_slno', 'asc')->paginate(10);
      $customers = Customers::query()->orderBy('cust_name','asc')->get();
      return view ('/customers/index', compact('rows','customers'));
    }

    public function search(Request $request)
    {
      $customer_id = $request->get('customer_id');
      $customers = Customers::query()->orderBy('cust_name','asc')->get();
      $salespersons = CustomerSalesPersons::query()->orderBy('sales_name','asc')->get();

      $rows = Customers::query()
            ->leftJoin("customer_sales_persons", "cust_sales_per_id", "=", "customer_sales_persons.id")
            ->leftJoin("districts", "cust_dist_id", "=", "districts.id")
            ->leftJoin("sales_courrier_infs", "cust_courrier_id", "=", "sales_courrier_infs.id")
            ->selectRaw('customers.*,customer_sales_persons.sales_name,districts.vCityName,sales_courrier_infs.courrier_to')
            ->where('customers.id', $customer_id)
            ->orderBy('customers.updated_at', 'desc')->paginate(10)->setpath('');

      $rows->appends(array(
        'customer_id' => $customer_id,
      ));
      $collect = collect($rows);
      return view ('/customers/index', compact('rows','customers','salespersons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dropdownscontroller = new DropdownsController();
        $company_code = $dropdownscontroller->defaultCompanyCode();
        $companies  = $dropdownscontroller->comboCompanyAssignList();
        $dist_list  = $dropdownscontroller->comboDistrictsList();
        $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
        
        $salespersons = CustomerSalesPersons::query()->orderBy('sales_name','asc')->get();
        $customers = Customers::query()->orderBy('cust_name','asc')->get();
        return view('/customers/create',compact('companies','dist_list','company_code','customers','salespersons','courr_list'));
    }

    public function getCustFromChartOfAcc(Request $request)
    {
        //return $request->company_code;
        $company_code = $request->company_code;
        $dropdownscontroller = new DropdownsController();
        $companies  = $dropdownscontroller->comboCompanyAssignList();
        $generalscontroller = new GeneralsController();

        $rows_acchead = Chartofaccounts::query()
                ->where('is_moved_to_cust', 0)
                ->where('is_deleted', 0)
                ->where('company_id', $company_code)
                ->where('acc_code', 'like', '30201%')
                ->where('parent_id', '=', '61')
                ->selectRaw('id,acc_head,file_level')->get();

        foreach ($rows_acchead as $key => $value) {
        //  $mk_cust_code  = $generalscontroller->makeCustomerCode($company_code);
          $mk_cust_sl  = $generalscontroller->makeCustomerSLNo($company_code);
          Customers::create([
            'cust_com_id'=> $company_code,
            'cust_code'  => $value->file_level,
            'cust_slno'    => $mk_cust_sl,
            'cust_name'  => $value->acc_head,
            'cust_chartofacc_id' => $value->id,
          ]);

          $inputdata  = Chartofaccounts::find($value->id);
          $inputdata->is_moved_to_cust = 1;
          $inputdata->save();
        }
        return redirect()->route('cust.create');
    }

    public function get_cust_code(Request $request){
        $id = $request->get('id');
        $customerCode = customers::where('cust_code',$id)->first();
        if ($customerCode){
            return "<b class='text-danger'>Customer Code is Unavailable !</b> ";
        }else{
            return "<b class='text-success'>Customer Code is available.</b>";
        }
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
            'customer_id' =>'required',
            'customer_code' =>'required',
            //'mobileno'    =>'required',
            'address1'    =>'required',
            'district_id' =>'required', 
        ]);

        $generalscontroller = new GeneralsController();
        //$id = $generalscontroller->CustomerId($request->customer_id);
        $id = $request->customer_id;
        $inputdata  = customers::find($id);
      //$inputdata->cust_com_id =  $request->company_id;
      // $inputdata->cust_code   = $request->customer_code;
        $inputdata->cust_mobile =  $request->mobileno;
        $inputdata->cust_add1   = $request->address1;
        $inputdata->cust_add2   = $request->address2;
        $inputdata->cust_dist_id = $request->district_id;
        $inputdata->cust_sales_per_id = $request->salesperson_id;
        $inputdata->cust_courrier_id = $request->courrier_id; 
        
        $creditamt = $request->input('creditamt');
        if($creditamt>0){
          $inputdata->credit_limit  = $creditamt;
          $inputdata->is_credit_chk = 1;
        }
        $inputdata->cust_VAT = $request->cust_vat;
        $inputdata->cust_own_comm = $request->cust_own_commission==''?'0':$request->cust_own_commission;
        $inputdata->cust_overall_comm = $request->commision==''?'0':$request->commision;
        $inputdata->same_as_del = $request->same_as_delivery==''?'0':'1';
        $inputdata->save();
        
        //Customer Delivery Information
        $customer_name = $generalscontroller->CustomerName($request->customer_id);
        if($request->same_as_delivery == 1){
          $isRec = $generalscontroller->get_delivered_id_by_name($id,$request->customer_name);
          //return $isRec;
          if($isRec == 0){
            $inputdata = new CustomerDeliveryInfs();
            $inputdata->cust_d_ref_id = $id;
            $inputdata->deliv_to      = $customer_name;
            $inputdata->deliv_add     = $request->address1;
            $inputdata->deliv_mobile  = $request->mobileno;
            $inputdata->deliv_dist_id = $request->district_id;
            $inputdata->save();
          }
        }
        
        // delete old Details Records
        CustomerContacts::where('cust_ref_id', $request->customer_id)->delete();

        // Insert Contact Information Records
        $con_name = $request->input('con_name');
        //dd($con_name);
        if ($con_name){
              foreach ($con_name as $key => $value){
                if ($request->con_name[$key] != ''){
                  CustomerContacts::create([
                      'cust_ref_id' => $id,
                      'cont_name'   => $request->con_name[$key],
                      'cont_mobile' => $request->cell[$key],
                      'cont_email'  => $request->email[$key],
                  ]);
                }
              }
        } 
        return redirect()->back()->with('message','Customer Created Successfull !')->withInput();

      //  return redirect()->route('cust.index')->with('message','Customer Created Successfull !');
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

      $dropdownscontroller = new DropdownsController();
      $generalscontroller = new GeneralsController();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $salespersons = CustomerSalesPersons::query()->orderBy('sales_name','asc')->get();
      $company_code = $generalscontroller->CustomersCompanyId($id);
      $courr_list  = $dropdownscontroller->comboCourrierList($company_code);
      
      $rows = Customers::query()
      ->Join("companies", "cust_com_id", "=", "companies.id")
      ->leftJoin("customer_sales_persons", "cust_sales_per_id", "=", "customer_sales_persons.id")
      ->leftJoin("districts", "cust_dist_id", "=", "districts.id")
      ->selectRaw('customers.*,customer_sales_persons.sales_name,companies.name,districts.vCityName')
      ->where('customers.id', $id)->get();

      $row_d = CustomerContacts::query()
      ->where('cust_ref_id', $id)->get();

      $customers = Customers::query()
      ->leftJoin("customer_sales_persons", "cust_sales_per_id", "=", "customer_sales_persons.id")
      ->leftJoin("districts", "cust_dist_id", "=", "districts.id")
      ->leftJoin("sales_courrier_infs", "cust_courrier_id", "=", "sales_courrier_infs.id")
      ->selectRaw('customers.*,customer_sales_persons.sales_name,districts.vCityName,sales_courrier_infs.courrier_to')
      ->orderBy('customers.cust_slno', 'asc')->paginate(10);
      // $customers = Customers::query()->orderBy('cust_name','asc')->get();

      return view('/customers/edit',compact('rows', 'customers', 'row_d','dist_list','salespersons','courr_list'));
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
      $customer_id = $request->customer_id;
      $Customers = Customers::find($customer_id);
      $Customers->cust_mobile = $request->mobileno;
      $Customers->cust_add1 = $request->address1;
      $Customers->cust_add2 = $request->address2;
      $Customers->contract_person = $request->contract_person;
      if ($Customers->save()) {
        return back()->with('message', 'update successfully');
      }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id,$comid)
    {

    }

}
