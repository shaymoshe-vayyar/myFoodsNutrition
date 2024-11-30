<?php

function is_session_started() {
    if (php_sapi_name() === "cgi")
        return false;

    if (version_compare(phpversion(), '5.4.0', '>='))
        return session_status() === PHP_SESSION_ACTIVE;

    return session_id() !== '';
}

function readDictTable($tableName, $con) {
    // Load $tableName
    $result = mysqli_query($con, "SELECT * FROM {$tableName};");
    $dict = [];
    while ($row = mysqli_fetch_array($result)) {
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

// Placeholder for conversion classes (you'll need to implement these)
class GramConversionTable {

    public const GRAM = 1;
    public const MILI_GRAM = 1e-3;
    public const MICRO_GRAM = 1e-6;
    public const K_GRAM = 1e3;
    public const TSPN = 4.2;

    // Method to get conversion rate
    public static function getConversionRate(string $unit): float {
        return match ($unit) {
            'gram' => self::GRAM,
            'miliGram' => self::MILI_GRAM,
            'microGram' => self::MICRO_GRAM,
            'kGram' => self::K_GRAM,
            'tspn' => self::TSPN,
            default => -1 // Default to 1 if unit not found
        };
    }
}

class HandleConversion {

    private $engHebDict;
    private $HebEngDict;
    private $nutUnitsToDisplayDict;

    public function __construct($my_con) {
        $this->LoadTermTranslationTables($my_con);
    }

    public function dictHebNameToEngName($hebName) {
        // Implement Hebrew to English name conversion
        // You'll need to create a mapping dictionary
//        foreach ($this->HebEngDict as $item) {
//            echo($item[0].',');
////            echo($item[1]);
//        }
//        echo('---------------------------');
//        echo($hebName);
        return $this->HebEngDict[$hebName] ?? $hebName;
    }

    public function dictEngNameToHebName($engName) {
        // Implement English to Hebrew name conversion

        return $this->engHebDict[$engName] ?? $engName;
    }

    /**
     * Convert unit to standard (gram)
     * 
     * @param string $orgValueStr Original value as string
     * @param string $orgUnits Original units 
     * @return array [converted value, units]
     */
    public function convertUnitToStandard($orgValueStr, $orgUnits) {
        // Remove commas and convert to float
        $orgValueStr = str_replace(',', '', $orgValueStr);
        $value = floatval($orgValueStr);

        $newUnits = $orgUnits;
        // Convert to gram if conversion table contains the unit
        if (GramConversionTable::getConversionRate($orgUnits) > 0) {
            $value *= GramConversionTable::getConversionRate($orgUnits);
            $newUnits = 'gram';
        }

//        echo $orgValueStr . "\n" . $orgUnits . "\n" . $value . "\n" . $units . "\n";

        return [$value, $newUnits];
    }

    /**
     * Convert value from standard unit to specified unit
     * 
     * @param array $value [value, current units]
     * @param string $newUnit New units
     * @return array [converted value]
     */
    public function convertUnitFromStandard($value, $newUnitName) {
        $newValue = $value;

        // Convert back from gram if conversion table contains the unit
        if (GramConversionTable::getConversionRate($newUnitName) > 0) {
            $newValue /= 1e-9 + GramConversionTable::getConversionRate($newUnitName);
        }

        return $newValue;
    }

    public function convertNameValueToDisplay($nut_name, $nut_value) {
        $newUnit = $this->nutUnitsToDisplayDict[$nut_name];
        $newValue = $this->convertUnitFromStandard($nut_value, $newUnit);
        $newValue_disp = round($newValue, 1);

//        if (array_key_exists($unitToDisp, $_SESSION['nutWeightUnitsToStandardDict'])) {
//            $newValue = $nut_value / (1e-9 + floatval($_SESSION['nutWeightUnitsToStandardDict'][$unitToDisp]));
//            $newValue = round($nutValueUnitConv, 1);
//        } else {
//            $newValue = $nut_value;
//        }
        $newUnit_disp = $this->dictEngNameToHebName($newUnit);
        return [$newValue_disp, $newUnit_disp];
    }

    private function LoadTermTranslationTables($my_con) {
        $table_name = 'table_nutrition_attribute';

        // Load rows from the database      
        $result = mysqli_query($my_con, "SELECT nutritionName,nutritionDGoal,nutritionDisplayUnits,hebrewDisplayName,isDisplayed FROM {$table_name};");
        while ($row = mysqli_fetch_array($result)) {
//            $_SESSION['dailyNutritionGoalsDict'][$row[0]] = $row[1];
            $this->nutUnitsToDisplayDict['_'.$row[0]] = $row[2];
            $this->engHebDict[$row[0]] = $row[3];
        }
        // Additional manual entries
        $additional_entries = [
            'gram' => 'גרם',
            'miliGram' => 'מ"ג',
            'microGram' => 'מק"ג',
            'tspn' => 'כפיות סוכר',
            'calories' => 'קלוריות'
        ];
        // Merge additional entries
        $this->engHebDict = array_merge($this->engHebDict, $additional_entries);

        // Generate the Hebrew to English dictionary
        $this->HebEngDict = [];
        foreach ($this->engHebDict as $key => $value) {
            $this->HebEngDict[$value] = $key;
        }

//        // Load $tableName
//        $result = mysqli_query($my_con,"SELECT nutritionName, hebrewDisplayName FROM {$table_name};");
//        $keys_values = [];
//        while($row = mysqli_fetch_array($result)){
//            $keys_values[$row[0]] = $row[1];
//        }
//        
//        // Additional manual entries
//        $additional_entries = [
//            'gram' => 'גרם',
//            'miliGram' => 'מ"ג',
//            'microGram' => 'מק"ג',
//            'tspn' => 'כפיות סוכר',
//            'calories' => 'קלוריות'
//        ];
//
//        // Merge additional entries
//        $keys_values = array_merge($keys_values, $additional_entries);
//
//        // Create English to Hebrew dictionary
//        // And Hebrew to English dictionary
//        $this->engHebDict = [];
//        $this->HebEngDict = [];
//        foreach ($keys_values as $key => $value) {
//            $this->engHebDict[$key] = $value;
//            $this->HebEngDict[$value] = $key;
//        }
        // Load Conversion Tables
    }
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
    if (str_contains($server1, 'mydailynutrition')) {
        $_SESSION['host'] = '127.0.0.1';
        $_SESSION['username'] = 'u230048523_shay2';
        $_SESSION['password'] = 'MosheMoshe1!';
        $_SESSION['database'] = 'u230048523_nutrition'; //'u230048523_ajax_demo';
        $_SESSION['isLocal'] = False;
    } else {
        $_SESSION['host'] = 'localhost';
        $_SESSION['username'] = 'root';
        $_SESSION['password'] = '';
        $_SESSION['database'] = 'nutrition_app';
        $_SESSION['isLocal'] = True;
    }

    // Establish DB Connection to read tables
    if (array_key_exists($conStr, $_SESSION) and !$_SESSION[$conStr] and mysqli_ping($_SESSION[$conStr])) {
        $con = $_SESSION[$conStr];
    } else {
        $con = mysqli_connect($_SESSION['host'], $_SESSION['username'], $_SESSION['password']);
        if (!$con) {
            die('Could not connect: ' . mysqli_error($con));
        }
        mysqli_select_db($con, $_SESSION['database']);
        $_SESSION[$conStr] = $con;
    }

    $_SESSION['HandleConversion'] = new HandleConversion($con);

    $_SESSION[$isSet] = True;
}


