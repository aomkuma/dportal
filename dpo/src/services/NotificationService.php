<?php

    namespace App\Service;
    
    use App\Model\Notification;
    use App\Model\NotificationType;
    use App\Model\RoomReserve;
    use App\Model\CarReserve;
    use App\Model\News;
    use App\Model\Repair;
    
    use Illuminate\Database\Capsule\Manager as DB;
    
    class NotificationService {
        public static function pushNotiFication($obj){
            $notification = new Notification;
            $notification = $notification->setValues($notification , $obj);
            return $notification->save();
            
        }

        public static function getNotificationType($notificationTypeID){
            return NotificationType::find($notificationTypeID);
        }        

        public static function getNotificationList($regionID , $adminGroup, $userID, $offset){
            $limit = 10;
            $skip = $offset * $limit;
            
            $NotificationList = Notification::
                                //where('RegionID',$regionID)
                                //->where('AdminGroup', $adminGroup)
                                //->
                                where('ToSpecificPersonID', $userID)
                                ->groupBy('ToSpecificPersonID')
                                ->groupBy('NotificationType')
                                ->groupBy('PushDateTime')
                                ->orderBy('PushDateTime', 'DESC')
                                ->skip($skip)
                                ->take($limit)
                                ->get();

            $offset += 1;
            $continueLoad = true;
            if(ceil($total / $limit) == $offset){
                $continueLoad = false;
            }

            return ['NotificationList'=>$NotificationList, 'offset'=>$offset, 'continueLoad'=>$continueLoad];
        }

        public static function getNotificationListByCondition($notificationType, $regionID, $keyword , $adminGroup, $userID, $offset){
            // echo "$notificationType, $regionID , $adminGroup, $userID, $offset";
            $limit = 15;
            $skip = $offset * $limit;
            
            $total = count(Notification::where('ToSpecificPersonID', $userID)
                                ->where(function($query) use ($notificationType, $regionID, $keyword){
                                        if($regionID != '' && $regionID != 'null'){
                                            // $query->where('RegionID',$regionID);
                                        }
                                        if($notificationType != 'ALL'){
                                            $query->whereIn('NotificationType', explode(":", $notificationType));
                                        }
                                        if($keyword != '-'){
                                            $query->where('NotificationText', 'LIKE' , DB::raw("N'%" . $keyword . "%'"));   
                                        }
                                    })
                                ->groupBy('ToSpecificPersonID')
                                ->groupBy('PushDateTime')
                                ->get()->toArray());

            $NotificationList = Notification::
                                //where('RegionID',$regionID)->
                                //where('AdminGroup', $adminGroup)
                                where('ToSpecificPersonID', $userID)
                                ->where(function($query) use ($notificationType, $regionID, $keyword){
                                        if($regionID != '' && $regionID != 'null'){
                                            // $query->where('RegionID',$regionID);
                                        }
                                        if($notificationType != 'ALL'){
                                            $query->whereIn('NotificationType', explode(":", $notificationType));
                                        }
                                        if($keyword != '-'){
                                            $query->where('NotificationText', 'LIKE' , DB::raw("N'%" . $keyword . "%'"));   
                                        }
                                    })
                                ->groupBy('ToSpecificPersonID')
                                ->groupBy('PushDateTime')
                                ->orderBy('PushDateTime', 'DESC')
                                //->groupBy('ToSpecificPersonID')
                                ->skip($skip)
                                ->take($limit)
                                ->get();
            if($total > 0)
            {
                $offset += 1;    
            }
            
            $continueLoad = true;
            if(ceil($total / $limit) == $offset){
                $continueLoad = false;
            }

            return ['NotificationList'=>$NotificationList, 'offset'=>$offset, 'continueLoad'=>$continueLoad, 'total'=>$total, 'skip'=>$skip, 'limit'=>$limit];
        }

        public static function countNotificationUnseen($regionID , $adminGroup ,$userID){
            return count(Notification::
                            //where('RegionID',$regionID)
                            //->where('AdminGroup', $adminGroup)
                            //->
                            where('ToSpecificPersonID' ,$userID)
                            ->where('NotificationStatus', 'Unseen')
                            ->groupBy('ToSpecificPersonID')
                            ->groupBy('PushDateTime')
                            ->get()->toArray());
        }

        public static function countNotificationByConditionUnseen($notificationType, $regionID , $adminGroup ,$userID){
            return count(Notification::
                                //where('RegionID',$regionID)
                                //->
                                where('AdminGroup', $adminGroup)
                                ->where('ToSpecificPersonID' ,$userID)
                                ->where(function($query) use ($notificationType){
                                        if($notificationType != 'ALL'){
                                            $query->whereIn('NotificationType', explode(":", $notificationType));
                                        }else{
                                            $query->where(DB::raw('1'),'=',DB::raw('1'));
                                        }
                                    })
                                ->where('NotificationStatus', 'Unseen')
                                ->groupBy('ToSpecificPersonID')
                                ->groupBy('PushDateTime')
                                ->get()->toArray());
        }

        public static function updateNotificationStatus($obj, $pullBy){
            $update['PullBy'] = $pullBy;
            $update['NotificationStatus'] = 'Seen';
            // $notification = Notification::where('NotificationText', $obj['NotificationText'])
            // ->where('NotificationKeyID', $obj['NotificationKeyID'])
            // ->whereIn('NotificationType', ['1','6','9','12','15'])
            // ->update($update);
            $notification = Notification::find($obj['NotificationID']);
            $obj['PullBy'] = $pullBy;
            $obj['NotificationStatus'] = 'Seen';
            $notification = $notification->setValues($notification , $obj);
            $notification->save();
            return $notification->NotificationID;
        }

        public static function updateNotificationSeenData($NotificationKeyID, $NotificationTypeList, $NotificationText = ''){
            $update['NotificationStatus'] = 'Seen';

            if(!empty($NotificationText)){
                $update['NotificationText'] = $NotificationText;
            }
            $notification = Notification::where('NotificationKeyID', $NotificationKeyID)
                            ->whereIn('NotificationType', $NotificationTypeList)
                            ->update($update)
                            ;
            // $obj['NotificationStatus'] = 'Seen';
            // $notification = $notification->setValues($notification , $obj);
            // $notification->save();
        }

        public static function getNotificationRoomData($NotificationKeyID, $NotificationTypeList){
            
            return Notification::where('NotificationKeyID', $NotificationKeyID)
                            ->whereIn('NotificationType', $NotificationTypeList)
                            ->first();
            // $obj['NotificationStatus'] = 'Seen';
            // $notification = $notification->setValues($notification , $obj);
            // $notification->save();
        }

         public static function checkAdminPermission($UserID, $ReserveRoomID, $NotificationType){
            return Notification::where('ToSpecificPersonID', $UserID)
                            ->where('NotificationKeyID', $ReserveRoomID)
                            ->where('NotificationType', $NotificationType)
                            ->first();
        }



        public static function getRoom($keyID){
            return RoomReserve::find($keyID);
        }

        public static function getCar($keyID){
            return CarReserve::find($keyID);
        }

        public static function getNews($keyID){
            return News::find($keyID);
        }

        public static function getRepair($keyID){
            return Repair::find($keyID);
        }

    }

?>