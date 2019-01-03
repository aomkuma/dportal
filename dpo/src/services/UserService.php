<?php
    
    namespace App\Service;
    
    use App\Model\User;
    use App\Model\Permission;
    use App\Model\FavouriteContact;
    use App\Model\Group;

    use Illuminate\Database\Capsule\Manager as DB;

    class UserService {
                        
        public static function getUser($id){
            return User::select("ACCOUNT.*"
                        //,"POSITION.PositionName" 
                        ,"REGION.RegionName" 
                    )
                    //->join("POSITION","POSITION.PositionID", "=", "ACCOUNT.PositionID")
                    ->join("REGION","REGION.RegionID", "=", "ACCOUNT.RegionID")->find($id);
        }

        public static function getUserByDepartment($departmentID){
            return User::where("OrgID", $departmentID)->get();
        }

        public static function getUserFullData($id){
            return User::select("ACCOUNT.*"
                        ,"ACCOUNT.PositionName" 
                        ,"REGION.RegionName" 
                        ,DB::raw("g1.OrgName AS OrgName")
                        ,DB::raw("g2.OrgName AS UpperOrgName")
                    )
                    //->join("POSITION","POSITION.PositionID", "=", "ACCOUNT.PositionID")
                    ->join("REGION","REGION.RegionID", "=", "ACCOUNT.RegionID")
                    ->leftJoin(DB::raw("TBL_GROUP g1"), DB::raw("g1.OrgID"), "=", "ACCOUNT.OrgID")
                    ->leftJoin(DB::raw("TBL_GROUP g2"), DB::raw("g1.UpperOrgID"), "=", DB::raw("g2.OrgID"))
                    ->find($id);   
        }

        public static function getUserByGroupAndRegionID($groupID, $regionID){
             return User::where('GroupID', $groupID)->where('RegionID',$regionID)->get();
        }

        public static function getUserByGroupAndRegionIDWithPermission($groupID, $regionID, $ownerID){
             return User::select("ACCOUNT.UserID"
                                ,"PERSON_REGION.RegionID"
                                ,"ACCOUNT.FirstName"
                                ,"ACCOUNT.LastName"
                                ,"ACCOUNT.Email"
                                ,"ACCOUNT.Mobile"
                                ,"ACCOUNT.Tel"
                                , "PERMISSION.UserID"
                                //, "PERMISSION.AdminGroupID"
                            )
                ->join("PERMISSION", 'PERMISSION.UserID', '=', 'ACCOUNT.UserID')
                ->join("PERSON_REGION", 'PERSON_REGION.UserID', '=', 'ACCOUNT.UserID')
                ->where(function($query) use ($groupID){
                    $query->where('PERMISSION.AdminGroupID', $groupID);
                    $query->orWhere('PERMISSION.AdminGroupID', DB::raw('0'));
                 })
                 ->where('PERSON_REGION.RegionID',$regionID)
                 ->where('ACCOUNT.UserID' , '<>', $ownerID)
                 ->groupBy("ACCOUNT.UserID")
                 ->groupBy("PERSON_REGION.RegionID")
                 ->groupBy("ACCOUNT.FirstName")
                 ->groupBy("ACCOUNT.LastName")
                 ->groupBy("ACCOUNT.Email")
                 ->groupBy("ACCOUNT.Mobile")
                 ->groupBy("ACCOUNT.Tel")
                 ->groupBy('PERMISSION.UserID')
                 ->get();
        }
        
        public static function addUser(User $user){
            $user->save();
            return $user->id;
        }

        public static function getPhoneBookList($Group, $Username, $offset, $RegionID, $DepartmentID, $LoginUserID){

            $ResultList = [];

            // Find favourite contact
            $favouriteList = FavouriteContact::select("FavouriteUserID")
                            ->where('UserID',$LoginUserID)
                            ->get()
                            ->toArray();
            if(!empty($favouriteList) && $offset == 0){
                // Get Favourite before
                $FavouriteUserIDList = [];
                foreach ($favouriteList as $key => $value) {
                    $FavouriteUserIDList[] = $value[FavouriteUserID];
                }

                $DataList = User::select(
                        "ACCOUNT.UserID"
                        ,"ACCOUNT.GroupID"
                        ,"ACCOUNT.RegionID"
                        ,"ACCOUNT.PositionID"
                        ,"ACCOUNT.Username"
                        ,"ACCOUNT.FirstName"
                        ,"ACCOUNT.LastName"
                        ,"ACCOUNT.Email"
                        ,"ACCOUNT.Mobile"
                        ,"ACCOUNT.Tel"
                        ,"ACCOUNT.Fax"
                        ,"ACCOUNT.Picture"
                        ,"ACCOUNT.InternalContact"
                        ,"ACCOUNT.PositionName"
                        ,"ACCOUNT.IsHeader"
                        ,"REGION.RegionName"
                        ,"ACCOUNT.Org AS orgName"
                        ,"FAVOURITE_CONTACT.FavouriteID"
                        ,DB::raw("'Y' AS Star")
                        )
                    ->join("REGION", "REGION.RegionID","=","ACCOUNT.RegionID")
                    ->join("FAVOURITE_CONTACT", "FAVOURITE_CONTACT.FavouriteUserID","=","ACCOUNT.UserID") 
                    ->whereIn("ACCOUNT.UserID", $FavouriteUserIDList)
                    ->where(function($query) use ($Group, $Username){
                        if(!empty($Group)){
                            $query->where("ACCOUNT.GroupID", $Group);
                        }
                        if(!empty($Username)){
                            $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $Username));
                            $FirstName = trim($UsernameArr[0]);
                            $LastName = trim($UsernameArr[1]);
                            if(count($UsernameArr) == 1){
                                $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                                $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            }else{
                                $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                                if(!empty($LastName)){
                                    $query->where('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$LastName."%'"));
                                }
                            }
                            
                        }
                    })
                    ->orderBy("FAVOURITE_CONTACT.FavouriteID", 'DESC') 
                    ->get();

                //Group data
                foreach ($DataList as $key => $value) {
                    # code...
                    $ResultList[] = $value;
                }
            }

            $limit = 15;
            $skip = $offset * $limit;
            $total = User::count();
            $DataList = User::select(
                        "ACCOUNT.UserID"
                        ,"ACCOUNT.GroupID"
                        ,"ACCOUNT.RegionID"
                        ,"ACCOUNT.PositionID"
                        ,"ACCOUNT.Username"
                        ,"ACCOUNT.FirstName"
                        ,"ACCOUNT.LastName"
                        ,"ACCOUNT.Email"
                        ,"ACCOUNT.Mobile"
                        ,"ACCOUNT.Tel"
                        ,"ACCOUNT.Fax"
                        ,"ACCOUNT.Picture"
                        ,"ACCOUNT.InternalContact"
                        ,"ACCOUNT.PositionName"
                        ,"ACCOUNT.IsHeader"
                        ,"REGION.RegionName"
                        ,"ACCOUNT.Org AS orgName")
                    ->join("REGION", "REGION.RegionID","=","ACCOUNT.RegionID") 
                    ->whereNotIn("ACCOUNT.UserID", $FavouriteUserIDList)
                    ->where(function($query) use ($Group, $Username){
                        if(!empty($Group)){
                            $query->where("ACCOUNT.GroupID", $Group);
                        }
                        if(!empty($Username)){
                            $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $Username));
                            $FirstName = trim($UsernameArr[0]);
                            $LastName = trim($UsernameArr[1]);
                            if(count($UsernameArr) == 1){
                                $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                                $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            }else{
                                $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                                if(!empty($LastName)){
                                    $query->where('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$LastName."%'"));
                                }
                            }
                        }
                    })
                    ->orderBy("ACCOUNT.RegionID") 
                    ->orderBy("ACCOUNT.FirstName") 
                    ->skip($skip)
                    ->take($limit)
                    ->get();

            $offset += 1;
            $continueLoad = true;
            if(ceil($total / $limit) == $offset){
                $continueLoad = false;
            }

            //Group data
            foreach ($DataList as $key => $value) {
                # code...
                $ResultList[] = $value;
            }
            return [ 'DataList'=>$ResultList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

        public static function getFavouriteContact($UserFavouriteID){
            return User::select(
                        "ACCOUNT.UserID"
                        ,"ACCOUNT.GroupID"
                        ,"ACCOUNT.RegionID"
                        ,"ACCOUNT.PositionID"
                        ,"ACCOUNT.Username"
                        ,"ACCOUNT.FirstName"
                        ,"ACCOUNT.LastName"
                        ,"ACCOUNT.Email"
                        ,"ACCOUNT.Mobile"
                        ,"ACCOUNT.Tel"
                        ,"ACCOUNT.Fax"
                        ,"ACCOUNT.Picture"
                        ,"ACCOUNT.InternalContact"
                        ,"ACCOUNT.PositionName"
                        ,"ACCOUNT.IsHeader"
                        ,"REGION.RegionName"
                        ,"ACCOUNT.Org AS orgName"
                        ,"FAVOURITE_CONTACT.FavouriteID"
                        ,DB::raw("'Y' AS Star")
                        )
                    ->join("REGION", "REGION.RegionID","=","ACCOUNT.RegionID")
                    ->join("FAVOURITE_CONTACT", "FAVOURITE_CONTACT.FavouriteUserID","=","ACCOUNT.UserID") 
                    ->where('ACCOUNT.UserID', $UserFavouriteID)
                    ->first();
        }

        public static function getContact($UserID){
            return User::select(
                        "ACCOUNT.UserID"
                        ,"ACCOUNT.GroupID"
                        ,"ACCOUNT.RegionID"
                        ,"ACCOUNT.PositionID"
                        ,"ACCOUNT.Username"
                        ,"ACCOUNT.FirstName"
                        ,"ACCOUNT.LastName"
                        ,"ACCOUNT.Email"
                        ,"ACCOUNT.Mobile"
                        ,"ACCOUNT.Tel"
                        ,"ACCOUNT.Fax"
                        ,"ACCOUNT.Picture"
                        ,"ACCOUNT.InternalContact"
                        ,"ACCOUNT.PositionName"
                        ,"ACCOUNT.IsHeader"
                        ,"ACCOUNT.PinID"
                        ,"REGION.RegionName"
                        ,"ACCOUNT.Org AS orgName")
                    ->join("REGION", "REGION.RegionID","=","ACCOUNT.RegionID") 
                    ->where('ACCOUNT.UserID', $UserID)
                    ->first();
        }

        public static function addFavouriteContact($UserID, $FavouriteUserID){
            $favourite = new FavouriteContact;
            $favourite->UserID = $UserID;
            $favourite->FavouriteUserID = $FavouriteUserID;
            $favourite->UpdateDateTime = date('Y-m-d H:i:s.000');
            return $favourite->save();
        }

        public static function removeFavouriteContact($FavouriteID){
            return FavouriteContact::find($FavouriteID)->delete();
        }
        
        public static function updatePhoneBookContact($obj){
            $user = User::find($obj['UserID']);
            
            $user->Mobile = $obj['Mobile'];
            $user->Tel = $obj['Tel'];
            $user->Fax = $obj['Fax'];
            $user->InternalContact = $obj['InternalContact'];
            $user->PinID = $obj['PinID'];
            return $user->save();
            
        }

        public static function getMISUserInfoList($Username){
            return User::select('ACCOUNT.UserID'
                        , 'ACCOUNT.Username'
                        , 'ACCOUNT.FirstName'
                        , 'ACCOUNT.LastName'
                        , 'ACCOUNT.LastName'
                        , 'ACCOUNT.Org'
                        , 'ACCOUNT.UpdateDateTime'
                        , DB::raw('a1.RegionID AS RegionID1')
                        , DB::raw('ra1.RegionName AS RegionName1')
                        , DB::raw('a2.RegionID AS RegionID2')
                        , DB::raw('ra2.RegionName AS RegionName2')
                        , DB::raw('a3.RegionID AS RegionID3')
                        , DB::raw('ra3.RegionName AS RegionName3')
                        , DB::raw('a4.RegionID AS RegionID4')
                        , DB::raw('ra4.RegionName AS RegionName4')
                        , DB::raw('a5.RegionID AS RegionID5')
                        , DB::raw('ra5.RegionName AS RegionName5')
                        , DB::raw('a6.RegionID AS RegionID6')
                        , DB::raw('ra6.RegionName AS RegionName6')
                        , DB::raw('a7.RegionID AS RegionID7')
                        , DB::raw('ra7.RegionName AS RegionName7')
                    )
                    ->leftJoin(DB::raw('TBL_PERSON_REGION as a1'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a1.UserID'));
                           $join->on(DB::raw('a1.RegionID'), '=', DB::raw('1'));
                         })
                    ->leftJoin(DB::raw('TBL_REGION as ra1'), DB::raw('a1.RegionID'), '=', DB::raw('ra1.RegionID'))
                    ->leftJoin(DB::raw('TBL_PERSON_REGION as a2'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a2.UserID'));
                           $join->on(DB::raw('a2.RegionID'), '=', DB::raw('2'));
                         })
                    ->leftJoin(DB::raw('TBL_REGION as ra2'), DB::raw('a2.RegionID'), '=', DB::raw('ra2.RegionID'))
                    ->leftJoin(DB::raw('TBL_PERSON_REGION as a3'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a3.UserID'));
                           $join->on(DB::raw('a3.RegionID'), '=', DB::raw('3'));
                         })
                    ->leftJoin(DB::raw('TBL_REGION as ra3'), DB::raw('a3.RegionID'), '=', DB::raw('ra3.RegionID'))
                    ->leftJoin(DB::raw('TBL_PERSON_REGION as a4'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a4.UserID'));
                           $join->on(DB::raw('a4.RegionID'), '=', DB::raw('4'));
                         })
                    ->leftJoin(DB::raw('TBL_REGION as ra4'), DB::raw('a4.RegionID'), '=', DB::raw('ra4.RegionID'))
                    ->leftJoin(DB::raw('TBL_PERSON_REGION as a5'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a5.UserID'));
                           $join->on(DB::raw('a5.RegionID'), '=', DB::raw('5'));
                         })
                    ->leftJoin(DB::raw('TBL_REGION as ra5'), DB::raw('a5.RegionID'), '=', DB::raw('ra5.RegionID'))
                    ->leftJoin(DB::raw('TBL_PERSON_REGION as a6'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a6.UserID'));
                           $join->on(DB::raw('a6.RegionID'), '=', DB::raw('6'));
                         })
                    ->leftJoin(DB::raw('TBL_REGION as ra6'), DB::raw('a6.RegionID'), '=', DB::raw('ra6.RegionID'))
                    ->leftJoin(DB::raw('TBL_PERSON_REGION as a7'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a7.UserID'));
                           $join->on(DB::raw('a7.RegionID'), '=', DB::raw('7'));
                         })
                    ->leftJoin(DB::raw('TBL_REGION as ra7'), DB::raw('a7.RegionID'), '=', DB::raw('ra7.RegionID'))

                    ->where(function($query) use ($Username){
                        if(!empty($Username)){
                            $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $Username));
                            $FirstName = trim($UsernameArr[0]);
                            $LastName = trim($UsernameArr[1]);
                            if(count($UsernameArr) == 1){
                                $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                                $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            }else{
                                $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                                if(!empty($LastName)){
                                    $query->where('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$LastName."%'"));
                                }
                            }
                        }
                    })
                ->orderBy('ACCOUNT.UpdateDateTime', 'DESC')
                ->get();
        }

        public static function getMISUserInfo($id){
            return User::select('ACCOUNT.UserID'
                        , 'ACCOUNT.Username'
                        , 'ACCOUNT.FirstName'
                        , 'ACCOUNT.LastName'
                        , 'ACCOUNT.LastName'
                        , 'ACCOUNT.StaffID'
                        , 'ACCOUNT.GroupID'
                    )
                    ->with(array('personRegion' => function($query){
                        $query->join('REGION', 'PERSON_REGION.RegionID', '=', 'REGION.RegionID');
                    }))
                    ->where('ACCOUNT.UserID' , $id)
                ->first();
        }

        public static function getGroup($groupID){
            return Group::find($groupID);   
        }

        public static function getGroupByOrgID($orgID){
            return Group::where('OrgID', $orgID)->first();   
        }
    }

?>