<?php

require_once "Classes/PHPExcel.php";

	$excelSheets = array();
	
	$tmpfname = "manufactPrices/valleyCraft.xlsx";
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
		for ($row = 2; $row <= $lastRow; $row++) {
			echo "<tr><td style='border: 1px solid black;'>";
			echo $worksheet->getCell('A'.$row)->getValue();
			echo "</td><td style='border: 1px solid black;'>";
			echo $worksheet->getCell('D'.$row)->getValue();
			echo "</td><td style='border: 1px solid black;'>";
			echo $worksheet->getCell('E'.$row)->getValue();
			echo "</td><tr>";
		}
		echo "</table>";

?>