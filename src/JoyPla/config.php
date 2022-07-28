<?php
require_once "Library/require.php";
require_once "Define.php";

define('MY_AREA_TITLE', "NJ_HPLogin");
define('OROSHI_MY_AREA_TITLE', "NJ_OUser");

define('FROM_ADDRESS', "joypla-spd@pi-pe.co.jp");
define('FROM_NAME', "joypla");
define('HP_MAIL_FIELD_TITLE', "mailAddress");
define('OROSHI_MAIL_FIELD_TITLE', "mailAddress");



/**
 * 設定クラス
 */
class LogConfig {
    const LOG_LEVEL = 3;
    const EXPORT_TO_SPIRALV2 = true; // SPIRALv2オブジェクトで出力する
    const SPIRAL_API_LOGGING_DB_TITLE = '73308'; // SPIRALv1v2オブジェクトで出力する場合に設定するDBタイトル
    const JOYPLA_API_LOGGING_DB_TITLE = '73304'; // SPIRALv1v2オブジェクトで出力する場合に設定するDBタイトル
    const LOGGING_APP_TITLE = '24083'; // SPIRALv1v2オブジェクトで出力する場合に設定するAPPタイトル
    const SPIRALV2_API_KEY = 'dGFvQlZ9VUU4emE4TDMwbnp4T0hiUiRd'; // SPIRALv1v2オブジェクトで出力する場合に設定するAPPタイトル
}