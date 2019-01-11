<?php  

namespace App\Model;
class RepairSubIssue extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'REPAIRED_SUB_ISSUE';
	protected $primaryKey = 'RepairedSubIssueID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->RepairedIssueID = $parsedBody['RepairedIssueID'];
		$obj->RepairedSubIssueName = $parsedBody['RepairedSubIssueName'];
		$obj->SLAHour = $parsedBody['SLAHour'];
		$obj->SLAMinute = $parsedBody['SLAMinute'];
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}
}