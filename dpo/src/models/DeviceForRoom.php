<?php  

namespace App\Model;
class DeviceForRoom extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'RESERVE_ROOM_DEVICE';
  public $timestamps = false;

	public function setValues($obj, $parsedBody){
	  $obj->ReserveRoomID = $parsedBody['ReserveRoomID'];
	  $obj->DeviceID = $parsedBody['DeviceID'];
	  $obj->Amount = $parsedBody['Amount'];
	  $obj->CreateBy = $parsedBody['CreateBy'];
      $obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
	  return $obj;
  }
}