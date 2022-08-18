<?php
namespace App\SpiralDb;
use SpiralORM;

class OrderItem extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_OrderDB";
    public static $guarded = ["id"];
    public static $primary_key = "orderCNumber";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "receivingTime",
        "dueDate",
        "orderCNumber",
        "hospitalId",
        "inHospitalItemId",
        "orderNumber",
        "price",
        "orderQuantity",
        "orderPrice",
        "receivingFlag",
        "receivingNum",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "divisionId",
        "distributorId",
        "lotManagement",
        "itemId",
        "priceId",
        "adjustment",
        "useMedicode",
        "hospitalCode",
        "distributorCode",
        "medicodeStatus",
        "medicodeSentDate",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "adjustment" => [
            1 => "定数発注",
            2 => "個別発注",
        ],
    ];
}