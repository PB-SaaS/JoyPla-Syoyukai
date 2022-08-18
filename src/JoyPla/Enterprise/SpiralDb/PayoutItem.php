<?php

namespace App\SpiralDb;
use SpiralORM;

class PayoutItem extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_PayoutDB";
    public static $guarded = ["id"];
    public static $primary_key = "payoutId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "payoutHistoryId",
        "payoutId",
        "inHospitalItemId",
        "hospitalId",
        "sourceDivisionId",
        "targetDivisionId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "price",
        "payoutQuantity",
        "payoutAmount",
        "payoutCount",
        "payoutLabelCount",
        "adjAmount",
        "priceAfterAdj",
        "lotNumber",
        "lotDate",
        "unitPrice",
        "cardId",
        "itemId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
    
}