<?php

    namespace App\Controller;
    
    use App\Model\Province;
    use App\Service\ProvinceService;
    
    class ProvinceController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
        
        public function getProvinceList($request, $response, $args){
            
            try{
                
                $ProvinceList = ProvinceService::getAllProvince();
                $this->data_result['DATA'] = $ProvinceList;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }
    }
    
?>