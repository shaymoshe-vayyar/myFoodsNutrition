<!--https://hevodata.com/learn/xampp-mysql/-->
<!--<!DOCTYPE html>-->
<!--<html lang="he">-->
<!--<head>-->
<!--    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">-->
<!--</head>-->
<!--</html>-->
<?php
echo 'inParseUrl';
$url = 'https://www.google.com/search?q=site%3Afoodsdictionary.co.il%20%D7%97%D7%A1%D7%94%20%D7%A2%D7%A8%D7%9A%20%D7%AA%D7%96%D7%95%D7%A0%D7%AA%D7%99%20';

$page = file_get_contents($url);
//file_put_contents("tmp.php.cache",$page);
//$page = file_get_contents("tmp.php.cache");

echo $page;


//$page = mb_convert_encoding($page, 'utf-8', mb_detect_encoding($page));

//$dom = new DOMDocument();
//$dom->loadHTML($page);
//$table_classnv = getElementsByClass($dom,'table','nv-table');
//$str = $dom->saveHTML($table_classnv[1]);
//if (strpos($str,'/NutritionalValues/Calories.php'))
//{
//    //echo 'found';
//    //mb_internal_encoding("UTF-8");
//    $tmp = substr($str,strpos($str,'/NutritionalValues/Calories.php'),120);
//    //echo mb_detect_encoding('קל');
//    //echo '<html lang="he"><meta http-equiv="Content-Type" content="text/html; charset=windows-1255" /><div>'.$tmp.'</div></html>';
//    //echo var_dump(mb_strpos($tmp,iconv('ק','cp-1255','UTF-8'),0));
//}
////echo $str;
//
//foreach ($table_classnv as $table_classnv_node)
//{
//    //echo $table_classnv_node->nodeValue, PHP_EOL;
//    $trs = $table_classnv_node->getElementsByTagName("tr");
//    foreach ($trs as $tr) {
//        //echo $tr->nodeValue, PHP_EOL;
//        $tds = $tr->getElementsByTagName("td");
//        if (count($tds)>1)
//        {
//            echo $tds[0]->textContent;
//            echo $tds[1]->getAttribute('data-start');
//        }
////        foreach ($tds as $td) {
////            $str = $td->textContent;
////            //echo mb_convert_encoding($str, "UTF-8", mb_detect_encoding($str));
////            //echo iconv("cp1255", "UTF-8", $str);
////            //echo "\xEF\xBB\xBF".$str;
////
////            //echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-1255" />'.$td->textContent;
////            //echo $dom->saveHTML($td);
//////            echo '<html lang="he"><meta http-equiv="Content-Type" content="text/html; charset=windows-1255" /><div>'.$str.'</div></html>';
////
////            //echo $str;
////        }
//    }
//}
////echo $dom->saveHTML($table_classnv[1]);
//
//
////echo $page;


function getElementsByClass(&$parentNode, $tagName, $className) {
    $nodes=array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for ($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        if (stripos($temp->getAttribute('class'), $className) !== false) {
            $nodes[]=$temp;
        }
    }

    return $nodes;
}
?>




<!--function makeHttpObject() {-->
<!--try {return new XMLHttpRequest();}-->
<!--catch (error) {}-->
<!--try {return new ActiveXObject("Msxml2.XMLHTTP");}-->
<!--catch (error) {}-->
<!--try {return new ActiveXObject("Microsoft.XMLHTTP");}-->
<!--catch (error) {}-->
<!---->
<!--throw new Error("Could not create HTTP request object.");-->
<!--}-->
<!--const cors = require('cors');-->
<!--const corsOptions ={-->
<!--origin:'http://localhost:3000',-->
<!--credentials:true,            //access-control-allow-credentials:true-->
<!--optionSuccessStatus:200-->
<!--}-->
<!--app.use(cors(corsOptions));-->
<!---->
<!--$.ajax({-->
<!--url: "https://www.googleapis.com/customsearch/v1?key=AIzaSyACa4OpyUg53lK6-SFJLqSs1TQcjX1iCCs&cx=017576662512468239146:omuauf_lfve&q=lectures"-->
<!--}).then(-->
<!--function(data){-->
<!--console.log("Here");-->
<!--alert("here");-->
<!--console.log(data);-->
<!--}-->
<!--)-->

<!--function searchItem()-->
<!--{-->
<!--var qrVal = document.getElementById("qr").value;-->
<!--var request = makeHttpObject();-->
<!--var key = "AIzaSyACa4OpyUg53lK6-SFJLqSs1TQcjX1iCCs";//"AIzaSyACa4OpyUg53lK6-SFJLqSs1TQcjX1iCCs"; // your api key registered with google.-->
<!--var itemQuery = qrVal+"%20ערך%20תזונתי%20";-->
<!--var query = "food";//"site%3Afoodsdictionary.co.il"+"%20"+itemQuery;-->
<!--var url = "https://www.googleapis.com/customsearch/v1?key="+key+"&q="+query;-->
<!---->
<!---->
<!--var url = `GET https://www.googleapis.com/customsearch/v1?key=${key}&cx=017576662512468239146:omuauf_lfve&q=lectures`;-->
<!--// var url = `GET https://www.googleapis.com/customsearch/v1?key=${key}&q=lectures`;-->
<!--// console.log(itemQuery);-->
<!--// var query = "www.google.com/search?q=site%3Afoodsdictionary.co.il"+"%20"+itemQuery;-->
<!--console.log(url);-->
<!--//-->
<!--request.open("POST",url , true);-->
<!--request.send(null);-->
<!--request.onreadystatechange = function() {-->
<!--if (request.readyState == 4)-->
<!--console.log(request.responseText);-->
<!--};-->
<!---->
<!--}-->
<!---->
<!--//var inpStr = '[\x22ערך תזונתי של חסה ערבית, ערכים תזונתיים - FoodsDictionary\x22,\x22https://www.foodsdictionary.co.il/Products/1/%D7%97%D7%A1%D7%94%20%D7%A2%D7%A8%D7%91%D7%99%D7%AA\x22]';-->
<!--var inpStr = "'[\x22https://www.foodsdictionary.co.il/Products/665/%D7%97%D7%A1%D7%94%20%D7%A1%D7%9C%D7%A0%D7%95%D7%91%D7%94\x22,\x22ערך תזונתי של חסה סלנובה, כרמלים שדות חדשים, ערכים תזונתיים\x22,'";-->
<!--//const regex = /[\u{590}\-\u{5ff}\s,]+/u;-->
<!--//const regex = /\x22[\u{590}\-\u{5ff}\s,]+/u;-->
<!--//const regex = /\x22([\u{590}\-\u{5ff}\s,]+)/u;-->
<!--const regex = /'\[\x22(https:\/\/[\w\.\/%]+)\x22([.]+)/u;-->
<!--//document.getElementById("idh2").textContent =regex.exec(inpStr);-->
<!--// var strArr = '[\x22ערך תזונתי של חסה ערבית, ערכים תזונתיים - FoodsDictionary\x22,\x22https://www.foodsdictionary.co.il/Products/1/%D7%97%D7%A1%D7%94%20%D7%A2%D7%A8%D7%91%D7%99%D7%AA\x22'.matchAll('/(\x22[.]*)/u')-->
<!--// nextWord = strArr.next();-->
<!--// while (!nextWord.done)-->
<!--// {-->
<!--//     document.getElementById("idh2").textContent +=nextWord.value;-->
<!--//     nextWord = strArr.next();-->
<!--// }-->
<!---->
<!--//console.log(regex.exec(inpStr));-->
<!--// console.log(strArr);-->
<!---->
<!--//console.log(inpStr.split('\x22'));-->