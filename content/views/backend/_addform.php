<?php

global $languages;
$pd = \plugins\riPlugin\Plugin::get('riProduct.ProductsDescription');
$tabs = \plugins\riPlugin\Plugin::get('settings')->get("riProduct.tabs");
$fields = count($tabs) + 1;
//var_dump($fields);die("");
for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $language_id = $languages[$i]['id'];
    $id = isset($_GET['pID']) ? $_GET['pID'] : 0;
    $tabs = $pd->getTab($language_id, $_GET['pID']);
    
    for ($j = 1; $j < $fields; $j++) {
    $content_tab  =  (isset($tabs['tab_' . $j]))? $tabs['tab_' . $j]:"";
        ?>
        <tr>
            <td class="main" valign="top">
                tab <?php echo $j ?>
            </td>
            <td colspan="2" >
                <table>
                    <tr>
                        <td valign="top" >
                            <?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>
                        </td>
                        <td><?php
                            echo zen_draw_textarea_field('tab['.  $j .']['. $languages[$i]['id'] .']', 'soft', '100%', '5',$content_tab, ENT_COMPAT, CHARSET, TRUE);
                            ?>
                            <!--
                            <textarea name="tab[<?php echo $j ?>][<?php echo $languages[$i]['id'] ?>]" cols="100" rows="5">
                                <?php if (isset($tabs['tab_' . $j])) echo $tabs['tab_' . $j]; ?>
                            </textarea>
                            -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php
    }
}
?>

