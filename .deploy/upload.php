<?php
// API接続用パラメータ
define("API_URL", "https://www.pi-pe.co.jp/api/locator");
define("MULTIPART_BOUNDARY", "SPIRAL_API_MULTIPART_BOUNDARY");

$API_TOKEN = $argv[1];
$API_SECRET = $argv[2];

// [ サンプルで行う事 ] - APIを利用するためのURLを取得する
// [サンプルを動かすための準備]
// PHPにcurlライブラリが組み込まれている必要があります。
// 参考：http://www.php.net/manual/ja/intro.curl.php
// ロケータのURL (変更の必要はありません)
$locator = API_URL;
// API用のHTTPヘッダ
$api_headers = array(
"X-SPIRAL-API: locator/apiserver/request",
"Content-Type: application/json; charset=UTF-8",
);
// リクエストデータを作成
$parameters = array();
$parameters["spiral_api_token"] = $API_TOKEN; //トークン
// JSON形式にエンコードします。
$json = json_encode($parameters);
// curlライブラリを使って送信します。
$curl = curl_init($locator);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST , true);
curl_setopt($curl, CURLOPT_POSTFIELDS , $json);
curl_setopt($curl, CURLOPT_HTTPHEADER , $api_headers);
curl_exec($curl);
// エラーがあればエラー内容を表示
if (curl_errno($curl)) echo curl_error($curl);
$response = curl_multi_getcontent($curl);
curl_close($curl);
$response = json_decode($response , true);
// 画面に表示
if($response['code'] != 0)
{
    var_dump($response);
    exit(1);
}

$API_URL = $response['location'];

//アップロードするファイル
$filename = $argv[3];
$filedata = file_get_contents($filename);
// API用のHTTPヘッダ
$api_headers = array(
    "X-SPIRAL-API: custom_module/upload/request",
    "Content-Type: multipart/form-data; boundary=\"" . MULTIPART_BOUNDARY . "\"",
);
// 送信するJSONデータを作成
$parameters = array();
$parameters["spiral_api_token"] = $API_TOKEN; // トークン
$parameters["passkey"] = time(); // エポック秒
$parameters["dir"] = ""; // ディレクトリ
$parameters["compress"] = "t"; // compress
// 署名を付けます
$key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
$parameters["signature"] = hash_hmac('sha1', $key, $API_SECRET, false);
// POSTデータを生成します

$postdata = "--" . MULTIPART_BOUNDARY . "\r\n";
$postdata .= "Content-Type: application/json; charset=\"UTF-8\";\r\n";
$postdata .= "Content-Disposition: form-data; name=\"json\"\r\n\r\n";
$postdata .= json_encode($parameters);
$postdata .= "\r\n\r\n";

$postdata .= "--" . MULTIPART_BOUNDARY . "\r\n";
$postdata .= "Content-Type: application/x-httpd-php;\r\n";
$postdata .= "Content-Disposition: form-data; name=\"src\"; filename=\"$filename\"\r\n\r\n";
$postdata .= $filedata;
$postdata .= "\r\n\r\n";
$postdata .= "--" . MULTIPART_BOUNDARY . "--\r\n";
$postdata .= "\r\n";
// curlライブラリを使って送信します。
$curl = curl_init($API_URL);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
curl_exec($curl);
$response = curl_multi_getcontent($curl);
curl_close($curl);

$response = json_decode($response , true);

if($response['code'] != 0)
{
    var_dump($response);
    exit(1);
}

exit(0);