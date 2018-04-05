<?php  

namespace App\Model;
class NewsPicture extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'NEWS_PICTURE';
  protected $primaryKey = 'NewsPictureID';
  public $timestamps = false;
  public function setValues($obj, $parsedBody){
    $obj->NewsID = $parsedBody['NewsID'];
    $obj->PicturePath = $parsedBody['PicturePath'];
    return $obj;
}

}