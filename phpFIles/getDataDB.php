<?php
// Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user
$host = 'pc';
//$host = 'web';
if ($host == 'pc')
{
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'ajax_demo';
}
else
{
    $host = '127.0.0.1';
    $username = 'u230048523_shay';
    $password = 'MosheMoshe1';
    $database = "u230048523_ajax_demo";
}

$q = $_GET['q'].'%';
//$q = 'מלפפון';

$con = mysqli_connect($host,$username,$password);
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

mysqli_select_db($con,$database);
$sql="SELECT itemName,Calories FROM `db_items_nut` WHERE itemName LIKE '".$q."';";
//$sql="SELECT * FROM user WHERE id = '".$q."'";
$result = mysqli_query($con,$sql);

//echo "<tr>data: ";
//while($row = mysqli_fetch_array($result)) {
//    echo "<td>" . $row[0] . "</td>";
//}
//echo "</tr>";
while($row = mysqli_fetch_array($result)) {
//    echo "<option>" . $row[0]."[".$row[1]."]"."</option>";
    echo $row[0].','.$row[1].';';
}

mysqli_close($con);
?>
