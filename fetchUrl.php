<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

include 'globals.php';

//set_error_handler(function($errno, $errstr, $errfile, $errline) {
//    // Convert all errors to exceptions
//    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
//});

$encoding = 'UTF-8';
mb_internal_encoding($encoding);

$target_url = $_POST['targeturl'];

$item_properties = [];
$product_name = '';
$image_link_str = '';
$html_table = '';
$log = '';

try {
// Initialize cURL
    $ch = curl_init();

// Set cURL options
    curl_setopt($ch, CURLOPT_URL, $target_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept-Language: he,en',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
    ]);

// Execute request
    $response = curl_exec($ch);
// Comprehensive Encoding Detection
    $encodings = [
        'UTF-8',
        'ISO-8859-8',
        'ASCII'
    ];

// Safe Encoding Detection
    $detectedEncoding = mb_detect_encoding($response, $encodings, true);

    if (!$detectedEncoding) {
        // Fallback to UTF-8 if no encoding detected
        $detectedEncoding = 'UTF-8';
    }

// Convert to UTF-8
    $convertedText = mb_convert_encoding(
            $response,
            'UTF-8',
            $detectedEncoding
    );

//    $log = $log.$response."\n";
//    var_dump($response);
    
//echo json_encode([
//    'result' => 'Processed',
//    'data' => $param1 . $param2
//]);
//// Check for errors
//if (curl_errno($ch)) {
//    echo json_encode(['error' => curl_error($ch)]);
//} else {
//    echo $convertedText;
//}
// Close cURL
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($response);

    $xpath = new DOMXPath($dom);

// Get product name if any
    $header1s = $xpath->query('//h1');
    if (count($header1s) > 0) {
        $product_name = $header1s[0]->textContent;
    } else {
        $product_name = '';
    }

// Get image if any
    $image_links = $xpath->query('//meta[@property="og:image"]');
    if (count($image_links) > 0) {
        $image_link_str = $image_links[0]->getAttribute('content');
//    echo  $image_link_str . "\n";
    } else {
        $image_link_str = '';
    }

// Find table with class 'nv-table'
    $table = $xpath->query("//table[contains(@class,'nv-table')]")[0];

    $nutValueTableRows = [];

//echo $convertedText;
    $HandleConversion = $_SESSION['HandleConversion'];
    $ignoreIncludedNut = 'כפיות סוכר';
// Iterate through table rows
    foreach ($xpath->query('.//tr', $table) as $nutData) {
        $nutDataEnt = $xpath->query('.//td', $nutData);

        if ($nutDataEnt->length > 1) {
            $nutName = trim($nutDataEnt[0]->textContent);

            // Extract units
            preg_match('/\((.*?)\)/', $nutName, $unitsMatch);
            $nutUnitsHeb = $unitsMatch[1] ?? '';

            // Remove units from name
            $nutNameWOUnits = trim(preg_replace('/\(.*?\)/', '', $nutName));

            // Special case handling
            if ($nutUnitsHeb === 'אנרגיה') {
                $tmp = $nutUnitsHeb;
                $nutUnitsHeb = $nutNameWOUnits;
                $nutNameWOUnits = $tmp;
            }

            $nutValue = trim($nutDataEnt[$nutDataEnt->length - 1]->textContent);
            $nutValue = $nutValue ?: 0;

            // Ignore specific nutrients
            $ignoreList = [
                'מתוכם שומן טראנס', 'אלכוהול', 'נחושת', 'תראונין',
                'ואלין', 'מנגן', 'לאוצין', 'ארגינין',
                'טריפטופן', 'ליזין', 'אלנין', 'ויטמין B8'
            ];

            if (in_array($nutNameWOUnits, $ignoreList)) {
                continue;
            }

            if (strpos($nutNameWOUnits, $ignoreIncludedNut) == true) {
                continue;
            }

            // Special name replacements
            $specialReplacements = [
                'ויטמין B' => 'סה"כ ויטמין B',
                'ויטמין B3' => "ויטמין B3 - ניאצין",
                'מתוכן סוכרים' => 'סוכרים'
            ];

            $nutNameWOUnits = $specialReplacements[$nutNameWOUnits] ?? $nutNameWOUnits;

            // Convert Hebrew name to English (you'll need to implement this)
            $EngNutName = $HandleConversion->dictHebNameToEngName($nutNameWOUnits);
            $nutEngUnits = $HandleConversion->dictHebNameToEngName($nutUnitsHeb);

            // Convert units (you'll need to implement this)
            list($convertedValue, $convertedUnit) = $HandleConversion->convertUnitToStandard($nutValue, $nutEngUnits);

            if (isset($__nutDisplayUnitsEng__[$EngNutName])) {
                if ($__nutDisplayUnitsEng__[$EngNutName] !== $nutEngUnits) {
                    $oldUnit = $__nutDisplayUnitsEng__[$EngNutName];
                    echo "Display unit for $EngNutName has changed during read from $oldUnit to $nutEngUnits\n";
                }
            } else {
                $__nutDisplayUnitsEng__[$EngNutName] = $nutEngUnits;
            }

            // Store converted value
            $nutValueTableRows['_' . $EngNutName] = $convertedValue;
        } else {
            // Size validation
            $thElements = $xpath->query('.//th', $nutData);
            foreach ($thElements as $line) {
                if ($line->getAttribute('id') === 'sizeNameTd') {
                    if (strpos($line->textContent, '100') === false) {
                        throw new Exception('Error in parsing, size is not 100 grams/mili-liter!');
                    }
                }
            }
        }
    }

    function arrayToHTMLTable($data) {
        ob_start();

        echo "<table border='1'>";
        echo "<tr>";

        // Create header row
        foreach (array_keys($data[0]) as $key) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr>";

        // Create data rows
        foreach ($data as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";

        $tableHTML = ob_get_clean();

        return $tableHTML;
    }

    $tableToDisplay = [];
    foreach ($nutValueTableRows as $nut_name => $nut_value)
    {
        list($nut_value_disp, $nut_unit_disp) = $HandleConversion->convertNameValueToDisplay($nut_name, $nut_value);
        $nut_name_disp = $HandleConversion->dictEngNameToHebName(trim($nut_name,'_'));
        array_push($tableToDisplay, ['יחידות' => $nut_unit_disp,
                'ערך' => $nut_value_disp,
                'שם' => $nut_name_disp]
                );
    }
    
    $html_table = arrayToHTMLTable($tableToDisplay);
    
    // Generate item properties
    $item_properties = array_merge(["itemName" => $product_name, 
                                    "additionalNames" => '',
                                    "nutritionsVSource" => $target_url,
                                    "itemPhotoLink" => $image_link_str,
                                    "categoryType" => 'general',
                                    "isExtended" => '1',        // default
                                    "itemsCombination" => ''
                                    ], $nutValueTableRows);    
    
    $error = '';
} catch (Exception $e) {
    $error = $e->getMessage()."\n".$e->getTraceAsString();
} catch (ValueError $e) {
    $error = $e->getMessage()."\n".$e->getTraceAsString();
}

echo json_encode([
    'item_properties' => $item_properties,
    'product_name' => $product_name,
    'image_link_str' => $image_link_str,
    'html_table' => $html_table,
    'error' => $error,
    'log' => $log
]);



