<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Chartofaccounts;
use App\Models\Suppliers\Suppliers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;

class SuppliersController extends Controller
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
      $companies  = $dropdownscontroller->comboCompanyAssignList();

      $rows = Suppliers::query()
            ->leftJoin("districts", "supp_dist_id", "=", "districts.id")
            ->selectRaw('suppliers.*,districts.vCityName')
            ->orderBy('suppliers.updated_at', 'desc')->get();
       $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();

      return view ('/suppliers/suppliers_index', compact('rows','companies',
      'company_code','suppliers'))->render();
      //->renderSections()['content'];
    }

    public function search(Request $request)
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies    = $dropdownscontroller->comboCompanyAssignList();

      $supplier_id = $request->get('supplier_id');
      $suppliers   = Suppliers::query()->orderBy('supp_name','asc')->get();

      $rows = Suppliers::query()
            ->leftJoin("districts", "supp_dist_id", "=", "districts.id")
            ->selectRaw('suppliers.*,districts.vCityName')
             ->where('suppliers.id', $supplier_id)
            ->orderBy('suppliers.updated_at', 'desc')->paginate(10)->setpath('');

      $rows->appends(array(
        'supplier_id' => $supplier_id,
      ));

      $collect = collect($rows);
      return view ('/suppliers/suppliers_index', compact('rows','companies',
      'company_code','suppliers'))->render();
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
       $dist_list  = $dropdownscontroller->comboDistrictsList();

       $q = Suppliers::query()
           ->where('supp_com_id', $company_code );
       $rows = $q->orderBy('suppliers.id', 'desc')->paginate(10)->setpath('');

       $suppliers = Suppliers::query()
        ->where('supp_com_id', $company_code)->orderBy('supp_name','asc')->get();
        
        $suppliers = Suppliers::query()
            ->leftJoin("districts", "supp_dist_id", "=", "districts.id")
            ->selectRaw('suppliers.*,districts.vCityName')
            ->orderBy('suppliers.updated_at', 'desc')->get();

       return view ('/suppliers/suppliers_create', compact('rows','companies',
       'company_code','dist_list','suppliers'))->render();
      }

      public function getSupplierFromChartOfAcc(Request $request)
      {
          //return $request->company_code;
          $company_code = $request->company_code;
          $dropdownscontroller = new DropdownsController();
          $companies  = $dropdownscontroller->comboCompanyAssignList();
          $generalscontroller = new GeneralsController();

          $rows = Chartofaccounts::query()
            ->Join("settings", "sett_accid", "=", "parent_id")
            ->Join("settings_categories", "settings_categories.id", "=", "sett_mapped")
            ->where('sett_comp_id', $company_code )
            ->where('is_moved_to_cust', '0' )
            ->where('sett_cat_name', 'SUPPLIER' )
            ->selectRaw('chartofaccounts.id,sett_comp_id,acc_head,file_level')->get();
          foreach ($rows as $key => $value) {
            Suppliers::create([
              'supp_com_id'=> $company_code,
              'supp_code'  => $value->file_level,
              'supp_name'  => $value->acc_head,
              'supp_chartofacc_id' => $value->id,
            ]);

            $inputdata  = Chartofaccounts::find($value->id);
            $inputdata->is_moved_to_cust = 1;
            $inputdata->save();
          }
          $suppliers = Suppliers::query()->orderBy('supp_name','asc')->get();
          return redirect()->route('suppliers.create');
      }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request)
     {
    //   $id = $request->supplier_id;
      // return $id;
       // Validate the Field
        $validate = $this->validate($request,[
             'acc_head' => 'required',
             'name'         =>'required'
         ]);
        // if ($validate->fails()) {
        //   return redirect()->back()->withErrors($validation)->withInput();
        // }
        
        $parent_id = $request ->acc_head;
        $company_code = 1;
        
        $post = '';
        $origins = DB::select("SELECT func_chartofaccount_path($parent_id,  $company_code) as origin");
        foreach( $origins as $index => $acc_origin ) {
            global $post;
            $post = $acc_origin->origin;
        }
        
        $chart_acc_id = Chartofaccounts::insertGetId([
          'company_id' => $company_code,
          'acc_code' => $request ->acc_head,
          'acc_head' => $request->name,
          'acc_origin' => $post,
          'parent_id' => $request ->acc_head,
          'acc_level' => 3,
        ]);



         $inputdata  = new Suppliers();
         $inputdata->supp_com_id =  $request->company_id;
         $inputdata->supp_code   = 0;
         $inputdata->supp_name   = $request->name;
         $inputdata->supp_add1   = $request->address1;
         $inputdata->supp_add2   = $request->address2;
         $inputdata->supp_dist_id = $request->district_id;
         $inputdata->supp_mobile = $request->mobile;
         $inputdata->supp_phone = $request->phone;
         $inputdata->supp_email = $request->email;
         $inputdata->supp_chartofacc_id = $chart_acc_id;

         $inputdata->save();

         return redirect()->back()->with('message','Supplier Created Successfull !')->withInput();

       //  return redirect()->route('cust.index')->with('message','Customer Created Successfull !');
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
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $rows = Suppliers::query()
          ->selectRaw('suppliers.*')
          ->where('suppliers.id', $id)->first();

      return view ('/suppliers/suppliers_edit', compact('rows','dist_list'))->render();
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
      $id     = $request->result_supplier_id;

     // Validate the Field
       $this->validate($request,[
         'result_supplier_id'  =>'required',
         'name'  =>'required',
       ]);

       $inputdata  = Suppliers::find($id);
       $inputdata->supp_name   = $request->name;
       $inputdata->supp_add1   = $request->address1;
       $inputdata->supp_add2   = $request->address2;
       $inputdata->supp_dist_id = $request->district_id;
       $inputdata->supp_mobile = $request->mobile;
       $inputdata->supp_phone = $request->phone;
       $inputdata->supp_email = $request->email;

       $inputdata->save();

       return redirect()->back()->with('message','Supplier Updated Successfull !')->withInput();


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
            Suppliers::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
