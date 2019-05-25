<?php

    namespace App\Service;
    
    use App\Model\CarReserve;
    use App\Model\Car;
    use App\Model\Cartype;
    use App\Model\Province;
    use App\Model\Traveller;
    use App\Model\Notification;
    use App\Model\InternalDriver;
    use App\Model\ExternalDriver;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class CarReserveService {

        public static function getCarListDetail($condition){
            // print_r($condition);exit;
            return CarReserve::select("RESERVE_CAR.*"
                                    , DB::raw('a1.ProvinceName')
                                    ,'RegionName'
                                    ,DB::raw("CONCAT(acc1.FirstName, ' ', acc1.LastName) AS RequestName ")
                                    ,DB::raw("CONCAT(acc2.FirstName, ' ', acc2.LastName) AS ApproveName ")
                                )
                    ->leftJoin(DB::raw('TBL_PROVINCE as a1'), 'RESERVE_CAR.ProvinceID', '=', DB::raw('a1.ProvinceID'))
                    ->join("REGION", "RESERVE_CAR.RegionID", '=', 'REGION.RegionID')
                    ->leftJoin(DB::raw('TBL_ACCOUNT as acc1'), 'RESERVE_CAR.CreateBy', '=', DB::raw('acc1.UserID'))
                    ->leftJoin(DB::raw('TBL_ACCOUNT as acc2'), 'RESERVE_CAR.AdminID', '=', DB::raw('acc2.UserID'))
                    ->where(function($query) use ($condition){
                        if(!empty($condition['Region']['RegionID'])){
                            $query->where('RESERVE_CAR.RegionID', $condition['Region']['RegionID']);
                        }
                        if(!empty($condition['StartDate']) && !empty($condition['EndDate'])){
                            $condition['StartDate'] = substr($condition['StartDate'], 0,10) . ' 00:00:00';
                            $condition['EndDate'] = substr($condition['EndDate'], 0,10) . ' 23:59:59';
                            // $query->where('StartDateTime' ,'<=', $condition['StartDate']);
                            // $query->where('EndDateTime' ,'>=', $condition['EndDate']);    
                            $query->whereBetween('RESERVE_CAR.CreateDateTime' , [$condition['StartDate'] , $condition['EndDate']]);
                        }
                    })
                    ->get();
        }
        
        public static function getCarReserveDetail($ReserveCarID){
            return CarReserve::select("RESERVE_CAR.*", DB::raw('a1.ProvinceName'))
                    ->leftJoin(DB::raw('TBL_PROVINCE as a1'), 'RESERVE_CAR.ProvinceID', '=', DB::raw('a1.ProvinceID'))
                    ->find($ReserveCarID);
        }

        public static function checkSameDestination($ReserveCarID, $RegionID, $ProvinceID, $StartDateTime){
            return CarReserve::
                    //where('ReserveCarID', '<>', $ReserveCarID)
                    where('RegionID', $RegionID)
                    ->where('ProvinceID', $ProvinceID)
                    ->where('StartDateTime', $StartDateTime)
                    ->get();
        }

        public static function getCarDetail($CarID){
            return Car::select("CAR.*"
                        , DB::raw('a1.FirstName')
                        , DB::raw('a1.LastName')
                        , DB::raw('a1.Email')
                        , DB::raw('a1.Mobile')
                        , DB::raw('a2.CarType')
                        , DB::raw('a2.SeatAmount')
                        , DB::raw('a3.ProvinceName AS LicenseProvinceName')
                    )
                    ->leftJoin(DB::raw('TBL_ACCOUNT as a1'), 'CAR.CarAdminID', '=', DB::raw('a1.UserID'))
                    ->leftJoin(DB::raw('TBL_CAR_TYPE as a2'), 'CAR.CarTypeID', '=', DB::raw('a2.CarTypeID'))
                    ->leftJoin(DB::raw('TBL_PROVINCE as a3'), 'CAR.LicenceProvince', '=', DB::raw('a3.ProvinceID'))
                    ->where("CAR.CarID",$CarID)->first();
        }

        public static function getProvinceList(){
        	return Province::all();
        }

        public static function getTravellerList($ReserveCarID){
            return Traveller::select("TRAVELLER.*","ACCOUNT.FirstName","ACCOUNT.LastName","ACCOUNT.Mobile","ACCOUNT.Email")
                            ->join("ACCOUNT","TRAVELLER.UserID","=","ACCOUNT.UserID")
                            ->where('ReserveCarID',$ReserveCarID)
                            ->get();
        }

        public static function getInternalDriver($reserveCarID){
            return InternalDriver::select("INTERNAL_DRIVER.*","ACCOUNT.FirstName","ACCOUNT.LastName","ACCOUNT.Email","ACCOUNT.Mobile")
                                ->join("ACCOUNT" , "INTERNAL_DRIVER.UserID", "=", "ACCOUNT.UserID")
                                ->where('ReserveCarID',$reserveCarID)->first();
        }

        public static function getExternalDriver($reserveCarID){
            return ExternalDriver::where('ReserveCarID',$reserveCarID)->first();
        }

        public static function getMaxSeatAmount(){
        	return Cartype::select('SeatAmount')->groupBy('SeatAmount')->orderBy('SeatAmount', 'DESC')->first();
        }

        public static function getReserveCartypeList(){
            return Cartype::orderBy('SeatAmount')->get();
        }

        public static function getCarsInRegion($regionID, $findDate){
            return Car::select("CAR.*"
                        , DB::raw('a1.FirstName')
                        , DB::raw('a1.LastName')
                        , DB::raw('a1.Email')
                        , DB::raw('a1.Mobile')
                        , DB::raw('a2.CarType')
                        , DB::raw('a2.SeatAmount')
                        , DB::raw('a3.ProvinceName AS LicenseProvinceName')
                    )
                    ->leftJoin(DB::raw('TBL_ACCOUNT as a1'), 'CAR.CarAdminID', '=', DB::raw('a1.UserID'))
                    ->leftJoin(DB::raw('TBL_CAR_TYPE as a2'), 'CAR.CarTypeID', '=', DB::raw('a2.CarTypeID'))
                    ->leftJoin(DB::raw('TBL_PROVINCE as a3'), 'CAR.LicenceProvince', '=', DB::raw('a3.ProvinceID'))
                    ->where('CAR.RegionID',$regionID)
                    ->where('CAR.ActiveStatus','Y')
                    ->where(DB::raw('a2.ActiveStatus'),'Y')
                    ->orderBy('CarTypeID', 'ASC')
                    ->with(['reserveCars' => function($q) use ($findDate){
                            $q->where('ReserveStatus' , '<>', 'Reject');
                            $q->where('StartDateTime', "<=", DB::raw("'".$findDate . ' 23:59:59.000'."'"));
                            
                        }])
                    ->get();
        }

        public static function updateReserveCarInfo($obj){
        	
        	if($obj['ReserveCarID'] == ''){
        		$carReserve = new CarReserve;
        	}else{
        		$carReserve = CarReserve::find($obj['ReserveCarID']);
        	}

        	$carReserve = $carReserve->setValues($carReserve, $obj);
        	$carReserve->save();
        	return $carReserve->ReserveCarID;
        }

        public static function updateTraveller($obj){
            //print_r($obj);exit;
            if($obj['TravellerID'] == ''){
            	$traveller = new Traveller;
            }else{
                $traveller = Traveller::find($obj['TravellerID']);
            }
        	$traveller = $traveller->setValues($traveller, $obj);
        	$traveller->save();
        	return $traveller;
        }

        public static function deleteTraveller($travellerID){
            return Traveller::where('TravellerID' , $travellerID)->delete();
        }

        public static function cancelTraveller($reserveCarID){
            return Traveller::where('ReserveCarID' , $reserveCarID)->delete();
        }

        public static function cancelInternalDriver($reserveCarID){
            return InternalDriver::where('ReserveCarID' , $reserveCarID)->delete();
        }

        public static function cancelExternalDriver($reserveCarID){
            return ExternalDriver::where('ReserveCarID' , $reserveCarID)->delete();
        }

        public static function cancelReserveCarNotification($reserveCarID){
            return Notification::where('NotificationKeyID' , $reserveCarID)
                            ->whereIn('NotificationType', [9,10,11])
                            ->delete();
        }

        public static function cancelReserveCar($reserveCarID){
            return CarReserve::where('ReserveCarID' , $reserveCarID)->delete();
        }

        public static function markStatus($ReserveCarID, $ReserveStatus, $AdminComment){
            $carReserve = CarReserve::find($ReserveCarID);
            if($carReserve != null){
                $carReserve->ReserveStatus = $ReserveStatus;
                $carReserve->AdminComment = $AdminComment;
                $carReserve->MarkStatusDateTime = date('Y-m-d H:i:s.000');
                $carReserve->save();
                return $carReserve->ReserveCarID;
            }else{
                return null;
            }
        }

        public static function updateReserveCarAdminRecv($adminID, $ReserveCarID){
            $carReserve = CarReserve::where('ReserveCarID', $ReserveCarID)->whereNull('AdminID')->first();
            if(!is_null($carReserve)){
                $carReserve->AdminID = $adminID;
                $carReserve->save();
                return $carReserve->ReserveCarID;
            }else{
                return null;
            }
        }

        public static function checkDuplicateTime($reserveCarID, $carID, $startDateTime , $endDateTime){
            //echo "$startDateTime , $endDateTime";
            return CarReserve::where('ReserveCarID', '<>' , $reserveCarID)
                            ->where(function($query) use ($startDateTime,$endDateTime){
                                //$query->whereBetween('StartDateTime' , [$startDateTime , $endDateTime]);
                                //$query->orWhereBetween('EndDateTime' , [$startDateTime , $endDateTime]);
                                $query->where(function($subquery) use ($startDateTime,$endDateTime){
                                    $subquery->where('StartDateTime' ,'<=', $startDateTime);
                                    $subquery->where('EndDateTime' ,'>=', $endDateTime);
                                });
                                $query->orWhere(function($subquery) use ($startDateTime){
                                    $subquery->where('StartDateTime' ,'<=', $startDateTime);
                                    $subquery->where('EndDateTime' ,'>=', $startDateTime);
                                });
                                $query->orWhere(function($subquery) use ($endDateTime){
                                    $subquery->where('StartDateTime' ,'<=', $endDateTime);
                                    $subquery->where('EndDateTime' ,'>=', $endDateTime);
                                });
                                $query->orWhere(function($subquery) use ($startDateTime, $endDateTime){
                                    $subquery->where('StartDateTime' ,'>=', $startDateTime);
                                    $subquery->where('EndDateTime' ,'>=', $startDateTime);
                                    $subquery->where('StartDateTime' ,'<=', $endDateTime);
                                    $subquery->where('EndDateTime' ,'<=', $endDateTime);
                                });
                            })
                            ->where("ReserveStatus", "<>", "Reject")
                            ->where("CarID", $carID)
                            ->get();
        }

        public static function adminUpdateCarStatus($obj){
            $carReserve = CarReserve::where('ReserveCarID', $obj['ReserveCarID'])->first();
            if(!is_null($carReserve)){
                $carReserve->ReserveStatus = $obj['ReserveStatus'];
                $carReserve->DriverType = $obj['DriverType'];
                if(!empty($obj['CarID'])){
                    $carReserve->CarID = $obj['CarID'];
                }
                $carReserve->AdminComment = $obj['AdminComment'];
                $carReserve->MarkStatusDateTime = date('Y-m-d H:i:s.000');
                $carReserve->save();
                return $carReserve->ReserveCarID;
            }else{
                return null;
            }
        }

        public static function updateInternalDriver($obj, $ReserveCarID, $updateBy){
            if($obj['ReserveCarID'] == ''){
                $internalDriver = new InternalDriver;
                $obj['ReserveCarID'] = $ReserveCarID;  
                $obj['CreateBy'] = $updateBy;  
            }else{
                $internalDriver = InternalDriver::where('ReserveCarID', $ReserveCarID)->first();
            }
            $internalDriver->setValues($internalDriver, $obj);
            $internalDriver->save();
            return $internalDriver->ReserveCarID;
        }

        public static function updateExternalDriver($obj, $ReserveCarID, $updateBy){
            if($obj['ReserveCarID'] == ''){
                $externalDriver = new ExternalDriver;
                $obj['ReserveCarID'] = $ReserveCarID;  
                $obj['CreateBy'] = $updateBy;  

            }else{
                $externalDriver = ExternalDriver::where('ReserveCarID', $ReserveCarID)->first();
            }
            $obj['UpdateBy'] = $updateBy;  
            $externalDriver->setValues($externalDriver, $obj);
            $externalDriver->save();
            return $externalDriver->ReserveCarID;
        }

        public static function updateTotalTraveller($ReserveCarID, $TotalTraveller){
            
            $carReserve = CarReserve::find($ReserveCarID);
        
            $carReserve->TravelerAmount = ($carReserve->TravelerAmount + $TotalTraveller);
            $carReserve->save();
            return $carReserve->ReserveCarID;
        }
    }

?>