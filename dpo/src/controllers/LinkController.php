<?php

    namespace App\Controller;
    
    use App\Service\LinkService;
    use App\Service\PermissionService;
    
    class LinkController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
       
        public function getLinkList($request, $response, $args){
            
            try{

                $mode = $request->getAttribute('mode');
                $userID = $request->getAttribute('userID');

                if($mode != 'view'){
                    // Check permission
                    $user_permission = PermissionService::checkPermission($userID);
                    
                    $valid = false;
                    foreach ($user_permission as $key => $value) {
                        if($value['AdminGroupID'] == '0' || $value['AdminGroupID'] == '8'){
                            $valid = true;
                        } 
                    }
                    if(!$valid){
                        $this->data_result['STATUS'] = 'ERROR';
                        return $this->returnResponse(401, $this->data_result, $response);  
                    }
                }
                //$Permission = false;

                // if($mode == 'view'){
                //     // Check permission admin
                //     $Permission = LinkService::checkAdminLink($userID);
                //     $this->data_result['Permission'] = $Permission['PermissionID'];
                // }

                $LinkList = LinkService::getLinkList($mode, $userID, $Permission);

                $this->data_result['DATA'] = $LinkList;

                
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
                    if($files->getClientFilename() != ''){
                        $ext = pathinfo($files->getClientFilename(), PATHINFO_EXTENSION);
                        $newFileName = date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                        // $newFileName = date('YmdHis').'_'.$files->getClientFilename();
                        $parsedBody['LinkIcon'] = 'img/link/'.$newFileName;
                        $files->moveTo('../../img/link/'.$newFileName);
                    }
                }
               
                $Link = LinkService::updateData($parsedBody);
                $this->data_result['DATA'] = $Link;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteData($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = LinkService::deleteData($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getLinkPermission($request, $response, $args){
            
            try{

                $parsedBody = $request->getParsedBody();
                $offset  = filter_var($parsedBody['offset'], FILTER_SANITIZE_NUMBER_INT); 
                $linkID = filter_var($parsedBody['linkID'], FILTER_SANITIZE_NUMBER_INT);
                $userID = filter_var($parsedBody['userID'], FILTER_SANITIZE_NUMBER_INT);
                $condition = $parsedBody['condition'];
                
                $Permission = LinkService::checkAdminLink($userID);
                
                if(!empty($Permission['PermissionID'])){
                    $this->data_result['Permission'] = $Permission['PermissionID'];
                    $Link = LinkService::getLink($linkID);
                    $LinkPermission = LinkService::getLinkPermission($offset, $linkID, $condition);    
                }

                $this->data_result['Link'] = $Link;
                $this->data_result['DATA'] = $LinkPermission;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateLinkPermission($request, $response, $args){
            
            try{

                $parsedBody = $request->getParsedBody();
                $data = $parsedBody['data'];
                $userID = filter_var($parsedBody['userID'], FILTER_SANITIZE_NUMBER_INT);

                $obj['LinkPermissionID'] = $data['LinkPermissionID'];
                $obj['LinkID'] = $data['LinkID'];
                $obj['LinkStatus'] = $data['LinkStatus'];
                $obj['UserID'] = $data['UserID'];
                $obj['CreateBy'] = $userID;
                $LinkPermissionID = LinkService::updateLinkPermission($obj);
                $this->data_result['DATA'] = $LinkPermissionID;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function setAllLinkPermission($request, $response, $args){
            
            try{

                $parsedBody = $request->getParsedBody();
                $condition = $parsedBody['condition'];
                $updateType = $parsedBody['updateType'];
                $linkID = $parsedBody['linkID'];
                $userID = $parsedBody['userID'];

                if($updateType == 'CHECKED'){
                    $linkStatus = 1;
                }else{
                    $linkStatus = 0;
                }
                // Get Link First
                $LinkPermission = LinkService::getAllLinkPermission($linkID, $condition);    
                // print_r($LinkPermission);
                // exit;
                foreach ($LinkPermission as $key => $value) {
                    $value['LinkStatus'] = $linkStatus;
                    $value['CreateBy'] = $userID;
                    // print_r($value);
                    $LinkPermissionID = LinkService::updateLinkPermission($value);    
                }
                // exit;
                $this->data_result['DATA'] = $LinkPermissionID;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
        
    }
    
?>