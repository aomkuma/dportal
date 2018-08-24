<?php  

namespace App\Model;
class Attendee extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'ATTENDEE';
  public $timestamps = false;
  
   public function setValues($obj, $parsedBody){
	   $obj->UserID = $parsedBody['UserID'];	   
		$obj->ReserveRoomID = $parsedBody['ReserveRoomID'];	   
		$obj->CreateBy = $parsedBody['CreateBy'];
        $obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		return $obj;
   }

}