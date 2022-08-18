<?php
namespace App\SpiralDb;
use SpiralORM;

class ConsumptionView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_consumeView";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "billingNumber",
        "hospitalId",
        "hospitalName",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "divisionName",
        "divisionType",
        "deleteFlag",
        "hospitalName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "billingDate",
        "billingStatus",
    ];

    //デフォルト値
    public static $attributes = [];
} 