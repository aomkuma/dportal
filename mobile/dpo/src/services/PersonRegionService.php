<?php

namespace App\Service;

use App\Model\User;
use App\Model\PersonRegion;

use Illuminate\Database\Capsule\Manager as DB;

class PersonRegionService {

	public static function getPersonRegionList($Username){
		return User::select('ACCOUNT.UserID'
                        , 'ACCOUNT.FirstName'
        				, 'ACCOUNT.LastName'
        				, DB::raw('a1.RegionID AS RegionID1')
                        , DB::raw('a2.RegionID AS RegionID2')
                        , DB::raw('a3.RegionID AS RegionID3')
                        , DB::raw('a4.RegionID AS RegionID4')
                        , DB::raw('a5.RegionID AS RegionID5')
                        , DB::raw('a6.RegionID AS RegionID6')
                        , DB::raw('a7.RegionID AS RegionID7')
        			)
					->leftJoin(DB::raw('TBL_PERSON_REGION as a1'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a1.UserID'));
                           $join->on(DB::raw('a1.RegionID'), '=', DB::raw('1'));
                         })
                	->leftJoin(DB::raw('TBL_PERSON_REGION as a2'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a2.UserID'));
                           $join->on(DB::raw('a2.RegionID'), '=', DB::raw('2'));
                         })
                	->leftJoin(DB::raw('TBL_PERSON_REGION as a3'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a3.UserID'));
                           $join->on(DB::raw('a3.RegionID'), '=', DB::raw('3'));
                         })
                	->leftJoin(DB::raw('TBL_PERSON_REGION as a4'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a4.UserID'));
                           $join->on(DB::raw('a4.RegionID'), '=', DB::raw('4'));
                         })
                	->leftJoin(DB::raw('TBL_PERSON_REGION as a5'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a5.UserID'));
                           $join->on(DB::raw('a5.RegionID'), '=', DB::raw('5'));
                         })
                	->leftJoin(DB::raw('TBL_PERSON_REGION as a6'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a6.UserID'));
                           $join->on(DB::raw('a6.RegionID'), '=', DB::raw('6'));
                         })
                	->leftJoin(DB::raw('TBL_PERSON_REGION as a7'), function($join)
                         {
                           $join->on(DB::raw('TBL_ACCOUNT.UserID'), '=', DB::raw('a7.UserID'));
                           $join->on(DB::raw('a7.RegionID'), '=', DB::raw('7'));
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

	public static function updatePersonRegion($UserID, $RegionID){	
		$model = PersonRegion::where('UserID', $UserID)
					->where('RegionID', $RegionID)
					->first();
		if(empty($model)){
			$model = new PersonRegion;
			$model->UserID = $UserID;
			$model->RegionID = $RegionID;
			$model->save();
		}else{
			$model->delete();
		}
	}

    public static function deleteData($ID) {
        return PersonRegion::find($ID)->delete();
    }

}

?>