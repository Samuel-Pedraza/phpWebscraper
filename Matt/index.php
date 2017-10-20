<?php
    require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Data Graph</title>
    <link rel="stylesheet" type="text/css" href="main.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php
    if(empty($_POST["manufacturer"])) { ?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Data Graph!!!</h1>
                <form method="POST" action="landingPageTest.php">
                    <p align="center">
                        <select name="manufacturer">
                            <?php

                                $var = $GLOBALS["result"];

                                while($row = mysqli_fetch_assoc($var)) {
                                    echo '<option value"' . $row['manufactName'] . '">' . $row['manufactName'] . '</option>';
                                }
                            ?>
                        </select>
                        <input type="submit">
                    </p>
                </form>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">


        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">

        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">

        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<?php } else {
            echo $_POST["manufacturer"];
 } ?>
</body>
</html>