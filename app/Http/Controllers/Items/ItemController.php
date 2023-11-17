<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Items\Items;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use Response;
use DB;
use Validator;

class ItemController extends Controller
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
      $unit_list = $dropdownscontroller->comboUnitsList($company_code);

      /*$sql = "select * from `item_categories`
      where `itm_comp_id` = $company_code and `id` not in (select `parent_id` from `item_categories`)
      Order By itm_cat_name asc";
      $itm_cat = DB::select($sql); */

      $item_list =  Items::query()
        ->join("units", "unit_id", "=", "units.id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', '=', $company_code)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')
        ->orderBy('item_name','asc')->get();

      $q = Items::query()
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->join("units", "units.id", "=", "unit_id")
           ->where('item_ref_comp_id', $company_code )
           ->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
           item_desc,item_level,item_qr_code,item_bar_code,item_origin,
           itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price');

      $rows = $q->orderBy('items.id', 'desc')->paginate(10)->setpath('');

      return view ('/items/itm_index', compact('rows','item_list','unit_list'));
      //->renderSections()['content'];
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
       $unit_list = $dropdownscontroller->comboUnitsList($company_code);
       $generalscontroller = new GeneralsController();
       $itm_code = $generalscontroller->maxItemCode($company_code);
       $itm_qrcode = $generalscontroller->maxItemQRCode($company_code);
       $itm_barcode = $generalscontroller->makeItemBarCode($company_code);
     //  return $itm_qrcode.':::'.$itm_barcode;
       $sql = "select * from `item_categories`
       where `itm_comp_id` = $company_code and `id` not in (select `parent_id` from `item_categories`)
       Order By itm_cat_name asc";
       $itm_cat = DB::select($sql);

       $q = Items::query()
            ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
            ->join("units", "units.id", "=", "unit_id")
            ->where('item_ref_comp_id', $company_code )
            ->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
            item_desc,item_level,item_qr_code,item_bar_code,item_origin,
            itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price');

       $rows = $q->orderBy('items.id', 'desc')->get();
      //  $rows->appends(array(
      //    'company_code' => $company_code, 
      //  ));

       return view('/items/itm_create', 
       compact('companies','company_code','itm_code','itm_qrcode','itm_barcode',
       'rows','itm_cat','unit_list')); 
      }
    
      public function getItem(Request $request)
      {
        return [
          'item' => Items::find($request->itemId),
          'categories' => DB::table('item_categories')->get(),
        ];
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
        // $this->validate($request,[
        //     'company_code' =>'required',
        //     'itm_category' =>'required',
        //     'itm_code'     =>'required',
        //     'itm_barcode'  =>'required',
        //     'itm_name'     => 'required | unique:items',
        //     'itm_unit'     =>'required',
        // ]);

        $validator = Validator::make($request->all(),[
          'company_code' =>'required',
          'itm_category' =>'required',
          'itm_code'     =>'required',
          'itm_barcode'  =>'required',
          'item_name'     => 'required | unique:items',
          'itm_unit'     =>'required',
        ]);

        if ($validator->fails()) {
          return redirect('itm-create')
                      ->withErrors($validator)
                      ->withInput();
        }

        $generalscontroller = new GeneralsController();
        $itm_code     = $generalscontroller->maxItemCode($request->company_code);
        $itm_qrcode   = $generalscontroller->maxItemQRCode($request->company_code);
        $itm_barcode  = $generalscontroller->makeItemBarCode($request->company_code);

        $inputdata = new Items();
        $inputdata->item_ref_comp_id = $request->company_code;
        $inputdata->item_ref_cate_id = $request->itm_category;
        $inputdata->item_code       = $itm_code;
        $inputdata->item_bar_code   = $itm_barcode;
        $inputdata->item_qr_code    = $itm_qrcode;
        $inputdata->item_name       = $request->item_name;
        $inputdata->item_desc       = $request->item_desc;
        $inputdata->packing_id      = $request->itm_pack;
        $inputdata->unit_id         = $request->itm_unit;
        $inputdata->size            = $request->itm_size;
        $inputdata->base_price      = $request->itm_price;
        $inputdata->save();


        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function p_search(Request $request)
    {
      $item_id = $request->get('item_id');

      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $unit_list = $dropdownscontroller->comboUnitsList($company_code);

      /*$sql = "select * from `item_categories`
      where `itm_comp_id` = $company_code and `id` not in (select `parent_id` from `item_categories`)
      Order By itm_cat_name asc";
      $itm_cat = DB::select($sql);*/

      $item_list =  Items::query()
        ->join("units", "unit_id", "=", "units.id")
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->where('item_ref_comp_id', '=', $company_code)
        ->select('items.id','item_code','item_name','item_desc','item_bar_code',
        'item_op_stock','item_bal_stock','vUnitName','itm_cat_name')
        ->orderBy('item_name','asc')->get();


      $q = Items::query()
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->join("units", "units.id", "=", "unit_id")
           ->where('item_ref_comp_id', $company_code )
           ->where('items.id', $item_id )
           ->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
           item_desc,item_level,item_qr_code,item_bar_code,item_origin,
           itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price');

      $rows = $q->orderBy('items.id', 'desc')->paginate(10)->setpath('');

      $rows->appends(array(
        'item_id' => $item_id,
      ));

      $collect = collect($rows);
      return view ('/items/itm_index', compact('rows','item_list','unit_list'));
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

       $inputdata  = Items::find($id);
       $inputdata->item_name       = $request->itm_name;
       $inputdata->item_desc       = $request->itm_desc;
       $inputdata->item_ref_cate_id= $request->item_category;
       $inputdata->packing_id      = $request->itm_pack;
       $inputdata->unit_id         = $request->itm_unit;
       $inputdata->size            = $request->itm_size;
       $inputdata->base_price      = $request->itm_price;
       $inputdata->save();
       return back()->withInput();
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
            Items::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
