<?php  

namespace App\Model;
class Room extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'ROOM';
	protected $primaryKey = 'RoomID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];
		$obj->ConferenceType = $parsedBody['ConferenceType'];
		$obj->DeviceAdminID = $parsedBody['DeviceAdminID'];	
		$obj->RegionID = $parsedBody['RegionID'];	
		$obj->FoodAdminID = $parsedBody['FoodAdminID'];	
		$obj->RoomAdminID = $parsedBody['RoomAdminID'];	
		$obj->RoomDescription = $parsedBody['RoomDescription'];	
		$obj->RoomName = $parsedBody['RoomName'];	
		$obj->RoomPicture = $parsedBody['RoomPicture'];
		$obj->SeatAmount = $parsedBody['SeatAmount'];	
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}

}