<?php

namespace App\Service;

use App\Model\User;
use App\Model\Permission;
use Illuminate\Database\Capsule\Manager as DB;

class PermissionService {

    public static function updatePermission($obj) {
        //print_r($obj);die();
        
        $permission = Permission::where('UserID' , $obj['UserID'])
                    ->where('PermissionType' , $obj['PermissionType'])
                    ->first();

        if(empty($permission)){
            $permission = new Permission;
            $permission->CreateBy = 1;
            $permission->CreateDateTime = date('Y-m-d H:i:s.000');
            $permission->PermissionType = $obj['PermissionType'];
            $permission->UserID = $obj['UserID'];
        }

        $permission->AdminGroupID = $obj['AdminGroupID'];
        $permission->RegionID = $obj['RegionID'];
        $permission->UpdateBy = 1;
        $permission->UpdateDateTime = date('Y-m-d H:i:s.000');
        $permission->PermissionType = $obj['PermissionType'];
        return $permission->save();

    }

    public static function getPermissionList($Region, $Group, $Username) {
        
        return User::select('ACCOUNT.UserID'
                        ,'ACCOUNT.RegionID'
                        , 'ACCOUNT.FirstName'
        				, 'ACCOUNT.LastName'
        				, 'REGION.RegionName'
                        , DB::raw('a1.AdminGroupID AS SuperAdmin')
                        , DB::raw('a2.AdminGroupID AS PermissionAdmin')
                        , DB::raw('a3.AdminGroupID AS RoomAdmin')
                        , DB::raw('a4.AdminGroupID AS CarAdmin')
                        , DB::raw('a5.AdminGroupID AS DeviceAdmin')
                        , DB::raw('a6.AdminGroupID AS NewsAdmin')
                        , DB::raw('a7.AdminGroupID AS NewsApproveAdmin')
                        , DB::raw('a8.AdminGroupID AS RepairAdmin')
                        , DB::raw('a9.AdminGroupID AS LinkAdmin')
                        , DB::raw('a10.AdminGroupID AS ExPhoneBookAdmin')
                        , DB::raw('a11.AdminGroupID AS CalendarAdmin')
                        
                )
                ->leftJoin('REGION', 'ACCOUNT.RegionID', '=', 'REGION.RegionID')
                ->leftJoin(DB::raw('TBL_PERMISSION as a1'), function($join)
						 {
						   $join->on(DB::raw('a1.UserID'), '=', 'ACCOUNT.UserID');
						   $join->on(DB::raw('a1.AdminGroupID'), '=', DB::raw("'0'"));
						 })
                ->leftJoin(DB::raw('TBL_PERMISSION as a2'), function($join)
                         {
                           $join->on(DB::raw('a2.UserID'), '=', 'ACCOUNT.UserID');
                           $join->on(DB::raw('a2.AdminGroupID'), '=', DB::raw("'1'"));
                         })
                ->leftJoin(DB::raw('TBL_PERMISSION as a3'), function($join)
						 {
						   $join->on(DB::raw('a3.UserID'), '=', 'ACCOUNT.UserID');
						   $join->on(DB::raw('a3.AdminGroupID'), '=', DB::raw("'2'"));
						 })
                ->leftJoin(DB::raw('TBL_PERMISSION as a4'), function($join)
						 {
						   $join->on(DB::raw('a4.UserID'), '=', 'ACCOUNT.UserID');
						   $join->on(DB::raw('a4.AdminGroupID'), '=', DB::raw("'3'"));
						 })
                ->leftJoin(DB::raw('TBL_PERMISSION as a5'), function($join)
                         {
                           $join->on(DB::raw('a5.UserID'), '=', 'ACCOUNT.UserID');
                           $join->on(DB::raw('a5.AdminGroupID'), '=', DB::raw("'4'"));
                         })
                ->leftJoin(DB::raw('TBL_PERMISSION as a6'), function($join)
                         {
                           $join->on(DB::raw('a6.UserID', '='), 'ACCOUNT.UserID');
                           $join->on(DB::raw('a6.AdminGroupID'), '=', DB::raw("'5'"));
                         })
                ->leftJoin(DB::raw('TBL_PERMISSION as a7'), function($join)
                         {
                           $join->on(DB::raw('a7.UserID'), '=', 'ACCOUNT.UserID');
                           $join->on(DB::raw('a7.AdminGroupID'), '=', DB::raw("'6'"));
                         })
                ->leftJoin(DB::raw('TBL_PERMISSION as a8'), function($join)
                         {
                           $join->on(DB::raw('a8.UserID'), '=', 'ACCOUNT.UserID');
                           $join->on(DB::raw('a8.AdminGroupID'), '=', DB::raw("'7'"));
                         })

                ->leftJoin(DB::raw('TBL_PERMISSION as a9'), function($join)
                         {
                           $join->on(DB::raw('a9.UserID'), '=', 'ACCOUNT.UserID');
                           $join->on(DB::raw('a9.AdminGroupID'), '=', DB::raw("'8'"));
                         })

                ->leftJoin(DB::raw('TBL_PERMISSION as a10'), function($join)
                         {
                           $join->on(DB::raw('a10.UserID'), '=', 'ACCOUNT.UserID');
                           $join->on(DB::raw('a10.AdminGroupID'), '=', DB::raw("'9'"));
                         })

                ->leftJoin(DB::raw('TBL_PERMISSION as a11'), function($join)
                         {
                           $join->on(DB::raw('a11.UserID'), '=', 'ACCOUNT.UserID');
                           $join->on(DB::raw('a11.AdminGroupID'), '=', DB::raw("'10'"));
                         })

                ->where(function($query) use ($Region){
                		if(!empty($Region)){
	                        $query->where('ACCOUNT.RegionID', DB::raw("'".$Region."'"));
	                    }
                    })
                ->where(function($query) use ($Group){
                		if(!empty($Group)){
	                        $query->where('ACCOUNT.OrgID', DB::raw("'".$Group."'"));
	                    }
                    })
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
                ->orderBy('ACCOUNT.FirstName', 'ASC')
                ->get();

    }

    public static function checkPermission($UserID){
        return Permission::select('AdminGroupID')->where('UserID', $UserID)->get()->toArray();
    }
}