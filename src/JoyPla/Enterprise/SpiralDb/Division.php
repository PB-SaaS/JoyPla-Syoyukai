<?php
namespace App\SpiralDb;
use SpiralORM;

class Division extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "deleteFlag";

    public static $spiral_db_name = "NJ_divisionDB";
    public static $guarded = ["id"];
    public static $primary_key = "divisionId";
    public static $fillable = [
        "registrationTime",
        "divisionId",
        "hospitalId",
        "divisionName",
        "divisionType",
        "deleteFlag",
        "authkey"
    ];

    //デフォルト値
    public static $attributes = [];
    
}
 