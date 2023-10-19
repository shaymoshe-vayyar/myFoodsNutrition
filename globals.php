<?php
function is_session_started()
{
    if (php_sapi_name() === "cgi")
        return false;

    if (version_compare(phpversion(), '5.4.0', '>='))
        return session_status() === PHP_SESSION_ACTIVE;

    return session_id() !== '';
}
function readDictTable($tableName, $con)
{
    // Load $tableName
    $result = mysqli_query($con,"SELECT * FROM {$tableName};");
    $dict = [];
    while($row = mysqli_fetch_array($result)){
        $dict[$row[0]] = $row[1];
    }
//     echo $tableName.PHP_EOL;
//        foreach($dict as $key=>$value) {
//            // your code here
//            echo "{$key}={$value}\n";
//        }
//    echo "--------------------------------------------------------";
    return $dict;
}
$session_name = "DailyFoodsSessionID";
if (!is_session_started()) {
    session_name($session_name);
    //    session_set_cookie_params($cookie_options);
    session_start();
}
$isSet = 'isSet';

// TODO: Comment
$_SESSION[$isSet] = False;

if ((!array_key_exists($isSet, $_SESSION)) or (!$_SESSION[$isSet])) {
    //echo "Re-defining Globals".PHP_EOL;

    $server1 = $_SERVER['SERVER_NAME'];
    if (str_contains($server1,'PhpStorm')) // ($host == 'pc')
    {
        $_SESSION['host'] = 'localhost';
        $_SESSION['username'] = 'root';
        $_SESSION['password'] = '';
        $_SESSION['database'] = 'ajax_demo';
        $_SESSION['isLocal'] = True;
    }
    else
    {
        $_SESSION['host'] = '127.0.0.1';
        $_SESSION['username'] = 'u230048523_shay';
        $_SESSION['password'] = 'MosheMoshe1';
        $_SESSION['database'] = 'u230048523_ajax_demo';
        $_SESSION['isLocal'] = False;
    }

    // Establish DB Connection to read tables
    $con = mysqli_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
    if (!$con) {
        die('Could not connect: ' . mysqli_error($con));
    }
    mysqli_select_db($con,$_SESSION['database']);

    // Load Conversion Tables
    $_SESSION['engNameToHebDict'] = readDictTable('conversion_eng_name_to_heb', $con);
    $_SESSION['nutUnitsToDisplayDict'] = readDictTable('conversion_nut_units_to_display', $con);
    $_SESSION['nutWeightUnitsToStandardDict'] = readDictTable('conversion_units_to_standard', $con);


    $_SESSION[$isSet] = True;
}


