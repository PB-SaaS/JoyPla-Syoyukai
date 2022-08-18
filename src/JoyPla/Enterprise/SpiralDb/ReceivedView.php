<?php
namespace App\SpiralDb;
use SpiralORM;

class ReceivedView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_received";
    public static $guarded = ["id"];
    public static $primary_key = "receivingHId";
    public static $fillable = [
        "registrationTime",
        "receivingHId",
        "distributorId",
        "orderHistoryId",
        "hospitalId",
        "itemsNumber",
        "authKey",
        "divisionId",
        "recevingStatus",
        "slipCategory",
        "totalAmount",
        "hospitalName",
        "distributorName",
        "orderNumber",
        "divisionName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "recevingStatus" => [
            1 =>"通常入庫",
            2 =>"貸出品",
        ] 
    ];
}
