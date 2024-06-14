<?php
// Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user
// db_items_nut,

$q = $_GET['q'];
//$q = 'מלפפון';

$isFull = $_GET['isFull'];

$isStarCharInStr = $_GET['isStarCharInStr'];

include 'globals.php';
//$con = mysqli_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

mysqli_select_db($con,$_SESSION['database']);
if ($isFull == '1') {
//    $sql = "SELECT itemName,Calories FROM `db_items_nut` WHERE itemName=='" . $q."';";
//    $result = mysqli_query($con, $sql);
//    $list = mysqli_fetch_array($result);
//    if (count($list) > 1)
//    {
//        echo 'ErrMoreThanOneItemSelected';
//    }
//    else
//    {
//        echo join(",",$list[0]);
//    }
}
else {
    //$sql = "SELECT itemName,Energy FROM `db_items_nut` WHERE itemName LIKE '" . $q . "%';";
    if ($isStarCharInStr=='true')
    {
        //echo 'true';
        $sql = "SELECT itemName,_energy, itemUID FROM `table_items_data` WHERE itemName REGEXP '\\\\b{$q}';";
    }
    else
    {
        //echo 'false';
        $sql = "SELECT itemName,_energy, itemUID FROM `table_items_data` WHERE itemName REGEXP '\\\\b{$q}' AND isExtended=0;";        
    }
    $result = mysqli_query($con, $sql);

    while ($row = mysqli_fetch_array($result)) {
        echo $row[0] . ',' . $row[1] . "," . $row[2] . ';';
    }

}

//mysqli_close($con);
?>
