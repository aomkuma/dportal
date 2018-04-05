<?php

    namespace App\Controller;
    
    use App\Service\ExternalPhoneBookService;
    use App\Service\PermissionService;
    
    class ExternalPhoneBookController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        public function getManagePhoneBookList($request, $response, $args){
            try{
                $parsedBody = $request->getParsedBody();
                $offset = filter_var($parsedBody['offset'], FILTER_SANITIZE_NUMBER_INT);
                $keyword = filter_var($parsedBody['condition']['keyword'], FILTER_SANITIZE_STRING);
                $activeStatus = filter_var($parsedBody['condition']['activeStatus'], FILTER_SANITIZE_STRING);

                $UserID = filter_var($parsedBody['UserID'], FILTER_SANITIZE_STRING);    
                // Check permission
                $user_permission = PermissionService::checkPermission($UserID);
                
                $valid = false;
                foreach ($user_permission as $key => $value) {
                    if($value['AdminGroupID'] == '0' || $value['AdminGroupID'] == '9'){
                        $valid = true;
                    } 
                }
                if(!$valid){
                    $this->data_result['STATUS'] = 'ERROR';
                    return $this->returnResponse(401, $this->data_result, $response);  
                }

                //$this->logger->info('Find by id : '.$id);
                $DataList = ExternalPhoneBookService::getManagePhoneBookList($offset, $keyword, $activeStatus);
                $this->data_result['DATA'] = $DataList;

                return $this->returnResponse(200, $this->data_result, $response, false);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function loadPhoneBookPermission($request, $response, $args){
            try{
                $PhoneBookID = filter_var($request->getAttribute('PhoneBookID'), FILTER_SANITIZE_NUMBER_INT);
                $RegionID = $request->getAttribute('RegionID');

                //$this->logger->info('Find by id : '.$id);
                $Office = ExternalPhoneBookService::loadGroupPermission($RegionID, $PhoneBookID, 'OFFICE', '');
                // Loop Office
                $OfficeList = [];
                foreach ($Office as $o_key => $o_value) {

                    $Division = ExternalPhoneBookService::loadGroupPermission($RegionID, $PhoneBookID, 'DIVISION', $o_value['OrgID']);
                    $DivisionList = [];
                    foreach ($Division as $d_key => $d_value) {
                        $Department = ExternalPhoneBookService::loadGroupPermission($RegionID, $PhoneBookID, 'DEPARTMENT', $d_value['OrgID']);
                        $d_value['Department'] = $Department;
                        array_push($DivisionList, $d_value);
                    }
                    $o_value['Division'] = $DivisionList;
                    array_push($OfficeList, $o_value);
                }
                $this->data_result['DATA']['DataList'] = $OfficeList;
                // Get Phone book detail
                $PhoneBook = ExternalPhoneBookService::getPhoneBookDetail($PhoneBookID);
                $this->data_result['DATA']['PhoneBook'] = $PhoneBook;
                
                return $this->returnResponse(200, $this->data_result, $response, false);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function updateExPhoneBookPermission($request, $response, $args){
            try{
                $parsedBody = $request->getParsedBody();
                $RegionID = $parsedBody['RegionID'];
                $UpdateType = $parsedBody['UpdateType'];

                if($UpdateType == 'OFFICE'){
                    // get child org
                    $GroupID = $parsedBody['GroupID'];
                    $OrgID = $parsedBody['OrgID'];
                    $AllowStatus = $parsedBody['AllowStatus'];
                    $Division = ExternalPhoneBookService::getChildOrg($OrgID);
                    foreach ($Division as $key => $value) {
                        $DivisionOrgID = $value['OrgID'];
                        ExternalPhoneBookService::updatePermissionAllGroup($value['OrgID'], $value['GroupType'], $AllowStatus, $parsedBody);   
                        // Find Department
                        $Department = ExternalPhoneBookService::getChildOrg($DivisionOrgID);
                        foreach ($Department as $d_key => $d_value) {
                            ExternalPhoneBookService::updatePermissionAllGroup($d_value['OrgID'], $d_value['GroupType'], $AllowStatus, $parsedBody);   
                        }
                    }
                    ExternalPhoneBookService::updatePermissionAllGroup($OrgID, $UpdateType, $AllowStatus, $parsedBody);   
                    // ExternalPhoneBookService::updatePermissionGroup($parsedBody);
                }else if($UpdateType == 'DIVISION'){

                    $GroupID = $parsedBody['GroupID'];
                    $OrgID = $parsedBody['OrgID'];
                    $AllowStatus = $parsedBody['AllowStatus'];
                    // Find Department
                    $Department = ExternalPhoneBookService::getChildOrg($OrgID);
                    foreach ($Department as $d_key => $d_value) {
                        ExternalPhoneBookService::updatePermissionAllGroup($d_value['OrgID'], $d_value['GroupType'], $AllowStatus, $parsedBody);   
                    }
                    ExternalPhoneBookService::updatePermissionAllGroup($GroupID, $UpdateType, $AllowStatus, $parsedBody);   

                }else if($UpdateType == 'DEPARTMENT'){
                    
                    $GroupID = $parsedBody['GroupID'];
                    $OrgID = $parsedBody['OrgID'];
                    $AllowStatus = $parsedBody['AllowStatus'];
                    ExternalPhoneBookService::updatePermissionAllGroup($OrgID, $UpdateType, $AllowStatus, $parsedBody);   

                }else{
                    $GroupList = ExternalPhoneBookService::getAllGroup($RegionID);
                    $AllowStatus = $parsedBody['UpdateType'] == 'SELECT_ALL'?1:0;
                    foreach ($GroupList as $key => $value) {
                        ExternalPhoneBookService::updatePermissionAllGroup($value['OrgID'], $value['GroupType'], $AllowStatus, $parsedBody);
                    }
                    
                }

                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function getExternalPhoneBookList($request, $response, $args){
            try{
                $parsedBody = $request->getParsedBody();
                $offset = filter_var($parsedBody['offset'], FILTER_SANITIZE_NUMBER_INT);
                $keyword = filter_var($parsedBody['condition']['keyword'], FILTER_SANITIZE_STRING);
                $LoginUserID = filter_var($parsedBody['condition']['LoginUserID'], FILTER_SANITIZE_NUMBER_INT);

                //$this->logger->info('Find by id : '.$id);
                $DataList = ExternalPhoneBookService::getExternalPhoneBookList($offset, $keyword, $LoginUserID);
                $this->data_result['DATA'] = $DataList;

                return $this->returnResponse(200, $this->data_result, $response, false);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function updateExternalPhoneBook($request, $response, $args){
            try{
                $parsedBody = $request->getParsedBody();
                $Contact = $parsedBody['Contact'];
                $files = $request->getUploadedFiles();
                $img = $files['fileimg'];
                
                if($img != null){
                    if($img->getClientFilename() != ''){
                        unlink('../../img/external_phonebook/'.$Contact['Picture']);

                        $newFileName = date('YmdHis').'_'.$img->getClientFilename();
                        $Contact['Picture'] = 'img/external_phonebook/'.$newFileName;
                        $img->moveTo('../../img/external_phonebook/'.$newFileName);
                    }
                }

                //$this->logger->info('Find by id : '.$id);
                $result = ExternalPhoneBookService::updatePhoneBookContact($Contact);
                if($result){
                    $this->data_result['DATA'] = $result;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Cannot update contact. Plase try again';
                }
                return $this->returnResponse(200, $this->data_result, $response, false);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function addFavouriteContact($request, $response, $args){
            try{

                $parsedBody = $request->getParsedBody();
                $UserFavouriteID = filter_var($parsedBody['UserFavouriteID'], FILTER_SANITIZE_NUMBER_INT);
                $LoginUserID = filter_var($parsedBody['LoginUserID'], FILTER_SANITIZE_NUMBER_INT);

                //$this->logger->info('Find by id : '.$id);
                $insertID = ExternalPhoneBookService::addFavouriteContact($LoginUserID, $UserFavouriteID);
                if(intval($insertID) > 0){
                    $this->data_result['DATA'] = ExternalPhoneBookService::getFavouriteContact($UserFavouriteID);
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Cannot add favourite contact';
                }
                

                return $this->returnResponse(200, $this->data_result, $response, false);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function removeFavouriteContact($request, $response, $args){
            try{
    //             error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
                $FavouriteID = filter_var($request->getAttribute('FavouriteID'), FILTER_SANITIZE_NUMBER_INT);
                $UserID = filter_var($request->getAttribute('UserID'), FILTER_SANITIZE_NUMBER_INT);

                if(ExternalPhoneBookService::removeFavouriteContact($FavouriteID)){
                    $this->data_result['DATA'] = ExternalPhoneBookService::getContact($UserID);
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'Cannot remove favourite contact';
                }
                

                return $this->returnResponse(200, $this->data_result, $response, false);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

    }