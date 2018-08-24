<?php
    
    namespace App\Service;
    
    use App\Model\User;
    use App\Model\SystemLoginCount;
    use App\Model\GroupMenu;
    use App\Model\Permission;
    use App\Model\OTP;

    use Illuminate\Database\Capsule\Manager as DB;
    
    class LoginService {
        
        public static function authenticate($username , $password){
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
                    ->where('Username', $username)->where('Password',$password)->first();      
        }

        public static function authenticateNoPass($username ){
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
                    ->where('Username', $username)->first();      
        }                

        public static function verifyUsername($username){
            $user = User::where('Username', $username)->first();
            return (!empty($user));
        }

        public static function getMobilePhoneNumber($username){
            return User::where('Username', $username)->first();
        }

        public static function generateOTP(){
            $newOtp = rand(100000, 999999);    // Random 6 digits

            // Check dupicate OTP
            $otp = OTP::where('OtpCode', $newOtp)
                    ->where('OtpStatus' , 'active')
                    ->first();
            if(!empty($otp)){
                $newOtp = LoginService::generateOTP();
            }

            return $newOtp;
        }

        public static function updateOTP($otp, $UserID){
            $otpObj = new OTP;
            $otpObj->OtpCode = $otp;
            $otpObj->OtpStatus = 'active';
            $otpObj->CreateBy = $UserID;
            $otpObj->CreateDateTime = date('Y-m-d H:i:s.000');
            $otpObj->save();
            return $otpObj;
        }

        public static function updateInactiveOTP($otpID){

            $otpObj = OTP::find($otpID);
            $otpObj->OtpStatus = 'inactive';
            $otpObj->save();
            //return $otpObj;
        }

        public static function verifyOTP($otp){
            return OTP::where('OtpCode', $otp)
                    ->where('OtpStatus' , 'active')
                    ->first();
        }

        public static function updatePassword($username, $newPassword){
            $user = User::where('Username', $username)->first();
            $user->Password = $newPassword;
            return $user->save();
            //return $otpObj;
        }

        public static function updateSystemLoginCount(){

            $totalLogin = 0;
            $loginCount = SystemLoginCount::first();
            //if(!empty($loginCount)){
                $totalLogin = (intval($loginCount->CountTotal) + 1);
                $loginCount->CountTotal = $totalLogin;
                $loginCount->save();
            //}
            return $totalLogin;
        }

        public static function getMenuList($UserID){
            //return GroupMenu::where('GroupID',$GroupID)->get();
            return Permission::where('UserID' , $UserID)
                    ->where("AdminGroupID" , "<>", -1)
                    ->get();
        }
        
    }

?>