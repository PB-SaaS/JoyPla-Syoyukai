<?php

namespace App\SpiralDb;

use SpiralORM;

class Payout extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_PayoutHDB";
    public static $guarded = ["id"];
    public static $primary_key = "payoutHistoryId";
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
        "totalAmount"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}
