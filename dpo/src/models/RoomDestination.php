<?php  

namespace App\Model;

class RoomDestination extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'DESTINATION_CONFERENCE_ROOM';
  protected $primaryKey = 'DestinationRoomID';
  
  public $timestamps = false;
  
  public function setValues($obj, $parsedBody){
        $obj->ReserveRoomID = $parsedBody['ReserveRoomID'];
        $obj->RoomID = $parsedBody['RoomID'];
        $obj->ReserveStatus = $parsedBody['ReserveStatus'];
        $obj->AdminComment = $parsedBody['AdminComment'];
        $obj->RequestBy = $parsedBody['CreateBy'];
        $obj->RequestDateTime = $parsedBody['RequestDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['RequestDateTime'];
        return $obj;
    }

}