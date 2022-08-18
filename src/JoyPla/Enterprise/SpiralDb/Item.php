<?php
namespace App\SpiralDb;
use SpiralORM;

class Item extends SpiralORM
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