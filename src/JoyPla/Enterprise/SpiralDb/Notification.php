<?php
namespace App\SpiralDb;
use SpiralORM;

class Notification extends SpiralORM
{
    const CREATED_AT = "";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_NoticeDB";
    public static $guarded = ["id"];
    public static $primary_key = "";
    public static $fillable = [
        "registrationTime",
        "noticeId",
        "authKey",
        "title",
        "content",
        "creator",
        "type",
    ]; 

    //デフォルト値
    public static $attributes = [];

    public static $select = [
    ];
}