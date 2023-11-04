<?php
// Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user
// db_items_nut,

$date = $_GET['date'];
$displayType = $_GET['displayType'];

include 'globals.php';
$con = mysqli_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

mysqli_select_db($con,$_SESSION['database']);

// Fetch Nut Values Columns
$sqlCol = "SHOW COLUMNS FROM db_items_nut";
$resultCol = mysqli_query($con,$sqlCol);
$arrColsNames = [];
$arrNutValues = [];
while($row = mysqli_fetch_array($resultCol)){
    //echo $row['Field']."<br>";
    if (($row['Field'] != 'itemName') and (!str_starts_with($row['Field'],'_'))) {
        array_push($arrColsNames, $row['Field']);
        $arrNutValues[$row['Field']] = 0;
    }
}

//foreach ($arrColsNames as $colName)
//{
//    printf('%s, ',$colName);
//}


// Fetch daily items
$totalQuantityInGram = 0;
$sqlDailyItems = "SELECT itemName,quantity,mealTimeSlot,indexCol,time FROM `db_daily_items` WHERE date='" . $date."' ORDER BY indexCol DESC;";
$resultDailyItems = mysqli_query($con, $sqlDailyItems);
//echo '<ul dir="rtl">';
$prevMealState = '';
date_default_timezone_set("Asia/Jerusalem");
$lastTimeMark = DateTime::createFromFormat( 'H:i',date('H:i',strtotime('-90 minutes')) );
    while ($row = mysqli_fetch_array($resultDailyItems)) {
        $itemName = $row[0];
        $quantity = $row[1];
        $meal = $row[2];
        $indexCol = $row[3];
        $timeStamp = DateTime::createFromFormat('H:i:s', $row[4]);
        $sqlItemNut = "SELECT * FROM `db_items_nut` WHERE itemName='" . $itemName."';";
        $resultItemNut = mysqli_query($con, $sqlItemNut);
        $list = mysqli_fetch_array($resultItemNut,MYSQLI_NUM);
        //printf("item=%s, quantity=%d, meal=%s, count(list)=%d, list[0]=%s",$itemName, $quantity,$meal, count($list),$list[0]);
        //echo PHP_EOL;

        $totalQuantityInGram += $quantity;
        // $arrColsNames length is one less than $list as it does not include the 'itemName' column.
        for ($ii = 0; $ii < count($arrColsNames); $ii++) {
            if ($list[$ii]>0)
            {
                $arrNutValues[$arrColsNames[$ii]] += $list[$ii]*$quantity/100;
            }
            else
            {
                $arrNutValues[$arrColsNames[$ii]] += 0;
            }
        }
        if (($displayType == 'butFull') and ($prevMealState != $meal))
        {
                echo '<div class="w3-row" style="background-color: lightgrey;" >
                    <i class="w3-cell w3-xlarge w3-right">
                    '.$meal.'
                    </i>
                    </div>';
                $prevMealState = $meal;
        }
//        echo '<div>
//            '.$timeStamp->format('H:i:s').'  '.$lastTimeMark->format(' H:i:s').'
//            </div>';
        if (($displayType == 'butFull') or ($timeStamp > $lastTimeMark))
        {
                $strText1 = "'{$itemName} {$quantity} גרם '";
                $strText2 = "'{$itemName}'";
                echo '<div class="w3-row" style="background-color: grey;" >
                    <i class="fa fa-times w3-large w3-cell w3-left" style="padding: 5px" onclick="removeItemFromDaily('.$indexCol.','.$strText1.')"></i>
                    <i class="fa fa-edit w3-large w3-cell w3-left" style="padding: 5px" onclick="editItemFromDaily('.$indexCol.','.$strText2.','.$quantity.')"></i>
                    <p class="w3-cell w3-large w3-right">'.$itemName.' '.$quantity.' גרם</p> 
                    </div>';
        }
    }
    //echo '</ul>';
echo '<br><br>';

    $arrColsNamesToDisplay = [];
$arrNutValuesToDisplay = [];
$arrNutUnitsToDisplay = [];
foreach ($arrColsNames as $engNutName)
{
    // Convert Nut Names
    array_push($arrColsNamesToDisplay,
        $_SESSION['engNameToHebDict'][$engNutName]);

    // Convert And Translate Units Values For displaying
    $nutValue = $arrNutValues[$engNutName];
    $unitToDisp = $_SESSION['nutUnitsToDisplayDict'][$engNutName];
    if (array_key_exists($unitToDisp, $_SESSION['nutWeightUnitsToStandardDict']))
    {
        $nutValueUnitConv = $nutValue / (1e-9 + floatval($_SESSION['nutWeightUnitsToStandardDict'][$unitToDisp]));
        $nutValueUnitConv = round($nutValueUnitConv, 1);

    }
    else
    {
        $nutValueUnitConv = $nutValue;
    }
    array_push($arrNutValuesToDisplay,
        $nutValueUnitConv);
    array_push($arrNutUnitsToDisplay,
        $_SESSION['engNameToHebDict'][$unitToDisp]);
}

//echo '<table style="border:1; cellpadding:1; cellspacing:0; align:center; valign:top;" dir="rtl" class="nv-table text-center">';
//echo '<div style="display: grid; justify-content=center;">';
$strCalories = $arrNutValues['energy'];
echo '<table id="tableNutValues" data-totalcal="'.$strCalories.'" dir="rtl"  width="100%"'; //justify-self: center; !important"
echo '<tbody><tr><th style="text-align: center;width: 160px;height: 26px;">סימון תזונתי</th><th style="text-align: center;width: 130px;height: 26px;">סה"כ</th><th style="text-align: center;width: 130px;height: 26px;">אחוז מתוך הכמות המומלצת</th></tr>';
//echo '<tr><td>קלוריות (אנרגיה)</td><td><span>1,819</span></td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="">100%</div></div></td></tr><tr><td>חלבונים (גרם)</td><td><span>98.28</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 86%;"></div><div class="percent-text" style="" title="">86%</div></div></td></tr><tr><td>פחמימות (גרם)</td><td><span>248</span></td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="">121%</div></div></td></tr><tr><td>מתוכן סוכרים (גרם)</td><td><span>73.15</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>שומנים (גרם)</td><td><span>59</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 97%;"></div><div class="percent-text" style="" title="">97%</div></div></td></tr><tr><td>מתוכם שומן רווי (גרם)</td><td><span>7.26</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>מתוכם שומן טראנס (גרם)</td><td>0.03</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>כולסטרול (מ"ג)</td><td><span>86.85</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>נתרן (מ"ג)</td><td>1,005</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 67%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור נתרן הינו 1500 מ&quot;ג">67%</div></div></td></tr><tr><td>סיבים תזונתיים (גרם)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td><span>56.73</span> </td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור סיבים תזונתיים הינו 38 גרם">149%</div></div></td></tr><tr><td>מים (גרם)</td><td>1,709</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>ליקופן (מ"ג)</td><td>4.32</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>ויטמין A (מק"ג)</td><td>1,520</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ויטמין A הינו 900 מק&quot;ג">169%</div></div></td></tr><tr><td>ויטמין B (גרם)</td><td>34.85</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>ויטמין B1 (מ"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>1.62</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור ויטמין B1 הינו 1 מ&quot;ג">135%</div></div></td></tr><tr><td>ויטמין B2 (מ"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>1.61</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור ויטמין B2 הינו 1 מ&quot;ג">124%</div></div></td></tr><tr><td>ויטמין B3 (מ"ג)</td><td>21.76</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ויטמין B3 הינו 16 מ&quot;ג">136%</div></div></td></tr><tr><td>ויטמין B5 (מ"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>6.36</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור ויטמין B5 הינו 5 מ&quot;ג">127%</div></div></td></tr><tr><td>ויטמין B6 (מ"ג)</td><td>2.58</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ויטמין B6 הינו 1 מ&quot;ג">198%</div></div></td></tr><tr><td>חומצה פולית - ויטמין B9 (מק"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-exclamation-circle"></i>חריגה מהכמות המומלצת</div></td><td>921.3</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;background-color: #c61f1f !important;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור חומצה פולית - ויטמין B9 הינו 400 מק&quot;ג">230%</div></div></td></tr><tr><td>ויטמין B12 (מק"ג)</td><td>1.36</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 57%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור ויטמין B12 הינו 2 מק&quot;ג">57%</div></div></td></tr><tr><td>ויטמין C (מ"ג)</td><td>419.71</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ויטמין C הינו 90 מ&quot;ג">466%</div></div></td></tr><tr><td>ויטמין D (מק"ג)</td><td>2.67</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 53%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור ויטמין D הינו 5 מק&quot;ג">53%</div></div></td></tr><tr><td>ויטמין E (מ"ג)</td><td>11.79</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 79%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור ויטמין E הינו 15 מ&quot;ג">79%</div></div></td></tr><tr><td>ויטמין K (מק"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>1,012</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור ויטמין K הינו 120 מק&quot;ג">844%</div></div></td></tr><tr><td>סידן (מ"ג)</td><td>1,048</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור סידן הינו 1000 מ&quot;ג">105%</div></div></td></tr><tr><td>ברזל (מ"ג)</td><td>21.45</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור ברזל הינו 8 מ&quot;ג">268%</div></div></td></tr><tr><td>מגנזיום (מ"ג)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>571.41</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור מגנזיום הינו 420 מ&quot;ג">136%</div></div></td></tr><tr><td>זרחן (מ"ג)</td><td>1,591</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור זרחן הינו 700 מ&quot;ג">227%</div></div></td></tr><tr><td>אבץ (מ"ג)</td><td>12.53</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור אבץ הינו 11 מ&quot;ג">114%</div></div></td></tr><tr><td>אשלגן (מ"ג)</td><td>5,354</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="מינון מומלץ עבור אשלגן הינו 4700 מ&quot;ג">114%</div></div></td></tr><tr><td>חומצות שומן רב בלתי רוויות (גרם)</td><td>17.71</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>חומצה אלפא לינולנית-אומגה 3 (גרם)<div style="color: gray;font-size: .9em;"><i class="fas fa-info-circle"></i>אין רעילות לערך זה</div></td><td>2.15</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;top: 10px;" title="מינון מומלץ עבור חומצה אלפא לינולנית-אומגה 3 הינו 2 גרם">134%</div></div></td></tr><tr><td>חומצה לינולאית-אומגה 6 (גרם)</td><td>15.37</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 90%;"></div><div class="percent-text" style="" title="מינון מומלץ עבור חומצה לינולאית-אומגה 6 הינו 17 גרם">90%</div></div></td></tr><tr><td>חומצות שומן חד בלתי רוויות (גרם)</td><td>19.76</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr><tr><td>חומצת שומן אולאית-אומגה 9 (גרם)</td><td>16.04</td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-text" style="position: static;">לא ידוע</div></div></td></tr></tbody></table>';
for ($ii = 0; $ii < count($arrNutValues); $ii++) {
    $curDRIGoal = floatval($_SESSION['dailyNutritionGoalsDict'][$arrColsNames[$ii]]);
    //if ($curDRIGoal > 0):
    $percOfDRI = floatval($arrNutValues[$arrColsNames[$ii]])/$curDRIGoal*100;
    $percOfDRI = round($percOfDRI,0);
    $percOfDRIToDisplay = '---';
    if ($curDRIGoal >= 0) {
        $percOfDRIToDisplay = $percOfDRI.'%';
    }
//    echo "<br>".$arrColsNames[$ii]."=".$arrNutValues[$arrColsNames[$ii]]."</br>";
//    echo '<tr><td>'.$arrColsNamesToDisplay[$ii].'</td><td><span>'.$arrNutValues[$arrColsNames[$ii]].$nutValueUnitConv[ii].'</span></td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="">100%</div></div></td></tr>';
    if (($displayType == 'butFull') or (($percOfDRI < 90) and ($curDRIGoal >= 0)))
    {
        echo '<tr><td>'.$arrColsNamesToDisplay[$ii].'</td><td><span>'.
            $arrNutValuesToDisplay[$ii]." ".$arrNutUnitsToDisplay[$ii].
            '</span></td><td class="percentage-td"><div style="position: relative;height: 100%;"><div class="percent-bg" style="width: 100%;background-color: #dcd7d3 !important;"></div><div class="percent-bg" style="width: 100%;background-image: none;"></div><div class="percent-text" style="font-weight: bold;" title="">
    '.$percOfDRIToDisplay.'
    </div></div></td></tr>';
    }
//    echo '</div>';
}

mysqli_close($con);
?>
