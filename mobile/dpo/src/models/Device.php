<?php  

namespace App\Model;
class Device extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'DEVICE';
  	protected $primaryKey = 'DeviceID';
  	public $timestamps = false;
    public function setValues($obj, $parsedBody){
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];	
		$obj->RegionID = $parsedBody['RegionID'];	
		$obj->DeviceDescription = $parsedBody['DeviceDescription'];	
		$obj->DeviceName = $parsedBody['DeviceName'];	
		$obj->DevicePicture = $parsedBody['DevicePicture'];	
		$obj->DeviceAmount = round(intval($parsedBody['DeviceAmount']));	
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}
}