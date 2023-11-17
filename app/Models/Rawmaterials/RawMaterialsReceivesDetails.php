<?php

namespace App\Models\Rawmaterials;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Models\Rawmaterials\RawMaterialsReceives;
use App\Models\Items\Items;

class RawMaterialsReceivesDetails extends Model
{
    protected $guarded = [];

    public function rawmetarial(){
        return $this->belongsTo(RawMaterialsReceives::class, 'raw_order_id', 'id');
    }

    public function item(){
        return $this->belongsTo(Items::class, 'raw_item_id', 'id');
    }

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




}
