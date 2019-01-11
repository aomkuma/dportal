<?php

    namespace App\Service;
    
    use App\Model\RunningNo;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class RunningNoService {
   
        public static function updateGetRunningNo($prefix, $curDate){
            $runningNo = RunningNo::where("RunningCode", $prefix)
                        ->where("RunningDate", $curDate)
                        ->first();
            if(empty($runningNo)){
            	$runningNo = new RunningNo;
            	$newRunningNo = 1;
            	$runningNo->RunningCode = $prefix;
                $runningNo->RunningDate = $curDate;
            }else{
            	$newRunningNo = intval($runningNo->RunningNo);
            	if($newRunningNo == 999){
            		$newRunningNo = 1;
            	}else{
            		$newRunningNo = intval($runningNo->RunningNo) + 1;	
            	}
            	
            }
            
            $runningNo->RunningNo = $newRunningNo;
            $runningNo->save();
			return $newRunningNo;
        }
        
    }    

?>