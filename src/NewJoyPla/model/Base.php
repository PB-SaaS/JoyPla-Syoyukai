<?php
namespace App\Model;

use Model;

class UsedSlipHistoy extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_UsedSlipHDB';
    public static $guarded = ['id'];
    public static $primary_key = 'usedSlipId';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'usedTime',
        'usedSlipId',
        'itemsNumber',
        'usedSlipStatus',
        'totalAmount',
        'distributorId',
        'divisionId',
        'hospitalId',
        'reportPersonName',
        'authKey'
    ];

    //デフォルト値
    public static $attributes = [];
    public static $select = [
        'usedSlipStatus' => [1 => '未確定',2 => '確定済み']
    ];
}

class Borrowing extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_Borrowing';
    public static $guarded = ['id'];
    public static $primary_key = 'borrowingId';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'borrowingId',
        'borrowingNum',
        'inHospitalItemId',
        'lotNumber',
        'lotDate',
        'divisionId',
        'usedSlipId',
        'price',
        'quantity',
        'quantityUnit',
        'itemUnit',
        'usedDate',
        'distributorId'
    ];

    //デフォルト値
    public static $attributes = [];
}

class Division extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_divisionDB';
    public static $guarded = ['id'];
    public static $primary_key = 'divisionId';
    public static $fillable = [
        'registrationTime',
        'divisionId',
        'hospitalId',
        'divisionName',
        'divisionType',
        'deleteFlag',
        'authkey'
    ];

    //デフォルト値
    public static $attributes = [];
}

class Billing extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_BillingDB';
    public static $guarded = ['id'];
    public static $primary_key = 'id';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'inHospitalItemId',
        'billingNumber',
        'price',
        'billingQuantity',
        'billingAmount',
        'hospitalId',
        'divisionId',
        'quantity',
        'quantityUnit',
        'itemUnit',
        'lotNumber',
        'lotDate',
        'unitPrice',
        'lotManagement'
    ];

    //デフォルト値
    public static $attributes = [];
}

class BillingHistory extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_BillingHDB';
    public static $guarded = ['id'];
    public static $primary_key = 'billingNumber';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'billingNumber',
        'hospitalId',
        'divisionId',
        'itemsNumber',
        'totalAmount',
        'billingAuthKey',
        'billingStatus',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        'billingStatus' => [1,2]
    ];
}

class Order extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_OrderDB';
    public static $guarded = ['id'];
    public static $primary_key = 'orderCNumber';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'receivingTime',
        'dueDate',
        'orderCNumber',
        'hospitalId',
        'inHospitalItemId',
        'orderNumber',
        'price',
        'orderQuantity',
        'orderPrice',
        'receivingFlag',
        'receivingNum',
        'quantity',
        'quantityUnit',
        'itemUnit',
        'divisionId',
        'distributorId',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class OrderHistory extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_OrderHDB';
    public static $guarded = ['id'];
    public static $primary_key = 'orderNumber';
    public static $fillable = [
        'registrationTime',
        'orderTime',
        'receivingTime',
        'orderNumber',
        'hospitalId',
        'divisionId',
        'itemsNumber',
        'totalAmount',
        'orderStatus',
        'hachuRarrival',
        /*'f002664851',*/ //ルックアップキーは除外
        'distributorId',
        'ordererUserName',
        'ordercomment',
        'orderAuthKey',
        'staffName',
        
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        'orderStatus' => [
            1 =>'未発注',
            2 =>'発注完了',
            3 =>'受注完了',
            4 =>'納期報告済',
            5 =>'一部入庫完了',
            6 =>'入庫完了',
            7 =>'納品取消',
            8 =>'貸出品',
        ]
    ];
}

class Receiving extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_ReceivingDB';
    public static $guarded = ['id'];
    public static $primary_key = 'receivingNumber';
    public static $fillable = [
        'registrationTime',
        'orderCNumber',
        'receivingCount',
        'receivingHId',
        /*'f002664881',*/ //ルックアップキーは除外
        'inHospitalItemId',
        'receivingNumber',
        'price',
        'receivingPrice',
        'hospitalId',
        'totalReturnCount',
        'divisionId',
        'distributorId',
        'adjAmount',
        'priceAfterAdj',
        'lotNumber',
        'lotDate',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class ReceivingView extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = '310_receItems';
    public static $guarded = ['id'];
    public static $primary_key = 'receivingNumber';
    public static $fillable = [
        'registrationTime',
        'orderCNumber',
        'receivingCount',
        'receivingHId',
        'inHospitalItemId',
        'receivingNumber',
        'price',
        'receivingPrice',
        'hospitalId',
        'totalReturnCount',
        'divisionId',
        'distributorId',
        'adjAmount',
        'priceAfterAdj',
        'lotNumber',
        'lotDate',
        'divisionName',
        'distributorName',
        'orderHistoryId',
        'makerName',
        'itemName',
        'itemCode',
        'itemStandard',
        'itemJANCode',
        'quantity',
        'quantityUnit',
        'itemUnit',
        'unitPrice',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

}


class ReceivingHistory extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_ReceivingHDB';
    public static $guarded = ['id'];
    public static $primary_key = 'receivingHId';
    public static $fillable = [
        'registrationTime',
        'receivingHId',
        'distributorId',
        'orderHistoryId',
        'hospitalId',
        'itemsNumber',
        'authKey',
        /*'f002664878',*/ //ルックアップキーは除外
        'divisionId',
        'recevingStatus',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        'recevingStatus' => [
            1 =>'通常入庫',
            2 =>'貸出品',
        ]
    ];
}
//Return は使用できない文字列なので、ReturnItemにします
class ReturnItem extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_ReturnDB';
    public static $guarded = ['id'];
    public static $primary_key = 'receivingNumber';
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

class ReturnHistory extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_ReturnHDB';
    public static $guarded = ['id'];
    public static $primary_key = 'returnHistoryID';
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
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = '310_payoutItems';
    public static $guarded = ['id'];
    public static $primary_key = 'payoutId';
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "payoutHistoryId",
        "payoutId",
        "inHospitalItemId",
        "hospitalId",
        "sourceDivisionId",
        "targetDivisionId",
        "sourceDivision",
        "targetDivision",
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
        'distributorId',
        'catalogNo',
        'serialNo',
        'medicineCategory',
        'homeCategory',
        'notUsedFlag',
        'itemId',
        'itemName',
        'itemCode',
        'itemStandard',
        'itemJANCode',
        'notice',
        'HPstock',
        'makerName',
        'labelId',
        'officialFlag',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}


class Payout extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_PayoutDB';
    public static $guarded = ['id'];
    public static $primary_key = 'payoutId';
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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}

class PayoutHistory extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_PayoutHDB';
    public static $guarded = ['id'];
    public static $primary_key = 'payoutHistoryId';
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
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_itemDB';
    public static $guarded = ['id'];
    public static $primary_key = 'itemId';
    public static $fillable = [
        'itemId',
        'itemName',
        'itemCode',
        'itemStandard',
        'itemJANCode',
        'makerName',
        'officialFlag',
        'tenantId',
        'quantity',
        'quantityUnit',
        'itemUnit',
        'minPrice',
        'itemsAuthKey',
        'janTenantId',
        'requestId',
        'requestFlg',
        'requestItemId',
        'hospitalId',
        'registrationTime',
        'officialpriceOld',
        'officialprice',
        'catalogNo',
        'serialNo',
        'lotManagement'
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        'requestFlg' => [
            1 => '採用',
            2 => '不採用',
            3 => '見積中',
            4 => '業者記入済'
        ]
    ];
}

class InventoryAdjustmentTransaction extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_inventoryTRDB';
    public static $guarded = ['id'];
    public static $primary_key = 'id';
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
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class InHospitalItem extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_inHPItemDB';
    public static $guarded = ['id'];
    public static $primary_key = 'inHospitalItemId';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'inHospitalItemId',
        'authKey',
        'hospitalId',
        'distributorId',
        'catalogNo',
        'serialNo',
        'quantity',
        'quantityUnit',
        'itemUnit',
        'medicineCategory',
        'homeCategory',
        'notUsedFlag',
        'itemId',
        'itemName',
        'itemCode',
        'itemStandard',
        'itemJANCode',
        'notice',
        'HPstock',
        'makerName',
        'price',
        'oldPrice',
        'labelId',
        'minPrice',
        'officialFlag',
        'priceId',
        'unitPrice',
        'measuringInst',
        'distributorName',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class InHospitalItemView extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'itemInHospitalv2';
    public static $guarded = ['id'];
    public static $primary_key = 'inHospitalItemId';
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

    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}



class Hospital extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_HospitalDB';
    public static $guarded = ['id'];
    public static $primary_key = 'hospitalId';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'hospitalId',
        'hospitalName',
        'postalCode',
        'prefectures',
        'address',
        'phoneNumber',
        'faxNumber',
        'tenantId',
        'name',
        'nameKana',
        'mailAddress',
        'contactAddress',
        'plan',
        'receivingTarget',
        'function1',
        'function2',
        'function3',
        'function4',
        'function5',
        'function6',
        'function7',
        'function8',
        'authKey',
        'registerableNum',
        'labelDesign1',
        'labelDesign2',
        'billingUnitPrice',
        'payoutUnitPrice',
        'invUnitPrice',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class HospitalUser extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_HUserDB';
    public static $guarded = ['id'];
    public static $primary_key = 'loginId';
    public static $mail_field_title = 'mailAddress';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'authKey',
        'hospitalId',
        'divisionId',
        'userPermission',
        'loginId',
        'loginPassword',
        'name',
        'nameKana',
        'mailAddress',
        'remarks',
        'termsAgreement',
        'tenantId',
        'agreementDate',
        'hospitalAuthKey'
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class DistributorUser extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_OUserDB';
    public static $guarded = ['id'];
    public static $primary_key = 'loginId';
    public static $mail_field_title = 'mailAddress';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'authKey',
        'distributorId',
        'userPermission',
        'loginId',
        'loginPassword',
        'name',
        'nameKana',
        'mailAddress',
        'remarks',
        'hospitalId',
        'termsAgreement',
        'agreementDate',
        'distAuthKey'
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Distributor extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_distributorDB';
    public static $guarded = ['id'];
    public static $primary_key = 'distributorId';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'distributorId',
        'distributorName',
        'postalCode',
        'prefectures',
        'address',
        'phoneNumber',
        'faxNumber',
        'hospitalId',
        'authKey',
        'vendorFlag',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Tenant extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_TenantAdminDB';
    public static $guarded = ['id'];
    public static $primary_key = 'tenantId';
    public static $fillable = [
        'registrationTime',
        'tenantId',
        'tenantName',
        'note',
        'tenantKind'
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        'tenantKind' => [1,2]
    ];
}

class AssociationTR extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'NJ_associationTR';
    public static $guarded = ['id'];
    public static $primary_key = 'id';
    public static $fillable = [
        'registrationTime',
        'usedSlipId',
        'orderNumber',
        'receivingHId',
        'billingNumber',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}

class Stock extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_stockDB';
    public static $guarded = ['id'];
    public static $primary_key = 'id';
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


class Card extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'NJ_CardDB';
    public static $guarded = ['id'];
    public static $primary_key = 'cardId';
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
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'CardInfo';
    public static $guarded = ['id'];
    public static $primary_key = 'cardId';
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "cardId",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "quantity",
        "payoutId",
        'distributorId',
        'catalogNo',
        'serialNo',
        'quantityUnit',
        'itemUnit',
        'medicineCategory',
        'homeCategory',
        'notUsedFlag',
        'itemId',
        'itemName',
        'itemCode',
        'itemStandard',
        'itemJANCode',
        'notice',
        'HPstock',
        'makerName',
        'price',
        'oldPrice',
        'labelId',
        'minPrice',
        'officialFlag',
        'priceId',
        'unitPrice',
        'measuringInst',
        'distributorName',
        'hospitalName',
        'divisionName',
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}


class OrderDataView extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = '';

    public static $spiral_db_name = 'orderdataDB';
    public static $guarded = ['id'];
    public static $primary_key = 'orderNumber';
    public static $fillable = [
        'registrationTime',
        'orderTime',
        'receivingTime',
        'orderNumber',
        'hospitalId',
        'divisionId',
        'itemsNumber',
        'totalAmount',
        'orderStatus',
        'hachuRarrival',
        'distributorId',
        'distributorName',
        'divisionName',
        'hospitalName',
        'postalCode',
        'prefectures',
        'address',
        'phoneNumber',
        'faxNumber',
        'ordererUserName',
        'ordercomment',
        'orderAuthKey',
        'receivingTarget'
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

}


class OrderedItemView extends Model
{
    const CREATED_AT = 'registrationTime';
    const UPDATED_AT = 'updateTime';

    public static $spiral_db_name = 'hacchuShouhin';
    public static $guarded = ['id'];
    public static $primary_key = 'orderCNumber';
    public static $fillable = [
        'registrationTime',
        'updateTime',
        'receivingTime',
        'dueDate',
        'orderCNumber',
        'hospitalId',
        'inHospitalItemId',
        'orderNumber',
        'price',
        'orderQuantity',
        'orderPrice',
        'receivingFlag',
        'receivingNum',
        'quantity',
        'quantityUnit',
        'itemUnit',
        'divisionId',
        'distributorId',
        'distributorName',
        'divisionName',
        'deleteFlag',
        'catalogNo',
        'serialNo',
        'medicineCategory',
        'homeCategory',
        'notUsedFlag',
        'itemId',
        'itemName',
        'itemCode',
        'itemStandard',
        'itemJANCode',
        'notice',
        'HPstock',
        'makerName',
        'labelId',
        'minPrice',
        'orderStatus'
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

}
