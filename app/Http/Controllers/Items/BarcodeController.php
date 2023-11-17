<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Items\Items;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

use PDF;

class BarcodeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('can:barcode');
    }

    public function index(){
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $item_list =  Items::query()
      ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
      ->where('item_ref_comp_id', $company_code)
      ->select('items.id','item_bar_code','item_code','item_name','itm_cat_name')
      ->orderBy('item_name','asc')->get();
      
      $items = Items::select('items.id','item_bar_code','item_code','item_name','itm_cat_name')
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', $company_code)
        ->orderByDesc('items.id')->paginate(5);
//      $items = Item::where('ware_house_id',auth()->user()->ware_house_id)->orderByDesc('id')->paginate(20);
        return view('barcode.index',compact('items','item_list','companies','company_code'));
    }

    public function getSearchBarcode(Request $request){
      $company_code = $request->input('company_code');
      $item_id = $request->input('item_id');

      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      
      $item_list =  Items::query()
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', $company_code)
        ->select('items.id','item_bar_code','item_code','item_name','itm_cat_name')
        ->orderBy('item_name','asc')->get();
      
      $items = Items::query() 
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', $company_code)
        ->where('items.id', $item_id)
        ->select('items.id','item_bar_code','item_code','item_name','itm_cat_name')
        ->orderByDesc('items.id')->paginate(5);
//      $items = Item::where('ware_house_id',auth()->user()->ware_house_id)->orderByDesc('id')->paginate(20);
        return view('barcode.index',compact('items','item_list','companies','company_code'));
    }

    public function print($id){
        //$item = Items::findOrFail($id);
        
        $item = Items::query() 
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('items.id', $id)->first();
        
        $fileName = $item->item_name;
         $pdf = PDF::loadView('barcode.print',compact('item','id'), [], [
            'title' => $fileName,
        ]);
        //return $pdf->stream($fileName,'.pdf');
        return view('barcode.print',compact('item','id'));
    }

}
