<?php

namespace App\Models\Employees;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\SysInfos;
use App\Models\Attendance\WeekEndEntry;
use App\Models\Attendance\WeekEndEntryValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Employees extends Model
{
    protected $guarded = [];

    public function section()
    {
        return $this->belongsTo(SysInfos::class, 'emp_sec_ref_id');
    }

    public function department()
    {
        return $this->belongsTo(SysInfos::class, 'emp_dept_ref_id');
    }

    public function designation()
    {
        return $this->belongsTo(SysInfos::class, 'emp_desig_ref_id');
    }

    public function week_entry_value()
    {
        return $this->belongsTo(WeekEndEntryValue::class, 'id', 'empId');
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
