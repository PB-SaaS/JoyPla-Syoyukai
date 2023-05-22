<?php

namespace JoyPla\InterfaceAdapters\GateWays;

use framework\SpiralConnecter\SpiralDB;

class ModelRepository
{
    public static function getHospitalUserMailInstance()
    {
        return SpiralDb::mail('NJ_HUserDB');
    }

    public static function getHospitalUserInstance()
    {
        return SpiralDb::title('NJ_HUserDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'authKey',
            'hospitalId',
            'divisionId',
            'userPermission',
            'loginId',
            'loginPassword',
            'name',
            'nameKana',
            'mailAddress',
            'remarks',
            'termsAgreement',
            'tenantId',
            'agreementDate',
            'hospitalAuthKey',
            'userCheck',
        ]);
    }

    public static function getDistributorInstance()
    {
        return SpiralDB::title('NJ_distributorDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'distributorId',
            'distributorName',
            'postalCode',
            'prefectures',
            'address',
            'phoneNumber',
            'faxNumber',
            'hospitalId',
            'authKey',
            'vendorFlag',
            'distCommonId',
            'orderMethod',
        ]);
    }

    public static function getInvitingInstance()
    {
        return SpiralDb::title('invitingDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'authKey',
            'affiliationId',
            'distributorId',
            'OUserPermission',
            'loginId',
            'hospitalId',
            'invitingTime',
            'invitingAgree',
            'hospitalName',
            'distributorName',
            'mailAddress',
            'name',
            'nameKana',
            'tenantId',
        ]);
    }

    public static function getInvitingMailInstance()
    {
        return SpiralDb::mail('invitingDB');
    }

    public static function getOrderViewInstance()
    {
        return SpiralDB::title('340_order')->value([
            'id',
            'registrationTime',
            'orderTime',
            'receivingTime',
            'orderNumber',
            'hospitalId',
            'divisionId',
            'itemsNumber',
            'totalAmount',
            'orderStatus',
            'hachuRarrival',
            'distributorId',
            'distributorName',
            'divisionName',
            'hospitalName',
            'postalCode',
            'prefectures',
            'address',
            'phoneNumber',
            'faxNumber',
            'ordererUserName',
            'ordercomment',
            'orderAuthKey',
            'receivingTarget',
            'adjustment',
            'distrComment',
            'orderMethod',
            'sentFlag',
        ]);
    }

    public static function getOrderItemViewInstance()
    {
        return SpiralDB::title('340_orderItem')->value([
            'id',
            'registrationTime',
            'updateTime',
            'receivingTime',
            'dueDate',
            'orderCNumber',
            'hospitalId',
            'inHospitalItemId',
            'orderNumber',
            'price',
            'orderQuantity',
            'orderPrice',
            'receivingFlag',
            'receivingNum',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'divisionId',
            'distributorId',
            'lotManagement',
            'itemId',
            'priceId',
            'distributorMCode',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'makerName',
            'divisionName',
            'orderStatus',
            'category',
            'serialNo',
            'catalogNo',
            'distributorName',
            'adjustment',
            'inItemImage',
            'useMedicode',
            'hospitalCode',
            'distributorCode',
            'medicodeStatus',
            'medicodeSentDate',
            'orderMethod',
        ]);
    }

    public static function getDivisionInstance()
    {
        return SpiralDB::title('NJ_divisionDB')->value([
            'id',
            'registrationTime',
            'divisionId',
            'hospitalId',
            'divisionName',
            'divisionType',
            'deleteFlag',
            'authkey',
            'deliveryDestCode',
        ]);
    }

    public static function getOrderInstance()
    {
        return SpiralDB::title('NJ_OrderHDB')->value([
            'id',
            'registrationTime',
            'orderTime',
            'receivingTime',
            'orderNumber',
            'hospitalId',
            'divisionId',
            'itemsNumber',
            'totalAmount',
            'orderStatus',
            'hachuRarrival', //ルックアップキーは除外
            /*"f002664851",*/
            'distributorId',
            'ordererUserName',
            'ordercomment',
            'orderAuthKey',
            'staffName',
            'adjustment',
            'distrComment',
            'sentFlag',
        ]);
    }

    public static function getOrderItemInstance()
    {
        return SpiralDB::title('NJ_OrderDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'receivingTime',
            'dueDate',
            'orderCNumber',
            'hospitalId',
            'inHospitalItemId',
            'orderNumber',
            'price',
            'orderQuantity',
            'orderPrice',
            'receivingFlag',
            'receivingNum',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'divisionId',
            'distributorId',
            'lotManagement',
            'itemId',
            'priceId',
            'adjustment',
            'useMedicode',
            'hospitalCode',
            'distributorCode',
            'medicodeStatus',
            'medicodeSentDate',
            'deliveryDestCode',
        ]);
    }

    public static function getCardInstance()
    {
        return SpiralDB::title('NJ_CardDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'cardId',
            'hospitalId',
            'divisionId',
            'inHospitalItemId',
            'quantity',
            'payoutId',
            'lotNumber',
            'lotDate',
        ]);
    }

    public static function getCardViewInstance()
    {
        return SpiralDB::title('CardInfo')->value([
            'id',
            'registrationTime',
            'updateTime',
            'cardId',
            'hospitalId',
            'divisionId',
            'inHospitalItemId',
            'quantity',
            'payoutId',
            'distributorId',
            'catalogNo',
            'serialNo',
            'quantityUnit',
            'itemUnit',
            'medicineCategory',
            'homeCategory',
            'notUsedFlag',
            'itemId',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'notice',
            'HPstock',
            'makerName',
            'price',
            'oldPrice',
            'labelId',
            'minPrice',
            'officialFlag',
            'priceId',
            'unitPrice',
            'measuringInst',
            'distributorName',
            'hospitalName',
            'divisionName',
            'lotNumber',
            'lotDate',
        ]);
    }

    public static function getConsumptionInstance()
    {
        return SpiralDB::title('NJ_BillingHDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'billingDate',
            'billingNumber',
            'hospitalId',
            'divisionId',
            'itemsNumber',
            'totalAmount',
            'billingAuthKey',
            'billingStatus',
        ]);
    }

    public static function getConsumptionItemInstance()
    {
        return SpiralDB::title('NJ_BillingDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'inHospitalItemId',
            'billingNumber',
            'price',
            'billingQuantity',
            'billingAmount',
            'hospitalId',
            'divisionId',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'lotNumber',
            'lotDate',
            'unitPrice',
            'lotManagement',
            'itemId',
        ]);
    }

    public static function getConsumptionItemViewInstance()
    {
        return SpiralDB::title('340_ConsItemView')->value([
            'id',
            'registrationTime',
            'updateTime',
            'inHospitalItemId',
            'billingNumber',
            'price',
            'billingQuantity',
            'billingAmount',
            'hospitalId',
            'divisionId',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'lotNumber',
            'lotDate',
            'unitPrice',
            'lotManagement',
            'itemId',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'makerName',
            'officialFlag',
            'tenantId',
            'catalogNo',
            'serialNo',
            'category',
            'divisionName',
            'inItemImage',
        ]);
    }

    public static function getConsumptionViewInstance()
    {
        return SpiralDB::title('340_consumeView')->value([
            'id',
            'registrationTime',
            'updateTime',
            'billingNumber',
            'hospitalId',
            'hospitalName',
            'divisionId',
            'itemsNumber',
            'totalAmount',
            'divisionName',
            'divisionType',
            'deleteFlag',
            'hospitalName',
            'postalCode',
            'prefectures',
            'address',
            'phoneNumber',
            'faxNumber',
            'billingDate',
            'billingStatus',
        ]);
    }

    public static function getHospitalInstance()
    {
        return SpiralDB::title('NJ_HospitalDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'hospitalId',
            'hospitalName',
            'postalCode',
            'prefectures',
            'address',
            'phoneNumber',
            'faxNumber',
            'tenantId',
            'name',
            'nameKana',
            'mailAddress',
            'contactAddress',
            'plan',
            'receivingTarget',
            'function1',
            'function2',
            'function3',
            'function4',
            'function5',
            'function6',
            'function7',
            'function8',
            'authKey',
            'registerableNum',
            'labelDesign1',
            'labelDesign2',
            'labelDesign3',
            'billingUnitPrice',
            'payoutUnitPrice',
            'invUnitPrice',
        ]);
    }

    public static function getInHospitalItemInstance()
    {
        return SpiralDB::title('NJ_inHPItemDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'itemId',
            'priceId',
            'inHospitalItemId',
            'authKey',
            'hospitalId',
            'medicineCategory',
            'homeCategory',
            'notUsedFlag',
            'notice',
            'HPstock',
            'labelId',
            'unitPrice',
            'measuringInst',
            'inItemImage',
            'distributorMCode',
        ]);
    }

    public static function getInHospitalItemViewInstance()
    {
        return SpiralDB::title('340_inItems')->value([
            'id',
            'registrationTime',
            'updateTime',
            'itemId',
            'priceId',
            'inHospitalItemId',
            'authKey',
            'hospitalId',
            'medicineCategory',
            'homeCategory',
            'notUsedFlag',
            'notice',
            'HPstock',
            'unitPrice',
            'measuringInst',
            'makerName',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'officialFlag',
            'officialpriceOld',
            'officialprice',
            'catalogNo',
            'serialNo',
            'lotManagement',
            'category',
            'distributorId',
            'distributorName',
            'price',
            'quantity',
            'itemUnit',
            'quantityUnit',
            'distributorMCode',
            'inItemImage',
            'labelId',
            'notUsedFlag',
        ]);
    }

    public static function getInventoryAdjustmentTransactionInstance()
    {
        return SpiralDB::title('NJ_inventoryTRDB')->value([
            'id',
            'registrationTime',
            'divisionId',
            'inHospitalItemId',
            'count',
            'hospitalId',
            'orderWithinCount',
            'pattern',
            'lotUniqueKey',
            'lotNumber',
            'lotDate',
            'stockQuantity',
            'rackName',
            'constantByDiv',
            'loginId',
            'previousStock',
            'changeReason',
        ]);
    }

    public static function getItemInstance()
    {
        return SpiralDB::title('NJ_itemDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'itemId',
            'makerName',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'officialFlag',
            'officialpriceOld',
            'officialprice',
            'tenantId',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'minPrice',
            'itemsAuthKey',
            'janTenantId',
            'requestId',
            'requestFlg',
            'requestItemId',
            'catalogNo',
            'serialNo',
            'lotManagement',
            'category',
        ]);
    }

    public static function getItemRequestInstance()
    {
        return SpiralDB::title('NJ_ItemReqHDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'itemReqHAuthKey',
            'requestHId',
            'hospitalId',
            'sourceDivisionId',
            'sourceDivision',
            'targetDivisionId',
            'targetDivision',
            'requestType',
            'totalAmount',
            'itemsNumber',
            'requestUserName',
        ]);
    }

    public static function getItemRequestViewInstance()
    {
        return SpiralDB::title('ItemRequestView')->value([
            'id',
            'registrationTime',
            'updateTime',
            'requestHId',
            'hospitalId',
            'sourceDivisionId',
            'sourceDivision',
            'targetDivisionId',
            'targetDivision',
            'requestType',
            'totalAmount',
            'itemsNumber',
            'requestUserName',
            'hospitalName',
            'postalCode',
            'prefectures',
            'address',
            'phoneNumber',
            'faxNumber',
            'divisionType',
            'deleteFlag',
        ]);
    }

    public static function getNotificationInstance()
    {
        return SpiralDB::title('NJ_NoticeDB')->value([
            'id',
            'registrationTime',
            'noticeId',
            'authKey',
            'title',
            'content',
            'creator',
            'type',
        ]);
    }

    public static function getPayoutInstance()
    {
        return SpiralDB::title('NJ_PayoutHDB')->value([
            'id',
            'registrationTime',
            'payoutHistoryId',
            'hospitalId',
            'payoutAuthKey',
            'sourceDivisionId',
            'sourceDivision',
            'targetDivisionId',
            'targetDivision',
            'itemsNumber',
            'totalAmount',
        ]);
    }

    public static function getPayoutItemInstance()
    {
        return SpiralDB::title('NJ_PayoutDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'payoutHistoryId',
            'payoutId',
            'inHospitalItemId',
            'hospitalId',
            'sourceDivisionId',
            'targetDivisionId',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'price',
            'payoutQuantity',
            'payoutAmount',
            'payoutCount',
            'payoutLabelCount',
            'adjAmount',
            'priceAfterAdj',
            'lotNumber',
            'lotDate',
            'unitPrice',
            'cardId',
            'itemId',
        ]);
    }

    public static function getPriceInstance()
    {
        return SpiralDB::title('NJ_PriceDB')->value([
            'id',
            'priceId',
            'authKey',
            'itemId',
            'distributorId',
            'price',
            'quantity',
            'itemUnit',
            'quantityUnit',
            'hospitalId',
            'requestFlg',
            'notice',
            'notUsedFlag',
            'requestId',
        ]);
    }

    public static function getReceivedInstance()
    {
        return SpiralDB::title('NJ_ReceivingHDB')->value([
            'id',
            'registrationTime',
            'receivingHId',
            'distributorId',
            'orderHistoryId',
            'hospitalId',
            'itemsNumber',
            'authKey',
            'divisionId',
            'recevingStatus',
            'slipCategory',
            'totalAmount',
        ]);
    }

    public static function getReceivedItemInstance()
    {
        return SpiralDB::title('NJ_ReceivingDB')->value([
            'id',
            'registrationTime',
            'orderCNumber',
            'receivingCount',
            'receivingHId',
            'inHospitalItemId',
            'receivingNumber',
            'price',
            'receivingPrice',
            'hospitalId',
            'totalReturnCount',
            'divisionId',
            'distributorId',
            'adjAmount',
            'priceAfterAdj',
            'lotNumber',
            'lotDate',
            'itemId',
        ]);
    }

    public static function getReceivedItemViewInstance()
    {
        return SpiralDB::title('340_receivedItem')->value([
            'id',
            'registrationTime',
            'orderCNumber',
            'receivingCount',
            'receivingHId',
            'inHospitalItemId',
            'receivingNumber',
            'price',
            'receivingPrice',
            'hospitalId',
            'totalReturnCount',
            'divisionId',
            'distributorId',
            'adjAmount',
            'priceAfterAdj',
            'lotNumber',
            'lotDate',
            'itemId',
            'orderType',
            'hospitalName',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'makerName',
            'officialFlag',
            'tenantId',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'catalogNo',
            'serialNo',
            'officialpriceOld',
            'officialprice',
            'lotManagement',
            'category',
            'divisionName',
            'distributorName',
            'inItemImage',
        ]);
    }

    public static function getReceivedViewInstance()
    {
        return SpiralDB::title('340_received')->value([
            'id',
            'registrationTime',
            'receivingHId',
            'distributorId',
            'orderHistoryId',
            'hospitalId',
            'itemsNumber',
            'authKey',
            'divisionId',
            'recevingStatus',
            'slipCategory',
            'totalAmount',
            'hospitalName',
            'distributorName',
            'orderNumber',
            'divisionName',
            'postalCode',
            'prefectures',
            'address',
            'phoneNumber',
        ]);
    }

    public static function getItemRequestItemInstance()
    {
        return SpiralDB::title('NJ_ItemRequestDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'itemReqAuthKey',
            'requestId',
            'requestHId',
            'hospitalId',
            'itemId',
            'inHospitalItemId',
            'sourceDivisionId',
            'targetDivisionId',
            'requestQuantity',
            'requestType',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'price',
            'unitPrice',
        ]);
    }

    public static function getItemRequestItemCountTransactionInstance()
    {
        return SpiralDB::title('NJ_requestTRDB')->value([
            'id',
            'registrationTime',
            'recordId',
            'hospitalId',
            'inHospitalItemId',
            'itemId',
            'quantity',
            'sourceDivisionId',
            'targetDivisionId',
        ]);
    }

    public static function getItemRequestItemViewInstance()
    {
        return SpiralDB::title('requestItem')->value([
            'id',
            'registrationTime',
            'updateTime',
            'requestId',
            'requestHId',
            'hospitalId',
            'itemId',
            'inHospitalItemId',
            'sourceDivisionId',
            'targetDivisionId',
            'requestQuantity',
            'requestType',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'price',
            'unitPrice',
            'sourceDivision',
            'targetDivision',
            'catalogNo',
            'serialNo',
            'lotManagement',
            'category',
            'minPrice',
            'officialFlag',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'makerName',
            'measuringInst',
            'distributorName',
            'notice',
            'medicineCategory',
            'homeCategory',
            'notUsedFlag',
        ]);
    }

    public static function getReturnHistoryInstance()
    {
        return SpiralDB::title('NJ_ReturnHDB')->value([
            'id',
            'registrationTime',
            'receivingHId',
            'distributorId',
            'orderHistoryId',
            'returnHistoryID',
            'hospitalId',
            'itemsNumber',
            'authKey',
            'returnTotalPrice',
            'divisionId',
        ]);
    }

    public static function getReturnItemInstance()
    {
        return SpiralDB::title('NJ_ReturnDB')->value([
            'id',
            'registrationTime',
            'orderCNumber',
            'receivingHId',
            'inHospitalItemId',
            'receivingNumber',
            'price',
            'returnID',
            'returnCount',
            'returnPrice',
            'hospitalId',
            'returnHistoryID',
            'lotNumber',
            'lotDate',
            'itemId',
        ]);
    }

    public static function getReturnItemViewInstance()
    {
        return SpiralDB::title('340_retunItem')->value([
            'id',
            'registrationTime',
            'orderCNumber',
            'receivingHId',
            'inHospitalItemId',
            'receivingNumber',
            'price',
            'returnID',
            'returnCount',
            'returnPrice',
            'hospitalId',
            'returnHistoryID',
            'lotNumber',
            'lotDate',
            'distributorId',
            'divisionId',
            'returnCount2',
            'itemId',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'makerName',
            'officialFlag',
            'minPrice',
            'officialprice',
            'catalogNo',
            'serialNo',
            'lotManagement',
            'category',
            'updateTime',
            'distributorName',
            'hospitalName',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'inItemImage',
        ]);
    }

    public static function getReturnViewInstance()
    {
        return SpiralDB::title('340_return')->value([
            'id',
            'registrationTime',
            'receivingHId',
            'distributorId',
            'orderHistoryId',
            'returnHistoryID',
            'hospitalId',
            'itemsNumber',
            'authKey',
            'returnTotalPrice',
            'divisionId',
            'hospitalName',
            'divisionName',
            'distributorName',
            'postalCode',
            'prefectures',
            'address',
            'phoneNumber',
        ]);
    }

    public static function getStockInstance()
    {
        return SpiralDB::title('NJ_stockDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'hospitalId',
            'divisionId',
            'inHospitalItemId',
            'authKey',
            'stockQuantity',
            'orderWithinCount',
            'rackName',
            'invFinishTime',
            'planInventoryCnt',
            'constantByDiv',
            'requiredOrderNum',
            'barcode',
            'labelId',
            'recordId',
        ]);
    }

    public static function getStockViewInstance()
    {
        return SpiralDB::title('340_stocks')->value([
            'id',
            'registrationTime',
            'updateTime',
            'hospitalId',
            'divisionId',
            'inHospitalItemId',
            'stockQuantity',
            'authKey',
            'orderWithinCount',
            'rackName',
            'distributorId',
            'invFinishTime',
            'planInventoryCnt',
            'constantByDiv',
            'requiredOrderNum',
            'barcode',
            'labelId',
            'recordId',
            'divisionName',
        ]);
    }

    public static function getStockItemViewInstance()
    {
        return SpiralDB::title('340_stock')->value([
            'id',
            'inHospitalItemId',
            'rackName',
            'itemId',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'makerName',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'stockQuantity',
            'hospitalName',
            'divisionName',
            'hospitalId',
            'divisionId',
            'constantByDiv',
            'distributorName',
        ]);
    }

    public static function getTenantInstance()
    {
        return SpiralDB::title('NJ_TenantAdminDB')->value([
            'id',
            'registrationTime',
            'tenantId',
            'tenantName',
            'note',
            'tenantKind',
            'postalCode',
            'prefectures',
            'address',
            'phoneNumber',
            'faxNumber',
        ]);
    }

    public static function getTotalRequestByDivisionInstance()
    {
        return SpiralDB::title('NJ_divRequestDB')->value([
            'id',
            'registrationTime',
            'updateTime',
            'authKey',
            'recordId',
            'hospitalId',
            'inHospitalItemId',
            'itemId',
            'requestQuantity',
            'sourceDivisionId',
            'targetDivisionId',
            'requestUniqueKey',
        ]);
    }

    public static function getTotalRequestByInHpItemViewIntance()
    {
        return SpiralDB::title('TotalRequestItem')->value([
            'id',
            'recordId',
            'hospitalId',
            'inHospitalItemId',
            'itemId',
            'requestQuantity',
            'sourceDivisionId',
            'targetDivisionId',
            'stockQuantity',
            'rackName',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'makerName',
            'catalogNo',
            'serialNo',
            'category',
            'smallCategory',
            'lotManagement',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'notUsedFlag',
            'measuringInst',
        ]);
    }

    public static function getAccountantInstance()
    {
        return SpiralDB::title('NJ_Accountant')->value([
            'id',
            'registTime',
            'updateTime',
            'hospitalId',
            'divisionId',
            'distributorId',
            'accountantId',
            'accountantDate',
            'orderNumber',
            'receivingNumber',
            'totalAmount',
        ]);
    }

    public static function getAccountantItemInstance()
    {
        return SpiralDB::title('NJ_AccountantI')->value([
            'id',
            'registTime',
            'updateTime',
            'accountantId',
            'itemId',
            'itemName',
            'makerName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'count',
            'unit',
            'price',
            'taxrate',
            'accountantItemId',
            'action',
            'method',
            'index',
        ]);
    }

    public static function getAccountantItemLogInstance()
    {
        return SpiralDB::title('NJ_AItemLog')->value([
            'id',
            'registTime',
            'accountantId',
            'itemId',
            'itemName',
            'makerName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'count',
            'unit',
            'price',
            'taxrate',
            'accountantItemId',
            'action',
            'method',
            'index',
            'kinds',
            'userId',
        ]);
    }

    public static function getReservationPriceInstance()
    {
        return SpiralDB::title('NJ_Reservation')->value([
            'id',
            'registrationTime',
            'updateTime',
            'reservationTime',
            'priceId',
            'hospitalId',
            'itemId',
            'distributorId',
            'distributorMCode',
            'quantity',
            'itemUnit',
            'unitPrice',
            'price',
            'notice',
            'isActive',
            'recordId',
        ]);
    }

    public static function getReservationPriceViewInstance()
    {
        return SpiralDB::title('reservationPrice')->value([
            'id',
            'registrationTime',
            'updateTime',
            'reservationTime',
            'priceId',
            'hospitalId',
            'itemId',
            'distributorId',
            'distributorMCode',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'unitPrice',
            'price',
            'notice',
            'isActive',
            'authKey',
        ]);
    }

    public static function getPriceUpsertTransactionInstance()
    {
        return SpiralDB::title('T_Price')->value([
            'id',
            'registrationTime',
            'priceId',
            'itemId',
            'itemsAuthKey',
            'hospitalId',
            'distributorId',
            'quantity',
            'itemUnit',
            'price',
            'notice',
            'unitPrice',
            'distributorMCode',
        ]);
    }

    public static function getItemListTableInstance()
    {
        return SpiralDB::title('NJ_ItemListTbl')->value([ //商品リストDB
            'id',
            'registrationTime',
            'updateTime',
            'itemListId',
            'itemListName',
            'hospitalId',
            'divisionId',
            'itemsNumber',
            'usableStatus',
        ]);
    }

    public static function getItemListRowsInstance()
    {
        return SpiralDB::title('NJ_ItemListRows')->value([ //商品リスト項目DB
            'id',
            'registrationTime',
            'updateTime',
            'itemListId',
            'itemListRowId',
            'hospitalId',
            'divisionId',
            'inHospitalItemId',
            'itemId',
            'distributorId',
            'index',
        ]);
    }

    public static function getItemListTableViewInstance()
    {
        return SpiralDB::title('itemListTblView')->value([ //商品リスト仮想DB-画面表示に使うかもしれないやつ
            'id',
            'registrationTime',
            'updateTime',
            'itemListId',
            'itemListName',
            'hospitalId',
            'divisionId',
            'itemsNumber',
            'usableStatus',
            'hospitalName',
            'divisionName',
        ]);
    }

    public static function getItemListRowsViewInstance()
    {
        return SpiralDB::title('itemListRowsView')->value([ //商品リスト項目仮想DB-画面表示に使うかもしれないやつ
            'id',
            'registrationTime',
            'updateTime',
            'itemListId',
            'itemListRowId',
            'hospitalId',
            'divisionId',
            'inHospitalItemId',
            'itemId',
            'distributorId',
            'index',
            'distributorName',
            'hospitalName',
            'divisionName',
            'itemName',
            'itemCode',
            'itemStandard',
            'itemJANCode',
            'quantity',
            'quantityUnit',
            'itemUnit',
            'labelId',
        ]);
    }

}
