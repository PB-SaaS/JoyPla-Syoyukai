<?php
namespace App\SpiralDb;
use SpiralORM;

class Hospital extends SpiralORM
{

    const CREATED_AT = "registrationTime";
    const UPDATED_AT = "updateTime";
    const DELETED_AT = "";

    public static $spiral_db_name = "NJ_HospitalDB";
    public static $guarded = ["id"];
    public static $primary_key = "hospitalId";
    public static $fillable = [
        "registrationTime",
        "updateTime",
        "hospitalId",
        "hospitalName",
        "postalCode",
        "prefectures",
        "address",
        "phoneNumber",
        "faxNumber",
        "tenantId",
        "name",
        "nameKana",
        "mailAddress",
        "contactAddress",
        "plan",
        "receivingTarget",
        "function1",
        "function2",
        "function3",
        "function4",
        "function5",
        "function6",
        "function7",
        "function8",
        "authKey",
        "registerableNum",
        "labelDesign1",
        "labelDesign2",
        "labelDesign3",
        "billingUnitPrice",
        "payoutUnitPrice",
        "invUnitPrice",
    ];

    //デフォルト値
    public static $attributes = [];

    public static $select = [
        'receivingTarget' => [
            '1'=>'大倉庫',
            '2'=>'発注部署'
            ]
        ];
}

 