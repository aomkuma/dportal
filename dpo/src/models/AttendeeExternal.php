<?php  

namespace App\Model;
class AttendeeExternal extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'ATTENDEE_EXTERNAL';
  public $timestamps = false;
  
  public function setValues($obj, $parsedBody){
	  $obj->ReserveRoomID = $parsedBody['ReserveRoomID'];
	  $obj->AttendeeName = $parsedBody['AttendeeName'];
	  $obj->Email = $parsedBody['Email'];
	  $obj->Mobile = $parsedBody['Mobile'];
	  $obj->CreateBy = $parsedBody['CreateBy'];
      $obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
	  return $obj;
  }

}