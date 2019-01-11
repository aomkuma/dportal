<?php

    namespace App\Controller;
    
    use App\Service\LeaveService;

    class LeaveController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        public function getNotification($request, $response, $args){
            try{
                
                // $parsedBody = $request->getParsedBody();
                // $email = $parsedBody['email'];
                $email = filter_var($request->getAttribute('email'), FILTER_SANITIZE_STRING);

                $_List = LeaveService::getNotification($email);
                $this->data_result['DATA']['NotificationList'] = $_List;

                $totalNewNotifications = LeaveService::countNotificationUnseen($email);
                $this->data_result['DATA']['totalNewNotifications'] = $totalNewNotifications;

                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }                
        }

        public function updateSeenNotification($request, $response, $args){
            
            try{
                $parsedBody = $request->getParsedBody();
                $ID = filter_var($parsedBody['ID'], FILTER_SANITIZE_NUMBER_INT);    
                $email = filter_var($parsedBody['email'], FILTER_SANITIZE_STRING);    

                $result = LeaveService::updateSeenNotification($ID);
                $this->data_result['DATA']['result'] = $result;

                $totalNewNotifications = LeaveService::countNotificationUnseen($email);
                $this->data_result['DATA']['totalNewNotifications'] = $totalNewNotifications;

                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }


        public function putNotification($request, $response, $args){
            try{
                
                $parsedBody = $request->getParsedBody();

                $User = $parsedBody['UserLogin'];
                $_LeaveNotificationList = $parsedBody['Leaves'];
                
                // Authen
                $Username = $User['Username'];
                $Password = $User['Password'];
                $loginResult = LeaveService::logon($Username, $Password);
                if(empty($loginResult)){
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA']['Message'] = 'Invalid User Account';
                    return $this->returnResponse(200, $this->data_result, $response);
                    exit(0);
                }

                $cnt_success = 0;
                foreach($_LeaveNotificationList as $k => $v){

                    $data = [];
                    $data['Email'] = $v['email'];
                    $data['Messages'] = $v['message'];
                    $data['NorifyDatetime'] = $v['notifyDTTm'];
                    $data['ReturnLink'] = $v['link'];
                    $id = LeaveService::putNotification($data);
                    if(!empty($id)){
                        $cnt_success++;
                    }
                }

                $this->data_result['DATA']['SuccessResult'] = $cnt_success;

                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }
    }
