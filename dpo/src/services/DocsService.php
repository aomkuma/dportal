<?php

    namespace App\Service;
    
    use App\Model\Docs1;
    use App\Model\Docs2;
    use App\Model\Docs3;

    use Illuminate\Database\Capsule\Manager as DB;
    
    class DocsService {
        
        public static function getDocsView($doc_type){
            return Docs1::where('doc_type', $doc_type)
                    ->where('actives', 'Y')
                    ->with(array('docs2' => function($query){
                                $query->with('docs3');
                            }))
                    ->get();      
        } 

        public static function getDocs1List($doc_type, $condition = []){
            return Docs1::where('doc_type', $doc_type)
                    ->where(function($query) use ($condition){
                                if(!empty($condition['keyword'])){
                                    $query->where('doc_name', 'LIKE', DB::raw("'%".$condition['keyword']."%'"));
                                }
                            })
            		->get();      
        } 

        public static function getDocs2List($parent_id, $condition = []){
            return Docs2::where('parent_id', $parent_id)
            		->get();      
        } 

        public static function getDocs3List($parent_id, $condition = []){
            return Docs3::where('parent_id', $parent_id)
            		->get();      
        }

        public static function getDocs1($id){
            return Docs1::find($id);
        }

        public static function getDocs2($id){
            return Docs2::find($id);
        }

        public static function getDocs3($id){
            return Docs3::find($id);
        }                
        
        public static function updateDoc1($obj){

            if(empty($obj['id'])){
                $model = Docs1::create($obj);
                return $model->id;    
               
            }else{
                Docs1::where('id', $obj['id'])->update($obj);
                return $obj['id'];
            }
        }

        public static function updateDoc2($obj){

            if(empty($obj['id'])){
                $model = Docs2::create($obj);
                return $model->id;    
               
            }else{
                Docs2::where('id', $obj['id'])->update($obj);
                return $obj['id'];
            }
        }

        public static function updateDoc3($obj){

            if(empty($obj['id'])){
                $model = Docs3::create($obj);
                return $model->id;    
               
            }else{
                Docs3::where('id', $obj['id'])->update($obj);
                return $obj['id'];
            }
        }
    }    

?>