<?php

	namespace App\Controller;
    
    use App\Service\PermissionService;
    
    class PermissionController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
       
        public function getPermissionList($request, $response, $args){
            //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{
                
                $parsedBody = $request->getParsedBody();
                $Region = filter_var($parsedBody['Region'], FILTER_SANITIZE_NUMBER_INT);
                $Group = filter_var($parsedBody['Group'], FILTER_SANITIZE_NUMBER_INT);
                $Username = filter_var($parsedBody['Username'], FILTER_SANITIZE_STRING);
                $UserID = filter_var($parsedBody['UserID'], FILTER_SANITIZE_STRING);    
                // Check permission
                $user_permission = PermissionService::checkPermission($UserID);
                
                $valid = false;
                foreach ($user_permission as $key => $value) {
                    if($value['AdminGroupID'] == '0' || $value['AdminGroupID'] == '1'){
                        $valid = true;
                    } 
                }
                if(!$valid){
                    $this->data_result['STATUS'] = 'ERROR';
                    return $this->returnResponse(401, $this->data_result, $response);  
                }

                $PermissionList = PermissionService::getPermissionList($Region, $Group, $Username);
                $this->data_result['DATA']['DataList'] = $PermissionList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updatePermission($request, $response, $args){
            
            try{
                $PermissionTypeList = ['SuperAdmin','PermissionAdmin','RoomAdmin','CarAdmin','DeviceAdmin'
                                    ,'NewsAdmin','NewsApproveAdmin','RepairAdmin','LinkAdmin','ExPhoneBookAdmin','CalendarAdmin'];
                $parsedBody = $request->getParsedBody();
                $Data = $parsedBody['data'];
                $PermissionType = filter_var($parsedBody['permissionType'], FILTER_SANITIZE_STRING);

                $objUpdate['UserID'] = $Data['UserID'];
                $objUpdate['RegionID'] = $Data['RegionID'];
                $objUpdate['PermissionType'] = $PermissionType;

                if($PermissionType == 'SuperAdmin'){

                    for($i = 0; $i < count($PermissionTypeList); $i++){
                        $objUpdate['AdminGroupID'] = $Data[$PermissionTypeList[$i]];
                        $objUpdate['PermissionType'] = $PermissionTypeList[$i];
                        $PermissionList = PermissionService::updatePermission($objUpdate);    
                    }

                }else if($PermissionType == 'PermissionAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['PermissionAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);    
                }else if($PermissionType == 'RoomAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['RoomAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }else if($PermissionType == 'CarAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['CarAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }else if($PermissionType == 'DeviceAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['DeviceAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }else if($PermissionType == 'NewsAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['NewsAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }else if($PermissionType == 'NewsApproveAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['NewsApproveAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }else if($PermissionType == 'RepairAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['RepairAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }else if($PermissionType == 'LinkAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['LinkAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }else if($PermissionType == 'ExPhoneBookAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['ExPhoneBookAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }else if($PermissionType == 'CalendarAdmin'){
                    $objUpdate['AdminGroupID'] = $Data['CalendarAdmin'];
                    $PermissionList = PermissionService::updatePermission($objUpdate);
                }
                
                $this->data_result['DATA'] = 'Success '. $PermissionList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
	}