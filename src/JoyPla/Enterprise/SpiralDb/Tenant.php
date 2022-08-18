<?php
namespace App\SpiralDb;
use SpiralORM;

class Tenant extends SpiralORM
{
    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_TenantAdminDB";
    public static $guarded = ["id"];
    public static $primary_key = "tenantId";
    public static $fillable = [
        "registrationTime",
        "tenantId",
        "tenantName",
        "note",
        "tenantKind",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        "tenantKind" => [1,2]
    ];
}