<?php  

namespace App\Model;
class RepairTitle extends \Illuminate\Database\Eloquent\Model {  

	protected $table = 'REPAIRED_TITLE';
	protected $primaryKey = 'RepairedTitleID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->RepairedTypeID = $parsedBody['RepairedTypeID'];
		$obj->RepairedTitleName = $parsedBody['RepairedTitleName'];
		$obj->RepairedTitleCode = $parsedBody['RepairedTitleCode'];
		$obj->DepartmentID = $parsedBody['DepartmentID'];
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}
	public function repairIssue()
    {
        return $this->hasMany('App\Model\RepairIssue','RepairedTitleID');
    }

}

?>
