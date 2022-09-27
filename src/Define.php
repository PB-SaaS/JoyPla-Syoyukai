<?php
date_default_timezone_set('Asia/Tokyo'); 

define('LOGIN_URL', "https://area18.smp.ne.jp/area/p/mjtf5qbqjt9lhpgqh7/1icLdA/login.html");
define('OROSHI_LOGIN_URL', "https://area18.smp.ne.jp/area/p/mjtf5qbral1lhpgrh7/EEOAFE/login.html");
define('OROSHI_OrderDetailAccess', "https://area18.smp.ne.jp/area/d/56bd6c69/lhpgrj"); //伝票直リンクURL
define('TENANT_ADMIN_LOGIN_URL', "https://area18.smp.ne.jp/area/p/mjtf5qcsjo4lhsetg6/IjS8C5/login.html");

/**
 * 設定クラス
 */
class LogConfig {
    const LOG_LEVEL = 1;
    const EXPORT_TO_SPIRALV2 = true; // SPIRALv2オブジェクトで出力する
    const SPIRAL_API_LOGGING_DB_TITLE = '73308'; // SPIRALv1v2オブジェクトで出力する場合に設定するDBタイトル
    const JOYPLA_API_LOGGING_DB_TITLE = '73304'; // SPIRALv1v2オブジェクトで出力する場合に設定するDBタイトル
    const LOGGING_APP_TITLE = '24083'; // SPIRALv1v2オブジェクトで出力する場合に設定するAPPタイトル
    const SPIRALV2_API_KEY = 'dGFvQlZ9VUU4emE4TDMwbnp4T0hiUiRd'; // SPIRALv1v2オブジェクトで出力する場合に設定するAPPタイトル
}

/***
 * tennant Admin MyArea Setting
 */

const GateSetting = [
    'all' => [
        'FacilityList' => true,
        'FacilityRegist' => true,
        'FacilityUpdate' => true,
        'DistributorBlukInsert' => true,
        'ItemBulkUpsert' => true,
        'PriceBulkUpsert' => true,
        'PriceAndInHospitalItemBulkInsert' => true,
        'NewFacilityItemsBulkInsert' => true,
        'ConsumedHistory' => true,
        'OrderedHistory' => true,
        'ReceivedHistory' => true,
        'PayoutHistory' => true,
        'ConsumMR' => true,
        'OrderMR' => true,
        'ReceivingMR' => true,
        'PayoutMR' => true,
        'InventoryMovement' => true, //追加
        'ReceiveHistoryMCH' => true,
        'ReturnHistoryMCH' => true,
        'SystemNotification' => true,
        'SystemNotificationReg' => true,
        ],
    'custom1' => [
        'FacilityList' => false,
        'FacilityRegist' => false,
        'FacilityUpdate' => false,
        'DistributorBlukInsert' => false,
        'ItemBulkUpsert' => false,
        'PriceBulkUpsert' => false,
        'PriceAndInHospitalItemBulkInsert' => false,
        'NewFacilityItemsBulkInsert' => false,
        'ConsumedHistory' => false,
        'OrderedHistory' => false,
        'ReceivedHistory' => false,
        'PayoutHistory' => false,
        'ConsumMR' => false,
        'OrderMR' => false,
        'ReceivingMR' => false,
        'PayoutMR' => false,
        'InventoryMovement' => false, //追加
        'ReceiveHistoryMCH' => false,
        'ReturnHistoryMCH' => false,
        'SystemNotification' => false,
        'SystemNotificationReg' => false,
        ],
];
