<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Inventory\ItemTransfers;
use App\Models\Inventory\ItemTransfersDetails;
use App\Models\Inventory\ItemReceives;
use App\Models\Inventory\ItemReceivesDetails;
use App\Models\Items\Items;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;

class InvReceivedController extends Controller
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
      $q = ItemReceives::query()
        ->join("warehouses as sou", "sou.id", "=", "rec_m_sou_ware_id")
        ->join("warehouses as rec", "rec.id", "=", "rec_m_rec_ware_id")
        ->select('item_receives.id','rec_transfer_id','item_receives.rec_comp_id','rec_title','item_receives.rec_date as rec_date',
        'rec_m_sou_ware_id','sou.ware_name as s_warename','rec.ware_name as r_warename',
        'rec_comments','rec_total_qty','rec_total_amount');

      if($request->filled('fromdate')){
        $q->where('rec_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $q->where('rec_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
      $rows = $q->orderBy('item_receives.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'rec_date'  => $request->get('fromdate'),
        'rec_date'  => $request->get('todate'),
      ));

      return view ('/inventory/itm_inv_received_index', compact('rows','fromdate','todate'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pending(Request $request)
    {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $unit_list = $dropdownscontroller->comboUnitsList($company_code);
      $fromdate = date('d-m-Y');
      $todate = date('d-m-Y');
      $q = ItemTransfers::query()
        ->join("warehouses as sou", "sou.id", "=", "trans_m_sou_ware_id")
        ->join("warehouses as rec", "rec.id", "=", "trans_m_rec_ware_id")
        ->where('item_transfers.is_received', 0)
        ->select('item_transfers.id','item_transfers.trans_comp_id','trans_title','trans_date',
        'trans_m_sou_ware_id','sou.ware_name as s_warename','rec.ware_name as r_warename','trans_comments',
        'trans_total_qty','trans_total_amount');

      if($request->filled('fromdate')){
        $q->where('trans_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $q->where('trans_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
      $rows = $q->orderBy('item_transfers.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'trans_date'  => $request->get('fromdate'),
        'trans_date'  => $request->get('todate'),
      ));

      return view ('/inventory/itm_inv_pend_received_index', compact('rows','fromdate','todate'));
    }

    public function received_modal_view($id)
    {
        $rows_m = ItemReceives::query()
          ->join("warehouses as sou", "sou.id", "=", "rec_m_sou_ware_id")
          ->join("warehouses as rec", "rec.id", "=", "rec_m_rec_ware_id")
          ->where('item_receives.id', $id)
          ->select('item_receives.id','item_receives.rec_comp_id','rec_title','item_receives.rec_date as rec_date',
          'rec_m_sou_ware_id','sou.ware_name as s_warename','rec.ware_name as r_warename',
          'rec_comments','rec_total_qty','rec_total_amount')->first();

        $rows_d =ItemReceivesDetails::query()
            ->join("items", "items.id", "=", "rec_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('rec_ref_id', $id)
            ->select('item_receives_details.id','item_receives_details.rec_comp_id',
            'item_code','item_name','itm_cat_name','rec_lot_no','rec_item_qty',
            'rec_item_remarks','rec_item_price')->get();

        return view('inventory.inv_received_item_viewmodal',compact('rows_m','rows_d'));
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function create($id)
     {
       $dropdownscontroller = new DropdownsController();
       $company_code = $dropdownscontroller->defaultCompanyCode();
       $companies  = $dropdownscontroller->comboCompanyAssignList();
       $dist_list  = $dropdownscontroller->comboDistrictsList();
       //$generalsController = new GeneralsController();
       $rows_m =ItemTransfers::query()
           ->join("warehouses as sou", "sou.id", "=", "trans_m_sou_ware_id")
           ->join("warehouses as rec", "rec.id", "=", "trans_m_rec_ware_id")
           ->join("storage_locations", "stor_warehouse_id", "=", "trans_m_rec_ware_id")
           ->where('item_transfers.id', $id )
           ->select('item_transfers.id','storage_locations.id as storeid','trans_comp_id','trans_title','trans_m_sou_ware_id',
           'trans_m_rec_ware_id','trans_date','trans_comments','trans_total_qty',
           'trans_total_amount','sou.ware_name as s_warename','rec.ware_name as r_warename')
           ->orderBy('item_transfers.id', 'desc')->first();
       $warehouse_list = $dropdownscontroller->WareHouseList($rows_m->trans_comp_id);
       $stor_list  = $dropdownscontroller->comboStorageList($rows_m->trans_comp_id,$rows_m->trans_m_rec_ware_id);

       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $rows_m->trans_comp_id)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $sql= "select item_transfers_details.*,item_bal_stock,items.item_code,item_name,item_bar_code,item_desc
       from `item_transfers_details`
       inner join `view_item_stocks` on `item_ref_id` = `trans_item_id` and `item_warehouse_id` = trans_sou_ware_id
       and `item_storage_loc` = trans_storage_id and `trans_lot_no` = item_lot_no
       inner join `items` on `items`.`id` = `trans_item_id`
       inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id`
       where `trans_ref_id` = ".$id;
       $rows_d = DB::select($sql);

       $rows_d = collect($rows_d);
       return view('/inventory/itm_inv_received_create',
       compact('rows_m','rows_d','companies','item_list','warehouse_list','stor_list'));

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
      $itemreceives = new ItemReceives();
      try{
          $id = $itemreceives->storeReceives($request);
          $itemreceives->storeReceivesItem($request,$id);
          //Update received tag in transfer table
          $inputdata  = ItemTransfers::find($request->id);
          $inputdata->is_received  = 1;
          $inputdata->save();

      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage())->withInput();
      }
      return redirect()->route('itm.inv.received.pending')->with('message','Item Received Successful.');
      //return redirect()->back()->with('message', 'Item Received Successful.');
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
             /* else if ($request->ItemCodeId[$key] == ''){
                $message = 'Failed: Item Does Not Selected';
             } */
             else if ($request->lotno[$key] == ''){
               $message = 'Failed: Lot No Could Not empty';
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
      $rows_m =ItemTransfers::query()
          ->join("warehouses as sou", "sou.id", "=", "trans_m_sou_ware_id")
          ->join("warehouses as rec", "rec.id", "=", "trans_m_rec_ware_id")
          ->where('item_transfers.id', $id )
          ->select('item_transfers.id','trans_comp_id','trans_title','trans_m_sou_ware_id',
          'trans_m_rec_ware_id','trans_date','trans_comments','trans_total_qty',
          'trans_total_amount','sou.ware_name as s_warename','rec.ware_name as r_warename')
          ->orderBy('item_transfers.id', 'desc')->first();
      $warehouse_list = $dropdownscontroller->WareHouseList($rows_m->trans_comp_id);
      $stor_list  = $dropdownscontroller->comboStorageList($rows_m->trans_comp_id,$rows_m->trans_m_rec_ware_id);

      $item_list =  Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $rows_m->trans_comp_id)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

      $sql= "select item_transfers_details.*,stock as item_bal_stock,items.item_code,item_name,item_bar_code,item_desc
      from `item_transfers_details`
      inner join `view_item_stocks` on `item_ref_id` = `trans_item_id` and `item_warehouse_id` = trans_sou_ware_id
      and `item_storage_loc` = trans_storage_id and `trans_lot_no` = item_lot_no
      inner join `items` on `items`.`id` = `trans_item_id`
      inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id`
      where `trans_ref_id` = ".$id;
      $rows_d = DB::select($sql);

      $rows_d = collect($rows_d);
      return view('/inventory/itm_inv_received_edit',
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


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function destroy($id,$transid)
     {
         //return $id.",".$transid;
         try{
             ItemReceivesDetails::where('rec_ref_id',$id)->delete();
             ItemReceives::where('id',$id)->delete();
             $inputdata  = ItemTransfers::find($transid);
             $inputdata->is_received  = 0;
             $inputdata->save();
         }catch (\Exception $e){
             return redirect()->back()->with('error',$e->getMessage());
         }
         return redirect()->back()->with('message','Deleted Successfull');

     }

}
