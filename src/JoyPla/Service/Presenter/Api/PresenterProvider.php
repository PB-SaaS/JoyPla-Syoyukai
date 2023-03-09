<?php

namespace JoyPla\Service\Presenter\Api;

use JoyPla\InterfaceAdapters\Presenters\Api\Barcode\BarcodeOrderSearchPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Barcode\BarcodeSearchPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Consumption\ConsumptionDeletePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Consumption\ConsumptionIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Consumption\ConsumptionRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Distributor\DistributorIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Division\DivisionIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem\InHospitalItemIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\InHospitalItem\InHospitalItemRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Item\ItemRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Item\ItemShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest\ItemRequestDeletePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest\ItemRequestHistoryPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest\ItemRequestRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest\ItemRequestUpdatePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest\RequestItemDeletePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest\TotalizationPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Notification\NotificationShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\FixedQuantityOrderPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderRevisedPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedApprovalPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedDeletePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedItemDeletePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnapprovedUpdatePresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Order\OrderUnReceivedShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Payout\PayoutRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Price\PriceRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Received\ReceivedRegisterByOrderSlipPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Received\ReceivedRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Received\ReceivedReturnRegisterPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Received\ReceivedShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\ReceivedReturn\ReturnShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Api\Reference\ConsumptionHistoryShowPresenter;

class PresenterProvider
{
    public function getDivisionIndexPresenter()
    {
        return new DivisionIndexPresenter();
    }

    public function getInHospitalItemIndexPresenter()
    {
        return new InHospitalItemIndexPresenter();
    }

    public function getConsumptionRegisterPresenter()
    {
        return new ConsumptionRegisterPresenter();
    }

    public function getConsumptionIndexPresenter()
    {
        return new ConsumptionIndexPresenter();
    }

    public function getConsumptionDeletePresenter()
    {
        return new ConsumptionDeletePresenter();
    }

    public function getOrderRegisterPresenter()
    {
        return new OrderRegisterPresenter();
    }

    public function getOrderUnapprovedShowPresenter()
    {
        return new OrderUnapprovedShowPresenter();
    }

    public function getOrderUnapprovedUpdatePresenter()
    {
        return new OrderUnapprovedUpdatePresenter();
    }

    public function getFixedQuantityOrderPresenter()
    {
        return new FixedQuantityOrderPresenter();
    }

    public function getOrderUnReceivedShowPresenter()
    {
        return new OrderUnReceivedShowPresenter();
    }

    public function getOrderUnapprovedDeletePresenter()
    {
        return new OrderUnapprovedDeletePresenter();
    }

    public function getOrderUnapprovedApprovalPresenter()
    {
        return new OrderUnapprovedApprovalPresenter();
    }

    public function getOrderUnapprovedItemDeletePresenter()
    {
        return new OrderUnapprovedItemDeletePresenter();
    }

    public function getOrderRevisedPresenter()
    {
        return new OrderRevisedPresenter();
    }

    public function getOrderShowPresenter()
    {
        return new OrderShowPresenter();
    }

    public function getPayoutRegisterPresenter()
    {
        return new PayoutRegisterPresenter();
    }

    public function getReceivedRegisterByOrderSlipPresenter()
    {
        return new ReceivedRegisterByOrderSlipPresenter();
    }

    public function getReceivedReturnRegisterPresenter()
    {
        return new ReceivedReturnRegisterPresenter();
    }

    public function getReceivedShowPresenter()
    {
        return new ReceivedShowPresenter();
    }

    public function getReceivedRegisterPresenter()
    {
        return new ReceivedRegisterPresenter();
    }

    public function getBarcodeOrderSearchPresenter()
    {
        return new BarcodeOrderSearchPresenter();
    }

    public function getBarcodeSearchPresenter()
    {
        return new BarcodeSearchPresenter();
    }

    public function getDistributorIndexPresenter()
    {
        return new DistributorIndexPresenter();
    }

    public function getInHospitalItemRegisterPresenter()
    {
        return new InHospitalItemRegisterPresenter();
    }

    public function getItemRequestDeletePresenter()
    {
        return new ItemRequestDeletePresenter();
    }

    public function getItemRequestHistoryPresenter()
    {
        return new ItemRequestHistoryPresenter();
    }

    public function getItemRequestRegisterPresenter()
    {
        return new ItemRequestRegisterPresenter();
    }

    public function getItemRequestUpdatePresenter()
    {
        return new ItemRequestUpdatePresenter();
    }

    public function getRequestItemDeletePresenter()
    {
        return new RequestItemDeletePresenter();
    }

    public function getTotalizationPresenter()
    {
        return new TotalizationPresenter();
    }

    public function getNotificationShowPresenter()
    {
        return new NotificationShowPresenter();
    }

    public function getPriceRegisterPresenter()
    {
        return new PriceRegisterPresenter();
    }

    public function getItemRegisterPresenter()
    {
        return new ItemRegisterPresenter();
    }

    public function getItemShowPresenter()
    {
        return new ItemShowPresenter();
    }

    public function getConsumptionHistoryShowPresenter()
    {
        return new ConsumptionHistoryShowPresenter();
    }

    public function getReturnShowPresenter()
    {
        return new ReturnShowPresenter();
    }
}
