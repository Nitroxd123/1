<?php

require_once ("../library/PX/PHPExcel.php"); 
require_once ("../library/CheckChecker.php");
require_once ("../library/SimpleXLSX.php");

$filePath = '../library/files/table.xlsx';
$settingsPath = '../library/files/table-settings.txt';
$downloadFilePath = '../library/files/temp-download.xlsx';

use Shuchkin\SimpleXLSX;

$allItems = CheckChecker::getAllChecks();
if (file_exists($filePath) && file_exists($settingsPath)) {
	$xlsx = SimpleXLSX::parse($downloadFilePath);

	$xlsxAllItems = array();
	$activeCount = 0;


	$fileJson = file_get_contents($settingsPath);
    $arSettings = json_decode($fileJson, true);

	if( count($arSettings) === 3 ){
		foreach( $xlsx->rows() as $rowNum => $r ){
			if( in_array($rowNum, array(0, 1)) ) continue;
		
            $xlsxAllItems[$rowNum]['ROW'] = $rowNum;
			$xlsxAllItems[$rowNum]['ID'] = $r[$arSettings['COL_ID']];
			$xlsxAllItems[$rowNum]['DATE'] = $r[$arSettings['COL_DATE']];
			$xlsxAllItems[$rowNum]['PRICE'] = $r[$arSettings['COL_PRICE']];
			$xlsxAllItems[$rowNum]['ACTIVE'] = false;
		
			$date = date('Ymd\THi', strtotime($r[6]));
			foreach( $allItems as $arItem ){
		
				if( $date === $arItem['DATE'] && floatval($arItem['PRICE']) === floatval($xlsxAllItems[$rowNum]['PRICE']) ){
		
					$xlsxAllItems[$rowNum]['ACTIVE'] = true;
					$activeCount++;
				}
			}
		}

        $unsetItems = array();
        foreach( $xlsxAllItems as $item ) {
            if( !$item['ACTIVE'] ) {
                $unsetItems[$item['ROW']] = $item['ID'];
            }
        }
	}
}





/*
if (file_exists($downloadFilePath)) {
    unlink($downloadFilePath);
}
copy($filePath, $downloadFilePath);
*/


/*
foreach( $unsetItems as $rowNum => $itemId ) {
        $res = $xlsxFastEditor->deleteRow($worksheetId1, $rowNum);

        var_dump($rowNum . '  ' . $res);
    }
*/    

$fileType = 'Excel5';
$objReader = PHPExcel_IOFactory::createReader($fileType);
$objPHPExcel = $objReader->load($filePath);


foreach( $unsetItems as $rowNum => $itemId ) {
    $objPHPExcel->getActiveSheet()->removeRow($rowNum);
}



// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="active-items.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;