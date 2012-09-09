<?php
//global $languages;
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
