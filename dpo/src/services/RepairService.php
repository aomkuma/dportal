<?php

    namespace App\Service;
    
    use App\Model\RepairType;
    use App\Model\RepairTitle;
    use App\Model\RepairIssue;
    use App\Model\RepairSubIssue;
    use App\Model\Repair;
    use App\Model\Region;
    use App\Model\Account;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class RepairService {
        
        // Repair Type
        public static function getRepairTypeList($mode){

			$DataList = RepairType::where(function($query) use ($mode){
                        if($mode == 'view'){
                            $query->where('ActiveStatus', 'Y');
                        }
                    })
                    ->orderBy('RepairedTypeID', 'DESC')
					->get();

			return [ 'DataList'=>$DataList ];

        }

        public static function getRepairType($ID){
            return RepairType::with(array('RepairTitle' => function($query)
                                {
                                    $query->select("REPAIRED_TITLE.*","DEPARTMENT.orgName");
                                    $query->join('DEPARTMENT', "DEPARTMENT.orgId","=","REPAIRED_TITLE.DepartmentID");
                                }))->find($ID);
        }

        public static function updateRepairType($obj){
        	if($obj['RepairedTypeID'] == ''){
                $RepairType = new RepairType;
            }else{
                $RepairType = RepairType::find($obj['RepairedTypeID']);
            }
            $RepairType = $RepairType->setValues($RepairType , $obj);
            $RepairType->save();
			return $RepairType;
        }

        public static function deleteRepairType($ID){
            return RepairType::find($ID)->delete();
        } 
        // Repair Type
        /*
        ....................
        ....................
        */

        // Repair Title
        public static function getRepairTitleList($mode, $RepairedTypeID){
            $DataList = RepairTitle::where(function($query) use ($mode, $RepairedTypeID){
                        if($mode == 'view'){
                            $query->where('ActiveStatus', 'Y');
                        }
                        if(!empty($RepairedTypeID)){
                            $query->where('RepairedTypeID', $RepairedTypeID);
                        }
                    })
                    
                    ->orderBy('RepairedTitleID', 'DESC')
                    ->get();

            return [ 'DataList'=>$DataList ];
        }

        public static function getRepairTitle($RepairTitleID){
            return RepairTitle::with('repairIssue')->find($RepairTitleID);
        }

        public static function updateRepairTitle($obj){
            if($obj['RepairedTitleID'] == ''){
                $RepairTitle = new RepairTitle;
            }else{
                $RepairTitle = RepairTitle::find($obj['RepairedTitleID']);
            }
            $RepairTitle = $RepairTitle->setValues($RepairTitle , $obj);
            $RepairTitle->save();
            return $RepairTitle;
        }

        public static function deleteRepairTitle($ID){
            return RepairTitle::find($ID)->delete();
        } 
        // Repair Title
        /*
        ....................
        ....................
        */
        public static function getRepairIssueList($mode, $RepairedTitleID){
            $DataList = RepairIssue::where(function($query) use ($mode, $RepairedTitleID){
                        if($mode == 'view'){
                            $query->where('ActiveStatus', 'Y');
                        }
                        if(!empty($RepairedTitleID)){
                            $query->where('RepairedTitleID', $RepairedTitleID);    
                        }
                    })
                    ->orderBy('RepairedIssueID', 'DESC')
                    ->get();

            return [ 'DataList'=>$DataList ];
        }

        public static function getRepairIssue($RepairIssueID){
            return RepairIssue::with('repairSubIssue')->find($RepairIssueID);
        }

        public static function updateRepairIssue($obj){
            if($obj['RepairedIssueID'] == ''){
                $RepairIssue = new RepairIssue;
            }else{
                $RepairIssue = RepairIssue::find($obj['RepairedIssueID']);
            }
            $RepairIssue = $RepairIssue->setValues($RepairIssue , $obj);
            $RepairIssue->save();
            return $RepairIssue;
        }

        public static function deleteRepairIssue($ID){
            return RepairIssue::find($ID)->delete();
        } 

        /*
        ....................
        ....................
        */
        public static function getRepairSubIssueList($mode, $RepairedIssueID){
            $DataList = RepairSubIssue::where(function($query) use ($mode, $RepairedIssueID){
                        if($mode == 'view'){
                            $query->where('ActiveStatus', 'Y');
                        }
                        if(!empty($RepairedIssueID)){
                            $query->where('RepairedIssueID', $RepairedIssueID);
                        }
                    })
                    
                    ->orderBy('RepairedSubIssueID', 'DESC')
                    ->get();

            return [ 'DataList'=>$DataList ];
        }

        public static function getRepairSubIssue($RepairSubIssueID){
            return RepairSubIssue::find($RepairSubIssueID);
        }

        public static function updateRepairSubIssue($obj){
            if($obj['RepairedSubIssueID'] == ''){
                $RepairSubIssue = new RepairSubIssue;
            }else{
                $RepairSubIssue = RepairSubIssue::find($obj['RepairedSubIssueID']);
            }
            $RepairSubIssue = $RepairSubIssue->setValues($RepairSubIssue , $obj);
            $RepairSubIssue->save();
            return $RepairSubIssue;
        }

        public static function deleteRepairSubIssue($ID){
            return RepairSubIssue::find($ID)->delete();
        } 

        public static function getRepair($ID){
            return Repair::find($ID);
        } 

        public static function updateRepair($obj, $ip){
            if($obj['RepairedID'] == ''){
                $Repair = new Repair;
            }else{
                $Repair = Repair::find($obj['RepairedID']);
            }
            $Repair = $Repair->setValues($Repair , $obj);
            if(empty($Repair->RequestorIP))
            {
                $Repair->RequestorIP = $ip;
            }
            $Repair->save();
            return $Repair;
        }
        
        public static function getRepairForNotify($RepairedID){
            return Repair::select("REPAIRED.RegionID"
                                , "REPAIRED.RepairedID"
                                , "REPAIRED.RepairedStatus"
                                , "REPAIRED.RepairedDetail"
                                , "REPAIRED.RepairedCode"
                                , "REPAIRED.CreateBy"
                                , "REPAIRED_SUB_ISSUE.RepairedSubIssueName"
                                , DB::raw("CONCAT(TBL_REPAIRED_SUB_ISSUE.SLAHour, ' ชั่วโมง ', TBL_REPAIRED_SUB_ISSUE.SLAMinuteม , ' นาที ') AS SLA")
                                , "REPAIRED_TYPE.RepairedTypeName"
                                , "REPAIRED_TITLE.RepairedTitleName"
                                , "REPAIRED_TITLE.DepartmentID"
                                , "REPAIRED_ISSUE.RepairedIssueName"
                                , "REGION.RegionName"
                                , "ACCOUNT.FirstName"
                                , "ACCOUNT.LastName"
                                , "ACCOUNT.Mobile"
                                , "ACCOUNT.Email")
                    ->join("REPAIRED_SUB_ISSUE", "REPAIRED_SUB_ISSUE.RepairedSubIssueID" , "=", "REPAIRED.RepairedSubIssueID")
                    ->join("REPAIRED_ISSUE", "REPAIRED_ISSUE.RepairedIssueID" , "=", "REPAIRED.RepairedIssueID")
                    ->join("REPAIRED_TITLE", "REPAIRED_TITLE.RepairedTitleID" , "=", "REPAIRED.RepairedTitleID")
                    ->join("REPAIRED_TYPE", "REPAIRED_TYPE.RepairedTypeID" , "=", "REPAIRED.RepairedTypeID")
                    ->join("REGION", "REGION.RegionID" , "=", "REPAIRED.RegionID")
                    ->join("ACCOUNT", "ACCOUNT.UserID" , "=", "REPAIRED.CreateBy")
                    ->where("REPAIRED.RepairedID" , $RepairedID)
                    ->first()
                    ;
        }

        public static function getRepairForNotify24Hours($dateCheck){
            return Repair::select("REPAIRED.RegionID"
                                , "REPAIRED.RepairedID"
                                , "REPAIRED.RepairedStatus"
                                , "REPAIRED.RepairedDetail"
                                , "REPAIRED.RepairedCode"
                                , "REPAIRED.CreateBy"
                                , "REPAIRED.UpdateDateTime"
                                , "REPAIRED_TYPE.RepairedTypeName"
                                , "REGION.RegionName"
                                , "ACCOUNT.FirstName"
                                , "ACCOUNT.LastName"
                                , "ACCOUNT.Mobile"
                                , "ACCOUNT.Email")
                    ->join("REPAIRED_TYPE", "REPAIRED_TYPE.RepairedTypeID" , "=", "REPAIRED.RepairedTypeID")
                    ->join("REGION", "REGION.RegionID" , "=", "REPAIRED.RegionID")
                    ->join("ACCOUNT", "ACCOUNT.UserID" , "=", "REPAIRED.CreateBy")
                    ->where("REPAIRED.RepairedStatus" , 'Request')
                    ->whereNull("REPAIRED.ReceiveDateTime")
                    ->whereNull("REPAIRED.Notify24Hrs")
                    ->where(DB::raw("DATEDIFF(day,TBL_REPAIRED.UpdateDateTime,'".$dateCheck."')") , ">" , 1)
                    ->get()
                    ;
        }

        public static function getRepairAdminForNotify($RepairedID){
            return Repair::select("REPAIRED.RegionID"
                                , "REPAIRED.RepairedID"
                                , "REPAIRED.RepairedStatus"
                                , "REPAIRED.RepairedDetail"
                                , "REPAIRED.RepairedCode"
                                , "REPAIRED.CreateBy"
                                , "REPAIRED.CompleteDateTime"
                                , "REPAIRED.SuspenedComment"
                                , "REPAIRED_SUB_ISSUE.RepairedSubIssueName"
                                , "REPAIRED_TYPE.RepairedTypeName"
                                , "REPAIRED_TITLE.RepairedTitleName"
                                , "REPAIRED_ISSUE.RepairedIssueName"
                                , "REGION.RegionName"
                                , "ACCOUNT.FirstName"
                                , "ACCOUNT.LastName"
                                , "ACCOUNT.Mobile"
                                , "ACCOUNT.Email")
                    ->join("REPAIRED_SUB_ISSUE", "REPAIRED_SUB_ISSUE.RepairedSubIssueID" , "=", "REPAIRED.RepairedSubIssueID")
                    ->join("REPAIRED_ISSUE", "REPAIRED_ISSUE.RepairedIssueID" , "=", "REPAIRED.RepairedIssueID")
                    ->join("REPAIRED_TITLE", "REPAIRED_TITLE.RepairedTitleID" , "=", "REPAIRED.RepairedTitleID")
                    ->join("REPAIRED_TYPE", "REPAIRED_TYPE.RepairedTypeID" , "=", "REPAIRED.RepairedTypeID")
                    ->join("REGION", "REGION.RegionID" , "=", "REPAIRED.RegionID")
                    ->join("ACCOUNT", "ACCOUNT.UserID" , "=", "REPAIRED.AdminID")
                    ->where("REPAIRED.RepairedID" , $RepairedID)
                    ->first()
                    ;
        }

        public static function updateAdminReceiveRepair($obj, $AdminID){
            
            $Repair = Repair::find($obj['RepairedID']);
            $Repair->AdminID = $AdminID;
            $Repair->RepairedStatus = 'Receive';
            $Repair->ReceiveDateTime = date('Y-m-d H:i:s.000');
            $Repair->save();
            return $Repair;
        }

        public static function updateRepairAdmin($obj, $RepairStatus){
            
            $Repair = Repair::find($obj['RepairedID']);
            $Repair->RepairedStatus = $RepairStatus;
            $Repair->FinishComment = $obj['FinishComment'];
            $Repair->SuspenedComment = $obj['SuspenedComment'];
            $Repair->CancelComment = $obj['CancelComment'];
            $Repair->CompleteDateTime = empty($Repair->CompleteDateTime)?date('Y-m-d H:i:s.000') : $Repair->CompleteDateTime;
            if($RepairStatus == 'Finish' && empty($Repair->SLAStatus)){
                //  Find SLA
                $sla = RepairService::getSLA($Repair->RepairedSubIssueID);
                $startDate = strtotime(str_replace('.000','', $Repair->CreateDateTime ));
                $endDate = strtotime(str_replace('.000','', $Repair->CompleteDateTime ));
                // $datediff = $endDate - $startDate;
                // $diffDay = floor($datediff / (60 * 60 * 24 * 24));
                $timeDiff = floor($endDate - $startDate);
                $diffDay = $timeDiff/86400;
                if($diffDay > $sla){
                    $Repair->SLAStatus = 0;
                }else{
                    $Repair->SLAStatus = 1;
                }
            }
            
            $Repair->save();
            return $Repair;
        }

        public static function updateRepairNotify24Hours($RepairedID){
            Repair::where('RepairedID', $RepairedID)->update(['Notify24Hrs' => 'Y']);
        }

        public static function getSLA($RepairedSubIssueID){
            $data = RepairSubIssue::find($RepairedSubIssueID);
            return $data->SLA;
        }

    }    

?>