<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Customers\Customers;
use App\Models\Customers\CustomerPrices;
use App\Models\Items\Items;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;
use DB;

class CustPriceController extends Controller
{
  public $cust_id = 0;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id,$comp)
    {
      //return $id .' :: '. $comp;
      $this->cust_id = $id;
      $dropdownscontroller = new DropdownsController();
      $item_list = Items::query()
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->join("units", "units.id", "=", "unit_id")
        ->where('item_ref_comp_id', $comp)
        ->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
        item_desc,item_level,item_qr_code,item_bar_code,item_origin,
        itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price')->orderBy('item_name','asc')->get();

      $item_pend_list = Items::query()
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->join("units", "units.id", "=", "unit_id")
        ->leftjoin('customer_prices', function ($join) {
          $join->on('items.id', '=', 'cust_item_p_id')
          ->where('cust_p_ref_id', '=', $this->cust_id);
      })
        ->where('item_ref_comp_id', $comp)
        ->whereNull('cust_item_p_id')
        ->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
        item_desc,item_level,item_qr_code,item_bar_code,item_origin,
        itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price')->orderBy('item_name','asc')->get();

    $generalscontroller = new GeneralsController();
    $cust_name  = $generalscontroller->CustomerName($id);
    $cust_code  = $generalscontroller->CustomerCode($id);
    $company_name  = $generalscontroller->CompanyName($comp);

    $rows = CustomerPrices::query()
          ->Join("items", "items.id", "=", "cust_item_p_id")
          ->join("units", "units.id", "=", "unit_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('cust_p_ref_id', '=', $id)
          ->where('p_del_flag', '=', 0)
          ->where('cust_p_com_ref_id', '=', $comp)
          ->selectRaw('customer_prices.id, cust_price,cust_comm,p_valid_from,p_valid_to,p_del_flag,item_code,item_name,itm_cat_name,vUnitName,packing_id,size')
          ->orderBy('customer_prices.id', 'desc')->get();


      return view ('/customers/cust_price_index', compact('id','comp','rows',
      'company_name','cust_name','cust_code','item_list','item_pend_list'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
      //return $id .' :: '. $comp;
      $id       = $request->input('s_cust_id');
      $item_id  = $request->input('s_item_id');
      $comp     = $request->input('s_comp_id');
      $this->cust_id = $id;
      $dropdownscontroller = new DropdownsController();
      $item_list = Items::query()
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->join("units", "units.id", "=", "unit_id")
        ->where('item_ref_comp_id', $comp)
        ->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
        item_desc,item_level,item_qr_code,item_bar_code,item_origin,
        itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price')->orderBy('item_name','asc')->get();

      $item_pend_list = Items::query()
        ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
        ->join("units", "units.id", "=", "unit_id")
        ->leftjoin('customer_prices', function ($join) {
        $join->on('items.id', '=', 'cust_item_p_id')
          ->where('cust_p_ref_id', '=', $this->cust_id)
          ->where('p_del_flag', '=', 0);
        })
        ->where('item_ref_comp_id', $comp)
        ->whereNull('cust_item_p_id')
        ->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
        item_desc,item_level,item_qr_code,item_bar_code,item_origin,
        itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price')->orderBy('item_name','asc')->get();

      $generalscontroller = new GeneralsController();
      $cust_name  = $generalscontroller->CustomerName($id);
      $cust_code  = $generalscontroller->CustomerCode($id);
      $company_name  = $generalscontroller->CompanyName($comp);

     $q = CustomerPrices::query()
          ->Join("items", "items.id", "=", "cust_item_p_id")
          ->join("units", "units.id", "=", "unit_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('cust_p_ref_id', '=', $id);

      if ($item_id !='')  $q->where('cust_item_p_id', '=', $item_id);

      $q->where('p_del_flag', '=', 0)
          ->where('cust_p_com_ref_id', '=', $comp)
          ->selectRaw('customer_prices.id, cust_price,cust_comm,p_valid_from,p_valid_to,p_del_flag,item_code,item_name,itm_cat_name,vUnitName,packing_id,size')
          ->orderBy('customer_prices.id', 'desc');
     $rows=$q->get();

      return view ('/customers/cust_price_index', compact('id','comp','rows',
      'company_name','cust_name','cust_code','item_list','item_pend_list'));
    }

    public function index1($id,$comp)
    {
      //return $id .' :: '. $comp;
      $dropdownscontroller = new DropdownsController();
      //$item_list =  Items::query()->orderBy('item_name','asc')->get();
      $item_list = Items::query()
           ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
           ->join("units", "units.id", "=", "unit_id")
           ->where('item_ref_comp_id', $comp)
           ->selectRaw('items.id,item_ref_comp_id,item_ref_cate_id,item_code,item_name,
           item_desc,item_level,item_qr_code,item_bar_code,item_origin,
           itm_cat_code,itm_cat_name,itm_cat_origin,packing_id,units.id as unit_id,vUnitName,size,base_price')->orderBy('item_name','asc')->get();


      $generalscontroller = new GeneralsController();
      $cust_name  = $generalscontroller->CustomerName($id);
      $cust_code  = $generalscontroller->CustomerCode($id);
      $company_name  = $generalscontroller->CompanyName($comp);

      $rows = CustomerPrices::query()
          ->Join("items", "items.id", "=", "cust_item_p_id")
          ->join("units", "units.id", "=", "unit_id")
          ->join("item_categories", "item_categories.id", "=", "item_ref_cate_id")
          ->where('cust_p_ref_id', '=', $id)
          ->where('cust_p_com_ref_id', '=', $comp)
          ->selectRaw('customer_prices.id, cust_price,cust_comm,p_valid_from,p_valid_to,p_del_flag,item_code,item_name,itm_cat_name,vUnitName,packing_id,size')
          ->orderBy('customer_prices.id', 'desc')->paginate(10);

      return view ('/customers/cust_price_index1', compact('id','comp','rows','company_name','cust_name','cust_code','item_list'));
    }

    public function store(Request $request)
    {
      // Validate the Field
        $this->validate($request,[
            'comp_id'   =>'required',
            'id'        =>'required',
            'item_id'   =>'required',
            'itm_price' =>'required',
            'fromdate'  =>'required',
            'todate'    =>'required',
        ]);

    //  return $this->dateValidityCheck($request->comp_id,$request->item_id,$request->fromdate,$request->todate);

     if($this->dateValidityCheck($request->comp_id,$request->id,$request->item_id,
        $request->fromdate,$request->todate)){
          $inputdata = new CustomerPrices();
          $inputdata->cust_p_com_ref_id = $request->comp_id;
          $inputdata->cust_p_ref_id   = $request->id;
          $inputdata->cust_item_p_id  = $request->item_id;
          $inputdata->cust_price      = $request->itm_price;
          $inputdata->cust_comm       = $request->itm_comm;
          $inputdata->p_valid_from    = $request->fromdate;
          $inputdata->p_valid_to      = $request->todate;
          $inputdata->save();

          return redirect()->route('cust.price.index', ['id' => $request->id,'comp' => $request->comp_id])
          ->with('message','Price Created Successfull !')->withInput();

          //return redirect()->back()->with('message','Price Created Successfull !')->withInput();
        } else {
          return redirect()->route('cust.price.index',$request->id,$request->comp_id)
          ->with('message','InValid Date !')->withInput();

        //  return redirect()->back()->with('message','InValid Date !')->withInput();
        }
   }

   public function store1(Request $request)
   {
     // Validate the Field
       $this->validate($request,[
           'comp_id'   =>'required',
           'fromdate'  =>'required',
           'todate'    =>'required',
       ]);

      $detId = $request->input('ItemCodeId');
      if ($detId){
        //Validity Checking
         foreach ($detId as $key => $value){
            if ($request->Price[$key] > 0){
              $item_id  = $request->ItemCodeId[$key];
              $itemCode = $request->ItemCode[$key];
              $price    = $request->Price[$key];
              $fromdate = $request->fromdate;
              $todate   = $request->todate;
              if(!$this->dateValidityCheck($request->comp_id,$request->id,$item_id,
                $request->fromdate,$request->todate)){
                  $msg = 'Code:'.$itemCode.':'.$price.' : '.$fromdate.' : '.$todate;
                  return redirect()->back()->with('message','InValid Date ->'.$msg)->withInput();
              }
            }
        }

        foreach ($detId as $key => $value){
          if ($request->Price[$key] > 0){
            CustomerPrices::create([
              'cust_p_com_ref_id' => $request->comp_id,
              'cust_p_ref_id'     => $request->id,
              'cust_item_p_id'    => $request->ItemCodeId[$key],
              'cust_price'        => $request->Price[$key],
              'p_valid_from'      => $request->fromdate,
              'p_valid_to'        => $request->todate,
            ]);
          }
        }
      }

      return redirect()->back()->with('message','Price Created Successfull !')->withInput();

  }

  public function update(Request $request)
  {
    // Validate the Field
      $this->validate($request,[
          'company_id'=>'required',
          'update_id' =>'required',
          'itm_code'  =>'required',
          'itm_u_price' =>'required',
          'valid_from' =>'required',
          'valid_to'   =>'required',
      ]);

        $inputdata  = CustomerPrices::find($request->update_id);
        $inputdata->cust_price      = $request->itm_u_price;
        $inputdata->cust_comm       = $request->itm_u_comm;
        $inputdata->p_valid_from    = date('Y-m-d',strtotime($request->valid_from));
        $inputdata->p_valid_to      = date('Y-m-d',strtotime($request->valid_to));
        $inputdata->save();

        return redirect()->route('cust.price.index', ['id' => $request->customer_code,'comp' => $request->company_id])
        ->with('message','Price Created Successfull !')->withInput();
      //  return redirect()->back()->with('message','Price Update Successfull !')->withInput();

 }

   public function dateValidityCheck($comp_id,$cust_id,$item_id,$fromdate,$todate)
   {
     $isValid = true;
     $sql = "SELECT id as rec FROM customer_prices
     Where cust_p_com_ref_id = ".$comp_id." AND cust_p_ref_id = ".$cust_id ." AND cust_item_p_id = ".$item_id ."
     AND p_del_flag = 0 AND '". $fromdate. "' BETWEEN p_valid_from and p_valid_to";
     $data = DB::select($sql);
     $countofrec = count($data);
     //return $countofrec;
     if($countofrec>0){
       $isValid = false;
     }else{
       $sql1 = "SELECT id as rec FROM customer_prices
       Where cust_p_com_ref_id = ".$comp_id." AND cust_p_ref_id = ".$cust_id ." AND cust_item_p_id = ".$item_id ."
       AND p_del_flag = 0 AND '". $todate. "' BETWEEN p_valid_from and p_valid_to";
       $data1 = DB::select($sql1);
       $countofrec1 = count($data1);
       if($countofrec1>0){
          $isValid = false;
       }
     }
     return $isValid;
   }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id,$cid,$comp)
    {
      try{
        //return $id;
        $inputdata  = CustomerPrices::find($id);
        $inputdata->p_del_flag = 1;
        $inputdata->save();

        //CustomerPrices::where('id',$id)->delete();
      }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage());
      }
      return redirect()->route('cust.price.index', ['id' => $cid,'comp' => $comp])
      ->with('message','Deletetion Successfull !')->withInput();
      //return redirect()->back()->with('message','Deletetion Successfull');
    }

}
