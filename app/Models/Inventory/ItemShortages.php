<?php

namespace App\Models\Inventory;
use App\Models\Inventory\ItemShortagesDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ItemShortages extends Model
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

    public function storeShortage($request){

      $trans_id = ItemShortages::insertGetId([
        'short_comp_id'   => $request->company_code,
        'short_title'     => 'SH',
        'short_m_warehouse_id' => $request->itm_warehouse,
        'short_date'      => date('Y-m-d',strtotime($request->short_date)),
        'short_comments'  => $request->comments,
        'short_total_qty' => ($request->total_qty=='')?'0':$request->total_qty,
        'short_total_amount' => ($request->total_amount=='')?'0':$request->total_amount,
      ]);
      return $trans_id;
    }

    public function storeShortageItem($request,$id)
    {
      //Details Records
      $detId = $request->input('ItemCodeId');
      if ($detId){
         $i = 0;
          foreach ($detId as $key => $value){
            if ($request->Qty[$key] > 0){
              ItemShortagesDetails::create([
                  'short_comp_id'    => $request->company_code,
                  'short_ref_id'     => $id,
                  'short_warehouse_id' => $request->itm_warehouse,
                  'short_storage_id' => 1,
                  'short_lot_no'     => 101,
                  'short_item_id'    => $request->ItemCodeId[$key],
                  'short_item_spec'  => $request->ItemDesc[$key],
                  'short_item_unit'  => $request->Unit[$key],
                  'short_item_qty'   => $request->Qty[$key] == ''?'0':$request->Qty[$key],
                  'short_item_price' => $request->Rate[$key] == ''?'0':$request->Rate[$key], 
                  'short_item_remarks' => $request->Remarks[$key],
              ]);
            }
          }
        }
    }

}
