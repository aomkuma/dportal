<?php
    
    namespace App\Service;
    
    use App\Model\User;
    use App\Model\Department;
    use App\Model\Division;
    use App\Model\Office;
    use App\Model\Group;

    class EHRService {
        
        public static function updateDepartment($obj){
            //echo $obj['id'];
            $dep = Department::find($obj['id']);
            if($dep == null){
                $dep = new Department;
                $dep->id = $obj['id'];    
            }
            $dep->orgID = $obj['orgID'];
            $dep->orgShortName = $obj['orgShortName'];
            $dep->orgName = $obj['orgName'];
            $dep->upperOrgID = $obj['upperOrgID'];
            $dep->save();
        }

        public static function updateOffice($obj){
            //echo $obj['id'];
            $office = Office::find($obj['id']);
            if($office == null){
                $office = new Office;
                $office->id = $obj['id'];    
            }
            $office->orgID = $obj['orgID'];
            $office->orgShortName = $obj['orgShortName'];
            $office->orgName = $obj['orgName'];
            $office->upperOrgID = $obj['upperOrgID'];
            $office->save();
        }

        public static function updateDivision($obj){
            //echo $obj['id'];
            $division = Division::find($obj['id']);
            if($division == null){
                $division = new Division;
                $division->id = $obj['id'];    
            }
            $division->orgID = $obj['orgID'];
            $division->orgShortName = $obj['orgShortName'];
            $division->orgName = $obj['orgName'];
            $division->upperOrgID = $obj['upperOrgID'];
            $division->save();
        }

        public static function updateGroup($obj, $GroupType){
            //echo $obj['id'];
            $g = Group::where('OrgID' , $obj['orgID'])->first();
            if($g == null){
                $g = new Group;
                //$g->id = $obj['id'];    
            }
            $g->OrgID = $obj['orgID'];
            $g->OrgShortName = $obj['orgShortName'];
            $g->OrgName = $obj['orgName'];
            $g->UpperOrgID = $obj['upperOrgID'];
            $g->GroupType = $GroupType;
            $g->GroupName = $obj['orgName'];
            $g->GroupAdminType = 'N';
            if(empty($g->RegionID)){
                $g->RegionID = EHRService::findRegion($obj['orgName']);
            }
            $g->save();
        }

        public static function findRegion($orgName){
            $regionID = 2;
            if(strpos($orgName, 'ภก') !== false || strpos($orgName, 'ภาคกลาง') !== false){
                $regionID = 3;
            }else if(strpos($orgName, 'ภต') !== false || strpos($orgName, 'ภาคใต้') !== false){
                $regionID = 4;
            }elseif(strpos($orgName, 'ภอ') !== false || strpos($orgName, 'ภาคตะวันออกเฉียงเหนือ') !== false){
                $regionID = 5;
            }elseif(strpos($orgName, 'ภล') !== false || strpos($orgName, 'ภาคเหนือตอนล่าง') !== false){
                $regionID = 6;
            }elseif(strpos($orgName, 'ภบ') !== false || strpos($orgName, 'ภาคเหนือตอนบน') !== false){
                $regionID = 7;
            }
            return $regionID;
        }
        
        public static function updateStaff($obj, $regionID, $groupID){
            //echo $obj['id'];
            $user = User::where('DataID' , $obj['ID'])->first();
            if($user == null){
                $user = new User;   
                $user->CreateBy = 1;
                $user->CreateDateTime = date('Y-m-d H:i:s.000');
            }
            $user->StaffID = (empty($obj['staffID'])?$obj['ID']:$obj['staffID']);
            $user->FirstName = $obj['staffFName'];
            $user->LastName = $obj['staffLName'];
            $user->Picture = $obj['staffImage'];
            $user->Mobile = $obj['staffTel'];
            $user->Email = $obj['staffEmail'];
            $user->PositionID = $obj['positionID'];
            $user->PositionName = $obj['position'];
            $user->Rank = $obj['rank'];
            $user->OrgID = $obj['orgID'];
            $user->Org = $obj['org'];
            $user->SegID = $obj['segID'];
            $user->IsHeader = $obj['isHeader'];
            $user->IsConsult = $obj['isConsult'];
            $user->IsActing = $obj['isActing'];
            $user->GroupID = $groupID;
            $user->RegionID = $regionID;
            $user->Username = $obj['staffEmail'];
            $user->Password = $obj['staffEmail'];
            $user->UpdateBy = 1;
            $user->UpdateDateTime = date('Y-m-d H:i:s.000');
            $user->DataID = $obj['ID'];
            return $user->save();
        }

        public static function getGroupID($orgName){
            $Group = Group::select('GroupID')->where('OrgName', $orgName)->first();
            return $Group['GroupID'];
        }
    }

?>