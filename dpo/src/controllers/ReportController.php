<?php

    namespace App\Controller;
    
    use App\Service\ReportService;
    use PHPExcel;

    class ReportController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }
        public function loadPDF($request, $response, $args){
            $pdfName = filter_var($request->getAttribute('pdfName'), FILTER_SANITIZE_STRING);
            $filename = '../../'.$pdfName;
            $arrFiles = glob($filename);
            $file = $arrFiles[0];
            ob_end_clean();
            ob_end_flush();
            header("Content-Length: " . filesize ($file) );
            header("Content-type: application/pdf");
            header("Content-disposition: inline;
            filename=".basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            $filepath = readfile($file);
            //echo $pdfContent = file_get_contents('../../dportal/'.$pdfName);
            //$this->data_result['DATA'] = base64_encode($pdfContent);
              
            //return $this->returnResponse(200, $this->data_result, $response);
        }

        public function exportExcel($request, $response, $args){
            // error_reporting(E_ERROR);
            // error_reporting(E_ALL);
            // ini_set('display_errors','On');           
            try{
                $obj = $request->getParsedBody();
                //print_r($obj);
                $condition = $obj['condition'];
                $data = $obj['data'];
                $summary = $obj['summary'];

                $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
                $catch_result = \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

                $objPHPExcel = new PHPExcel();
                switch($condition['report_type']){
                    case 'detail_repair' : $objPHPExcel = $this->generateExcelDetailRepair($objPHPExcel, $condition, $data, $summary); break;
                    case 'summary_repair' : $objPHPExcel = $this->generateExcelSummaryRepair($objPHPExcel, $condition, $data, $summary); break;
                    case 'detail_room' : $objPHPExcel = $this->generateExcelDetailRoom($objPHPExcel, $condition, $data, $summary); break;
                    case 'summary_room' : $objPHPExcel = $this->generateExcelSummaryRoom($objPHPExcel, $condition, $data, $summary); break;
                    case 'detail_car' : $objPHPExcel = $this->generateExcelDetailCar($objPHPExcel, $condition, $data, $summary); break;
                    case 'summary_car' : $objPHPExcel = $this->generateExcelSummaryCar($objPHPExcel, $condition, $data, $summary); break;
                    default : $result = null;
                }
                
                $filename = $condition['report_type'] . '_' . date('YmdHis') . '.xlsx';
                $filepath = '../../downloads/' . $filename;
                
                $objWriter = \PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
                $objWriter->setPreCalculateFormulas(); 
                
                $objWriter->save ( $filepath );
                
                $this->data_result['DATA'] = $filename;
                
                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }
       
        public function queryReport($request, $response, $args){
            // error_reporting(E_ERROR);
            // error_reporting(E_ALL);
            // ini_set('display_errors','On');
            try{
                $obj = $request->getParsedBody();
                
                switch($obj['report_type']){
                    case 'detail_repair' : $result = $this->queryDetailRepair($obj); break;
                    case 'summary_repair' : $result = $this->querySummaryRepair($obj); break;
                    case 'detail_room' : $result = $this->queryDetailRoom($obj); break;
                    case 'summary_room' : $result = $this->querySummaryRoom($obj); break;
                    case 'detail_car' : $result = $this->queryDetailCar($obj); break;
                    case 'summary_car' : $result = $this->querySummaryCar($obj); break;
                    default : $result = null;
                }
                
                $this->data_result['DATA'] = $result;

                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function loadRoomListByRegion($request, $response, $args){

            try{

                $regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT);
                $result = ReportService::loadRoomListByRegion($regionID);
                $this->data_result['DATA'] = $result;

                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function loadCarListByRegion($request, $response, $args){

            try{

                $regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT);
                $result = ReportService::loadCarListByRegion($regionID);
                $this->data_result['DATA'] = $result;

                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function loadUserListByRegion($request, $response, $args){

            try{

                $regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT);
                $result = ReportService::loadUserListByRegion($regionID);
                $this->data_result['DATA'] = $result;

                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        public function loadUserListByRegionAndRole($request, $response, $args){

            try{

                $regionID = filter_var($request->getAttribute('regionID'), FILTER_SANITIZE_NUMBER_INT);
                $result = ReportService::loadUserListByRegionAndRole($regionID);
                $this->data_result['DATA'] = $result;

                return $this->returnResponse(200, $this->data_result, $response);
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
            
        }

        private function queryDetailRepair($condition){
    //                 error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');
            //print_r($condition);
            $condition['RepairedTypeID'] = $condition['RepairedTypeID']['RepairedTypeID'];
            $condition['RegionID'] = $condition['Region']['RegionID'];
            $condition['UserID'] = $condition['UserID']['UserID'];
            $condition['Day'] = $condition['Day']['dayText'];
            $condition['Month'] = $condition['Month']['monthText'];
            //$condition['Year'] = $condition['Year']['yearText'];

            if(empty($condition['Day'])&& !empty($condition['Month'])){
                $startDate = ($condition['Year']) . '-' . $condition['Month'] . '-01 00:00:00.000';
                $lastDay = $this->getMaxDayInmonth($startDate);
                $endDate = ($condition['Year']) . '-' . $condition['Month'] . '-' . $lastDay . ' 23:59:59.000';

            }else if(empty($condition['Day'])&& empty($condition['Month'])){
                $startDate = ($condition['Year']) . '-01-01 00:00:00.000';
                $endDate = ($condition['Year']) . '-12-31 23:59:59.000';

            }else{
                $startDate = ($condition['Year']) . '-' . $condition['Month'] . '-' . $condition['Day'] . ' 00:00:00.000';
                $endDate = ($condition['Year']) . '-' . $condition['Month'] . '-' . $condition['Day'] . ' 23:59:59.000';
            }
            
            // get data
            $RoomDetail = ReportService::getDetailRepair($condition['RepairedTypeID'], $condition['RegionID'], $condition['UserID'], $startDate, $endDate);
            return $RoomDetail;
        }
        
        private function querySummaryRepair($condition){
            $RepairType = $condition['RepairType'];
            $RepairedTitle = $condition['RepairedTitle'];
            $RepairIssue = $condition['RepairIssue'];
            $RepairSubIssue = $condition['RepairSubIssue'];
            $dateCondition = $this->calculateFiscalYear($condition['Year']);
            $startDate = $dateCondition['startDate'];
            $endDate = $dateCondition['endDate'];
            // get Repair sub issue
            $RepairSubIssueList = ReportService::getRepairSubIssueList($startDate, $endDate, $RepairType, $RepairedTitle, $RepairIssue, $RepairSubIssue);

            $result = [];

            $sumTotal = 0;
            $sumFinish = 0;
            $sumHold = 0;
            $sumCancel = 0;
            $sumPassSLA = 0;
            $sumFailSLA = 0;

            foreach ($RepairSubIssueList as $key => $value) {
                // Count total notify
                $countTotalRepairNotify = ReportService::countTotalRepairNotify($value['RepairedSubIssueID'], $startDate, $endDate);
                $value['countTotal'] = $countTotalRepairNotify;
                // count finish
                $countFinish = ReportService::countRepairFinish($value['RepairedSubIssueID'], $startDate, $endDate);
                $value['countFinish'] = $countFinish;
                // count hold 
                $countHold = ReportService::countRepairHold($value['RepairedSubIssueID'], $startDate, $endDate);
                $value['countHold'] = $countHold;
                // count cancel 
                $countCancel = ReportService::countRepairCancel($value['RepairedSubIssueID'], $startDate, $endDate);
                $value['countCancel'] = $countCancel;
                // count sla pass
                $countSLAPass = ReportService::countSLAPass($value['RepairedSubIssueID'], $startDate, $endDate);
                $value['countSLAPass'] = $countSLAPass;
                // count sla pass
                $countSLAFailed = ReportService::countSLAFailed($value['RepairedSubIssueID'], $startDate, $endDate);
                $value['countSLAFailed'] = $countSLAFailed;

                $sumTotal += intval($countTotalRepairNotify);
                $sumFinish += intval($countFinish);
                $sumHold += intval($countHold);
                $sumCancel += intval($countCancel);
                $sumPassSLA += intval($countSLAPass);
                $sumFailSLA += intval($countSLAFailed);

                $result[] = $value;
            }
            // set summary
            $summary = ['name'=>'รวม','sumTotal'=>$sumTotal, 'sumFinish'=>$sumFinish, 'sumHold'=>$sumHold, 'sumCancel'=>$sumCancel, 'sumPassSLA'=>$sumPassSLA, 'sumFailSLA'=>$sumFailSLA];

            return ['result'=>$result,'summary'=>$summary];
        }

        private function querySummaryRoom($condition){

            $regionID = $condition['Region']['RegionID'];
            $year = $condition['Year']['yearText'];
            $yearThai = $condition['Year']['yearValue'];
            $dateCondition = $this->calculateFiscalYear($year);
            $startDate = $dateCondition['startDate'];
            $endDate = $dateCondition['endDate'];
            // get Repair sub issue
            $RoomList = ReportService::getSummaryRoom($regionID, $startDate, $endDate, $year);

            $List = [];
            foreach ($RoomList as $key => $value) {
                // count total use
                $countUse = ReportService::countSummaryRoom($value['RoomID'], $startDate, $endDate);
                $value['CountUseRoom'] = $countUse;
                $value['ReportYear'] = $yearThai;
                $List[] = $value;
            }
            return $List;
        }

        private function queryDetailRoom($condition){
            // start date , end date
            
            $condition['RoomID'] = $condition['Room']['RoomID'];
            $condition['Month'] = $condition['Month']['monthText'];
            $condition['Year'] = $condition['Year']['yearText'];
            //print_r($condition['Month']);
            //echo $condition['RoomID'].$condition['Month'].$condition['Year'];
            $roomID = $condition['RoomID'];
            $startDate = ($condition['Year']) . '-' . $condition['Month'] . '-01 00:00:00.000';

            $endDate = ($condition['Year']) . '-' . $condition['Month'] . '-' . $this->getMaxDayInmonth($startDate) . ' 23:59:59.000';
            // get data
            $RoomDetail = ReportService::getDetailRoomUsing($roomID, $startDate, $endDate);
            $RoomDetailList = [];
            foreach($RoomDetail as $key => $val){
                $food = ReportService::getDetailFood($val['ReserveRoomID']);
                $device = ReportService::getDetailDevice($val['ReserveRoomID']);
                $val['food'] = $food;
                $val['device'] = $device;
                $RoomDetailList[] = $val;
            }
            return $RoomDetailList;

        }

        private function queryDetailCar($condition){
            // start date , end date
            $condition['CarID'] = $condition['Car']['CarID'];
            $condition['Month'] = $condition['Month']['monthText'];
            $condition['Year'] = $condition['Year']['yearText'];
            //print_r($condition['Month']);
            //echo $condition['RoomID'].$condition['Month'].$condition['Year'];
            $carID = $condition['CarID'];
            $startDate = ($condition['Year']) . '-' . $condition['Month'] . '-01 00:00:00.000';

            $endDate = ($condition['Year']) . '-' . $condition['Month'] . '-' . $this->getMaxDayInmonth($startDate) . ' 23:59:59.000';
            // get data
            $CarDetail = ReportService::getDetailCarUsing($carID, $startDate, $endDate);
            return $CarDetail;

        }

        private function querySummaryCar($condition){

            $regionID = $condition['Region']['RegionID'];
            $year = $condition['Year']['yearText'];
            $yearThai = $condition['Year']['yearValue'];
            $dateCondition = $this->calculateFiscalYear($year);
            $startDate = $dateCondition['startDate'];
            $endDate = $dateCondition['endDate'];
            // get car
            $CarList = ReportService::getSummaryCar($regionID, $startDate, $endDate);

            $List = [];
            foreach ($CarList as $key => $value) {
                // count total use
                $countUse = ReportService::countSummaryCar($value['CarID'], $startDate, $endDate);
                $value['CarName'] = $value['CarType'] . ' ' . $value['Brand'] . ' ' . $value['Model'];
                $value['CountUseCar'] = $countUse;
                $value['ReportYear'] = $yearThai;
                $List[] = $value;
            }
            return $List;
        }

        private function generateExcelDetailRepair($objPHPExcel, $condition, $data, $summary){
            
            $condition['Day'] = $condition['Day']['dayText'];
            $condition['Month'] = $condition['Month']['monthText'];
            //$condition['Year'] = $condition['Year']['yearText'];
            
            $reportDate = ($condition['Year']) . '-' . $condition['Month'] . '-' . $condition['Day'];

            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ตารางการแจ้งซ่อม');

            if(!empty($condition['Month']) && !empty($condition['Day'])){
                $objPHPExcel->getActiveSheet()->setCellValue('A2', 'ประจำวันที่ ' . $this->getReportDate($reportDate));
            }else if(!empty($condition['Month']) && empty($condition['Day'])){
                $objPHPExcel->getActiveSheet()->setCellValue('A2', $condition['Month'] . '/' . ($condition['Year'] + 543));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue('A2', 'ปี' . ($condition['Year'] + 543));
            }
            // set header
            $header = ['รหัสแจ้งซ่อม', 'ปัญหาย่อยงานซ่อม', "หน่วยงาน", "วันที่แจ้ง", "ผู้รับเรื่อง", 'SLA', 'สถานะ'];
            
            $objPHPExcel->getActiveSheet()->fromArray($header, NULL, 'A4' );
            $objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
            
            // re format data
            $new_data = [];
            $con_row = 5;
            $row_index = 1;
            foreach ($data as $key => $value) {
                $new_data[] = [$value['RepairedCode']
                                ,$value['RepairedSubIssueName']
                                ,$value['GroupName']
                                ,$value['CreateDateTime']
                                ,$value['RecieverFirstName'] . ' ' .$value['RecieverLastName']
                                ,$value['RepairedStatus']=='เสร็จสิ้น'?$value['SLAStatus'] == 1?'ผ่าน':'ไม่ผ่าน':'-'
                                ,$value['RepairedStatus']
                            ];
                $row_index++;
            }

            $objPHPExcel->getActiveSheet()->fromArray($new_data, NULL, 'A5' );

            $objPHPExcel->getActiveSheet()
            ->getStyle("A4:G" . $objPHPExcel->getActiveSheet()->getHighestRow())
            ->applyFromArray($this->getDefaultStyle());

            // set header align center
            $objPHPExcel->getActiveSheet()
            ->getStyle("A4:G4")
            ->applyFromArray(array(                  
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                     )
                )
            );

            // header style
            $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);

            return $objPHPExcel;
        }

        private function getReportDate($d){
            $date = explode("-", $d);
            $monthTxt = '';
            switch(intval($date[1])){
                case 1 : $monthTxt = 'มกราคม';break;
                case 2 : $monthTxt = 'กุมภาพันธ์';break;
                case 3 : $monthTxt = 'มีนาคม';break;
                case 4 : $monthTxt = 'เมษายน';break;
                case 5 : $monthTxt = 'พฤษภาคม';break;
                case 6 : $monthTxt = 'มิถุนายน';break;
                case 7 : $monthTxt = 'กรกฎาคม';break;
                case 8 : $monthTxt = 'สิงหาคม';break;
                case 9 : $monthTxt = 'กันยายน';break;
                case 10 : $monthTxt = 'ตุลาคม';break;
                case 11 : $monthTxt = 'พฤษจิกายน';break;
                case 12 : $monthTxt = 'ธันวาคม';break;
            }
            return $date[2] . ' ' . $monthTxt . ' ' . ($date[0] + 543);
        }

        private function generateExcelSummaryRepair($objPHPExcel, $condition, $data, $summary){

            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'สรุปการแจ้งซ่อม ประจำปีงบประมาณ ' . ($condition['Year'] + 543));

            // set header
            $header = ['ประเภทงานซ่อม', 'หัวข้องานซ่อม', 'ปัญหางานซ่อม','ปัญหาย่อยงานซ่อม', 'จำนวนที่รับแจ้ง', 'ซ่อมเสร็จสิ้น', 'ระงับชั่วคราว' , 'ยกเลิกงานซ่อม' , 'ผ่าน SLA' , 'ไม่ผ่าน SLA'];
            $objPHPExcel->getActiveSheet()->fromArray($header, NULL, 'A3' );

            // re format data
            //$new_data = [];
            $con_row = 4;
            foreach ($data as $key => $value) {
                //$new_data[] = [$value['RepairedSubIssueName'], $value['countTotal'],$value['countFinish'],$value['countHold'],$value['countCancel'],0,0];
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$con_row, $value['RepairedTypeName']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$con_row, $value['RepairedTitleName']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$con_row, $value['RepairedIssueName']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$con_row, $value['RepairedSubIssueName']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$con_row, $value['countTotal']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$con_row, $value['countFinish']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$con_row, $value['countHold']);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$con_row, $value['countCancel']);
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$con_row, $value['countSLAPass']);
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$con_row, $value['countSLAFailed']);
                $con_row++;
            }

            //$objPHPExcel->getActiveSheet()->fromArray($new_data, NULL, 'A4' );
            
            // set summary
            $lastRow = $objPHPExcel->getActiveSheet()->getHighestRow() + 1;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$lastRow, $summary['name']);
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$lastRow.':D'.$lastRow);

            $objPHPExcel->getActiveSheet()->setCellValue('E'.$lastRow, $summary['sumTotal']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$lastRow, $summary['sumFinish']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$lastRow, $summary['sumHold']);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$lastRow, $summary['sumCancel']);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$lastRow, $summary['sumPassSLA']);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$lastRow, $summary['sumFailSLA']);

            $objPHPExcel->getActiveSheet()
            ->getStyle("A3:J" . $objPHPExcel->getActiveSheet()->getHighestRow())
            ->applyFromArray($this->getDefaultStyle());

            // header style
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);

            // detail style
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(24);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(24);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(24);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(24);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(16);

            $objPHPExcel->getActiveSheet()->getStyle('A'.$lastRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            return $objPHPExcel;
        }

        private function generateExcelDetailRoom($objPHPExcel, $condition, $data, $summary){
            
            $room_name = $condition['Room']['RoomName'];
            $region_name = $condition['Region']['RegionName'];
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ตารางการใช้งานห้องประชุม');
            $objPHPExcel->getActiveSheet()->setCellValue('A2', 'ห้องประชุม ' . $room_name . ' พื้นที่ ' . $region_name);
            
            // set header
            $header = ['ลำดับ', 'ผู้ทำการจอง', "เริ่มประชุม\n วัน / เวลา", "สิ้นสุดประชุม\n วัน / เวลา", 'อุปกรณ์', 'อาหาร', 'หัวข้อการประชุม'];
            
            $objPHPExcel->getActiveSheet()->fromArray($header, NULL, 'A4' );
            $objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
            
            // re format data
            $new_data = [];
            $con_row = 5;
            $row_index = 1;
            foreach ($data as $key => $value) {
                $new_data[] = [$row_index
                                ,$value['FirstName'] . ' ' .$value['LastName']
                                ,$value['StartDateTime']
                                ,$value['EndDateTime']
                                ,$this->getDeviceData($value['device'])
                                ,$this->getFoodData($value['food'])
                                ,$value['TopicConference']
                            ];
                $row_index++;
            }

            $objPHPExcel->getActiveSheet()->fromArray($new_data, NULL, 'A5' );

            $objPHPExcel->getActiveSheet()
            ->getStyle("A4:G" . $objPHPExcel->getActiveSheet()->getHighestRow())
            ->applyFromArray($this->getDefaultStyle());

            // set header align center
            $objPHPExcel->getActiveSheet()
            ->getStyle("A4:G4")
            ->applyFromArray(array(                  
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                     )
                )
            );

            // header style
            $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);

            return $objPHPExcel;
        }

        private function getDeviceData($o){
            $str = '';
            foreach ($o as $key => $value) {
                $str .= $value['DeviceName'] . "       ". $value['Amount']."\n";
            }
            return $str;
        }

        private function getFoodData($o){
            $str = '';
            foreach ($o as $key => $value) {
                $str .= $value['FoodName'] . "       ". $value['Amount']."\n";
            }
            return $str;   
        }

        private function generateExcelSummaryRoom($objPHPExcel, $condition, $data, $summary){
            
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'สรุปการใช้ห้องประชุมประจำปี');
            
            // set header
            $header = ['ห้องประชุม', 'พื้นที่', "จำนวนครั้งที่ใช้งาน", 'ปี'];
            
            $objPHPExcel->getActiveSheet()->fromArray($header, NULL, 'A3' );
            
            // re format data
            //$new_data = [];
            $con_row = 4;
            foreach ($data as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$con_row, $value['RoomName']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$con_row, $value['RegionName']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$con_row, $value['CountUseRoom']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$con_row, $value['ReportYear']);
                $con_row++;
            }

            $objPHPExcel->getActiveSheet()
            ->getStyle("A3:D" . $objPHPExcel->getActiveSheet()->getHighestRow())
            ->applyFromArray($this->getDefaultStyle());

            // set header align center
            $objPHPExcel->getActiveSheet()
            ->getStyle("A3:D3")
            ->applyFromArray(array(                  
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                     )
                )
            );

            $objPHPExcel->getActiveSheet()
            ->getStyle("C4:D" . $objPHPExcel->getActiveSheet()->getHighestRow())
            ->applyFromArray(array(                  
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                     )
                )
            );

            // header style
            $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
            
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);

            return $objPHPExcel;
        }

        private function generateExcelDetailCar($objPHPExcel, $condition, $data, $summary){
            
            $car_license = $condition['Car']['License'];
            $region_name = $condition['Region']['RegionName'];
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ตารางบันทึกการใช้ยานพาหนะ');
            $objPHPExcel->getActiveSheet()->setCellValue('A2', 'รถหมายเลขทะเบียน ' . $car_license);
            
            // set header
            $header = ['ลำดับ', "เดินทางไป\n วัน / เวลา", 'ผู้ใช้รถ', 'สถานที่ไป', "เดินทางกลับ\n วัน / เวลา", 'พนักงานขับรถ', 'หมายเหตุ'];
            
            $objPHPExcel->getActiveSheet()->fromArray($header, NULL, 'A4' );
            
            // re format data
            $new_data = [];
            $con_row = 5;
            $row_index = 1;
            foreach ($data as $key => $value) {
                $new_data[] = [$row_index
                                ,$value['StartDateTime']
                                ,$value['FirstName'] . ' ' .$value['LastName']
                                ,$value['Destination']
                                ,$value['EndDateTime']
                                ,($value['DriverType'] == 'Internal'? $value['DriverFirstName'] . ' ' .$value['DriverLastName']: $value['DriverName'])
                                ,$value['Remark']
                            ];
                $row_index++;
            }

            $objPHPExcel->getActiveSheet()->fromArray($new_data, NULL, 'A5' );

            $objPHPExcel->getActiveSheet()
            ->getStyle("A4:G" . $objPHPExcel->getActiveSheet()->getHighestRow())
            ->applyFromArray($this->getDefaultStyle());

            // set header align center
            $objPHPExcel->getActiveSheet()
            ->getStyle("A4:G4")
            ->applyFromArray(array(                  
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                     )
                )
            );

            // header style
            $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);

            return $objPHPExcel;
        }

        private function generateExcelSummaryCar($objPHPExcel, $condition, $data, $summary){
            
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'สรุปการใช้ยานพาหนะประจำปี');
            
            // set header
            $header = ['ลำดับ', 'ยานพาหนะ', 'ทะเบียนรถ', 'พื้นที่', "จำนวนครั้งที่ใช้งาน", 'ปี'];
            
            $objPHPExcel->getActiveSheet()->fromArray($header, NULL, 'A3' );
            
            // re format data
            //$new_data = [];
            $con_row = 4;
            $row_index = 1;
            foreach ($data as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$con_row, $row_index);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$con_row, $value['CarName']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$con_row, $value['License']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$con_row, $value['RegionName']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$con_row, $value['CountUseCar']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$con_row, $value['ReportYear']);
                $con_row++;
                $row_index++;
            }

            $objPHPExcel->getActiveSheet()
            ->getStyle("A3:F" . $objPHPExcel->getActiveSheet()->getHighestRow())
            ->applyFromArray($this->getDefaultStyle());

            // set header align center
            $objPHPExcel->getActiveSheet()
            ->getStyle("A3:F3")
            ->applyFromArray(array(                  
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                     )
                )
            );

            $objPHPExcel->getActiveSheet()
            ->getStyle("D4:F" . $objPHPExcel->getActiveSheet()->getHighestRow())
            ->applyFromArray(array(                  
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                     )
                )
            );

            // header style
            $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
            
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

            return $objPHPExcel;
        }

        private function getDefaultStyle(){
            return array(                  
                    'borders' => array(
                        'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
                        'wrap' => true
                     ),
                     'font'  => array(
                        'size'  => 12,
                        // 'bold'  => true
                    )
                );
        }

        private function calculateFiscalYear($year){
            $startYear = $year - 1; 
            $endYear = $year;

            $startDate = $startYear . '-10-01 00:00:00.000';
            $endDate = $endYear . '-09-30 23:59:59.000';
            return ['startDate' => $startDate, 'endDate' => $endDate];
        }

        private function getMaxDayInmonth($dateStr){
            return date("t", strtotime($dateStr));
        }
    }

?>