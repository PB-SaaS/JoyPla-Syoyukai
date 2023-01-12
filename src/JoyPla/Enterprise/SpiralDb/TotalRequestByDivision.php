<?php

namespace App\SpiralDb;

use SpiralORM;

class TotalRequestByDivision extends SpiralORM
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "updateTime";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_divRequestDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "authKey",
        "recordId",
        "hospitalId",
        "inHospitalItemId",
        "itemId",
        "requestQuantity",
        "sourceDivisionId",
        "targetDivisionId",
        "requestUniqueKey"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}
