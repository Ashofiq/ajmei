<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;



class Category extends Model
{


  public $fillable = ['title','parent_id'];


  /**
   * Get the index name for the model.
   *
   * @return string
  */
  public function childs() {
      return $this->hasMany('App\Models\Category','parent_id','id') ;
  }
}
