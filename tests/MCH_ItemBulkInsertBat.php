<?php
require_once "framework/Bootstrap/autoload.php";
require_once "JoyPla/require.php";

use framework\SpiralConnecter\SpiralApiConnecter;
use framework\SpiralConnecter\SpiralDB;
use JoyPla\Application\LoggingObject\Spiralv2LogginObject;

const TOKEN = '00011KB9HzJA6571fc2a62048af337abb32cbab1e0dfa3c8aadb';
const SECRET = 'f2bf4a8e7b3567fdba896429ea1c136e89320175';

const LOG_LEVEL = 3;
const EXPORT_TO_SPIRALV2 = true; // SPIRALv2オブジェクトで出力する
const SPIRAL_API_LOGGING_DB_TITLE = '73308'; // SPIRALv1v2オブジェクトで出力する場合に設定するDBタイトル
const JOYPLA_API_LOGGING_DB_TITLE = '73304'; // SPIRALv1v2オブジェクトで出力する場合に設定するDBタイトル
const LOGGING_APP_TITLE = '24083'; // SPIRALv1v2オブジェクトで出力する場合に設定するAPPタイトル
const SPIRALV2_API_KEY = 'dGFvQlZ9VUU4emE4TDMwbnp4T0hiUiRd'; // SPIRALv1v2オブジェクトで出力する場合に設定するAPPタイトル

SpiralApiConnecter::$logger = new Logger( new Spiralv2LogginObject( SPIRALV2_API_KEY , LOGGING_APP_TITLE ,SPIRAL_API_LOGGING_DB_TITLE  ) );

$tenantId = 'tenant_0000000001';

$insertData = [];

//読み込み("r")でファイルを指定
$fo = fopen("../tests/test.txt", "r");

if (true == $fo) {
    //ファイル内の列を配列形式で$dataに格納する
    //tsvファイルの場合は「fgetcsv($fo, 0, "\t")」とする
    while ($data = fgetcsv($fo, 0, "\t")) {
    //$dataに対する処理がある場合はここに記載
    mb_convert_variables('UTF-8', 'SJIS-win', $data);
        if($data[4] === '' && $tenantId !== $data[7] )
        {
            continue;
        }
        $insertData[] = 
        [
            'itemName' => $data[1],
            'itemCode' => $data[2],
            'itemStandard' =>  $data[3],
            'itemJANCode' =>  $data[4],
            'makerName' =>  $data[5],
            'catalogNo' =>  $data[21],
            'minPrice' =>  $data[11],
            'officialFlag' =>  $data[6],
            'officialprice' =>  $data[20],
            'quantity' =>  $data[8],
            'quantityUnit' =>  $data[9],
            'itemUnit' =>  $data[10],
            'tenantId' =>  $tenantId,
            'itemId' =>  '',
            'officialpriceOld' =>  $data[19],
            'lotManagement' =>  $data[23],
            'category' =>  $data[24],
            'itemsAuthKey' =>  '',
            'smallCategory' =>  $data[27],
        ];
    }
}
//読み込んでいたファイルを閉じる
fclose($fo);

SpiralDB::setToken(TOKEN,SECRET);
$db = SpiralDB::title('NJ_itemDB');
unset($insertData[0]);//header削除
$insertData = array_values($insertData);

//test
$insertData = array_chunk($insertData , 50000);

$chunk = array_chunk($insertData[0] , 5000);

foreach($chunk as $data){

    $instance = $db->reInstance();
    $instance = $instance->where('tenantId','tenant_0000000001', '=');
    echo "[START ".date('Y-m-d H:i:s')." ] data select".PHP_EOL;

    $whereIn = [];
    foreach($data as $insert)
    {
        $whereIn[] = $insert['itemJANCode'].$tenantId;
    }

    $instance->whereIn('janTenantId',$whereIn);
    $record = $instance->getMulti(
        [
            'janTenantId',
            'itemId',
            'itemsAuthKey',
        ]
    );
    
    echo "[INFO ".date('Y-m-d H:i:s')." ] ".$record->count() ." records".PHP_EOL;

    $insert = [];
    foreach($data as $key => $inst)
    {
        $item = $record->where('janTenantId',$inst['itemJANCode'].$tenantId)->first();

        if(is_null($item))
        {
            $insert[] = $inst;
            continue;
        }
        $inst['itemId'] = $item->itemId;
        $inst['itemsAuthKey'] = $item->itemsAuthKey;
        $insert[] = $inst;
    }

    echo "[INFO ".date('Y-m-d H:i:s')." ] INSERT start.".PHP_EOL;

    //SpiralDB::title('T_itemBulkUpsert')->insert($insert);

    echo "[INFO ".date('Y-m-d H:i:s')." ] INSERT end.".PHP_EOL;

    sleep(30);
}
echo "[FINISH ".date('Y-m-d H:i:s')." ] end.".PHP_EOL;
