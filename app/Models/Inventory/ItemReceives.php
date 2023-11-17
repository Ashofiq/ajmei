<?php

namespace App\Models\Inventory;
use App\Models\Inventory\ItemReceivesDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ItemReceives extends Model
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

    public function storeReceives($request){

      $trans_id = ItemReceives::insertGetId([
        'rec_comp_id'   => $request->company_code,
        'rec_title'     => 'SR',
        'rec_transfer_id' => $request->id,
        'rec_m_sou_ware_id' => $request->itm_warehouse,
        'rec_m_rec_ware_id' => $request->itm_rec_warehouse,
        'rec_date'      => date('Y-m-d',strtotime($request->rec_date)),
        'rec_comments'  => $request->comments,
        'rec_total_qty' => ($request->total_qty=='')?'0':$request->total_qty,
      ]);
      return $trans_id;
    }

    public function storeReceivesItem($request,$id)
    {
      //Details Records
      $detId = $request->input('ItemCodeId');
      if ($detId){
         $i = 0;
          foreach ($detId as $key => $value){
            if ($request->Qty[$key] > 0){
                ItemReceivesDetails::create([
                  'rec_comp_id'    => $request->company_code,
                  'rec_ref_id'     => $id,
                  'rec_sou_ware_id' => $request->itm_warehouse,
                  'rec_rec_ware_id' => $request->itm_rec_warehouse,
                  'rec_storage_id' => $request->Storage[$key],
                  'rec_lot_no'     => $request->lotno[$key],
                  'rec_item_id'    => $request->ItemCodeId[$key],
                  'rec_item_unit'  => $request->Unit[$key],
                  'rec_item_qty'   => $request->Qty[$key],
                  'rec_item_remarks' => $request->Remarks[$key],
              ]);
            }
          }
        }
    }

}
