<?php
global $languages;
foreach($_POST['tab'] as $keys => $item){
    foreach($item as $key => $value){
?>
    <tr>
        <td>
            <?php echo  $value;?>
        </td>
    </tr>
<?php
        
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
