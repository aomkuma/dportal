<?php  

namespace App\Model;
class Inbox extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'INBOX';
  protected $primaryKey = 'InboxID';
  protected $pictureList = [];
  public $timestamps = false;
  public function setValues($obj, $parsedBody){
    $obj->InboxTitle = $parsedBody['InboxTitle'];
    $obj->InboxContent = $parsedBody['InboxContent'];
    $obj->InboxPicture = $parsedBody['InboxPicture'];	
    //$obj->VerifyBy = $parsedBody['VerifyBy'];  
    //$obj->VerifyDate = $parsedBody['VerifyDate'];  
    $obj->InboxStatus = $parsedBody['InboxStatus'];  
    $obj->InboxRegionID = $parsedBody['InboxRegionID'];  
    $obj->InboxType = $parsedBody['InboxType'];  
    $obj->GlobalInbox = $parsedBody['GlobalInbox'];  
    $obj->LimitDisplay = $parsedBody['LimitDisplay'];  
    $obj->InboxDateTime = $parsedBody['InboxDateTime'];  
    $obj->InboxStartDateTime = $parsedBody['InboxStartDateTime']==null?NULL:$parsedBody['InboxStartDateTime']; 
    $obj->InboxEndDateTime = $parsedBody['InboxEndDateTime']==null?NULL:$parsedBody['InboxEndDateTime'];  
    $obj->ActiveStatus = $parsedBody['ActiveStatus'];  
    $obj->CreateBy = $parsedBody['CreateBy'];
    $obj->CreateDateTime = $parsedBody['CreateDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['CreateDateTime'];
    $obj->UpdateBy = $parsedBody['UpdateBy'];
    $obj->UpdateDateTime = date('Y-m-d H:i:s.000');
    return $obj;
  }

  public function addPictureList ($pictureList){
    $this->pictureList = $pictureList;
  }

    public function pictures()
    {
        return $this->hasMany('App\Model\InboxPicture','InboxID');
    }

    public function attachFiles()
    {
        return $this->hasMany('App\Model\InboxAttachFile','InboxID');
    }

}