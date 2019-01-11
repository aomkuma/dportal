<?php  

namespace App\Model;
class Calendar extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'CALENDAR';
  protected $primaryKey = 'CalendarID';
  public $timestamps = false;
  public function setValues($obj, $parsedBody){
		$obj->CalendarName = $parsedBody['CalendarName'];	
		$obj->CalendarUrl = $parsedBody['CalendarUrl'];	
		$obj->IsFirstPage = $parsedBody['IsFirstPage'];	
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];	
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}
}