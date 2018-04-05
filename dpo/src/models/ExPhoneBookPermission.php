<?php  

namespace App\Model;
class ExPhoneBookPermission extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'EXPHONEBOOK_PERMISSION';
	protected $primaryKey = 'ExPhoneBookPermissionID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->PermissionType = $parsedBody['UpdateType'];
		$obj->GroupID = $parsedBody['GroupID'];	
		$obj->ExPhoneBookID = $parsedBody['PhoneBookID'];
		$obj->AllowStatus = $parsedBody['AllowStatus'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = $parsedBody['UpdateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['UpdateDateTime'];
		return $obj;
	}
}