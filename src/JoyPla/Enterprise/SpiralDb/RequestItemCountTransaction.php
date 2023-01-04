<?php

namespace App\SpiralDb;

use SpiralORM;

class RequestItemCountTransaction extends SpiralORM
{
    public const CREATED_AT = "registrationTime";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "NJ_requestTRDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "recordId",
        "hospitalId",
        "inHospitalItemId",
        "itemId",
        "quantity",
        "sourceDivisionId",
        "targetDivisionId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}
