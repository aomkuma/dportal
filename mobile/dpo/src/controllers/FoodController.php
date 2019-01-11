<?php

    namespace App\Controller;
    
    use App\Service\FoodService;
    
    class FoodController extends Controller {
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
       
        public function getFoodList($request, $response, $args){
            
            try{

                $offset = filter_var($request->getAttribute('offset'), FILTER_SANITIZE_NUMBER_INT);
                $RoomList = FoodService::getFoodList($offset);
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
                        $parsedBody['FoodPicture'] = 'img/food/'.$newFileName;
                        $files->moveTo('../../img/food/'.$newFileName);
                    }
                }
                //print_r($parsedBody);
               // die();

                $News = FoodService::updateData($parsedBody);
                $this->data_result['DATA'] = $News;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }

        public function deleteData($request, $response, $args){
            
            try{

                $ID = filter_var($request->getAttribute('ID'), FILTER_SANITIZE_NUMBER_INT);
                $deleteStatus = FoodService::deleteData($ID);
                $this->data_result['DATA'] = $deleteStatus;
                
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }            
        }
        
    }
    
?>