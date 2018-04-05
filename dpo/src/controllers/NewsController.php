<?php

    namespace App\Controller;
    
    use App\Model\News;
    use App\Service\NewsService;
    use App\Service\UserService;
    use App\Service\NotificationService;
    use App\Service\PermissionService;
    
    class NewsController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
        
        public function getNewsFeed($request, $response, $args){
            
            try{
                
                $NewsList = NewsService::getNewsFeed();
                $this->data_result['DATA'] = $NewsList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function searchNews($request, $response, $args){
            
            try{
                
                $keyword = $request->getAttribute('keyword');
                $NewsList = NewsService::searchNews($keyword);
                $AttachFileList = NewsService::searchNewsAttachFile($keyword);
                $this->data_result['DATA']['NewsList'] = $NewsList;
                $this->data_result['DATA']['AttachFileList'] = $AttachFileList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getNewsList($request, $response, $args){
            
            try{

                $offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT);
                $RegionID = filter_var($request->getAttribute('RegionID'), FILTER_SANITIZE_NUMBER_INT);
                $HideNews = $request->getAttribute('HideNews');
                $CurrentNews = $request->getAttribute('CurrentNews');
                $WaitApprove = $request->getAttribute('WaitApprove');
                $UserID = filter_var($request->getAttribute('UserID'), FILTER_SANITIZE_NUMBER_INT);
                // Check permission
                $user_permission = PermissionService::checkPermission($UserID);
                
                $valid = false;
                foreach ($user_permission as $key => $value) {
                    if($value['AdminGroupID'] == '0' || $value['AdminGroupID'] == '5' || $value['AdminGroupID'] == '6'){
                        $valid = true;
                    } 
                }
                if(!$valid){
                    $this->data_result['STATUS'] = 'ERROR';
                    return $this->returnResponse(401, $this->data_result, $response);  
                }

                $NewsList = NewsService::getNewsList($offset,$RegionID,$HideNews,$CurrentNews,$WaitApprove);
                $this->data_result['DATA'] = $NewsList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getNewsListView($request, $response, $args){
            
            try{

                $offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT);
                $RegionID = filter_var($request->getAttribute('RegionID'), FILTER_SANITIZE_NUMBER_INT);

                $NewsList = NewsService::getNewsListView($offset,$RegionID);
                $this->data_result['DATA'] = $NewsList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getNewsTypeList($request, $response, $args){
            
            try{

                $NewsTypeList = NewsService::getNewsTypeList();
                $this->data_result['DATA'] = $NewsTypeList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateData($request, $response, $args){
            
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
                        $parsedBody['NewsPicture'] = 'img/news/'.$newFileName;
                        $img->moveTo('../../img/news/'.$newFileName);
                        
                    }
                }

                
                $News = NewsService::updateData($parsedBody);

                // Insert new picture list
                $files = $files['fileimg'];
                
                if($files != null){
                    foreach($files as $key => $val){
                        if($val != null){
                            if($val['fileimg']->getClientFilename() != ''){
                                $ext = pathinfo($val['fileimg']->getClientFilename(), PATHINFO_EXTENSION);
                                $newFileName = $News->NewsID . '_' . date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                                //$newFileName = $News->NewsID . '_' . date('YmdHis').'_'.$val['fileimg']->getClientFilename();
                                $PicturePath = 'img/news/'.$newFileName;
                                if(NewsService::updateNewsPictureData($News->NewsID, $PicturePath) > 0){
                                    $val['fileimg']->moveTo('../../img/news/'.$newFileName);
                                }
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
                                $newFileName = $News->NewsID . '_' .  date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                                $AttachRealFileName =  $News->NewsID . '_' . date('YmdHis').'_'.$val['attachFile']->getClientFilename();
                                $AttachPath = 'uploads/news/'.$newFileName;
                                $AttachFileType = $val['attachFile']->getClientMediaType();
                                $AttachFileSize = $val['attachFile']->getSize();
                                if(NewsService::updateNewsAttachFile($News->NewsID, $newFileName, $AttachPath, $AttachFileType, $AttachFileSize, $AttachRealFileName) > 0){
                                    $val['attachFile']->moveTo('../../uploads/news/'.$newFileName);
                                }
                            }
                        }
                    }
                }
                $this->data_result['DATA'] = $News;
                $this->data_result['NewsID'] = $News['NewsID'];
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteData($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = NewsService::deleteData($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteNewsPictureData($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = NewsService::deleteNewsPictureData($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteNewsAttachFile($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = NewsService::deleteNewsAttachFile($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getNewsByID($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('newsID'), FILTER_SANITIZE_NUMBER_INT);
                $News = NewsService::getNewsByID($ID);
                $this->data_result['DATA'] = $News;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function viewNews($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('newsID'), FILTER_SANITIZE_NUMBER_INT);
                $News = NewsService::getNewsByID($ID);
                //  Update view
                NewsService::updateView($ID);
                $this->data_result['DATA'] = $News;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function requestNews($request, $response, $args){
    //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{

                $parsedBody = $request->getParsedBody();
                $News = $parsedBody['News'];
                // echo $News['NewsID'].' askdjhasd' ;
                if(NewsService::updateRequestNewsStatus($News['NewsID'])){
                    $notifications =  $this->generateNotificationReqData($News);
                    foreach ($notifications as $key => $value) {
                        NotificationService::pushNotification($value);
                    }

                }

                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function adminUpdateNewsStatus($request, $response, $args){
    //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{

                $parsedBody = $request->getParsedBody();
                $News = $parsedBody['News'];
                if(NewsService::adminUpdateNewsStatus($News)){
                    $notification =  $this->generateNotificationMarkStatusData($News);
                    NotificationService::pushNotification($notification);
                }
                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        private function generateNotificationReqData($News){

            $notificationTypeID = 12; // News request
            $adminGroup = 6; // Admin News

            $notificationType = NotificationService::getNotificationType($notificationTypeID);
            $RequestUser = UserService::getUser($News['CreateBy']);
            
            $NewsDateTime = $this->makeThaiDate($News['NewsDateTime']);

            $notification['RegionID'] = $News['NewsRegionID'];
            $notification['NotificationType'] = $notificationTypeID;
            $notification['NotificationStatus'] = 'Unseen';
            $notification['NotificationUrl'] = '#/manage_news/'.$News['NewsID'];
            $notification['NotificationText'] =  $RequestUser['FirstName'] . ' ' . $RequestUser['LastName'] . ' ' . $notificationType['NotificationType'] . ' หัวข้อข่าว '. $News['NewsTitle'] . ' พื้นที่ต้นข่าว : ' . $News['RegionName'] . ' วันที่ข่าว : ' . $NewsDateTime;
            $notification['NotificationKeyID'] = $News['NewsID'];
            $notification['PushBy'] = $News['CreateBy'];
            $notification['AdminGroup'] = $adminGroup;

            // To specific person who can see this notify by group ID
            $UserList = UserService::getUserByGroupAndRegionIDWithPermission($adminGroup, $News['NewsRegionID'], $News['CreateBy']);
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

        private function generateNotificationMarkStatusData($News){

            $notificationTypeID =  $News['NewsStatus']=='Approve'?13:14; // News request
            $adminGroup = 1; // Admin News

            $notificationType = NotificationService::getNotificationType($notificationTypeID);
            $ApproveUser = UserService::getUser($News['VerifyBy']);
            
            $NewsDateTime = $this->makeThaiDate($News['NewsDateTime']);

            $notification['RegionID'] = $News['NewsRegionID'];
            $notification['NotificationType'] = $notificationTypeID;
            $notification['NotificationStatus'] = 'Unseen';
            $notification['NotificationUrl'] = '#/manage_news/'.$News['NewsID'];
            $notification['NotificationText'] =  $ApproveUser['FirstName'] . ' ' . $ApproveUser['LastName'] . ' ' . $notificationType['NotificationType'] . ' หัวข้อข่าว '. $News['NewsTitle'] . ' พื้นที่ต้นข่าว : ' . $News['RegionName'] . ' วันที่ข่าว : ' . $NewsDateTime;
            $notification['NotificationKeyID'] = $News['NewsID'];
            $notification['PushBy'] = $News['CreateBy'];
            $notification['AdminGroup'] = $adminGroup;
            $notification['ToSpecificPersonID'] = $News['CreateBy'];
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