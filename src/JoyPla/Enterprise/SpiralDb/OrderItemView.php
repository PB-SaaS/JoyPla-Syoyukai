<?php
namespace App\SpiralDb;
use SpiralORM;

class OrderItemView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_orderItem";
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
        "distributorMCode",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "divisionName",
        "orderStatus",
        "category",
        "serialNo",
        "catalogNo",
        "distributorName",
        "adjustment",
        "inItemImage",
        "useMedicode",
        "hospitalCode",
        "distributorCode",
        "medicodeStatus",
        "medicodeSentDate",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];

} 