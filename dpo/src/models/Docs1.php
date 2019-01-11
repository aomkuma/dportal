<?php  

namespace App\Model;
class Docs1 extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'DOCS_1';
  public $timestamps = false;
  protected $primaryKey = 'id';

  protected $fillable = array('id'
  							, 'doc_type'
  							, 'doc_name'
  							, 'doc_file'
  							, 'actives'
  						);

  public function docs2()
    {
        return $this->hasMany('App\Model\Docs2','parent_id');
    }
   

}