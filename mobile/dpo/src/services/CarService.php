<?php

namespace App\Service;

use App\Model\Car;
use Illuminate\Database\Capsule\Manager as DB;

class CarService {

    public static function getCarList($offset) {

        $limit = 15;
        $skip = $offset * $limit;
        $total = Car::count();

        $DataList = Car::select('Car.*', 'REGION.RegionName AS RegionName'
                        , DB::raw('a1.FirstName AS car_admin_firstname')
                        , DB::raw('a1.LastName AS car_admin_lastname')
                        , DB::raw('a1.Email AS car_admin_email')
                        , DB::raw('a1.Mobile AS car_admin_Mobile')
                        , DB::raw('a2.CarType AS cartype')
                        , DB::raw('a2.SeatAmount AS seat')
                        , DB::raw('a3.ProvinceName AS LicenseProvinceName')
                )
                ->leftJoin('REGION', 'Car.RegionID', '=', 'REGION.RegionID')
                ->leftJoin(DB::raw('TBL_ACCOUNT as a1'), 'CAR.CarAdminID', '=', DB::raw('a1.UserID'))
                ->leftJoin(DB::raw('TBL_CAR_TYPE as a2'), 'CAR.CarTypeID', '=', DB::raw('a2.CarTypeID'))
                ->leftJoin(DB::raw('TBL_PROVINCE as a3'), 'CAR.LicenceProvince', '=', DB::raw('a3.ProvinceID'))
                ->skip($skip)
                ->take($limit)
                ->orderBy('CarID', 'DESC')
                ->get();

        $offset += 1;
        $continueLoad = true;
        if (ceil($total / $limit) == $offset) {
            $continueLoad = false;
        }

        return ['DataList' => $DataList, 'offset' => $offset, 'continueLoad' => $continueLoad];
    }

    public static function updateData($obj) {


        $checkLicence = Car::where('License', $obj['License'])
                ->where('LicenceProvince', $obj['LicenceProvince'])
                ->where('CarID','<>', $obj['CarID'])
                ->get();

        if ($obj['CarID'] == '') {

            $Car = new Car;
        } else {
            $Car = Car::find($obj['CarID']);
        }
        if (sizeof($checkLicence) == 0) {
            $Car = $Car->setValues($Car, $obj);
            $Car->save();
            return $Car;
        } else {
            return FALSE;
        }
    }

    public static function deleteData($ID) {
        return Car::find($ID)->delete();
    }

}

?>