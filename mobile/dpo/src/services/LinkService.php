<?php

    namespace App\Service;
    
    use App\Model\Link;
    use App\Model\LinkPermission;
    use App\Model\Permission;
    use App\Model\User;

    use Illuminate\Database\Capsule\Manager as DB;    
    
    class LinkService {
        
        public static function getLinkList($mode, $userID = '', $permission = true){
            if($mode == 'view'){
    			$DataList = Link::select("LINK.*")
                        ->leftJoin("LINK_PERMISSION", "LINK_PERMISSION.LinkID", "=" , "LINK.LinkID")
                        ->where('LINK.LinkStatus', 'Y')
                        ->where(function($query) use ($userID, $permission){
                            if(!empty($userID) && !$permission){
                                $query->where('LINK_PERMISSION.UserID', DB::raw("'".$userID."'"));
                                $query->where('LINK_PERMISSION.LinkStatus', 1);
                            }
                            
                         })
                        ->groupBy("LINK.LinkID", "LINK.LinkUrl", "LINK.LinkIcon", "LINK.LinkStatus", "LINK.CreateBy", "LINK.CreateDateTime", "LINK.LinkTopic")
                        ->orderBy('LINK.LinkID', 'DESC')
    					->get();
            }else{
                $DataList = Link::orderBy('LinkID', 'DESC')
                        ->get();
            }
			return [ 'DataList'=>$DataList ];

        }

        public static function updateData($obj){
        	if($obj['LinkID'] == ''){
                $Link = new Link;
            }else{
                $Link = Link::find($obj['LinkID']);
            }
            $Link = $Link->setValues($Link , $obj);
            $Link->save();
			return $Link;
        }

        public static function deleteData($ID){
			return Link::find($ID)->delete();
        }  

        public static function checkAdminLink($userID){
            return Permission::where("UserID" , $userID)
                    ->where("AdminGroupID", 8)
                    ->first();
        }

        public static function getLink($linkID){
            return Link::where("LinkID",$linkID)->first();
        }        
        
        public static function getLinkPermission($offset, $linkID, $condition){
            $limit = 15;
            $skip = $offset * $limit;
            $total = User::join("REGION", "REGION.RegionID","=","ACCOUNT.RegionID") 
                    ->leftJoin("GROUP", "GROUP.GroupID","=","ACCOUNT.GroupID")
                    ->leftJoin('LINK_PERMISSION', function($join) use ($linkID)
                         {
                           $join->on('ACCOUNT.UserID', '=', 'LINK_PERMISSION.UserID');
                           $join->on('LINK_PERMISSION.LinkID', '=', DB::raw("'".$linkID."'"));
                         })
                    ->where(function($query) use ($condition){
                        if(!empty($condition['Region'])){
                            $query->where('REGION.RegionID', DB::raw("'".$condition['Region']."'"));
                        }
                        if(!empty($condition['Group'])){
                            $query->where('ACCOUNT.GroupID', DB::raw("'".$condition['Group']."'"));    
                        }
                        if(!empty($condition['Username'])){
                            $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $condition['Username']));
                            $FirstName = trim($UsernameArr[0]);
                            $LastName = trim($UsernameArr[1]);
                            $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            if(!empty($LastName)){
                                $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$LastName."%'"));
                            }
                            // $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'%".$condition['Username']."%'"));
                            // $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'%".$condition['Username']."%'"));
                        }
                     })->count();
            $DataList = User::select(
                        "ACCOUNT.UserID"
                        ,"ACCOUNT.GroupID"
                        ,"ACCOUNT.RegionID"
                        ,"ACCOUNT.FirstName"
                        ,"ACCOUNT.LastName"
                        ,"REGION.RegionName"
                        ,"LINK_PERMISSION.LinkPermissionID"
                        ,DB::raw("'".$linkID."' AS LinkID ")
                        ,"LINK_PERMISSION.LinkStatus")
                    ->join("REGION", "REGION.RegionID","=","ACCOUNT.RegionID") 
                    //->leftJoin("LINK_PERMISSION", "ACCOUNT.UserID","=","LINK_PERMISSION.UserID")
                    ->leftJoin('LINK_PERMISSION', function($join) use ($linkID)
                         {
                           $join->on('ACCOUNT.UserID', '=', 'LINK_PERMISSION.UserID');
                           $join->on('LINK_PERMISSION.LinkID', '=', DB::raw("'".$linkID."'"));
                         })
                    ->where(function($query) use ($condition){
                        if(!empty($condition['Region'])){
                            $query->where('REGION.RegionID', DB::raw("'".$condition['Region']."'"));
                        }
                        if(!empty($condition['Group'])){
                            $query->where('ACCOUNT.OrgID', DB::raw("'".$condition['Group']."'"));    
                        }
                        if(!empty($condition['Username'])){
                            $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $condition['Username']));
                            $FirstName = trim($UsernameArr[0]);
                            $LastName = trim($UsernameArr[1]);
                            $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            if(!empty($LastName)){
                                $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$LastName."%'"));
                            }
                            // $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'%".$condition['Username']."%'"));
                            // $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'%".$condition['Username']."%'"));
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

        public static function getAllLinkPermission($linkID, $condition){
            return User::select(
                        "ACCOUNT.UserID"
                        ,"ACCOUNT.GroupID"
                        ,"ACCOUNT.RegionID"
                        ,"ACCOUNT.FirstName"
                        ,"ACCOUNT.LastName"
                        ,"REGION.RegionName"
                        ,"LINK_PERMISSION.LinkPermissionID"
                        ,DB::raw("'".$linkID."' AS LinkID ")
                        ,"LINK_PERMISSION.LinkStatus")
                    ->join("REGION", "REGION.RegionID","=","ACCOUNT.RegionID") 
                    //->leftJoin("LINK_PERMISSION", "ACCOUNT.UserID","=","LINK_PERMISSION.UserID")
                    ->leftJoin('LINK_PERMISSION', function($join) use ($linkID)
                         {
                           $join->on('ACCOUNT.UserID', '=', 'LINK_PERMISSION.UserID');
                           $join->on('LINK_PERMISSION.LinkID', '=', DB::raw("'".$linkID."'"));
                         })
                    ->where(function($query) use ($condition){
                        if(!empty($condition['Region'])){
                            $query->where('REGION.RegionID', DB::raw("'".$condition['Region']."'"));
                        }
                        if(!empty($condition['Group'])){
                            $query->where('ACCOUNT.OrgID', DB::raw("'".$condition['Group']."'"));    
                        }
                        if(!empty($condition['Username'])){
                            $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $condition['Username']));
                            $FirstName = trim($UsernameArr[0]);
                            $LastName = trim($UsernameArr[1]);
                            $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                            if(!empty($LastName)){
                                $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$LastName."%'"));
                            }
                            // $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'%".$condition['Username']."%'"));
                            // $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'%".$condition['Username']."%'"));
                        }
                     })
                    ->orderBy("ACCOUNT.RegionID") 
                    ->orderBy("ACCOUNT.FirstName") 
                    // ->skip($skip)
                    // ->take($limit)
                    ->get();
            
        }

        public static function updateLinkPermission($obj){
            $Link = LinkPermission::where("UserID", $obj['UserID'])
                        ->where("LinkID", $obj['LinkID'])
                        ->first();
            if(empty($Link)){
                $Link = new LinkPermission;
            }

            $Link = $Link->setValues($Link , $obj);
            $Link->save();
            return $Link->LinkPermissionID;
        }
    }    

?>