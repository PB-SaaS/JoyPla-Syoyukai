<?php

namespace App\SpiralDb;
use SpiralORM;

class ReturnHistory extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_ReturnHDB";
    public static $guarded = ["id"];
    public static $primary_key = "returnHistoryID";
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