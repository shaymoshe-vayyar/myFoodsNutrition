<!DOCTYPE html>
<!--https://websitesetup.org/website-coding-html-css/-->
<!--https://websitesetup.org/wp-content/uploads/2019/10/WSU-HTML-Cheat-Sheet.pdf-->
<!--https://websitesetup.org/bootstrap-tutorial-for-beginners/-->
<!--https://www.w3schools.com/html/html_table_headers.asp-->

<!--https://funprojects.blog/2023/07/18/pyscript-python-on-a-web-page/-->
<!-- Old?: https://dev.to/steadylearner/how-to-use-python-in-javascript-4bnm-->
<!-- Old?:https://www.educative.io/blog/web-development-in-python-->
<!-- https://www.jhanley.com/blog/pyscript-javascript-and-python-interoperability/-->
<!-- https://www.delftstack.com/howto/javascript/call-python-from-javascript/-->

<!-- https://websitesetup.org/website-coding-html-css/ -->
<!--https://firstclicklimited.com/tutorials/index.php/2021/08/04/html-dropdown-with-text-input/-->
<!--https://stackoverflow.com/questions/17929356/html-datalist-values-from-array-in-javascript-->
<!--https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/input_event-->

<!--https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_flexible_box_layout/Aligning_items_in_a_flex_container-->

<html lang="he" style="font-size: 16px;" >
    <head>
        <meta content="charset=utf-8; text/html" http-equiv="Content-Type">
        <!--  <meta content="text/html; charset=windows-1255" http-equiv="Content-Type">-->
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
    <!--    <script src="https://kit.fontawesome.com/dbe437d295.js" crossorigin="anonymous"></script>-->
        <!--   Bootstraps ] -->

        <!-- Script -->
        <script>
        </script>
        <!-- Script -->

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
        <header class="w3-main w3-cell-row" >
            <button class="w3-button w3-cell w3-xlarge w3-right w3-hover-theme" style="float:right;" onclick='document.getElementById("mySidebar").style.display = "block";'>&#9776;</button>
            <h2 class="w3-cell w3-right" style="float:right; margin: 5px;">יומן מעקב יומי</h2>
        </header>
        <hr>
        <div class="w3-container w3-row" style="position: relative;">
            <div class="w3-cell-row" style="background-color:#ffffff;">     <!-- Date and Summary -->
                <br>
                <div class="w3-cell-row" >
                    <div class="w3-cell" style="width:30%;"></div>
                    <input type='text' class="form-control w3-rest" aria-label="picker" id='picker' style="text-align:center;" >
                    <script type="text/javascript">
                        $(function () {
                            $('#picker').datetimepicker({viewMode: 'days', format: 'YYYY-MM-DD', useCurrent: true,
                                showTodayButton: true,
                                widgetPositioning: {
                                    horizontal: 'auto',
                                },
                                defaultDate: new Date()});
                            $("#picker").on("dp.change", function (e) {
                                updateTables();
                                $('#qr').focus();
                            });
                        });
                    </script>
                    <div class="w3-cell" style="width:30%"></div>
                </div>
                <div class="w3-cell-row">
                    <div class="w3-cell" style="width:30%;"></div>
                    <div class="w3-cell w3-center" style="w3-rest" dir="rtl"><p><span id="idTotalCal">  </span><span> קלוריות </span><span style="color:green;" id="idDiffCal" dir="ltr"><i> (0+)</i></span></p> </div>
                    <div class="w3-cell" style="width:30%;"></div>
                </div>
                <div class="w3-cell-row">
                    <div class="w3-cell" style="width:5%;"></div>
                    <div class="w3-cell w3-center" style="w3-rest" dir="rtl"><p><span id="idTotalProtein">  </span><span> חלבונים </span></p> </div>
                    <div class="w3-cell" style="width:5%;"></div>
                    <div class="w3-cell w3-center" style="w3-rest" dir="rtl"><p><span id="idTotalCarb">  </span><span> פחמימות </span></p> </div>
                    <div class="w3-cell" style="width:5%;"></div>
                    <div class="w3-cell w3-center" style="w3-rest" dir="rtl"><p><span id="idTotalFat">  </span><span> שומנים </span></p> </div>
                    <div class="w3-cell" style="width:5%;"></div>
                </div>
                <br>
            </div>                                                             <!-- Date and Summary -->
            <br>
            <div>                                            <!-- Search Query -->
                <input type="search" dir="rtl" name="qr" id="qr"
                       placeholder="מה אכלתם היום?" value="" oninput="updateQRSuggestions()" 
                       aria-label="Search" autocomplete="off" style='width: 100%;' onfocusout="$('#qrpopover').hide();">

                <ul popover id="qrpopover" style="position:relative; inset:unset; top:40px"></ul>
            </div>                                                              <!-- Search Query -->

            <br>
            <br>
            <br>
            <div class="w3-row" style="background-color: darkslategrey;" id="divButtonsDisplay">
                <button class="w3-cell w3-button w3-left" style="color: lightgrey;font-weight: bold" onclick="switchButton(this)" id='butBrief'>תקציר</button>
                <button class="w3-cell w3-button w3-right" style="color: lightgrey;font-weight: bold" onclick="switchButton(this)" id='butFull'>מלא</button>
            </div>
            <br>
            <br>

            <div id="nutDataDiv"></div>
            <br>
            <br>
        </div>
        <br>
        <br>

        <script>

            function updateTables()
            {
                dateToAdd = document.getElementById("picker").value;
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    //console.log(this.readyState); //
                    //console.log(this.status); //
                    if (this.readyState == 4 && this.status == 200) {
                        //console.log(this.responseText);
                        document.getElementById('nutDataDiv').innerHTML = this.responseText;
                        prevCaloriesValue = $("#qr").data('prevCaloriesValue');
                        prevCaloriesValF = parseFloat(prevCaloriesValue);
                        $('#idDiffCal').hide();
                        curCaloriesValue = document.getElementById('tableNutValues').getAttribute('data-totalcal');
                        if (prevCaloriesValF >= 0)
                        {
                            curCaloriesValF = parseFloat(curCaloriesValue);
                            diffF = (curCaloriesValF - prevCaloriesValF).toFixed();
                            if (diffF > 0)
                            {
                                document.getElementById('idDiffCal').textContent = `(+${diffF})`;
                                document.getElementById('idDiffCal').style.color = `green`;
                                $('#idDiffCal').show();
                            } else if (diffF < 0)
                            {
                                document.getElementById('idDiffCal').textContent = `(-${Math.abs(diffF)})`;
                                document.getElementById('idDiffCal').style.color = `red`;
                                $('#idDiffCal').show();
                            }
                            //console.log("cal. diff="+diffF);
                        }
                        //$('#idDiffCal').hide(); // Tmp
                        //console.log("curCaloriesValue="+curCaloriesValue)
                        document.getElementById('idTotalCal').textContent = curCaloriesValue;
                        document.getElementById('idTotalProtein').textContent = document.getElementById('tableNutValues').getAttribute('data-protein') + '%';
                        document.getElementById('idTotalCarb').textContent = document.getElementById('tableNutValues').getAttribute('data-carb') + '%';
                        document.getElementById('idTotalFat').textContent = document.getElementById('tableNutValues').getAttribute('data-fat') + '%';


                    }
                };
                displayType = $('#divButtonsDisplay').data('selId');
                //console.log("displayType="+displayType);
                xmlhttp.open("GET", "updateDailyNutValues.php?date=" + dateToAdd + "&displayType=" + displayType, true);

                xmlhttp.send();
            }

            function docLoaded()
            {
                updateTables();
                $('#qr').focus();
                $('#butBrief').click();
                $("#qr").data('prevCaloriesValue', -1);
            }
            function isMobile() {
                const regex = /Mobi|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
                return regex.test(navigator.userAgent);
            }
            if (isMobile()) {
      //  console.log("Mobile device detected");
      //  document.getElementById("myH").innerHTML = "Mobile";
            } else {
      //  console.log("Desktop device detected");
      //  document.getElementById("myH").innerHTML = "Desktop";
            }

            function clearQR()
            {
                document.getElementById("qr").value = '';
                document.getElementById("qrpopover").innerHTML = '';
                $('#qr').data('selItem', '');
                $('#qr').data('quantity', 0);
                $('#qrpopover').hide();
            }

            function updateQRSuggestions() {
                // Get the value of the selected drop down
                let dropDownText = document.getElementById("qr").value;
                if (dropDownText.includes('מחק שורה'))
                {
                    clearQR();
                    return;
                }

                // TODO: consider avoid calling findItemDB if response.trim() is empty

//                xmlhttp.open("GET", "findItemDB.php?q=" + hebWordsInStr.join(' ') + "&isFull=0" + "&isStarCharInStr=" + isStarCharInStr + "&numDesiredQuantity=" + numDesiredQuantity +
//                        "&numbersInStr=" + numbersInStr, true);
                $.ajax({
                        url: 'findItemDB.php',
                        type: 'POST',
                        data: {
                                query: dropDownText
                              },
                        success: function(response) {
                                //$('#result').html(response);
                                //console.log(response);
                                //console.log(response.search('{'));
                                //json_str = response.substring(response.search('{'));
                                //console.log(json_str);
                                const obj = JSON.parse(response);
                                //console.log(obj);
                                //console.log("n_items_found="+obj.n_items_found);

                                if (obj.items.length === 0) // no item found
                                {
                                    document.getElementById("qrpopover").innerHTML = '';
                                    $('#qrpopover').hide();
                                    $('#qr').data('selItem', '');
                                    $('#qr').data('quantity', 0);
                                }
                                else
                                {
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
                                    let ind_perfect_match = -1; // index of perfect match between query and result if exists
                                    for (let i = 0; i < obj.items.length; i++) {
                                        const cur_item = obj.items[i];

                                        const caloriesActual = cur_item['_energy'] * numDesiredQuantity / 100;

                                        text += `<ul> ${cur_item['itemName']} [${caloriesActual} ${caloriesUnit} ${toWord} ${numDesiredQuantity} ${units}]</ul>`;
                                        
                                        if (cur_item['itemName'].trim() === obj.query_txt_only)
                                        {
                                            ind_perfect_match = i;
                                        }                                            
                                    }
                                    document.getElementById("qrpopover").innerHTML = text;
                                    $('#qrpopover').show();
                                    
                                    // one selection
                                    if ((obj.items.length === 1) && (obj.number_in_result === 1))
                                    {
                                        $('#qr').data('selItem', obj.items[0]['itemName']);
                                        $('#qr').data('quantity', numDesiredQuantity);
                                    }
                                    else
                                    {
                                        if ((ind_perfect_match >= 0) && (obj.number_in_result === 1))
                                        {
                                            $('#qr').data('selItem', obj.items[ind_perfect_match]['itemName']);
                                            $('#qr').data('quantity', numDesiredQuantity);                                            
                                        }
                                        else
                                        {
                                            $('#qr').data('selItem', '');
                                            $('#qr').data('quantity', 0);                                        
                                        }
                                    }
                                }
                            }
                        });                                
            }

            function qrSearchSubmitted() {
                // if (e.key === 'Enter' || e.keyCode === 13) {
                // TODO: get the data from the input directly and not the stored one.
                //      move the exact match option to the findItemDb, and check it there. Then call here for findItemDB
                let dropDownText = document.getElementById("qr").value;
                if (dropDownText.trim() == "")
                {
                    // No data
                    return;
                }
                
                console.log('submitted');
                $.ajax({
                        url: 'findItemDB.php',
                        type: 'POST',
                        data: {
                                query: dropDownText
                              },
                        success: function(response) {
                                const obj = JSON.parse(response);
                                //console.log(obj);
                                //console.log("n_items_found="+obj.n_items_found);

                                if (obj.items.length === 0) // no item found
                                {
                                    return;
                                }
                                else
                                {
                                    if (obj.number_in_result === 0) // No quantity specified -> ignore
                                    {
                                        return; 
                                    }
                                    let numDesiredQuantity = obj.required_quantity;
                                    let item_idx = -1; // index of perfect match between query and result if exists
                                    for (let i = 0; i < obj.items.length; i++) {
                                        const cur_item = obj.items[i];

//                                        const caloriesActual = cur_item['_energy'] * numDesiredQuantity / 100;
//
//                                        text += `<ul> ${cur_item['itemName']} [${caloriesActual} ${caloriesUnit} ${toWord} ${numDesiredQuantity} ${units}]</ul>`;
                                        
                                        if (cur_item['itemName'].trim() === obj.query_txt_only)
                                        {
                                            ind_perfect_match = i;
                                        }                                            
                                    }
                                   
                                    // one selection
                                    let flag_update = false;
                                    if ((obj.items.length === 1) && (obj.number_in_result === 1))
                                    {
                                        flag_update = true;
                                        item_idx = 0;
                                    }
                                    else
                                    {
                                        if ((ind_perfect_match >= 0) && (obj.number_in_result === 1))
                                        {
                                            flag_update = true;                                           
                                        }
                                        else
                                        {
                                            return;                                        
                                        }
                                    }
                                    if (flag_update)
                                    {
                                        itemToAdd = obj.items[item_idx]['itemName'];
                                        //['item']; ['date']; ['quantity']; ['mealTimeSlot']; ['time'];
                                        dateToAdd = document.getElementById("picker").value;
                                        quantity = numDesiredQuantity;
                                        mealTimeSlot = '';
                                        if ((itemToAdd.length > 0) && quantity > 0)
                                        {
                                            var xmlhttp = new XMLHttpRequest();
                                            xmlhttp.onreadystatechange = function () {
                                                //console.log(this.readyState); //
                                                //console.log(this.status); //
                                                //console.log("responseText="+this.responseText);
                                                if (this.readyState == 4 && this.status == 200) {
                                                    // console.log(this.responseText);
                                                    //console.log('updating table');
                                                    prevCaloriesValue = document.getElementById('tableNutValues').getAttribute('data-totalcal');
                                                    $("#qr").data('prevCaloriesValue', prevCaloriesValue);
                                                    updateTables();
                                                    //clearQR();
                                                    document.getElementById("qr").value = '';
                                                    document.getElementById("qrpopover").innerHTML = '';
                                                    $('#qr').data('selItem', '');
                                                    $('#qr').data('quantity', 0);
                                                    $('#qrpopover').hide();
                                                }
                                            };
                                            //console.log("itemToAdd=" + itemToAdd+', dateToAdd='+dateToAdd+', quantity='+quantity+', mealTimeSlot='+mealTimeSlot);
                                            xmlhttp.open("GET", "addDailyItemDB.php?item=" + itemToAdd + "&date=" + dateToAdd + "&quantity=" + quantity + "&mealTimeSlot=" + mealTimeSlot, true);

                                            xmlhttp.send();
                                        }
                                    }
                                }
                            }
                        }); 
            }

            let textqr = document.getElementById("qr");
            textqr.addEventListener("keyup", (e) => {
                // console.log(`Key "${e.key}" ["${e.keyCode}"] released [event: keyup]`);
                if (e.keyCode == 13)
                {
                    qrSearchSubmitted();
                }
            });

            function removeItemFromDaily(itemIndex, strTxt)
            {
                //console.log("remove "+itemIndex);
                retValue = confirm("האם אתה בטוח שתרצה להסיר את המוצר הבא?\n" + strTxt);
                if (retValue == true) // Remove
                {
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function () {
                        //console.log(this.readyState); //
                        //console.log(this.status); //
                        if (this.readyState == 4 && this.status == 200) {
                            // console.log(this.responseText);
                            //console.log('updating table');
                            // console.log('delete');
                            prevCaloriesValue = document.getElementById('tableNutValues').getAttribute('data-totalcal');
                            $("#qr").data('prevCaloriesValue', prevCaloriesValue);
                            updateTables();
                        }
                    };
                    xmlhttp.open("GET", "removeDailyItemDB.php?itemIndex=" + itemIndex, true);

                    xmlhttp.send();
                }
            }
            function editItemFromDaily(itemIndex, strTxt, quantity)
            {
                // console.log("edit "+itemIndex);
                newQuantity = prompt(strTxt + " [גרם] ", quantity.toString());
                if (newQuantity != null) // Do the edit
                {
                    // console.log("edit "+itemIndex+" = "+newQuantity);
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function () {
                        //console.log(this.readyState); //
                        //console.log(this.status); //
                        if (this.readyState == 4 && this.status == 200) {
                            // console.log(this.responseText);
                            //console.log('updating table');
                            // console.log('delete');
                            prevCaloriesValue = document.getElementById('tableNutValues').getAttribute('data-totalcal');
                            $("#qr").data('prevCaloriesValue', prevCaloriesValue);
                            updateTables();
                        }
                    };
                    xmlhttp.open("GET", "updateDailyItemDB.php?itemIndex=" + itemIndex + "&newQuantity=" + newQuantity, true);

                    xmlhttp.send();
                }
            }
            function clearButtons(list)
            {
                for (let bElem of list) {
                    bElem.style.backgroundColor = 'darkslategrey';
                    bElem.style.color = 'white';
                    //console.log(bElem.id);
                }
            }
            function switchButton(buttonElement)
            {
                //console.log(buttonElement.id);
                let list = buttonElement.parentElement.children;
                clearButtons(list);
                buttonElement.style.backgroundColor = 'black';
                buttonElement.style.color = 'white';
                $('#divButtonsDisplay').data('selId', buttonElement.id);
                //console.log("$('#divButtonsDisplay').data('selId'="+$('#divButtonsDisplay').data('selId'));

                updateTables();
            }
      //
      //window.onload = function() {
      //  docLoaded();
      //  alert('doc loaded');
      //};

        </script>
    </body>
