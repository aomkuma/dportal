<?php

    namespace App\Service;
    
    use App\Model\Calendar;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class CalendarService {
        
        public static function getCalendarList($mode){

			$DataList = Calendar::where(function($query) use ($mode){
                        if($mode == 'view'){
                            $query->where('ActiveStatus', 'Y');
                        }
                    })
                    ->orderBy('CalendarID', 'DESC')
					->get();

			return [ 'DataList'=>$DataList ];

        }

        public static function getHomePageCalendar(){
            return Calendar::where("IsFirstPage", 'Y')
                        ->where("ActiveStatus", 'Y')
                        ->first();
        }

        public static function updateData($obj){
            if($obj['IsFirstPage'] == 'Y'){
                Calendar::where('IsFirstPage', 'Y')->update(['IsFirstPage' => 'N']);
            }
            
        	if($obj['CalendarID'] == ''){
                $Calendar = new Calendar;
            }else{
                $Calendar = Calendar::find($obj['CalendarID']);
            }

            $Calendar = $Calendar->setValues($Calendar , $obj);
            $Calendar->save();
			return $Calendar;
        }

        public static function deleteData($ID){
			return Calendar::find($ID)->delete();
        }  
        
    }    

?>