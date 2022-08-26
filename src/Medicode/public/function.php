<?php

declare(strict_types=1);

function bulkInsertLog(array $logs): void
{
    global $SPIRAL;
    
    $param = [
        'db_title' => MEDICODELOG_TRDB,
        'columns' => ['registrationTime', 'occurrenceTime', 'responseCode', 'response'],
        'data' => $logs
    ];
    
    insideApi('database', 'bulk_insert', $param);
}

function getApiUrl(string $token): string
{
    $locator = "http://www.pi-pe.co.jp/api/locator";
    
    // API用のHTTPヘッダ
    $api_headers = array(
        "X-SPIRAL-API: locator/apiserver/request",
        "Content-Type: application/json; charset=UTF-8",
    );
    
    // 送信するJSONデータを作成
    $parameters = array();
    $parameters['spiral_api_token'] = $token;
    $json = json_encode($parameters);
    
    // curlライブラリを使って送信します。
    $curl = curl_init($locator);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST         , true);
    curl_setopt($curl, CURLOPT_POSTFIELDS   , $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER , $api_headers);
    curl_exec($curl);
    
    // エラーがあればエラー内容を表示
    if (curl_errno($curl)) {
        echo curl_error($curl);
    }
    
    $response = curl_multi_getcontent($curl);
    curl_close($curl);
    
    $array = json_decode($response, true);
    
    return $array['location'];
}

function insideApi($_app, $_method, $_params)
{
    global $SPIRAL;
    $communicator = $SPIRAL->getSpiralApiCommunicator();
    $request = new SpiralApiRequest();
    $request->putAll($_params);
    return $communicator->request($_app, $_method, $request);
}

function notifyError(array $errors): void
{
    global $SPIRAL;
    $body = "メディコード連携定期実行でエラーが発生しました。"."\n"."エラー内容をご確認の上、アカウントを確認してください。"."\n\n";
    $body .= "■アカウント: ".$SPIRAL->getAccountId()."\n\n";
    foreach ($errors as $error)
    {
        $body .= "■発生時刻: ".$error[1]."\n"."■コード: ".$error[2]."\n"."■メッセージ: ".$error[3]."\n\n";
    }
    
    $param['db_title'] = NOTIFICATION_DB;
    $param['mail_field_title'] = 'email';
    $param['reserve_date'] = 'now';
    $param['subject'] = '[JoyPla] メディコード連携定期実行エラー通知';
    $param['body_text'] = $body;
    $param['from_address'] = 'joypla-spd@pi-pe.co.jp';
    $param['from_name'] = 'joypla';
    $param['error_field_title'] = 'errorCount';
    $param['error_auto_update'] = true;
    $param['error_auto_exclude'] = true;
    $param['error_exclude_count'] = 3;
    
    outsideApi(API_TOKEN, API_SECRET, 'deliver_express2', 'regist', $param);
}

function outsideApi(string $token, string $secret, string $method, string $action, array $parameters)
{
    if (!$token || !$secret || !$method || !$action || !$parameters || empty($parameters))
    {
        return null;
    }
    
    $api_headers = array(
        "X-SPIRAL-API: ".$method."/".$action."/request",
        "Content-Type: application/json; charset=UTF-8",
    );
    $APIURL = $parameters["APIURL"] ? $parameters["APIURL"] : getApiUrl($token);
    $parameters["spiral_api_token"] = $token;
    $parameters["passkey"] = time();
    $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
    $parameters["signature"] = hash_hmac('sha1', $key, $secret, false);
    $json = json_encode($parameters);
    
    // curlライブラリを使って送信
    $curl = curl_init($APIURL);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
    curl_exec($curl);
    
    if (curl_errno($curl))
    {
        echo curl_error($curl);
    }
    
    $response = curl_multi_getcontent($curl);
    curl_close($curl);
    
    return $response;
}