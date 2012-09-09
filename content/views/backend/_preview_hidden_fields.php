<?php
//echo "<pre>";var_dump($_POST);die("sss");

foreach($_POST['tab'] as $keys => $item){
    foreach($item as $key => $value)
    echo zen_draw_hidden_field("tab[".$keys."]"."[".$key."]", $value);
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
