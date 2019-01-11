<?php

    namespace App\Controller;
    
    use App\Service\PersonRegionService;
    
    class PersonRegionController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
        
        public function getPersonRegionList($request, $response, $args){
            // error_reporting(E_ERROR);
            // error_reporting(E_ALL);
            // ini_set('display_errors','On');
            $parsedBody = $request->getParsedBody();
            $Username = filter_var($parsedBody['Username'], FILTER_SANITIZE_STRING);
            
            try{
                $PersonRegionList = PersonRegionService::getPersonRegionList($Username);
                $this->data_result['DATA']['PersonRegionList'] = $PersonRegionList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }   
        }

        public function updatePersonRegion($request, $response, $args){
            error_reporting(E_ERROR);
            error_reporting(E_ALL);
            ini_set('display_errors','On');
            $parsedBody = $request->getParsedBody();
            $UserID = filter_var($parsedBody['UserID'], FILTER_SANITIZE_NUMBER_INT);
            $RegionID = filter_var($parsedBody['RegionID'], FILTER_SANITIZE_NUMBER_INT);
            // echo $UserID . ':' . $RegionID;exit;
            try{
                $PersonRegionList = PersonRegionService::updatePersonRegion($UserID, $RegionID);
                // $this->data_result['DATA']['PersonRegionList'] = $PersonRegionList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }   
        }        
    }

?>