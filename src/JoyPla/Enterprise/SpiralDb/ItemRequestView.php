<?php

namespace App\SpiralDb;

use SpiralORM;

class ItemRequestView extends SpiralORM
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "ItemRequestView";
    public static $guarded = ["id"];
    public static $primary_key = "requestHId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "requestHId",
        "hospitalId",
        "sourceDivisionId",
        "sourceDivision",
        "targetDivisionId",
        "targetDivision",
        "requestType",
        "totalAmount",
        "itemsNumber",
        "requestUserName",
        "hospitalName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "divisionType",
        "deleteFlag"
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
