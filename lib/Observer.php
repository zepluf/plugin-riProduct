<?php
namespace plugins\riProduct;
use plugins\riPlugin\Plugin;

class Observer extends \base
{
	function __construct(){
		global $zco_notifier;
		
		$zco_notifier->attach($this,array('FORM_ADD_INFO_PRODUCT','ON_PRODUCT_INFO_FORM_PROCESS_END','PRIVIEW_PRODUCT','ON_PRODUCT_PREVIEW_INFO_FORM_END'));
                
                
	}
	
	function update($callingClass, $notifier, $paramsArray)
	{
            
            if(strcmp($notifier, 'FORM_ADD_INFO_PRODUCT') == 0){
                echo Plugin::get('view')->render('riProduct::backend/_addform.php');
            }elseif(strcmp($notifier, 'ON_PRODUCT_PREVIEW_INFO_FORM_END') == 0  ){
                echo Plugin::get('view')->render('riProduct::backend/_addFieldHidden.php');
            }elseif(strcmp($notifier, 'PRIVIEW_PRODUCT') == 0){
                echo Plugin::get('view')->render('riProduct::backend/_priviewProduct.php');
            }else{
                echo Plugin::get('view')->render('riProduct::backend/_action.php');
            }
                
	}
}