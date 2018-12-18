<?php  

namespace App\Model;
class InternalDriver extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'INTERNAL_DRIVER';
  	protected $primaryKey = 'InternalDriverID';
  	public $timestamps = false;
    public function setValues($obj, $parsedBody){
		$obj->ReserveCarID = $parsedBody['ReserveCarID'];	
		$obj->UserID = $parsedBody['UserID'];	
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		return $obj;
	}
}