<?php
define("API_URL", "https://www.pi-pe.co.jp/api/locator");
define("MULTIPART_BOUNDARY", "SPIRAL_API_MULTIPART_BOUNDARY");

$API_TOKEN = $argv[1];
$API_SECRET = $argv[2];

$locator = API_URL;
$api_headers = array(
"X-SPIRAL-API: locator/apiserver/request",
"Content-Type: application/json; charset=UTF-8",
);
$parameters = array();
$parameters["spiral_api_token"] = $API_TOKEN;
$json = json_encode($parameters);
$curl = curl_init($locator);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST , true);
curl_setopt($curl, CURLOPT_POSTFIELDS , $json);
curl_setopt($curl, CURLOPT_HTTPHEADER , $api_headers);
curl_exec($curl);
if (curl_errno($curl)){
    echo curl_error($curl);
    exit(1);
}
$response = curl_multi_getcontent($curl);
curl_close($curl);
$response = json_decode($response , true);
if($response['code'] != 0)
{
    var_dump($response);
    exit(1);
}

$API_URL = $response['location'];

$filename = $argv[3];
$filedata = file_get_contents($filename);
// API�Ѥ�HTTP�إå�
$api_headers = array(
    "X-SPIRAL-API: custom_module/upload/request",
    "Content-Type: multipart/form-data; boundary=\"" . MULTIPART_BOUNDARY . "\"",
);
$parameters = array();
$parameters["spiral_api_token"] = $API_TOKEN; 
$parameters["passkey"] = time();
$parameters["dir"] = "";
$parameters["compress"] = "t"; 
$key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
$parameters["signature"] = hash_hmac('sha1', $key, $API_SECRET, false);

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
// curl�饤�֥���Ȥä��������ޤ���
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