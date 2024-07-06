<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html lang="he" style="font-size: 16px;" >
    <head>
        <meta content="charset=utf-8; text/html" http-equiv="Content-Type">
        <title>myDiary</title>
        <!--    maximum-scale=1 is used for iphone to avoid auto-zoom-->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3pro.css">
        <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-teal.css">

        <!-- [ Bootstraps -->
        <script src ="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src ="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js" integrity="sha512-42PE0rd+wZ2hNXftlM78BSehIGzezNeQuzihiBCvUEB3CVxHvsShF86wBWwQORNxNINlBPuq7rG4WWhNiTVHFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src ="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
        <link rel ="stylesheet" href ="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel ="stylesheet" href ="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'  as="style" onload="this.onload = null;this.rel = 'stylesheet'">
        <!--   Bootstraps ] -->

        <!--    Style -->
        <style>
            table, th, td {
                border-collapse: collapse;
                border:1px solid;
            }
            th {
                background-color: lightgrey;
            }    </style>
        <!--    Style-->

    </head>
    <body onload="docLoaded()" style="background-color:#dcd7d3;" >

        <hr>
        <nav class="w3-sidebar w3-bar-block w3-card-4" id="mySidebar" style="display: none;right:5px;">
            <button onclick='document.getElementById("mySidebar").style.display = "none";' class="w3-button w3-display-topright w3-large">X</button>
            <h3 class="w3-bar-item w3-button"><a href="index.php">יומן מעקב יומי</a></h3>
            <h3 class="w3-bar-item w3-button"><a href="AddingFoodPage.php">הוספה של מאכל</a></h3>
            <h3 class="w3-bar-item w3-button"><a href="AddingRecipePage.php">הוספה של מתכון</a></h3>
            <h3 class="w3-bar-item w3-button" href="#">הגדרות</h3>
        </nav>
        <header class="w3-main w3-row" >
            <button class="w3-button w3-cell w3-xlarge w3-right w3-hover-theme" style="float:right;" onclick='document.getElementById("mySidebar").style.display = "block";'>&#9776;</button>
            <h2 class="w3-cell w3-right" style="float:right; margin: 5px;">הוספת מתכון למאכל</h2>
        </header>
        <hr>
        <div class="w3-container w3-cell-row" style="position: relative;">
            <br>
            <div>                                            <!-- Search Query -->
                <textarea name="qrlist" id="qrlist" dir="rtl" placeholder="הכנס את הרשימה כאן" required autocomplete="off" style='width: 100%;' oninput="updateQRList(event)" onpaste="updateQRListUponPaste(event)" ></textarea>

                <ul popover id="qrpopover" style="position:relative; inset:unset; top:40px"></ul>
            </div>                                                              <!-- Search Query -->
        </div>
        <br>
        <div id="itemsListDiv" w3-right class="w3-container w3-row"  dir="rtl">
            <!--<div class="w3-cell" style="width:2%;"></div>-->
<!--            <table style="w3-rest float:right" dir="rtl">
                <tr>
                    <th>מוצר</th>
                    <th>כמות</th>
                </tr>
                <tr>
                    <td>תא1</td>
                    <td>תא2</td>
                </tr>
                <tr>
                    <td>Cell3</td>
                    <td>Cell4</td>
                </tr>
            </table>-->
            <div >
                <textarea id="ItemsListTextarea" name="ItemsListTextarea" style="width: 100%;background-color: lightgoldenrodyellow" dir="rtl"></textarea>
            </div>
            <!--<div class="w3-cell" style="width:2%;"></div>-->
        </div>
        <br>
        <div>
            <div >                                            <!-- Item Name Query -->
                <div class="w3-cell-row">
                    <div class="w3-cell" style="width:15%;"><button class="w3-button" id="buttonInsertRecipt" name="buttonInsertRecipt" style="background-color: lightyellow"  onclick='insertRecite()' disabled>שמור מתכון</button></div>
                <div class="w3-cell" style="w3-rest"><input type="search" dir="rtl" name="qrItemName" id="qrItemName"
                   placeholder="שם המתכון" value="" oninput="search_recipe_name()" 
                   aria-label="Search" autocomplete="off" style='width: 100%;' onfocusout="$('#srchRecPopover').hide();"></div>
                </div>
                <ul popover id="srchRecPopover" style="position:relative; inset:unset; top:40px"></ul>
            </div>                                                              <!-- Search Query -->
        </div>
        <div>
            <div dir="rtl" class="w3-large"><p><span> משקל כולל: </span><span id="idTotalQuantity">  </span></p></div>
            <div id="nutDataDiv"></div>
        </div>
        <script>
            function searchItem(item_str, flag_is_extended = false)
            {
                //console.log('-----------------')
                //console.log(item_str);
                //console.log('-----------------')
                
                if (item_str.trim() == "") {
                    document.getElementById("qrpopover").innerHTML = '';
                    $('#qrpopover').hide();
                    return;
                } else {
                    if (flag_is_extended == false)
                    {
                        indexOfStarCharInStr = item_str.indexOf("*");
                        isStarCharInStr = (indexOfStarCharInStr >= 0);
                        if (isStarCharInStr){ // remove the '*' for the rest of the search
                            item_str = item_str.substring(0,indexOfStarCharInStr) + item_str.slice(indexOfStarCharInStr+1);
                        }
                    }
                    else
                    {
                        isStarCharInStr = true;
                    }
                    numbersInStr = item_str.match(/\b(\d+\.?\d?)\b/g);
                    engWordsInStr = item_str.match(/\b[^\d\W]+\b/g);
                    hebWordsInStr = item_str.match(/[\u0590-\u05FF]+/g);

                    numDesiredQuantity = 100;
                    if ((numbersInStr != null) && (numbersInStr.length > 0)) {
                        if (numbersInStr.length > 1)
                        {
                            console.error('too many numbers');
                        } else
                        {
                            numDesiredQuantity = parseFloat(numbersInStr[0]);
                            //console.log('------------------------------');
                            //console.log(numDesiredQuantity); //
                            //console.log('------------------------------');
                        }
                    }
                    if ((hebWordsInStr != null) && (hebWordsInStr.length > 0) && (hebWordsInStr[0].length > 0))
                    {
                        if (hebWordsInStr.includes('גרם'))
                        {
                            hebWordsInStr = hebWordsInStr.filter(x => x!== 'גרם');
                        }
                        retAtTheEnd = false;
                        if (hebWordsInStr.join(' ').includes('מחק שורה'))
                        {
                            // console.log(hebWordsInStr.join(' ').includes('מחק שורה'));
                            clearQR(); 
                            return;
                        }
                         //const mypopover = document.getElementById("qrpopover");

                        //mypopover.style.top = '100px';
                        const xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function () {
                            //console.log(this.readyState); //
                            //console.log(this.status); //
                            if (this.readyState == 4 && this.status == 200) {
                                //console.log(this.responseText);
                                text = '';
                                if (this.responseText.length > 1)
                                {
                                    arrOptions = this.responseText.split(';');
                                    if (arrOptions[0].split(',').length > 6) {
                                    const units = 'גרם';
                                    const caloriesUnit = 'קלוריות';
                                    const toWord = 'ל';
                                    for (let i = 0; i < arrOptions.length; i++) {
                                        if (arrOptions[i].length > 0)
                                        {
                                            arrPair = arrOptions[i].split(','); // Name, Calories, itemUID
                                            const name = arrPair[0];
                                            const itemUID = arrPair[2];
                                            const numDesiredQuantity = arrPair[3];
                                            const caloriesActual = parseFloat(arrPair[1])*numDesiredQuantity/100;
                                            const numbersInStr = arrPair[4];
                                            const hebWordsInStrJoined = arrPair[5];
                                            //text += `<option> ${name} [${caloriesActual} ${caloriesUnit} ${toWord} ${numDesiredQuantity} ${units}]</option>`;
                                            text += `<ul> ${name} [${caloriesActual} ${caloriesUnit} ${toWord} ${numDesiredQuantity} ${units}]</ul>`;
                                            if ((numbersInStr != null) && (numbersInStr.length > 0)) // Only one suggestion
                                            {
                                                flag_is_submit = false;
                                                if (arrOptions.length == 2)
                                                {
                                                    flag_is_submit = true;
                                                } else
                                                {
                                                    if (name.trim() == hebWordsInStrJoined.trim())
                                                    {
                                                        flag_is_submit = true;
                                                    }
                                                    else
                                                    {
                                                        const words_in_name = name.split(' ');
                                                        for (let ii=0;ii<words_in_name.length;ii++)
                                                        {
                                                          if (words_in_name[ii].trim() == hebWordsInStrJoined.trim())
                                                          {
                                                             flag_is_submit = true; 
                                                          }
                                                        }
                                                    }
                                                }
                                                if (flag_is_submit)
                                                {
                                                    //console.log("data="+$('#qr').data('selItem'));
//                                                    console.log("Match");
                                                    $('#qr').data('selItem', name);
                                                    $('#qr').data('quantity', numDesiredQuantity);
                                                    //console.log("Inserting: item_str="+item_str+", name="+name+", numDesiredQuantity="+numDesiredQuantity);
                                                    addItemToCache(item_str,name,numDesiredQuantity,itemUID);
                                                    //printTable();
                                                    if (retAtTheEnd)
                                                    {
                                                        qrSearchSubmitted();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $('#qrpopover').show();
                                } else
                                    {
                                        arrPair = arrOptions[0].split(','); 
                                        isStarCharInStrSent = arrPair[3];

//                                        console.log("here+isStarCharInStrSent="+isStarCharInStrSent);
                                        if (isStarCharInStrSent==="false") // if no result found, recheck extended
                                        {
                                            searchItem(item_str,true);
                                            return;
                                        }
                                        else
                                        {
                                            $('#qrpopover').hide();
                                        }
                                    }
                                }
                                // console.log(text);
                                //document.getElementById("ListName").innerHTML = text;
                                document.getElementById("qrpopover").innerHTML = text;
                            }
                        };
                        //xmlhttp.open("GET","./phpFiles/findItemDB.php?q="+hebWordsInStr.join(' '),true);
                        //console.log("findItemDB.php?q=" + hebWordsInStr.join(' ') + "&isFull=0" + "&isStarCharInStr=" + isStarCharInStr)
                        xmlhttp.open("GET", "findItemDB.php?q=" + hebWordsInStr.join(' ') + "&isFull=0" + "&isStarCharInStr=" + isStarCharInStr + "&numDesiredQuantity=" + numDesiredQuantity +
                                "&numbersInStr="+numbersInStr, true);

                        xmlhttp.send();
                    } else
                    {
                        //clearQR();
                        document.getElementById("qrpopover").innerHTML = '';
                        $('#qrpopover').hide();

                    }
                }
            }
            const tableCachedItem = {};
            function addItemToCache(line, name, desired_quantity, item_id)
            {
                tableCachedItem[line] = name+"&"+desired_quantity+"&"+item_id;
            }
            function printTable()
            {
                console.log("----- Print tableCachedItem ------")
                for (let item_str_qr in tableCachedItem)
                {
                    const item_prop = tableCachedItem[item_str_qr];
                    const item_prop_arr = item_prop.split('&'); 
                    const name = item_prop_arr[0];
                    const desiredQuantity = parseFloat(item_prop_arr[1]);
                    const itemUID = item_prop_arr[2];
                    console.log("item_str_qr="+item_str_qr+", name="+name+", desiredQuantity="+desiredQuantity+", itemUID="+itemUID);
                }
            }
            function docLoaded()
            {
//                document.getElementById("tableofidentifieditems").text = "bbbbb\nqqq";
//                sendData();
            }
            function delay_tmp(milliseconds) {
                return new Promise(resolve => {
                    setTimeout(resolve, milliseconds);
                });
            }
            async function delay() {
                await delay_tmp(100);
            }
            function generateListOfItemsWithProp(flag_is_store = false)
            {
                const str_items_q_textbox = document.getElementById("qrlist").value;
                const lines = str_items_q_textbox.split("\n");
                const items_list_ta = document.getElementById("ItemsListTextarea");
                items_list_ta.value = "";
                let actual_lines_len = 0;
                let flag_is_all_items_good = true;
                const arr_items = [];
                for (let i = 0; i < lines.length; i++) 
                {
                    const line = lines[i].trim();
                    if (line.length == 0) // Empty line -> ignore
                    {
                        continue;
                    }
                    if (line in tableCachedItem)
                    {
//                        console.log(line+" in cache!");
                        const item_prop = tableCachedItem[line];
                        const item_prop_arr = item_prop.split('&'); 
                        const name = item_prop_arr[0];
                        const numDesiredQuantity = item_prop_arr[1];
                        const itemUID = item_prop_arr[2];
                        
                        items_list_ta.value = items_list_ta.value + name + " " + numDesiredQuantity + " גרם "  + "\n";
                        actual_lines_len = actual_lines_len+1;
                        let cur_item = {
                                "name": name,
                                "numDesiredQuantity": numDesiredQuantity,
                                "itemUID": itemUID
                               };
                        arr_items.push(cur_item);
                    }
                    else
                    {
//                        console.log("line '"+line+"' not in cache, line.length="+line.length);
                        items_list_ta.value = items_list_ta.value  + "???" + line+ "\n";
                        actual_lines_len = actual_lines_len+1;
                        flag_is_all_items_good = false;
                    }
                }
                items_list_ta.rows = actual_lines_len+1;
                if (flag_is_all_items_good)
                {
                    if (flag_is_store)
                    {
                        const item_str = document.getElementById("qrItemName").value;
//                        console.log("Saving Recipe: "+item_str)
                        sendData(arr_items, item_str);
                    }
                    else
                    {
                        sendData(arr_items);
                    }
                }
                else
                {
                   document.getElementById("buttonInsertRecipt").setAttribute("disabled",""); 
                }
            }
            
            function sendData(arr_items, name_of_recipe = null)
            {
                let flag_is_update_db = 0; 
                if (!(name_of_recipe === null))
                {
                    flag_is_update_db = 1;
                }
                let arr_items2 = arr_items;
                if (arguments.length === 0)
                {
                    arr_items2 = [
                        {"name":"דלעת מבושלת", "numDesiredQuantity":"40", "itemUID":144},
                        {"name":"בצל", "numDesiredQuantity":"0", "itemUID":1},
                    ];
                    flag_is_update_db = 1;
                    name_of_recipe = "מתכון";
                }
                const blob = new Blob([JSON.stringify(arr_items2)], { type: "application/json" });

//                console.log("sending data");

                const xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    //console.log(this.readyState); //
                    //console.log(this.status); //
                    if (this.readyState == 4 && this.status == 200) {
                        //console.log(this.responseText);
                        if (this.responseText.length > 0)
                        {
                            document.getElementById('nutDataDiv').innerHTML = this.responseText;
                            total_quantity = document.getElementById('tableNutValues').getAttribute('data-totalquan');
                            document.getElementById('idTotalQuantity').textContent = total_quantity+" "+"גרם";
                        }
                    };
                };
                xmlhttp.open("POST", "updateRecipeItem.php?flag_is_update_db="+flag_is_update_db+"&name_of_recipe="+name_of_recipe, true);
                xmlhttp.send(blob);

            }
            
            function removeItemFromList(itemIndex,strTxt)
            {
                //console.log("remove "+itemIndex);
                retValue = confirm("האם אתה בטוח שתרצה להסיר את המוצר הבא?\n"+strTxt);
                if (retValue == true) // Remove
                {
                    const xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function () {
                        //console.log(this.readyState); //
                        //console.log(this.status); //
                        if (this.readyState == 4 && this.status == 200) {
                            // console.log(this.responseText);
                            //console.log('updating table');
                            // console.log('delete');
                            document.getElementById('nutDataDiv').innerHTML = "";
                        }
                    };
                    xmlhttp.open("GET", "removeItemFromList.php?itemIndex=" + itemIndex, true);

                    xmlhttp.send();
                }
            }

            
            function updateQRList(e)
            {
//                console.log("in updateQRList");
                const textval = e.target.value;
                const selectionStart = e.target.selectionStart;
//                console.log("selectionStart="+selectionStart);
//                console.log("textval.length="+textval.length);
                if (textval[textval.length-1] === '\n')
                {
                    //console.log("Enter pressed!");
                    generateListOfItemsWithProp();
                }
                lines = textval.split("\n");
                let current_line_ind = lines.length-1;
                if (selectionStart == textval.length)
                {
//                  // console.log("User cursor is at end of the line");
                }
                else
                {
//                    console.log("User cursor is not at end of the line");
                    let running_ind = 0;
                    for (let i = 0; i < lines.length; i++) {
                        let prev_running_ind = running_ind;
                        running_ind = running_ind + lines[i].length;
//                        console.log("i="+i+", prev_running_ind="+prev_running_ind+", running_ind="+running_ind);
                        if ((selectionStart >= prev_running_ind) && (selectionStart <= running_ind))
                        {
                            current_line_ind = i;
                        }
                    }
                }
//                    console.log("current_line_ind="+current_line_ind);
                const line = lines[current_line_ind].trim();
                searchItem(line);                                    
            }
            
            function updateQRListUponPaste(e)
            {
//                console.log("Copy&Paste");                
                const textval = event.clipboardData.getData('text'); //e.target.value;//.substring(0,selectionEnd);
//                console.log("textval="+textval);
                lines = textval.split("\n");
                for (let i = 0; i < lines.length; i++) {
                        line = lines[i].trim();
                        //console.log("line= "+line);
                        //console.log("line.length= "+line.length);
                        //console.log("searchItem number "+i);
                        searchItem(line);
                    }                    
            }
            
            function insertRecite()
            {
                generateListOfItemsWithProp(true);
            }
            
            function search_recipe_name()
            {
                const item_str = document.getElementById("qrItemName").value; //$('#qrItemName').value;
//                console.log("item_str="+item_str);
                document.getElementById("buttonInsertRecipt").setAttribute("disabled","");
                if (item_str.trim() == "") {
                    $('#srchRecPopover').innerHTML = '';
                    $('#srchRecPopover').hide();
                    return;
                } else {
                    isStarCharInStr = true; // always extend when searching for recipe
                    numbersInStr = item_str.match(/\b(\d+\.?\d?)\b/g);
//                    engWordsInStr = item_str.match(/\b[^\d\W]+\b/g);
//                    hebWordsInStr = item_str.match(/[\u0590-\u05FF]+/g);

                    numDesiredQuantity = 100;
                    if ((numbersInStr != null) && (numbersInStr.length > 0)) {
                        alert('Recipe name can"t contain numbers');
                        return;
                    }
                    {
                        retAtTheEnd = false;
                        const xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function () {
                            //console.log(this.readyState); //
                            //console.log(this.status); //
                            if (this.readyState == 4 && this.status == 200) {
                                //console.log("************************************");
//                                console.log(this.responseText);
                                text = '';
                                if (this.responseText.length > 1)
                                {
                                    arrOptions = this.responseText.split(';');
                                    if (arrOptions[0].split(',').length > 6)
                                    {
//                                        console.log("len>6");
                                        const units = 'גרם';
                                        const caloriesUnit = 'קלוריות';
                                        const toWord = 'ל';
                                        for (let i = 0; i < arrOptions.length; i++) {
                                            if (arrOptions[i].length > 0)
                                            {
                                                arrPair = arrOptions[i].split(','); // Name, Calories, itemUID
                                                const name = arrPair[0];
                                                const caloriesActual = parseFloat(arrPair[1]);
                                                const itemUID = arrPair[2];
                                                text += `<ul> ${name} [${caloriesActual} ${caloriesUnit} ${toWord} 100 ${units}]</ul>`; // 100 gram is default

                                            }
                                        }
                                        $('#srchRecPopover').show();
                                    }
                                    else
                                    {
//                                        console.log("len<6");
                                        $('#srchRecPopover').hide();
                                    }
                                } 
//                                console.log(text);
                                document.getElementById("srchRecPopover").innerHTML = text;
                                if (text.length == 0) // No match found
                                {
                                    document.getElementById("buttonInsertRecipt").removeAttribute("disabled");
                                }
                            }
                        };
                        //xmlhttp.open("GET","./phpFiles/findItemDB.php?q="+hebWordsInStr.join(' '),true);
                        //console.log("findItemDB.php?q=" + hebWordsInStr.join(' ') + "&isFull=0" + "&isStarCharInStr=" + isStarCharInStr)
                        xmlhttp.open("GET", "findItemDB.php?q=" + item_str + "&isFull=0" + "&isStarCharInStr=" + isStarCharInStr + "&numDesiredQuantity=" + numDesiredQuantity +
                                "&numbersInStr="+numbersInStr, true);

                        xmlhttp.send();
                    }
                }
            }
        </script>
    </body>
</html>
