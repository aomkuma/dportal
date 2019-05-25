<?php

    namespace App\Controller;
    
    use App\Service\NotificationService;
    use App\Service\RoomReserveService;
    use App\Service\CarReserveService;
    use App\Service\NewsService;

    class NotificationController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
        
        public function getNotificationList($request, $response, $args){
            
            try{
                
                $regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT);
                $adminGroup = filter_var($request->getAttribute('adminGroup'), FILTER_SANITIZE_NUMBER_INT);
                $userID = filter_var($request->getAttribute('userID'), FILTER_SANITIZE_NUMBER_INT);
                $offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT);
                $notificationList = NotificationService::getNotificationList($regionID, $adminGroup, $userID, $offset);
                $this->data_result['DATA'] = $notificationList;

                $totalNewNotifications = NotificationService::countNotificationUnseen($regionID, $adminGroup, $userID);
                $this->data_result['DATA']['totalNewNotifications'] = $totalNewNotifications;
                
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getNotificationListByCondition($request, $response, $args){
            
            try{
                $notificationType = $request->getAttribute('notificationType');
                $regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT);
                $keyword = $request->getAttribute('keyword');
                $adminGroup = filter_var($request->getAttribute('adminGroup'), FILTER_SANITIZE_NUMBER_INT);
                $userID = filter_var($request->getAttribute('userID'), FILTER_SANITIZE_NUMBER_INT);
                $offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT);
                $keyword = $keyword == '-'?'':$keyword;
                $notificationList = NotificationService::getNotificationListByCondition($notificationType, $regionID, $keyword, $adminGroup, $userID, $offset);

                // get status 
                //$Result = $notificationList;
                foreach ($notificationList['NotificationList'] as $key => $value) {
                    if($value['NotificationType'] <= 8){
                        // get room
                        $RES = NotificationService::getRoom($value['NotificationKeyID']);
                        $value['Status'] = $RES['ReserveStatus'];
                    }
                    else if($value['NotificationType'] >= 9 && $value['NotificationType'] <= 11){
                        $RES = NotificationService::getCar($value['NotificationKeyID']);
                        $value['Status'] = $RES['ReserveStatus'];
                    }
                    else if($value['NotificationType'] >= 12 && $value['NotificationType'] <= 14){
                        $RES = NotificationService::getNews($value['NotificationKeyID']);
                        $value['Status'] = $RES['NewsStatus'];
                    }
                    else if($value['NotificationType'] >= 15 && $value['NotificationType'] <= 22){
                        $RES = NotificationService::getRepair($value['NotificationKeyID']);
                        $value['Status'] = $RES['RepairedStatus'];
                    }
                    //array_push($Result, $value);
                    $Result['NotificationList'][] = $value;
                }

                $this->data_result['DATA'] = $Result;
                $this->data_result['DATA']['offset'] = $notificationList['offset'];
                $this->data_result['DATA']['continueLoad'] = $notificationList['continueLoad'];
                $this->data_result['DATA']['totalNewNotifications'] = $notificationList['total'];

                // $totalNewNotifications = NotificationService::countNotificationByConditionUnseen($notificationType, $regionID, $adminGroup, $userID);
                // $this->data_result['DATA']['totalNewNotifications'] = $totalNewNotifications;
                
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateNotificationStatus($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$Notification = $parsedBody['Notification'];
				$pullBy =  $parsedBody['userID'];
				$regionID = filter_var($parsedBody['regionID'], FILTER_SANITIZE_NUMBER_INT);
                $adminGroup = filter_var($parsedBody['groupID'], FILTER_SANITIZE_NUMBER_INT);
				//die();
				$result = NotificationService::updateNotificationStatus($Notification, $pullBy);
				$totalNewNotifications = NotificationService::countNotificationUnseen($regionID, $adminGroup, $pullBy);

				// Update workflow status by type (reserve room, car)
				switch($Notification['NotificationType']){
					case '1': RoomReserveService::updateReserveRoomAdminRecv($pullBy, $Notification['NotificationKeyID']);break;
                    case '6': RoomReserveService::updateReserveDestinationRoomAdminRecv($pullBy, $Notification['NotificationKeyID']);break;
                    case '9': CarReserveService::updateReserveCarAdminRecv($pullBy, $Notification['NotificationKeyID']);break;
                    case '12': NewsService::updateNewsAdminRecv($pullBy, $Notification['NotificationKeyID']);break;
				}

				$this->data_result['DATA']['totalNewNotifications'] = $totalNewNotifications;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}

        public function checkAdminPermission($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
                $UserID = $parsedBody['UserID'];
                $ReserveRoomID = $parsedBody['ReserveRoomID'];
                $NotificationType =  $parsedBody['NotificationType'];
                
                $result = NotificationService::checkAdminPermission($UserID, $ReserveRoomID, $NotificationType);

                $this->data_result['DATA']['IsAdmin'] = (!empty($result)?true:false);
                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
        }
        
    }
    
?>