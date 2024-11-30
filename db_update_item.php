<?php

include 'globals.php';

$con = $_SESSION[$conStr];

$item_properties_data = json_decode(file_get_contents('php://input'),true);

$name_of_product = $item_properties_data['itemName'];

$list_column_names = array_keys($item_properties_data);
$list_column_values = array_values($item_properties_data);

$str_column_names = implode(', ', $list_column_names);
//var_dump($str_column_names);
$str_column_values = "'".implode("', '", $list_column_values)."'";
//var_dump($str_column_values);


$sql_str = "INSERT INTO table_items_data ({$str_column_names}) VALUES ({$str_column_values});";
//echo $sql_str."\n";
$result = mysqli_query($con, $sql_str);
//    echo "\n\n";
$sql_str = "SELECT LAST_INSERT_ID();";
$result = mysqli_query($con, $sql_str);
$row = mysqli_fetch_array($result);
//    echo $row[0]."\n";

$strText1 = "'{$name_of_product}'";
echo '<div class="w3-row" style="background-color: grey;" >
    <i class="fa fa-times w3-large w3-cell w3-left" style="padding: 5px" onclick="removeItemFromList('.$row[0].','.$strText1.')"></i>
    <p class="w3-cell w3-large w3-right">'.$name_of_product.' </p> 
    </div>';

