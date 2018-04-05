<?php
	namespace App\Service;

	use App\Model\Repair;
	use App\Model\RepairSubIssue;
    use App\Model\Room;
    use App\Model\RoomReserve;
    use App\Model\Car;
    use App\Model\CarReserve;
    use App\Model\User;
    use App\Model\FoodForRoom;
    use App\Model\DeviceForRoom;

	use Illuminate\Database\Capsule\Manager as DB;

	class ReportService {

        public static function loadRoomListByRegion($regionID){
            return Room::where('RegionID', $regionID)
                    ->get();
        }

        public static function loadCarListByRegion($regionID){
            return Car::select("CarID", "License")->where('RegionID', $regionID)
                    ->get();
        }

        public static function loadUserListByRegion($regionID){
            return User::select("UserID", "FirstName", "LastName")->where('RegionID', $regionID)
                    ->get();
        }

        public static function loadUserListByRegionAndRole($regionID){
            return User::select("ACCOUNT.UserID", "ACCOUNT.FirstName", "ACCOUNT.LastName")
                    ->join(DB::raw('TBL_PERMISSION as a1'), function($join)
                         {
                           $join->on(DB::raw('a1.UserID'), '=', 'ACCOUNT.UserID');
                           $join->on(DB::raw('a1.AdminGroupID'), '=', DB::raw("'7'"));
                         })
                    // ->where(function($query){
                    //     $query->where('AdminGroupID' , 7);
                    //     $query->orWhere('AdminGroupID', );
                    // })
                    ->where('ACCOUNT.RegionID', $regionID)
                    ->get();
        }

		public static function getRepairSubIssueList($startDate, $endDate, $RepairType, $RepairedTitle, $RepairIssue, $RepairSubIssue){
            $DataList = RepairSubIssue::select("REPAIRED_SUB_ISSUE.RepairedSubIssueID"
                                                ,"REPAIRED_TYPE.RepairedTypeName"
                                                ,"REPAIRED_TITLE.RepairedTitleName"
                                                ,"REPAIRED_ISSUE.RepairedIssueName"
                                                ,"REPAIRED_SUB_ISSUE.RepairedSubIssueName")
            		->join("REPAIRED" , "REPAIRED.RepairedSubIssueID" , "=", "REPAIRED_SUB_ISSUE.RepairedSubIssueID")
                    ->join("REPAIRED_TYPE" , "REPAIRED.RepairedTypeID" , "=", "REPAIRED_TYPE.RepairedTypeID")
                    ->join("REPAIRED_TITLE" , "REPAIRED.RepairedTitleID" , "=", "REPAIRED_TITLE.RepairedTitleID")
                    ->join("REPAIRED_ISSUE" , "REPAIRED.RepairedIssueID" , "=", "REPAIRED_ISSUE.RepairedIssueID")
            		->whereBetween('REPAIRED.CreateDateTime', [$startDate, $endDate])
            		->where("REPAIRED.RepairedStatus" , '<>', 'Request')
                    ->where(function($query) use ($RepairType, $RepairedTitle, $RepairIssue, $RepairSubIssue){
                        if(!empty($RepairType)){
                            $query->where("REPAIRED.RepairedTypeID", $RepairType);
                        }
                        if(!empty($RepairedTitle)){
                            $query->where("REPAIRED.RepairedTitleID", $RepairedTitle);
                        }
                        if(!empty($RepairIssue)){
                            $query->where("REPAIRED.RepairedIssueID", $RepairIssue);
                        }
                        if(!empty($RepairSubIssue)){
                            $query->where("REPAIRED.RepairedSubIssueID", $RepairSubIssue);
                        }
                    })
            		->groupBy("REPAIRED_SUB_ISSUE.RepairedSubIssueID", "REPAIRED_SUB_ISSUE.RepairedSubIssueName","REPAIRED_TYPE.RepairedTypeName" ,"REPAIRED_TITLE.RepairedTitleName","REPAIRED_ISSUE.RepairedIssueName")
                    ->orderBy("REPAIRED_SUB_ISSUE.RepairedSubIssueName")
                    ->get();

            return $DataList;
        }

        public static function countTotalRepairNotify($RepairedSubIssueID, $startDate, $endDate){
        	return Repair::where('RepairedSubIssueID', $RepairedSubIssueID)
        			->where("REPAIRED.RepairedStatus" , '<>', 'Request')
        			->whereBetween('REPAIRED.CreateDateTime', [$startDate, $endDate])
        			->count();
        }

        public static function countRepairFinish($RepairedSubIssueID, $startDate, $endDate){
        	return Repair::where('RepairedSubIssueID', $RepairedSubIssueID)
        			->where("REPAIRED.RepairedStatus" , '=', 'Finish')
        			->whereBetween('REPAIRED.CreateDateTime', [$startDate, $endDate])
        			->count();
        }

        public static function countRepairHold($RepairedSubIssueID, $startDate, $endDate){
        	return Repair::where('RepairedSubIssueID', $RepairedSubIssueID)
        			->where("REPAIRED.RepairedStatus" , '=', 'Suspend')
        			->whereBetween('REPAIRED.CreateDateTime', [$startDate, $endDate])
        			->count();
        }

        public static function countRepairCancel($RepairedSubIssueID, $startDate, $endDate){
        	return Repair::where('RepairedSubIssueID', $RepairedSubIssueID)
        			->where("REPAIRED.RepairedStatus" , '=', 'Cancel')
        			->whereBetween('REPAIRED.CreateDateTime', [$startDate, $endDate])
        			->count();
        }

        public static function countSLAPass($RepairedSubIssueID, $startDate, $endDate){
            return Repair::where('RepairedSubIssueID', $RepairedSubIssueID)
                    ->where("REPAIRED.SLAStatus" , '=', '1')
                    ->whereBetween('REPAIRED.CreateDateTime', [$startDate, $endDate])
                    ->count();
        }

        public static function countSLAFailed($RepairedSubIssueID, $startDate, $endDate){
            return Repair::where('RepairedSubIssueID', $RepairedSubIssueID)
                    ->where("REPAIRED.SLAStatus" , '=', '0')
                    ->whereBetween('REPAIRED.CreateDateTime', [$startDate, $endDate])
                    ->count();
        }

        public static function getDetailRoomUsing($roomID, $startDate, $endDate){
            return Room::select("RESERVE_ROOM.ReserveRoomID"
                                ,"ROOM.RoomName"
                                ,"REGION.RegionName"
                                ,"ACCOUNT.FirstName"
                                ,"ACCOUNT.LastName"
                                ,"RESERVE_ROOM.StartDateTime"
                                ,"RESERVE_ROOM.EndDateTime"
                                ,"RESERVE_ROOM.TopicConference" )
                        ->join("REGION", "REGION.RegionID", "=", "ROOM.RegionID")
                        ->join("RESERVE_ROOM", "RESERVE_ROOM.RoomID", "=", "ROOM.RoomID")
                        ->join("ACCOUNT", "ACCOUNT.UserID", "=", "RESERVE_ROOM.CreateBy")
                        ->where("ROOM.RoomID", $roomID)
                        ->where(function($query) use ($startDate,$endDate){
                                $query->whereBetween('RESERVE_ROOM.StartDateTime' , [$startDate , $endDate]);
                                $query->orWhereBetween('RESERVE_ROOM.EndDateTime' , [$startDate , $endDate]);
                            })
                        ->orderBy('RESERVE_ROOM.StartDateTime')
                        ->get();
        }

        public static function getDetailFood($ReserveRoomID){
            return FoodForRoom::select("FOOD.FoodName", "RESERVE_ROOM_FOOD.Amount")
                        ->join("FOOD", "FOOD.FoodID" , '=', "RESERVE_ROOM_FOOD.FoodID")
                        ->where("ReserveRoomID" , $ReserveRoomID)
                        ->get();
        }

        public static function getDetailDevice($ReserveRoomID){
            return DeviceForRoom::select("DEVICE.DeviceName", "RESERVE_ROOM_DEVICE.Amount")
                        ->join("DEVICE", "DEVICE.DeviceID" , '=', "RESERVE_ROOM_DEVICE.DeviceID")
                        ->where("ReserveRoomID" , $ReserveRoomID)
                        ->get();
        }

        public static function getSummaryRoom($regionID, $startDate, $endDate, $year){
            return Room::select("ROOM.RoomID"
                                ,"ROOM.RoomName"
                                ,"REGION.RegionName"
                                )
                    ->join("REGION", "REGION.RegionID", "=", "ROOM.RegionID")
                    ->where("ROOM.RegionID" , DB::raw("'" . $regionID. "'"))
                    ->orderBy("ROOM.RoomName", 'ASC')
                    ->get();
        }

        public static function countSummaryRoom($roomID, $startDate, $endDate){
            return RoomReserve::where(function($query) use ($startDate,$endDate){
                                $query->whereBetween('StartDateTime' , [$startDate , $endDate]);
                                $query->orWhereBetween('EndDateTime' , [$startDate , $endDate]);
                            })
                    ->where("ReserveStatus" , 'Approve')
                    ->where("RoomID" , $roomID)
                    ->count();
        }

        public static function getDetailCarUsing($carID, $startDate, $endDate){
            return Car::select("ACCOUNT.FirstName"
                                ,"ACCOUNT.LastName"
                                ,"CAR.License"
                                ,"REGION.RegionName"
                                ,"RESERVE_CAR.StartDateTime"
                                ,"RESERVE_CAR.EndDateTime"
                                ,"RESERVE_CAR.DriverType"
                                ,DB::raw('ACCOUNT_DRIVER.FirstName AS DriverFirstName')
                                ,DB::raw('ACCOUNT_DRIVER.LastName AS DriverLastName')
                                ,"EXTERNAL_DRIVER.DriverName"
                                ,"RESERVE_CAR.Destination"
                                ,"RESERVE_CAR.Remark")
                        ->join("REGION", "REGION.RegionID", "=", "CAR.RegionID")
                        ->join("RESERVE_CAR", "RESERVE_CAR.CarID", "=", "CAR.CarID")
                        ->join("ACCOUNT", "ACCOUNT.UserID", "=", "RESERVE_CAR.CreateBy")
                        ->leftJoin("INTERNAL_DRIVER", "INTERNAL_DRIVER.ReserveCarID", "=", "RESERVE_CAR.ReserveCarID")
                        ->leftJoin(DB::raw('TBL_ACCOUNT as ACCOUNT_DRIVER'), DB::raw('ACCOUNT_DRIVER.UserID'), "=", "INTERNAL_DRIVER.UserID")
                        ->leftJoin("EXTERNAL_DRIVER", "EXTERNAL_DRIVER.ReserveCarID", "=", "RESERVE_CAR.ReserveCarID")
                        ->where("CAR.CarID", $carID)
                        ->where("RESERVE_CAR.ReserveStatus", 'Approve')
                        ->where(function($query) use ($startDate,$endDate){
                                $query->whereBetween('RESERVE_CAR.StartDateTime' , [$startDate , $endDate]);
                                $query->orWhereBetween('RESERVE_CAR.EndDateTime' , [$startDate , $endDate]);
                            })
                        ->orderBy('RESERVE_CAR.StartDateTime')
                        ->get();
        }

        public static function getSummaryCar($regionID, $startDate, $endDate){
            return Car::select("CAR.CarID"
                                ,"CAR.Brand" 
                                ,"CAR.Model"
                                ,"CAR.License"
                                ,"CAR_TYPE.CarType"
                                ,"REGION.RegionName"
                                )
                    ->join("CAR_TYPE", "CAR_TYPE.CarTypeID", "=", "CAR.CarTypeID")
                    ->join("REGION", "REGION.RegionID", "=", "CAR.RegionID")
                    ->where("CAR.RegionID" , DB::raw("'" . $regionID. "'"))
                    ->orderBy("CAR.Brand", 'ASC')
                    ->get();
        }

        public static function countSummaryCar($carID, $startDate, $endDate){
            return CarReserve::where(function($query) use ($startDate,$endDate){
                                $query->whereBetween('StartDateTime' , [$startDate , $endDate]);
                                $query->orWhereBetween('EndDateTime' , [$startDate , $endDate]);
                            })
                    ->where("CarID" , $carID)
                    ->where("ReserveStatus", 'Approve')
                    ->count();
        }

        public static function getDetailRepair($RepairedTypeID, $RegionID, $UserID, $startDate, $endDate){

            return Repair::select("REPAIRED.RepairedID"
                                , "REPAIRED.RepairedCode"
                                , "REPAIRED_SUB_ISSUE.RepairedSubIssueName"
                                , "GROUP.GroupName"
                                , "REPAIRED.CreateDateTime"
                                , "ACCOUNT_RECIEVER.FirstName AS RecieverFirstName"
                                , "ACCOUNT_RECIEVER.LastName AS RecieverLastName"
                                , "REPAIRED.RepairedStatus"
                                , "REPAIRED_SUB_ISSUE.SLA"
                                , "REPAIRED.SLAStatus"
                                , "REPAIRED.ReceiveDateTime"
                                , "ACCOUNT_CREATOR.FirstName AS CreatorFirstName"
                                , "ACCOUNT_CREATOR.LastName AS CreatorLastName"
                                , "REPAIRED.RequestorIP"
                                , "REPAIRED.RepairedDetail"
                            )
                    ->join("REPAIRED_TITLE", "REPAIRED.RepairedTitleID", "=", "REPAIRED_TITLE.RepairedTitleID")
                    ->join("REPAIRED_SUB_ISSUE", "REPAIRED.RepairedSubIssueID", "=", "REPAIRED_SUB_ISSUE.RepairedSubIssueID")
                    ->leftJoin("GROUP", "REPAIRED_TITLE.DepartmentID", "=", "GROUP.OrgID") 
                    ->leftJoin("ACCOUNT AS ACCOUNT_RECIEVER", "REPAIRED.AdminID", "=", "ACCOUNT_RECIEVER.UserID")
                    ->leftJoin("ACCOUNT AS ACCOUNT_CREATOR", "REPAIRED.CreateBy", "=", "ACCOUNT_CREATOR.UserID")
                    ->where(function($query) use ($RepairedTypeID, $RegionID, $UserID, $startDate,$endDate){
                                if(!empty($RepairedTypeID)){
                                    $query->where("REPAIRED.RepairedTypeID", $RepairedTypeID);
                                }
                                if(!empty($RegionID)){
                                    $query->where("REPAIRED.RegionID", $RegionID);
                                }
                                if(!empty($UserID)){
                                    $query->where("ACCOUNT_RECIEVER.UserID", $UserID);
                                }
                                $query->whereBetween('REPAIRED.CreateDateTime' , [$startDate , $endDate]);
                            })
                    ->get();
        }

	}