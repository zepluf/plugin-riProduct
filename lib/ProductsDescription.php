<?php 

namespace plugins\riProduct;

use plugins\riCore\Model;

class ProductsDescription extends Model{
    
    protected $table = TABLE_PRODUCTS_DESCRIPTION;
    
    public function save($new = false){
        $data = $this->getArray();
        if(!$new){        
            unset($data['products_id']);
            unset($data['language_id']);
            zen_db_perform($this->table, $data, 'update', ' products_id = ' . $this->get('products_id') . ' AND language_id = ' . $this->get('language_id'));
        }
        else{
            zen_db_perform($this->table, $data);            
        }
        
        return $this;
    }
    
    public function getTab($language,$products_id){
        global $db;
        $data =array();
        $sql = "select * from " . $this->table ." where language_id = " .$language ." and products_id = ".$products_id;
        
        $result = $db->Execute($sql);
        //echo "<pre>";var_dump($result);die();
        while(!$result->EOF){
            $data = $result->fields; 
            $result->MoveNext();
        }
        return $data;
    }
    
 
}