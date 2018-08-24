<?php  

namespace App\Model;
class ExternalDriver extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'EXTERNAL_DRIVER';
  	protected $primaryKey = 'ExternalDriverID';
  	public $timestamps = false;
    public function setValues($obj, $parsedBody){
		$obj->ReserveCarID = $parsedBody['ReserveCarID'];	
		$obj->DriverName = $parsedBody['DriverName'];	
		$obj->Mobile = $parsedBody['Mobile'];	
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}
}