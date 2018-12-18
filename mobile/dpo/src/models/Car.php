<?php  

namespace App\Model;
class Car extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'CAR';
  	protected $primaryKey = 'CarID';
  	public $timestamps = false;
    public function setValues($obj, $parsedBody){
      $obj->RegionID = $parsedBody['RegionID'];		
      $obj->CarTypeID = $parsedBody['CarTypeID'];
      $obj->CarAdminID = $parsedBody['CarAdminID'];
      $obj->Brand = $parsedBody['Brand'];
      $obj->Model = $parsedBody['Model'];
      $obj->License = $parsedBody['License'];
      $obj->LicenceProvince = $parsedBody['LicenceProvince'];
      $obj->CarPicture = $parsedBody['CarPicture'];
      $obj->Description = $parsedBody['Description'];
      $obj->ActiveStatus = $parsedBody['ActiveStatus'];
      $obj->CreateBy = $parsedBody['CreateBy'];
      $obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
      $obj->UpdateBy = $parsedBody['UpdateBy'];
      $obj->UpdateDateTime = date('Y-m-d H:i:s.000');
      return $obj;
  	}

    public function reserveCars()
    {
        return $this->hasMany('App\Model\CarReserve','CarID');
    }
}