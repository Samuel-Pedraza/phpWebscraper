<?php

require_once 'Classes/PHPExcel.php';

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