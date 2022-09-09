<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Tokyo');

/* Medicode
----------------------------*/
define("AUTHAPI_URL", "https://www.e-mednet.jp/NASApp/medi/api/authentication");
define("SENDAPI_URL", "https://www.e-mednet.jp/NASApp/medi/api/send/01");
define("MEDICODE_BOUNDARY", "----MedicodeAPIBoundary");
define("SSL_CIPHER_LIST", "DEFAULT:!DH");
define("NEWLINE", "\r\n");
define("SENDFILE", "JoyPlaOrderData_");

/* SPIRAL
----------------------------*/
define("LINES_PER_PAGE", 1000);
define("SETTING_DB", "NJ_medicodeDB");
define("ORDER_ITEM_DB", "340_orderItem");
define("MEDICODELOG_TRDB", "MedicodeLogTRDB");

/* NOTIFICATION
   account: joypla_cleansing
----------------------------*/
define("API_TOKEN", "00002Gj9Z5Af31a4c7ee1c00422efa41a4d5aea3ed4dc57e1375");
define("API_SECRET", "1a7b9f1289a864d5d9155cab45d37ad2b78c47de");
define("NOTIFICATION_DB", "notificationDB");
