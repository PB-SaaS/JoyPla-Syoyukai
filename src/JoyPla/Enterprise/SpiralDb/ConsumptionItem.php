<?php
namespace App\SpiralDb;
use SpiralORM;

class ConsumptionItem extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_BillingDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "inHospitalItemId",
        "billingNumber",
        "price",
        "billingQuantity",
        "billingAmount",
        "hospitalId",
        "divisionId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "lotNumber",
        "lotDate",
        "unitPrice",
        "lotManagement",
        "itemId"
    ];

    //デフォルト値
    public static $attributes = [];
} 