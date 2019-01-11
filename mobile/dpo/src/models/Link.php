<?php  

namespace App\Model;
class Link extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'LINK';
	protected $primaryKey = 'LinkID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->LinkTopic = $parsedBody['LinkTopic'];
		$obj->LinkUrl = $parsedBody['LinkUrl'];	
		$obj->LinkIcon = $parsedBody['LinkIcon'];
		$obj->LinkStatus = $parsedBody['LinkStatus'];	
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		return $obj;
	}
}