<?php
namespace App\SpiralDb;
use SpiralORM;

class StockView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_stocks";
    public static $guarded = ["id"];
    public static $primary_key = "recordId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "stockQuantity",
        "authKey",
        "orderWithinCount",
        "rackName",
        "distributorId",
        "invFinishTime",
        "planInventoryCnt",
        "constantByDiv",
        "requiredOrderNum",
        "barcode",
        "labelId",
        "recordId",
        "divisionName",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
} 

