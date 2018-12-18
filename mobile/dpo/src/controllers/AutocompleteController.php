<?php

    namespace App\Controller;
    
    use App\Model\User;
    use App\Service\AutocompleteService;
    
    class AutocompleteController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
       
        public function getAutocomplete($request, $response, $args){
            try{
                $qtype = filter_var($request->getAttribute('qtype'), FILTER_SANITIZE_STRING);
                $keyword = filter_var($request->getAttribute('keyword'), FILTER_SANITIZE_STRING);
                $this->logger->info('Find by $qtype : '.$qtype.', $keyword : '.$keyword);
                
                switch($qtype){
                    case 'USER' : $this->data_result['DATA'] = AutocompleteService::getAccount($keyword); break;
                    case 'EXUSER' : $this->data_result['DATA'] = AutocompleteService::getExUser($keyword); break;
                    default : null;
                }
                
                return $this->returnResponse(200, $this->data_result, $response, false);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $e, $response);
            }
            
        }
        
    }

?>