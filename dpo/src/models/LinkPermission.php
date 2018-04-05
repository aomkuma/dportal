<?php  

namespace App\Model;
class LinkPermission extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'LINK_PERMISSION';
	protected $primaryKey = 'LinkPermissionID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->LinkID = $parsedBody['LinkID'];
		$obj->UserID = $parsedBody['UserID'];	
		$obj->LinkStatus = $parsedBody['LinkStatus'];	
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		return $obj;
	}
}