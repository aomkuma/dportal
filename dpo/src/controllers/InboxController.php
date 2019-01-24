<?php

    namespace App\Controller;
    
    use App\Model\Inbox;
    use App\Service\InboxService;
    use App\Service\UserService;
    use App\Service\NotificationService;
    use App\Service\PermissionService;
    
    class InboxController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        public function getInboxList($request, $response, $args){
            
            try{
                $parsedBody = $request->getParsedBody();
                $offset = $parsedBody['offset'];
                $user_session =  $parsedBody['user_session'];

                $UserID = $user_session['UserID'];
                $OrgID = $user_session['OrgID'];

                $InboxList = InboxService::getInboxList($offset, $UserID, $OrgID);
                $this->data_result['DATA'] = $InboxList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
        
        public function getInboxListManage($request, $response, $args){
            
            try{
                $parsedBody = $request->getParsedBody();
                $offset = $parsedBody['offset'];
                $condition =  $parsedBody['offset'];

                $InboxList = InboxService::getInboxListManage($offset, $condition);
                $this->data_result['DATA'] = $InboxList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getInboxListView($request, $response, $args){
            
            try{

                $offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT);
                $RegionID = filter_var($request->getAttribute('RegionID'), FILTER_SANITIZE_NUMBER_INT);
                $GlobalInbox = $request->getAttribute('GlobalInbox');
                $InboxList = InboxService::getInboxListView($offset,$RegionID,$GlobalInbox);
                $this->data_result['DATA'] = $InboxList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getInboxTypeList($request, $response, $args){
            
            try{

                $InboxTypeList = InboxService::getInboxTypeList();
                $this->data_result['DATA'] = $InboxTypeList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateData($request, $response, $args){
            //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{

                $parsedBody = $request->getParsedBody();
                $parsedBody = $parsedBody['updateObj'];

                $files = $request->getUploadedFiles();
                $attachFileList = $files['attachFile'];
                $img = $files['img'];
                
                if($img != null){
                    if($img->getClientFilename() != ''){
                        $ext = pathinfo($img->getClientFilename(), PATHINFO_EXTENSION);
                        $newFileName = date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                        // $newFileName = date('YmdHis').'_'.$img->getClientFilename();
                        $parsedBody['InboxPicture'] = 'img/inbox/'.$newFileName;
                        $img->moveTo('../../img/inbox/'.$newFileName);
                        
                    }
                }

                // Update 21/12/2018
                $parsedBody['InboxStatus'] = 'Approve';
                $Inbox = InboxService::updateData($parsedBody);

                // Insert new picture list
                $files = $files['fileimg'];
                
                if($files != null){
                    foreach($files as $key => $val){
                        if($val != null){
                            if($val['fileimg']->getClientFilename() != ''){
                                $ext = pathinfo($val['fileimg']->getClientFilename(), PATHINFO_EXTENSION);
                                $newFileName = $Inbox->InboxID . '_' . date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                                //$newFileName = $Inbox->InboxID . '_' . date('YmdHis').'_'.$val['fileimg']->getClientFilename();
                                $PicturePath = 'img/inbox/'.$newFileName;
                                InboxService::updateInboxPictureData($Inbox->InboxID, $PicturePath);
                                // if(InboxService::updateInboxPictureData($Inbox->InboxID, $PicturePath) > 0){
                                    $val['fileimg']->moveTo('../../img/inbox/'.$newFileName);
                                // }
                            }
                        }
                    }
                }

                // Insert new attach file
                if($attachFileList != null){
                    foreach($attachFileList as $key => $val){
                        if($val != null){
                            if($val['attachFile']->getClientFilename() != ''){
                                $ext = pathinfo($val['attachFile']->getClientFilename(), PATHINFO_EXTENSION);
                                $newFileName = $Inbox->InboxID . '_' .  date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                                $AttachRealFileName =  $Inbox->InboxID . '_' . date('YmdHis').'_'.$val['attachFile']->getClientFilename();
                                $AttachPath = 'uploads/inbox/'.$newFileName;
                                $AttachFileType = $val['attachFile']->getClientMediaType();
                                $AttachFileSize = $val['attachFile']->getSize();
                                InboxService::updateInboxAttachFile($Inbox->InboxID, $newFileName, $AttachPath, $AttachFileType, $AttachFileSize, $AttachRealFileName);
                                // if(InboxService::updateInboxAttachFile($Inbox->InboxID, $newFileName, $AttachPath, $AttachFileType, $AttachFileSize, $AttachRealFileName) > 0){
                                    $val['attachFile']->moveTo('../../uploads/inbox/'.$newFileName);
                                // }
                            }
                        }
                    }
                }
                $this->data_result['DATA'] = $Inbox;
                $this->data_result['InboxID'] = $Inbox['InboxID'];
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function pushNotification($request, $response, $args){
            
            try{

                // $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $parsedBody = $request->getParsedBody();
                $InboxID = $parsedBody['InboxID'];
                $Data = $parsedBody['Data'];

                if($Data['Group'] == '-1'){

                    $data = [];
                    $data['user_id'] = '';
                    $data['org_id'] = '-1';
                    $data['notification_type'] = 'all';
                    $result = InboxService::pushNotification($InboxID, $data);

                }
                else if(!empty($Data['Group'])){
                    // Push for person in org
                    $data = [];
                    $data['user_id'] = '';
                    $data['org_id'] = $Data['Group'];
                    $data['notification_type'] = 'org';
                    $result = InboxService::pushNotification($InboxID, $data);

                    // Find child in org
                    $OrgList = InboxService::findChildOrg($Data['Group']);
                    foreach ($OrgList as $key => $value) {

                        $data = [];
                        $data['user_id'] = '';
                        $data['org_id'] = $value['OrgID'];
                        $data['notification_type'] = 'org';

                        $result = InboxService::pushNotification($InboxID, $data);
                        $SubOrgList = InboxService::findChildOrg($value['OrgID']);
                        
                        foreach ($SubOrgList as $_key => $_value) {
                            $data = [];
                            $data['user_id'] = '';
                            $data['org_id'] = $value['OrgID'];
                            $data['notification_type'] = 'org';

                            $result = InboxService::pushNotification($InboxID, $data);
                        
                        }
                    }
                }

                foreach ($Data['UserList'] as $key => $value) {
                    $data = [];
                    $data['user_id'] = $value['UserID'];
                    $data['org_id'] = '';
                    $data['notification_type'] = 'user';

                    $result = InboxService::pushNotification($InboxID, $data);
                }

                $this->data_result['DATA']['result'] = $result;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteData($request, $response, $args){
            
            try{

                // $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $parsedBody = $request->getParsedBody();
                $ID = $parsedBody['ID'];
                $deleteStatus = InboxService::deleteData($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteInboxPictureData($request, $response, $args){
            
            try{

                // $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $parsedBody = $request->getParsedBody();
                $ID = $parsedBody['ID'];
                $deleteStatus = InboxService::deleteInboxPictureData($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteInboxAttachFile($request, $response, $args){
            
            try{

                // $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $parsedBody = $request->getParsedBody();
                $ID = $parsedBody['ID'];
                $deleteStatus = InboxService::deleteInboxAttachFile($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getInboxByID($request, $response, $args){
            
            try{

                // $ID = filter_var($request->getAttribute('inboxID'), FILTER_SANITIZE_NUMBER_INT);
                $parsedBody = $request->getParsedBody();
                $ID = $parsedBody['InboxID'];
                $UserID = $parsedBody['UserID'];
                $OrgID = $parsedBody['OrgID'];
                $Inbox = InboxService::getInboxByID($ID);

                // Update seen status
                $result = InboxService::updateSeen($ID, $UserID);

                $this->data_result['DATA'] = $Inbox;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getUnseen($request, $response, $args){
            
            try{

                // $ID = filter_var($request->getAttribute('inboxID'), FILTER_SANITIZE_NUMBER_INT);
                $parsedBody = $request->getParsedBody();
                $UserID = $parsedBody['UserID'];
                $OrgID = $parsedBody['OrgID'];
                
                $TotalUnseen = InboxService::countUnseen($UserID, $OrgID);

                $this->data_result['DATA']['Unseen'] = $TotalUnseen;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function viewInbox($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('inboxID'), FILTER_SANITIZE_NUMBER_INT);
                $Inbox = InboxService::getInboxByID($ID);
                //  Update view
                InboxService::updateView($ID);
                $this->data_result['DATA'] = $Inbox;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function requestInbox($request, $response, $args){
    //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{

                $parsedBody = $request->getParsedBody();
                $Inbox = $parsedBody['Inbox'];
                // echo $Inbox['InboxID'].' askdjhasd' ;
                if(InboxService::updateRequestInboxStatus($Inbox['InboxID'])){
                    $notifications =  $this->generateNotificationReqData($Inbox);
                    foreach ($notifications as $key => $value) {
                        NotificationService::pushNotification($value);
                    }

                }

                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function adminUpdateInboxStatus($request, $response, $args){
    //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{

                $parsedBody = $request->getParsedBody();
                $Inbox = $parsedBody['Inbox'];
                if(InboxService::adminUpdateInboxStatus($Inbox)){
                    $notification =  $this->generateNotificationMarkStatusData($Inbox);
                    NotificationService::pushNotification($notification);
                }
                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        private function generateNotificationReqData($Inbox){

            $notificationTypeID = 12; // Inbox request
            $adminGroup = 6; // Admin Inbox

            $notificationType = NotificationService::getNotificationType($notificationTypeID);
            $RequestUser = UserService::getUser($Inbox['CreateBy']);
            
            $InboxDateTime = $this->makeThaiDate($Inbox['InboxDateTime']);

            $notification['RegionID'] = $Inbox['InboxRegionID'];
            $notification['NotificationType'] = $notificationTypeID;
            $notification['NotificationStatus'] = 'Unseen';
            $notification['NotificationUrl'] = '#/manage_inbox/'.$Inbox['InboxID'];
            $notification['NotificationText'] =  $RequestUser['FirstName'] . ' ' . $RequestUser['LastName'] . ' ' . $notificationType['NotificationType'] . ' หัวข้อข่าว '. $Inbox['InboxTitle'] . ' พื้นที่ต้นข่าว : ' . $Inbox['RegionName'] . ' วันที่ข่าว : ' . $InboxDateTime;
            $notification['NotificationKeyID'] = $Inbox['InboxID'];
            $notification['PushBy'] = $Inbox['CreateBy'];
            $notification['AdminGroup'] = $adminGroup;

            // To specific person who can see this notify by group ID
            $UserList = UserService::getUserByGroupAndRegionIDWithPermission($adminGroup, $Inbox['InboxRegionID'], $Inbox['CreateBy']);
            if(count($UserList) > 0){
                $index = 0;
                foreach ($UserList as $key => $value) {
                    $NotificationList[$index] = $notification;
                    $NotificationList[$index]['ToSpecificPersonID'] = $value['UserID'];
                    $index++;
                }
            }else{
                $NotificationList[0] = $notification;
            }
            return $NotificationList;
        }

        private function generateNotificationMarkStatusData($Inbox){

            $notificationTypeID =  $Inbox['InboxStatus']=='Approve'?13:14; // Inbox request
            $adminGroup = 1; // Admin Inbox

            $notificationType = NotificationService::getNotificationType($notificationTypeID);
            $ApproveUser = UserService::getUser($Inbox['VerifyBy']);
            
            $InboxDateTime = $this->makeThaiDate($Inbox['InboxDateTime']);

            $notification['RegionID'] = $Inbox['InboxRegionID'];
            $notification['NotificationType'] = $notificationTypeID;
            $notification['NotificationStatus'] = 'Unseen';
            $notification['NotificationUrl'] = '#/manage_inbox/'.$Inbox['InboxID'];
            $notification['NotificationText'] =  $ApproveUser['FirstName'] . ' ' . $ApproveUser['LastName'] . ' ' . $notificationType['NotificationType'] . ' หัวข้อข่าว '. $Inbox['InboxTitle'] . ' พื้นที่ต้นข่าว : ' . $Inbox['RegionName'] . ' วันที่ข่าว : ' . $InboxDateTime;
            $notification['NotificationKeyID'] = $Inbox['InboxID'];
            $notification['PushBy'] = $Inbox['CreateBy'];
            $notification['AdminGroup'] = $adminGroup;
            $notification['ToSpecificPersonID'] = $Inbox['CreateBy'];
            return $notification;
        }

        private function makeThaiDate($date){
            $date = str_replace('.000', '', $date);
            $dateTimeObj = split(' ', $date);
            $dateObj = $dateTimeObj[0]; 

            $dateObj = split('-' , $dateObj);
            $day = $dateObj[2];
            $month = $dateObj[1];
            $year = $dateObj[0];

            // Convert month to month text
             switch($month){
                case 1 : $month = 'มกราคม';break;
                case 2 : $month = 'กุมภาพันธ์';break;
                case 3 : $month = 'มีนาคม';break;
                case 4 : $month = 'เมษายน';break;
                case 5 : $month = 'พฤษภาคม';break;
                case 6 : $month = 'มิถุนายน';break;
                case 7 : $month = 'กรกฎาคม';break;
                case 8 : $month = 'สิงหาคม';break;
                case 9 : $month = 'กันยายน';break;
                case 10 : $month = 'ตุลาคม';break;
                case 11 : $month = 'พฤษจิกายน';break;
                case 12 : $month = 'ธันวาคม';break;
            }

            return $day . ' ' . $month . ' ' . $year;
        }
        
    }
    
?>