<?php  

	namespace App\Model;
	class CarReserve extends \Illuminate\Database\Eloquent\Model {  
		protected $table = 'RESERVE_CAR';
		protected $primaryKey = 'ReserveCarID';
		public $timestamps = false;

		public function setValues($obj, $parsedBody){
			$obj->RegionID = $parsedBody['RegionID'];
			$obj->ProvinceID = $parsedBody['ProvinceID'];
			$obj->CarID = $parsedBody['CarID'];
			$obj->StartDateTime = $parsedBody['StartDateTime'];
			$obj->EndDateTime = $parsedBody['EndDateTime'];
			$obj->Destination = $parsedBody['Destination'];
			$obj->Mission = $parsedBody['Mission'];
			$obj->TravelerAmount = round(intval($parsedBody['TravelerAmount']));
			$obj->DriverType = $parsedBody['DriverType'];
			$obj->Remark = $parsedBody['Remark'];
			$obj->CreateBy = $parsedBody['CreateBy'];
			$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
			$obj->CreateBy = $parsedBody['CreateBy'];
			$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
			$obj->UpdateBy = $parsedBody['UpdateBy'];
        	$obj->UpdateDateTime = $parsedBody['UpdateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['UpdateDateTime'];
			return $obj;
		}

	}

?>