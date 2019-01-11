<?php

    namespace App\Controller;
    
    use App\Service\DepartmentService;
    
    class DepartmentController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
       
        public function getDepartmentList($request, $response, $args){
            
            try{

                $DepartmentList = DepartmentService::getDepartmentList();
                $this->data_result['DATA'] = $DepartmentList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function getAllDepartmentList($request, $response, $args){
            
            try{

                $DepartmentList = DepartmentService::getAllDepartmentList();
                $this->data_result['DATA'] = $DepartmentList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function loadManageDepartmentList($request, $response, $args){
            
            try{

                $parsedBody = $request->getParsedBody();
                $condition  = $parsedBody['condition']; 

                $DepartmentList = DepartmentService::loadManageDepartmentList($condition);
                $this->data_result['DATA'] = $DepartmentList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function updateRegionOfDepartment($request, $response, $args){
            
            try{
                $parsedBody = $request->getParsedBody();
                $groupID  = $parsedBody['groupID']; 
                $orgID  = $parsedBody['orgID']; 
                $RegionID  = $parsedBody['RegionID']; 

                $result = DepartmentService::updateRegionOfDepartment($groupID, $RegionID);
                if($result){
                    // Update region id of users contain to orgID
                    DepartmentService::updateRegionOfUsers($orgID, $RegionID);
                }
                $this->data_result['DATA'] = $result;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

    }
    
?>