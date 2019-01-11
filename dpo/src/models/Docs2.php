<?php  

namespace App\Model;
class Docs2 extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'DOCS_2';
  public $timestamps = false;
  protected $primaryKey = 'id';

  protected $fillable = array('id'
                , 'parent_id'
  							, 'doc_name'
  							, 'doc_file'
                , 'file_type'
  							, 'actives'
  						);

  public function docs3()
    {
        return $this->hasMany('App\Model\Docs3','parent_id');
    }
   

}