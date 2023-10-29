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

<html lang="he" style="font-size: 16px;">
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
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'  as="style" onload="this.onload=null;this.rel='stylesheet'">
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
<body onload="docLoaded()" style="background-color:#dcd7d3;">

<hr>
<nav class="w3-sidebar w3-bar-block w3-card-4" id="mySidebar" style="display: none;right:5px;">
    <button onclick='document.getElementById("mySidebar").style.display = "none";' class="w3-button w3-display-topright w3-large">X</button>
    <h3 class="w3-bar-item w3-button" href="#">יומן מעקב יומי</h3>
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
                        $('#picker').datetimepicker({viewMode: 'days', format: 'YYYY-MM-DD', useCurrent : true,
                            showTodayButton : true,
                            widgetPositioning: {
                                horizontal: 'auto',
                            },
                            defaultDate : new Date()});
                        $("#picker").on("dp.change", function (e) {
                            updateTables();
                            $('#qr').focus();
                        });
                    });
                </script>
                <div class="w3-cell" style="width:30%"></div>
            </div>
            <br>
            <br>
        </div>                                                             <!-- Date and Summary -->
        <br>
        <div>                                            <!-- Search Query -->
            <input type="search" dir="rtl" name="qr" id="qr"
                   placeholder="מה אכלתם היום?" value="" oninput="updateQRSuggestions()" onsearch="qrSearchSubmitted()"
                   aria-label="Search" autocomplete="off" style='width: 100%;' onfocusout="$('#qrpopover').hide();">

            <ul popover id="qrpopover" style="position:relative; inset=unset;top=40px"></ul>
        </div>                                                              <!-- Search Query -->

        <br>
        <br>
        <br>
        <div class="w3-row" style="background-color: darkslategrey;">
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
      // window.onload = function () {
      //     var span = document.createElement('span');
      //
      //     // <i class="fa-regular fa-pen-to-square"></i>
      //     span.className = 'fa';
      //     span.style.display = 'none';
      //     document.body.insertBefore(span, document.body.firstChild);
      //
      //     alert(window.getComputedStyle(span, null).getPropertyValue('font-family'));
      //
      //     document.body.removeChild(span);
      // };
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
        }
    };
    xmlhttp.open("GET", "updateDailyNutValues.php?date=" + dateToAdd, true);

    xmlhttp.send();
}

function docLoaded()
{
    updateTables();
    $('#qr').focus();
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
    $('#qr').data('selItem','');
    $('#qr').data('quantity',0);
    $('#qrpopover').hide();
}


function updateQRSuggestions() {
    //console.log('hello')
    //alert('hello')
    // Get the value of the selected drop down
    var dropDownText = document.getElementById("qr").value;
    // If selected text matches 'Other', display the text field.

    // document.getElementById('qr').data-fullnamesuggest = '';
    // console.log("data="+$('#qr').data('selItem'));
    $('#qr').data('selItem','');
    $('#qr').data('quantity',0);
    if (dropDownText == "") {
        document.getElementById("qrpopover").innerHTML = '';
        return;
      } else {
        numbersInStr = dropDownText.match(/\b(\d+\.?\d?)\b/g)
        engWordsInStr = dropDownText.match(/\b[^\d\W]+\b/g)
        hebWordsInStr = dropDownText.match(/[\u0590-\u05FF]+/g)

        numDesiredQuantity = 100;
        if ((numbersInStr != null) && (numbersInStr.length>0)) {
            if (numbersInStr.length > 1)
            {
                console.error('too many numbers');
            }
            else
            {
                numDesiredQuantity = parseFloat(numbersInStr[0])
                // console.log('------------------------------');
                // console.log(numDesiredQuantity); //
                // console.log('------------------------------');
            }
        }
        if ( (hebWordsInStr != null) && (hebWordsInStr.length > 0) )
        {
            retAtTheEnd = false;
            if (hebWordsInStr.join(' ').includes('מחק שורה'))
            {
                // console.log(hebWordsInStr.join(' ').includes('מחק שורה'));
                clearQR();
                return;
            }
            if (hebWordsInStr.includes('הכנס'))
            {
                // console.log(hebWordsInStr.join(' '));
                hebWordsInStr = hebWordsInStr.filter(hebWordsInStr =>  hebWordsInStr != 'הכנס');
                // console.log(hebWordsInStr.join(' '));
                retAtTheEnd = true;
            }

            //const mypopover = document.getElementById("qrpopover");

            //mypopover.style.top = '100px';
          var xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
              //console.log(this.readyState); //
              //console.log(this.status); //
              if (this.readyState == 4 && this.status == 200) {
                  //console.log(this.responseText);
                  text = '';
                  if (this.responseText.length > 1)
                  {
                      arrOptions = this.responseText.split(';')
                      const units = 'גרם';
                      const caloriesUnit = 'קלוריות';
                      const toWord = 'ל';
                      for (let i = 0; i < arrOptions.length; i++) {
                          if (arrOptions[i].length > 0)
                          {
                              arrPair = arrOptions[i].split(','); // Name, Calories
                              const name = arrPair[0]
                              const caloriesActual = parseFloat(arrPair[1])*numDesiredQuantity/100;
                               //text += `<option> ${name} [${caloriesActual} ${caloriesUnit} ${toWord} ${numDesiredQuantity} ${units}]</option>`;
                              text += `<ul> ${name} [${caloriesActual} ${caloriesUnit} ${toWord} ${numDesiredQuantity} ${units}]</ul>`;
                              if (arrOptions.length == 2) // Only one suggestion
                              {
                                  //console.log("data="+$('#qr').data('selItem'));
                                  $('#qr').data('selItem',name);
                                  $('#qr').data('quantity',numDesiredQuantity);
                                  if (retAtTheEnd)
                                  {
                                      qrSearchSubmitted();
                                  }
                              }
                          }
                      }
                      $('#qrpopover').show();
                  }
                  else
                  {
                      $('#qrpopover').hide();
                  }
                  // console.log(text);
                  //document.getElementById("ListName").innerHTML = text;
                  document.getElementById("qrpopover").innerHTML = text;
              }
          };
            //xmlhttp.open("GET","./phpFiles/findItemDB.php?q="+hebWordsInStr.join(' '),true);
            xmlhttp.open("GET","findItemDB.php?q="+hebWordsInStr.join(' ')+"&isFull=0",true);

          xmlhttp.send();
        }
        else
        {
            //clearQR();
             document.getElementById("qrpopover").innerHTML = '';
             $('#qr').data('selItem','');
             $('#qr').data('quantity',0);
        }
    }
}
function qrSearchSubmitted() {
    // if (e.key === 'Enter' || e.keyCode === 13) {
    //console.log('submitted');
    itemToAdd = $('#qr').data('selItem');
    //['item']; ['date']; ['quantity']; ['mealTimeSlot']; ['time'];
    dateToAdd = document.getElementById("picker").value;
    quantity = $('#qr').data('quantity');
    mealTimeSlot = '';
    // console.log("itemToAdd=" + itemToAdd);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        //console.log(this.readyState); //
        //console.log(this.status); //
        if (this.readyState == 4 && this.status == 200) {
            // console.log(this.responseText);
            //document.getElementById('blabla').textContent = this.responseText;
            //console.log('updating table');
            updateTables();
            //clearQR();
            document.getElementById("qr").value = '';
            document.getElementById("qrpopover").innerHTML = '';
            $('#qr').data('selItem','');
            $('#qr').data('quantity',0);
            $('#qrpopover').hide();
        }
    };
    xmlhttp.open("GET", "addDailyItemDB.php?item=" + itemToAdd + "&date=" + dateToAdd + "&quantity=" + quantity + "&mealTimeSlot=" + mealTimeSlot, true);

    xmlhttp.send();
    // }
}

    function removeItemFromDaily(itemIndex,strTxt)
    {
        //console.log("remove "+itemIndex);
        retValue = confirm("האם אתה בטוח שתרצה להסיר את המוצר הבא?\n"+strTxt);
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
                    updateTables();
                }
            };
            xmlhttp.open("GET", "removeDailyItemDB.php?itemIndex=" + itemIndex, true);

            xmlhttp.send();
        }
    }
      function editItemFromDaily(itemIndex,strTxt,quantity)
      {
          // console.log("edit "+itemIndex);
          newQuantity = prompt(strTxt+" [גרם] ",quantity.toString());
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
        bElem.style.backgroundColor='#FFFFFF';
        bElem.style.color='#000000';
        console.log(bElem.id);
    }
}
function switchButton(buttonElement)
{
    console.log(buttonElement.id);
    let list = buttonElement.parentElement.children;
    clearButtons(list);
    buttonElement.style.backgroundColor = 'lightgreen';
    buttonElement.style.color = 'gray';
}
  </script>
</body>
