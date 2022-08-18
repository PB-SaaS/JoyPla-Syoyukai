<?php

namespace App\SpiralDb;
use SpiralORM;

class CardView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "CardInfo";
    public static $guarded = ["id"];
    public static $primary_key = "cardId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "cardId",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "quantity",
        "payoutId",
        "distributorId",
        "catalogNo",
        "serialNo",
        "quantityUnit",
        "itemUnit",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "price",
        "oldPrice",
        "labelId",
        "minPrice",
        "officialFlag",
        "priceId",
        "unitPrice",
        "measuringInst",
        "distributorName",
        "hospitalName",
        "divisionName",
        "lotNumber",
        "lotDate",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}