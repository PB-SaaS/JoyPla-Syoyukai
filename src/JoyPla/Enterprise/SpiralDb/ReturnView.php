<?php

namespace App\SpiralDb;
use SpiralORM;

class ReturnView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_return";
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
        "divisionId",
        "hospitalName",
        "divisionName",
        "distributorName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}