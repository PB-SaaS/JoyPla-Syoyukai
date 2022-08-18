<?php
namespace App\SpiralDb;
use SpiralORM;

class InHospitalItem extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_inHPItemDB";
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
        "labelId",
        "unitPrice",
        "measuringInst",
        "inItemImage",
        "distributorMCode",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
} 
