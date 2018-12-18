<?php

    namespace App\Controller;
    
    use App\Service\LoginService;
    use App\Controller\SMS;

    class LoginController extends Controller {
        
        protected $logger;
        protected $db;
        protected $sms;
        protected $ldap;
        
        public function __construct($logger, $sms, $db, $ldap){
            $this->logger = $logger;
            $this->db = $db;
            $this->sms = $sms;
            $this->ldap = $ldap;
        }

        private function authenticateWithAD($username, $password)
        {
            // return 'OK';
            $bind_result = '';

            $ldaphost = $this->ldap['host'];
            $ldapport = $this->ldap['port'];

            $ldapconn = ldap_connect($ldaphost, $ldapport);
            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

            if ($ldapconn) {
                $ldapbind = ldap_bind($ldapconn, $username, $password);
                if ($ldapbind) {
                    $bind_result = 'OK';
                }else{
                    $errorNo = ldap_errno($ldapconn);
                    $bind_result = ', '.ldap_err2str ($errorNo);
                }
            }else{
                $bind_result = ', Cannot connect with Active Directory Server';
            }

            return $bind_result;
        }
        
        public function authenticate($request, $response, $args){
    //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{
                $error_msg = '';

                $loginObj = $request->getParsedBody();
                $username = $loginObj['obj_login']['Username'];
                $password = $loginObj['obj_login']['Password'];
                $use_ldap = $this->ldap['active'];
                $this->logger->info('Find by username : '. $username . " Password : " . $password);
                // print_r($this->ldap);

                if(strpos($username,'superadmin') !== false){
                    $use_ldap = 'N';
                }

                if($use_ldap == 'Y'){
                    // Login Authen with AD First
                    $userPrincipal = $username;
                    if(strpos($username, $this->ldap['principal']) === false){
                        $userPrincipal .= $this->ldap['principal'];
                    }
                    $this->logger->info('userPrincipal : '. $userPrincipal);

                    $ad_result = $this->authenticateWithAD($userPrincipal, $password);

                    if($ad_result == 'OK'){
                        // get user data
                        $user = LoginService::authenticateNoPass($username);    
                        if(!empty($user)){
                            // Update current password
                            $update_pass_result = LoginService::updatePassword($username , $password);   
                        }else{
                            $error_msg .= " Cannot find this user in this system";    
                        }
                    }else{
                        $error_msg .= $ad_result;
                    }

                }else{
                    // System login
                    $user = LoginService::authenticate($username , $password);    
                }

                $this->logger->info($user);
                if(!empty($user[UserID])){
                    unset($user[Password]);

                    // Update login count
                    $totalLogin = LoginService::updateSystemLoginCount();

                    // Get menu in this user's group
                    $menuList = LoginService::getMenuList($user['UserID']);                    


                    // Update 10/9/2018
                    // Get Person Region
                    $PersonRegion = LoginService::getPersonRegion($user['UserID']);

                    // Update 23/9/2018
                    // Generate and update login session
                    $session = $this->generateLoginSession($user['UserID']);
                    LoginService::updateLoginSession($user['UserID'], $session);
                    $user['LoginSession'] = $session;

                    $this->data_result['DATA']['UserData'] = $user;
                    $this->data_result['DATA']['TotalLogin'] = $totalLogin;
                    $this->data_result['DATA']['MenuList'] = $menuList;
                    $this->data_result['DATA']['PersonRegion'] = $PersonRegion;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = $error_msg;
                }
                
                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function authenticateWithSession($request, $response, $args){
            
            try{
                $loginObj = $request->getParsedBody();
                $Username = $loginObj['obj_login']['Username'];
                $LoginSession = $loginObj['obj_login']['LoginSession'];
                
                $this->logger->info('Find by username : '. $Username . " LoginSession : " . $LoginSession);

                $user = LoginService::authenticateWithSession($Username , $LoginSession);    
                if(!empty($user[UserID])){
                    unset($user[Password]);

                    // Update 10/9/2018
                    // Get Person Region
                    $PersonRegion = LoginService::getPersonRegion($user['UserID']);

                    // Update 23/9/2018
                    
                    $this->data_result['DATA']['UserData'] = $user;
                    $this->data_result['DATA']['TotalLogin'] = $totalLogin;
                    $this->data_result['DATA']['MenuList'] = $menuList;
                    $this->data_result['DATA']['PersonRegion'] = $PersonRegion;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = $error_msg;
                }

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function authenticateWithPIN($request, $response, $args){
            
            try{
                $loginObj = $request->getParsedBody();
                $UserID = $loginObj['obj_login']['UserID'];
                $PinID = $loginObj['obj_login']['PinID'];
                
                $this->logger->info('Find by pin : '. $PinID);

                $user = LoginService::authenticateWithPIN($UserID, $PinID);    
                if(!empty($user[UserID])){
                    unset($user[Password]);

                    // Update 10/9/2018
                    // Get Person Region
                    $PersonRegion = LoginService::getPersonRegion($user['UserID']);

                    // Update 23/9/2018
                    
                    $this->data_result['DATA']['UserData'] = $user;
                    $this->data_result['DATA']['TotalLogin'] = $totalLogin;
                    $this->data_result['DATA']['MenuList'] = $menuList;
                    $this->data_result['DATA']['PersonRegion'] = $PersonRegion;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'ไม่พบผู้ใช้งานนี้';
                }

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function pinSetting($request, $response, $args){
            
            try{
                $loginObj = $request->getParsedBody();
                $obj_setting = $loginObj['obj_setting'];
                $PinID = $obj_setting['PinID'];
                $UserID = $obj_setting['UserID'];

                // find Duplicate pin
                // $dup = LoginService::findDuplicatePin($UserID, $PinID);  
                // if($dup){
                //     $this->data_result['STATUS'] = 'ERROR';
                //     $this->data_result['DATA'] = '';
                //     return $this->returnResponse(200, $this->data_result, $response, false);
                //     exit();
                // }
                LoginService::updatePin($UserID, $PinID);  

                $user = LoginService::authenticateWithPIN($PinID);  
                if(!empty($user[UserID])){
                    unset($user[Password]);

                    // Update 10/9/2018
                    // Get Person Region
                    $PersonRegion = LoginService::getPersonRegion($user['UserID']);

                    // Update 23/9/2018
                    
                    $this->data_result['DATA']['UserData'] = $user;
                    $this->data_result['DATA']['TotalLogin'] = $totalLogin;
                    $this->data_result['DATA']['MenuList'] = $menuList;
                    $this->data_result['DATA']['PersonRegion'] = $PersonRegion;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = $error_msg;
                }

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        private function generateLoginSession($UserID){
            $arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
            $char = '';
            for($i = 0; $i < 50; $i++){
                $char .= $arr[rand(0,23)];
            }
            $str = $UserID . '::' . date('YmdHis') . '::' . $char;
            $str = base64_encode(substr($str, 0, 50));
            return $str;
        }

        public function verifyUsername($request, $response, $args){
            try{
                $loginObj = $request->getParsedBody();
                $username = $loginObj['username'];
                
                $verify = LoginService::verifyUsername($username);
                
                if($verify){
                    $this->data_result['DATA'] = $username;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Not found';
                }
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function requestOTP($request, $response, $args){
    //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{
                $loginObj = $request->getParsedBody();
                $username = $loginObj['username'];
                
                $User = LoginService::getMobilePhoneNumber($username);
                
                if(!empty($User['Mobile'])){

                    // Generate OTP
                    $otp = LoginService::generateOTP();
                    // Call SMS API
                    $smsContent = 'OTP for change password is : ' . $otp;
                    $smsResult = $this->sendSMS($User['Mobile'], $smsContent);

                    if($smsResult){
                        $this->data_result['STATUS'] = 'ERROR';
                        $this->data_result['DATA'] = 'Cannot send SMS';
                    }else{
                        // Update OTP
                        $otpResult = LoginService::updateOTP($otp, $User['UserID']);
                        $this->data_result['DATA']['OTPData'] = $otpResult['CreateDateTime'];    
                    }

                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Mobile number Not found';
                }
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function verifyOTP($request, $response, $args){
            try{
                $loginObj = $request->getParsedBody();
                $otp = $loginObj['otp'];
                
                $otpResult = LoginService::verifyOTP($otp);
                if(!empty($otpResult)){
                    // Check time diff
                    $startTime = str_replace('.000', '',$otpResult['CreateDateTime']);
                    $endTime = date('Y-m-d H:i:s');
                    $time_diff = $this->diff($startTime, $endTime);
                    
                    if($time_diff < 5){
                        //$this->logger->info($);
                        // Update inactive OTP
                        LoginService::updateInactiveOTP($otpResult['OtpID']);
                        $this->data_result['DATA'] = $time_diff;

                    }else{
                        $this->data_result['STATUS'] = 'ERROR';
                        $this->data_result['DATA'] = 'OTP over 5 minutes';
                    }
                    
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Invalid OTP';
                }

                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function changePassword($request, $response, $args){
            try{
                $loginObj = $request->getParsedBody();
                $objChangePassword = $loginObj['objChangePassword'];
                $username = $objChangePassword['Username'];
                $newPassword = $objChangePassword['NewPassword'];

                $result = LoginService::updatePassword($username, $newPassword);
                
                if($result){
                    $this->data_result['DATA'] = 'Update password success';
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Cannot update password';
                }

                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        private function diff($date1, $date2) {
            $to_time = strtotime($date2);
            $from_time = strtotime($date1);
            return round(abs($to_time - $from_time) / 60,2);
        }
        
        private function sendSMS($receiver, $content){
            $sms = new SMSController($this->logger, $this->sms);
            $sms->setSmsReceiver($receiver);
            $sms->setSmsDesc($content);
            $sms->sendSMS();
        }
    }


?>