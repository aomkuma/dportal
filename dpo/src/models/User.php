<?php  

namespace App\Model;
class User extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'ACCOUNT';
  protected $primaryKey = 'UserID';
  public $timestamps = false;
  
  public function setValues($user, $parsedBody){
    $user->UserID = $parsedBody['UserID'];    
    $user->GroupID = $parsedBody['GroupID'];
    $user->RegionID = $parsedBody['RegionID'];
    $user->PositionID = $parsedBody['PositionID'];
    $user->Username = $parsedBody['Username'];
    $user->Password = $parsedBody['Password'];
    $user->FirstName = $parsedBody['FirstName'];
    $user->LastName = $parsedBody['LastName'];
    $user->Email = $parsedBody['Email'];
    $user->Mobile = $parsedBody['Mobile'];
    $user->Tel = $parsedBody['Tel'];
    $user->Fax = $parsedBody['Fax'];
    $user->Picture = $parsedBody['Picture'];
    $user->CreateBy = $parsedBody['CreateBy'];
    $user->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s'):$parsedBody['CreateDateTime'];
    $user->UpdateBy = $parsedBody['UpdateBy'];
    $user->UpdateDateTime = $parsedBody['UpdateDateTime']==''?date('Y-m-d H:i:s'):$parsedBody['UpdateDateTime'];
    return $user;
  }

  public function personRegion()
    {
        return $this->hasMany('App\Model\PersonRegion','UserID');
    }
}