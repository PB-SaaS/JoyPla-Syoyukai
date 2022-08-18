<?php
namespace App\SpiralDb;
use SpiralORM;

class DistributorAffiliationView extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "invitingDB";
    public static $guarded = ["id"];
    public static $primary_key = "affiliationId";
    public static $mail_field_title = "mailAddress";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "authKey",
        "affiliationId",
        "distributorId",
        "OUserPermission",
        "loginId",
        "hospitalId",
        "invitingTime",
        "invitingAgree",
        "hospitalName",
        "distributorName",
        "mailAddress",
        "name",
        "nameKana",
        "tenantId",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [];
}