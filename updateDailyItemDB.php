<?php
// Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user
// db_daily_items
//   CREATE TABLE `ajax_demo`.`db_daily_items` (`date` DATE NOT NULL , `itemName` VARCHAR(255) NOT NULL , `quantity` INT NOT NULL , `mealTimeSlot` VARCHAR(255) NOT NULL , `time` TIME NOT NULL ) ENGINE = InnoDB;
//   ALTER TABLE `db_daily_items` ADD INDEX(`date`);

$itemIndex = $_GET['itemIndex'];
$newQuantity = $_GET['newQuantity'];

include 'globals.php';
//$con = mysqli_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);

if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

mysqli_select_db($con,$_SESSION['database']);
$sql = "UPDATE table_daily_items SET quantity={$newQuantity} WHERE UID={$itemIndex};";
printf("SQL query = %s\n",$sql);
$result = mysqli_query($con, $sql);

if ($result)
{
//    printf("New record has ID %d.\n", mysqli_insert_id($con));
}
else
{
    printf("Error creating record.\n");
}

mysqli_close($con);
?>
