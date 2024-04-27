<?php

class CheckChecker
{
    const FILE_PATH = __DIR__ . '/check-items.txt';
		
    public static function getAllChecks()
    {
        $result = array();


        $fileJson = file_get_contents(static::FILE_PATH);
        $fileJson = str_replace(array("\r\n", "\r", "\n"), '',  strip_tags($fileJson));
        $fileJson = rtrim($fileJson, ",");
        $arItems = json_decode('[' . $fileJson . ']', true);

        if( $arItems && count($arItems) > 0) $result = $arItems;
        
        return $result;

    }

    public static function setCheck($data)
    {
        
        if( $data['DATE'] && $data['PRICE'] ){

            $item = '{"TYPE":"' . $data['TYPE'] . '", "TYPE_COMMENT":"' . $data['TYPE_COMMENT'] . '", "DATE":"' . $data['DATE'] . '","PRICE":' . $data['PRICE'] . '},';
            file_put_contents(static::FILE_PATH, $item . PHP_EOL, FILE_APPEND);

            return true;

        } 
        
        return false;
    }
}