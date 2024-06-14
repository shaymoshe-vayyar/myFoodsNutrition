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
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'  as="style" onload="this.onload=null;this.rel='stylesheet'">
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
<header class="w3-main w3-cell-row" >
    <button class="w3-button w3-cell w3-xlarge w3-right w3-hover-theme" style="float:right;" onclick='document.getElementById("mySidebar").style.display = "block";'>&#9776;</button>
    <h2 class="w3-cell w3-right" style="float:right; margin: 5px;">הוספת מאכל</h2>
</header>
<hr>
<div class="w3-container w3-row" style="position: relative;">
        <br>
        <div>                                            <!-- Search Query -->
            <input type="search" dir="rtl" name="qr" id="qr"
                   placeholder="מה תרצו להוסיף?" value="" oninput="updateQRSuggestions(event)" onsearch="qrSearchSubmitted()"
                   aria-label="Search" autocomplete="off" style='width: 100%;' onfocusout="$('#qrpopover').hide();">

            <ul popover id="qrpopover" style="position:relative; inset:unset; top:40px"></ul>
        </div>                                                              <!-- Search Query -->
</div>
<a id="mytxt1">aaaaa</a>
        <?php
        $a = "123";
        echo 'hello';
        ?>
<script>
    function updateQRSuggestions(e)
    {
        console.log(e.data);
        document.getElementById("mytxt1").text = e.data;
    }
</script>
    </body>
</html>
