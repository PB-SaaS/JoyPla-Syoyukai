<?php
namespace App\SpiralDb;
use SpiralORM;

class InHospitalItemView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_inItems";
    public static $guarded = ["id"];
    public static $primary_key = "inHospitalItemId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "itemId",
        "priceId",
        "inHospitalItemId",
        "authKey",
        "hospitalId",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "notice",
        "HPstock",
        "unitPrice",
        "measuringInst",
        "makerName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "officialFlag",
        "officialpriceOld",
        "officialprice",
        "catalogNo",
        "serialNo",
        "lotManagement",
        "category",
        "distributorId",
        "distributorName",
        "price",
        "quantity",
        "itemUnit",
        "quantityUnit",
        "distributorMCode",
        "inItemImage",
        "labelId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

} 