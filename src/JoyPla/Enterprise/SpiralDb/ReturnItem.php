<?php

namespace App\SpiralDb;
use SpiralORM;

class ReturnItem extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";
    public static $spiral_db_name = "NJ_ReturnDB";
    public static $guarded = ["id"];
    public static $primary_key = "returnID";
    public static $fillable = [
        "registrationTime",
        "orderCNumber",
        "receivingHId",
        "inHospitalItemId",
        "receivingNumber",
        "price",
        "returnID",
        "returnCount",
        "returnPrice",
        "hospitalId",
        "returnHistoryID",
        "lotNumber",
        "lotDate",
        "itemId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}