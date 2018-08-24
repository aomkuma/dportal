<?php

    namespace App\Service;
    
    use App\Model\Cartype;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class CartypeService {
        
        public static function getCartypeManageList($offset){

        	$limit = 15;
			$skip = $offset * $limit;
			$total = Cartype::count();
			
			$DataList = Cartype::skip($skip)->take($limit)
                    ->orderBy('CarTypeID', 'DESC')
					->get();

			$offset += 1;
			$continueLoad = true;
			if(ceil($total / $limit) == $offset){
				$continueLoad = false;
			}

			return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

        public static function getCartypeList(){
            
            $DataList = Cartype::where('ActiveStatus', 'Y')
                    ->orderBy('CarTypeID', 'DESC')
                    ->get();

            return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

        public static function updateData($obj){
        	if($obj['CarTypeID'] == ''){
                $Cartype = new Cartype;
            }else{
                $Cartype = Cartype::find($obj['CarTypeID']);
            }
            $Cartype = $Cartype->setValues($Cartype , $obj);
            $Cartype->save();
			return $Cartype;
        }

        public static function deleteData($ID){
			return Cartype::find($ID)->delete();
        }  
        
    }    

?>