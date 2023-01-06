<?php

namespace App\SpiralDb;

use SpiralORM;

class RequestItem extends SpiralORM
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_ItemRequestDB";
    public static $guarded = ["id"];
    public static $primary_key = "requestId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "itemReqAuthKey",
        "requestId",
        "requestHId",
        "hospitalId",
        "itemId",
        "inHospitalItemId",
        "sourceDivisionId",
        "targetDivisionId",
        "requestQuantity",
        "requestType",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "unitPrice"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "requestType" => [
            "1" => "個別請求",
            "2" => "消費請求"
            ]
        ];
}
