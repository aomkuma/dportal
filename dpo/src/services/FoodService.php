<?php

    namespace App\Service;
    
    use App\Model\Food;
    use Illuminate\Database\Capsule\Manager as DB;    
    
    class FoodService {
        
        public static function getFoodList($offset){

        	$limit = 15;
			$skip = $offset * $limit;
			$total = Food::count();
			
			$DataList = Food::select('FOOD.*', 'REGION.RegionName AS RegionName')
				    ->leftJoin('REGION', 'FOOD.RegionID', '=', 'REGION.RegionID')
                    ->skip($skip)
					->take($limit)
                    ->orderBy('FoodID', 'DESC')
					->get();

			$offset += 1;
			$continueLoad = true;
			if(ceil($total / $limit) == $offset){
				$continueLoad = false;
			}

			return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad ];

        }

        public static function updateData($obj){
        	if($obj['FoodID'] == ''){
                $food = new Food;
            }else{
                $food = Food::find($obj['FoodID']);
            }
            $food = $food->setValues($food , $obj);
            $food->save();
			return $food;
        }

        public static function deleteData($ID){
			return Food::find($ID)->delete();
        }  
        
    }    

?>