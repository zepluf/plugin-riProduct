<?php
namespace plugins\riProduct;
use plugins\riPlugin\Plugin;

class Observer extends \base
{
	function __construct(){
		global $zco_notifier;
		
		$zco_notifier->attach($this,array(
            'ON_PRODUCT_INFO_FORM_AFTER_DESCRIPTION',
            'ON_PRODUCT_INFO_PROCESS_END',
            'ON_PRODUCT_INFO_PREVIEW_AFTER_DESCRIPTION',
            'ON_PRODUCT_INFO_PREVIEW_FORM_END'));
                
                
	}
	
	function update($callingClass, $notifier, $paramsArray)
	{
        switch($notifier){
            case "ON_PRODUCT_INFO_FORM_AFTER_DESCRIPTION":
                echo Plugin::get('view')->render('riProduct::backend/_add_form.php');
                break;
            case "ON_PRODUCT_INFO_PREVIEW_AFTER_DESCRIPTION":
                echo Plugin::get('view')->render('riProduct::backend/_preview_info.php');
                break;
            case "ON_PRODUCT_INFO_PREVIEW_FORM_END":
                echo Plugin::get('view')->render('riProduct::backend/_preview_hidden_fields.php');
                break;
            case "ON_PRODUCT_INFO_PROCESS_END":
                global $languages, $products_id;

                $tabs = \plugins\riPlugin\Plugin::get('settings')->get("riProduct.tabs");
                $fields = count($tabs) + 1;

                for ($i = 0, $n = sizeof($languages); $i <= $n; $i++) {
                    $language_id = $languages[$i]['id'];
                    $data = array();
                    for ($j = 1; $j < $fields; $j++) {
                        $data['tab_' . $j] = $_POST['tab'][$j][$i + 1];
                    }

                    zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $data, "update", 'products_id = ' . $products_id . " and language_id = " . (int) $language_id);
                }
                break;
        }
	}
}