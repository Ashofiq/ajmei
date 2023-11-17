<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Items\Items;
use App\Models\Items\ItemStocks;
use App\Models\Customers\Customers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;

class ItemOpeningController extends Controller
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
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
      /*$sql = "select * from `item_categories`
      where `itm_comp_id` = $company_code and `id` not in (select `parent_id` from `item_categories`)
      Order By itm_cat_name asc";
      $itm_cat = DB::select($sql);*/

      // $item_list =  Items::query()
      //   ->join("units", "unit_id", "=", "units.id")
      //   ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      //   ->where('item_ref_comp_id', '=', $company_code)
      //   ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      //   'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
      //   ->orderBy('item_name','asc')->get();
      
      $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)  
       //->whereRaw("itm_cat_code like '10%'")
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();


      $q = ItemStocks::query()
           ->join("items", "items.id", "=", "item_ref_id")
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->join("units", "units.id", "=", "unit_id")
           ->where('item_op_comp_id', $company_code )
           ->where('item_trans_desc', 'OP' )
           ->selectRaw('item_stocks.id,item_op_comp_id,item_warehouse_id,item_storage_loc,item_ref_id,item_op_dt,item_lot_no,item_exp_dt,item_stocks.item_op_stock,item_base_price,
           item_op_desc,item_ref_cate_id,item_base_amount,item_trans_spec,item_code,item_name,item_desc,items.item_level,item_qr_code,item_bar_code,item_origin,itm_cat_code,itm_cat_name,itm_cat_origin,packing_id, units.id as unit_id,vUnitName,size');
      $rows = $q->orderBy('item_stocks.id', 'desc')->paginate(10)->setpath('');

      return view ('/items/itm_opening_index', compact('rows','item_list','warehouse_list','stor_list','company_code'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function op_search(Request $request)
    {
      $item_id = $request->get('item_id');

      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
      /*$sql = "select * from `item_categories`
      where `itm_comp_id` = $company_code and `id` not in (select `parent_id` from `item_categories`)
      Order By itm_cat_name asc";
      $itm_cat = DB::select($sql);*/

      // $item_list =  Items::query()
      //   ->join("units", "unit_id", "=", "units.id")
      //   ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      //   ->where('item_ref_comp_id', '=', $company_code)
      //   ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      //   'item_op_stock','item_bal_stock','vUnitName','itm_cat_name','itm_cat_origin')
      //   ->orderBy('item_name','asc')->get();

        $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)  
       //->whereRaw("itm_cat_code like '10%'")
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();


      $q = ItemStocks::query()
           ->join("items", "items.id", "=", "item_ref_id")
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->join("units", "units.id", "=", "unit_id")
           ->where('item_op_comp_id', $company_code )
           ->where('items.id', $item_id )
           ->where('item_trans_desc', 'OP' )
           ->selectRaw('item_stocks.id,item_op_comp_id,item_warehouse_id,item_storage_loc,item_ref_id,item_op_dt,item_lot_no,item_exp_dt,item_stocks.item_op_stock,
           item_base_price,item_op_desc,item_ref_cate_id,item_code,item_name,item_desc,items.item_level,item_qr_code,
           item_bar_code,item_origin,itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,
           units.id as unit_id,vUnitName,size');

      $rows = $q->orderBy('item_stocks.id', 'desc')->paginate(10)->setpath('');

      $rows->appends(array(
        'item_id' => $item_id,
      ));

      $collect = collect($rows);
      return view ('/items/itm_opening_index', compact('rows','item_list','warehouse_list','stor_list','company_code'));
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
      $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);
    //  $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
    //  $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);
      
    // $item_list =  Items::query()
    //   ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
    //   ->where('item_ref_comp_id', '=', $company_code)
    //   ->select('items.id','item_code','item_name','item_desc','item_bar_code',
    //   'item_op_stock','item_bal_stock','itm_cat_name')
    //   ->orderBy('item_name','asc')->get();
    
    $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)  
       //->whereRaw("itm_cat_code like '10%'")
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();



      $opening_date = date('d-m-Y');
      $exp_date = '2021-12-31';
      return view('/items/itm_opening_create',
      compact('companies','opening_date','exp_date','company_code','item_list','warehouse_list','stor_list'))->render();

    }

   public function get_op_item($id){
      //return('ss'. $id);
      $generalcontroller = new GeneralsController();
      return $generalcontroller->get_item_details($id);
    }

    public function getItemCode(Request $request)
    {
      $generalcontroller = new GeneralsController();
      return $generalcontroller->getItemCode($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $generalscontroller = new GeneralsController();
        $company_code = $request->company_code;
        $itm_desc     = $request->itm_desc;
        $warehouse_id = $request->itm_warehouse;
        $opening_date  = date('Y-m-d',strtotime($request->opening_date));
        // Insert Opening Records
        $detId = $request->input('ItemCodeId');
        //dd($detId);
        if ($detId){
            $i = 0;
            foreach ($detId as $key => $value){
              $i = $i + 1;
              $exp_date = $request->input('exp_date_'.$i);
              //if ($request->Stock[$key] > 0){
              if ($request->Stock[$key] > 0 && $request->ItemCodeId[$key] != ''){
                ItemStocks::create([
                    'item_op_comp_id' => $request->company_code,
                    'item_ref_id'     => $request->ItemCodeId[$key],
                    'item_op_dt'      => $opening_date,
                    'item_exp_dt'     => date('Y-m-d',strtotime($exp_date)),
                    'item_lot_no'     => 101,
                    'item_warehouse_id'=> $warehouse_id,
                    'item_storage_loc' => 1,
                    'item_op_stock'   => $request->Stock[$key],
                    'item_base_price' => $request->Price[$key],
                    'item_base_amount' => $request->Amount[$key],
                    'item_trans_spec' => $request->ItemDesc[$key],
                    'item_op_desc'    => $request->itm_desc,
                    'item_trans_desc' => 'OP',
                ]);
                $inputdata  = items::find($request->ItemCodeId[$key]);
                $inputdata->item_op_stock  = $inputdata->item_op_stock + $request->Stock[$key];
                $inputdata->item_bal_stock = $inputdata->item_bal_stock + $request->Stock[$key];
                $inputdata->base_price     = $request->Price[$key];
                $inputdata->save();
              }
            }
        }
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
      $id = $request->id;

     // Validate the Field
       $this->validate($request,[
      // 'itm_name'  =>'required',
      ////   'itm_desc'  =>'required',
      //   'itm_pack'  =>'required',
      //   'itm_unit'  =>'required',
       ]);

       $inputdata  = ItemStocks::find($id);
       $inputdata->item_lot_no      = 101;
       $inputdata->item_op_dt       = date('Y-m-d',strtotime($request->op_date));
       $inputdata->item_exp_dt      = date('Y-m-d');
       $inputdata->item_op_stock    = $request->itm_op_qty;
       $inputdata->item_base_price  = floatval(preg_replace('/[^\d.]/', '', $request->itm_op_price));
       $inputdata->item_op_desc     = $request->itm_op_desc;
       $inputdata->save();

       $inputdata  = items::find($request->itm_id);
       $inputdata->item_op_stock  = $inputdata->item_op_stock  - $request->itm_op_prev_qty + $request->itm_op_qty;
       $inputdata->item_bal_stock = $inputdata->item_bal_stock - $request->itm_op_prev_qty + $request->itm_op_qty; 
       $inputdata->base_price     = floatval(preg_replace('/[^\d.]/', '', $request->itm_op_price));
       $inputdata->save();

       return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($stock,$itmid,$id)
    {
        try{
          $inputdata  = items::find($itmid);
          $inputdata->item_op_stock  = $inputdata->item_op_stock  - $stock;
          $inputdata->item_bal_stock = $inputdata->item_bal_stock - $stock;
          $inputdata->base_price     = 0;
          $inputdata->save();

          ItemStocks::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
