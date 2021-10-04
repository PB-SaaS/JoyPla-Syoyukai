<?php
// API��³�ѥѥ�᡼��
define("API_URL", "https://www.pi-pe.co.jp/api/locator");
define("MULTIPART_BOUNDARY", "SPIRAL_API_MULTIPART_BOUNDARY");

$API_TOKEN = $argv[1];
$API_SECRET = $argv[2];

// [ ����ץ�ǹԤ��� ] - API�����Ѥ��뤿���URL���������
// [����ץ��ư��������ν���]
// PHP��curl�饤�֥�꤬�Ȥ߹��ޤ�Ƥ���ɬ�פ�����ޤ���
// ���͡�http://www.php.net/manual/ja/intro.curl.php
// ��������URL (�ѹ���ɬ�פϤ���ޤ���)
$locator = API_URL;
// API�Ѥ�HTTP�إå�
$api_headers = array(
"X-SPIRAL-API: locator/apiserver/request",
"Content-Type: application/json; charset=UTF-8",
);
// �ꥯ�����ȥǡ��������
$parameters = array();
$parameters["spiral_api_token"] = $API_TOKEN; //�ȡ�����
// JSON�����˥��󥳡��ɤ��ޤ���
$json = json_encode($parameters);
// curl�饤�֥���Ȥä��������ޤ���
$curl = curl_init($locator);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST , true);
curl_setopt($curl, CURLOPT_POSTFIELDS , $json);
curl_setopt($curl, CURLOPT_HTTPHEADER , $api_headers);
curl_exec($curl);
// ���顼������Х��顼���Ƥ�ɽ��
if (curl_errno($curl)) echo curl_error($curl);
$response = curl_multi_getcontent($curl);
curl_close($curl);
$response = json_decode($response , true);
// ���̤�ɽ��
if($response['code'] != 0)
{
    var_dump($response);
    exit(1);
}

$API_URL = $response['location'];

//���åץ��ɤ���ե�����
$filename = $argv[3];
$filedata = file_get_contents($filename);
// API�Ѥ�HTTP�إå�
$api_headers = array(
    "X-SPIRAL-API: custom_module/upload/request",
    "Content-Type: multipart/form-data; boundary=\"" . MULTIPART_BOUNDARY . "\"",
);
// ��������JSON�ǡ��������
$parameters = array();
$parameters["spiral_api_token"] = $API_TOKEN; // �ȡ�����
$parameters["passkey"] = time(); // ���ݥå���
$parameters["dir"] = ""; // �ǥ��쥯�ȥ�
$parameters["compress"] = "t"; // compress
// ��̾���դ��ޤ�
$key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
$parameters["signature"] = hash_hmac('sha1', $key, $API_SECRET, false);
// POST�ǡ������������ޤ�

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