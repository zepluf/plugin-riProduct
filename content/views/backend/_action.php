<?php

global $languages, $products_id;

$tabs = \plugins\riPlugin\Plugin::get('settings')->get("riProduct.add_tabs.tabs");
$fields = count($tabs) + 1;

for ($i = 0, $n = sizeof($languages); $i <= $n; $i++) {
    $language_id = $languages[$i]['id'];
    $data = array();
    for ($j = 1; $j < $fields; $j++) {
        $data['tab_' . $j] = $_POST['tab'][$j][$i + 1];
    }

    zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $data, "update", 'products_id = ' . $products_id . " and language_id = " . (int) $language_id);
}
?>
