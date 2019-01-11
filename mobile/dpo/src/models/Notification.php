<?php  

namespace App\Model;
class Notification extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'NOTIFICATION';
  protected $primaryKey = 'NotificationID';
  public $timestamps = false;
  public function setValues($obj, $parsedBody){
        $obj->RegionID = $parsedBody['RegionID'];
        $obj->NotificationType = $parsedBody['NotificationType'];
        $obj->NotificationUrl = $parsedBody['NotificationUrl'];	
        $obj->NotificationText = $parsedBody['NotificationText'];
        $obj->NotificationKeyID = $parsedBody['NotificationKeyID'];
        $obj->NotificationStatus = $parsedBody['NotificationStatus'];
        $obj->PushBy = $parsedBody['PushBy'];
        $obj->PushDateTime = $parsedBody['PushDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['PushDateTime'];
        $obj->AdminGroup = $parsedBody['AdminGroup'];
        $obj->ToSpecificPersonID = $parsedBody['ToSpecificPersonID'];
        if($parsedBody['PullBy'] != '' || $parsedBody['PullBy'] != null){
            $obj->PullBy = $parsedBody['PullBy'];
            $obj->PullDateTime = $parsedBody['PullDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['PullDateTime'];
        }
        return $obj;
    }
}