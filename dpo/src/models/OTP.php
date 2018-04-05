<?php  

namespace App\Model;
class OTP extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'OTP';
  	protected $primaryKey = 'OtpID';
  	public $timestamps = false;
    
}