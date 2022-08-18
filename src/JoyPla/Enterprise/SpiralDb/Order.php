<?php
namespace App\SpiralDb;
use SpiralORM;

class Order extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_OrderHDB";
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
        /*"f002664851",*/ //ルックアップキーは除外
        "distributorId",
        "ordererUserName",
        "ordercomment",
        "orderAuthKey",
        "staffName",
        "adjustment",
    ]; 

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "orderStatus" => [
            1 =>"未発注",
            2 =>"発注完了",
            3 =>"受注完了",
            4 =>"納期報告済",
            5 =>"一部入庫完了",
            6 =>"入庫完了",
            7 =>"納品取消",
            8 =>"貸出品",
        ],
        "adjustment" => [
            1 => "定数発注",
            2 => "個別発注",
        ],
    ];
}

