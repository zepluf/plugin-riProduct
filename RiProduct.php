<?php
namespace plugins\riProduct;

use plugins\riCore\PluginCore;
use plugins\riPlugin\Plugin;

class RiProduct extends PluginCore{
    
    public function install(){
        
        global $db;
        $tabs = Plugin::get('settings')->load("riProduct"); 
        $tabs = $tabs['add_tabs'];
        
        $after = $tabs['after'];
        foreach($tabs['tabs'] as $key => $value ){
            $db->Execute("ALTER TABLE " . TABLE_PRODUCTS_DESCRIPTION . " ADD " . $key ." " .$value['type'] . " AFTER ". $after);
        }
        
        return true;
    }
    
    public function uninstall(){
        //return Plugin::get('riCore.DatabasePatch')->executeSqlFile(file(__DIR__ . '/install/sql/uninstall.sql'));
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