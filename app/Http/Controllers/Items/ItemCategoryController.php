<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Items\ItemCategories;
use App\Models\Items\Test;
use App\Models\Items\View_Items_Tree;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Validator;
use Response;
use DB;

class ItemCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function p_index(Request $request)
    {
      //identifiy the company code of user
      $dropdownscontroller = new DropdownsController();
      $companycode = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();

      $sql = "select * from `item_categories`
      where `itm_comp_id` = $companycode Order By itm_cat_name asc";
      $itm_cat = DB::select($sql);

      if($request->filled('company_code')){
        $companycode = $request->get('company_code');
      }

      $rows = ItemCategories::query()
              ->Join("companies", "itm_comp_id", "=", "companies.id")
              ->where('parent_id', 0)
              ->where('item_categories.is_deleted', 0)
              ->where('itm_comp_id', $companycode)
              ->selectRaw('item_categories.id,itm_comp_id,itm_cat_code,itm_cat_name,parent_id,itm_cat_origin,itm_cat_level,companies.name')
              ->orderBy('item_categories.id', 'asc')->paginate(10);
      $collect = collect($rows);
      // get requested action
      return view('/items/cat_index',compact('rows','companies','companycode','itm_cat'));

    }

    public function p_store(Request $request)
    {

       $company_code = $request->company_code;
       // Validate the Field
        $this->validate($request,[
            'company_code'  =>'required',
            'cate_name'     =>'required',
        ]);

        $catecode = ItemCategories::query()
            ->selectRaw('IFNULL(MAX(itm_cat_code),0) + 1 as itm_cat_code')
            ->where('itm_comp_id',$company_code)
            ->where('parent_id','0')
            ->first()->itm_cat_code;

        $origin = $request->cate_name.' >> ';

        $inputdata = new ItemCategories();
        $inputdata->itm_comp_id     = $company_code;
        $inputdata->itm_cat_name    = $request->cate_name;
        $inputdata->itm_cat_code    = $catecode;
        $inputdata->itm_cat_origin  = $origin;
        $inputdata->parent_id       = 0;
        $inputdata->itm_cat_level   = 1;
        $inputdata->save();

        return redirect()->back()->with('message','Category Created Successfull !')->withInput();
    }

    public function p_update(Request $request)
    {
        $id = $request->id;
      // Validate the Field
        $this->validate($request,[
          'company_code'  =>'required',
          'cate_name'     =>'required',
        ]);

        $origin = $request->cate_name.' >> ';

        $inputdata               = ItemCategories::find($id);
        $inputdata->itm_comp_id  = $request->company_code;
        $inputdata->itm_cat_name = $request->cate_name;
        $inputdata->itm_cat_origin  = $origin;
        $inputdata->save();

        return redirect()->back()->with('message','Category updated Successfull !')->withInput();
    }

    public function destroy($id)
    {
        try{
            $isExist = ItemCategories::where('parent_id', '=',$id)->get()->count();
            if($isExist > 0){
              return redirect()->back()->with('message','Can Not Delete.. Sub Category is already existed');
            } else {
              ItemCategories::where('id',$id)->delete();
            }

        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Category Main Head Deleted Successfull');
    }

    public function mk_childcat($parent_id)
    {
      $data = ItemCategories::find($parent_id);
      //$chartofaccounts = Chartofaccounts::where('parent_id', $parent_id)->paginate(10);
      $data1 = ItemCategories::query()
                        ->Join("companies", "itm_comp_id", "=", "companies.id")
                        ->where('parent_id', $parent_id)
                        ->where('item_categories.is_deleted', 0)
                        ->selectRaw('item_categories.id,itm_comp_id,itm_cat_code,itm_cat_name,parent_id,itm_cat_origin,itm_cat_level,companies.name')
                        ->orderBy('item_categories.id', 'asc')->paginate(10);

      $companycode = "'".$data->itm_comp_id."'";

      $origin = '';
      $origins = DB::select("SELECT func_itemcategory_path($parent_id,  $companycode) as origin");
      foreach ($origins as $key => $value) {
          $origin =  $value->origin;
      }

      $collect = collect($data1);
      // get requested action
      return view('/items/cat_child_index',compact('data','data1','origin','parent_id'));
    }

    public function mk_childcat_store(Request $request)
    {
        $parent_id  = $request->parent_id;
        $p_code     = $request->itm_cat_code;
        $cate_level  = $request->itm_cat_level + 1;
        $company_code = $request->company_code;

        $rules = [];
        foreach($request->input('name') as $key => $value) {
              $rules["name.{$key}"] = 'required';
        }
        $insert = false;
        $validator = Validator::make($request->all(), $rules);
            foreach($request->input('name') as $key => $value) {
              if($value != ''){

                $length = strlen($p_code) + 1;
                $maxCode = ItemCategories::where('parent_id', '=',$parent_id)
                  ->where('itm_comp_id', '=', $company_code)
                  ->where('itm_cat_code', 'like', $p_code.'%')
                  ->selectRaw('max(substring(itm_cat_code,'.$length.')) as MaxCode')->first()->MaxCode;

                $cat_code = $maxCode?$maxCode+1:1;

                if ($cat_code == 1) {
                    $cat_code = $p_code . sprintf("%02d", 1);
                }else if (strlen($cat_code) == 1){
                    $cat_code = $p_code . sprintf("%02d", $cat_code);
                }else{
                    $cat_code = $p_code.$cat_code;
                }

                $post = '';
                $origins = DB::select("SELECT func_itemcategory_path($parent_id,  $company_code) as origin");
                foreach( $origins as $index => $acc_origin ) {
                   global $post;
                   $post = $acc_origin->origin;
                }
                 //return $post;
                ItemCategories::create([
                  'itm_comp_id'     => $company_code,
                  'itm_cat_code'    => $cat_code,
                  'itm_cat_name'    => $value,
                  'itm_cat_origin'  => $post,
                  'parent_id'       => $parent_id,
                  'itm_cat_level'   => $cate_level,
                ]);

                $insert = true;
              }
            }
          if($insert)
            return redirect()->back()->with('message','Sub Category Created Successfull');
          else
            return redirect()->back()->with('message','Needs to fill up atleast one Sub Category');
    }

    public function updateChild(Request $request)
    {
        $id        = $request->id;
        $parent_id = $request->parent_id;
      // Validate the Field
        $this->validate($request,[
            'company_code' =>'required',
            'itm_cat_name' =>'required',
        ]);

        $inputdata  = ItemCategories::find($id);
        $inputdata->itm_cat_name = $request->itm_cat_name;
        $inputdata->save();

        return redirect()->back()->with('message','Sub Category Updated Successfull');
     }

     public function child_destroy($id)
     {
         try{
             $isExist = ItemCategories::where('parent_id', '=',$id)->get()->count();
             if($isExist > 0){
               return redirect()->back()->with('message','Can Not Delete.. Sub Category is already existed');
             } else {
               ItemCategories::where('id',$id)->delete();
             }

         }catch (\Exception $e){
             return redirect()->back()->with('error',$e->getMessage());
         }
         return redirect()->back()->with('message','Sub Category Deleted Successfull');
     }

     public function manageItemCatTree(Request $request )
     {
        $code = $request->comp_id;
        $itm_cat_id = $request->itm_category;
        $company = Companies::where('id', $code)->first();

        $categories = View_Items_Tree::query() 
         ->where('parent_id', '=', $itm_cat_id)
         ->where('companyid', '=', $code)->get();

         $allCategories = ItemCategories::query()
         ->Join("items", "item_ref_cate_id", "=", "item_categories.id")
         ->pluck('itm_cat_name','item_categories.id')
         ->where('itm_comp_id', '=', $code)->all();
         return view('items.itm_catTreeview',compact('company','categories'));
     }

}
