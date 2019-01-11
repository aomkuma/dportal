<?php  

namespace App\Model;
class RepairIssue extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'REPAIRED_ISSUE';
	protected $primaryKey = 'RepairedIssueID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->RepairedTitleID = $parsedBody['RepairedTitleID'];
		$obj->RepairedIssueName = $parsedBody['RepairedIssueName'];
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}
	public function repairSubIssue()
    {
        return $this->hasMany('App\Model\RepairSubIssue','RepairedIssueID');
    }
}

?>