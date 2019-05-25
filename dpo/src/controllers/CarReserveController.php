<?php

	namespace App\Controller;
    
    use App\Service\CarReserveService;
    use App\Service\UserService;
    use App\Service\NotificationService;
    use App\Controller\Mailer;
    class CarReserveController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        public function getCarListDetail($request, $response, $args){
            try{
                $parsedBody = $request->getParsedBody();
                $condition = $parsedBody['condition'];

                $ReserveCarInfo = CarReserveService::getCarListDetail($condition);
                
                $this->data_result['DATA']['List'] = $ReserveCarInfo;
                
                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function getCarReserveDetail($request, $response, $args){
            try{
                $reserveCarID = filter_var($request->getAttribute('reserveCarID'), FILTER_SANITIZE_NUMBER_INT);
                $ReserveCarInfo = CarReserveService::getCarReserveDetail($reserveCarID);
                $this->data_result['DATA']['ReserveCarInfo'] = $ReserveCarInfo;
                
                // Get car detail
                if($ReserveCarInfo['CarID'] != '' && $ReserveCarInfo['CarID'] != null){
                    $CarInfo = CarReserveService::getCarDetail($ReserveCarInfo['CarID']);
                    $this->data_result['DATA']['CarInfo'] = $CarInfo;
                }
                
                // Get Travellers
                $Travellers = CarReserveService::getTravellerList($reserveCarID);
                $this->data_result['DATA']['TravellerList'] = $Travellers;

                // Get Internal Driver
                $InternalDriver = CarReserveService::getInternalDriver($reserveCarID);
                $this->data_result['DATA']['InternalDriver'] = $InternalDriver;

                // Get External Driver
                $ExternalDriver = CarReserveService::getExternalDriver($reserveCarID);
                $this->data_result['DATA']['ExternalDriver'] = $ExternalDriver;

                // Get Request Person Info
                $RequestUser = UserService::getUserFullData($ReserveCarInfo['CreateBy']);
                unset($RequestUser['Password']); 
                // Get Verify Person Info
                $VerifyUser = UserService::getUser($ReserveCarInfo['AdminID']);
                unset($VerifyUser['Password']); 
                $this->data_result['DATA']['RequestUser'] = $RequestUser;
                $this->data_result['DATA']['VerifyUser'] = $VerifyUser;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }
       
        public function getProvinceList($request, $response, $args){
        	try{

                $ProvinceList = CarReserveService::getProvinceList();
                $this->data_result['DATA'] = $ProvinceList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function getMaxSeatAmount($request, $response, $args){
        	try{

                $SeatAmountList = CarReserveService::getMaxSeatAmount();
                $this->data_result['DATA'] = $SeatAmountList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function getReserveCartypeList($request, $response, $args){
            try{

                $CarTypeList = CarReserveService::getReserveCartypeList();
                $this->data_result['DATA'] = $CarTypeList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function getCarsInRegion($request, $response, $args){
            try{

                $regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT); 
                $findDate = $request->getAttribute('findDate'); 
                $CarTypeList = CarReserveService::getCarsInRegion($regionID,$findDate);
                $this->data_result['DATA'] = $CarTypeList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function updateTraveller($request, $response, $args){
        	try{

        		$parsedBody = $request->getParsedBody();
                //print_r($parsedBody['UserID']);exit;
                $traveller = CarReserveService::updateTraveller($parsedBody['UserID']);
                $this->data_result['DATA'] = $traveller;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function updateReserveCarInfo($request, $response, $args){
        	try{

        		$parsedBody = $request->getParsedBody();
                $ReserveCarInfo = $parsedBody['ReserveCarInfo'];
                $TravellerList = $parsedBody['TravellerList'];
                //print_r($parsedBody);exit;
                $ReserveCarID = CarReserveService::updateReserveCarInfo($ReserveCarInfo);

                foreach ($TravellerList as $key => $value) {
                    $traveller['TravellerID'] = $value['TravellerID'];
                    $traveller['ReserveCarID'] = $ReserveCarID;
                    $traveller['UserID'] = $value['UserID'];
                    $traveller['CreateBy'] = $value['CreateBy'];
                    CarReserveService::updateTraveller($traveller);
                }
                $this->data_result['DATA']['ReserveCarID'] = $ReserveCarID;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function deleteTraveller($request, $response, $args){
            try{
                
                $travellerID = filter_var($request->getAttribute('travellerID'), FILTER_SANITIZE_NUMBER_INT); 
                
                //die();
                $result = CarReserveService::deleteTraveller($travellerID);
                $this->data_result['DATA'] = $result;
                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
        }

        public function cancelReserveCar($request, $response, $args){
            try{

                $parsedBody = $request->getParsedBody();
                $reserveCarID = filter_var($parsedBody['reserveCarID'], FILTER_SANITIZE_NUMBER_INT);
                // Delete traveller
                CarReserveService::cancelTraveller($reserveCarID);
                // Delete Internal driver
                CarReserveService::cancelInternalDriver($reserveCarID);
                // Delete External driver
                CarReserveService::cancelExternalDriver($reserveCarID);
                // Delete Notification
                CarReserveService::cancelReserveCarNotification($reserveCarID);
                // Delete Reserve car
                CarReserveService::cancelReserveCar($reserveCarID);

                $this->data_result['DATA']['ReserveCarID'] = $ReserveCarID;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function requestReserveCar($request, $response, $args){
            try{

                $parsedBody = $request->getParsedBody();
                
                $ReserveCarInfo = $parsedBody['ReserveCarInfo'];
                $TravellerList = $parsedBody['TravellerList'];
                $RequestUser = $parsedBody['RequestUser'];
                // print_r($ReserveCarInfo);
                // print_r($RequestUser);
                // exit;
                $ReserveCarID = CarReserveService::updateReserveCarInfo($ReserveCarInfo);
                $ReserveCarInfo['ReserveCarID'] = $ReserveCarID;
                if(!empty($ReserveCarID)){
                    // Add Travellers
                    foreach ($TravellerList as $key => $value) {
                        $traveller['ReserveCarID'] = $ReserveCarID;
                        $traveller['UserID'] = $value['UserID'];
                        $traveller['CreateBy'] = $value['CreateBy'];
                        CarReserveService::updateTraveller($traveller);
                    }

                    $result = CarReserveService::markStatus($ReserveCarInfo['ReserveCarID'], 'Request', '');
                    $notifications = $this->generateNotificationReqData($ReserveCarInfo, $RequestUser);
                    foreach ($notifications as $key => $value) {
                        //print_r($value);
                        NotificationService::pushNotification($value);
                    }

                    $this->data_result['DATA'] = 'Request';

                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Cannot request. Please try again';
                }

                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function checkSameDestination($request, $response, $args){
            try{

                $parsedBody = $request->getParsedBody();
                
                $ReserveCarInfo = $parsedBody['Data'];
                
                $ReserveCarID = $ReserveCarInfo['ReserveCarID'];
                $RegionID = $ReserveCarInfo['RegionID'];
                $ProvinceID = $ReserveCarInfo['ProvinceID'];
                $StartDateTime = $ReserveCarInfo['StartDateTime'];

                $Data = CarReserveService::checkSameDestination($ReserveCarID, $RegionID, $ProvinceID, $StartDateTime);
                
                $this->data_result['DATA'] = $Data;

                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        public function chooseSameDestination($request, $response, $args){
            try{

                $parsedBody = $request->getParsedBody();
                
                $TravellerList = $parsedBody['TravellerList'];
                $ReserveCarID = $parsedBody['choose_destiny'];
                
                $traveller_amount = 0;
                if(!empty($ReserveCarID)){
                    // Add Travellers
                    foreach ($TravellerList as $key => $value) {
                        $traveller['ReserveCarID'] = $ReserveCarID;
                        $traveller['UserID'] = $value['UserID'];
                        $traveller['CreateBy'] = $value['CreateBy'];
                        CarReserveService::updateTraveller($traveller);
                        $traveller_amount++;
                    }
                }

                // Update Total traveller
                $Data = CarReserveService::updateTotalTraveller($ReserveCarID, $traveller_amount);
                
                $this->data_result['DATA'] = $Data;

                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }    
        }

        private function chkDuplicateTime($reserveCarID, $carID, $startDateTime, $endDateTime){
            $chkStartDateTime = date('Y-m-d H:i:s.000', strtotime("+1 minutes", strtotime($startDateTime)));
            $chkEndDateTime = date('Y-m-d H:i:s.000', strtotime("-1 minutes", strtotime($endDateTime)));
            return CarReserveService::checkDuplicateTime($reserveCarID, $carID, $chkStartDateTime, $chkEndDateTime);
        }

        public function adminUpdateCarStatus($request, $response, $args){
            
            try{
                
                $parsedBody = $request->getParsedBody();
                $ReserveCarInfo = $parsedBody['ReserveCarInfo'];
                $TravellerList = $parsedBody['TravellerList'];
                $InternalDriver = $parsedBody['InternalDriver'];
                $ExternalDriver = $parsedBody['ExternalDriver'];
                // print_r($request->getParsedBody());
                // $this->logger->info($ReserveCarInfo);
                if($ReserveCarInfo['ReserveStatus'] == 'Approve'){
                    $duplicate = $this->chkDuplicateTime($ReserveCarInfo['ReserveCarID'], $ReserveCarInfo['CarID'], $ReserveCarInfo['StartDateTime'], $ReserveCarInfo['EndDateTime']);
                }
                // print_r($duplicate);
                // exit;
                if(trim($duplicate[0]['ReserveCarID']) == ''){
                    //exit;
                    $reserveCarID = CarReserveService::adminUpdateCarStatus($ReserveCarInfo);
                    $this->data_result['DATA']['ReserveCarID'] = $reserveCarID;

                    // Update driver if exist
                    if($ReserveCarInfo['ReserveStatus'] == 'Approve' && $ReserveCarInfo['DriverType'] == 'Internal'){
                        CarReserveService::updateInternalDriver($InternalDriver, $reserveCarID, $ReserveCarInfo['AdminID']);
                    }else if($ReserveCarInfo['ReserveStatus'] == 'Approve' && $ReserveCarInfo['DriverType'] == 'External'){
                        CarReserveService::updateExternalDriver($ExternalDriver, $reserveCarID, $ReserveCarInfo['AdminID']);
                    }
                    
                    // Push notify
                    $notification = $this->generateNotificationMarkStatusData($ReserveCarInfo);
                    NotificationService::pushNotification($notification);

                    // Send mail
                    if($ReserveCarInfo['ReserveStatus'] == 'Approve' || $ReserveCarInfo['ReserveStatus'] == 'Reject'){
                        $mailer = new Mailer;
                        $mailer->setSubject("DPO :: แจ้งการจองพาหนะ ภารกิจ : " . $ReserveCarInfo['Mission'] );
                        $mailer->isHtml(true);
                        $mailer->setHTMLContent($this->generateEmailContent($ReserveCarInfo, $TravellerList, $InternalDriver, $ExternalDriver));

                        // Set receiver

                        // Get Create by Email
                        $CreateBy = UserService::getUser($ReserveCarInfo['CreateBy']);
                        if(!empty($CreateBy['Email'])){
                            $mailer->setReceiver($CreateBy['Email']);
                        }

                        // Get Traverllers Email
                        foreach ($TravellerList as $key => $value) {
                            if(!empty($value['Email'])){
                                $mailer->setReceiver($value['Email']);
                            }
                        }

                        // Get Internal Driver Email
                        if($ReserveCarInfo['DriverType'] == 'Internal' && !empty($InternalDriver['Email'])){
                            $mailer->setReceiver($InternalDriver['Email']);
                        }

                        if($mailer->sendMail()){
                            $this->logger->info('Sent mail Car success');
                        }else{
                            $this->logger->info('Sent mail Car failed');
                        }
                    }

                    // $NotificationTypeList = [1,6];

                    // if($ReserveStatus == 'Approve'){
                    //     $descriptions = ' (ได้รับการอนุมัติแล้ว)';
                    // }else if($ReserveStatus == 'Reject'){
                    //     $descriptions = ' (ไม่ได้รับการอนุมัติ)';
                    // }

                    // $NotificationData = NotificationService::getNotificationRoomData($ReserveRoomID, $NotificationTypeList);
                    // $NotificationData['NotificationText'] .= $descriptions;

                    // CarReserveService::updateReserveCarAdminRecv($pullBy, $Notification['NotificationKeyID']);

                    // $_value = $NotificationData;
                    // $_value['NotifcationID'] = '';
                    // $_value['PushBy'] = $ReserveCarInfo['AdminID'];
                    // $_value['ToSpecificPersonID'] = $ReserveCarInfo['UserID'];
                    // $_value['NotificationStatus'] = 'Unseen';

                    // NotificationService::pushNotification($_value);

                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA']['MSG'] = 'มีการจองพาหนะในช่วงเวลา ' . $duplicate[0]['StartDateTime'] . ' - ' . $duplicate[0]['EndDateTime'] . ' ในภารกิจ ' . $duplicate[0]['Mission'] . ' แล้ว';
                }
                
                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            } 
        }

        private function generateEmailContent($ReserveCarInfo, $TravellerList, $InternalDriver, $ExternalDriver){
            
            $content = '<h3><b>รายละเอียดการจองพาหนะ ดังนี้</b></h3><br>';

            if($ReserveCarInfo['ReserveStatus'] == 'Approve'){
                $content .= '<h4><span style="color:green;"><b>สถานะ</b> อนุมัติ</span></h4><br>';
            }
            else if($ReserveCarInfo['ReserveStatus'] == 'Reject'){
                $content .= '<h4><span style="color:red;"><b>สถานะ</b> ไม่อนุมัติ เนื่องจาก '.$ReserveCarInfo['AdminComment'].'</span></h4><br>';
            }

            $content .= '<b>พื้นที่ : </b>' . $ReserveCarInfo['RegionName'] . '<br>';
            $content .= '<b>สถานที่ปลายทาง : </b>' . $ReserveCarInfo['Destination'] . '<br>';
            $content .= '<b>วันที่เดินทางไป : </b>' . $this->makeThaiDate($ReserveCarInfo['StartDateTime']) 
                     .' เวลา ' . $this->makeThaiTime($ReserveCarInfo['StartDateTime']) . ' น.<br>';

            $content .= '<b>วันที่เดินทางกลับ : </b>' . $this->makeThaiDate($ReserveCarInfo['EndDateTime'])
                     .' เวลา ' . $this->makeThaiTime($ReserveCarInfo['EndDateTime']) . ' น.<br>';
            $content .=  '<b>ภารกิจ : </b>' . $ReserveCarInfo['Mission'] . '<br>';
            $content .=  '<b>ผู้ร่วมเดินทาง : </b>' . $this->groupTravellers($TravellerList) . '<br>';
            $content .=  '<b>จำนวนผู้เดินทาง : </b>' . count($TravellerList) . ' คน<br>';
            $content .=  '<b>หมายเหตุ : </b>' . nl2br($ReserveCarInfo['Remark']) . '<br>';

            if($ReserveCarInfo['DriverType'] == 'Internal'){
                $content .=  '<b>พนักงานขับยานพาหนะ : </b>' . $InternalDriver['Name'] . '<br>';
                $content .=  '<b>เบอร์โทรติดต่อ : </b>' . $InternalDriver['Mobile'] . '<br>';
            }else if($ReserveCarInfo['DriverType'] == 'External'){
                $content .=  '<b>พนักงานขับยานพาหนะสัญญาจ้าง : </b>' . $ExternalDriver['DriverName'] . '<br>';
                $content .=  '<b>เบอร์โทรติดต่อ : </b>' . $ExternalDriver['Mobile'] . '<br>';
            }

            $content .= '<br>e-mail ฉบับนี้ถูกส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ (Please do not reply this e-mail)';
            return $content;

        }

        private function groupTravellers($TravellerList){
            $travellers = [];
            foreach ($TravellerList as $key => $value) {
                $travellers[] = $value['FirstName'] . ' ' . $value['LastName'] . ' เบอร์โทรฯ : '. $value['Mobile'];
            }
            return implode(', ' , $travellers);
        }

        private function generateNotificationReqData($ReserveCarInfo, $RequestUser){

            $notificationTypeID = 9; // Car request
            $adminGroup = 3; // Admin Car

            $startDateTime = $this->makeThaiDateTime($ReserveCarInfo['StartDateTime']);
            $endDateTime = $this->makeThaiDateTime($ReserveCarInfo['EndDateTime']);

            $notificationType = NotificationService::getNotificationType($notificationTypeID);
            //$user = UserService::getUser($ReserveCarInfo['CreateBy']);
            

            $notification['RegionID'] = $ReserveCarInfo['RegionID'];
            $notification['NotificationType'] = $notificationTypeID;
            $notification['NotificationStatus'] = 'Unseen';
            $notification['NotificationUrl'] = '#/vehicles/'.$ReserveCarInfo['ReserveCarID'];
            $notification['NotificationText'] =  $RequestUser['FirstName'] . ' ' . $RequestUser['LastName'] . ' ' . $notificationType['NotificationType'] . ' พื้นที่ : ' . $ReserveCarInfo['RegionName'] . ' ช่วงเวลา : ' . $startDateTime . 'น. ถึง ' . $endDateTime . 'น.';
            $notification['NotificationKeyID'] = $ReserveCarInfo['ReserveCarID'];
            $notification['PushBy'] = $ReserveCarInfo['CreateBy'];
            $notification['AdminGroup'] = $adminGroup;

            // To specific person who can see this notify by group ID
            $UserList = UserService::getUserByGroupAndRegionIDWithPermission($adminGroup, $ReserveCarInfo['RegionID'], $ReserveCarInfo['CreateBy']);
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

        private function generateNotificationMarkStatusData($ReserveCarInfo){

            $notificationTypeID = $ReserveCarInfo['ReserveStatus']=='Approve'?10:11; // Room mark status
            $adminGroup = 1; // General user 

            $startDateTime = $this->makeThaiDateTime($ReserveCarInfo['StartDateTime']);
            $endDateTime = $this->makeThaiDateTime($ReserveCarInfo['EndDateTime']);

            $notificationType = NotificationService::getNotificationType($notificationTypeID);
            $user = UserService::getUser($ReserveCarInfo['AdminID']);
            //$requestUser = UserService::getUser($ReserveCarInfo['CreateBy']);

            $notification['RegionID'] = $ReserveCarInfo['RegionID'];
            $notification['NotificationType'] = $notificationTypeID;
            $notification['NotificationStatus'] = 'Unseen';
            $notification['NotificationUrl'] = '#/vehicles/'.$ReserveCarInfo['ReserveCarID'];
            $notification['NotificationText'] =  $user['FirstName'] . ' ' . $user['LastName'] . ' ' . $notificationType['NotificationType'] . ' พื้นที่ : ' . $ReserveCarInfo['RegionName'] . ' ช่วงเวลา : ' . $startDateTime . 'น. ถึง ' . $endDateTime . 'น.';
            $notification['NotificationKeyID'] = $ReserveCarInfo['ReserveCarID'];
            $notification['PushBy'] = $ReserveCarInfo['AdminID'];
            $notification['AdminGroup'] = $adminGroup;
            $notification['ToSpecificPersonID'] = $ReserveCarInfo['CreateBy'];
            
            return $notification;
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

        private function makeThaiTime($date){
            $date = str_replace('.000', '', $date);
            $dateTimeObj = split(' ', $date);
            $timeObj = $dateTimeObj[1];

            $timeObj = split(':' , $timeObj);
            $hour = $timeObj[0];
            $minute = $timeObj[1];

            return $hour . ':' . $minute;
        }
    }
         

?>