<?php

namespace App\Http\Controllers\Accounts;

use App\Models\Chartofaccounts;
use App\Models\Customers\Customers;
use App\Models\Suppliers\Suppliers;
use App\Models\CompaniesAssigns;
use App\Models\AccTransactionDetails;
use App\Models\Companies;
use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;
use Validator;
use DB;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      //identifiy the company code of user
      $companycode = CompaniesAssigns::query()
                   ->selectRaw('comp_id')
                   ->where('is_deleted', 0)
                   ->where('user_id',auth()->user()->id)
                   ->where('default',1)->first()->comp_id;

      $dropdownscontroller = new DropdownsController();
      $companies  = $dropdownscontroller->comboCompanyAssignList();

      if($request->filled('company_code')){
        $companycode = $request->get('company_code');
      }

      $chartofaccounts = Chartofaccounts::query()
              ->Join("companies", "chartofaccounts.company_id", "=", "companies.id")
              ->where('parent_id', 0)
              ->where('chartofaccounts.is_deleted', 0)
              ->where('company_id', $companycode)
              ->selectRaw('chartofaccounts.id,company_id,acc_code,acc_head,parent_id,acc_level,file_level,acc_origin,companies.name')
              ->orderBy('chartofaccounts.id', 'asc')->paginate(10);
      $collect = collect($chartofaccounts);
      // get requested action
      return view('/accounts/chartofaccount_index',compact('chartofaccounts','companies','companycode'));
    }

    /*public function after_save($companycode)
    {

      $dropdownscontroller = new DropdownsController();
      $companies  = $dropdownscontroller->comboCompanyAssignList();

      $chartofaccounts = Chartofaccounts::query()
                        ->Join("companies", "chartofaccounts.company_id", "=", "companies.id")
                        ->where('parent_id', 0)
                        ->where('chartofaccounts.is_deleted', 0)
                        ->where('company_id', $companycode)
                        ->orderBy('chartofaccounts.id', 'asc')->paginate(10);
      $collect = collect($chartofaccounts);
      // get requested action
      return view('/accounts/chartofaccount_index',compact('chartofaccounts','companies','companycode'));
    } */

    public function makeacc_childhead($parent_id)
    {
      //return $parent_id;
      $chartofdata = Chartofaccounts::find($parent_id);
      //$chartofaccounts = Chartofaccounts::where('parent_id', $parent_id)->paginate(10);
      $chartofaccounts = Chartofaccounts::query()
                        ->Join("companies", "chartofaccounts.company_id", "=", "companies.id")
                        ->where('parent_id', $parent_id)
                        ->where('chartofaccounts.is_deleted', 0)
                        ->selectRaw('chartofaccounts.id,company_id,acc_code,acc_head,parent_id,acc_level,file_level,is_cash_sheet,acc_origin,companies.name')
                        ->orderBy('chartofaccounts.acc_head', 'asc')->paginate(10);
      $companycode = "'".$chartofdata->company_id."'";

      $origin = '';
      $origins = DB::select("SELECT func_chartofaccount_path($parent_id,  $companycode) as origin");
      foreach ($origins as $key => $value) {
          $origin =  $value->origin;
      }

      $collect = collect($chartofaccounts); 
      // get requested action
      return view('/accounts/chartofaccount_child_index', compact('chartofdata','chartofaccounts','origin','parent_id'));
    }

    public function makeacc_childhead2($parent_id)  
    {
      $chartofdata = Chartofaccounts::find($parent_id);
      //$chartofaccounts = Chartofaccounts::where('parent_id', $parent_id)->paginate(10);
      $chartofaccounts = Chartofaccounts::query()
                        ->Join("companies", "chartofaccounts.company_id", "=", "companies.id")
                        ->Join("customers", "customers.cust_chartofacc_id", "=", "chartofaccounts.id")
                        ->where('parent_id', $parent_id)
                        ->where('chartofaccounts.is_deleted', 0)
                        ->selectRaw('customers.id as customer_id, chartofaccounts.id,company_id,acc_code,acc_head,parent_id,acc_level,file_level,is_cash_sheet,acc_origin,companies.name, customers.cust_slno as customerId')
                        ->orderBy('chartofaccounts.customerId', 'asc')->paginate(10);
      $companycode = "'".$chartofdata->company_id."'";

      $origin = '';
      $origins = DB::select("SELECT func_chartofaccount_path($parent_id,  $companycode) as origin");
      foreach ($origins as $key => $value) {
          $origin =  $value->origin;
      }

      $collect = collect($chartofaccounts); 
      // get requested action
      return view('/accounts/chartofaccount_child_index2', compact('chartofdata','chartofaccounts','origin','parent_id'));
    }


  /*

   public function addChildhead($parent_id)
    {

    }

    public function addChildheadMore(Request $request)
    {
        $parent_id =  $request->input(parent_id);
        $rules = [];
        foreach($request->input('name') as $key => $value) {
              $rules["name.{$key}"] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            foreach($request->input('name') as $key => $value) {
                Chartofaccounts::create([
                  'company_id'=> 'BD02',
                  'acc_code'  => '20000',
                  'acc_head'  => $value,
                  'parent_id' => $parent_id,
              ]);
        }
            return response()->json(['success'=>'done']);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }



    public function get_chart_data()
    {
      $chartofaccounts = Chartofaccounts::orderBy('id', 'asc')->get();
      //dd($chartofaccounts);
      //print_r($chartofaccounts);
      foreach ($chartofaccounts as $key => $value) {
         $parent_chart_id = $value['parent_chart_id'];
         $data = $this->get_chart_node($parent_chart_id);
        //  echo 'ABD: '.print_r($parent_chart_id);
     }
    // print_r('HELLOO');
    echo json_encode(array_values($data));
    }

    public function get_chart_node($parent_chart_id)
    {
      $chartofnode = Chartofaccounts::where('parent_chart_id', $parent_chart_id)->get();
      $output = array();
      foreach ($chartofnode as $key => $value) {
       $sub_array = array();
        $sub_array['text'] =  $value['name'];
        $sub_array['nodes'] = array_values($this->get_chart_node($value['id']));
        $output[] = $sub_array;

      }
      return $output;
    }

*/

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
      return $request;
       $company_code = $request->company_code;
       // Validate the Field
        $this->validate($request,[
            'company_code' =>'required',
            'acc_head'     =>'required',
        ]);

        $acccode = Chartofaccounts::query()
            ->selectRaw('IFNULL(MAX(acc_code),0) + 1 as acc_code')
            ->where('company_id',$company_code)
            ->where('parent_id','0')
            ->first()
            ->acc_code;

        $origin = $request->acc_head.' >> ';

        /*$origins = DB::select("SELECT func_chartofaccount_path(0,  $company_code) as origin");
        foreach ($origins as $key => $value) {
                $origin =  $value->origin;
        }*/

        $inputdata = new Chartofaccounts();
        $inputdata->company_id  = $company_code;
        $inputdata->acc_head    = $request->acc_head;
        $inputdata->acc_code    = $acccode;
        $inputdata->acc_origin  = $origin;
        $inputdata->parent_id   = 0;
        $inputdata->acc_level   = 1;
        $inputdata->save();

        //return redirect()->route('chartofacc.after_save',$company_code)->with('message','Account Main Head Created Successfull !');
        return redirect()->back()->with('message','Account Main Head Created Successfull !')->withInput();
      
        //return back()->withInput();
    }

    public function childstore(Request $request)
    {   
        
        $parent_id  = $request->parent_id;
        $p_code     = $request->acc_code;
        $acc_level  = $request->acc_level + 1;
        $company_code = $request->company_code;

        $check = AccTransactionDetails::where('chart_of_acc_id', $parent_id)->first();
        if ($check != null) {
          return back()->with('check', 'Data found! Can not create sub head .');
        }
        
        $rules = [];
        foreach($request->input('name') as $key => $value) {
              $rules["name.{$key}"] = 'required';
        }
        $insert = false;
        $validator = Validator::make($request->all(), $rules);
        //if ($validator->passes()) {
            foreach($request->input('name') as $key => $value) {
              if($value != ''){

              /*  $siblings_count = Chartofaccounts::where('parent_id', '=',$parent_id)
                ->where('company_id', '=', $company_code)->get()->count();

                if ($siblings_count) {
                    $code = sprintf("%03d", $siblings_count + 1);
                } else {
                    $code = sprintf("%03d", 1);
                }
                $code = $p_code . $code; */

                /*$maxCode = Chartofaccounts::where('parent_id', '=',$parent_id)
                  ->where('company_id', '=', $company_code)
                  ->where('acc_code', 'like', $p_code.'%')->max('acc_code');
                $acc_code = $maxCode?$maxCode+1:1;
                if ($acc_code == 1) {
                  $acc_code = $p_code . sprintf("%02d", 1);
                }*/

                $length = strlen($p_code) + 1;
                $maxCode = Chartofaccounts::where('parent_id', '=',$parent_id)
                  ->where('company_id', '=', $company_code)
                  ->where('acc_code', 'like', $p_code.'%')
                  ->selectRaw('max(substring(acc_code,'.$length.')) as MaxCode')->first()->MaxCode;

                $acc_code = $maxCode?$maxCode+1:1;

                if ($acc_code == 1) {
                    $acc_code = $p_code . sprintf("%02d", 1);
                }else if (strlen($acc_code) == 1){
                    $acc_code = $p_code . sprintf("%02d", $acc_code);
                }else{
                    $acc_code = $p_code.$acc_code;
                }

                $post = '';
                $origins = DB::select("SELECT func_chartofaccount_path($parent_id,  $company_code) as origin");
                foreach( $origins as $index => $acc_origin ) {
                   global $post;
                   $post = $acc_origin->origin;
                }
                 //return $post;
              
                $chart_acc_id = Chartofaccounts::insertGetId([
                  'company_id'=> $company_code,
                  'acc_code'  => $acc_code,
                  'acc_head'  => $value,
                  'acc_origin' => $post,
                  'parent_id' => $parent_id,
                  'acc_level' => $acc_level,
                ]);

                if ($parent_id == 85 || $parent_id == 86 || $parent_id == 2927){
                  Suppliers::create([
                    'supp_com_id'=> $company_code,
                    'supp_code'  => 0,
                    'supp_name'  => $value,
                    'supp_chartofacc_id' =>  $chart_acc_id,
                  ]);
                  $inputdata  = Chartofaccounts::find($chart_acc_id);
                  $inputdata->is_moved_to_cust = 1;
                  $inputdata->save();
                }

                if ($parent_id == 61){
                  $generalscontroller = new GeneralsController(); 
                  $mk_cust_sl  = $generalscontroller->makeCustomerSLNo($company_code);
                  Customers::create([
                    'cust_com_id'=> $company_code,
                    'cust_code'  => 0,
                    'cust_slno'  => $mk_cust_sl,
                    'cust_name'  => $value,
                    'cust_chartofacc_id' => $chart_acc_id,
                  ]);
                  $inputdata  = Chartofaccounts::find($chart_acc_id);
                  $inputdata->is_moved_to_cust = 1;
                  $inputdata->save();
                }

                /*$inputdata = new Chartofaccounts();
                $inputdata->company_id  = $company_code;
                $inputdata->acc_head    = $value;
                $inputdata->acc_code    = $acc_code;
                $inputdata->acc_origin  = $post;
                $inputdata->parent_id   = $parent_id;
                $inputdata->acc_level   = $acc_level;
                $inputdata->save();*/

                $insert = true;
              }
            }
          if($insert)
            return redirect()->route('chartofacc.makechildhead',$parent_id)->with('message', 'Accounts Sub Head Created Successfull !');
          else
            return redirect()->route('chartofacc.makechildhead',$parent_id)->with('message','Needs to fill up atleast one Accounts Head!');
    }

    public function childstore2(Request $request)
    {   
        // return $request;
        $parent_id  = 61;
        $acc_level  = 4;
        $company_code = 1;
        $customer_name = $request->customer_name;

        $post = '';
        $origins = DB::select("SELECT func_chartofaccount_path($parent_id,  $company_code) as origin");
        foreach( $origins as $index => $acc_origin ) {
            global $post;
            $post = $acc_origin->origin;
        }
        $chart_acc_id = Chartofaccounts::insertGetId([
          'company_id' => $company_code,
          'acc_code' => 61,
          'acc_head' => $customer_name,
          'acc_origin' => $post,
          'parent_id' => $parent_id,
          'acc_level' => $acc_level,
        ]);

        if ($parent_id == 61){
          $generalscontroller = new GeneralsController(); 
          $mk_cust_sl  = $generalscontroller->makeCustomerSLNo($company_code);
          
          $chartOfAcc = Chartofaccounts::orderBy('customerId', 'DESC')->first();

          Customers::create([
            'cust_com_id'=> $company_code,
            'cust_code'  => 0,
            'cust_mobile' => $request->mobileno,
            'contract_person' => $request->contract_person,
            'personalMobileno' => $request->personalMobileno,
            'cust_add1' => $request->address1,
            'cust_add2' => $request->address2,
            'cust_slno'  => $mk_cust_sl,
            'cust_name'  => $customer_name,
            'cust_chartofacc_id' => $chart_acc_id,
          ]);

          $inputdata  = Chartofaccounts::find($chart_acc_id);
          $inputdata->is_moved_to_cust = 1;

          $inputdata->customerId = $chartOfAcc->customerId + 1;
          $inputdata->save();

          $insert = true;
        }

               
              
          if($insert)
            return back()->with('message', 'Accounts Sub Head Created Successfull !');
          else
            return redirect()->route('chartofacc.makechildhead2',$parent_id)->with('message','Needs to fill up atleast one Accounts Head!');
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
            'acc_head'     =>'required',
        ]);

        $inputdata              = Chartofaccounts::find($id);
        $inputdata->company_id  = $request->company_code;
        $inputdata->acc_head    = $request->acc_head;
        $inputdata->save();

        return redirect()->route('chartofacc.index')->with('message','Account Main Head Updated Successfull !');
    }

    public function updateChild(Request $request)
    {
        $id = $request->id;
        $parent_id = $request->parent_id;
      // Validate the Field
        $this->validate($request,[
            'company_code' =>'required',
            'acc_head'     =>'required',
        ]);
        
      //return  $request->cash_stm_level;
        
        $inputdata              = Chartofaccounts::find($id);
        $inputdata->acc_head    = $request->acc_head;
        $inputdata->file_level   = $request->file_level;
        $inputdata->is_cash_sheet  = $request->cash_stm_level=='on'?'1':'0';
        
        $inputdata->save();
        
        // update customer master information
        DB::update("update customers set cust_name = '".$request->acc_head."'
        where cust_chartofacc_id = ?", [$id]);
        
        // update supplier master information
        DB::update("update suppliers set supp_name = '".$request->acc_head."'
        where supp_chartofacc_id = ?", [$id]);
        
        $sql = "select  id, acc_head, parent_id  from (select * from chartofaccounts
        order by parent_id, id) chartofaccounts_sorted,
        (select @pv := '".$id."') initialisation
        where  find_in_set(parent_id, @pv) and length(@pv := concat(@pv, ',', id))";
        
        if($request->cash_stm_level == 'on'){
            $is_cash_sheet = 1;
        }else{
            $is_cash_sheet = 0;
        }
        $data = DB::select($sql);
        foreach ($data as $key => $value) {
              $accid =  $value->id;
              $inputdata = Chartofaccounts::find($accid);
              $inputdata->is_cash_sheet = $is_cash_sheet;
              $inputdata->save();
        }
        
        return redirect()->route('chartofacc.makechildhead',$parent_id)->with('message','Account Sub Head Updated Successfull !');
    }

    public function updateChild2(Request $request)
    {
        $id = $request->id;
        $parent_id = $request->parent_id;
      // Validate the Field
        $this->validate($request,[
            'company_code' =>'required',
            'acc_head'     =>'required',
        ]);
        
      //return  $request->cash_stm_level;
        
        $inputdata              = Chartofaccounts::find($id);
        $inputdata->acc_head    = $request->acc_head;
        $inputdata->file_level   = $request->file_level;
        $inputdata->is_cash_sheet  = $request->cash_stm_level=='on'?'1':'0';
        
        $inputdata->save();
        
        // update customer master information
        DB::update("update customers set cust_name = '".$request->acc_head."'
        where cust_chartofacc_id = ?", [$id]);
        
        // update supplier master information
        DB::update("update suppliers set supp_name = '".$request->acc_head."'
        where supp_chartofacc_id = ?", [$id]);
        
        $sql = "select  id, acc_head, parent_id  from (select * from chartofaccounts
        order by parent_id, id) chartofaccounts_sorted,
        (select @pv := '".$id."') initialisation
        where  find_in_set(parent_id, @pv) and length(@pv := concat(@pv, ',', id))";
        
        if($request->cash_stm_level == 'on'){
            $is_cash_sheet = 1;
        }else{
            $is_cash_sheet = 0;
        }
        $data = DB::select($sql);
        foreach ($data as $key => $value) {
              $accid =  $value->id;
              $inputdata = Chartofaccounts::find($accid);
              $inputdata->is_cash_sheet = $is_cash_sheet;
              $inputdata->save();
        }
        
        return redirect()->route('chartofacc.makechildhead2',$parent_id)->with('message','Account Sub Head Updated Successfull !');
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
            $isExist = Chartofaccounts::where('parent_id', '=',$id)->get()->count();
            if($isExist > 0){
              return redirect()->back()->with('message','Can Not Delete.. Sub Account is already existed');
            } else {
              Chartofaccounts::where('id',$id)->delete();
              Customers::where('cust_chartofacc_id', $id)->delete();
            }

        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Account Main Head Deleted Successfull');
    }

    public function child_destroy(Request $request)
    {
        $id = $request->id;
        $s_parent_id = $request->s_parent_id;
        try{
            $isExist = Chartofaccounts::where('parent_id', '=',$id)->get()->count();
            if($isExist > 0){
                return redirect()->back()->with('message','Can Not Delete.. Sub Account is already existed');

            } else {
              $isTransac = AccTransactionDetails::where('chart_of_acc_id', '=',$id)->get()->count();
              if($isTransac > 0){
                return redirect()->back()->with('message','Can Not Delete.. Transaction is already existed');
              }else{
                Chartofaccounts::where('id',$id)->delete();
                Customers::where('cust_chartofacc_id', $id)->delete();
              }
          }

        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->route('chartofacc.makechildhead', $s_parent_id)->with('message','Account Sub Head Deleted Successfull');

        //return back()->with('message','Account Sub Head Deleted Successfull');
    }

    public function child_destroy2(Request $request)
    {
        $id = $request->id;
        $s_parent_id = $request->s_parent_id;
        try{
            $isExist = Chartofaccounts::where('parent_id', '=',$id)->get()->count();
            if($isExist > 0){
                return redirect()->back()->with('message','Can Not Delete.. Sub Account is already existed');

            } else {
              $isTransac = AccTransactionDetails::where('chart_of_acc_id', '=',$id)->get()->count();
              if($isTransac > 0){
                return redirect()->back()->with('message','Can Not Delete.. Transaction is already existed');
              }else{
                Chartofaccounts::where('id',$id)->delete();
                Customers::where('cust_chartofacc_id', $id)->delete();
              }
          }

        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->route('chartofacc.makechildhead2',$s_parent_id)->with('message','Account Sub Head Deleted Successfull');

        //return back()->with('message','Account Sub Head Deleted Successfull');
    }

    public function childsearch(Request $request)
    {

      $parent_id = $request->s_parent_id;
      //  return $parent_id;
      $chartofdata = Chartofaccounts::find($parent_id);
      //$chartofaccounts = Chartofaccounts::where('parent_id', $parent_id)->paginate(10);
      $q = Chartofaccounts::query()
            ->Join("companies", "chartofaccounts.company_id", "=", "companies.id")
            ->where('parent_id', $parent_id)
            ->where('chartofaccounts.is_deleted', 0);

      if($request->filled('ledger_id')){
          $ledger_id = $request->get('ledger_id');
            $q->where('chartofaccounts.id', $ledger_id);
      }

      $q->selectRaw('chartofaccounts.id,company_id,acc_code,acc_head,parent_id,acc_level,file_level,acc_origin,companies.name');
      $chartofaccounts = $q->orderBy('chartofaccounts.acc_head', 'asc')->paginate(10)->setpath('');

      $chartofaccounts->appends(array(
        'ledger_id'    => $request->get('ledger_id'),
        's_parent_id'  => $request->input('s_parent_id'),

      ));
      $companycode = "'".$chartofdata->company_id."'";

      $origin = '';
      $origins = DB::select("SELECT func_chartofaccount_path($parent_id,  $companycode) as origin");
      foreach ($origins as $key => $value) {
          $origin =  $value->origin;
      }

      $collect = collect($chartofaccounts);
      // get requested action
      return view('/accounts/chartofaccount_child_index',compact('chartofdata','chartofaccounts','origin','parent_id'));
    }

    public function manageAccountHeadTreeList()
    { 
       $data = Chartofaccounts::query()
            ->Join("companies", "chartofaccounts.company_id", "=", "companies.id")
            ->Join("companies_assigns", "companies_assigns.comp_id", "=", "companies.id")
            ->where('user_id',auth()->user()->id)
            ->distinct()->get(['company_id', 'name']);
        return view('accounts.accheadTreeViewList',compact('data'));
    }

    public function manageAccountHeadTree($code)
    {
        $company = Companies::where('id', $code)->first();

        $categories = Chartofaccounts::query()
        ->where('company_id', '=', $code)
        ->where('parent_id', '=', 0)
        ->orderBy('acc_code')
        ->get();
        $allCategories = Chartofaccounts::pluck('acc_head','id')->where('company_id', '=', $code)->all();
        return view('accounts.accheadTreeview',compact('company','categories','allCategories'));
    }
}
