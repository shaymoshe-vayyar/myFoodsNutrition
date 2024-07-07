<?php
// Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user
// table_items_data

$q = $_POST['query'];
$MAX_NUMBER_OF_ITEMS = 12;

//echo "-------\n";

$res_obj = [
    'query' => $q,
    'error' => '', // Default is no error
    'isStarCharInStr' => false,
    'n_items_found' => 0,
    'items' => array(),
    'query_txt_only' => '',
    ];

// Check star
if (str_contains($q, '*'))
{
    $res_obj['isStarCharInStr'] = true;
    $q = str_replace('*', '', $q);
}

// remove gram
$q = str_replace('גרם', '', $q);

// Finding words
//engWordsInStr = item_str.match(/\b[^\d\W]+\b/g);
//hebWordsInStr = item_str.match(/[\u0590-\u05FF]+/g);

// find number(s) in query
preg_match_all('/\d+\.?\d?/',$q,$numbers_match_arr);
//var_dump($numbers_match_arr[0]);
if (count($numbers_match_arr[0]) > 1)
{
    // error
    $res_obj['error'] = 'too many numbers!';
    echo json_encode($res_obj);
    return;
}
if (count($numbers_match_arr[0]) == 0)
{
    $res_obj['number_in_result'] = 0;
}
else if (count($numbers_match_arr[0]) == 1)
{
    $res_obj['number_in_result'] = 1;
    $res_obj['required_quantity'] = floatval($numbers_match_arr[0][0]);
    $q = str_replace($numbers_match_arr[0][0],'',$q);
}

// replace multiple spaces/tabs/line breaks in single space
$q = preg_replace('/\s+/', ' ', $q);
// Trim
$q = trim($q);
if (strlen($q) == 0)
{
    echo json_encode($res_obj);
    return;
}
$res_obj['query_txt_only'] = $q;
//echo $q."\n";



// Connect to sql DB and find item(s)
include 'globals.php';
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}
mysqli_select_db($con,$_SESSION['database']);
if ($res_obj['isStarCharInStr'] == true)
{
    $sql = "SELECT itemName,_energy, itemUID FROM `table_items_data` WHERE itemName REGEXP '\\\\b{$q}';";
}
else
{
    $sql = "SELECT itemName,_energy, itemUID FROM `table_items_data` WHERE itemName REGEXP '\\\\b{$q}' AND isExtended=0;";        
}
$result = mysqli_query($con, $sql);
$num_items = 0;
while ($row = mysqli_fetch_array($result)) {
    if ($num_items <= $MAX_NUMBER_OF_ITEMS)
    {
        array_push($res_obj['items'],$row);
    }
    $num_items = $num_items + 1;
}
if (($num_items == 0) and $res_obj['isStarCharInStr'] == false) // no item found and search was narrow -> check again with extended
{
    $sql = "SELECT itemName,_energy, itemUID FROM `table_items_data` WHERE itemName REGEXP '\\\\b{$q}';";
    $result = mysqli_query($con, $sql);
    $num_items = 0;
    while ($row = mysqli_fetch_array($result)) {
        if ($num_items <= $MAX_NUMBER_OF_ITEMS)
        {
            array_push($res_obj['items'],$row);
        }
        $num_items = $num_items + 1;
    }
}
$res_obj['n_items_found'] = $num_items;


echo json_encode($res_obj);

//echo $q."\n";
//echo $isStarCharInStr."\n";
//echo "-------\n";

//mysqli_close($con);
?>
