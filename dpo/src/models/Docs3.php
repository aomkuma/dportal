<?php  

namespace App\Model;
class Docs3 extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'DOCS_3';
  public $timestamps = false;
  protected $primaryKey = 'id';

  protected $fillable = array('id'
                , 'parent_id'
  							, 'doc_name'
  							, 'doc_file'
                , 'file_type'
  							, 'actives'
  						);
}