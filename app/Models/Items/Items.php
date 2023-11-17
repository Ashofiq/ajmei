<?php

namespace App\Models\Items;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Items extends Model
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

    public function makeItemCode($compid)
    {
      $length = strlen($compid)+1;
      $maxCode = items::where('item_ref_comp_id', '=', $compid)
      ->selectRaw('max(substring(item_code,'.$length.')) as MaxCode')->first()->MaxCode;
      $itm_code = $maxCode?$maxCode+1:1;
      if ($itm_code == 1) {
          $itm_code = $compid .'1'. sprintf("%04d", 1);
      }else{
          $itm_code = $compid.$itm_code;
      }
      return $itm_code;
    }

    public function makeitemqrcode($compid)
    {
      $length = strlen($compid)+2;
      $maxCode = items::where('item_ref_comp_id', '=', $compid)
      ->selectRaw('max(substring(item_qr_code,'.$length.')) as MaxCode')->first()->MaxCode;
      $itm_code = $maxCode?$maxCode+1:1;
      if ($itm_code == 1) {
          $itm_code = '9'.$compid.'1'.sprintf("%07d", 1);
      }else{
          $itm_code = '9'.$compid.$itm_code;
      }
      return $itm_code;
    }

    public function makeitembarcode($compid)
    {
      $length = strlen($compid)+2;
      $maxCode = items::where('item_ref_comp_id', '=', $compid)
      ->selectRaw('max(substring(item_bar_code,'.$length.')) as MaxCode')->first()->MaxCode;
      $itm_code = $maxCode?$maxCode+1:1;
      if ($itm_code == 1) {
          $itm_code = '2'.$compid.'1'.sprintf("%07d", 1);
      }else{
          $itm_code = '2'.$compid.$itm_code;
      }
      return $itm_code;
    }

}
