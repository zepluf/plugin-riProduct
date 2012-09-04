<?php
namespace plugins\riProduct;

use plugins\riCore\Object;
use plugins\riPlugin\Plugin;

class Products extends Object{
	public function findById($products_id){
		global $db;
		$sql = "SELECT * FROM ".TABLE_PRODUCTS." WHERE products_id = :products_id";
		$sql = $db->bindVars($sql, ":products_id", $products_id, 'integer');
		$result = $db->Execute($sql);
		
		if($result->RecordCount() > 0){
			$product = Plugin::get('riProduct.Product');			
			$product->setArray($result->fields);
			return $product;
		}
		
		return false;
	}
	
	public function findByName($products_name, $limit = 20){
		global $db;
		$sql = "SELECT p.* 
		        FROM " . TABLE_PRODUCTS . " p," . TABLE_PRODUCTS_DESCRIPTION . " pd
		 	    WHERE pd.products_id = p.products_id
				AND pd.language_id = :languages_id
				AND products_name like ':products_name%'";
		
		if($limit > 0) $sql .= " limit $limit";
		
		$sql = $db->bindVars($sql, ":languages_id", $_SESSION['languages_id'], 'integer');
		$sql = $db->bindVars($sql, ":products_name", $products_name, 'noquotestring');
		
		$result = $db->Execute($sql);
		
		return $this->generateCollection($result);
	}
	
	public function find($filters = array(), $limit = 0){
	    global $db;
	    $sql = $this->findSql($filters = array(), $limit = 0);
	    
	    $result = $db->Execute($sql);
	    
	    return $this->generateCollection($result);
	}
	
	public function generateSql($filters, $order = '', $limit = 0){

        $filters = array_merge(array('p.products_status = 1'), $filters);

	    $sql = "select distinct *
               from " . TABLE_PRODUCTS . " p
               left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id
               left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id
               left join " . TABLE_FEATURED . " f on p.products_id = f.products_id
               left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";

        if(count($filters) > 0){
            $sql .= ' WHERE ' . implode (' AND ', $filters);
        }

        if(!empty($order))
            $sql .= " order by " . $order;

	    if($limit > 0) $sql .= " limit " . (int)$limit;

	    return $sql;
	}
	
	/**
	 * 
	 * Special note: this function does not return the product object.
	 * Things may change later
	 * @param string $listing_split
	 */
    public function findBySql($sql){
        global $db;
	    $products = array();
	    
	    $products_result = $db->Execute($sql);
        while (!$products_result->EOF) {                    
            $products[] = array_merge($products_result->fields,
                array(
        			'products_display_price' => zen_get_products_display_price($products_result->fields['products_id']),    			            			
        			'products_link' => zen_href_link(zen_get_info_page($products_result->fields['products_id']), 'products_id=' . $products_result->fields['products_id'])                        
                )
            );            
            $products_result->MoveNext();
        }
	    
        return $products;
	}
	
	private function generateCollection($result){	   
	    
	    if($result->RecordCount() > 0){
			$collection = array();
			while(!$result->EOF){
				$product = Plugin::get('riProduct.Product');			
				$product->setArray($result->fields);	
				$collection[] = $product;
				$result->MoveNext();
			}		
			return $collection;
		}
		
		return false;
	}
}