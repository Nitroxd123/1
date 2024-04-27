<?
require_once("../library/CheckChecker.php");

$arResult = false;
$date = $_GET['DATE'];
$time = $_GET['TIME'];
$price = $_GET['PRICE'];
$type = $_GET['TYPE'] ?? '';
$typeComment = $_GET['TYPE_COMMENT'] ?? '';

if( $date && $time && $price ){

    $date = date('Ymd', strtotime($_GET['DATE']));
    $date .= 'T' . str_replace(':', '',  $time);
    $price = floatval(str_replace(',', '.',  $price));

    
    $arResult = CheckChecker::setCheck(
        array(
            'TYPE' => $type,
            'DATE' => $date,
            'PRICE' => $price,
            'TYPE_COMMENT' => $typeComment,
        )
    );
}


header('Content-Type: application/json');	
echo json_encode($arResult);
?>