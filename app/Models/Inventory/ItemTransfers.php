<?php

namespace App\Models\Inventory;
use App\Models\Inventory\ItemTransfersDetails;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ItemTransfers extends Model
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

    public function storeTransfers($request){

      $trans_id = ItemTransfers::insertGetId([
        'trans_comp_id'   => $request->company_code,
        'trans_title'     => 'ST',
        'trans_m_sou_ware_id' => $request->itm_warehouse,
        'trans_m_rec_ware_id' => $request->itm_rec_warehouse,
        'trans_date'      => date('Y-m-d',strtotime($request->trans_date)),
        'trans_comments'  => $request->comments,
        'trans_total_qty' => ($request->total_qty=='')?'0':$request->total_qty,
      ]);
      return $trans_id;
    }

    public function storeTransfersItem($request,$id)
    {
      //Details Records
      $detId = $request->input('ItemCodeId');
      if ($detId){
         $i = 0;
          foreach ($detId as $key => $value){
            if ($request->Qty[$key] > 0){
              ItemTransfersDetails::create([
                  'trans_comp_id'    => $request->company_code,
                  'trans_ref_id'     => $id,
                  'trans_sou_ware_id' => $request->itm_warehouse,
                  'trans_rec_ware_id' => $request->itm_rec_warehouse,
                  'trans_storage_id' => $request->Storage[$key],
                  'trans_lot_no'     => $request->lotno[$key],
                  'trans_item_id'    => $request->ItemCodeId[$key],
                  'trans_item_unit'  => $request->Unit[$key],
                  'trans_item_qty'   => $request->Qty[$key] == ''?'0':$request->Qty[$key],
                  'trans_item_remarks' => $request->Remarks[$key],
              ]);
            }
          }
        }
    }

}
