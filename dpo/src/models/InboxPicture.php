<?php  

namespace App\Model;
class InboxPicture extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'INBOX_PICTURE';
  protected $primaryKey = 'InboxPictureID';
  public $timestamps = false;
  public function setValues($obj, $parsedBody){
    $obj->InboxID = $parsedBody['InboxID'];
    $obj->PicturePath = $parsedBody['PicturePath'];
    return $obj;
}

}