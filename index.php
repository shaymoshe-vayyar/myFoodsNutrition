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
<html lang="he" >
<head>
  <!--  <meta content="text/html; charset=windows-1255" http-equiv="Content-Type">-->
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
  <title>myDiary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3pro.css">
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-teal.css">

  <!-- [ Bootstraps -->
  <script src ="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src ="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js" integrity="sha512-42PE0rd+wZ2hNXftlM78BSehIGzezNeQuzihiBCvUEB3CVxHvsShF86wBWwQORNxNINlBPuq7rG4WWhNiTVHFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src ="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
  <link rel ="stylesheet" href ="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel ="stylesheet" href ="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
  <!--   Bootstraps ] -->

  <!-- Script -->
  <script>
</script>
  <!-- Script -->

</head>
<body onload="docLoaded()" style="background-color:#dcd7d3">

<nav class="w3-sidebar w3-bar-block w3-card" id="mySidebar">
  <div class="w3-container w3-theme-light-blue">
    <span onclick="closeSidebar()" class="w3-button w3-display-topright w3-large">X</span>
    <br>
  </div>
  <a class="w3-bar-item w3-button" href="#">Diary</a>
  <a class="w3-bar-item w3-button" href="#">Settings</a>
</nav>

<header class="w3-top w3-bar ">
  <button class="w3-bar-item w3-button w3-xxxlarge w3-hover-theme" onclick="openSidebar()">&#9776;</button>
  <h1 class="w3-bar-item">Diary</h1>
</header>

<div class="w3-container" style="margin-top:90px">
  <hr>
  <div class="w3-cell-row">
    <div class="container">
      <div class="row" > <!-- date and summary -->
        <div class="col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1" style="background-color:#ffffff">
          <p class="row"></p>
          <p class="row"></p>
          <div class="row">
            <div class='col-xs-6 col-sm-4 col-xs-offset-3 col-sm-offset-4'>
              <!--          <button class="w3-button w3-circle w3-black"><</button>-->
              <input type='text' class="form-control" id='picker' style="text-align:center;" >
              <!--          <button class="w3-button w3-circle w3-black">></button>-->
            </div>
            <p class="row"></p>
            <p class="row"></p>
            <p class="row"></p>
            <p class="row"></p>
            <p class="row"></p>
          </div>
        </div>
        <script type="text/javascript">
         $(function () {
             $('#picker').datetimepicker({viewMode: 'days', format: 'DD/MM/YYYY', useCurrent : true,
             showTodayButton : true,
             defaultDate : new Date()});
             $("#picker").on("dp.change", function (e) {
            $('#qr').focus();
        });
         });
      </script>
      </div> <!-- date and summary -->
      <p></p>                     <!-- Blank -->
      <div class="row">    <!-- Search Query -->
        <div class="col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1" style="padding: 0;">
          <div class="search-div" style="padding: 15px 0 !important;">
            <form>
              <div class="form-group">
                <input type="search" dir="rtl" text-align="right" name="qr" id="qr" data-typecmd="1" placeholder="מה אכלתם היום?" value="" oninput="updateQRSuggestions()"  aria-label="Search" autocomplete="off" style='width: 100%;' onfocusout="$('#qrpopover').hide();">

                  <ul popover id="qrpopover" style="position:relative; inset=unset;top=40px">
                  </ul>
<!--                  <datalist id="ListName" dir="rtl" text-align="right">-->
<!--                  <option value="מלפפון"></option>-->
<!--                  <option value="עגבניה">Dry fish</option>-->
<!--                  <option value="עגבנית שרי">Palm oil</option>-->
<!--                </datalist>-->
                <!--                <button type="button" class="btn control-no-focus btn-clean-search" style="display: none;"><i class="fas fa-times">&#xf00d;</i></button>-->
                <!--                <button type="submit" class="btn control-no-focus"><i class="far fa-search">&#xf002;</i></button>-->
              </div>
            </form>
          </div>
          <div id="searchResultsDiv" style="width: 100%;"></div>
        </div>
      </div>       <!-- Search Query -->
    </div>
    <br>
    <hr>
  </div>

  <footer class="w3-container w3-bottom w3-margin-top">
    <h3>Footer</h3>
  </footer>
  <script>
    closeSidebar();
function openSidebar() {
  document.getElementById("mySidebar").style.display = "block";
}

function closeSidebar() {
  document.getElementById("mySidebar").style.display = "none";
}
function docLoaded()
{
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
function updateQRSuggestions() {
    //console.log('hello')
    //alert('hello')
    // Get the value of the selected drop down
    var dropDownText = document.getElementById("qr").value;
    // If selected text matches 'Other', display the text field.

    if (dropDownText == "") {
        document.getElementById("qrpopover").innerHTML = '';
        return;
      } else {
        numbersInStr = dropDownText.match(/\b(\d+\.?\d?)\b/g)
        engWordsInStr = dropDownText.match(/\b[^\d\W]+\b/g)
        hebWordsInStr = dropDownText.match(/[\u0590-\u05FF]+/g)
        if ( (hebWordsInStr != null) && (hebWordsInStr.length > 0) )
        {
        console.log('------------------------------');
        console.log(hebWordsInStr.join(' ')); //
        console.log('------------------------------');
        }
        numDesiredQuantity = 100;
        if ((numbersInStr != null) && (numbersInStr.length>0)) {
            if (numbersInStr.length > 1)
            {
                console.log('error, too many numbers');
            }
            else
            {
                numDesiredQuantity = parseInt(numbersInStr[0])
                console.log('------------------------------');
                console.log(numDesiredQuantity); //
                console.log('------------------------------');
            }
        }
        if ( (hebWordsInStr != null) && (hebWordsInStr.length > 0) )
        {
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
                          }
                      }
                      $('#qrpopover').show();
                  }
                  else
                  {
                      $('#qrpopover').hide();
                  }
                  console.log(text);
                  //document.getElementById("ListName").innerHTML = text;
                  document.getElementById("qrpopover").innerHTML = text;
              }
          };
            //xmlhttp.open("GET","./phpFiles/getDataDB.php?q="+hebWordsInStr.join(' '),true);
            xmlhttp.open("GET","getDataDB.php?q="+hebWordsInStr.join(' '),true);

          xmlhttp.send();
        }
        else
        {
            document.getElementById("qrpopover").innerHTML = '';
        }
    }
}

  </script>
</body>
