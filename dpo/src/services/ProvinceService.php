<?php

    namespace App\Service;
    
    use App\Model\Province;
    
    class ProvinceService {
        
        public static function getAllProvince(){
            return Province::orderBy("ProvinceName", "ASC")->get();      
        }                
        
    }    

?>