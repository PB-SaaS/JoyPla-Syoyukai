<?php
namespace App\SpiralDb;
use SpiralORM;

class InventoryAdjustmentTransaction extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_inventoryTRDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "divisionId",
        "inHospitalItemId",
        "count",
        "hospitalId",
        "orderWithinCount",
        "pattern",
        "lotUniqueKey",
        "lotNumber",
        "lotDate",
        "stockQuantity",
        "rackName",
        "constantByDiv",
        "loginId",
        "previousStock",
        "changeReason"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}