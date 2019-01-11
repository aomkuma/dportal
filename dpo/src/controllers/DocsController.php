<?php

    namespace App\Controller;
    
    use App\Service\DocsService;
    use App\Service\AttachFileService;

    class DocsController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        public function getListView($request, $response, $args){
            try{
                $params = $request->getParsedBody();
                //$emailID = $params['obj']['emailID'];
                $doc_type = $params['doc_type'];

                // if($doc_level == 1){
                //     $_List = DocsService::getDocs1List($doc_type, $condition);
                // }else if($doc_level == 2){
                //     $_List = DocsService::getDocs2List($parent_id, $condition);
                // }else if($doc_level == 3){
                //     $_List = DocsService::getDocs3List($parent_id, $condition);
                // }
                $_List = DocsService::getDocsView($doc_type);

                $this->data_result['DATA']['List'] = $_List;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function getList($request, $response, $args){
            try{
                $params = $request->getParsedBody();
                //$emailID = $params['obj']['emailID'];
                $doc_type = $params['doc_type'];
                $doc_level = $params['doc_level'];
                $parent_id = $params['parent_id'];
                $condition = $params['condition'];

                if($doc_level == 1){
                    $_List = DocsService::getDocs1List($doc_type, $condition);
                }else if($doc_level == 2){
                    $_List = DocsService::getDocs2List($parent_id, $condition);
                }else if($doc_level == 3){
                    $_List = DocsService::getDocs3List($parent_id, $condition);
                }

                $this->data_result['DATA']['List'] = $_List;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function getData($request, $response, $args){
            try{
                $params = $request->getParsedBody();
                $doc_level = $params['doc_level'];
                $id = $params['id'];

                if($doc_level == 1){
                    $_Data = DocsService::getDocs1($id);
                }else if($doc_level == 2){
                    $_Data = DocsService::getDocs2($id);
                }else if($doc_level == 3){
                    $_Data = DocsService::getDocs3($id);
                }

                $this->data_result['DATA'] = $_Data;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function updateData($request, $response, $args){
            
            $_WEB_FILE_PATH = 'files/files';
            try{
                // error_reporting(E_ERROR);
                // error_reporting(E_ALL);
                // ini_set('display_errors','On');
                $params = $request->getParsedBody();
                $_Docs = $params['Data'];
                $doc_level = $params['doc_level']; 
                
                $files = $request->getUploadedFiles();
                $f = $files['AttachFile'];
                // print_r($f);
                // exit;
                if($f != null && !empty($f) && $_Docs['file_type'] == 'FILE'){

                    if($f->getClientFilename() != ''){
                        $ext = pathinfo($f->getClientFilename(), PATHINFO_EXTENSION);
                        $FileName = date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                        $FilePath = 'uploads/docs/'.$FileName;

                        $f->moveTo('../../' . $FilePath);
                        $_Docs['doc_file'] = $FilePath;    
                    }

                }

                if($doc_level == 1){
                    $id = DocsService::updateDoc1($_Docs);
                }else if($doc_level == 2){
                    $id = DocsService::updateDoc2($_Docs);
                }else if($doc_level == 3){
                    $id = DocsService::updateDoc3($_Docs);
                }

                $this->data_result['DATA']['id'] = $id;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

    
    }