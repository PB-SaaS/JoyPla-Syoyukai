<?php
namespace App\SpiralDb;
use SpiralORM;

class OrderView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_order";
    public static $guarded = ["id"];
    public static $primary_key = "orderNumber";
    public static $fillable = [
        "registrationTime",
        "orderTime",
        "receivingTime",
        "orderNumber",
        "hospitalId",
        "divisionId",
        "itemsNumber",
        "totalAmount",
        "orderStatus",
        "hachuRarrival",
        "distributorId",
        "distributorName",
        "divisionName",
        "hospitalName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "ordererUserName",
        "ordercomment",
        "orderAuthKey",
        "receivingTarget",
        "adjustment",
        "distrComment",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

} 