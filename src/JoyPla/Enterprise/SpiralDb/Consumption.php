<?php
namespace App\SpiralDb;
use SpiralORM;

class Consumption extends  SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_BillingHDB";
    public static $guarded = ["id"];
    public static $primary_key = "billingNumber";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "billingDate",
        "billingNumber",
        "hospitalId",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "billingAuthKey",
        "billingStatus",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "billingStatus" => [1,2]
    ];
}
 