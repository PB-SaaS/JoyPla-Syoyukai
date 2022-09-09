<?php

namespace App\SpiralDb;

use SpiralORM;

class Division extends SpiralORM
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
        "authkey",
        "deliveryDestCode"
    ];

    //デフォルト値
    public static $attributes = [];
}
