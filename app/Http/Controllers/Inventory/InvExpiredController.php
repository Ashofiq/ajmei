<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Suppliers\Suppliers;
use App\Models\Inventory\ItemExpires;
use App\Models\Inventory\ItemExpiresDetails;
use App\Models\Items\Items;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use Response;
use DB;

class InvExpiredController extends Controller
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

      $q =ItemExpires::query()
          ->select('item_expires.id','item_expires.exp_comp_id','exp_title','exp_date',
          'exp_comments','exp_total_qty','exp_total_amount');

      if($request->filled('fromdate')){
        $q->where('exp_date','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $q->where('exp_date','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
      $rows = $q->orderBy('item_expires.id', 'desc')->paginate(10)->setpath('');
      $rows->appends(array(
        'exp_date'  => $request->get('fromdate'),
        'exp_date'  => $request->get('todate'),
      ));

      return view ('/inventory/itm_inv_expired_index', compact('rows','fromdate','todate'));
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
       $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
       $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);

       $item_list =  Items::query()
       ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
       ->where('item_ref_comp_id', '=', $company_code)
       ->select('items.id','item_code','item_name','item_desc','item_bar_code',
       'item_op_stock','item_bal_stock','itm_cat_name')
       ->orderBy('item_name','asc')->get();

       $exp_date = date('d-m-Y');

       return view('/inventory/itm_inv_expired_create',compact('companies','exp_date',
       'company_code','item_list','warehouse_id','stor_list'))->render();
      }

      public function expired_modal_view($id)
      {
          $rows_m = ItemExpires::query()
            ->where('item_expires.id', $id)
            ->select('item_expires.id','item_expires.exp_comp_id','exp_title','exp_date',
            'exp_comments','exp_total_qty','exp_total_amount')->first();

          $rows_d =ItemExpiresDetails::query()
              ->join("items", "items.id", "=", "exp_item_id")
              ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
              ->where('exp_ref_id', $id)
              ->select('item_expires_details.id','item_expires_details.exp_comp_id',
              'item_code','item_name','itm_cat_name','exp_lot_no','exp_item_qty',
              'exp_item_remarks','exp_item_price','exp_item_unit','exp_item_spec')->get();

          return view('inventory.inv_expired_item_viewmodal',compact('rows_m','rows_d'));
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
      $itemexpires = new ItemExpires();
      try{
          $id = $itemexpires->storeExpired($request);
          $itemexpires->storeExpiredItem($request,$id);
      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage())->withInput();
      }
      return redirect()->back()->with('message', 'Expired Creation Successful.');
  }

  public function ItemValidityCheck($request)
  {
     $message = '';
     $detId = $request->input('ItemCodeId');
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
      $ware_list = $dropdownscontroller->WareHouseList($company_code);
      $warehouse_id = $dropdownscontroller->defaultWareHouseCode($company_code);
      $stor_list  = $dropdownscontroller->comboStorageList($company_code,$warehouse_id);

      $item_list =  Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', '=', $company_code)
      ->select('items.id','item_code','item_name','item_desc','item_bar_code',
      'item_op_stock','item_bal_stock','itm_cat_name')
      ->orderBy('item_name','asc')->get();

      $rows_m =ItemExpires::query()
          ->where('exp_comp_id', '=', $company_code)
          ->where('item_expires.id', $id )
          ->select('item_expires.id','exp_comp_id','exp_title',
          'exp_date','exp_comments','exp_total_qty', 'exp_total_amount')
          ->orderBy('item_expires.id', 'desc')->first();

      $rows_d = ItemExpiresDetails::query()
          ->join("items", "items.id", "=", "exp_item_id")
          ->join("view_item_stocks", "items.id", "=", "item_ref_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('exp_ref_id', $id)->get();

      return view('/inventory/itm_inv_expired_edit',
      compact('rows_m','rows_d','companies','item_list','warehouse_id','ware_list','stor_list'));

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
       $itemexpires = new ItemExpires();
       try{
         $inputdata  = ItemExpires::find($id);
         $inputdata->exp_comp_id    = $request->company_code;
         $inputdata->exp_date       = date('Y-m-d',strtotime($request->exp_date));
         $inputdata->exp_comments   = $request->comments;
         $inputdata->exp_total_qty  = ($request->total_qty=='')?'0':$request->total_qty;
         $inputdata->exp_total_amount  = ($request->total_amount=='')?'0':$request->total_amount;
         $inputdata->save();
          //Details Records
         ItemExpiresDetails::where('exp_ref_id',$id)->delete();
         $itemexpires->storeExpiredItem($request,$id);
       }catch (\Exception $e){
           return redirect()->back()->with('error',$e->getMessage())->withInput();
       }
       return redirect()->back()->with('message', 'Expired Updated Successful.');

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
             ItemExpiresDetails::where('exp_ref_id',$id)->delete();
             ItemExpires::where('id',$id)->delete();
         }catch (\Exception $e){
             return redirect()->back()->with('error',$e->getMessage());
         }
         return redirect()->back()->with('message','Deleted Successfull');
     }

}
