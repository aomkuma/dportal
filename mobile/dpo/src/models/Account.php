<?php  

namespace App\Model;
class Account extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'ACCOUNT';
  public $timestamps = false;

  public function personRegion()
    {
        return $this->hasMany('App\Model\PersonRegion','UserID');
    }

}