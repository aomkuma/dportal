<?php

    namespace App\Service;
    
    use App\Model\Region;
    
    class RegionService {
        
        public static function getAllRegion(){
            return Region::all();      
        }                
        
    }    

?>