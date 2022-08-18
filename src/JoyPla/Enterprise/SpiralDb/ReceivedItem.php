<?php
namespace App\SpiralDb;
use SpiralORM;

class ReceivedItem extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_ReceivingDB";
    public static $guarded = ["id"];
    public static $primary_key = "receivingNumber";
    public static $fillable = [
        "registrationTime",
        "orderCNumber",
        "receivingCount",
        "receivingHId",
        /*"f002664881",*/ //ルックアップキーは除外
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
        "itemId"
    ]; 

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}
