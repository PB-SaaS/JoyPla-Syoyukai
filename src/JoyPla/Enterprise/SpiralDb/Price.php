<?php
namespace App\SpiralDb;
use SpiralORM;

class Price extends SpiralORM
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