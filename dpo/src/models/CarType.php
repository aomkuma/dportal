<?php  

namespace App\Model;
class Cartype extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'CAR_TYPE';
  	protected $primaryKey = 'CarTypeID';
  	public $timestamps = false;
    public function setValues($obj, $parsedBody){
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];	
		$obj->CarType = $parsedBody['CarType'];	
		$obj->SeatAmount = intval($parsedBody['SeatAmount']);	
		
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}
}