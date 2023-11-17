<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectSales extends Model
{
    use HasFactory;

    protected $table = 'sales_order_direct';
}
