<?php

    namespace App\Service;
    
    use App\Model\Inbox;
    use App\Model\InboxSeen;
    use App\Model\InboxNotification;
    use App\Model\InboxPicture;
    use App\Model\InboxAttachFile;
    use App\Model\InboxType;
    use App\Model\Group;

    use Illuminate\Database\Capsule\Manager as DB;

    class InboxService {

        public static function findChildOrg($OrgID){
            
            return Group::where('UpperOrgID', $OrgID)
                    ->get();      
                    
             //return Inbox::where('InboxID', DB::raw("'2'"))->get();
        }    
        
        public static function getInboxFeed($RegionID){
            
            return Inbox::where('InboxStatus','Approve')
                    ->where('ActiveStatus','Y')
                    ->where('ShowInboxFeed','Y')
                    ->where(function($query) use ($RegionID){
                        $query->where('InboxRegionID', DB::raw("'".$RegionID."'"));
                        $query->orWhere('GlobalVisible','Y');
                    })
                    ->orderBy('CreateDateTime', 'DESC')->skip(0)->take(8)->get();      
                    
             //return Inbox::where('InboxID', DB::raw("'2'"))->get();
        }            

        public static function getInboxByID($inboxID){
            return Inbox::
                    with('pictures')
                    ->with('attachFiles')
                    ->find($inboxID);  
        }  

        public static function getInboxList($offset, $UserID, $OrgID){

            $limit = 15;
            $skip = $offset * $limit;
            $total = Inbox::join("INBOX_NOTIFICATION", 'INBOX_NOTIFICATION.inbox_id', '=', 'INBOX.InboxID')
                            ->where('INBOX_NOTIFICATION.org_id', $OrgID)
                            ->orWhere('INBOX_NOTIFICATION.user_id', $UserID)
                            ->groupBy('InboxID')
                            ->count();

            $DataList = Inbox::join("INBOX_NOTIFICATION", 'INBOX_NOTIFICATION.inbox_id', '=', 'INBOX.InboxID')
                            ->where('INBOX_NOTIFICATION.org_id', $OrgID)
                            ->orWhere('INBOX_NOTIFICATION.user_id', $UserID)
                            ->orWhere('INBOX_NOTIFICATION.notification_type','all')
                            ->skip($skip)
                            ->take($limit)
                            ->groupBy('InboxID')
                            ->orderBy('InboxID', 'DESC')
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
        
        public static function getInboxListManage($offset,$condition){

        	$limit = 15;
			$skip = $offset * $limit;
			$total = Inbox::where(function($query) use ($condition) {

                            if(!empty($condition['keyword'])){
                                // $query->where('InboxRegionID', DB::raw("'".$RegionID."'"));
                                $query->where('InboxTitle', 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                            }
                        })
            ->count();

			$DataList = Inbox::where(function($query) use ($condition) {

                            if(!empty($condition['keyword'])){
                                // $query->where('InboxRegionID', DB::raw("'".$RegionID."'"));
                                $query->where('InboxTitle', 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                            }
                        })
					->skip($skip)
					->take($limit)
                    ->orderBy('InboxID', 'DESC')
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

        public static function getInboxListView($offset,$RegionID,$GlobalInbox){

            $currentDate = date('Y-m-d H:i:s.000');

            $limit = 15;
            $skip = $offset * $limit;
            $total = Inbox::count();
            //$RegionID = '2';
            $DataList = Inbox::select("INBOX.*", "INBOX.InboxDateTime AS InboxDateTimeFormat", "INBOX_TYPE.InboxTypeName", "REGION.RegionName")
                    ->leftJoin("INBOX_TYPE", 'INBOX.InboxType' , "=" , "INBOX_TYPE.InboxTypeID")
                    ->leftJoin("REGION", 'REGION.RegionID' , "=" , "INBOX.InboxRegionID")
                    ->where("InboxStatus","Approve")
                    ->where("ActiveStatus","Y")
                    ->where(function($query) use ($RegionID,$GlobalInbox, $currentDate) {

                            if($RegionID != '0'){
                                $query->where('InboxRegionID', DB::raw("'".$RegionID."'"));
                            }if($GlobalInbox != '-'){
                                $query->where('GlobalInbox', $GlobalInbox);
                            }
                            /*
                            $query->orWhere(function($subquery) use ($currentDate) {

                                    $subquery->orWhere(function($subquery1) use ($currentDate) {
                                        $subquery1->where('InboxStartDateTime','<=',DB::raw("'".$currentDate."'"));
                                        $subquery1->Where('InboxEndDateTime','>=',DB::raw("'".$currentDate."'"));
                                    });

                                    $subquery->orWhere(function($subquery2) use ($currentDate) {
                                        $subquery2->where('InboxStartDateTime','<=',DB::raw("'".$currentDate."'"));
                                        $subquery2->Where('InboxEndDateTime',NULL);
                                    });

                                    $subquery->orWhere(function($subquery3) {
                                        $subquery3->where('InboxStartDateTime',NULL);
                                        $subquery3->Where('InboxEndDateTime',NULL);
                                    });

                                });
                            */
                        })
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy('InboxID', 'DESC')
                    ->get();

            $offset += 1;
            $continueLoad = true;
            if(ceil($total / $limit) == $offset){
                $continueLoad = false;
            }

            return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

        public static function getInboxPictureList($InboxID){
            return Inbox::find($InboxID)->pictures;
            //return InboxPicture::where('InboxID',$InboxID)->get();
        }  

        public static function getInboxTypeList(){
            return InboxType::all();
        }       

        public static function updateData($obj){
        	if($obj['InboxID'] == ''){
                $inbox = new Inbox;
            }else{
                $inbox = Inbox::find($obj['InboxID']);
            }
            $inbox = $inbox->setValues($inbox , $obj);
            $inbox->save();
			return $inbox;
        }

        public static function pushNotification($InboxID, $obj){
            
            $model = new InboxNotification;
            
            $model->inbox_id = $InboxID;
            $model->user_id = $obj['user_id'];
            $model->org_id = $obj['org_id'];
            $model->notification_type = $obj['notification_type'];
            return $model->save();
            
        }

        public static function updateView($InboxID){
            $inbox = Inbox::find($InboxID);
            if(!empty($inbox)){
                $inbox->VisitCount = ($inbox->VisitCount + 1);
                return $inbox->save();
            }
        }

        public static function updateRequestInboxStatus($InboxID){
            $inbox = Inbox::find($InboxID);
            $inbox->InboxStatus = 'Request';
            return $inbox->save();
        }

        public static function adminUpdateInboxStatus($Inbox){
            $inbox = Inbox::find($Inbox['InboxID']);
            $inbox->InboxStatus = $Inbox['InboxStatus'];
            $inbox->GlobalVisible = $Inbox['GlobalVisible'];
            $inbox->ShowInboxFeed = $Inbox['ShowInboxFeed'];
            $inbox->VerifyDate = date('Y-m-d H:i:s.000');
            return $inbox->save();
        }

        public static function updateInboxAdminRecv($AdminID, $InboxID){
            $inbox = Inbox::find($InboxID);
            $inbox->VerifyBy = $AdminID;
            return $inbox->save();
        }

        public static function updateInboxPictureData($InboxID, $PicturePath){
            $inboxPicture = new InboxPicture;
            $inboxPicture->InboxID = $InboxID;
            $inboxPicture->PicturePath = $PicturePath;
            $inboxPicture->save();
            return $inboxPicture->InboxPictureID;
        }

        public static function updateInboxAttachFile($InboxID, $AttachFileName, $AttachFilePath, $AttachFileType, $AttachFileSize, $AttachRealFileName){
            $inboxAttachFile = new InboxAttachFile;
            $inboxAttachFile->InboxID = $InboxID;
            $inboxAttachFile->AttachFileName = $AttachFileName;
            $inboxAttachFile->AttachFilePath = $AttachFilePath;
            $inboxAttachFile->AttachFileType = $AttachFileType;
            $inboxAttachFile->AttachFileSize = $AttachFileSize;
            $inboxAttachFile->AttachRealFileName = $AttachRealFileName;
            $inboxAttachFile->UploadDateTime = date('Y-m-d H:i:s.000');
            $inboxAttachFile->save();
            return $inboxAttachFile->AttachID;
        }

        public static function deleteData($ID){
			return Inbox::find($ID)->delete();
        }

        public static function deleteInboxPictureData($ID){
            return InboxPicture::find($ID)->delete();
        }

        public static function deleteInboxAttachFile($ID){
            return InboxAttachFile::find($ID)->delete();
        }

        public static function searchInbox($keyword){
            return Inbox::where('InboxTitle' ,'LIKE', '%'. $keyword . '%')
                        ->where('ActiveStatus','Y')
                        ->where('InboxStatus','Approve')
                        ->orWhere('InboxContent' ,'LIKE', '%'. $keyword . '%')
                        ->get();
        }

        public static function searchInboxAttachFile($keyword){
            return InboxAttachFile::where('AttachFileName' ,'LIKE', '%'. $keyword . '%')->get();
        }

        public static function updateSeen($InboxID, $UserID){
            $model = InboxSeen::where('inbox_id', $InboxID)->where('user_id', $UserID)->first();
            if(empty($model)){
                $model = new InboxSeen;
                $model->inbox_id = $InboxID;
                $model->user_id = $UserID;
                $model->seen_datetime = date('Y-m-d H:i:s');
                $model->save();
            }
        }

        public static function countUnseen($UserID, $OrgID){
            $TotalInboxNotification = InboxNotification::where('INBOX_NOTIFICATION.org_id', $OrgID)
                            ->orWhere('INBOX_NOTIFICATION.user_id', $UserID)
                            ->orWhere('INBOX_NOTIFICATION.notification_type','all')
                            ->groupBy('inbox_id')
                            ->get();

            $TotalInboxSeen = InboxSeen::where('user_id', $UserID)->count();
            return count($TotalInboxNotification) - $TotalInboxSeen;
        }
    }    

?>