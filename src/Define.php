<?php
date_default_timezone_set('Asia/Tokyo'); 

define('LOGIN_URL', "https://area18.smp.ne.jp/area/p/mjtf5qbqjt9lhpgqh7/1icLdA/login.html");
define('OROSHI_LOGIN_URL', "https://area18.smp.ne.jp/area/p/mjtf5qbral1lhpgrh7/EEOAFE/login.html");

define('TENANT_ADMIN_LOGIN_URL', "https://area18.smp.ne.jp/area/p/mjtf5qcsjo4lhsetg6/IjS8C5/login.html");

//define('MY_AREA_TITLE', "NJ_HPLogin");
//define('OROSHI_MY_AREA_TITLE', "NJ_OUser");

//define('APITITLE', "JoyPla");
//define('CHARSET', 'UTF-8');
//define('REPLACE_FLAGS', ENT_QUOTES);

//define('FROM_ADDRESS', "joypla-spd@pi-pe.co.jp");
//define('FROM_NAME', "joypla");
//define('HP_MAIL_FIELD_TITLE', "mailAddress");
//define('OROSHI_MAIL_FIELD_TITLE', "mailAddress");

//define('MITSUMORI_ITEMS_REG', "11556280");//NJ_見積依頼 見積商品登録通知 見積商品登録完了


/***
 * barcode search For HP
 */

//define('HP_BILLING_PAGE', "page_263400");
//define('HP_UNORDER_PAGE', "page_262926");
//define('HP_ORDER_PAGE', "page_263320");
//define('HP_RECEIVING_PAGE', "page_266892");
//define('HP_RETERN_PAGE', "page_265604");
//define('HP_PAYOUT_PAGE', "page_263580");

//define('OROSHI_ORDER_PAGE', "page_266218");
//define('OROSHI_RECEIVING_PAGE', "page_266907");



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
        'ReceiveHistoryMCH' => false,
        'ReturnHistoryMCH' => false,
        'SystemNotification' => false,
        'SystemNotificationReg' => false,
        ],
    ];



