<?php
namespace App\SpiralDb;
use Model;

class Item extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_itemDB";
    public static $guarded = ["id"];
    public static $primary_key = "itemId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "itemId",
        "makerName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "officialFlag",
        "officialpriceOld",
        "officialprice",
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
        "catalogNo",
        "serialNo",
        "lotManagement",
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
        "hospitalAuthKey",
        "userCheck"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
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

    public static $select = [
        'receivingTarget' => [
            '1'=>'大倉庫',
            '2'=>'発注部署'
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
        "price",
        "quantity",
        "itemUnit",
        "quantityUnit",
        "hospitalId",
        "requestFlg",
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
        "itemId",
        "priceId",
        "inHospitalItemId",
        "authKey",
        "hospitalId",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "notice",
        "HPstock",
        "labelId",
        "unitPrice",
        "measuringInst",
        "inItemImage",
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

    public static $spiral_db_name = "340_inItems";
    public static $guarded = ["id"];
    public static $primary_key = "inHospitalItemId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "itemId",
        "priceId",
        "inHospitalItemId",
        "authKey",
        "hospitalId",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "notice",
        "HPstock",
        "unitPrice",
        "measuringInst",
        "makerName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "officialFlag",
        "officialpriceOld",
        "officialprice",
        "catalogNo",
        "serialNo",
        "lotManagement",
        "category",
        "distributorId",
        "distributorName",
        "price",
        "quantity",
        "itemUnit",
        "quantityUnit",
        "inItemImage",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

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
        "distCommonId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class Consumption extends Model
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
        "billingDate",
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


class ConsumptionItem extends Model
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
class ConsumptionView extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_consumeView";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "billingNumber",
        "hospitalId",
        "hospitalName",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "divisionName",
        "divisionType",
        "deleteFlag",
        "hospitalName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "billingDate",
        "billingStatus",
    ];

    //デフォルト値
    public static $attributes = [];
}

class ConsumptionItemView extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_ConsItemView";
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
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "officialFlag",
        "tenantId",
        "catalogNo",
        "serialNo",
        "category",
        "divisionName",
        "inItemImage"
    ];

    //デフォルト値
    public static $attributes = [];
}


class OrderItem extends Model
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

class Order extends Model
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
        "adjustment",
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


class OrderView extends Model
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
        "receivingTarget",
        "adjustment",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

}

class OrderItemView extends Model
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

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
        "serialNo",
        "catalogNo",
        "distributorName",
        "inItemImage"
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
        "authKey",
        "stockQuantity",
        "orderWithinCount",
        "rackName",
        "invFinishTime",
        "planInventoryCnt",
        "constantByDiv",
        "requiredOrderNum",
        "barcode",
        "labelId",
        "recordId"
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

    public static $spiral_db_name = "340_stocks";
    public static $guarded = ["id"];
    public static $primary_key = "recordId";
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
        "labelId",
        "recordId",
        "divisionName",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}