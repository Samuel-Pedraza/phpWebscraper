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
              <?php
                require_once "Classes/PHPExcel.php";

                $tmpfname = "manufactPrices/aignerIndex.xlsx";
                $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
                $excelObj = $excelReader->load($tmpfname);
                $worksheet = $excelObj->getActiveSheet();
                $lastRow = $worksheet->getHighestRow();

                  echo "<table style='border: 1px solid black;'>
                    <tr style='font-weight: 800; font-size: 110%;'>
                      <td style='border: 1px solid black;'>MODEL NUMBER</td>
                      <td style='border: 1px solid black;'>PART NUMBER</td>
                      <td style='border: 1px solid black;'>OUR PRICE</td>
                      <td style='border: 1px solid black;'>RECOMMENDED PRICE</td>
                      <td style='border: 1px solid black;'>LIST PRICE</td>
                      <td style='border: 1px solid black;'>OUR COST</td>
                      <td style='border: 1px solid black;'>COMPETITOR</td>
                      <td style='border: 1px solid black;'>COMP.- OUR PRICE</td>
                      <td style='border: 1px solid black;'>COMP.- OUR COST</td>
                      <td style='border: 1px solid black;'>COMPETITOR NAME</td>
                    </tr>";

                    for ($row = 12; $row <= $lastRow; $row++) {
                        echo '<tr><td style="border: 1px solid black;">';
                        echo $worksheet->getCell('C'.$row)->getValue();
                        echo '</td><td style="border: 1px solid black;">';
                        echo $worksheet->getCell('D'.$row)->getValue();
                        echo '</td><td value"' . $row['partPrice'] . '">' . $row['partPrice'] . '</td>
                              <td>' . NULL . '</td>';
                        echo '<td style="border: 1px solid black;">$';
                        echo $worksheet->getCell('K'.$row)->getValue();
                        echo '</td><td style="border: 1px solid black;">';
                        echo $worksheet->getCell('L'.$row)->getValue();
                        echo '</td>
                              <td>' . NULL . '</td>
                              <td>' . NULL . '</td>
                              <td>' . NULL . '</td>
                              <td>' . NULL . '</td>
                              <td>' . NULL . '</td>
                            </tr>';
                    }
                  echo "</table>";
                ?>
                <?php

                    //creat PHPExcel object
                    $excel = new PHPExcel();

                    //insert some data to PHPExcel object
                    $excel->setActiveSheetIndex(0)
                      ->setCellValue('A1','Hello')
                      ->setCellValue('B1','Neo...');

                      //write the result to a file
                    $file = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
                    $file->save('excelMonkey.xlsx');

                ?>
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
