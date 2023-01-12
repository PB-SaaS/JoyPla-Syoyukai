<?php

namespace App\SpiralDb;

use SpiralORM;

class TotalRequestByInHpItemView extends SpiralORM
{
    public const CREATED_AT = "";
    public const UPDATED_AT = "";
    public const DELETED_AT = "";

    public static $spiral_db_name = "TotalRequestItem";
    public static $guarded = ["id"];
    public static $primary_key = "recordId";
    public static $fillable = [
        "recordId",
        "hospitalId",
        "inHospitalItemId",
        "itemId",
        "requestQuantity",
        "sourceDivisionId",
        "targetDivisionId",
        "stockQuantity",
        "rackName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "catalogNo",
        "serialNo",
        "category",
        "smallCategory",
        "lotManagement",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "notUsedFlag",
        "measuringInst"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}
