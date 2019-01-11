<?php  

	namespace App\Model;
	class Traveller extends \Illuminate\Database\Eloquent\Model {  
		protected $table = 'TRAVELLER';
		protected $primaryKey = 'TravellerID';
		public $timestamps = false;

		public function setValues($obj, $parsedBody){
			$obj->ReserveCarID = $parsedBody['ReserveCarID'];	
			$obj->UserID = $parsedBody['UserID'];	
			$obj->CreateBy = $parsedBody['CreateBy'];
			$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
			return $obj;
		}
	}

?>