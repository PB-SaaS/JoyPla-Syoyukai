<?php

namespace App\SpiralDb;
use SpiralORM;

class Card extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_CardDB";
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
        "payoutId"
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}