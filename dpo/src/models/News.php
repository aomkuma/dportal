<?php  

namespace App\Model;
class News extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'NEWS';
  protected $primaryKey = 'NewsID';
  protected $pictureList = [];
  public $timestamps = false;
  public function setValues($obj, $parsedBody){
    $obj->NewsTitle = $parsedBody['NewsTitle'];
    $obj->NewsContent = $parsedBody['NewsContent'];
    $obj->NewsPicture = $parsedBody['NewsPicture'];	
    //$obj->VerifyBy = $parsedBody['VerifyBy'];  
    //$obj->VerifyDate = $parsedBody['VerifyDate'];  
    $obj->NewsStatus = $parsedBody['NewsStatus'];  
    $obj->NewsRegionID = $parsedBody['NewsRegionID'];  
    $obj->NewsType = $parsedBody['NewsType'];  
    $obj->GlobalNews = $parsedBody['GlobalNews'];  
    $obj->LimitDisplay = $parsedBody['LimitDisplay'];  
    $obj->NewsDateTime = $parsedBody['NewsDateTime'];  
    $obj->NewsStartDateTime = $parsedBody['NewsStartDateTime']==null?NULL:$parsedBody['NewsStartDateTime']; 
    $obj->NewsEndDateTime = $parsedBody['NewsEndDateTime']==null?NULL:$parsedBody['NewsEndDateTime'];  
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
        return $this->hasMany('App\Model\NewsPicture','NewsID');
    }

    public function attachFiles()
    {
        return $this->hasMany('App\Model\NewsAttachFile','NewsID');
    }

}