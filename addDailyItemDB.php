<?php
// Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user
// db_daily_items
//   CREATE TABLE `ajax_demo`.`db_daily_items` (`date` DATE NOT NULL , `itemName` VARCHAR(255) NOT NULL , `quantity` INT NOT NULL , `mealTimeSlot` VARCHAR(255) NOT NULL , `time` TIME NOT NULL ) ENGINE = InnoDB;
//   ALTER TABLE `db_daily_items` ADD INDEX(`date`);

$itemName = $_GET['item'];
//$itemName = 'מלפפון';
$date = $_GET['date'];
$quantity = $_GET['quantity'];
$mealTimeSlot = $_GET['mealTimeSlot'];
//$time = $_GET['time'];
date_default_timezone_set("Asia/Jerusalem");
$time = date('H:i',strtotime("now"));
if (strlen($mealTimeSlot)==0)
{
    $mealTimeSlot = "ארוחת ביניים"; // Default
    $timeAsClass = DateTime::createFromFormat('H:i', $time);
    $timeMorningS = DateTime::createFromFormat('H:i', "6:00");
    $timeMorningE = DateTime::createFromFormat('H:i', "12:00");
    if ($timeAsClass > $timeMorningS && $timeAsClass < $timeMorningE)
    {
        $mealTimeSlot = "ארוחת בוקר";
    }
    else
    {
        $timeLaunchS = DateTime::createFromFormat('H:i', "12:00");
        $timeLaunchE = DateTime::createFromFormat('H:i', "15:30");
        if ($timeAsClass > $timeLaunchS && $timeAsClass < $timeLaunchE)
        {
            $mealTimeSlot = "ארוחת צהריים";
        }
        else
        {
            $timeAfterLaunchS = DateTime::createFromFormat('H:i', "15:30");
            $timeAfterLaunchE = DateTime::createFromFormat('H:i', "18:30");
            if ($timeAsClass > $timeAfterLaunchS && $timeAsClass < $timeAfterLaunchE)
            {
                $mealTimeSlot = "ארוחת אחהצ";
            }
            else
            {
                $timeEveningS = DateTime::createFromFormat('H:i', "18:30");
                $timeEveningE = DateTime::createFromFormat('H:i', "22:30");
                if ($timeAsClass > $timeEveningS && $timeAsClass < $timeEveningE)
                {
                    $mealTimeSlot = "ארוחת ערב";
                }
                else
                {
                    $timeNightS = DateTime::createFromFormat('H:i', "22:30");
                    $timeNightE = DateTime::createFromFormat('H:i', "23:59");
                    if ($timeAsClass > $timeNightS && $timeAsClass < $timeNightE)
                    {
                        $mealTimeSlot = "ארוחת לילה";
                    }
                }
            }
        }
    }
}

include 'globals.php';
$con = mysqli_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);

if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

mysqli_select_db($con,$_SESSION['database']);
$sql = "INSERT INTO db_daily_items (`date`, `itemName`, `quantity`, `mealTimeSlot`, `time`) VALUES ('".$date."', '".$itemName."', ".$quantity.", '".$mealTimeSlot."', '".$time."');";
printf("SQL query = %s\n",$sql);
$result = mysqli_query($con, $sql);

if ($result)
{
    printf("New record has ID %d.\n", mysqli_insert_id($con));
}
else
{
    printf("Error creating record.\n");
}

mysqli_close($con);
?>
