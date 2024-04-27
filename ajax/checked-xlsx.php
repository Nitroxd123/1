<?
require_once ("../library/SimpleXLSX.php"); 
use Shuchkin\SimpleXLSX;

$arResult['result'] = false;

if( $_FILES['file']['tmp_name'] && $_FILES['file']["type"] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ){
    $properPath = '../library/files/temp.xlsx';
    move_uploaded_file($_FILES['file']['tmp_name'], $properPath);

    if( $xlsx = SimpleXLSX::parse($properPath) ){
        foreach( $xlsx->rows() as $rowNum => $r ){
            if( !in_array($rowNum, array(0, 1)) || $r === '' ) continue;

            $arColumns = $r;
        }

        if( $arColumns ) {

            $returnStr = '';
            $arOptions = '<option value="">Не выбрано</option>';
            foreach( $arColumns as $key => $arCol ){
                $arOptions .= '<option value="' . $key . '">' . $arCol . '</option>';
            }

            $returnStr .= '<label>
                                <span>Столбик отвечающий за ID<i>*</i></span>
                                <select name="col_id" required>'
                                    . $arOptions .
                                '</select>
                          </label>';

            $returnStr .= '<label>
                                <span>Столбик отвечающий за Дату<i>*</i></span>
                                <select name="col_date" required>'
                                    . $arOptions .
                                '</select>
                          </label>';


            $returnStr .= '<label>
                                <span>Столбик отвечающий за Цену<i>*</i></span>
                                <select name="col_price" required>'
                                    . $arOptions .
                                '</select>
                          </label>';

            $arResult = array(
                'result' => true,
                'html' => $returnStr
            );
        }
    }
}


header('Content-Type: application/json');	
echo json_encode($arResult);