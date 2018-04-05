<?php

    namespace App\Service;
    
    use App\Model\ExternalPhoneBook;
	use App\Model\Account;
    
    
    use Illuminate\Database\Capsule\Manager as DB;
    
    class AutocompleteService {
        
        public static function getAccount($keyword){
            
            return Account::where(function($query) use ($keyword){
                                if(!empty($keyword)){
                                    $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $keyword));
                                    $FirstName = trim($UsernameArr[0]);
                                    $LastName = trim($UsernameArr[1]);
                                    $query->where('ACCOUNT.FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                                    if(!empty($LastName)){
                                        $query->orWhere('ACCOUNT.LastName', 'LIKE', DB::raw("N'".$LastName."%'"));
                                    }
                                }
                            })
                            ->skip(0)
                            ->take(10)
                            ->get();
        }

        public static function getExUser($keyword){
            $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $keyword));
            $FirstName = trim($UsernameArr[0]);
            $LastName = trim($UsernameArr[1]);
            return ExternalPhoneBook::where(function($query) use ($keyword){
                                if(!empty($keyword)){
                                    $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $keyword));
                                    $FirstName = trim($UsernameArr[0]);
                                    $LastName = trim($UsernameArr[1]);
                                    $query->where('FirstName', 'LIKE', DB::raw("N'".$FirstName."%'"));
                                    if(!empty($LastName)){
                                        $query->orWhere('LastName', 'LIKE', DB::raw("N'".$LastName."%'"));
                                    }
                                    $query->orWhere('CompanyName', 'like', DB::raw("N'".$FirstName."%'"));
                                }
                            })
                    ->skip(0)
                    ->take(10)
                    ->get();
        }
        
    }    

?>