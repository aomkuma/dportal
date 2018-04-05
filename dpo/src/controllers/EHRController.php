<?php

    namespace App\Controller;
    
    use App\Service\EHRService;
    use App\Controller\Mailer;
    
    class EHRController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
        
        public function eHrUpdateDepartment($request, $response, $args){
    
            try{
                $this->logger->info('Begin daily update Department.. ');
                // $ParsedBody = $request->getParsedBody();
                // $DepartmentList = json_decode($ParsedBody['obj'], true);
                
                include '../src/settings_var.php';
                $DepartmentList = json_decode($this->do_post_request($EHR_DEPARTMENT, "POST", []), true);
                $this->logger->info('Total Department : ' . count($DepartmentList));
                
                foreach ($DepartmentList as $key => $value) {
                    EHRService::updateDepartment($value);  
                    EHRService::updateGroup($value, 'DEPARTMENT');  
                }
                $this->logger->info('Finish update Department.. ');
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function eHrUpdateOffice($request, $response, $args){
    
            try{
                $this->logger->info('Begin daily update Office.. ');
                // $ParsedBody = $request->getParsedBody();
                // $OfficeList = json_decode($ParsedBody['obj'], true);
                include '../src/settings_var.php';
                $OfficeList = json_decode($this->do_post_request($EHR_OFFICE, "POST", []), true);
                
                $this->logger->info('Total Office : ' . count($OfficeList));
                foreach ($OfficeList as $key => $value) {
                    EHRService::updateOffice($value);
                    EHRService::updateGroup($value, 'OFFICE');    
                }
                $this->logger->info('Finish update Office.. ');
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function eHrUpdateDivision($request, $response, $args){
    
            try{
                $this->logger->info('Begin daily update Division.. ');
                // $ParsedBody = $request->getParsedBody();
                // $OfficeList = json_decode($ParsedBody['obj'], true);
                include '../src/settings_var.php';
                $DivisionList = json_decode($this->do_post_request($EHR_DIVISION, "POST", []), true);

                $this->logger->info('Total Division : ' . count($DivisionList));
                foreach ($DivisionList as $key => $value) {
                    EHRService::updateDivision($value);  
                    EHRService::updateGroup($value, 'DIVISION');
                }
                $this->logger->info('Finish update Division.. ');
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function eHrUpdateStaff($request, $response, $args){
    //                 error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            try{
                $this->logger->info('Begin daily update Staff.. ');
                // $ParsedBody = $request->getParsedBody();
                // $StaffList = json_decode($ParsedBody['obj'], true);
                include '../src/settings_var.php';
                $StaffList = json_decode($this->do_post_request($EHR_STAFF, "POST", []), true);

                $this->logger->info('Total Staff : ' . count($StaffList));
                foreach ($StaffList as $key => $value) {
                    // get region ID by orgID
                    // $regionID = 2;
                    // if(strpos($value['org'], 'ภก') !== false){
                    //     $regionID = 3;
                    // }else if(strpos($value['org'], 'ภต') !== false){
                    //     $regionID = 4;
                    // }elseif(strpos($value['org'], 'ภอ') !== false){
                    //     $regionID = 5;
                    // }elseif(strpos($value['org'], 'ภล') !== false){
                    //     $regionID = 6;
                    // }elseif(strpos($value['org'], 'ภบ') !== false){
                    //     $regionID = 7;
                    // }
                    $orgName = $value['org'];
                    $regionID = 2;
                    if(strpos($orgName, 'ภก') !== false || strpos($orgName, 'ภาคกลาง') !== false){
                        $regionID = 3;
                    }else if(strpos($orgName, 'ภต') !== false || strpos($orgName, 'ภาคใต้') !== false){
                        $regionID = 4;
                    }elseif(strpos($orgName, 'ภอ') !== false || strpos($orgName, 'ภาคตะวันออกเฉียงเหนือ') !== false){
                        $regionID = 5;
                    }elseif(strpos($orgName, 'ภล') !== false || strpos($orgName, 'ภาคเหนือตอนล่าง') !== false){
                        $regionID = 6;
                    }elseif(strpos($orgName, 'ภบ') !== false || strpos($orgName, 'ภาคเหนือตอนบน') !== false){
                        $regionID = 7;
                    }
                    //$this->logger->info($value['org'] . ' Region ID : ' . $regionID);
                    // Get group ID by OrgName
                    $GroupID = EHRService::getGroupID($value['org']);  
                    $this->logger->info($value['staffEmail'] . ' :: ' . $value['org'] . ' Group ID : ' . $GroupID);
                    if(empty($GroupID)){
                        $GroupID = 1;
                    }

                    $res = EHRService::updateStaff($value, $regionID, $GroupID);
                    if($res){
                        $this->logger->info($value['staffEmail'] . ' Update success ');
                    } else{
                        $this->logger->info($value['staffEmail'] . ' Update Failed ');
                    }
                }
                $this->logger->info('Finsih update Staff.. ');
                return $this->returnResponse(200, $this->data_result, $response);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function testMail($request, $response, $args){
            try{
                $mailer = new Mailer;
                $mailer->setSubject( "test mail from d-Portal" );
                $mailer->isHtml(true);
                $mailer->setHTMLContent("test ทดสอบส่งเมลภาษาไทย");
                $mailer->setReceiver("robot@dpo.go.th");
                $mailer->setReceiver("farang.c@live.com");
                if($mailer->sendMail()){
                    $this->data_result = ('Sent mail Room success');
                }else{
                    $this->data_result = ('Sent mail Room failed');
                }
                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        private function do_post_request($url, $method, $data = null, $optional_headers = null)
        {
              $params = array('http' => array(
                          'method' => $method,
                          'content' => http_build_query($data)
                        ));
              if ($optional_headers !== null) {
                $params['http']['header'] = $optional_headers;
              }
              $ctx = stream_context_create($params);
              $fp = @fopen($url, 'rb', false, $ctx);
               if (!$fp) {
                    return array("STATUS"=>'ERROR',"MSG"=>"ERROR :: Problem with $url, $php_errormsg");
                //throw new Exception("Problem with $url, $php_errormsg");
              }
              $response = @stream_get_contents($fp);
              if ($response === false) {
                    return array("STATUS"=>'ERROR',"MSG"=>"ERROR :: Problem reading data from $url, $php_errormsg");
    //            throw new Exception("Problem reading data from $url, $php_errormsg");
              }

              return $response;
              
        }

        
    }


?>