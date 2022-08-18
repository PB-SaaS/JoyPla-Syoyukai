<?php
namespace App\SpiralDb;
use SpiralORM;

class Stock extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_stockDB";
    public static $guarded = ["id"];
    public static $primary_key = "id";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "hospitalId",
        "divisionId",
        "inHospitalItemId",
        "authKey",
        "stockQuantity",
        "orderWithinCount",
        "rackName",
        "invFinishTime",
        "planInventoryCnt",
        "constantByDiv",
        "requiredOrderNum",
        "barcode",
        "labelId",
        "recordId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
} 