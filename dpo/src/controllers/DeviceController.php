<?php

    namespace App\Controller;
    
    use App\Service\DeviceService;
    use App\Service\PermissionService;
    
    class DeviceController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
       
        public function getDeviceList($request, $response, $args){
            
            try{

                $offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT);
                $UserID = filter_var($request->getAttribute('UserID'), FILTER_SANITIZE_NUMBER_INT);
                // Check permission
                $user_permission = PermissionService::checkPermission($UserID);
                
                $valid = false;
                foreach ($user_permission as $key => $value) {
                    if($value['AdminGroupID'] == '0' || $value['AdminGroupID'] == '4'){
                        $valid = true;
                    } 
                }
                if(!$valid){
                    $this->data_result['STATUS'] = 'ERROR';
                    return $this->returnResponse(401, $this->data_result, $response);  
                }

                $RoomList = DeviceService::getDeviceList($offset);
                $this->data_result['DATA'] = $RoomList;
                
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
                $files = $files['fileimg'];
                if($files != null){
                    //print_r($files);
                    //echo 'sada '.$files->getClientFilename();
                    if($files->getClientFilename() != ''){
                        $ext = pathinfo($files->getClientFilename(), PATHINFO_EXTENSION);
                        $newFileName = date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                        // $newFileName = date('YmdHis').'_'.$files->getClientFilename();
                        $parsedBody['DevicePicture'] = 'img/device/'.$newFileName;
                        $files->moveTo('../../img/device/'.$newFileName);
                    }
                }
                //print_r($parsedBody);
               // die();

                $News = DeviceService::updateData($parsedBody);
                $this->data_result['DATA'] = $News;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteData($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = DeviceService::deleteData($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
        
    }
    
?>