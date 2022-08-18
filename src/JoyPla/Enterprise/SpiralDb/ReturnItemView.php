<?php

namespace App\SpiralDb;
use SpiralORM;

class ReturnItemView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_retunItem";
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
        "distributorId",
        "divisionId",
        "returnCount2",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "officialFlag",
        "minPrice",
        "officialprice",
        "catalogNo",
        "serialNo",
        "lotManagement",
        "category",
        "updateTime",
        "distributorName",
        "hospitalName",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "inItemImage",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}