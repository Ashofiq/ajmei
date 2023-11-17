<?php

namespace App\Models\Inventory;
use App\Models\Inventory\ItemExpiresDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ItemExpires extends Model
{
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        if(!App::runningInConsole())
        {
            static::creating(function ($model)
            {
                $model->fill([
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            });
            static::updating(function ($model)
            {
                $model->fill([
                    'updated_by' => Auth::id(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => Carbon::now(),
                ]);
            });
        }
    }

    public function storeExpired($request){

      $trans_id = ItemExpires::insertGetId([
        'exp_comp_id'   => $request->company_code,
        'exp_title'     => 'EX',
        'exp_date'      => date('Y-m-d',strtotime($request->exp_date)),
        'exp_comments'  => $request->comments,
        'exp_total_qty' => ($request->total_qty=='')?'0':$request->total_qty,
      ]);
      return $trans_id;
    }

    public function storeExpiredItem($request,$id)
    {
      //Details Records
      $detId = $request->input('ItemCodeId');
      if ($detId){
         $i = 0;
          foreach ($detId as $key => $value){
            if ($request->Qty[$key] > 0){
              ItemExpiresDetails::create([
                  'exp_comp_id'    => $request->company_code,
                  'exp_ref_id'     => $id,
                  'exp_warehouse_id' => $request->itm_warehouse,
                  'exp_storage_id' => 1,
                  'exp_lot_no'     => 101,
                  'exp_item_id'    => $request->ItemCodeId[$key],
                  'exp_item_spec'  => $request->ItemDesc[$key],
                  'exp_item_unit'  => $request->Unit[$key],
                  'exp_item_qty'   => $request->Qty[$key] == ''?'0':$request->Qty[$key],
                  'exp_item_price' => $request->Rate[$key] == ''?'0':$request->Rate[$key],
                  'exp_item_remarks' => $request->Remarks[$key],
              ]);
            }
          }
       }
    }

}
