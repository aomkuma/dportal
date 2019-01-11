<?php  

namespace App\Model;
class FoodForRoom extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'RESERVE_ROOM_FOOD';
  public $timestamps = false;
  
  public function setValues($obj, $parsedBody){
	  $obj->ReserveRoomID = $parsedBody['ReserveRoomID'];
	  $obj->FoodID = $parsedBody['FoodID'];
	  $obj->Amount = $parsedBody['Amount'];
	  $obj->CreateBy = $parsedBody['CreateBy'];
      $obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
	  return $obj;
  }

}