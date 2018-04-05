<?php
    
    namespace App\Service;
    
    use App\Model\User;
    use App\Model\Permission;
    use App\Model\FavouriteContact;

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
                                ,"ACCOUNT.RegionID"
                                ,"ACCOUNT.FirstName"
                                ,"ACCOUNT.LastName"
                                ,"ACCOUNT.Email"
                                ,"ACCOUNT.Mobile"
                                ,"ACCOUNT.Tel"
                                , "PERMISSION.UserID"
                                //, "PERMISSION.AdminGroupID"
                            )
                ->join("PERMISSION", 'PERMISSION.UserID', '=', 'ACCOUNT.UserID')
                 //->where('PERMISSION.AdminGroupID', $groupID)
                ->where(function($query) use ($groupID){
                    $query->where('PERMISSION.AdminGroupID', $groupID);
                    $query->orWhere('PERMISSION.AdminGroupID', DB::raw('0'));
                 })
                 ->where('ACCOUNT.RegionID',$regionID)
                 ->where('ACCOUNT.UserID' , '<>', $ownerID)
                 ->groupBy("ACCOUNT.UserID")
                 ->groupBy("ACCOUNT.RegionID")
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
            return $user->save();
            
        }
    }

?>