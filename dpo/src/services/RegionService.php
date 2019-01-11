<?php

    namespace App\Service;
    
    use App\Model\Region;
    
    class RegionService {
        
        public static function getAllRegion(){
            return Region::all();      
        }   
        public static function getRegion($RegionID){
            return Region::where('RegionID',$RegionID)->first();      
        }                
        
    }    

?>