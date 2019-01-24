<?php  

namespace App\Model;
class InboxAttachFile extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'INBOX_ATTACH_FILE';
  protected $primaryKey = 'AttachID';
  public $timestamps = false;
  public function setValues($obj, $parsedBody){
    $obj->InboxID = $parsedBody['InboxID'];
    $obj->AttachFileName = $parsedBody['AttachFileName'];
    $obj->AttachFileType = $parsedBody['AttachFileType'];	
    $obj->UploadDateTime = $parsedBody['UploadDateTime']==''?date('Y-m-d H:i:s.000'):$parsedBody['UploadDateTime'];
    return $obj;
  }

}