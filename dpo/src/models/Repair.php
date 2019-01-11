<?php  

namespace App\Model;
class Repair extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'REPAIRED';
	protected $primaryKey = 'RepairedID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->RepairedTypeID = $parsedBody['RepairedTypeID'];
		$obj->RepairedTitleID = $parsedBody['RepairedTitleID'];
		$obj->RepairedIssueID = $parsedBody['RepairedIssueID'];
		$obj->RepairedSubIssueID = $parsedBody['RepairedSubIssueID'];
		$obj->RegionID = $parsedBody['RegionID'];
		$obj->RepairedCode = $parsedBody['RepairedCode'];
		$obj->RepairedDetail = $parsedBody['RepairedDetail'];
		$obj->RepairedStatus = $parsedBody['RepairedStatus'];
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