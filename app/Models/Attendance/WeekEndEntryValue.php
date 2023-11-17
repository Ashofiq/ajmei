<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\SysInfos;
use App\Models\Employees\Employees;

class WeekEndEntryValue extends Model
{
    use HasFactory;

    public function department()
    {
        return $this->belongsTo(SysInfos::class, 'departmentId');
    }

    public function section()
    {
        return $this->belongsTo(SysInfos::class, 'sectionId');
    }

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'empId');
    }

    public function unit()
    {
        return $this->belongsTo(SysInfos::class, 'unitId');
    }
}
