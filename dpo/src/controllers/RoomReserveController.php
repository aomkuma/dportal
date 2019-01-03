<?php

    namespace App\Controller;
    
    use App\Model\Region;
    use App\Service\RoomService;
    use App\Service\RoomReserveService;
    use App\Service\NotificationService;
    use App\Service\UserService;
    use App\Service\RegionService;
    
    use App\Controller\Mailer;
    use App\Controller\SMS;

    class RoomReserveController extends Controller {
        protected $logger;
        protected $sms;
        protected $db;
        
        public function __construct($logger, $sms, $db){
            $this->logger = $logger;
            $this->db = $db;
            $this->sms = $sms;
        }

        public function getRoomMonitor($request, $response, $args){
        	try{
        		$parsedBody = $request->getParsedBody();
                $RegionID = $parsedBody['RegionID'];
                $CurDate = substr($parsedBody['CurDate'], 0, 10);//str_replace('T', ' ', $parsedBody['CurDate']); // // $CurDate = substr($CurDate, 0, 19);
                
                // Get room list
                $RoomList = RoomService::getAllRoomActiveInRegion($RegionID);
                $DataList = [];
                foreach ($RoomList as $key => $value) {
                	$RoomID = $value['RoomID'];
                	$arr = [];
                	$arr['Room'] = $value['RoomName'];
                	$data = RoomReserveService::getRoomMonitor($RoomID, $CurDate);
                	$arr['Reserve'] = $data;
                	array_push($DataList, $arr);
                }
                
                $RegionData = RegionService::getRegion($RegionID);
                
                $this->data_result['DATA']['RegionData'] = $RegionData;
                $this->data_result['DATA']['DataList'] = $DataList;
                
                return $this->returnResponse(200, $this->data_result, $response);

        	}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }
        
        public function getRoomBookingList($request, $response, $args){
            
            try{
                
                $regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT);
                $findDate = filter_var($request->getAttribute('findDate'), FILTER_SANITIZE_STRING);
                $findDateArr = split('-', $findDate);
                $d = strlen($findDateArr[2]) == 1? '0'.$findDateArr[2]: $findDateArr[2];
                $m = $findDateArr[1];
                $y = $findDateArr[0];
                $findDate = $y . '-' . $m . '-' . $d;
                //$findDate = '2017-06-17';
                // Get all rooms in selected region
                $roomList = RoomService::getAllRoomActiveInRegion($regionID);
                
                // Find room reserve in selected day
                foreach($roomList as $index => $objRoom){
                    $roomReserveList = RoomReserveService::getReservListByRoomAndDay($objRoom[RoomID], $findDate);
                    $roomList[$index][ReserveList] = $roomReserveList;
                }
                
                $this->data_result['DATA'] = $roomList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
        
        public function getDefaultBookingRoomInfo($request, $response, $args){
            
            try{
                $userID = filter_var($request->getAttribute('userID'), FILTER_SANITIZE_NUMBER_INT);
                $roomID = filter_var($request->getAttribute('roomID'), FILTER_SANITIZE_NUMBER_INT);
                $reserveRoomID = filter_var($request->getAttribute('reserveRoomID'), FILTER_SANITIZE_NUMBER_INT);
                // Get room info
                $roomInfo = RoomReserveService::getRoomInfo($roomID);
                // Get reserve info
                $reserveRoomInfo = RoomReserveService::getReserveRoomInfo($reserveRoomID);
                // Get internal attendee
                $internalAttendeeList = RoomReserveService::getInternalAttendee($reserveRoomID);
                // Get external attendee
                $externalAttendeeList = RoomReserveService::getExternalAttendee($reserveRoomID);
                // Get devices info
                $deviceList = RoomReserveService::getDeviceForRoom($reserveRoomID);
                // Get foods info
                $foodList = RoomReserveService::getFoodForRoom($reserveRoomID);
                // Get Request Person Info
				$RequestUser = UserService::getUser($reserveRoomInfo[0]->CreateBy);
				unset($RequestUser['Password']); 
				// Get Verify Person Info
				$VerifyUser = UserService::getUser($reserveRoomInfo[0]->AdminID);
				unset($VerifyUser['Password']); 

				if($roomInfo[0]['ConferenceType'] == 'Y'){
					// Get room destination
					$roomDestinationList = RoomReserveService::getRoomDestinationList($roomID, $reserveRoomID);
				}
				
                $this->data_result['DATA']['RoomInfo'] = $roomInfo[0];
                
                //$reserveRoomInfo[0]['StartDate'] = substr($reserveRoomInfo[0]['StartDate'], 0, 10); //$reserveRoomInfo[0]['StartDate'];
                $this->data_result['DATA']['reserveRoomInfo'] = $reserveRoomInfo[0];
                $this->data_result['DATA']['internalAttendeeList'] = $internalAttendeeList;
                $this->data_result['DATA']['externalAttendeeList'] = $externalAttendeeList;
                $this->data_result['DATA']['deviceList'] = $deviceList;
                $this->data_result['DATA']['foodList'] = $foodList;
                $this->data_result['DATA']['roomDestinationList'] = $roomDestinationList;
                $this->data_result['DATA']['RequestUser'] = $RequestUser;
                $this->data_result['DATA']['VerifyUser'] = $VerifyUser;
                
                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
            
        }

        private function reFormatDate($date){

			$dateTimeArr = split(' ', $date);
			$dateArr = split('-', $dateTimeArr[0]);
            $d = strlen($dateArr[2]) == 1? '0'.$dateArr[2]: $dateArr[2];
            $m = $dateArr[1];
            $y = $dateArr[0];
            return $y . '-' . $m . '-' . $d;
        }
        
        public function updateReserveRoomInfo($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
                $ReserveRoomInfo = $parsedBody['ReserveRoomInfo'];
                $RoomInfo = $parsedBody['RoomInfo'];
				$reserveRoomID = $parsedBody['reserveRoomID'];
				
                
				//$this->logger->info($duplicate);
				// exit;
				$duplicate = $this->chkDuplicateTime($reserveRoomID, $RoomInfo['RoomID'], $ReserveRoomInfo['StartDateTime'], $ReserveRoomInfo['EndDateTime']);
				
                if(trim($duplicate[0]['ReserveRoomID']) == ''){
                    $reserveRoomID = RoomReserveService::updateReserveRoomInfo($ReserveRoomInfo);
					$this->data_result['DATA']['ReserveRoomID'] = $reserveRoomID;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA']['MSG'] = 'มีการจองห้องประชุมในช่วงเวลา ' . $duplicate[0]['StartDateTime'] . ' - ' . $duplicate[0]['EndDateTime'] . ' ในหัวข้อ ' . $duplicate[0]['TopicConference'] . ' แล้ว';
                }
                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
        }

        private function chkDuplicateTime($reserveRoomID, $roomID, $startDateTime, $endDateTime){
        	$chkStartDateTime = date('Y-m-d H:i:s.000', strtotime("+1 minutes", strtotime($startDateTime)));
        	$chkEndDateTime = date('Y-m-d H:i:s.000', strtotime("-1 minutes", strtotime($endDateTime)));
            return RoomReserveService::checkDuplicateTime($reserveRoomID, $roomID , $chkStartDateTime, $chkEndDateTime);
        }

		public function updateAttendee($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$Attendee = $parsedBody['Attendee'];
				//die();
				$result = RoomReserveService::updateAttendee($Attendee);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function deleteAttendee($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$UserID = filter_var($request->getAttribute('UserID'), FILTER_SANITIZE_NUMBER_INT); //$parsedBody['UserID'];
				$ReserveRoomID = filter_var($request->getAttribute('ReserveRoomID'), FILTER_SANITIZE_NUMBER_INT); //$parsedBody['ReserveRoomID'];
				$this->logger->info('Find by $UserID : '.$UserID.', $ReserveRoomID : '.$ReserveRoomID);
				//die();
				$result = RoomReserveService::deleteAttendee($UserID, $ReserveRoomID);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function updateExternalAttendee($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$ExtAttendee = $parsedBody['ExternalAttendee'];
				//die();
				$result = RoomReserveService::updateExternalAttendee($ExtAttendee);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function deleteExternalAttendee($request, $response, $args){
            try{
                
				$AttendeeID = filter_var($request->getAttribute('AttendeeID'), FILTER_SANITIZE_NUMBER_INT); 
				
				//die();
				$result = RoomReserveService::deleteExternalAttendee($AttendeeID);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function getDeviceList($request, $response, $args){
            try{
                
				$regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT); 
				$offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT); 
				//die();
				$result = RoomReserveService::getDeviceList($regionID, $offset);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function updateRoomDevice($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$Device = $parsedBody['Device'];
				$reserveRoomID = filter_var($parsedBody['reserveRoomID'], FILTER_SANITIZE_NUMBER_INT);
				//die();
				$result = RoomReserveService::updateRoomDevice($Device, $reserveRoomID);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function deleteRoomDevice($request, $response, $args){
            try{
                
				$deviceID = filter_var($request->getAttribute('deviceID'), FILTER_SANITIZE_NUMBER_INT); 
				$reserveRoomID = filter_var($request->getAttribute('reserveRoomID'), FILTER_SANITIZE_NUMBER_INT); 
				
				//die();
				$result = RoomReserveService::deleteRoomDevice($deviceID, $reserveRoomID);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function getFoodList($request, $response, $args){
            try{
                
				$regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT); 
				$offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT); 
				
				//die();
				$result = RoomReserveService::getFoodList($regionID, $offset);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function updateRoomFood($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$Food = $parsedBody['Food'];
				$reserveRoomID = filter_var($parsedBody['reserveRoomID'], FILTER_SANITIZE_NUMBER_INT);
				//die();
				$result = RoomReserveService::updateRoomFood($Food, $reserveRoomID);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function deleteRoomFood($request, $response, $args){
            try{
                
				$foodID = filter_var($request->getAttribute('foodID'), FILTER_SANITIZE_NUMBER_INT); 
				$reserveRoomID = filter_var($request->getAttribute('reserveRoomID'), FILTER_SANITIZE_NUMBER_INT); 
				
				//die();
				$result = RoomReserveService::deleteRoomFood($foodID, $reserveRoomID);
				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}
		
		public function updateRoomDestinationStatus($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$Room = $parsedBody['Room'];
				$reserveRoomID = filter_var($parsedBody['reserveRoomID'], FILTER_SANITIZE_NUMBER_INT);
				$status = $parsedBody['status'];
				
				if($status){
					// Update RoomReserve (destination)
					// Get reserve info
	                $reserveRoomInfoOrigin = RoomReserveService::getReserveRoomInfo($reserveRoomID, true);
	                $reserveRoomInfoOrigin = $reserveRoomInfoOrigin[0];
	                $duplicate = $this->chkDuplicateTimeDestination($Room['RoomID'], $reserveRoomInfoOrigin['StartDateTime'], $reserveRoomInfoOrigin['EndDateTime']);
	                // echo $reserveRoomInfoOrigin['StartDateTime'];exit;
	                if(trim($duplicate[0]['ReserveRoomID']) == ''){
	                	$reserveRoomInfoOrigin['ReserveRoomID'] = '';
	                	$reserveRoomInfoOrigin['RoomID'] = $Room['RoomID'];
	                    $DescReserveRoomID = RoomReserveService::updateReserveRoomInfo($reserveRoomInfoOrigin);
	                    $result = RoomReserveService::updateRoomDestinationStatus($Room, $reserveRoomID, $status);
						$this->data_result['DATA']['ReserveRoomID'] = $reserveRoomID;
						$this->data_result['DATA'] = $result;
	                }else{

	                    $this->data_result['STATUS'] = 'ERROR';
	                    $this->data_result['DATA']['ReserveStatus'] = 'Full';
	                    $this->data_result['DATA']['MSG'] = 'มีการจองห้องประชุมนี้ในช่วงเวลา ' . $duplicate[0]['StartDateTime'] . ' - ' . $duplicate[0]['EndDateTime'] . ' ในหัวข้อ ' . $duplicate[0]['TopicConference'] . ' แล้ว';
	                }
	            }else{
	            	$reserveRoomInfoOrigin = RoomReserveService::getReserveRoomInfo($reserveRoomID, true);
	                $reserveRoomInfoOrigin = $reserveRoomInfoOrigin[0];
	                
	            	$duplicate = $this->chkDuplicateTimeDestination($Room['RoomID'], $reserveRoomInfoOrigin['StartDateTime'], $reserveRoomInfoOrigin['EndDateTime']);
	            	
	                if(trim($duplicate[0]['ReserveRoomID']) != ''){
	                	// Remove Reserve room destination
	                	RoomReserveService::removeReserveRoom($duplicate[0]['ReserveRoomID']);
	                	$result = RoomReserveService::updateRoomDestinationStatus($Room, $reserveRoomID, $status);
	                	$this->data_result['DATA'] = $result;
	                }
	            }
				//die();
				
				
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}

		private function chkDuplicateTimeDestination($roomID, $startDateTime, $endDateTime){
        	$chkStartDateTime = date('Y-m-d H:i:s.000', strtotime("+1 minutes", strtotime($startDateTime)));
        	$chkEndDateTime = date('Y-m-d H:i:s.000', strtotime("-1 minutes", strtotime($endDateTime)));
            return RoomReserveService::checkDuplicateTimeDestination($roomID , $chkStartDateTime, $chkEndDateTime);
        }

		public function requestReserveRoom($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$ReserveRoomInfo = $parsedBody['ReserveRoomInfo'];
                $RoomInfo = $parsedBody['RoomInfo'];
                $InternalAttendeeList = $parsedBody['InternalAttendeeList'];
                $ExternalAttendeeList = $parsedBody['ExternalAttendeeList'];
                $DeviceList = $parsedBody['DeviceList'];
                $FoodList = $parsedBody['FoodList'];
                $RoomDestinationList = $parsedBody['RoomDestinationList'];
				$reserveRoomID = $parsedBody['reserveRoomID'];
				//die();
				//$this->logger->info(date('Y-m-d H:i:s.000'));
				$this->logger->info($ReserveRoomInfo['StartDateTime'] . ' to ' . $ReserveRoomInfo['EndDateTime']);
				$duplicate = $this->chkDuplicateTime($reserveRoomID, $RoomInfo['RoomID'], $ReserveRoomInfo['StartDateTime'], $ReserveRoomInfo['EndDateTime']);
				$this->logger->info($duplicate);
				// exit;
                if(trim($duplicate[0]['ReserveRoomID']) == ''){

					RoomReserveService::updateReserveRoomInfo($ReserveRoomInfo);
					$result = RoomReserveService::markStatus($reserveRoomID, 'Request', '');
					$notifications = $this->generateNotificationReqData($ReserveRoomInfo, $RoomInfo, $reserveRoomID);
					foreach ($notifications as $key => $value) {
						//print_r($value);
						NotificationService::pushNotification($value);
					}

					// Destination Room notification (DestinationRoomID)
					foreach($RoomDestinationList as $key => $value){
						if(intval($value['DestinationRoomID']) > 0 && $value['ReserveStatus'] != ''){
							$notifications = $this->generateNotificationReqRoomDesData($ReserveRoomInfo, $value, $reserveRoomID);
							foreach ($notifications as $_key => $_value) {
								NotificationService::pushNotification($_value);
							}
						}
					}

					$this->data_result['DATA'] = 'Request';
					
				}else{
					$this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA']['MSG'] = 'มีการจองห้องประชุมในช่วงเวลา ' . $duplicate[0]['StartDateTime'] . ' - ' . $duplicate[0]['EndDateTime'] . ' ในหัวข้อ ' . $duplicate[0]['TopicConference'] . ' แล้ว';
				}

				return $this->returnResponse(200, $this->data_result, $response);

			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}

		public function cancelRoom($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();
				$reserveRoomID = filter_var($parsedBody['reserveRoomID'], FILTER_SANITIZE_NUMBER_INT);
				
				// Option 1 : Update status
				//$result = RoomReserveService::cancelRoom($reserveRoomID);
				// Find reserve room details
				$reserveRoomInfo = RoomReserveService::getReserveRoomInfo($reserveRoomID);
				$reserveRoomInfo = $reserveRoomInfo[0];
			
				$StartDateTime = $reserveRoomInfo['StartDateTime'];
				$EndDateTime = $reserveRoomInfo['EndDateTime'];
				$TopicConference = $reserveRoomInfo['TopicConference'];

				// Find destination room ID
				$descReserveRoomInfo = RoomReserveService::getReserveDesctinationRoomInfo($TopicConference, $StartDateTime, $EndDateTime, $reserveRoomID);
				// print_r($descReserveRoomInfo);
				// exit;
				// Option 2 Delete all assosiate data
				$result = RoomReserveService::cancelAttendeeReserve($reserveRoomID);
				$this->logger->info('Remove Attendee : '.$result);
				$result = RoomReserveService::cancelAttendeeExternalReserve($reserveRoomID);
				$this->logger->info('Remove AttendeeExternal : '.$result);
				$result = RoomReserveService::cancelFoodReserve($reserveRoomID);
				$this->logger->info('Remove Food : '.$result);
				$result = RoomReserveService::cancelDeviceReserve($reserveRoomID);
				$this->logger->info('Remove Device : '.$result);
				$result = RoomReserveService::cancelRoomDestinationReserve($reserveRoomID);
				$this->logger->info('Remove Room Destination Log: '.$result);
				foreach ($descReserveRoomInfo as $k => $v) {
					$result = RoomReserveService::cancelRoomReserve($v['ReserveRoomID']);
					$this->logger->info('Remove Room Reserve  Destination : '.$result);	
				}
				$result = RoomReserveService::cancelNotifications($reserveRoomID);
				$this->logger->info('Remove Notification : '.$result);
				$result = RoomReserveService::cancelRoomReserve($reserveRoomID);
				$this->logger->info('Remove Room Reserve : '.$result);

				$this->data_result['DATA'] = $result;
				return $this->returnResponse(200, $this->data_result, $response);
			}catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
		}

		public function markStatus($request, $response, $args){
        	try{
    //              error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
                $parsedBody = $request->getParsedBody();
                $ReserveRoomInfo = $parsedBody['ReserveRoomInfo'];
                $RoomInfo = $parsedBody['RoomInfo'];
                $InternalAttendeeList = $parsedBody['InternalAttendeeList'];
                $ExternalAttendeeList = $parsedBody['ExternalAttendeeList'];
                $DeviceList = $parsedBody['DeviceList'];
                $FoodList = $parsedBody['FoodList'];
                $RequestUser =  $parsedBody['RequestUser'];
                $ReserveRoomID = $parsedBody['ReserveRoomID'];
                $ReserveStatus = $parsedBody['ReserveStatus'];
				$AdminComment = $parsedBody['AdminComment'];
				
				// Check already approve or not
                $RoomInfo = RoomReserveService::getReserveRoomInfo($ReserveRoomID);
                $RoomInfo = $RoomInfo[0];

                if($RoomInfo['ReserveStatus'] != 'Request'){
                	$this->data_result['STATUS'] = 'ERROR';
                	$this->data_result['DATA']['MSG'] = 'มีผู้ทำรายการ '.$RoomInfo['ReserveStatus'].' เรียบร้อยแล้ว';
					return $this->returnResponse(200, $this->data_result, $response);
					exit(0);
                }

                $ReserveRoom = RoomReserveService::markStatus($ReserveRoomID, $ReserveStatus, $AdminComment);
                $reserveRoomID = $ReserveRoom->ReserveRoomID;
				$this->data_result['DATA']['ReserveRoomID'] = $ReserveRoomID;

				// Push notify
				$notification = $this->generateNotificationMarkStatusData($ReserveRoomInfo, $RoomInfo, $ReserveRoomID, $ReserveStatus);
				$this->logger->info(' Region ID : '.$notification['RegionID']);
				$this->logger->info(' Room Region ID : '.$RoomInfo['RegionID']);
				if(empty($notification['RegionID'])){
					$notification['RegionID'] = $RoomInfo['RegionID'];	
				}
				
				NotificationService::pushNotification($notification);
				
				// Send mail	
				$mailer = new Mailer;
                $mailer->setSubject("DPO :: แจ้งยืนยันการใช้ห้องประชุม " . $RoomInfo['RoomName'] . " พื้นที่ " . $RoomInfo['region_name'] );
                $mailer->isHtml(true);
                $mailer->setHTMLContent($this->generateEmailContent($RoomInfo, $ReserveRoomInfo, $InternalAttendeeList, $ExternalAttendeeList, $DeviceList, $FoodList, $RequestUser));

                // Set receiver

                // Get Create by Email
                $CreateBy = UserService::getUser($ReserveRoomInfo['CreateBy']);
                $mailer->setReceiver($CreateBy['Email']);

                // Get InternalAttendeeList Email
                foreach ($InternalAttendeeList as $key => $value) {
                	if(!empty($value['Email'])){
	                    $mailer->setReceiver($value['Email']);
	                }
                }

                // Get ExternalAttendeeList Email
                foreach ($ExternalAttendeeList as $key => $value) {
                	if(!empty($value['Email'])){
	                    $mailer->setReceiver($value['Email']);
	                }
                }

                if(!empty($RoomInfo['room_admin_email'])){
	                $mailer->setReceiver($RoomInfo['room_admin_email']);
	            }
	            if(!empty($RoomInfo['device_admin_email'])){
                	$mailer->setReceiver($RoomInfo['device_admin_email']);
	            }
	            if(!empty($RoomInfo['food_admin_email'])){
	                $mailer->setReceiver($RoomInfo['food_admin_email']);
	            }

                if($mailer->sendMail()){
                	$this->logger->info('Sent mail Room success');
                }else{
                	$this->logger->info('Sent mail Room failed');
                }

                // SMS to paricipants
                if($ReserveStatus == 'Approve'){
                	
                	// Get reserve info
	                //$RoomInfo = RoomReserveService::getReserveRoomInfo($ReserveRoomID);
	                // Get internal attendee
	                $InternalAttendeeList = RoomReserveService::getInternalAttendee($ReserveRoomID);
	                // Get external attendee
	                $ExternalAttendeeList = RoomReserveService::getExternalAttendee($ReserveRoomID);

                	$smsContent = 'ประชุม' . $ReserveRoomInfo['TopicConference'] . ' ณ ' . $RoomInfo['RoomName'] . ' เริ่ม: ' . $this->makeShortDateTime($ReserveRoomInfo['StartDateTime']) . ' น.';

                	foreach ($InternalAttendeeList as $key => $value) {
                		if(!empty($value['Mobile'])){
		                    $this->sendSMS($value['Mobile'], $smsContent);
		                }
	                }
	                foreach ($ExternalAttendeeList as $key => $value) {
	                	if($value['Mobile']){
		                    $this->sendSMS(str_replace('-', '', $value['Mobile']), $smsContent);
		                }
	                }

	                if(!empty($RoomInfo['device_admin_Mobile'])){
		                $this->sendSMS($RoomInfo['device_admin_Mobile'], $smsContent);
		            }
		            if(!empty($RoomInfo['room_admin_Mobile'])){
	                    $this->sendSMS($RoomInfo['room_admin_Mobile'], $smsContent);
	                }
                    if(!empty($RoomInfo['food_admin_Mobile'])){
	                    $this->sendSMS($RoomInfo['food_admin_Mobile'], $smsContent);
	                }
                    if(!empty($RoomInfo['Mobile'])){
	                    $this->sendSMS($CreateBy['Mobile'], $smsContent);
	                }

                }
				
                // Update notification seen
                $NotificationTypeList = [1,6];
                NotificationService::updateNotificationSeenData($ReserveRoomID, $NotificationTypeList);

                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
        }

        private function sendSMS($receiver, $content){
        	$sms = new SMSController($this->logger, $this->sms);
            $sms->setSmsReceiver($receiver);
            $sms->setSmsDesc($content);
            $sms->sendSMS();
        }

        public function markStatusRoomDestination($request, $response, $args){
        	try{
                
                $parsedBody = $request->getParsedBody();
                $ReserveRoomInfo = $parsedBody['ReserveRoomInfo'];
                $RoomInfo = $parsedBody['RoomInfo'];
                $ReserveRoomID = $parsedBody['ReserveRoomID'];
                $ReserveStatus = $parsedBody['ReserveStatus'];
				$AdminComment = $parsedBody['AdminComment'];
                
                $reserveRoomID = RoomReserveService::markStatusRoomDestination($ReserveRoomID, $ReserveStatus, $AdminComment);
				$this->data_result['DATA']['ReserveRoomID'] = $reserveRoomID;

				// Push notify
				$notificationList = $this->generateNotificationMarkStatusRoomDesData($ReserveRoomInfo, $RoomInfo, $reserveRoomID, $ReserveStatus);
				foreach($notificationList as $k => $v){
					NotificationService::pushNotification($v);
				}
					
                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
        }

		private function generateNotificationReqRoomDesData($ReserveRoomInfo, $RoomDestination, $reserveRoomID){

			$notificationTypeID = 6; // Room destination request
			$adminGroup = 2; // Default Admin Room

			$startDateTime = $this->makeThaiDateTime($ReserveRoomInfo['StartDateTime']);
			$endDateTime = $this->makeThaiDateTime($ReserveRoomInfo['EndDateTime']);

			$notificationType = NotificationService::getNotificationType($notificationTypeID);
			$adminGroup = $notificationType['NotificationGroup'];
			$user = UserService::getUser($ReserveRoomInfo['CreateBy']);
			

			$notification['RegionID'] = $RoomDestination['RegionID'];
			$notification['NotificationType'] = $notificationTypeID;
			$notification['NotificationStatus'] = 'Unseen';
			$notification['NotificationUrl'] = '#/roombooking/'.$ReserveRoomInfo['CreateBy'].'/'.$ReserveRoomInfo['RoomID'].'/null/'.$reserveRoomID.'/'.$RoomDestination['DestinationRoomID'];
			$notification['NotificationText'] =  $user['FirstName'] . ' ' . $user['LastName'] . ' ' . $notificationType['NotificationType'] . ' ห้องประชุม : ' . $RoomDestination['RoomName'] . ' พื้นที่ : ' . $RoomDestination['RegionName'] . ' ช่วงเวลา : ' . $startDateTime . 'น. ถึง ' . $endDateTime . 'น.';
			$notification['NotificationKeyID'] = $RoomDestination['DestinationRoomID'];
			$notification['PushBy'] = $ReserveRoomInfo['CreateBy'];
			$notification['AdminGroup'] = $adminGroup;

			// To specific person who can see this notify by group ID
			$UserList = UserService::getUserByGroupAndRegionIDWithPermission($adminGroup, $RoomDestination['RegionID'], $ReserveRoomInfo['CreateBy']);
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

		private function generateNotificationReqData($ReserveRoomInfo, $RoomInfo, $reserveRoomID){

			$notificationTypeID = 1; // Room request
			$adminGroup = 2; // Default Admin Room

			$startDateTime = $this->makeThaiDateTime($ReserveRoomInfo['StartDateTime']);
			$endDateTime = $this->makeThaiDateTime($ReserveRoomInfo['EndDateTime']);

			$notificationType = NotificationService::getNotificationType($notificationTypeID);
			$adminGroup = $notificationType['NotificationGroup'];
			$user = UserService::getUser($ReserveRoomInfo['CreateBy']);
			

			$notification['RegionID'] = $RoomInfo['RegionID'];
			$notification['NotificationType'] = $notificationTypeID;
			$notification['NotificationStatus'] = 'Unseen';
			$notification['NotificationUrl'] = '#/roombooking/'.$ReserveRoomInfo['CreateBy'].'/'.$ReserveRoomInfo['RoomID'].'/' . 'null' .'/' . $reserveRoomID;
			$notification['NotificationText'] =  $user['FirstName'] . ' ' . $user['LastName'] . ' ' . $notificationType['NotificationType'] . ' ห้องประชุม : ' . $RoomInfo['RoomName'] . ' พื้นที่ : ' . $RoomInfo['region_name'] . ' ช่วงเวลา : ' . $startDateTime . 'น. ถึง ' . $endDateTime . 'น.';
			$notification['NotificationKeyID'] = $reserveRoomID;
			$notification['PushBy'] = $ReserveRoomInfo['CreateBy'];
			$notification['AdminGroup'] = $adminGroup;

			// To specific person who can see this notify by group ID
			$UserList = UserService::getUserByGroupAndRegionIDWithPermission($adminGroup, $RoomInfo['RegionID'], $ReserveRoomInfo['CreateBy']);
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

		private function generateNotificationMarkStatusData($ReserveRoomInfo, $RoomInfo, $reserveRoomID, $ReserveStatus){

			$notificationTypeID = $ReserveStatus=='Approve'?2:3; // Room mark status
			$adminGroup = 1; // General user 

			$startDateTime = $this->makeThaiDateTime($ReserveRoomInfo['StartDateTime']);
			$endDateTime = $this->makeThaiDateTime($ReserveRoomInfo['EndDateTime']);

			$notificationType = NotificationService::getNotificationType($notificationTypeID);
			$user = UserService::getUser($ReserveRoomInfo['AdminID']);
			$requestUser = UserService::getUser($ReserveRoomInfo['CreateBy']);
			$this->logger->info('Create by : '.$ReserveRoomInfo['CreateBy']);
			$this->logger->info($requestUser);
			$notification['RegionID'] = $requestUser['RegionID'];
			$this->logger->info(' 1. Region ID : '.$notification['RegionID']);
			$notification['NotificationType'] = $notificationTypeID;
			$notification['NotificationStatus'] = 'Unseen';
			$notification['NotificationUrl'] = '#/roombooking/'.$ReserveRoomInfo['CreateBy'].'/'.$ReserveRoomInfo['RoomID'].'/null/'.$reserveRoomID;
			$notification['NotificationText'] =  $user['FirstName'] . ' ' . $user['LastName'] . ' ' . $notificationType['NotificationType'] . ' ห้องประชุม : ' . $RoomInfo['RoomName'] . ' พื้นที่ : ' . $RoomInfo['region_name'] . ' ช่วงเวลา : ' . $startDateTime . 'น. ถึง ' . $endDateTime . 'น.';
			$notification['NotificationKeyID'] = $reserveRoomID;
			$notification['PushBy'] = $ReserveRoomInfo['AdminID'];
			$notification['AdminGroup'] = $adminGroup;
			$notification['ToSpecificPersonID'] = $ReserveRoomInfo['CreateBy'];
			$this->logger->info(' 2. Region ID : '.$notification['RegionID']);
			return $notification;
		}

		private function generateNotificationMarkStatusRoomDesData($ReserveRoomInfo, $RoomInfo, $reserveRoomID, $ReserveStatus){

			$notificationTypeID = $ReserveStatus=='Approve'?7:8; // Room mark status
			$adminGroup = 1; // General user 

			$startDateTime = $this->makeThaiDateTime($ReserveRoomInfo['StartDateTime']);
			$endDateTime = $this->makeThaiDateTime($ReserveRoomInfo['EndDateTime']);

			$notificationType = NotificationService::getNotificationType($notificationTypeID);
			$verifyUser = UserService::getUser($RoomInfo['VerifyBy']);
			$requestUser = UserService::getUser($ReserveRoomInfo['CreateBy']);

			$notification['RegionID'] = $requestUser['RegionID'];
			$notification['NotificationType'] = $notificationTypeID;
			$notification['NotificationStatus'] = 'Unseen';
			$notification['NotificationUrl'] = '#/roombooking/'.$ReserveRoomInfo['CreateBy'].'/'.$ReserveRoomInfo['RoomID'].'/null/'.$ReserveRoomInfo['ReserveRoomID'];
			$notification['NotificationText'] =  $verifyUser['FirstName'] . ' ' . $verifyUser['LastName'] . ' ' . $notificationType['NotificationType'] . ' ห้องประชุม : ' . $RoomInfo['RoomName'] . ' พื้นที่ : ' . $RoomInfo['RegionName'] . ' ช่วงเวลา : ' . $startDateTime . 'น. ถึง ' . $endDateTime . 'น.';
			$notification['NotificationKeyID'] = $ReserveRoomInfo['ReserveRoomID'];
			$notification['PushBy'] = $RoomInfo['VerifyBy'];
			$notification['AdminGroup'] = $adminGroup;
			$notification['ToSpecificPersonID'] = $ReserveRoomInfo['CreateBy'];
			// To request person
			$notificationList[0] = $notification;
			// To device admin
			$notificationList[1] = $notification;
			$notificationList[1]['ToSpecificPersonID'] = $RoomInfo['DeviceAdminID'];
			// // To food admin
			// $notificationList[2] = $notification;
			return $notificationList;
		}

		private function generateEmailContent($RoomInfo, $ReserveRoomInfo, $InternalAttendeeList, $ExternalAttendeeList, $DeviceList, $FoodList, $RequestUser){
            
            $content = '<h3><b>ยืนยันการใช้ห้องประชุม ดังนี้</b></h3><br>';

            if($ReserveRoomInfo['ReserveStatus'] == 'Approve'){
                $content .= '<h4><span style="color:green;"><b>สถานะ</b> อนุมัติ</span></h4><br>';
            }
            else if($ReserveRoomInfo['ReserveStatus'] == 'Reject'){
                $content .= '<h4><span style="color:red;"><b>สถานะ</b> ไม่อนุมัติ เนื่องจาก '.$ReserveRoomInfo['AdminComment'].'</span></h4><br>';
            }

            $content .= '<b>พื้นที่ : </b>' . $RoomInfo['region_name'] . '<br>';
            $content .= '<b>ห้องประชุม : </b>' . $RoomInfo['RoomName'] . '<br>';
            $content .= '<b>ช่วงเวลา : </b>' . $this->makeThaiDateTime($ReserveRoomInfo['StartDateTime'])
                     .' น. - ' . $this->makeThaiDateTime($ReserveRoomInfo['EndDateTime']) . ' น.<br>';
            $content .=  '<b>หัวข้อการประชุม : </b>' . $ReserveRoomInfo['TopicConference'] . '<br>';
            $content .=  '<b>รายชื่อผู้ร่วมประชุม : </b>' . $this->groupInternalAttendee($InternalAttendeeList) . ',' . $this->groupExternalAttendee($ExternalAttendeeList) . '<br>';
            $content .=  '<b>อุปกรณ์ที่ใช้ : </b>' . $this->groupDevices($DeviceList) . '<br>';
            $content .=  '<b>อาหารหลัก : </b>' . $this->groupFoods($FoodList) . '<br>';
            $content .=  '<b>อาหารว่าง : </b>' . $this->getSoftFood($ReserveRoomInfo['SnackStatus']) . '<br>';
            $content .=  '<b>หมายเหตุ : </b>' . nl2br($ReserveRoomInfo['Remark']) . '<br>';
            $content .=  '<b>ผู้จองห้องประชุม : </b>' . $RequestUser['FirstName'] . ' ' .$RequestUser['LastName'] . '<br>';
            $content .=  '<b>เบอร์โทรติดต่อ : </b>' . $RequestUser['Mobile'] . '<br>';
            $content .= '<br>e-mail ฉบับนี้ถูกส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ (Please do not reply this e-mail)';
            return $content;

        }

        private function groupDevices($DeviceList){
            $devices = [];
            foreach ($DeviceList as $key => $value) {
                $devices[] = $value['DeviceName'] . ' จำนวน ' . $value['Amount'];
            }
            return implode(', ' , $devices);
        }

        private function groupFoods($FoodList){
            $foods = [];
            foreach ($FoodList as $key => $value) {
                $foods[] = $value['FoodName'] . ' จำนวน ' . $value['Amount'];
            }
            return implode(', ' , $foods);
        }

        private function getSoftFood($foodType){
        	switch ($foodType) {
        		case 'Breakfast':
        			return 'เช้า';
        			break;
        		case 'Lunch':
        			return 'บ่าย';
        			break;
        		case 'Both':
        			return 'เช้า / บ่าย';
        			break;
        		default:
        			return 'ไม่มี';
        			break;
        	}
        }

        private function groupInternalAttendee($InternalAttendeeList){
            $internalAttendees = [];
            foreach ($InternalAttendeeList as $key => $value) {
                $internalAttendees[] = $value['FirstName'] . ' ' . $value['LastName'];
            }
            return implode(', ' , $internalAttendees);
        }

        private function groupExternalAttendee($ExternalAttendeeList){
            $externalAttendees = [];
            foreach ($ExternalAttendeeList as $key => $value) {
                $externalAttendees[] = $value['AttendeeName'];
            }
            return implode(', ' , $externalAttendees);
        }

		private function makeThaiDateTime($date){
			$date = str_replace('.000', '', $date);
			$dateTimeObj = split(' ', $date);
			$dateObj = $dateTimeObj[0]; 
			$timeObj = $dateTimeObj[1];

			$dateObj = split('-' , $dateObj);
			$day = $dateObj[2];
			$month = $dateObj[1];
			$year = $dateObj[0];

			$timeObj = split(':' , $timeObj);
			$hour = $timeObj[0];
			$minute = $timeObj[1];

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

			return $day . ' ' . $month . ' ' . $year . ' ' . $hour . ':' . $minute;
		}

		private function makeShortDateTime($date){
			$date = str_replace('.000', '', $date);
			$dateTimeObj = split(' ', $date);
			$dateObj = $dateTimeObj[0]; 
			$timeObj = $dateTimeObj[1];

			$dateObj = split('-' , $dateObj);
			$day = $dateObj[2];
			$month = $dateObj[1];
			$year = $dateObj[0];

			$timeObj = split(':' , $timeObj);
			$hour = $timeObj[0];
			$minute = $timeObj[1];

			return $day . '/' . $month . '/' . $year . ' ' . $hour . ':' . $minute;
		}
    }
    
?>