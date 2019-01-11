<?php  

namespace App\Model;
class VisitLink extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'VISIT_LINK';
  public $timestamps = false;
  protected $primaryKey = 'id';

  protected $fillable = array('id'
  							, 'visit_name'
  							, 'visit_datetime'
  							, 'visit_ip'
  							, 'link_id'
  						);

}