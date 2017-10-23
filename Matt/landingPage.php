<?php require_once 'dataQuery.php'; ?>

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
<div class="container">
    <div class="row">
        <div class="col-sm-12 text-center">
            <h1><?php echo $_POST["manufacturer"] ?>!!!</h1>
                <table>
                    <tr style="font-weight: bold; text-align: center;">
                              <td>Model Number</td>
                              <td>Part Number</td>
                              <td>Our Price</td>
                              <td>Recommended Price</td>
                              <td>List Price</td>
                              <td>Our Cost</td>
                              <td>Profit</td>
                              <td>Competitor Price</td>
                              <td>Comp.-Our Price</td>
                              <td>Comp./Our Cost</td>
                              <td>Competitor Name</td>
                            </tr>

                          <?php

                            $var = $GLOBALS["result"];

                            while($row = mysqli_fetch_assoc($var)) {
                                echo '<tr>
                                        <td value"' . $row['name'] . '">' . $row['name'] . '</td>
                                        <td>' . NULL . '</td>
                                        <td value"' . $row['partPrice'] . '">' . $row['partPrice'] . '</td>
                                        <td>' . NULL . '</td>
                                        <td>' . NULL . '</td>
                                        <td>' . NULL . '</td>
                                        <td>' . NULL . '</td>
                                        <td>' . NULL . '</td>
                                        <td>' . NULL . '</td>
                                        <td>' . NULL . '</td>
                                        <td>' . NULL . '</td>
                                      </tr>';
                            }

                          ?>
                </table>
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
</body>
</html>