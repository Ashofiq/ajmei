<?php

namespace App\Models\Sales;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Models\Sales\SalesOrdersDetails;
use App\Models\Customers\Customers;

class SalesOrders extends Model
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
                    // 'deleted_at' => Carbon::now(),
                ]);
            });
        }
    }


    public function items()
    {
        return $this->hasMany(SalesOrdersDetails::class, 'so_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'so_cust_id');
    }

}
