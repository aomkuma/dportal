<?php
    
    namespace App\Service;
    
    use App\Model\ExternalPhoneBook;
    use App\Model\FavouriteExContact;
    use App\Model\ExPhoneBookPermission;
    use App\Model\Group;

    use Illuminate\Database\Capsule\Manager as DB;

    class ExternalPhoneBookService {

        public static function getManagePhoneBookList($offset, $keyword, $activeStatus){
            $limit = 15;
            $skip = $offset * $limit;
            $total = ExternalPhoneBook::count();

            $ResultList = ExternalPhoneBook::where("CompanyName" , 'LIKE' , '%' . $keyword . '%')
                        ->where(function($query) use ($activeStatus){
                            if(!empty($activeStatus)){
                                $query->where('ActiveStatus', $activeStatus);    
                            }
                         })
                        ->skip($skip)
                        ->take($limit)
                        ->get();

            $offset += 1;
            $continueLoad = true;
            if(ceil($total / $limit) == $offset){
                $continueLoad = false;
            }

            return [ 'DataList'=>$ResultList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];
        }

        public static function getPhoneBookDetail($PhoneBookID){
            return ExternalPhoneBook::find($PhoneBookID);
        }

        public static function loadGroupPermission($RegionID, $PhoneBookID, $GroupType, $UpperOrgID){
            return Group::select("GROUP.GroupID"
                                , "GROUP.OrgID"
                                , "GROUP.OrgName"
                                , "EXPHONEBOOK_PERMISSION.ExPhoneBookID"
                                , "EXPHONEBOOK_PERMISSION.PermissionType"
                                , "EXPHONEBOOK_PERMISSION.AllowStatus"
                                , "REGION.RegionName"
                            )
                        ->join("REGION", "GROUP.RegionID", "=", "REGION.RegionID")
                        // ->leftJoin("EXPHONEBOOK_PERMISSION", "GROUP.GroupID", "=", "EXPHONEBOOK_PERMISSION.GroupID")
                        ->leftJoin("EXPHONEBOOK_PERMISSION", function($join) use($PhoneBookID)
                         {
                           $join->on("GROUP.OrgID", '=', "EXPHONEBOOK_PERMISSION.GroupID");
                           $join->on("EXPHONEBOOK_PERMISSION.ExPhoneBookID", '=', DB::raw("'".$PhoneBookID."'"));
                         })
                        //->where("EXPHONEBOOK_PERMISSION.ExPhoneBookID", $PhoneBookID)
                        ->where("GROUP.GroupType", $GroupType)
                        ->where(function($query) use ($GroupType, $UpperOrgID, $RegionID){
                            if($GroupType != 'OFFICE'){
                                $query->where('UpperOrgID', $UpperOrgID);    
                            }
                            if($RegionID != 'ALL'){
                                $query->where("GROUP.RegionID", $RegionID);
                            }
                        })
                        ->get();
        }

        public static function getAllGroup($RegionID){
            return Group::where(function($query) use ($RegionID){
                            if($RegionID != 'ALL'){
                                $query->where("RegionID", $RegionID);
                            }
                        })
                        ->get();
        }

        public static function getChildOrg($UpperOrgID){
            return Group::where("UpperOrgID", $UpperOrgID)
                        ->get();
        }        

        public static function updatePermissionAllGroup($GroupID, $GroupType, $AllowStatus, $obj){
            $permission = ExPhoneBookPermission::where("PermissionType", $GroupType)
                            ->where("GroupID", $GroupID)
                            ->where("ExPhoneBookID", $obj['PhoneBookID'])
                            ->first();
            if(empty($permission)){
                $permission = new ExPhoneBookPermission;
                $permission = $permission->setValues($permission, $obj);
            }
            $permission->GroupID = $GroupID;
            $permission->PermissionType = $GroupType;
            $permission->AllowStatus = $AllowStatus;
            $permission->save();
        }

        public static function updatePhoneBookContact($obj){
            if(empty($obj['PhoneBookID'])){
                $user = new ExternalPhoneBook;
            }else{
                $user = ExternalPhoneBook::find($obj['PhoneBookID']);    
            }
            $user = $user->setValues($user, $obj);

            return $user->save();
            
        }

        public static function getExternalPhoneBookList($offset, $keyword, $LoginUserID){

            $ResultList = [];

            // Find favourite contact
            $favouriteList = FavouriteExContact::select("FavouriteExID")
                            ->where('UserID',$LoginUserID)
                            ->get()
                            ->toArray();
            if(!empty($favouriteList) && $offset == 0){
                // Get Favourite before
                $FavouriteUserIDList = [];
                foreach ($favouriteList as $key => $value) {
                    $FavouriteUserIDList[] = $value['FavouriteExID'];
                }

                $DataList = ExternalPhoneBook::select(
                        'PHONEBOOK.*'
                        ,'FAVOURITE_EX_CONTACT.FavouriteID'
                        ,DB::raw("'Y' AS Star")
                        )
                    ->join("FAVOURITE_EX_CONTACT", "FAVOURITE_EX_CONTACT.FavouriteExID","=","PHONEBOOK.PhoneBookID") 
                    ->join("EXPHONEBOOK_PERMISSION", "EXPHONEBOOK_PERMISSION.ExPhoneBookID","=", "PHONEBOOK.PhoneBookID")
                    ->join("ACCOUNT", "ACCOUNT.OrgID", "=", "EXPHONEBOOK_PERMISSION.GroupID")
                    ->whereIn("FAVOURITE_EX_CONTACT.FavouriteExID", $FavouriteUserIDList)
                    ->where("PHONEBOOK.CompanyName" , 'LIKE' , '%' . $keyword . '%')
                    ->where("PHONEBOOK.ActiveStatus" ,  'Y')
                    ->where("ACCOUNT.UserID", $LoginUserID)
                    ->where("EXPHONEBOOK_PERMISSION.AllowStatus", 1)
                    ->orderBy("FAVOURITE_EX_CONTACT.FavouriteID", 'DESC') 
                    ->get();

                //Group data
                foreach ($DataList as $key => $value) {
                    # code...
                    $ResultList[] = $value;
                }
            }

            $limit = 15;
            $skip = $offset * $limit;
            $total = ExternalPhoneBook::count();
            $DataList = ExternalPhoneBook::select("PHONEBOOK.*")
                    ->join("EXPHONEBOOK_PERMISSION", "EXPHONEBOOK_PERMISSION.ExPhoneBookID","=", "PHONEBOOK.PhoneBookID")
                    ->join("ACCOUNT", "ACCOUNT.OrgID", "=", "EXPHONEBOOK_PERMISSION.GroupID")
                    ->whereNotIn("PHONEBOOK.PhoneBookID", $FavouriteUserIDList)
                    ->where("PHONEBOOK.CompanyName" , 'LIKE' , '%' . $keyword . '%')
                    ->where("PHONEBOOK.ActiveStatus" ,  'Y')
                    ->where("ACCOUNT.UserID", $LoginUserID)
                    ->where("EXPHONEBOOK_PERMISSION.AllowStatus", 1)
                    ->orderBy("PHONEBOOK.FirstName") 
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
            return ExternalPhoneBook::select(
                        'PHONEBOOK.*'
                        ,'FAVOURITE_EX_CONTACT.FavouriteID'
                        ,DB::raw("'Y' AS Star")
                        )
                    ->join("FAVOURITE_EX_CONTACT", "FAVOURITE_EX_CONTACT.FavouriteExID","=","PHONEBOOK.PhoneBookID") 
                    ->where('PHONEBOOK.PhoneBookID', $UserFavouriteID)
                    ->first();
        }

        public static function getContact($UserID){
            return ExternalPhoneBook::where('PhoneBookID', $UserID)
                    ->first();
        }

        public static function addFavouriteContact($UserID, $FavouriteUserID){
            $favourite = new FavouriteExContact;
            $favourite->UserID = $UserID;
            $favourite->FavouriteExID = $FavouriteUserID;
            $favourite->UpdateDateTime = date('Y-m-d H:i:s.000');
            return $favourite->save();
        }

        public static function removeFavouriteContact($FavouriteID){
            return FavouriteExContact::find($FavouriteID)->delete();
        }
    }
