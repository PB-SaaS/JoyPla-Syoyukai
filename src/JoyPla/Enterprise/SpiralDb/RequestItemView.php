<?php

namespace App\SpiralDb;

use SpiralORM;

class RequestItemView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "requestItem";
    public static $guarded = ["id"];
    public static $primary_key = "requestId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "requestId",
        "requestHId",
        "hospitalId",
        "itemId",
        "inHospitalItemId",
        "sourceDivisionId",
        "targetDivisionId",
        "requestQuantity",
        "requestType",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "unitPrice",
        "sourceDivision",
        "targetDivision",
        "catalogNo",
        "serialNo",
        "lotManagement",
        "category",
        "minPrice",
        "officialFlag",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "measuringInst",
        "distributorName",
        "notice",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "requestType" => [
            "1" => "個別請求",
            "2" => "消費請求"
        ]
    ];
}
