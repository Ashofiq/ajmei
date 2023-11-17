<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Inventory\ItemTransfers;
use App\Models\Inventory\ItemTransfersDetails;
use App\Models\Inventory\view_item_stocks;
use App\Models\Items\Items;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;

class InvTransferredController extends Controller
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

      $q =ItemTransfers::query()
          ->join("warehouses as sou", "sou.id", "=", "trans_m_sou_ware_id")
          ->join("warehouses as rec", "rec.id", "=", "trans_m_rec_ware_id")
          ->select('item_transfers.id','item_transfers.trans_comp_id','trans_title','trans_date',
          'trans_m_sou_ware_id','sou.ware_name as s_ware_name','rec.ware_name as r_ware_name',
          'trans_m_rec_ware_id','trans_comments',
          'trans_total_qty','trans_total_amount','is_received');

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

      return view ('/inventory/itm_inv_transferred_index', compact('rows','fromdate','todate'));
    }

    public function transfer_modal_view($id)
    {
        $rows_m = ItemTransfers::query()
          ->join("warehouses as sou", "sou.id", "=", "trans_m_sou_ware_id")
          ->join("warehouses as rec", "rec.id", "=", "trans_m_rec_ware_id")
          ->where('item_transfers.id', $id)
          ->select('item_transfers.id','item_transfers.trans_comp_id','trans_title','trans_date',
          'trans_m_sou_ware_id','sou.ware_name as s_warename','rec.ware_name as r_warename','trans_comments',
          'trans_total_qty','trans_total_amount')->first();

        $rows_d =ItemTransfersDetails::query()
            ->join("items", "items.id", "=", "trans_item_id")
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->where('trans_ref_id', $id)
            ->select('item_transfers_details.id','item_transfers_details.trans_comp_id',
            'item_code','item_name','itm_cat_name','trans_lot_no','trans_item_qty',
            'trans_item_remarks','trans_item_price')->get();

        return view('inventory.inv_transferred_item_viewmodal',compact('rows_m','rows_d'));
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

       $warehouse_list = $dropdownscontroller->WareHouseList($company_code);
       $stor_list  = $dropdownscontroller->comboStorageList($company_code,0);

       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $trans_date = date('d-m-Y');

       return view('/inventory/itm_inv_transferred_create',compact('companies','trans_date',
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
      $itemtransfers = new ItemTransfers();
      try{
          $id = $itemtransfers->storeTransfers($request);
          $itemtransfers->storeTransfersItem($request,$id);
      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage())->withInput();
      }
      return redirect()->back()->with('message', 'Item Transferred Successful.');
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
          ->where('item_transfers.id', $id )
          ->select('item_transfers.id','trans_comp_id','trans_title','trans_m_sou_ware_id',
          'trans_m_rec_ware_id','trans_date','trans_comments','trans_total_qty', 'trans_total_amount')
          ->orderBy('item_transfers.id', 'desc')->first();
      $warehouse_list = $dropdownscontroller->WareHouseList($rows_m->trans_comp_id);
      $stor_list  = $dropdownscontroller->comboStorageList($rows_m->trans_comp_id,$rows_m->trans_m_rec_ware_id);

      $item_list =  Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $rows_m->trans_comp_id)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

       /*$rows_d = ItemTransfersDetails::query()
          ->join('view_item_stocks', function ($join) {
              $join->on('Aitem_ref_id', '=', 'trans_item_id')
                 ->where('item_warehouse_id', '=', 'trans_sou_ware_id')
                 ->where('item_storage_loc', '=','trans_storage_id')
                 ->where('trans_lot_no', '=','item_lot_no');
            })
          ->join("items", "items.id", "=", "trans_item_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->selectRaw('item_transfers_details.*,item_bal_stock')
          ->where('trans_ref_id', $id)->get(); */

      $sql= "select item_transfers_details.*,stock as item_bal_stock,items.item_code,item_bar_code,item_desc
      from `item_transfers_details`
      inner join `view_item_stocks` on `item_ref_id` = `trans_item_id` and `item_warehouse_id` = trans_sou_ware_id
      and `item_storage_loc` = trans_storage_id and `trans_lot_no` = item_lot_no
      inner join `items` on `items`.`id` = `trans_item_id`
      inner join `item_categories` on `item_categories`.`id` = `item_ref_cate_id`
      where `trans_ref_id` = ".$id;
      $rows_d = DB::select($sql);

      $rows_d = collect($rows_d);
      return view('/inventory/itm_inv_transferred_edit',
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
       $itemtransfers = new ItemTransfers();
       try{
         $inputdata  = ItemTransfers::find($id);
         $inputdata->trans_comp_id    = $request->company_code;
         $inputdata->trans_m_sou_ware_id = $request->itm_warehouse;
         $inputdata->trans_m_rec_ware_id = $request->itm_rec_warehouse;
         $inputdata->trans_date       = date('Y-m-d',strtotime($request->trans_date));
         $inputdata->trans_comments   = $request->comments;
         $inputdata->trans_total_qty  = ($request->total_qty=='')?'0':$request->total_qty;

         $inputdata->save();
          //Details Records
          ItemTransfersDetails::where('trans_ref_id',$id)->delete();
          $itemtransfers->storeTransfersItem($request,$id);
       }catch (\Exception $e){
           return redirect()->back()->with('error',$e->getMessage())->withInput();
       }
       return redirect()->back()->with('message', 'Transferred Updated Successful.');

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
             ItemTransfersDetails::where('trans_ref_id',$id)->delete();
             ItemTransfers::where('id',$id)->delete();
         }catch (\Exception $e){
             return redirect()->back()->with('error',$e->getMessage());
         }
         return redirect()->back()->with('message','Deleted Successfull');

     }

}
