<?php

namespace App\Model;

use Model;

class UsedSlipHistoy extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_UsedSlipHDB";
    public static $guarded = ["id"];
    public static $primary_key = "usedSlipId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "usedTime",
        "usedSlipId",
        "itemsNumber",
        "usedSlipStatus",
        "totalAmount",
        "distributorId",
        "divisionId",
        "hospitalId",
        "reportPersonName",
        "authKey"
    ];

    //デフォルト値
    public static $attributes = [];
    public static $select = [
        "usedSlipStatus" => [1 => "未確定",2 => "確定済み"]
    ];
}

class Borrowing extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_Borrowing";
    public static $guarded = ["id"];
    public static $primary_key = "borrowingId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "borrowingId",
        "borrowingNum",
        "inHospitalItemId",
        "lotNumber",
        "lotDate",
        "divisionId",
        "usedSlipId",
        "price",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "usedDate",
        "distributorId"
    ];

    //デフォルト値
    public static $attributes = [];
}

class Division extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "deleteFlag";

    public static $spiral_db_name = "NJ_divisionDB";
    public static $guarded = ["id"];
    public static $primary_key = "divisionId";
    public static $fillable = [
        "registrationTime",
        "divisionId",
        "hospitalId",
        "divisionName",
        "divisionType",
        "deleteFlag",
        "authkey"
    ];

    //デフォルト値
    public static $attributes = [];
}

class Billing extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_BillingDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inHospitalItemId",
        "billingNumber",
        "price",
        "billingQuantity",
        "billingAmount",
        "hospitalId",
        "divisionId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "lotNumber",
        "lotDate",
        "unitPrice",
        "lotManagement",
        "itemId"
    ];

    //デフォルト値
    public static $attributes = [];
}

class BillingView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "billingDetailv2";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inHospitalItemId",
        "billingNumber",
        "billingQuantity",
        "billingAmount",
        "hospitalId",
        "divisionId",
        "authKey",
        "distributorId",
        "catalogNo",
        "serialNo",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "oldPrice",
        "lotNumber",
        "lotDate",
        "unitPrice",
        "officialFlag",
        "measuringInst",
        "divisionName",
        "category"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "category" => [
            1 => "医療材料",
            2 => "薬剤",
            3 => "試薬",
            4 => "日用品",
            99 => "その他"
        ]
    ];
}

class BillingHistory extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_BillingHDB";
    public static $guarded = ["id"];
    public static $primary_key = "billingNumber";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "billingNumber",
        "hospitalId",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "billingAuthKey",
        "billingStatus",
        "billingDate",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "billingStatus" => [1,2]
    ];
}

class Order extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_OrderDB";
    public static $guarded = ["id"];
    public static $primary_key = "orderCNumber";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "receivingTime",
        "dueDate",
        "orderCNumber",
        "hospitalId",
        "inHospitalItemId",
        "orderNumber",
        "price",
        "orderQuantity",
        "orderPrice",
        "receivingFlag",
        "receivingNum",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "divisionId",
        "distributorId",
        "lotManagement",
        "itemId",
        "priceId",
        "adjustment",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "adjustment" => [
            1 => "定数発注",
            2 => "個別発注",
        ],
    ];
}

class OrderHistory extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_OrderHDB";
    public static $guarded = ["id"];
    public static $primary_key = "orderNumber";
    public static $fillable = [
        "registrationTime",
        "orderTime",
        "receivingTime",
        "orderNumber",
        "hospitalId",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "orderStatus",
        "hachuRarrival",
        /*"f002664851",*/ //ルックアップキーは除外
        "distributorId",
        "ordererUserName",
        "ordercomment",
        "orderAuthKey",
        "staffName",
        "adjustment",
        "distrComment"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "orderStatus" => [
            1 =>"未発注",
            2 =>"発注完了",
            3 =>"受注完了",
            4 =>"納期報告済",
            5 =>"一部入庫完了",
            6 =>"入庫完了",
            7 =>"納品取消",
            8 =>"貸出品",
        ],
        "adjustment" => [
            1 => "定数発注",
            2 => "個別発注",
        ],
    ];
}

class Receiving extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_ReceivingDB";
    public static $guarded = ["id"];
    public static $primary_key = "receivingNumber";
    public static $fillable = [
        "registrationTime",
        "orderCNumber",
        "receivingCount",
        "receivingHId",
        /*"f002664881",*/ //ルックアップキーは除外
        "inHospitalItemId",
        "receivingNumber",
        "price",
        "receivingPrice",
        "hospitalId",
        "totalReturnCount",
        "divisionId",
        "distributorId",
        "adjAmount",
        "priceAfterAdj",
        "lotNumber",
        "lotDate",
        "itemId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class ReceivingView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "330_receItems";
    public static $guarded = ["id"];
    public static $primary_key = "receivingNumber";
    public static $fillable = [
        "registrationTime",
        "orderCNumber",
        "receivingCount",
        "receivingHId",
        "inHospitalItemId",
        "receivingNumber",
        "price",
        "receivingPrice",
        "hospitalId",
        "totalReturnCount",
        "divisionId",
        "distributorId",
        "adjAmount",
        "priceAfterAdj",
        "lotNumber",
        "lotDate",
        "divisionName",
        "distributorName",
        "orderHistoryId",
        "makerName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "unitPrice",
        "orderQuantity",
        "orderPrice",
        "category",
        "minPrice",
        "officialFlag",
        "labelId",
        "catalogNo",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "category" => [
            1 => "医療材料",
            2 => "薬剤",
            3 => "試薬",
            4 => "日用品",
            99 => "その他"
        ]
    ];
}

class ReceivingHistoryView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "receiptHDB";
    public static $guarded = ["id"];
    public static $primary_key = "receivingHId";
    public static $fillable = [
        "registrationTime",
        "receivingHId",
        "distributorId",
        "orderHistoryId",
        "hospitalId",
        "itemsNumber",
        "authKey",
        "divisionId",
        "divisionName",
        "receivingTime",
        "orderNumber",
        "totalAmount",
        "orderStatus",
        "hachuRarrival",
        "distributorName",
        "hospitalName",
        "ordererUserName",
        "ordercomment",
        "orderAuthKey",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "recevingStatus" => [
            1 =>"通常入庫",
            2 =>"貸出品",
        ]
    ];
}

class ReceivingHistory extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_ReceivingHDB";
    public static $guarded = ["id"];
    public static $primary_key = "receivingHId";
    public static $fillable = [
        "registrationTime",
        "receivingHId",
        "distributorId",
        "orderHistoryId",
        "hospitalId",
        "itemsNumber",
        "authKey",
        /*"f002664878",*/ //ルックアップキーは除外
        "divisionId",
        "recevingStatus",
        "slipCategory",
        "totalAmount",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "recevingStatus" => [
            1 =>"通常入庫",
            2 =>"貸出品",
        ]
    ];
}
//Return は使用できない文字列なので、ReturnItemにします
class ReturnItem extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";
    public static $spiral_db_name = "NJ_ReturnDB";
    public static $guarded = ["id"];
    public static $primary_key = "receivingNumber";
    public static $fillable = [
        "registrationTime",
        "orderCNumber",
        "receivingHId",
        "inHospitalItemId",
        "receivingNumber",
        "price",
        "returnID",
        "returnCount",
        "returnPrice",
        "hospitalId",
        "returnHistoryID",
        "lotNumber",
        "lotDate"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class ReturnItemView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "returnDatav2";
    public static $guarded = ["id"];
    public static $primary_key = "returnID";
    public static $fillable = [
        "registrationTime",
        "orderCNumber",
        "receivingHId",
        "inHospitalItemId",
        "receivingNumber",
        "price",
        "returnID",
        "returnCount",
        "returnPrice",
        "hospitalId",
        "returnHistoryID",
        "receivingCount",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "minPrice",
        "makerName",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "divisionId",
        "lotNumber",
        "lotDate",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class ReturnHistory extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_ReturnHDB";
    public static $guarded = ["id"];
    public static $primary_key = "returnHistoryID";
    public static $fillable = [
        "registrationTime",
        "receivingHId",
        "distributorId",
        "orderHistoryId",
        "returnHistoryID",
        "hospitalId",
        "itemsNumber",
        "authKey",
        "returnTotalPrice",
        "divisionId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class PayoutView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "payoutDatav2";
    public static $guarded = ["id"];
    public static $primary_key = "payoutId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "payoutHistoryId",
        "payoutId",
        "inHospitalItemId",
        "hospitalId",
        "sourceDivisionId",
        "targetDivisionId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "payoutQuantity",
        "payoutAmount",
        "payoutCount",
        "payoutLabelCount",
        "adjAmount",
        "priceAfterAdj",
        "lotNumber",
        "lotDate",
        "unitPrice",
        "distributorId",
        "catalogNo",
        "serialNo",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "labelId",
        "minPrice",
        "officialFlag",
        "payoutAuthKey",
        "sourceDivision",
        "targetDivision",
        "itemsNumber",
        "totalAmount",
        "category"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class Payout extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_PayoutDB";
    public static $guarded = ["id"];
    public static $primary_key = "payoutId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "payoutHistoryId",
        "payoutId",
        "inHospitalItemId",
        "hospitalId",
        "sourceDivisionId",
        "targetDivisionId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "payoutQuantity",
        "payoutAmount",
        "payoutCount",
        "payoutLabelCount",
        "adjAmount",
        "priceAfterAdj",
        "lotNumber",
        "lotDate",
        "unitPrice",
        "cardId",
        "itemId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class PayoutHistory extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_PayoutHDB";
    public static $guarded = ["id"];
    public static $primary_key = "payoutHistoryId";
    public static $fillable = [
        "registrationTime",
        "payoutHistoryId",
        "hospitalId",
        "payoutAuthKey",
        "sourceDivisionId",
        "sourceDivision",
        "targetDivisionId",
        "targetDivision",
        "itemsNumber",
        "totalAmount",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Item extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_itemDB";
    public static $guarded = ["id"];
    public static $primary_key = "itemId";
    public static $fillable = [
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "officialFlag",
        "tenantId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "minPrice",
        "itemsAuthKey",
        "janTenantId",
        "requestId",
        "requestFlg",
        "requestItemId",
        "hospitalId",
        "registrationTime",
        "officialpriceOld",
        "officialprice",
        "catalogNo",
        "serialNo",
        "lotManagement",
        "category",
        "updateTime"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "requestFlg" => [
            1 => "採用",
            2 => "不採用",
            3 => "見積中",
            4 => "業者記入済"
        ],
        "category" => [
            1 => "医療材料",
            2 => "薬剤",
            3 => "試薬",
            4 => "日用品",
            99 => "その他"
        ]
    ];
}

class InventoryAdjustmentTransaction extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_inventoryTRDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "divisionId",
        "inHospitalItemId",
        "count",
        "hospitalId",
        "orderWithinCount",
        "pattern",
        "lotUniqueKey",
        "lotNumber",
        "lotDate",
        "stockQuantity",
        "rackName",
        "constantByDiv",
        "loginId",
        "previousStock",
        "changeReason"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class InHospitalItem extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_inHPItemDB";
    public static $guarded = ["id"];
    public static $primary_key = "inHospitalItemId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inHospitalItemId",
        "authKey",
        "hospitalId",
        "distributorId",
        "catalogNo",
        "serialNo",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "price",
        "oldPrice",
        "labelId",
        "minPrice",
        "officialFlag",
        "priceId",
        "unitPrice",
        "measuringInst",
        "distributorName",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class InHospitalItemView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "itemInHospitalv2";
    public static $guarded = ["id"];
    public static $primary_key = "inHospitalItemId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inHospitalItemId",
        "authKey",
        "hospitalId",
        "distributorId",
        "catalogNo",
        "serialNo",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "distributorName",
        "hospitalName",
        "officialFlag",
        "makerName",
        "price",
        "oldPrice",
        "HPstock",
        "minPrice",
        "labelId",
        "officialpriceOld",
        "officialprice",
        "priceId",
        "unitPrice",
        "measuringInst",
        "lotManagement",
        "category",
        "tenantId",
        "smallCategory",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "category" => [
            1 => "医療材料",
            2 => "薬剤",
            3 => "試薬",
            4 => "日用品",
            99 => "その他"
        ]
    ];
}



class Hospital extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_HospitalDB";
    public static $guarded = ["id"];
    public static $primary_key = "hospitalId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "hospitalId",
        "hospitalName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "tenantId",
        "name",
        "nameKana",
        "mailAddress",
        "contactAddress",
        "plan",
        "receivingTarget",
        "function1",
        "function2",
        "function3",
        "function4",
        "function5",
        "function6",
        "function7",
        "function8",
        "authKey",
        "registerableNum",
        "labelDesign1",
        "labelDesign2",
        "labelDesign3",
        "billingUnitPrice",
        "payoutUnitPrice",
        "invUnitPrice",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        'receivingTarget' => [
            '1'=>'大倉庫',
            '2'=>'発注部署'
            ]
        ];
}


class HospitalUser extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_HUserDB";
    public static $guarded = ["id"];
    public static $primary_key = "loginId";
    public static $mail_field_title = "mailAddress";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "authKey",
        "hospitalId",
        "divisionId",
        "userPermission",
        "loginId",
        "loginPassword",
        "name",
        "nameKana",
        "mailAddress",
        "remarks",
        "termsAgreement",
        "tenantId",
        "agreementDate",
        "hospitalAuthKey",
        "userCheck"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class DistributorAffiliationView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "invitingDB";
    public static $guarded = ["id"];
    public static $primary_key = "affiliationId";
    public static $mail_field_title = "mailAddress";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "authKey",
        "affiliationId",
        "distributorId",
        "OUserPermission",
        "loginId",
        "hospitalId",
        "invitingTime",
        "invitingAgree",
        "hospitalName",
        "distributorName",
        "mailAddress",
        "name",
        "nameKana",
        "tenantId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class DistributorUser extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_OUserDB";
    public static $guarded = ["id"];
    public static $primary_key = "loginId";
    public static $mail_field_title = "mailAddress";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "authKey",
        "distributorId",
        "userPermission",
        "loginId",
        "loginPassword",
        "name",
        "nameKana",
        "mailAddress",
        "remarks",
        "hospitalId",
        "termsAgreement",
        "agreementDate",
        "affiliationId",
        "userCheck"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Distributor extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_distributorDB";
    public static $guarded = ["id"];
    public static $primary_key = "distributorId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "distributorId",
        "distributorName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "hospitalId",
        "authKey",
        "vendorFlag",
        "distCommonId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Tenant extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_TenantAdminDB";
    public static $guarded = ["id"];
    public static $primary_key = "tenantId";
    public static $fillable = [
        "registrationTime",
        "tenantId",
        "tenantName",
        "note",
        "tenantKind",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "tenantKind" => [1,2]
    ];
}

class AssociationTR extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_associationTR";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "usedSlipId",
        "orderNumber",
        "receivingHId",
        "billingNumber",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Stock extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_stockDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "stockQuantity",
        "authKey",
        "orderWithinCount",
        "rackName",
        "distributorId",
        "invFinishTime",
        "planInventoryCnt",
        "constantByDiv",
        "requiredOrderNum",
        "barcode",
        "labelId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class StockView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "310_stockMg";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "stockQuantity",
        "authKey",
        "orderWithinCount",
        "rackName",
        "distributorId",
        "distributorName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "catalogNo",
        "serialNo",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "divisionName",
        "divisionType",
        "invFinishTime",
        "planInventoryCnt",
        "constantByDiv",
        "requiredOrderNum",
        "price",
        "oldPrice",
        "minPrice",
        "labelId",
        "barcode",
        "unitPrice",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}



class Card extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_CardDB";
    public static $guarded = ["id"];
    public static $primary_key = "cardId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "cardId",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "quantity",
        "payoutId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class CardInfoView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "CardInfo";
    public static $guarded = ["id"];
    public static $primary_key = "cardId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "cardId",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "quantity",
        "payoutId",
        "distributorId",
        "catalogNo",
        "serialNo",
        "quantityUnit",
        "itemUnit",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "price",
        "oldPrice",
        "labelId",
        "minPrice",
        "officialFlag",
        "priceId",
        "unitPrice",
        "measuringInst",
        "distributorName",
        "hospitalName",
        "divisionName",
        "lotNumber",
        "lotDate",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class OrderDataView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "orderdataDB";
    public static $guarded = ["id"];
    public static $primary_key = "orderNumber";
    public static $fillable = [
        "registrationTime",
        "orderTime",
        "receivingTime",
        "orderNumber",
        "hospitalId",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "orderStatus",
        "hachuRarrival",
        "distributorId",
        "distributorName",
        "divisionName",
        "hospitalName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "ordererUserName",
        "ordercomment",
        "orderAuthKey",
        "receivingTarget",
        "distrComment"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class OrderedItemInfoView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "333_hacchu";
    public static $guarded = ["id"];
    public static $primary_key = "orderCNumber";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "receivingTime",
        "dueDate",
        "orderCNumber",
        "hospitalId",
        "inHospitalItemId",
        "orderNumber",
        "price",
        "orderQuantity",
        "orderPrice",
        "receivingFlag",
        "receivingNum",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "divisionId",
        "distributorId",
        "lotManagement",
        "itemId",
        "priceId",
        "distributorMCode",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "divisionName",
        "orderStatus",
        "category",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class OrderedItemView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "330_hacchuS";
    public static $guarded = ["id"];
    public static $primary_key = "orderCNumber";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "receivingTime",
        "dueDate",
        "orderCNumber",
        "hospitalId",
        "inHospitalItemId",
        "orderNumber",
        "price",
        "orderQuantity",
        "orderPrice",
        "receivingFlag",
        "receivingNum",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "divisionId",
        "distributorId",
        "distributorName",
        "divisionName",
        "deleteFlag",
        "catalogNo",
        "serialNo",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "labelId",
        "minPrice",
        "orderStatus",
        "itemId",
        "lotManagement",
        "category",
        "adjustment",
        "distributorMCode"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "adjustment" => [
            1 => "定数発注",
            2 => "個別発注",
        ],
    ];
}

class Lot extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "330_NJ_LotDB";
    public static $guarded = ["id"];
    public static $primary_key = "lotUniqueKey";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "lotId",
        "lotNumber",
        "lotDate",
        "divisionId",
        "inHospitalItemId",
        "hospitalId",
        "lotUniqueKey",
        "stockQuantity",
        "lotAuthKey",
        "itemId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Inventory extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_InventoryDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inventoryHId",
        "inHospitalItemId",
        "hospitalId",
        "price",
        "calculatingStock",
        "inventryNum",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "divisionId",
        "inventryAmount",
        "distributorId",
        "inventoryEndId",
        "lotNumber",
        "lotDate",
        "lotUniqueKey",
        "unitPrice",
        "invUnitPrice",
        "inventoryStatus"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class InventoryItemView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "inventoryData";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inventoryHId",
        "inHospitalItemId",
        "hospitalId",
        "price",
        "calculatingStock",
        "inventryNum",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "divisionId",
        "inventryAmount",
        "distributorId",
        "distributorName",
        "catalogNo",
        "serialNo",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "oldPrice",
        "unitPrice",
        "lotNumber",
        "lotDate",
        "labelId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class InventoryEnd extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_InventoryEDB";
    public static $guarded = ["id"];
    public static $primary_key = "inventoryEndId";
    public static $fillable = [
        "registrationTime",
        "inventoryTime",
        "inventoryEndId",
        "hospitalId",
        "itemsNumber",
        "totalAmount",
        "inventoryStatus",
        "invEndAuthKey"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "inventoryStatus" => [
            1 => "棚卸中",
            2 => "棚卸完了"
        ]
    ];
}

class InventoryHistory extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_InventoryHDB";
    public static $guarded = ["id"];
    public static $primary_key = "inventoryHId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inventoryHId",
        "inventoryEndId",
        "hospitalId",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "invHAuthKey",
        "inventoryHStatus"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class InventoryHistoryDivisionView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "333_divisionInv";
    public static $guarded = ["id"];
    public static $primary_key = "inventoryHId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inventoryHId",
        "inventoryEndId",
        "hospitalId",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "invHAuthKey",
        "divisionName",
        "inventoryTime",
        "inventoryStatus"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class InventoryView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "invInvEndData";
    public static $guarded = ["id"];
    public static $primary_key = "inventoryHId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inventoryHId",
        "inventoryEndId",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "price",
        "unitPrice",
        "inventryAmount",
        "inventryNum",
        "invUnitPrice",
        "inventoryStatus",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class StockTakingTransaction extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_stocktakingTR";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "inventoryEndId",
        "inventoryHId",
        "inHospitalItemId",
        "hospitalId",
        "divisionId",
        "price",
        "calculatingStock",
        "inventryNum",
        "inventryAmount",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "lotNumber",
        "lotDate",
        "lotUniqueKey",
        "unitPrice",
        "invUnitPrice"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class QuoteRequest extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_QRequestDB";
    public static $guarded = ["id"];
    public static $primary_key = "requestId";
    public static $fillable = [
        "registrationTime",
        "quotePeriod",
        "requestId",
        "requestTitle",
        "requestDetail",
        "requestStatus",
        "distributorId",
        "hospitalId",
        "requestUName",
        "distributorUName",
        "tenantId",
        "mail"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "requestStatus" => [
            1 => "未開封",
            2 => "開封",
            3 => "商品記載有",
            4 => "一部却下",
            5 => "一部採用",
            6 => "却下",
            7 => "採用"
        ]
    ];
}

class Price extends Model
{
    public const CREATED_AT = "";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_PriceDB";
    public static $guarded = ["id"];
    public static $primary_key = "priceId";
    public static $fillable = [
        "priceId",
        "authKey",
        "itemId",
        "distributorId",
        "quantity",
        "price",
        "hospitalId",
        "requestFlg",
        "quantityUnit",
        "itemUnit",
        "notice",
        "notUsedFlag",
        "requestId",
        "unitPrice"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "requestFlg" => [
            1 => "採用",
            2 => "不採用",
            3 => "見積中",
            4 => "業者記入済"
        ]
    ];
}


class PriceView extends Model
{
    public const CREATED_AT = "";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "330_ItemsPrice";
    public static $guarded = ["id"];
    public static $primary_key = "priceId";
    public static $fillable = [
        "priceId",
        "authKey",
        "itemId",
        "distributorId",
        "quantity",
        "price",
        "hospitalId",
        "requestFlg",
        "notice",
        "quantityUnit",
        "itemUnit",
        "notUsedFlag",
        "distributorName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "officialpriceOld",
        "officialprice",
        "catalogNo",
        "serialNo",
        "category",
        "lotManagement",
        "tenantId",
        "unitPrice"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "requestFlg" => [
            1 => "採用",
            2 => "不採用",
            3 => "見積中",
            4 => "業者記入済"
        ]
    ];
}

class QuoteItem extends Model
{
    public const CREATED_AT = "";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_reqItemDB";
    public static $guarded = ["id"];
    public static $primary_key = "requestItemId";
    public static $fillable = [
        "requestId",
        "reqitemsAuthKey",
        "requestItemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "tenantId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "minPrice",
        "distributorId",
        "officialFlag",
        "requestFlg",
        "officialprice",
        "officialpriceOld",
        "catalogNo",
        "serialNo",
        "category",
        "lotManagement"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "requestFlg" => [
            1 => "採用",
            2 => "不採用",
            3 => "見積中",
            4 => "業者記入済"
        ],
        "category" => [
            1 => "医療材料",
            2 => "薬剤",
            3 => "試薬",
            4 => "日用品",
            99 => "その他"
        ]
    ];
}


class CommentTr extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_comment";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "topicId",
        "name",
        "comment",
        "authKey",
        "commentCount",
        "B_Id",
        "O_Id",
        "M_Id",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Comment extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_commentDB";
    public static $guarded = ["id"];
    public static $primary_key = "commentId";
    public static $fillable = [
        "registrationTime",
        "topicId",
        "authKey",
        "commentName",
        "comment",
        "deleteFlg",
        "B_Id",
        "O_Id",
        "M_Id",
        "commentId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Topic extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_TopicDB";
    public static $guarded = ["id"];
    public static $primary_key = "topicId";
    public static $fillable = [
        "hospitalId",
        "distributorId",
        "registrationTime",
        "topicId",
        "topicTitle",
        "topicName",
        "updateTime",
        "topicContent",
        "authKey",
        "lastName",
        "commentCount",
        "distributorName",
        "tenantId",
        "adminViewFlg"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class TenantMaster extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_MasterDB";
    public static $guarded = ["id"];
    public static $primary_key = "loginId";
    public static $mail_field_title = "mailAddress";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "mailAddress",
        "name",
        "loginId",
        "password",
        "tenantId",
        "tenantName",
        "authority",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class ItemBulkUpsertTrDB extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "T_itemBulkUpsert";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "itemName",
        "o_itemName",
        "itemCode",
        "o_itemCode",
        "itemStandard",
        "o_itemStandard",
        "itemJANCode",
        "o_itemJANCode",
        "makerName",
        "o_makerName",
        "catalogNo",
        "o_catalogNo",
        "minPrice",
        "o_minPrice",
        "officialFlag",
        "o_officialFlag",
        "officialprice",
        "o_officialprice",
        "quantity",
        "o_quantity",
        "quantityUnit",
        "o_quantityUnit",
        "itemUnit",
        "o_itemUnit",
        "tenantId",
        "updateText",
        "itemId",
        "officialpriceOld",
        "o_officialpriceO",
        "serialNo",
        "o_serialNo",
        "lotManagement",
        "o_lotManagement",
        "category",
        "o_category",
        "itemsAuthKey",
        "smallCategory",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class PriceUpsertTrDB extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "T_Price";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "priceId",
        "itemId",
        "hospitalId",
        "distributorId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "notice",
        "hospitalName",
        "distributorName",
        "itemsAuthKey",
        "makerName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "requestFlg",
        "requestId",
        "unitPrice",
        "distributorMCode"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class InHospitalTrDb extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "T_inHpTrdb";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "inHospitalItemId",
        "authKey",
        "authKey2",
        "notUsedFlag",
        "itemId",
        "hospitalId",
        "distributorId",
        "catalogNo",
        "serialNo",
        "medicineCategory",
        "homeCategory",
        "HPstock",
        "priceId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "minPrice",
        "unitPrice",
        "measuringInst",
        "notice",
        "distributorMCode"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class PriceInfoView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "T_PriceList";
    public static $guarded = ["id"];
    public static $primary_key = "priceId";
    public static $fillable = [
        "priceId",
        "authKey",
        "itemId",
        "distributorId",
        "quantity",
        "price",
        "hospitalId",
        "requestFlg",
        "quantityUnit",
        "itemUnit",
        "notice",
        "notUsedFlag",
        "requestId",
        "hospitalName",
        "distributorName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "tenantId",
        "distributorMCode"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class DistributorAndHospitalDB extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "T_distributor";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "distributorId",
        "distributorName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "hospitalId",
        "authKey",
        "vendorFlag",
        "distCommonId",
        "tenantId",
        "hospitalName",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}
class DistributorUpsertDB extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "T_DistributorTr";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "distributorName",
        "distributorId",
        "distCommonId",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "vendorFlag",
        "hospitalId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class AllNewItemInsert extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "T_allNewIns";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "itemId",
        "priceId",
        "inHospitalItemId",
        "hospitalId",
        "distributorId",
        "authKey",
        "itemName",
        "category",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "lotManagement",
        "officialFlag",
        "officialprice",
        "catalogNo",
        "serialNo",
        "medicineCategory",
        "homeCategory",
        "HPstock",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "minPrice",
        "unitPrice",
        "measuringInst",
        "notice",
        "tenantId",
        "smallCategory",
        "distributorMCode"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}



class PayScheduleItems extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_PayScheduleDB";
    public static $guarded = ["id"];
    public static $primary_key = "payoutPlanId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "payoutPlanTime",
        "payoutPlanId",
        "pickingId",
        "inHospitalItemId",
        "itemId",
        "hospitalId",
        "cardId",
        "sourceDivisionId",
        "targetDivisionId",
        "payoutQuantity",
        "outOfStockStatus",
        "sourceDivision",
        "targetDivision",
    ];

    //デフォルト値
    public static $attributes = [
        'outOfStockStatus' => '1',
    ];

    public static $select = [
        'outOfStockStatus' => [
            '1'=>"未チェック",
            '2'=>"払出可能",
            '3'=>"欠品",
        ]
    ];
}


class PickingHistory extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_PickingSlip";
    public static $guarded = ["id"];
    public static $primary_key = "pickingId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "pickingId",
        "hospitalId",
        "divisionId",
        "pickingStatus",
    ];

    //デフォルト値
    public static $attributes = [
        'pickingStatus' => '1',
    ];

    public static $select = [
        'pickingStatus' => [
            '1'=>"未確認",
            '2'=>"完了",
        ]
    ];
}

class PayScheduleItemsView extends Model
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "payoutSchedule";
    public static $guarded = ["id"];
    public static $primary_key = "payoutPlanId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "payoutPlanTime",
        "payoutPlanId",
        "pickingId",
        "inHospitalItemId",
        "itemId",
        "hospitalId",
        "cardId",
        "sourceDivisionId",
        "targetDivisionId",
        "payoutQuantity",
        "outOfStockStatus",
        "catalogNo",
        "serialNo",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "price",
        "minPrice",
        "officialFlag",
        "priceId",
        "unitPrice",
        "measuringInst",
        "distributorName",
        "lotManagement",
        "category",
    ];

    //デフォルト値
    public static $attributes = [
        'outOfStockStatus' => '1',
    ];

    public static $select = [
        'outOfStockStatus' => [
            '1'=>"未チェック",
            '2'=>"払出可能",
            '3'=>"欠品",
        ]
    ];
}
