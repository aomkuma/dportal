<?php  

namespace App\Model;
class RoomReserve extends \Illuminate\Database\Eloquent\Model {  
    protected $table = 'RESERVE_ROOM';
    protected $primaryKey = 'ReserveRoomID';
    //protected $fillable = ['ReserveRoomID']; 
    /*
    protected $fillable = ['ReserveRoomID'
      ,'RoomID'
      ,'StartDateTime'
      ,'EndDateTime'
      ,'TopicConference'
      ,'SnackStatus'
      ,'Remark'
      ,'ReserveStatus'
      ,'AdminComment'
      ,'CreateBy'
      ,'CreateDateTime'
      ,'UpdateBy'
      ,'UpdateDateTime']; 
      */  
    public $timestamps = false;
    public function setValues($obj, $parsedBody){
        //$obj->ReserveRoomID = $parsedBody['ReserveRoomID'];
        $obj->RoomID = $parsedBody['RoomID'];
        $obj->StartDateTime = $parsedBody['StartDateTime'];	//$parsedBody['StartDate'] . ' ' . $parsedBody['StartTime'] . ':00.000';
        $obj->EndDateTime = $parsedBody['EndDateTime'];		//$parsedBody['EndDate'] . ' ' . $parsedBody['EndTime'] . ':00.000';
        $obj->TopicConference = $parsedBody['TopicConference'];
        $obj->SnackStatus = $parsedBody['SnackStatus'];
        $obj->Remark = $parsedBody['Remark'];
        $obj->ReserveStatus = $parsedBody['ReserveStatus'];
        $obj->AdminComment = $parsedBody['AdminComment'];
        $obj->CreateBy = $parsedBody['CreateBy'];
        $obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
        $obj->UpdateBy = $parsedBody['UpdateBy'];
        $obj->UpdateDateTime = $parsedBody['UpdateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['UpdateDateTime'];
        return $obj;
    }
}