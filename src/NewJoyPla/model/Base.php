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
        'unitPrice'
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
    const UPDATED_AT = 'updateTime';

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
        'distributorName'
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

