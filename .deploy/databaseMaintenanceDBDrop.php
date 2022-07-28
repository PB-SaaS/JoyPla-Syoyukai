<?php 
$dbs = [
    "NJ_HUserDB",
    "NJ_HospitalDB",
    "NJ_divisionDB",
    "NJ_OUserDB",
    "NJ_distributorDB",
    "NJ_itemDB",
    "NJ_inHPItemDB",
    "NJ_BillingHDB",
    "NJ_BillingDB",
    "NJ_OrderHDB",
    "NJ_OrderDB",
    "NJ_stockDB",
    "NJ_ReceivingHDB",
    "NJ_ReceivingDB",
    "NJ_PayoutHDB",
    "NJ_PayoutDB",
    "NJ_InventoryHDB",
    "NJ_InventoryDB",
    "NJ_InventoryEDB",
    "NJ_MasterDB",
    "NJ_TenantAdminDB",
    "NJ_LotDB",
    "NJ_ReturnDB",
    "NJ_ReturnHDB",
    "NJ_QRequestDB",
    "NJ_TopicDB",
    "NJ_NoticeDB",
    "NJ_contactUsDB",
    "NJ_reqItemDB",
    "NJ_PriceDB",
];

// API接続用パラメータ
define("API_URL", "https://reg31.smp.ne.jp/api/service");
define("API_TOKEN", "");
define("API_SECRET", "");

foreach($dbs as $d)
{
    // API用のHTTPヘッダ
    $api_headers = array(
        "X-SPIRAL-API: database/drop/response",
        "Content-Type: application/json; charset=UTF-8",
    );
    // 送信するJSONデータを作成
    $parameters = array();
    $parameters["spiral_api_token"] = API_TOKEN; //トークン
    $parameters["db_title"] = $d; //DBのタイトル
    $parameters["passkey"] = time(); //エポック秒
    // 署名を付けます
    $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
    $parameters["signature"] = hash_hmac('sha1', $key, API_SECRET, false);
    // 送信用のJSONデータを作成します。
    $json = json_encode($parameters);
    echo "===> database/select\n";
    // curlライブラリを使って送信します。
    $curl = curl_init(API_URL);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST , true);
    curl_setopt($curl, CURLOPT_POSTFIELDS , $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER , $api_headers);
    curl_exec($curl);
    // エラーがあればエラー内容を表示
    if (curl_errno($curl)) echo curl_error($curl);
    $response = curl_multi_getcontent($curl);
    curl_close($curl);
    // 画面に表示
    print_r(json_decode($response, true));
}