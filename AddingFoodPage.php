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
            }
            .right-aligned {
                float: right;
                margin-left: 10px; /* Optional: Add margin */
            }
            .table-container { 
            max-width: 800px; 
            margin: 10px auto; 
        }
        </style>
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
        <header class="w3-main w3-cell-row" >
            <button class="w3-button w3-cell w3-xlarge w3-right w3-hover-theme" style="float:right;" onclick='document.getElementById("mySidebar").style.display = "block";'>&#9776;</button>
            <h2 class="w3-cell w3-right" style="float:right; margin: 5px;">הוספת מאכל</h2>
        </header>
        <hr>
        <div class="w3-container w3-row" style="position: relative;">
            <br>
            <div>                                            <!-- Search Query -->
                <input type="search" dir="rtl" name="qr" id="qr"
                       placeholder="מה תרצו להוסיף?" value="" onsearch="qrSearchSubmitted()"
                       aria-label="Search" autocomplete="off" style='width: 100%;' onfocusout="$('#qrpopover').hide();">

                <ul popover id="qrpopover" style="position:relative; inset:unset; top:40px"></ul>
            </div>                                                              <!-- Search Query -->
        </div>
        <br>
        <div class="w3-container w3-row" style="position: relative;">
            <div >                                            <!-- Item Name Query -->
                <div class="w3-cell-row">
                    <div class="w3-cell" style="width:15%;"><button class="w3-button" id="buttonInsertRecipt" name="buttonInsertRecipt" style="background-color: lightyellow"  onclick='insertProduct()' disabled>שמור מתכון</button></div>
                    <div class="w3-cell" style="width:5%;"><label for="isExtended" dir="rtl" > <input type="checkbox" id="checkboxIsExtended" name="checkboxIsExtended" checked >מורחב? </label></div>
                    <div class="w3-cell" style="w3-rest"><input type="search" dir="rtl" name="qrItemName" id="qrItemName"
                        placeholder="שם המוצר" value="" oninput="search_recipe_name()" 
                        aria-label="Search" autocomplete="off" style='width: 100%;' onfocusout="$('#srchRecPopover').hide();"></div>
                </div>
                <ul popover id="srchRecPopover" style="position:relative; inset:unset; top:40px"></ul>
            </div>                                                              <!-- Search Query -->
        </div>
        <div>
            <div id="idNutItemDiv"></div>
        </div>
        <div class="w3-container w3-row" style="position: relative;">
            <h1 id="header1" dir="rtl" ></h1>
            <img id="myImage" src="" alt="אין תמונה זמינה להציג" class="right-aligned">
            <br>
            <div class="table-container" id="id_product_table"></div>
        </div>
        <script>
            let cachedItemProperties = {};
            
            async function fetchPHPData(targeturl) {
                try {
                    const response = await fetch('fetchUrl.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            targeturl: targeturl
                        })
                    });

                    returned_txt = await response.text();
//                    console.log(returned_txt);
//                    const data = await response.json();
                    try {
                        const data = JSON.parse(returned_txt);
                        if (data['error'].length == 0) // No error
                        {
                            console.log(data['log']);
                            document.getElementById('myImage').src = data['image_link_str'];
                            document.getElementById('header1').textContent = data['product_name'];
                            document.getElementById('id_product_table').innerHTML = data['html_table'];
    //                        console.log(data['item_properties']);
                            cachedItemProperties = data['item_properties'];
                            element_item_name = document.getElementById('qrItemName');
                            element_item_name.value = data['item_properties']['itemName'];
                            const event = new Event('input');
                            element_item_name.dispatchEvent(event);
    //                        console.log(data['html_table']);
                        }
                        else
                        {
                            console.log("Error in loading url!");
                            console.log(data['error']);
                            console.log(data['log']);
                            document.getElementById('header1').textContent = "Not found!";
                        }
                } catch (error) {
                        console.log('Error in parsing JSON. Received response: ');
                        console.log(returned_txt);
                        console.error('Parsing error:', error);
                }
                } catch (error) {
                    console.error('Error:', error);
                }
            }

            function docLoaded()
            {
                console.log("Loaded");
        //        const phpCommunicator = new PHPCommunicator('advanced_handler.php');
        //        const calcResult = await phpCommunicator.calculate(5, 3);
        //        console.log('Calculation:', calcResult);            
//                fetchPHPData('https://www.foodsdictionary.co.il/Products/1/%D7%A4%D7%99%D7%A1%D7%98%D7%95%D7%A7%20%D7%97%D7%9C%D7%91%D7%99');

            }
            function qrSearchSubmitted()
            {
                product_link = document.getElementById('qr').value;
                fetchPHPData(product_link);
        //        document.getElementById("mytxt1").text = e.data;
            }

            // TODO: Duplication -> Merge with AddReciptPage.php
            function search_recipe_name()
            {
                const item_str = document.getElementById("qrItemName").value; 
//                console.log("item_str="+item_str);
                $.ajax({
                        url: 'findItemDB.php',
                        type: 'POST',
                        data: {
                                query: item_str
                              },
                        success: function(response) {
                                const obj = JSON.parse(response);
                                //console.log(obj);
                                if (obj.items.length === 0) // no item found
                                {
                                    document.getElementById("srchRecPopover").innerHTML = '';
                                    $('#srchRecPopover').hide();
                                    if (obj.query_txt_only.length > 2) // name exists but no match
                                    {
                                        document.getElementById("buttonInsertRecipt").removeAttribute("disabled");
                                    }
                                    else
                                    {
                                        document.getElementById("buttonInsertRecipt").setAttribute("disabled","");
                                    }

                                }
                                else
                                {
                                    document.getElementById("buttonInsertRecipt").setAttribute("disabled","");
                                     // show result(s)
                                    let text = '';
                                    const units = 'גרם';
                                    const caloriesUnit = 'קלוריות';
                                    const toWord = 'ל';
                                    if (obj.number_in_result === 0)
                                    {
                                        numDesiredQuantity = 100; // show results for 100 gram
                                    }
                                    else
                                    {
                                        numDesiredQuantity = obj.required_quantity;
                                    }
                                    for (let i = 0; i < obj.items.length; i++) {
                                        const cur_item = obj.items[i];

                                        const caloriesActual = cur_item['_energy'] * numDesiredQuantity / 100;

                                        text += `<ul> ${cur_item['itemName']} [${caloriesActual} ${caloriesUnit} ${toWord} ${numDesiredQuantity} ${units}]</ul>`;
                                    }
                                    document.getElementById("srchRecPopover").innerHTML = text;
                                    $('#srchRecPopover').show();
                                    document.getElementById("buttonInsertRecipt").setAttribute("disabled","");                                    
                                }
                            }
                        });                                
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
                            document.getElementById('idNutItemDiv').innerHTML = "";
                        }
                    };
                    xmlhttp.open("GET", "removeItemFromList.php?itemIndex=" + itemIndex, true);

                    xmlhttp.send();
                }
            }

            
            async function insertProduct()
            {
                cachedItemProperties['itemName'] = document.getElementById('qrItemName').value.trim();
                cachedItemProperties['isExtended'] = document.getElementById('checkboxIsExtended').checked;
//                console.log('--- cachedItemProperties');
//                console.log(cachedItemProperties);
//                console.log('cachedItemProperties ---');
                const response = await fetch('db_update_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(cachedItemProperties) 
                });

                const html_item_props = await response.text();
//                console.log(html_item_props);
                document.getElementById('idNutItemDiv').innerHTML = html_item_props;
            }



        </script>
    </body>
</html>
