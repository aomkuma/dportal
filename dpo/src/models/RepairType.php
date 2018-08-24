<?php  

namespace App\Model;
class RepairType extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'REPAIRED_TYPE';
	protected $primaryKey = 'RepairedTypeID';
	public $timestamps = false;
	public function setValues($obj, $parsedBody){
		$obj->RepairedTypeName = $parsedBody['RepairedTypeName'];
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}

	public function repairTitle()
    {
        return $this->hasMany('App\Model\RepairTitle','RepairedTypeID');
    }
}