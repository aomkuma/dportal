<?php

    namespace App\Service;
    
    use App\Model\News;
    use App\Model\NewsPicture;
    use App\Model\NewsAttachFile;
    use App\Model\NewsType;

    use Illuminate\Database\Capsule\Manager as DB;

    class NewsService {
        
        public static function getNewsFeed($RegionID){
            
            return News::where('NewsStatus','Approve')
                    ->where('ActiveStatus','Y')
                    ->where('ShowNewsFeed','Y')
                    ->where(function($query) use ($RegionID){
                        $query->where('NewsRegionID', DB::raw("'".$RegionID."'"));
                        $query->orWhere('GlobalVisible','Y');
                    })
                    ->orderBy('CreateDateTime', 'DESC')->skip(0)->take(8)->get();      
                    
             //return News::where('NewsID', DB::raw("'2'"))->get();
        }            

        public static function getNewsByID($newsID){
            return News::select("NEWS.*", "NEWS.NewsDateTime AS NewsDateTimeFormat","ACCOUNT.FirstName","ACCOUNT.LastName", "NEWS_TYPE.NewsTypeName", "REGION.RegionName")
                    ->leftJoin("ACCOUNT", "NEWS.CreateBy","=","ACCOUNT.UserID") 
                    ->leftJoin("NEWS_TYPE", 'NEWS.NewsType' , "=" , "NEWS_TYPE.NewsTypeID")
                    ->leftJoin("REGION", 'REGION.RegionID' , "=" , "NEWS.NewsRegionID")
                    ->with('pictures')
                    ->with('attachFiles')
                    ->find($newsID);  
        }    
        
        public static function getNewsList($offset,$RegionID,$HideNews,$CurrentNews,$WaitApprove){

        	$limit = 15;
			$skip = $offset * $limit;
			$total = News::count();
			//$RegionID = '2';
			$DataList = News::select("NEWS.*", "NEWS.NewsDateTime AS NewsDateTimeFormat","ACCOUNT.FirstName","ACCOUNT.LastName", "NEWS_TYPE.NewsTypeName", "REGION.RegionName")
					->leftJoin("ACCOUNT", "NEWS.CreateBy","=","ACCOUNT.UserID") 
                    ->leftJoin("NEWS_TYPE", 'NEWS.NewsType' , "=" , "NEWS_TYPE.NewsTypeID")
                    ->leftJoin("REGION", 'REGION.RegionID' , "=" , "NEWS.NewsRegionID")
                    ->where(function($query) use ($RegionID,$HideNews,$CurrentNews,$WaitApprove) {

                            if($RegionID != '0'){
                                $query->where('NewsRegionID', DB::raw("'".$RegionID."'"));
                            }if($CurrentNews == 'Y'){
                                $currentDate = date('Y-m-d H:i:s.000');

                                $query->where(function($subquery) use ($currentDate) {

                                    $subquery->where(function($subquery1) use ($currentDate) {
                                        $subquery1->where('NewsStartDateTime','<=',DB::raw("'".$currentDate."'"));
                                        $subquery1->Where('NewsEndDateTime','>=',DB::raw("'".$currentDate."'"));
                                    });

                                    $subquery->orWhere(function($subquery2) use ($currentDate) {
                                        $subquery2->where('NewsStartDateTime','<=',DB::raw("'".$currentDate."'"));
                                        $subquery2->Where('NewsEndDateTime',NULL);
                                    });

                                    $subquery->orWhere(function($subquery3) {
                                        $subquery3->where('NewsStartDateTime',NULL);
                                        $subquery3->Where('NewsEndDateTime',NULL);
                                    });

                                });

                            }if($HideNews == 'N'){
                                $query->where('ActiveStatus', DB::raw("'".$HideNews."'"));
                            }if($WaitApprove == 'Y'){
                                $query->where(function($subquery){
                                    $subquery->where('NewsStatus', NULL);
                                    $subquery->orWhere('NewsStatus', DB::raw("'Waiting'"));
                                });
                            }else{
                                $query->where(DB::raw('1'),'=',DB::raw('1'));
                            }
                            
                        })
					->skip($skip)
					->take($limit)
                    ->orderBy('NewsID', 'DESC')
                    ->with('pictures')
                    ->with('attachFiles')
					->get();

			$offset += 1;
			$continueLoad = true;
			if(ceil($total / $limit) == $offset){
				$continueLoad = false;
			}

			return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

        public static function getNewsListView($offset,$RegionID){

            $currentDate = date('Y-m-d H:i:s.000');

            $limit = 15;
            $skip = $offset * $limit;
            $total = News::count();
            //$RegionID = '2';
            $DataList = News::select("NEWS.*", "NEWS.NewsDateTime AS NewsDateTimeFormat", "NEWS_TYPE.NewsTypeName", "REGION.RegionName")
                    ->leftJoin("NEWS_TYPE", 'NEWS.NewsType' , "=" , "NEWS_TYPE.NewsTypeID")
                    ->leftJoin("REGION", 'REGION.RegionID' , "=" , "NEWS.NewsRegionID")
                    ->where("NewsStatus","Approve")
                    ->where("ActiveStatus","Y")
                    ->where(function($query) use ($RegionID, $currentDate) {

                            if($RegionID != '0'){
                                $query->where('NewsRegionID', DB::raw("'".$RegionID."'"));
                            }
                            /*
                            $query->orWhere(function($subquery) use ($currentDate) {

                                    $subquery->orWhere(function($subquery1) use ($currentDate) {
                                        $subquery1->where('NewsStartDateTime','<=',DB::raw("'".$currentDate."'"));
                                        $subquery1->Where('NewsEndDateTime','>=',DB::raw("'".$currentDate."'"));
                                    });

                                    $subquery->orWhere(function($subquery2) use ($currentDate) {
                                        $subquery2->where('NewsStartDateTime','<=',DB::raw("'".$currentDate."'"));
                                        $subquery2->Where('NewsEndDateTime',NULL);
                                    });

                                    $subquery->orWhere(function($subquery3) {
                                        $subquery3->where('NewsStartDateTime',NULL);
                                        $subquery3->Where('NewsEndDateTime',NULL);
                                    });

                                });
                            */
                        })
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy('NewsID', 'DESC')
                    ->get();

            $offset += 1;
            $continueLoad = true;
            if(ceil($total / $limit) == $offset){
                $continueLoad = false;
            }

            return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

         public static function getNewsPictureList($NewsID){
            return News::find($NewsID)->pictures;
            //return NewsPicture::where('NewsID',$NewsID)->get();
        }  

        public static function getNewsTypeList(){
            return NewsType::all();
        }       

        public static function updateData($obj){
        	if($obj['NewsID'] == ''){
                $news = new News;
            }else{
                $news = News::find($obj['NewsID']);
            }
            $news = $news->setValues($news , $obj);
            $news->save();
			return $news;
        }

        public static function updateView($NewsID){
            $news = News::find($NewsID);
            if(!empty($news)){
                $news->VisitCount = ($news->VisitCount + 1);
                return $news->save();
            }
        }

        public static function updateRequestNewsStatus($NewsID){
            $news = News::find($NewsID);
            $news->NewsStatus = 'Request';
            return $news->save();
        }

        public static function adminUpdateNewsStatus($News){
            $news = News::find($News['NewsID']);
            $news->NewsStatus = $News['NewsStatus'];
            $news->GlobalVisible = $News['GlobalVisible'];
            $news->ShowNewsFeed = $News['ShowNewsFeed'];
            $news->VerifyDate = date('Y-m-d H:i:s.000');
            return $news->save();
        }

        public static function updateNewsAdminRecv($AdminID, $NewsID){
            $news = News::find($NewsID);
            $news->VerifyBy = $AdminID;
            return $news->save();
        }

        public static function updateNewsPictureData($NewsID, $PicturePath){
            $newsPicture = new NewsPicture;
            $newsPicture->NewsID = $NewsID;
            $newsPicture->PicturePath = $PicturePath;
            $newsPicture->save();
            return $newsPicture->NewsPictureID;
        }

        public static function updateNewsAttachFile($NewsID, $AttachFileName, $AttachFilePath, $AttachFileType, $AttachFileSize, $AttachRealFileName){
            $newsAttachFile = new NewsAttachFile;
            $newsAttachFile->NewsID = $NewsID;
            $newsAttachFile->AttachFileName = $AttachFileName;
            $newsAttachFile->AttachFilePath = $AttachFilePath;
            $newsAttachFile->AttachFileType = $AttachFileType;
            $newsAttachFile->AttachFileSize = $AttachFileSize;
            $newsAttachFile->AttachRealFileName = $AttachRealFileName;
            $newsAttachFile->UploadDateTime = date('Y-m-d H:i:s.000');
            $newsAttachFile->save();
            return $newsAttachFile->AttachID;
        }

        public static function deleteData($ID){
			return News::find($ID)->delete();
        }

        public static function deleteNewsPictureData($ID){
            return NewsPicture::find($ID)->delete();
        }

        public static function deleteNewsAttachFile($ID){
            return NewsAttachFile::find($ID)->delete();
        }

        public static function searchNews($keyword){
            return News::where('NewsTitle' ,'LIKE', '%'. $keyword . '%')
                        ->where('ActiveStatus','Y')
                        ->where('NewsStatus','Approve')
                        ->orWhere('NewsContent' ,'LIKE', '%'. $keyword . '%')
                        ->get();
        }

        public static function searchNewsAttachFile($keyword){
            return NewsAttachFile::where('AttachFileName' ,'LIKE', '%'. $keyword . '%')->get();
        }

    }    

?>