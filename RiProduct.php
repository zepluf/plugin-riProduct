<?php
namespace plugins\riProduct;

use plugins\riCore\PluginCore;
use plugins\riPlugin\Plugin;

class RiProduct extends PluginCore{
    
    public function install(){
        global $db;
        $settings = Plugin::get('settings')->load("riProduct");

        $columns = Plugin::get('riCore.DatabasePatch')->getColumns(TABLE_PRODUCTS_DESCRIPTION);

        foreach($settings['tabs'] as $key => $value ){
            if(!in_array($key, $columns))
            $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION . " ADD " . $key ." " .$value['type']);
        }
        
        return true;
    }

    public function uninstall(){
        global $db;
        $settings = Plugin::get('settings')->load("riProduct");

        $columns = Plugin::get('riCore.DatabasePatch')->getColumns(TABLE_PRODUCTS_DESCRIPTION);

        foreach($settings['tabs'] as $key => $value ){
            if(in_array($key, $columns))
                $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION . " DROP COLUMN " . $key);
        }

        return true;
    }

    public function init(){
        
        //Plugin::get('templating.holder')->add("FORM_ADD_INFO_PRODUCT", Plugin::get('View')->render("riProduct::backend/_addform.php"));
        if(IS_ADMIN_FLAG)
            if(basename($_SERVER["SCRIPT_FILENAME"]) == 'product.php'){
                global $autoLoadConfig;               
                $autoLoadConfig[200][] = array('autoType' => 'include', 'loadFile' => __DIR__ . '/lib/observers.php');
            }
    }
}