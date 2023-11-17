<?php

namespace App\Models\Items;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class View_Items_Tree extends Model
{
     protected $table = 'view_items_tree';

     /**
      * Get the index name for the model.
      *
      * @return string
     */
     public function childs() {
         return $this->hasMany('App\Models\Items\View_Items_Tree','parent_id','id') ;
     }
}
