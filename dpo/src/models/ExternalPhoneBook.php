<?php  

namespace App\Model;
class ExternalPhoneBook extends \Illuminate\Database\Eloquent\Model {  
	protected $table = 'PHONEBOOK';
	protected $primaryKey = 'PhoneBookID';
	public $timestamps = false;
	
	public function setValues($obj, $parsedBody){
		$obj->FirstName = $parsedBody['FirstName'];
	    $obj->LastName = $parsedBody['LastName'];
	    $obj->CompanyName = $parsedBody['CompanyName'];
	    $obj->Tel = $parsedBody['Tel'];
	    $obj->Fax = $parsedBody['Fax'];
	    $obj->Mobile = $parsedBody['Mobile'];
	    $obj->Email = $parsedBody['Email'];
	    $obj->Picture = $parsedBody['Picture'];
	    $obj->ActiveStatus = $parsedBody['ActiveStatus'];
	    $obj->CreateBy = $parsedBody['CreateBy'];
        $obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
        $obj->UpdateBy = $parsedBody['UpdateBy'];
        $obj->UpdateDateTime = $parsedBody['UpdateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['UpdateDateTime'];
	    return $obj;
	}
}