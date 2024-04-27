<?
require_once ("../library/SimpleXLSX.php"); 
use Shuchkin\SimpleXLSX;

$arResult['result'] = false;
$col_id = $_POST["COL_ID"];
$col_date = $_POST["COL_DATE"];
$col_price = $_POST["COL_PRICE"];

if( $_FILES['file']['tmp_name'] && $_FILES['file']["type"] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ){
    
    $tempFile = '../library/files/temp.xlsx';
    $mainFile = '../library/files/table.xlsx';

    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
    if (file_exists($mainFile)) {
        unlink($mainFile);
    }
    
    
    move_uploaded_file($_FILES['file']['tmp_name'], $mainFile);

    if( $col_id && $col_date && $col_price ){
        $item = '{"COL_ID":"' . $col_id . '", "COL_DATE":"' . $col_date . '", "COL_PRICE":"' . $col_price . '"}';

        $tableSettings = '../library/files/table-settings.txt';
        if (file_exists($tableSettings)) {
            unlink($tableSettings);
        }

        file_put_contents('../library/files/table-settings.txt', $item . PHP_EOL);
    }

    $arResult['result'] = true;
}


header('Content-Type: application/json');	
echo json_encode($arResult);