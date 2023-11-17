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
$conStr = 'connection';

// TODO: Comment
$_SESSION[$isSet] = False;

if ((!array_key_exists($isSet, $_SESSION)) or (!$_SESSION[$isSet])) {
    //echo "Re-defining Globals".PHP_EOL;
    // DOCUMENT_ROOT=/home/u230048523/domains/mydailynutrition.site/public_html\nServerName=mydailynutrition.site\nServerADDR=195.35.38.72\nREMOTE_ADDR=199.203.186.208\nREMOTE_HOST=/home/u230048523/domains/mydailynutrition.site/public_html/updateDailyNutValues.php\n
//    echo "DOCUMENT_ROOT=".$_SERVER['DOCUMENT_ROOT'].'\n';
//    echo "ServerName=".$_SERVER['SERVER_NAME'].'\n';
//    echo "ServerADDR=".$_SERVER['SERVER_ADDR'].'\n';
//    echo "REMOTE_ADDR=".$_SERVER['REMOTE_ADDR'].'\n';
//    echo "REMOTE_HOST=".$_SERVER['SCRIPT_FILENAME'].'\n';

    $server1 = $_SERVER['SERVER_NAME'];
    if (str_contains($server1,'mydailynutrition')) 
    {
        $_SESSION['host'] = '127.0.0.1';
        $_SESSION['username'] = 'u230048523_shay2';
        $_SESSION['password'] = 'MosheMoshe1!';
        $_SESSION['database'] = 'u230048523_nutrition'; //'u230048523_ajax_demo';
        $_SESSION['isLocal'] = False;
    }
    else
    {
        $_SESSION['host'] = 'localhost';
        $_SESSION['username'] = 'root';
        $_SESSION['password'] = '';
        $_SESSION['database'] = 'nutrition_app';
        $_SESSION['isLocal'] = True;
    }

    // Establish DB Connection to read tables
    if (array_key_exists($conStr,$_SESSION) and !$_SESSION[$conStr] and mysqli_ping($_SESSION[$conStr]))
    {
        $con = $_SESSION[$conStr];
    }
    else
    {
        $con = mysqli_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
        if (!$con) {
            die('Could not connect: ' . mysqli_error($con));
        }
        mysqli_select_db($con,$_SESSION['database']);
        $_SESSION[$conStr] = $con;
    }

    $_SESSION[$isSet] = True;
}


