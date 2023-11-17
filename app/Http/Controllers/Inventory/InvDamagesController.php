<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Inventory\ItemDamages;
use App\Models\Inventory\ItemDamagesDetails;
use App\Models\Items\Items;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;

class InvDamagesController extends Controller
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
      $q =ItemDamages::query()
          ->join("warehouses", "warehouses.id", "=", "dam_m_warehouse_id")
          ->select('item_damages.id','item_damages.dam_comp_id','dam_title','dam_date',
          'dam_m_warehouse_id','ware_name','dam_comments','dam_total_qty','dam_total_amount');

      if($request->filled('fromdate')){
        $q->where('dam_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $q->where('dam_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
      $rows = $q->orderBy('item_damages.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'dam_date'  => $request->get('fromdate'),
        'dam_date'  => $request->get('todate'),
      ));

      return view ('/inventory/itm_inv_damages_index', compact('rows','fromdate','todate'));
    }

    public function damage_modal_view($id)
    {
        $rows_m = ItemDamages::query()
          ->join("warehouses", "warehouses.id", "=", "dam_m_warehouse_id")
          ->where('item_damages.id', $id)
          ->select('item_damages.id','item_damages.dam_comp_id','dam_title','dam_date',
          'dam_m_warehouse_id','dam_comments','dam_total_qty','dam_total_amount','ware_name')->first();

        $rows_d =ItemDamagesDetails::query()
            ->join("items", "items.id", "=", "dam_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('dam_ref_id', $id)
            ->select('item_damages_details.id','item_damages_details.dam_comp_id',
            'item_code','item_name','itm_cat_name','dam_lot_no','dam_item_spec','dam_item_qty',
            'dam_item_remarks','dam_item_price','dam_item_price','dam_item_spec')->get();

        return view('inventory.inv_damages_item_viewmodal',compact('rows_m','rows_d'));
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
       $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
       $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);

      // $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      //$stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);

      $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)   
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $dam_date = date('d-m-Y');

       return view('/inventory/itm_inv_damages_create',compact('companies','dam_date',
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
      $detId = $request->input('ItemCodeId');
      $msg = $this->ItemValidityCheck($detId,$request);
      if($msg != '') {
        return redirect()->back()->with('message',$msg)->withInput();
      }
      $itemdamages = new ItemDamages();
      try{
          $id = $itemdamages->storeDamage($request);
          $itemdamages->storeDamageItem($request,$id);
      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage())->withInput();
      }
      return redirect()->back()->with('message', 'Damage Creation Successful.');
  }

  public function ItemValidityCheck($detId,$request)
  {
     $message = '';
     if ($detId){
       $i = 0;
       //return count($detId);
        foreach ($detId as $key => $value){
          if ($request->ItemCodeId[$key] != ''){
             if ($request->Qty[$key] == '' || $request->Qty[$key] <= 0){
                $message = 'Failed: Wrong Qty';
             }
             /* else if ($request->ItemCodeId[$key] == ''){
                $message = 'Failed: Item Does Not Selected';
             } */ 
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
      $rows_m =ItemDamages::query()
          ->where('item_damages.id', $id )
          ->select('item_damages.id','dam_comp_id','dam_title','dam_m_warehouse_id',
          'dam_date','dam_comments','dam_total_qty', 'dam_total_amount')
          ->orderBy('item_damages.id', 'desc')->first();
      $warehouse_list = $dropdownscontroller->WareHouseList($rows_m->dam_comp_id);
      $stor_list  = $dropdownscontroller->comboStorageList($rows_m->dam_comp_id,$rows_m->dam_m_warehouse_id);

      $item_list =  Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $rows_m->dam_comp_id)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();
 

      $rows_d = ItemDamagesDetails::query()
          ->join("items", "items.id", "=", "dam_item_id")
          ->join("view_item_stocks", "items.id", "=", "item_ref_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('dam_ref_id', $id)->get();

      return view('/inventory/itm_inv_damages_edit',
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
       $detId = $request->input('ItemCodeId');
       $msg = $this->ItemValidityCheck($detId,$request);
       if($msg != '') {
         return redirect()->back()->with('message',$msg)->withInput();
       }
       $itemdamages = new ItemDamages();
       try{
          $inputdata  = ItemDamages::find($id);
          $inputdata->dam_comp_id    = $request->company_code;
          $inputdata->dam_m_warehouse_id = $request->itm_warehouse;
          $inputdata->dam_date       = date('Y-m-d',strtotime($request->dam_date));
          $inputdata->dam_comments   = $request->comments;
          $inputdata->dam_total_qty  = ($request->total_qty=='')?'0':$request->total_qty;
          $inputdata->dam_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;
          $inputdata->save();
          //Details Records
          ItemDamagesDetails::where('dam_ref_id',$id)->delete();
          $itemdamages->storeDamageItem($request,$id);
       }catch (\Exception $e){
           return redirect()->back()->with('error',$e->getMessage())->withInput();
       }
       return redirect()->back()->with('message', 'Damage Updated Successful.');
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
             ItemDamagesDetails::where('dam_ref_id',$id)->delete();
             ItemDamages::where('id',$id)->delete();
         }catch (\Exception $e){
             return redirect()->back()->with('error',$e->getMessage());
         }
         return redirect()->back()->with('message','Deleted Successfull');

     }

}
