<?php

    namespace App\Controller;
    
    use App\Service\RepairService;
    use App\Service\NotificationService;
    use App\Service\UserService;
    use App\Service\RunningNoService;
    use App\Service\PermissionService;

    class RepairController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
       
        public function getRepairTypeList($request, $response, $args){
            
            try{

                $mode = $request->getAttribute('mode');
                $UserID = $request->getAttribute('UserID');

                if($mode != 'view'){
                    // Check permission
                    $user_permission = PermissionService::checkPermission($UserID);
                    
                    $valid = false;
                    foreach ($user_permission as $key => $value) {
                        if($value['AdminGroupID'] == '0' || $value['AdminGroupID'] == '7'){
                            $valid = true;
                        } 
                    }
                    if(!$valid){
                        $this->data_result['STATUS'] = 'ERROR';
                        return $this->returnResponse(401, $this->data_result, $response);  
                    }
                }

                $RepairList = RepairService::getRepairTypeList($mode);
                $this->data_result['DATA'] = $RepairList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getRepairType($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $RepairType = RepairService::getRepairType($ID);
                $this->data_result['DATA'] = $RepairType;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateRepairType($request, $response, $args){
            
            try{

                $parsedBody = $request->getParsedBody();
                $parsedBody = $parsedBody['updateObj'];

                $Repair = RepairService::updateRepairType($parsedBody);
                $this->data_result['DATA']['RepairedTypeID'] = $Repair['RepairedTypeID'];
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteRepairType($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = RepairService::deleteRepairType($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getRepairTitleList($request, $response, $args){
            
            try{

                $mode = $request->getAttribute('mode');
                $RepairedTypeID = filter_var($request->getAttribute('RepairedTypeID'), FILTER_SANITIZE_NUMBER_INT);
                $RepairList = RepairService::getRepairTitleList($mode, $RepairedTypeID);
                $this->data_result['DATA'] = $RepairList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getRepairTitle($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $RepairTitle = RepairService::getRepairTitle($ID);
                $this->data_result['DATA'] = $RepairTitle;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
        
        public function updateRepairTitle($request, $response, $args){
            
            try{
                $parsedBody = $request->getParsedBody();
                $parsedBody = $parsedBody['updateObj'];

                $Repair = RepairService::updateRepairTitle($parsedBody);
                $this->data_result['DATA']['RepairedTitleID'] = $Repair['RepairedTitleID'];
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteRepairTitle($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = RepairService::deleteRepairTitle($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getRepairIssueList($request, $response, $args){
            
            try{

                $mode = $request->getAttribute('mode');
                $RepairedTitleID = filter_var($request->getAttribute('RepairedTitleID'), FILTER_SANITIZE_NUMBER_INT);
                $RepairList = RepairService::getRepairIssueList($mode, $RepairedTitleID);
                $this->data_result['DATA'] = $RepairList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getRepairIssue($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $RepairIssue = RepairService::getRepairIssue($ID);
                $this->data_result['DATA'] = $RepairIssue;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
        
        public function updateRepairIssue($request, $response, $args){
            
            try{
                $parsedBody = $request->getParsedBody();
                $parsedBody = $parsedBody['updateObj'];

                $Repair = RepairService::updateRepairIssue($parsedBody);
                $this->data_result['DATA']['RepairedIssueID'] = $Repair['RepairedIssueID'];
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteRepairIssue($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = RepairService::deleteRepairIssue($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getRepairSubIssueList($request, $response, $args){
            
            try{

                $mode = $request->getAttribute('mode');
                $RepairedIssueID = filter_var($request->getAttribute('RepairedIssueID'), FILTER_SANITIZE_NUMBER_INT);
                $RepairList = RepairService::getRepairSubIssueList($mode, $RepairedIssueID);
                $this->data_result['DATA'] = $RepairList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getRepairSubIssue($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $RepairSubIssue = RepairService::getRepairSubIssue($ID);
                $this->data_result['DATA'] = $RepairSubIssue;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
        
        public function updateRepairSubIssue($request, $response, $args){
            
            try{
                $parsedBody = $request->getParsedBody();
                $parsedBody = $parsedBody['updateObj'];

                $Repair = RepairService::updateRepairSubIssue($parsedBody);
                $this->data_result['DATA']['RepairedSubIssueID'] = $Repair['RepairedSubIssueID'];
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteRepairSubIssue($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = RepairService::deleteRepairSubIssue($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getRepair($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('RepairedID'), FILTER_SANITIZE_NUMBER_INT);
                $Repair = RepairService::getRepair($ID);
                $this->data_result['DATA'] = $Repair;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateRepair($request, $response, $args){
            
            try{

                $parsedBody = $request->getParsedBody();
                $parsedBody = $parsedBody['updateObj'];

                $Repair = RepairService::updateRepair($parsedBody);
                $this->data_result['DATA']['RepairedID'] = $Repair['RepairedID'];
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateAdminReceiveRepair($request, $response, $args){
            
            try{
                $parsedBody = $request->getParsedBody();
                $AdminID = filter_var($parsedBody['AdminID'], FILTER_SANITIZE_NUMBER_INT);
                $parsedBody = $parsedBody['updateObj'];

                $Repair = RepairService::updateAdminReceiveRepair($parsedBody, $AdminID);
                $this->data_result['DATA']['ReceiveDateTime'] = $Repair['ReceiveDateTime'];
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateRepairAdmin($request, $response, $args){
            // error_reporting(E_ERROR);
            // error_reporting(E_ALL);
            // ini_set('display_errors','On'); 
            try{
                $parsedBody = $request->getParsedBody();
                $RepairStatus = $parsedBody['RepairStatus'];
                $parsedBody = $parsedBody['updateObj'];

                $Repair = RepairService::updateRepairAdmin($parsedBody, $RepairStatus);
                
                // Prepare Notification data
                $RepairNotify = RepairService::getRepairAdminForNotify($Repair['RepairedID']);
                $notifications =  $this->generateNotificationData($RepairNotify, 'PERSON', $Repair['AdminID'], 1);
                NotificationService::pushNotification($notifications);

                if($RepairStatus == 'Suspend' || $RepairStatus == 'Finish'){
                    $mailer = new Mailer;
                    $mailer->setSubject("DPO :: แจ้งความคืบหน้างานซ่อม : " . $RepairNotify['RepairedSubIssueName'] . " ( " . $RepairNotify['RepairedCode'] .' ) ' );
                    $mailer->isHtml(true);
                    $mailer->setHTMLContent($this->generateEmailToCreatorContent($RepairNotify, $RepairStatus));
                    $User = UserService::getUser($Repair['CreateBy']);
                    $mailer->setReceiver($User['Email']);

                    if($mailer->sendMail()){
                        $this->logger->info('Sent mail Repair success');
                    }else{
                        $this->logger->info('Sent mail Repair failed');
                    }
                }

                $this->data_result['DATA'] = $Repair;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateStatusRepair($request, $response, $args){
            
            try{
                $reqIPAddress = $request->getAttribute('ip_address');
                
                $parsedBody = $request->getParsedBody();
                $status = trim($parsedBody['status']);
                
                if($status != ''){

                    $parsedBody = $parsedBody['updateObj'];
                    $parsedBody['RepairedStatus'] = $status;

                    // get Prefix ref
                    $RepairTitle = RepairService::getRepairTitle($parsedBody['RepairedTitleID']);
                    $ref = $this->generateRefNumber($RepairTitle['RepairedTitleCode']);
                    //echo $ref;die();
                    $parsedBody['RepairedCode'] = $ref;

                    $Repair = RepairService::updateRepair($parsedBody, $reqIPAddress);
                    


                    // Prepare Notification data
                    $adminGroupID = 7;
                    $RepairNotify = RepairService::getRepairForNotify($Repair['RepairedID']);
                    $notifications =  $this->generateNotificationData($RepairNotify, 'GROUP', $Repair['CreateBy'], $adminGroupID);
                    // print_r($notifications);
                    // die();
                    foreach ($notifications as $key => $value) {
                        NotificationService::pushNotification($value);
                    }

                    if($status == 'Request'){
                        $mailer = new Mailer;
                        $mailer->setSubject("DPO :: แจ้งปัญหา แจ้งซ่อม : " . $RepairNotify['RepairedTypeName'] . " ( " . $RepairNotify['RepairedCode'] .' ) พื้นที่ ' . $RepairNotify['RegionName'] );
                        $mailer->isHtml(true);
                        $mailer->setHTMLContent($this->generateEmailToAdminContent($RepairNotify));

                        // Set receivers
                        $UserList = UserService::getUserByGroupAndRegionIDWithPermission($adminGroupID, $Repair['RegionID']);
                        foreach ($UserList as $key => $value) {
                            if(!empty($value['Email'])){
                                $mailer->setReceiver($value['Email']);    
                            }
                        }

                        $mailer->sendMail();
                    }

                    $this->data_result['DATA']['RepairedID'] = $Repair['RepairedID'];

                }
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function notify24Hours($request, $response, $args){

            try{

                $dateCheck = date('Y-m-d H:i:s.000');
                $this->logger->info('----------- Script Notify 24 Hours At : '.$dateCheck . ' ------------');

                $RepairList = RepairService::getRepairForNotify24Hours($dateCheck);
                $this->logger->info("Total repair to notify " . count($RepairList));
                
                if(count($RepairList) > 0){
                    $this->logger->info("Prepare sending e-mail : ". $repairData['RepairedCode']);
                    foreach ($RepairList as $repairKey => $repairData) {
                           
                        $mailer = new Mailer;
                        $mailer->setSubject("DPO :: แจ้งคำร้องครบ 24 ชั่วโมง หลังการแจ้งซ่อม : " . $repairData['RepairedTypeName'] . " ( " . $repairData['RepairedCode'] .' ) พื้นที่ ' . $repairData['RegionName'] );
                        $mailer->isHtml(true);
                        $mailer->setHTMLContent($this->generateEmailToAdminBossContent($repairData));

                        // Set receivers (Fix admin group id = 7)
                        $adminGroupID = 7;
                        $UserList = UserService::getUserByGroupAndRegionIDWithPermission($adminGroupID, $repairData['RegionID']);
                        foreach ($UserList as $key => $value) {
                            if(!empty($value['Email'])){
                                $this->logger->info("Notify to : " . $value['Email']);
                                $mailer->setReceiver($value['Email']);
                            }
                        }

                        if($mailer->sendMail()){
                            $this->logger->info("E-mail has been sent");
                            // Update notify 24 hrs status
                            RepairService::updateRepairNotify24Hours($repairData['RepairedID']);
                        }else{
                            $this->logger->info("E-mail could not been sent ");
                        }

                    }
                }
                $this->logger->info("----------- Finish Script Notify 24 Hours ------------");
                $this->data_result['DATA'] = 'Notify Success';
                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        private function generateEmailToAdminBossContent($DataMail){
            
            $content = '<h3><b>แจ้งปัญหา แจ้งซ่อม ดังนี้</b></h3><br>';
            $content .= '<b>รหัสแจ้งซ่อม : </b>' . $DataMail['RepairedCode'] . '<br>';
            $content .= '<b>พื้นที่ : </b>' . $DataMail['RegionName'] . '<br>';
            $content .= '<b>ประเภทงานซ่อม : </b>' . $DataMail['RepairedTypeName'] . '<br>';
            $content .= '<b>รายละเอียด : </b>' . $DataMail['RepairedDetail'] . '<br>';
            $content .= '<b>ผู้แจ้งซ่อม : </b>' . $DataMail['FirstName'] . ' ' . $DataMail['LastName'] . '<br>';
            $content .= '<b>เบอร์โทรติดต่อ : </b>' . $DataMail['Mobile'] . '<br>';
            $content .= '<b>อีเมล : </b>' . $DataMail['Email'] . '<br>';
            $content .= '<b>แจ้งเตือน : </b>ครบ 24 ชั่วโมงหลังการส่งคำร้องแจ้งซ่อม  ตั้งแต่ ' . $this->makeThaiDate($DataMail['UpdateDateTime'], 'true') . 'น. ยังไม่มีการดำเนินการตอบรับจากเจ้าหน้าที่<br>';

            $content .= '<br>e-mail ฉบับนี้ถูกส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ (Please do not reply this e-mail)';
            return $content;

        }

        private function generateEmailToAdminContent($DataMail){
            
            $content = '<h3><b>แจ้งปัญหา แจ้งซ่อม ดังนี้</b></h3><br>';
            $content .= '<b>รหัสแจ้งซ่อม : </b>' . $DataMail['RepairedCode'] . '<br>';
            $content .= '<b>พื้นที่ : </b>' . $DataMail['RegionName'] . '<br>';
            $content .= '<b>ประเภทงาน : </b>' . $DataMail['RepairedTypeName'] . ' > ' . $DataMail['RepairedTitleName'] . ' > ' . $DataMail['RepairedIssueName'] . ' > ' . $DataMail['RepairedSubIssueName'] . '<br>';
            $content .= '<b>รายละเอียด : </b>' . $DataMail['RepairedDetail'] . '<br>';
            $content .= '<b>ผู้แจ้ง : </b>' . $DataMail['FirstName'] . ' ' . $DataMail['LastName'] . '<br>';
            $content .= '<b>เบอร์โทรติดต่อ : </b>' . $DataMail['Mobile'] . '<br>';
            $content .= '<b>อีเมล : </b>' . $DataMail['Email'] . '<br>';
            
            $content .= '<br>e-mail ฉบับนี้ถูกส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ (Please do not reply this e-mail)';
            return $content;

        }

        private function generateEmailToCreatorContent($DataMail, $status){
            
            $content = '<h3><b>แจ้งปัญหา แจ้งซ่อม ดังนี้</b></h3><br>';
            $content .= '<b>รหัสแจ้งซ่อม : </b>' . $DataMail['RepairedCode'] . '<br>';
            $content .= '<b>พื้นที่ : </b>' . $DataMail['RegionName'] . '<br>';
            $content .= '<b>ประเภทงาน : </b>' . $DataMail['RepairedTypeName'] . '<br>';
            $content .= '<b>รายละเอียด : </b>' . $DataMail['RepairedDetail'] . '<br>';

            if($status == 'Finish'){
                $content .= '<b>ขั้นตอนการดำเนินการ : </b> ซ่อมเสร็จ วันที่ ' . $this->makeThaiDate($DataMail['CompleteDateTime']) . '<br>';
            }else{
                $content .= '<b>ขั้นตอนการดำเนินการ : </b> ระงับการดำเนินการชั่วคราว เนื่องจาก ' . $DataMail['SuspenedComment'] . '<br>';    
            }
            
            $content .= '<b>ผู้ดูแลงานซ่อม : </b>' . $DataMail['FirstName'] . ' ' . $DataMail['LastName'] . '<br>';
            $content .= '<b>เบอร์โทรติดต่อ : </b>' . $DataMail['Mobile'] . '<br>';
            $content .= '<b>อีเมล : </b>' . $DataMail['Email'] . '<br>';
            
            $content .= '<br>e-mail ฉบับนี้ถูกส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ (Please do not reply this e-mail)';
            return $content;

        }

        private function generateNotificationData($Repair, $NotifyType, $PushBy, $adminGroup){

            switch($Repair['RepairedStatus']){
                case 'Request' : $notificationTypeID = 15;break;
                case 'Cancel' : $notificationTypeID = 16;break;
                case 'Process' : $notificationTypeID = 20;break;
                case 'Finish' : $notificationTypeID = 21;break;
                case 'Suspend' : $notificationTypeID = 22;break;
                default : $notificationTypeID='';
            }

            //$adminGroup = 2; // Admin Repair

            $notificationType = NotificationService::getNotificationType($notificationTypeID);
            // $ActionUser = UserService::getUser($Repair['VerifyBy']);
            
            $notification['RegionID'] = $Repair['RegionID'];
            $notification['NotificationType'] = $notificationTypeID;
            $notification['NotificationStatus'] = 'Unseen';
            $notification['NotificationUrl'] = '#/repair/'.$Repair['RepairedID'];
            $notification['NotificationText'] =  $Repair['FirstName'] . ' ' . $Repair['LastName'] . ' ' . $notificationType['NotificationType']. ' รหัสแจ้งซ่อม : ' . $Repair['RepairedCode'] . ' หัวข้อแจ้งซ่อม : '. $Repair['RepairedSubIssueName']. ' ประเภทแจ้งซ่อม : '. $Repair['RepairedTypeName'] . ' รายละเอียด : '. $Repair['RepairedDetail'] . ' พื้นที่ : ' . $Repair['RegionName'];
            $notification['NotificationKeyID'] = $Repair['RepairedID'];
            $notification['PushBy'] = $PushBy;
            $notification['AdminGroup'] = $adminGroup;

            if($NotifyType == 'GROUP'){
                $index = 0;
                // To specific person who can see this notify by group ID
                $UserList = UserService::getUserByGroupAndRegionIDWithPermission($adminGroup, $Repair['RegionID']);
                if(count($UserList) > 0){
                    foreach ($UserList as $key => $value) {
                        $NotificationList[$index] = $notification;
                        $NotificationList[$index]['ToSpecificPersonID'] = $value['UserID'];
                        $index++;
                    }
                }else{
                    $NotificationList[0] = $notification;
                }

                // To user in owner repair title's department
                $this->logger->info('Notify Repair to Dep id : '.$Repair['DepartmentID']);
                $DepartmentUserList = UserService::getUserByDepartment($Repair['DepartmentID']);
                if(count($DepartmentUserList) > 0){
                    //$index = 0;
                    //$this->logger->info('Total person in Dep id : '.count($Repair['DepartmentID']));
                    foreach ($DepartmentUserList as $key => $value) {
                        $NotificationList[$index] = $notification;
                        $NotificationList[$index]['ToSpecificPersonID'] = $value['UserID'];
                        //$this->logger->info('To person : '.$value['UserID']);
                        $index++;
                    }
                }

                return $NotificationList;
            }else{
                $notification['ToSpecificPersonID'] = $Repair['CreateBy'];
                return $notification;
            }
        }

        private function makeThaiDate($date, $useTime = ''){
            $date = str_replace('.000', '', $date);
            $dateTimeObj = split(' ', $date);
            $dateObj = $dateTimeObj[0]; 
            $timeObj = $dateTimeObj[1]; 
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

            $date = $day . ' ' . $month . ' ' . $year;
            if($useTime != ''){
                $date .= ' ' . $timeObj;
            }
            return $date;
        }
        
        private function generateRefNumber($prefix){
            $curDate = date('Y-m-01');
            $runningNo = RunningNoService::updateGetRunningNo($prefix, $curDate);
            return $prefix.date('Ymd').str_pad($runningNo, 3, '0', STR_PAD_LEFT);
        }
    }
    
?>