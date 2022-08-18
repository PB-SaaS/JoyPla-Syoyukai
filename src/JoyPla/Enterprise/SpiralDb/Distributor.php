<?php
namespace App\SpiralDb;
use SpiralORM;

class Distributor extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_distributorDB";
    public static $guarded = ["id"];
    public static $primary_key = "distributorId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "distributorId",
        "distributorName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "hospitalId",
        "authKey",
        "vendorFlag",
        "distCommonId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}
 