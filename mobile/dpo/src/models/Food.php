<?php  

namespace App\Model;
class Food extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'FOOD';
  protected $primaryKey = 'FoodID';
  public $timestamps = false;
  public function setValues($obj, $parsedBody){
		$obj->ActiveStatus = $parsedBody['ActiveStatus'];	
		$obj->RegionID = $parsedBody['RegionID'];	
		$obj->FoodDescription = $parsedBody['FoodDescription'];	
		$obj->FoodName = $parsedBody['FoodName'];	
		$obj->FoodPicture = $parsedBody['FoodPicture'];	
		$obj->CreateBy = $parsedBody['CreateBy'];
		$obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
		$obj->UpdateBy = $parsedBody['UpdateBy'];
		$obj->UpdateDateTime = date('Y-m-d H:i:s.000');
		return $obj;
	}
}