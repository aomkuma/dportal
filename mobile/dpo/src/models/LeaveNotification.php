<?php  

namespace App\Model;
class LeaveNotification extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'leave_notification';
  	protected $primaryKey = 'ID';
  	public $timestamps = false;
  	
  	protected $fillable = ['ID'
  							, 'Email'
  							, 'Messages'
  							, 'NorifyDatetime'
  							, 'ReturnLink'
  							, 'ViewStatus'
  							, 'CreateDateTime'
  						];

}
