<?php 

namespace plugins\riProduct;

use plugins\riCore\Model;
use plugins\riPlugin\Plugin;

class Product extends Model {
    protected $id = 'products_id',
        $table = TABLE_PRODUCTS,
        $products_link = '',
        $products_prices = false;
    
	private $description, $categories = array();
	
	// TODO: need to return false
	public function save(){
		global $db;
					
		$data = $this->getArray(array('description'));
		
		unset($data['id']);
		
		if(isset($this->productsId) && $this->productsId > 0){
			// TODO: description
			zen_db_perform(TABLE_PRODUCTS, $data, 'update', 'id = '.$this->id);
			return true;
		}
		else {
			zen_db_perform(TABLE_PRODUCTS, $data);
			$this->productsId = $db->Insert_ID();
			
			// insert description
			$this->description->productsId = $this->productsId;
			$this->description->save(true);						
			
			// insert 2 category relationship
			zen_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, array('products_id' => $this->productsId, 'categories_id' => $this->masterCategoriesId));
			
			return true;
		}

		return false;
	}
	
	public function getDescription($languages_id = 1){
	    
	    if(isset($this->description) && !empty($this->description)) return $this->description;
		global $db;
		$sql = "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = :products_id AND language_id = :languages_id";
		$sql = $db->bindVars($sql, ":products_id", $this->productsId, 'integer');
		$sql = $db->bindVars($sql, ":languages_id", $languages_id, 'integer');
		
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0){
		    $this->description = Plugin::get('riProduct.ProductsDescription')->setArray($result->fields);		    
		}
		
		return $this->description;  
	} 
	
	public function setDescription($description){
	    $this->description = $description;
	}

    function getPrice($key = null) {
        if($this->products_prices === false){
            global $db;

            $free = false;
            $call = false;

    // 0 = normal shopping
    // 1 = Login to shop
    // 2 = Can browse but no prices
            // verify display of prices
            switch (true) {
                case (CUSTOMERS_APPROVAL == '1' and $_SESSION['customer_id'] == ''):
                    // customer must be logged in to browse
                    return $this->products_prices = '';
                    break;
                case (CUSTOMERS_APPROVAL == '2' and $_SESSION['customer_id'] == ''):
                    // customer may browse but no prices
                    return $this->products_prices = TEXT_LOGIN_FOR_PRICE_PRICE;
                    break;
                case (CUSTOMERS_APPROVAL == '3' and TEXT_LOGIN_FOR_PRICE_PRICE_SHOWROOM != ''):
                    // customer may browse but no prices
                    return $this->products_prices = TEXT_LOGIN_FOR_PRICE_PRICE_SHOWROOM;
                    break;
                case ((CUSTOMERS_APPROVAL_AUTHORIZATION != '0' and CUSTOMERS_APPROVAL_AUTHORIZATION != '3') and $_SESSION['customer_id'] == ''):
                    // customer must be logged in to browse
                    return $this->products_prices = TEXT_AUTHORIZATION_PENDING_PRICE;
                    break;
                case ((CUSTOMERS_APPROVAL_AUTHORIZATION != '0' and CUSTOMERS_APPROVAL_AUTHORIZATION != '3') and $_SESSION['customers_authorization'] > '0'):
                    // customer must be logged in to browse
                    return $this->products_prices = TEXT_AUTHORIZATION_PENDING_PRICE;
                    break;
                default:
                    // proceed normally
                    break;
            }

    // show case only
            if (STORE_STATUS != '0') {
                if (STORE_STATUS == '1') {
                    return $this->products_prices = '';
                }
            }

            // $new_fields = ', product_is_free, product_is_call, product_is_showroom_only';
            $product_check = $db->Execute("select products_tax_class_id, products_price, products_priced_by_attribute, product_is_free, product_is_call, products_type from " . TABLE_PRODUCTS . " where products_id = '" . (int)$this->productsId . "'" . " limit 1");

            // no prices on Document General
            if ($product_check->fields['products_type'] == 3) {
                return $this->products_prices = '';
            }

            $display_normal_price = zen_get_products_base_price($this->productsId);
            $display_special_price = zen_get_products_special_price($this->productsId, true);
            $display_sale_price = zen_get_products_special_price($this->productsId, false);

            $show_sale_discount = false;
            if (SHOW_SALE_DISCOUNT_STATUS == '1' and ($display_special_price != 0 or $display_sale_price != 0)) {
                if ($display_sale_price) {
                    if (SHOW_SALE_DISCOUNT == 1) {
                        if ($display_normal_price != 0) {
                            $show_discount_amount = number_format(100 - (($display_sale_price / $display_normal_price) * 100),SHOW_SALE_DISCOUNT_DECIMALS);
                        } else {
                            $show_discount_amount = false;
                        }
                        $show_sale_discount = $show_discount_amount;

                    } else {
                        $show_sale_discount = zen_add_tax(($display_normal_price - $display_sale_price), zen_get_tax_rate($product_check->fields['products_tax_class_id'])) . PRODUCT_PRICE_DISCOUNT_AMOUNT;
                    }
                } else {
                    if (SHOW_SALE_DISCOUNT == 1) {
                        $show_sale_discount = number_format(100 - (($display_special_price / $display_normal_price) * 100),SHOW_SALE_DISCOUNT_DECIMALS);
                    } else {
                        $show_sale_discount = zen_add_tax(($display_normal_price - $display_special_price), zen_get_tax_rate($product_check->fields['products_tax_class_id'])) . PRODUCT_PRICE_DISCOUNT_AMOUNT;
                    }
                }
            }

            if ($display_special_price) {
                $show_normal_price = zen_add_tax($display_normal_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                if ($display_sale_price && $display_sale_price != $display_special_price) {
                    $show_special_price = zen_add_tax($display_special_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                    if ($product_check->fields['product_is_free'] == '1') {
                        $show_sale_price = zen_add_tax($display_sale_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                    } else {
                        $show_sale_price = zen_add_tax($display_sale_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                    }
                } else {
                    if ($product_check->fields['product_is_free'] == '1') {
                        $show_special_price = zen_add_tax($display_special_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                    } else {
                        $show_special_price = zen_add_tax($display_special_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                    }
                    $show_sale_price = false;
                }
            } else {
                if ($display_sale_price) {
                    $show_normal_price = zen_add_tax($display_normal_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                    $show_special_price = false;
                    $show_sale_price = zen_add_tax($display_sale_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                } else {
                    if ($product_check->fields['product_is_free'] == '1') {
                        $show_normal_price = zen_add_tax($display_normal_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                    } else {
                        $show_normal_price = zen_add_tax($display_normal_price, zen_get_tax_rate($product_check->fields['products_tax_class_id']));
                    }
                    $show_special_price = false;
                    $show_sale_price = false;
                }
            }

            // If Free, Show it
            if ($product_check->fields['product_is_free'] == '1') {
                $free = true;
            }

            // If Call for Price, Show it
            if ($product_check->fields['product_is_call']) {
                $call = true;
            }
            //$final_display_price . $free_tag . $call_tag;
            $this->products_prices = array('normal' => $show_normal_price,
                'special' => $show_special_price,
                'sale' => $show_sale_price,
                'discount' => $show_sale_discount,
                'call' => $call,
                'free' => $free
            );
        }

        return empty($key) ? $this->products_prices : $this->products_prices[$key];
    }

    public function getLink(){
        if(empty($this->products_link))
        $this->products_link = zen_href_link(zen_get_info_page($this->productsId, 'products_id=' . $this->productsId));
        return $this->products_link;
    }


}