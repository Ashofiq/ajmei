<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Inventory\ItemShortages;
use App\Models\Inventory\ItemShortagesDetails;
use App\Http\Resources\TransItemCodeResource;

use App\Models\Items\Items;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;

class InvShortagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $unit_list = $dropdownscontroller->comboUnitsList($company_code);
      $fromdate = date('d-m-Y');
      $todate = date('d-m-Y');

      $q =ItemShortages::query()
          ->join("warehouses", "warehouses.id", "=", "short_m_warehouse_id")
          ->select('item_shortages.id','item_shortages.short_comp_id','short_title','short_date',
          'short_m_warehouse_id','ware_name','short_comments','short_total_qty','short_total_amount');

      if($request->filled('fromdate')){
        $q->where('short_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $q->where('short_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
      $rows = $q->orderBy('item_shortages.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'short_date'  => $request->get('fromdate'),
        'short_date'  => $request->get('todate'),
      ));

      return view ('/inventory/itm_inv_shortages_index', compact('rows','fromdate','todate'));
    }

    public function shortage_modal_view($id)
    {
        $rows_m = ItemShortages::query()
          ->join("warehouses", "warehouses.id", "=", "short_m_warehouse_id")
          ->where('item_shortages.id', $id)
          ->select('item_shortages.id','item_shortages.short_comp_id','short_title','short_date',
          'short_m_warehouse_id','ware_name','short_comments','short_total_qty','short_total_amount')->first();

        $rows_d =ItemShortagesDetails::query()
            ->join("items", "items.id", "=", "short_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('short_ref_id', $id)
            ->select('item_shortages_details.id','item_shortages_details.short_comp_id',
            'item_code','item_name','itm_cat_name','short_lot_no','short_item_qty',
            'short_item_remarks','short_item_unit','short_item_price','short_item_spec')->get();

        return view('inventory.inv_shortages_item_viewmodal',compact('rows_m','rows_d'));
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
       //$generalsController = new GeneralsController();
       $purchase_no = ''; //$generalsController->make_sales_order_ref($company_code);

       //$warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
       //$stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);
       $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
       $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);

       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $short_date = date('d-m-Y');

       return view('/inventory/itm_inv_shortages_create',compact('companies','short_date',
       'company_code','item_list','warehouse_list','stor_list'))->render();
      }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $generalsController = new GeneralsController();
      //Item Validity Check
      $msg = $this->ItemValidityCheck($request);
      if($msg != '') {
        return redirect()->back()->with('message',$msg)->withInput();
      }
      $itemshortages = new ItemShortages();
      try{
          $id = $itemshortages->storeShortage($request);
          $itemshortages->storeShortageItem($request,$id);
      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage())->withInput();
      }
      return redirect()->back()->with('message', 'Shortage Creation Successful.');
  }


  public function ItemValidityCheck($request)
  {
     $detId = $request->input('ItemCodeId');
     $message = '';
     if ($detId){
       $i = 0;
       //return count($detId);
        foreach ($detId as $key => $value){
          if ($request->ItemCodeId[$key] != ''){
             if ($request->Qty[$key] == '' || $request->Qty[$key] <= 0){
                $message = 'Failed: Wrong Qty';
             }
               
          }
        }
     }
     return $message;
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
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      //$generalsController = new GeneralsController();
      $rows_m =ItemShortages::query()
          ->where('item_shortages.id', $id )
          ->select('item_shortages.id','short_comp_id','short_title','short_m_warehouse_id',
          'short_date','short_comments','short_total_qty', 'short_total_amount')
          ->orderBy('item_shortages.id', 'desc')->first();
      $warehouse_list = $dropdownscontroller->WareHouseList($rows_m->short_comp_id);
      $stor_list  = $dropdownscontroller->comboStorageList($rows_m->short_comp_id,$rows_m->short_comp_id);

      $item_list =  Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $rows_m->short_comp_id)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

      $rows_d = ItemShortagesDetails::query()
          ->join("items", "items.id", "=", "short_item_id")
          ->join("view_item_stocks", "items.id", "=", "item_ref_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('short_ref_id', $id)->get();

      return view('/inventory/itm_inv_shortages_edit',
      compact('rows_m','rows_d','companies','item_list','warehouse_list','stor_list'));

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
       //Item Validity Check
       $msg = $this->ItemValidityCheck($request);
       if($msg != '') {
         return redirect()->back()->with('message',$msg)->withInput();
       }
       $itemshortages = new ItemShortages();
       try{
         $inputdata  = ItemShortages::find($id);
         $inputdata->short_comp_id    = $request->company_code;
         $inputdata->short_m_warehouse_id = $request->itm_warehouse;
         $inputdata->short_date       = date('Y-m-d',strtotime($request->short_date));
         $inputdata->short_comments   = $request->comments;
         $inputdata->short_total_qty  = ($request->total_qty=='')?'0':$request->total_qty;
         $inputdata->short_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;

         $inputdata->save();
          //Details Records
          ItemShortagesDetails::where('short_ref_id',$id)->delete();
          $itemshortages->storeShortageItem($request,$id);
       }catch (\Exception $e){
           return redirect()->back()->with('error',$e->getMessage())->withInput();
       }
       return redirect()->back()->with('message', 'Shortage Updated Successful.');

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
             ItemShortagesDetails::where('short_ref_id',$id)->delete();
             ItemShortages::where('id',$id)->delete();
         }catch (\Exception $e){
             return redirect()->back()->with('error',$e->getMessage());
         }
         return redirect()->back()->with('message','Deleted Successfull');

     }
     
    public function get_sel_item($id){
       //  return('ss'. $id);
         return new TransItemCodeResource(
           $itms = Items::query()
             ->join("units", "unit_id", "=", "units.id")
             ->where('items.id','=',$id)
             ->select('items.id','item_code','item_name','item_desc','item_bar_code',
           'item_op_stock','item_bal_stock','base_price as item_price','vUnitName')->first()
         );
       }

}
