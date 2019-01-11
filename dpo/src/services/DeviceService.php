<?php

    namespace App\Service;
    
    use App\Model\Device;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class DeviceService {
        
        public static function getDeviceList($offset){

        	$limit = 15;
			$skip = $offset * $limit;
			$total = Device::count();
			
			$DataList = Device::select('Device.*', 'REGION.RegionName AS RegionName')
				    ->leftJoin('REGION', 'Device.RegionID', '=', 'REGION.RegionID')
                    ->skip($skip)
					->take($limit)
                    ->orderBy('DeviceID', 'DESC')
					->get();

			$offset += 1;
			$continueLoad = true;
			if(ceil($total / $limit) == $offset){
				$continueLoad = false;
			}

			return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

        public static function updateData($obj){
        	if($obj['DeviceID'] == ''){
                $Device = new Device;
            }else{
                $Device = Device::find($obj['DeviceID']);
            }
            $Device = $Device->setValues($Device , $obj);
            $Device->save();
			return $Device;
        }

        public static function deleteData($ID){
			return Device::find($ID)->delete();
        }  
        
    }    

?>