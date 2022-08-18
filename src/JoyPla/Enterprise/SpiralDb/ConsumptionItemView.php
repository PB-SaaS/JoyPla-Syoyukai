<?php
namespace App\SpiralDb;
use SpiralORM;

class ConsumptionItemView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_ConsItemView";
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
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "officialFlag",
        "tenantId",
        "catalogNo",
        "serialNo",
        "category",
        "divisionName",
        "inItemImage"
    ];

    //デフォルト値
    public static $attributes = [];
}
 