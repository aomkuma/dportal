<?php

    namespace App\Service;
    
    use App\Model\Department;
    use App\Model\Group;
    use App\Model\User;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class DepartmentService {
        
        // Repair Type
        public static function getDepartmentList(){

			return Department::all();

        }

        public static function getAllDepartmentList(){

			return Group::all();

        }

        public static function loadManageDepartmentList($condition){

            return Group::where(function($query) use ($condition){
                        if(!empty($condition['Region'])){
                            $query->where('RegionID', DB::raw("'".$condition['Region']."'"));
                        }
                        if(!empty($condition['Group'])){
                            $query->where('GroupID', DB::raw("'".$condition['Group']."'"));    
                        }
                     })->get();

        }

        public static function updateRegionOfDepartment($groupID, $RegionID){

            $group = Group::find($groupID);
            $group->RegionID = $RegionID;
            return $group->save();

        }

        public static function updateRegionOfUsers($orgID, $RegionID){
            User::where('OrgID', $orgID)->update(['RegionID' => $RegionID]);
        }
        
    }    

?>