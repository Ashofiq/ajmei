<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Customers\Customers;
use App\Models\Sales\SalesOrders;
use App\Models\Items\Items;
use App\Models\Items\ItemCategories;
use App\Models\Sales\SalesQuotations;
use App\Models\Sales\SalesQuotationsDetails;

use App\Http\Resources\ItemCodeResource;
use App\Http\Resources\TransItemCodeResource;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use Response;
use DB;
use PDF;

class SalesQuotationController extends Controller
{

  public $quto_id = 0;
  public function so_quot_index()
  {
      $rows = SalesQuotations::query()
        ->orderBy('id', 'desc')->paginate(10);
      $customers = Customers::query()->orderBy('cust_name','asc')->get();
      return view ('/squotations/so_quot_index', compact('rows','customers'));
  }

  public function sales_quot_search(Request $request)
  {
    $cust_id = $request->get('customer_id');
    $rows = SalesQuotations::query()
      ->where('quot_cust_id','=',$cust_id)
      ->orderBy('updated_at', 'desc')->paginate(10);
    $customers = Customers::query()->orderBy('cust_name','asc')->get();
    return view ('/squotations/so_quot_index', compact('rows','customers'));
  }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function so_quot_create()
    {
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $dist_list  = $dropdownscontroller->comboDistrictsList();
      $customers = Customers::query()->where('cust_com_id', '=', $company_code)
      ->orderBy('cust_name','asc')->get();
    
      $generalsController = new GeneralsController();
      if ($company_code == 1 || $company_code == 2)
      $reference_no = $generalsController->makeQuotationNo('JIST',$company_code);
      
      $sql = "select * from `item_categories`
      where `itm_comp_id` = $company_code Order By itm_cat_name asc";
      $itm_cat = DB::select($sql);

      /*$item_list =  Items::query()->where('item_ref_comp_id', '=', $company_code)
      ->orderBy('item_name','asc')->get(); */
      $order_date = date('d-m-Y');

      return view('/squotations/so_quot_create',compact('companies','order_date','company_code','customers','itm_cat','reference_no'));

    }

    public function get_quot_cust_add($customer_id)
    {
      $customers = Customers::find($customer_id);
      return response()->json($customers);
    }

    public function get_quot_item($id){
    //  return('ss'. $id);
      return $itms = Items::query()
          ->join("units", "unit_id", "=", "units.id")
          ->where('item_ref_cate_id','=',$id)
          ->selectRaw('items.id,item_ref_cate_id,item_code,item_name,item_desc,
          item_bar_code, item_op_stock,item_bal_stock,vUnitName,size,base_price, 0 as cust_price')->get();

    }

    public function so_quot_save(Request $request)
    {
      $generalsController = new GeneralsController();
      $company_code     = $request->company_code;
      $existing_cust    = $request->existing_cust;
      $quot_date        = date('Y-m-d',strtotime($request->quot_date));
      $quot_ref         = $request->quot_ref;
      $quot_to          = $request->quot_to;
      $quot_writing_to  = $request->quot_writing_to;
      $exit_customer_id = $request->exit_customer_id;
      if ($exit_customer_id != ''){
        $quot_customer    = $generalsController->CustomerName($exit_customer_id);
      }else{
        $quot_customer    = $request->quot_customer;
      }

      $quot_add         = $request->quot_add;
      $quot_subj        = $request->quot_subj;
      $quot_body        = $request->quot_body;
      $quot_terms_conds = $request->quot_terms_conds;

      if ($company_code == 1 || $company_code == 2)
      $quot_ref = $generalsController->makeQuotationNo('JIST',$company_code);

      $trans_id = SalesQuotations::insertGetId([
        'quot_comp_id'  => $company_code,
        'quot_ref_no'   => $quot_ref,
        'quot_date'     => $quot_date,
        'quot_exit_cust' => $existing_cust,
        'quot_to'       => $quot_to,
        'quot_writ_to'  => $quot_writing_to,
        'quot_cust_id'  =>  $exit_customer_id,
        'quot_cust_name' => $quot_customer,
        'quot_cust_add'  => $quot_add,
        'quot_subj'      => $quot_subj,
        'quot_body'      => $quot_body,
        'quot_term_cond' => $quot_terms_conds,
        ]);

      for($i=1;$i<=10;$i++){
          $catid = $request->input('itemid'.$i);
        //  return $catid;
          //dd($catid);
          $cat_qty = '';
          $cat_amount = '0.00';
          $cat_quot_note = '';

          if ($catid != ''){
              $parameterid = 'parameterid'.$i;
              $test = 'test'.$i;
              $kit  = 'kit'.$i;
              $tab  = 'tabno'.$i;
              $cat_tab        = $request->input('tabno'.$i);
              $cat_qty        = $request->input('qty'.$i);
              $cat_amount     = $request->input('amount'.$i);
              $cat_quot_note  = $request->input('quot_note'.$i);
              $detId          = $request->input('parameterid'.$i);
              if($detId){
                foreach ($detId as $key => $value){
                    //dd($detId);
                    $quot_itmid     = $request->$parameterid[$key];
                    $quot_testprice = $request->$test[$key];
                    $quot_kitprice  = $request->$kit[$key];
                    //echo $quot_itmid.'-'.$quot_kitprice.'--'.$key.'<br/>';
                 if ($quot_itmid  != '' && ($quot_testprice != 0 || $quot_kitprice != 0)){
                      SalesQuotationsDetails::create([
                        'quot_ref_id'      => $trans_id,
                        'tab'              => $cat_tab,
                        'quot_det_comp_id' => $company_code,
                        'quot_cate_id'     => $catid,
                        'quot_qty'         => $cat_qty,
                        'quot_amount'      => $cat_amount,
                        'quot_note'        => $cat_quot_note,
                        'quot_itm_id'      => $quot_itmid,
                        'quot_test_price'  => $quot_testprice,
                        'quot_kit_price'   => $quot_kitprice,
                    ]);
                  }
                }
              }else{
               SalesQuotationsDetails::create([
                  'quot_ref_id'      => $trans_id,
                  'tab'              => $cat_tab,
                  'quot_det_comp_id' => $company_code,
                  'quot_cate_id'     => $catid,
                  'quot_qty'         => $cat_qty,
                  'quot_amount'      => $cat_amount,
                  'quot_note'        => $cat_quot_note,
              ]);
            }
          }

      }

      return back()->withInput();
    }

    public function so_quot_print($quotation_id)
    {
        //return $quotation_id;// dd($sale);
        $rows_m = SalesQuotations::query()
          ->where('id', $quotation_id)->get();
      //  dd($rows_m);

        $rows_d = SalesQuotationsDetails::query()
          ->leftjoin("items", "items.id", "=", "quot_itm_id")
          ->join("item_categories", "item_categories.id", "=", "quot_cate_id")
          ->where('quot_ref_id', $quotation_id)->get();

        $fileName = $quotation_id;
        //return $fileName;
        $pdf = PDF::loadView('/squotations/so_quotation_print_pdf',
        compact('rows_m','rows_d'), [], [
          'title' => $fileName,
        ]);
        return $pdf->stream($fileName,'.pdf');

        return view('/squotations/so_quotation_print_pdf', compact('rows_m','rows_d'));
    }

    public function so_quot_edit($tag, $id)
    {
      $this->quto_id = $id;
      if ($tag == 'e') $action = "sales.quot.update"; // this is for edit
      else $action = "sales.quot.save";  // this is for copy
      $dropdownscontroller = new DropdownsController();
      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
      $customers = Customers::query()->where('cust_com_id', '=', $company_code)
      ->orderBy('cust_name','asc')->get();

       $rows_m = SalesQuotations::query()
        ->where('id', $id)->first();

      //  dd($rows_m);

      $rows_d = SalesQuotationsDetails::query()
        ->join("items", "items.id", "=", "quot_itm_id")
        ->join("item_categories", "item_categories.id", "=", "quot_cate_id")
        ->where('quot_ref_id', $id)->get();

      $itm_cat = ItemCategories::query()
        ->leftjoin('view_sales_quot_uniq_cate', function ($join) {
          $join->on('item_categories.id', '=', 'quot_cate_id')
             ->where('quot_ref_id', '=', $this->quto_id);
           })
        //  ->leftjoin("view_sales_quot_uniq_cate", "item_categories.id", "=", "quot_cate_id")
          ->OrderBy('itm_cat_name')->get();

      return view('/squotations/so_quot_edit',compact('id','action','companies','company_code','customers','rows_m','rows_d','itm_cat'));
    }

   public function so_quot_update(Request $request)
    {
      $id = $request->id;
      $generalsController = new GeneralsController();
      $exit_customer_id = $request->exit_customer_id;
      $company_code     = $request->company_code;
      if ($exit_customer_id != ''){
        $quot_customer    = $generalsController->CustomerName($exit_customer_id);
      }else{
        $quot_customer    = $request->quot_customer;
      }

      $inputdata  = SalesQuotations::find($id);
      $inputdata->quot_ref_no     = $request->quot_ref;
      $inputdata->quot_date       = date('Y-m-d',strtotime($request->quot_date));
      $inputdata->quot_exit_cust  = $request->existing_cust;
      $inputdata->quot_to         = $request->quot_to;
      $inputdata->quot_writ_to    = $request->quot_writing_to;
      $inputdata->quot_cust_id    = $exit_customer_id;
      $inputdata->quot_cust_name  = $quot_customer;
      $inputdata->quot_cust_add   = $request->quot_add;
      $inputdata->quot_subj       = $request->quot_subj;
      $inputdata->quot_body       = $request->quot_body;
      $inputdata->quot_term_cond  = $request->quot_terms_conds;
      $inputdata->save();

      SalesQuotationsDetails::where('quot_ref_id',$id)->delete();
      for($i=1;$i<=10;$i++){

          $catid = $request->input('itemid'.$i);
          $delete = $request->input('delete'.$i);
        //  return $catid;

          //dd($catid);
          $cat_qty = '';
          $cat_amount = '0.00';
          $cat_quot_note = '';

          if ($catid != '' && $delete == 0){
              $parameterid = 'parameterid'.$i;
              $test = 'test'.$i;
              $kit  = 'kit'.$i;
              $tab  = 'tabno'.$i;
              $cat_tab        = $request->input('tabno'.$i);
              $cat_qty        = $request->input('qty'.$i);
              $cat_amount     = $request->input('amount'.$i);
              $cat_quot_note  = $request->input('quot_note'.$i);
              $detId          = $request->input('parameterid'.$i);
              if($detId){
                foreach ($detId as $key => $value){
                    //dd($detId);
                    $quot_itmid     = $request->$parameterid[$key];
                    $quot_testprice = $request->$test[$key];
                    $quot_kitprice  = $request->$kit[$key];
                    echo $id.'-'.$quot_kitprice.'--'.$key.'<br/>';


                 if ($quot_itmid  != '' && ($quot_testprice != 0 || $quot_kitprice != 0)){
                      SalesQuotationsDetails::create([
                        'quot_ref_id'      => $id,
                        'tab'              => $cat_tab,
                        'quot_det_comp_id' => $company_code,
                        'quot_cate_id'     => $catid,
                        'quot_qty'         => $cat_qty,
                        'quot_amount'      => $cat_amount,
                        'quot_note'        => $cat_quot_note,
                        'quot_itm_id'      => $quot_itmid,
                        'quot_test_price'  => $quot_testprice,
                        'quot_kit_price'   => $quot_kitprice,
                    ]);
                  }
                }
              }else{
               SalesQuotationsDetails::create([
                  'quot_ref_id'      => $id,
                  'tab'              => $cat_tab,
                  'quot_det_comp_id' => $company_code,
                  'quot_cate_id'     => $catid,
                  'quot_qty'         => $cat_qty,
                  'quot_amount'      => $cat_amount,
                  'quot_note'        => $cat_quot_note,
              ]);
            }
          }

      } // end for loop

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
            SalesQuotationsDetails::where('quot_ref_id',$id)->delete();
            SalesQuotations::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

}
