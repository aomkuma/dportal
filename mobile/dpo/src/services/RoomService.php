<?php

    namespace App\Service;
    
    use App\Model\Room;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class RoomService {
        
        public static function getAllRoomInRegion($regionID){
            return Room::where('RegionID', $regionID)->get();      
        }

        public static function getAllRoomActiveInRegion($regionID){
            return Room::where('RegionID', $regionID)
                    ->where("ActiveStatus" , 'Y')
                    ->get();      
        }              

        public static function getRoomList($offset){

        	$limit = 15;
			$skip = $offset * $limit;
			$total = Room::count();
			
			$DataList = Room::select('ROOM.*', 'REGION.RegionName  AS RegionName'
				
                                , DB::raw('a1.FirstName AS room_admin_firstname')
                                , DB::raw('a1.LastName AS room_admin_lastname')
                                , DB::raw('a1.Email AS room_admin_email')
                                , DB::raw('a1.Mobile AS room_admin_Mobile')
                                , DB::raw('a2.FirstName AS food_admin_firstname')
                                , DB::raw('a2.LastName AS food_admin_lastname')
                                , DB::raw('a2.Email AS food_admin_email')
                                , DB::raw('a2.Mobile AS food_admin_Mobile')
                                , DB::raw('a3.FirstName AS device_admin_firstname')
                                , DB::raw('a3.LastName AS device_admin_lastname')
                                , DB::raw('a3.Email AS device_admin_email')
                                , DB::raw('a3.Mobile AS device_admin_Mobile')
                                )
                    ->leftJoin('REGION', 'ROOM.RegionID', '=', 'REGION.RegionID')
                    ->leftJoin(DB::raw('TBL_ACCOUNT as a1'), 'ROOM.RoomAdminID', '=', DB::raw('a1.UserID'))
                    ->leftJoin(DB::raw('TBL_ACCOUNT as a2'), 'ROOM.FoodAdminID', '=', DB::raw('a2.UserID'))
                    ->leftJoin(DB::raw('TBL_ACCOUNT as a3'), 'ROOM.DeviceAdminID', '=', DB::raw('a3.UserID'))
					->skip($skip)
					->take($limit)
                    ->orderBy('RoomID', 'DESC')
					->get();

			$offset += 1;
			$continueLoad = true;
			if(ceil($total / $limit) == $offset){
				$continueLoad = false;
			}

			return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

        public static function updateData($obj){
        	if($obj['RoomID'] == ''){
                $room = new Room;
            }else{
                $room = Room::find($obj['RoomID']);
            }
            $room = $room->setValues($room , $obj);
            $room->save();
			return $room;
        }

        public static function deleteData($ID){
			return Room::find($ID)->delete();
        }  
        
    }    

?>