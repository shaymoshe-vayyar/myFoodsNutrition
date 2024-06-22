<?php
// Link to MySql DB Management: http://localhost:12345/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user
// Inputs: flag_is_store - whether to store the data
//         name_to_store
// 

$str_arr_data = file_get_contents('php://input');
$name_of_recipe = $_GET['name_of_recipe'];
$flag_is_update_db = $_GET['flag_is_update_db'];

include 'globals.php';
$con = mysqli_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}
mysqli_select_db($con,$_SESSION['database']);

$arr_data = json_decode($str_arr_data,true);
$arr_total_nut_value = array();
$total_q = 0;
foreach ($arr_data as $item_data)
{
    $uid = $item_data["itemUID"];
    $numDesiredQuantity = floatval($item_data["numDesiredQuantity"]);
    $total_q += $numDesiredQuantity;
    // Fetch Nut Values Columns for current item
//    echo "udi={$uid},n={$numDesiredQuantity}\n";
    $sql = "SELECT * FROM `table_items_data` WHERE itemUID={$uid};";        
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result); // Only one match for UID
    foreach ($row as $item_prop_name => $prop_value)
    {
        $prop_value_float = floatval($prop_value);
        if ($prop_value_float < 0) // Zero negative values which indicates value does not exist
        {
            $prop_value_float = 0;
        }
        if (str_starts_with($item_prop_name,'_')) {
            $nutrition_name = substr($item_prop_name, 1);
            if (key_exists($nutrition_name,$arr_total_nut_value))
            {
                $arr_total_nut_value[$nutrition_name] = $arr_total_nut_value[$nutrition_name] + $prop_value_float * $numDesiredQuantity / 100;
            }
            else
            {
                $arr_total_nut_value[$nutrition_name] = $prop_value_float * $numDesiredQuantity / 100;
            }
        }        
    }
}

$arr_total_nut_value_per_100 = array();
foreach ($arr_total_nut_value as $nutrition_name => $prop_value)
{
    $arr_total_nut_value_per_100[$nutrition_name] = $prop_value/$total_q*100;
}

function get_column_name_sql_str($name) {
    return $name.", ";
}

// Save to DB if asked
if ($flag_is_update_db)
{
    $str_column_names = "";
    $str_column_values = "";
    // itemUID is auto gen -> no need to pass
    // itemName
    $str_column_names = $str_column_names.get_column_name_sql_str("itemName");
    $str_column_values = $str_column_values."'{$name_of_recipe}', ";
    // additionalNames
    $str_column_names = $str_column_names.get_column_name_sql_str("additionalNames");
    $str_column_values = $str_column_values."'', ";
    // categoryType
    $str_column_names = $str_column_names.get_column_name_sql_str("categoryType");
    $str_column_values = $str_column_values."'general', ";
    // isExtended
    $str_column_names = $str_column_names.get_column_name_sql_str("isExtended");
    $str_column_values = $str_column_values."'1', ";
    // itemsCombination
    $str_column_names = $str_column_names.get_column_name_sql_str("itemsCombination");
    $str_column_values = $str_column_values."'".$str_arr_data."', ";

    foreach ($arr_total_nut_value_per_100 as $nutrition_name => $prop_value)
    {
        $str_column_names = $str_column_names.get_column_name_sql_str("_{$nutrition_name}");
        $str_column_values = $str_column_values."'{$prop_value}', ";
    }
    // Remove last ', '
    $str_column_names = substr($str_column_names, 0, -2);
    $str_column_values = substr($str_column_values, 0, -2);

//    echo $str_column_names."\n";
//    echo $str_column_values;
    //$sql_str = "INSERT INTO table_daily_items (`itmDate`, `itemName`, `quantity`, `mealTimeSlot`, `itmTime`) VALUES ('".$date."', '".$itemName."', ".$quantity.", '".$mealTimeSlot."', '".$time."');";
    $sql_str = "INSERT INTO table_items_data ({$str_column_names}) VALUES ({$str_column_values});";
//    echo $sql_str."\n";
    $result = mysqli_query($con, $sql_str);
//    echo "\n\n";
    $sql_str = "SELECT LAST_INSERT_ID();";
    $result = mysqli_query($con, $sql_str);
    $row = mysqli_fetch_array($result);
//    echo $row[0]."\n";
    
    $strText1 = "'{$name_of_recipe}'";
    echo '<div class="w3-row" style="background-color: grey;" >
        <i class="fa fa-times w3-large w3-cell w3-left" style="padding: 5px" onclick="removeItemFromList('.$row[0].','.$strText1.')"></i>
        <p class="w3-cell w3-large w3-right">'.$name_of_recipe.' </p> 
        </div>';
}



// Load Conversion Tables
$_SESSION['dailyNutritionGoalsDict'] = [];
$result = mysqli_query($con,"SELECT nutritionName,nutritionDGoal,nutritionDisplayUnits,hebrewDisplayName,isDisplayed FROM table_nutrition_attribute;");
while($row = mysqli_fetch_array($result)){
    $_SESSION['dailyNutritionGoalsDict'][$row[0]] = $row[1];
    $_SESSION['nutUnitsToDisplayDict'][$row[0]] = $row[2];
    $_SESSION['engNameToHebDict'][$row[0]] = $row[3];
}
// TODO: Move to eng. to heb. translator
$_SESSION['engNameToHebDict']['gram'] = 'גרם';
$_SESSION['engNameToHebDict']['miliGram'] = 'מ"ג';
$_SESSION['engNameToHebDict']['microGram'] = 'מק"ג';
$_SESSION['engNameToHebDict']['tspn'] = 'כפיות סוכר';
$_SESSION['engNameToHebDict']['calories'] = 'קלוריות';

// TODO: Move to dedicated table
$_SESSION['nutWeightUnitsToStandardDict'] = [
    "gram" => 1,
    "miliGram" => 0.001,
    "microGram" => 1e-06,
    "tspn" => 4.2,
];


$arrColsNamesToDisplay = [];
$arrNutValuesToDisplay = [];
$arrNutUnitsToDisplay = [];
foreach ($arr_total_nut_value_per_100 as $engNutName => $val)
{
    // Convert Nut Names
    //echo "engNutName={$engNutName}\n";
    array_push($arrColsNamesToDisplay,
        $_SESSION['engNameToHebDict'][$engNutName]);

    // Convert And Translate Units Values For displaying
    $nutValue = $val;
    $unitToDisp = $_SESSION['nutUnitsToDisplayDict'][$engNutName];
    if (array_key_exists($unitToDisp, $_SESSION['nutWeightUnitsToStandardDict']))
    {
        $nutValueUnitConv = $nutValue / (1e-9 + floatval($_SESSION['nutWeightUnitsToStandardDict'][$unitToDisp]));
        $nutValueUnitConv = round($nutValueUnitConv, 1);

    }
    else
    {
        $nutValueUnitConv = round($nutValue,1);
    }
    array_push($arrNutValuesToDisplay,
        $nutValueUnitConv);
    array_push($arrNutUnitsToDisplay,
        $_SESSION['engNameToHebDict'][$unitToDisp]);
}
//var_dump($arrNutValuesToDisplay);

//echo "סהכ"."{$total_q} גרם\n";
echo '<table id="tableNutValues" 
    data-totalquan = "'.$total_q.'"
        dir="rtl"  width="100%"'; //justify-self: center; !important"

echo '<tbody><tr><th style="text-align: center;width: 160px;height: 26px;">סימון תזונתי</th><th style="text-align: center;width: 130px;height: 26px;">ערך</th></tr>';
//echo '<tr><td>קלוריות (אנרגיה)</td><td><span>1,819</span></td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="">100%</div></div></td></tr><tr><td>חלבונים (גרם)</td><td><span>98.28</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 86%;"></div><div class="percent-text" style="" title="">86%</div></div></td></tr><tr><td>פחמימות (גרם)</td><td><span>248</span></td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="">121%</div></div></td></tr><tr><td>מתוכן סוכרים (גרם)</td><td><span>73.15</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>שומנים (גרם)</td><td><span>59</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 97%;"></div><div class="percent-text" style="" title="">97%</div></div></td></tr><tr><td>מתוכם שומן רווי (גרם)</td><td><span>7.26</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>מתוכם שומן טראנס (גרם)</td><td>0.03</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>כולסטרול (מ"ג)</td><td><span>86.85</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>נתרן (מ"ג)</td><td>1,005</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 67%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור נתרן הינו 1500 מ&quot;ג">67%</div></div></td></tr><tr><td>סיבים תזונתיים (גרם)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td><span>56.73</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור סיבים תזונתיים הינו 38 גרם">149%</div></div></td></tr><tr><td>מים (גרם)</td><td>1,709</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>ליקופן (מ"ג)</td><td>4.32</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>ויטמין A (מק"ג)</td><td>1,520</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ויטמין A הינו 900 מק&quot;ג">169%</div></div></td></tr><tr><td>ויטמין B (גרם)</td><td>34.85</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>ויטמין B1 (מ"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>1.62</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור ויטמין B1 הינו 1 מ&quot;ג">135%</div></div></td></tr><tr><td>ויטמין B2 (מ"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>1.61</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור ויטמין B2 הינו 1 מ&quot;ג">124%</div></div></td></tr><tr><td>ויטמין B3 (מ"ג)</td><td>21.76</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ויטמין B3 הינו 16 מ&quot;ג">136%</div></div></td></tr><tr><td>ויטמין B5 (מ"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>6.36</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור ויטמין B5 הינו 5 מ&quot;ג">127%</div></div></td></tr><tr><td>ויטמין B6 (מ"ג)</td><td>2.58</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ויטמין B6 הינו 1 מ&quot;ג">198%</div></div></td></tr><tr><td>חומצה פולית - ויטמין B9 (מק"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-exclamation-circle"></i>חריגה מהכמות המומלצת</div></td><td>921.3</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;background-color: #c61f1f !important;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור חומצה פולית - ויטמין B9 הינו 400 מק&quot;ג">230%</div></div></td></tr><tr><td>ויטמין B12 (מק"ג)</td><td>1.36</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 57%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור ויטמין B12 הינו 2 מק&quot;ג">57%</div></div></td></tr><tr><td>ויטמין C (מ"ג)</td><td>419.71</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ויטמין C הינו 90 מ&quot;ג">466%</div></div></td></tr><tr><td>ויטמין D (מק"ג)</td><td>2.67</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 53%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור ויטמין D הינו 5 מק&quot;ג">53%</div></div></td></tr><tr><td>ויטמין E (מ"ג)</td><td>11.79</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 79%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור ויטמין E הינו 15 מ&quot;ג">79%</div></div></td></tr><tr><td>ויטמין K (מק"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>1,012</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור ויטמין K הינו 120 מק&quot;ג">844%</div></div></td></tr><tr><td>סידן (מ"ג)</td><td>1,048</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור סידן הינו 1000 מ&quot;ג">105%</div></div></td></tr><tr><td>ברזל (מ"ג)</td><td>21.45</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ברזל הינו 8 מ&quot;ג">268%</div></div></td></tr><tr><td>מגנזיום (מ"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>571.41</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור מגנזיום הינו 420 מ&quot;ג">136%</div></div></td></tr><tr><td>זרחן (מ"ג)</td><td>1,591</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור זרחן הינו 700 מ&quot;ג">227%</div></div></td></tr><tr><td>אבץ (מ"ג)</td><td>12.53</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור אבץ הינו 11 מ&quot;ג">114%</div></div></td></tr><tr><td>אשלגן (מ"ג)</td><td>5,354</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור אשלגן הינו 4700 מ&quot;ג">114%</div></div></td></tr><tr><td>חומצות שומן רב בלתי רוויות (גרם)</td><td>17.71</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>חומצה אלפא לינולנית-אומגה 3 (גרם)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>2.15</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור חומצה אלפא לינולנית-אומגה 3 הינו 2 גרם">134%</div></div></td></tr><tr><td>חומצה לינולאית-אומגה 6 (גרם)</td><td>15.37</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 90%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור חומצה לינולאית-אומגה 6 הינו 17 גרם">90%</div></div></td></tr><tr><td>חומצות שומן חד בלתי רוויות (גרם)</td><td>19.76</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>חומצת שומן אולאית-אומגה 9 (גרם)</td><td>16.04</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr></tbody></table>';
for ($ii = 0; $ii < count($arrNutValuesToDisplay); $ii++) {
    
    echo '<tr><td>'.$arrColsNamesToDisplay[$ii].'</td><td><span>'.
            $arrNutValuesToDisplay[$ii]." ".$arrNutUnitsToDisplay[$ii].
            '</span></td></tr>';

}

mysqli_close($con);
?>
