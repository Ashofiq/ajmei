<?php

namespace App\Models\Inventory;

use App\Models\Inventory\ItemDamagesDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ItemDamages extends Model
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

    public function storeDamage($request){

     $trans_id = ItemDamages::insertGetId([
        'dam_comp_id'   => $request->company_code,
        'dam_title'     => 'DA',
        'dam_date'      => date('Y-m-d',strtotime($request->dam_date)),
        'dam_m_warehouse_id' => $request->itm_warehouse,
        'dam_comments'  => $request->comments,
        'dam_total_qty' => ($request->total_qty=='')?'0':$request->total_qty,
        'dam_total_amount' => ($request->total_amount=='')?'0':$request->total_amount,
      ]);
      return $trans_id;
    }

    public function storeDamageItem($request,$id)
    {
        $detId = $request->input('ItemCodeId');
        if ($detId){
           $i = 0;
            foreach ($detId as $key => $value){
              if ($request->Qty[$key] > 0){
                ItemDamagesDetails::create([
                    'dam_comp_id'    => $request->company_code,
                    'dam_ref_id'     => $id,
                    'dam_warehouse_id' => $request->itm_warehouse,
                    'dam_storage_id' => 1,
                    'dam_lot_no'     => 101, 
                    'dam_item_id'    => $request->ItemCodeId[$key],
                    'dam_item_spec'  => $request->ItemDesc[$key],
                    'dam_item_unit'  => $request->Unit[$key],
                    'dam_item_qty'   => $request->Qty[$key] == ''?'0':$request->Qty[$key],
                    'dam_item_price' => $request->Rate[$key] == ''?'0':$request->Rate[$key],  
                    'dam_item_remarks' => $request->Remarks[$key],
                ]);
              }
            }
        }
    }

}
