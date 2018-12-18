<?php

    namespace App\Controller;
    
    use App\Model\Region;
    use App\Service\RegionService;
    
    class RegionController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
        
        public function getRegionList($request, $response, $args){
            
            try{
                
                $regionList = RegionService::getAllRegion();
                $this->data_result['DATA'] = $regionList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }
    }
    
?>