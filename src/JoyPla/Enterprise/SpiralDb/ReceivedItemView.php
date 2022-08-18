<?php
namespace App\SpiralDb;
use SpiralORM;

class ReceivedItemView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "340_receivedItem";
    public static $guarded = ["id"];
    public static $primary_key = "receivingNumber";
    public static $fillable = [
        "registrationTime",
        "orderCNumber",
        "receivingCount",
        "receivingHId",
        "inHospitalItemId",
        "receivingNumber",
        "price",
        "receivingPrice",
        "hospitalId",
        "totalReturnCount",
        "divisionId",
        "distributorId",
        "adjAmount",
        "priceAfterAdj",
        "lotNumber",
        "lotDate",
        "itemId",
        "orderType",
        "hospitalName",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "makerName",
        "officialFlag",
        "tenantId",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "catalogNo",
        "serialNo",
        "officialpriceOld",
        "officialprice",
        "lotManagement",
        "category",
        "divisionName",
        "distributorName",
        "inItemImage"
    ]; 

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "category" => [
            1 => "医療材料",
            2 => "薬剤",
            3 => "試薬",
            4 => "日用品",
            99 => "その他"
        ]
    ];
}
