<!DOCTYPE html>
<html>
<head>
    <title>Popup Input Dialog</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#openDialog").click(function(){
                $("#dialog").dialog();
            });
        });
    </script>
</head>
<body>
<button id="openDialog">Open Dialog</button>
<div id="dialog" title="Input Dialog">
    <form method="post" action="">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br><br>
        <input type="submit" value="Submit">
    </form>
</div>
</body>
</html>
