<?php

    namespace App\Service;
    
    use App\Model\RoomReserve;
    use App\Model\Room;
    use App\Model\Attendee;
    use App\Model\AttendeeExternal;
    use App\Model\RoomDestination;
    use App\Model\DeviceForRoom;
    use App\Model\FoodForRoom;
    use App\Model\Device;
    use App\Model\Food;
    use App\Model\Notification;
    
    
    use Illuminate\Database\Capsule\Manager as DB;
    
    class RoomReserveService {

        public  static function getRoomMonitor($RoomID, $CurDate){
            return RoomReserve::select("StartDateTime"
                                        , "EndDateTime"
                                        , "TopicConference"
                                    )
                                // ->join('ROOM', 'ROOM.RoomID', '=', 'RESERVE_ROOM.RoomID')
                                ->where('ReserveStatus', 'Approve')
                                // ->where('RegionID', $RegionID)
                                ->where('RoomID', $RoomID)
                                ->where(DB::raw("DATE_FORMAT(StartDateTime, '%Y-%m-%d')"), '<=', $CurDate)
                                ->where(DB::raw("DATE_FORMAT(EndDateTime, '%Y-%m-%d')"), '>=', $CurDate)
                                ->get();
        }
        
        public static function getReservListByRoomAndDay($roomID, $day){
            //return RoomReserve::join('ROOM','RESERVE_ROOM.RoomID','=','ROOM.RoomID')->where('RESERVE_ROOM.RoomID', $roomID)
            
            return RoomReserve::where('RoomID', $roomID)
                    ->where(function($query) use ($day){
                                // $query->where('StartDateTime' , '>' , DB::raw("'" . $day .' 00:00:00.000'."'"));
                                // $query->orWhere('EndDateTime' , '<=' , DB::raw("'" . $day .' 23:59:59.000'."'"));
                                $query->where('EndDateTime' , '>=' , DB::raw("'" . $day .' 00:00:00.000'."'"));
                                $query->where('ReserveStatus' , '<>' , 'Reject');
                            })
                    ->get();
        }
        
        public static function getRoomInfo($roomID){
            return Room::select('ROOM.*', 'REGION.RegionName  AS region_name'
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
                        ->where('RoomID',$roomID)
                        ->get();
        }     
        
        public static function getReserveRoomInfo($reserveRoomId, $toArray = false){
            if($toArray){
                return RoomReserve::where('ReserveRoomID',$reserveRoomId)->get()->toArray();
            }else{
                return RoomReserve::where('ReserveRoomID',$reserveRoomId)->get();    
            }
        }   
        
        public static function getInternalAttendee($reserveRoomId){
            return Attendee::where('ReserveRoomID', $reserveRoomId)
                    ->join('ACCOUNT', 'ATTENDEE.UserID', '=', 'ACCOUNT.UserID')
					->orderBy('ATTENDEE.CreateDateTime', 'ASC')
                    ->get();
        }
        
        public static function getExternalAttendee($reserveRoomId){
            return AttendeeExternal::where('ReserveRoomID', $reserveRoomId)->get();
        }
        
        public static function getFoodForRoom($reserveRoomId){
            return FoodForRoom::join('FOOD', 'FOOD.FoodID', '=', 'RESERVE_ROOM_FOOD.FoodID')->where('ReserveRoomID', $reserveRoomId)->get();
        }
        
        public static function getDeviceForRoom($reserveRoomId){
            return DeviceForRoom::join('DEVICE', 'DEVICE.DeviceID', '=', 'RESERVE_ROOM_DEVICE.DeviceID')->where('ReserveRoomID', $reserveRoomId)->get();
        }
        
        public static function getRoomDestinationList($roomId, $reserveRoomID){
            //return RoomDestination::where('ReserveRoomID', $reserveRoomId)->get();
            //echo $reserveRoomID;
            return Room::select('ROOM.*'
								,'DESTINATION_CONFERENCE_ROOM.RoomID AS selected_room'
                                ,'DESTINATION_CONFERENCE_ROOM.DestinationRoomID'
                                ,'DESTINATION_CONFERENCE_ROOM.ReserveStatus'
                                ,'DESTINATION_CONFERENCE_ROOM.ReserveRoomID'
                                ,'DESTINATION_CONFERENCE_ROOM.VerifyBy'
                                ,'DESTINATION_CONFERENCE_ROOM.AdminComment AS RoomDesAdminComment'
								,'REGION.RegionName'
								)
					->where('ROOM.RoomID', '<>' , $roomId)
					->where('ConferenceType', 'Y')
                    ->where('ActiveStatus', 'Y')
                    ->leftJoin('REGION', 'ROOM.RegionID', '=', 'REGION.RegionID')
					->leftJoin('DESTINATION_CONFERENCE_ROOM', function($join)  use ($reserveRoomID) 
                             {
                               $join->on('DESTINATION_CONFERENCE_ROOM.RoomID', '=', 'ROOM.RoomID');
                               $join->on('DESTINATION_CONFERENCE_ROOM.ReserveRoomID', '=', DB::raw("'".$reserveRoomID."'"));
                             })
                    ->get();
        }
        
        public static function checkDuplicateTime($reserveRoomID, $roomID, $startDateTime , $endDateTime){
            return RoomReserve::where('ReserveRoomID', '<>' , $reserveRoomID)
							// ->where(function($query) use ($startDateTime,$endDateTime){
							// 	$query->whereBetween('StartDateTime' , [$startDateTime , $endDateTime]);
							// 	$query->orWhereBetween('EndDateTime' , [$startDateTime , $endDateTime]);
							// })
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
                            ->where("RoomID", $roomID)
                            ->get();
        }

        public static function checkDuplicateTimeDestination($roomID, $startDateTime , $endDateTime){
            return RoomReserve::where(function($query) use ($startDateTime,$endDateTime){
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
                            ->where("RoomID", $roomID)
                            ->get();
        }
        
        public static function updateReserveRoomInfo($obj){
            if($obj['ReserveRoomID'] == ''){
                $roomReserve = new RoomReserve;
            }else{
                $roomReserve = RoomReserve::find($obj['ReserveRoomID']);
            }
            $roomReserve = $roomReserve->setValues($roomReserve , $obj);
            $roomReserve->save();
            return $roomReserve->ReserveRoomID;
        }

        public static function removeReserveRoom($ReserveRoomID){
            
            $roomReserve = RoomReserve::find($ReserveRoomID);
            return $roomReserve->delete();
        }
		
		public static function updateAttendee($obj){
			$attendee = new Attendee;
			$attendee = $attendee->setValues($attendee , $obj);
			$attendee->save();
            return $attendee->UserID;
        }
		
		public static function deleteAttendee($UserID, $ReserveRoomID){
			Attendee::where('UserID' , $UserID)->where('ReserveRoomID' , $ReserveRoomID)->delete();
            return true;
        }
		
		public static function updateExternalAttendee($obj){
			$attendee = new AttendeeExternal;
			$attendee = $attendee->setValues($attendee , $obj);
			
			$attendee->save();
            return $attendee->AttendeeID;
        }
        
		public static function deleteExternalAttendee($AttendeeID){
			return AttendeeExternal::where('AttendeeID' , $AttendeeID)->delete();
        }
		
		public static function getDeviceList($regionID, $offset){
			$limit = 5;
			$skip = $offset * $limit;
			$total = Device::where('RegionID' , $regionID)->where('ActiveStatus', 'Y')->count();
			$DeviceList = Device::where('RegionID' , $regionID)->where('ActiveStatus', 'Y')->skip($skip)->take($limit)->get();
			
			$offset += 1;
			$continueLoad = true;
			if(ceil($total / $limit) == $offset){
				$continueLoad = false;
			}
			return [ 'DeviceList'=>$DeviceList, 'offset'=>$offset, 'continueLoad'=>$continueLoad, 'totals'=>(ceil($total / $limit)) ];
        }
		
		public static function updateRoomDevice($obj, $reserveRoomID){
			$deviceForRoom = new DeviceForRoom;
			$obj['ReserveRoomID'] = $reserveRoomID;
			$deviceForRoom = $deviceForRoom->setValues($deviceForRoom , $obj);
			$deviceForRoom->save();
            return $deviceForRoom->ReserveRoomID;
        }
		
		public static function deleteRoomDevice($deviceID, $reserveRoomID){
			return DeviceForRoom::where('DeviceID' , $deviceID)->where('ReserveRoomID' , $reserveRoomID)->delete();
        }
		
		public static function getFoodList($regionID, $offset){
			$limit = 5;
			$skip = $offset * $limit;
			$total = Food::where('RegionID' , $regionID)->where('ActiveStatus', 'Y')->count();
			$FoodList = Food::where('RegionID' , $regionID)->where('ActiveStatus', 'Y')->skip($skip)->take($limit)->get();
			
			$offset += 1;
			$continueLoad = true;
			if(ceil($total / $limit) == $offset){
				$continueLoad = false;
			}
			return [ 'FoodList'=>$FoodList, 'offset'=>$offset, 'continueLoad'=>$continueLoad, 'totals'=>(ceil($total / $limit)) ];
        }
		
		public static function updateRoomFood($obj, $reserveRoomID){
			$foodForRoom = new FoodForRoom;
			$obj['ReserveRoomID'] = $reserveRoomID;
			$foodForRoom = $foodForRoom->setValues($foodForRoom , $obj);
			$foodForRoom->save();
            return $foodForRoom->ReserveRoomID;
        }
		
		public static function deleteRoomFood($foodID, $reserveRoomID){
			return FoodForRoom::where('FoodID' , $foodID)->where('ReserveRoomID' , $reserveRoomID)->delete();
        }
		
		public static function updateRoomDestinationStatus($Room, $reserveRoomID, $status){
			if($Room['DestinationRoomID']==''){
		      $roomDestination = new RoomDestination;
			}else{
			 $roomDestination = RoomDestination::find($Room['DestinationRoomID']);
			}
            $Room['ReserveStatus'] = trim($status)==''?'':'Waiting';
            $Room['ReserveRoomID'] = $reserveRoomID;
			
			$roomDestination = $roomDestination->setValues($roomDestination , $Room);
			$roomDestination->save();
			return ['DestinationRoomID'=>$roomDestination->DestinationRoomID, 'ReserveStatus'=>$roomDestination->ReserveStatus];
			
        }

        public static function updateReserveRoomAdminRecv($adminID, $ReserveRoomID){
            $roomReserve = RoomReserve::where('ReserveRoomID', $ReserveRoomID)->whereNull('AdminID')->first();
            if(!is_null($roomReserve)){
                //$roomReserve = $roomReserve[0];
                $roomReserve->AdminID = $adminID;
                $roomReserve->save();
                return $roomReserve->ReserveRoomID;
            }else{
                return null;
            }
        }

        public static function updateReserveDestinationRoomAdminRecv($adminID, $DestinationRoomID){
            $roomDestination = RoomDestination::where('DestinationRoomID', $DestinationRoomID)->whereNull('VerifyBy')->first();
            //print_r($roomDestination);die();
            if(!is_null($roomDestination)){
                //$roomDestination = $roomDestination[0];
                //echo "here";die();
                $roomDestination->VerifyBy = $adminID;
                $roomDestination->save();
                return $roomDestination->DestinationRoomID;
            }else{
                return null;
            }
        }

        public static function markStatus($ReserveRoomID, $ReserveStatus, $AdminComment){
            $roomReserve = RoomReserve::find($ReserveRoomID);
            if($roomReserve != null){
                $roomReserve->ReserveStatus = $ReserveStatus;
                $roomReserve->AdminComment = $AdminComment;
                $roomReserve->MarkStatusDateTime = date('Y-m-d H:i:s.000');
                $roomReserve->save();
                return $roomReserve;
            }else{
                return null;
            }
        }

        public static function markStatusRoomDestination($ReserveRoomID, $ReserveStatus, $AdminComment){
            $roomReserve = RoomDestination::find($ReserveRoomID);
            if($roomReserve != null){
                $roomReserve->ReserveStatus = $ReserveStatus;
                $roomReserve->AdminComment = $AdminComment;
                $roomReserve->VerifyDateTime = date('Y-m-d H:i:s.000');
                $roomReserve->save();
                return $roomReserve->DestinationRoomID;
            }else{
                return null;
            }
        }

        public static function cancelRoom($reserveRoomID){
            $roomReserve = RoomReserve::find($reserveRoomID);
            if($roomReserve != null){
                $roomReserve->ReserveStatus = 'Cancelled';
                $roomReserve->save();
                return $roomReserve->ReserveRoomID;
            }else{
                return null;
            }
        }

        public static function cancelRoomReserve($reserveRoomID){
            return RoomReserve::where('ReserveRoomID' , $reserveRoomID)->delete();
            
        }

        public static function cancelAttendeeReserve($reserveRoomID){
            return Attendee::where('ReserveRoomID' , $reserveRoomID)->delete();
            
        }

        public static function cancelAttendeeExternalReserve($reserveRoomID){
            return AttendeeExternal::where('ReserveRoomID' , $reserveRoomID)->delete();
            
        }

        public static function cancelFoodReserve($reserveRoomID){
            return FoodForRoom::where('ReserveRoomID' , $reserveRoomID)->delete();
            
        }

        public static function cancelDeviceReserve($reserveRoomID){
            return DeviceForRoom::where('ReserveRoomID' , $reserveRoomID)->delete();
            
        }

        public static function cancelRoomDestinationReserve($reserveRoomID){
            return RoomDestination::where('ReserveRoomID' , $reserveRoomID)->delete();
            
        }

        public static function cancelNotifications($reserveRoomID){
            return Notification::where('NotificationKeyID' , $reserveRoomID)->delete();
            
        }

        public static function getReserveDesctinationRoomInfo($TopicConference, $StartDateTime, $EndDateTime, $originReserveRoomID){
            
            return RoomReserve::where('TopicConference', $TopicConference)
                    ->where('StartDateTime', $StartDateTime)
                    ->where('EndDateTime', $EndDateTime)
                    ->where('ReserveRoomID', '<>', $originReserveRoomID)
                    ->get()
                    ->toArray();
            
        }

    }    



?>