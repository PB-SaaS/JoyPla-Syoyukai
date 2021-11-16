<?php
namespace App\Model;

use Model;

class UsedSlipHistoy extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "deleteFlag";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "billingStatus" => [1,2]
    ];
}

class Order extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
        "itemId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class OrderHistory extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
        ]
    ];
}

class Receiving extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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

class ReceivingHistoryView extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";
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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "returnData";
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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class ReturnHistory extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
        "category"
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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
        "previousStock"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class InHospitalItem extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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



class Hospital extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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

    public static $select = [];
}


class HospitalUser extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
        "hospitalAuthKey"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class DistributorAffiliationView extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Distributor extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Tenant extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
        "receivingTarget"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

}


class OrderedItemView extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
        "lotManagement"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

}

class Lot extends Model
{
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
        "invUnitPrice"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}

class InventoryItemView extends Model
{
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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

    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}

class InventoryEnd extends Model
{
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
        "invHAuthKey"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}


class InventoryView extends Model
{
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    
    const CREATED_AT = "";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
        "requestId"
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
    
    const CREATED_AT = "";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    
    const CREATED_AT = "";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
        "category"
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


class Comment extends Model
{
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}


class Topic extends Model
{
    
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}

class TenantMaster extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_MasterDB";
    public static $guarded = ["id"];
    public static $primary_key = "loginId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "mailAddress",
        "name",
        "loginId",
        "password",
        "tenantId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}


class ItemBulkUpsertTrDB extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}


class PriceUpsertTrDB extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}
