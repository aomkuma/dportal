<?php

    namespace App\Controller;
    
    class SystemManageController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        public function getSettingManageList($request, $response, $args){
            try{
                include '../src/settings_var.php';

                $setting_content['AD']['AD_HOST'] = $AD_HOST;
                $setting_content['AD']['AD_PORT'] = $AD_PORT;
                $setting_content['AD']['AD_PRINCIPAL'] = $AD_PRINCIPAL;

                $setting_content['SMS']['SMS_FROM'] = $SMS_FROM;
                $setting_content['SMS']['SMS_ENDPOINT'] = $SMS_ENDPOINT;
                $setting_content['SMS']['SMS_USERNAME'] = $SMS_USERNAME;
                $setting_content['SMS']['SMS_PASSWORD'] = $SMS_PASSWORD;

                $setting_content['MAIL']['MAIL_HOST'] = $MAIL_HOST;
                $setting_content['MAIL']['MAIL_PORT'] = $MAIL_PORT;
                $setting_content['MAIL']['MAIL_USERNAME'] = $MAIL_USERNAME;
                $setting_content['MAIL']['MAIL_PASSWORD'] = $MAIL_PASSWORD;
                $setting_content['MAIL']['MAIL_FROM'] = $MAIL_FROM;
                $setting_content['MAIL']['MAIL_FROMNAME'] = $MAIL_FROMNAME;

                $setting_content['EHR']['EHR_OFFICE'] = $EHR_OFFICE;                
                $setting_content['EHR']['EHR_DIVISION'] = $EHR_DIVISION;                
                $setting_content['EHR']['EHR_DEPARTMENT'] = $EHR_DEPARTMENT;                
                $setting_content['EHR']['EHR_STAFF'] = $EHR_STAFF;                

                $this->data_result['DATA'] = $setting_content;
                return $this->returnResponse(200, $this->data_result, $response, false);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $e, $response);
            }
            
        }

        public function saveSettingManageList($request, $response, $args){
            try{

                $parsedBody = $request->getParsedBody();
                // print_r($parsedBody);exit;
                foreach ($parsedBody['data'] as $key => $value) {
                    ${$key} = $value;
                }

                $settings_dir = '../src/settings_var.php';

                $content = 
'<?php

    $AD_HOST = "'.$AD['AD_HOST'].'";
    $AD_PORT = "'.$AD['AD_PORT'].'";
    $AD_PRINCIPAL = "'.$AD['AD_PRINCIPAL'].'";

    $SMS_FROM = "'.$SMS['SMS_FROM'].'";
    $SMS_ENDPOINT = "'.$SMS['SMS_ENDPOINT'].'";
    $SMS_USERNAME = "'.$SMS['SMS_USERNAME'].'";
    $SMS_PASSWORD = "'.$SMS['SMS_PASSWORD'].'";

    $MAIL_HOST = "'.$MAIL['MAIL_HOST'].'";
    $MAIL_PORT = "'.$MAIL['MAIL_PORT'].'";
    $MAIL_USERNAME = "'.$MAIL['MAIL_USERNAME'].'";
    $MAIL_PASSWORD = "'.$MAIL['MAIL_PASSWORD'].'";
    $MAIL_FROM = "'.$MAIL['MAIL_FROM'].'";
    $MAIL_FROMNAME = "'.$MAIL['MAIL_FROMNAME'].'";

    $EHR_OFFICE = "'.$EHR['EHR_OFFICE'].'";
    $EHR_DIVISION = "'.$EHR['EHR_DIVISION'].'";
    $EHR_DEPARTMENT = "'.$EHR['EHR_DEPARTMENT'].'";
    $EHR_STAFF = "'.$EHR['EHR_STAFF'].'";

?>';
                
                $f = fopen($settings_dir, 'w');
                fwrite($f, $content);
                fclose($f);

                $this->data_result['DATA'] = $content;
                return $this->returnResponse(200, $this->data_result, $response, false);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $e, $response);
            }
            
        }        
       
        public function getLogManageList($request, $response, $args){
            try{
                $dir = '../logs/';
                $files = $this->scan_dir($dir);
                $log_list = [];
                foreach ($files as $key => $value) {
                    if(strtolower(substr($value, strrpos($value, '.') + 1)) == 'log'){
                        $log_list[]['filename'] = $value;
                    }
                }
                $this->data_result['DATA']['DataList'] = $log_list;
                return $this->returnResponse(200, $this->data_result, $response, false);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $e, $response);
            }
            
        }

        public function downloadLog($request, $response, $args){
            try{
                $dir = '../logs/';
                $parsedBody = $request->getParsedBody();
                $logName = $parsedBody['logName'];
                $logContent = file_get_contents($dir.$logName);

                $this->data_result['DATA'] = base64_encode($logContent);
                return $this->returnResponse(200, $this->data_result, $response, false);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $e, $response);
            }
            
        }

        private function scan_dir($dir) {
            $ignored = array('.', '..', '.svn', '.htaccess');

            $files = array();    
            foreach (scandir($dir) as $file) {
                if (in_array($file, $ignored)) continue;
                $files[$file] = filemtime($dir . '/' . $file);
            }

            arsort($files);
            $files = array_keys($files);

            return ($files) ? $files : false;
        }

        
    }

?>