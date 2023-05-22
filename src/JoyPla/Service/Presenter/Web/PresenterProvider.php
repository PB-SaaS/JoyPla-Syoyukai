<?php

namespace JoyPla\Service\Presenter\Web;

use JoyPla\InterfaceAdapters\Presenters\Web\Consumption\ConsumptionPrintPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Consumption\ConsumptionShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\ItemRequest\ItemRequestShowPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\ItemRequest\PickingListPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Order\OrderIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Order\OrderPrintPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Order\UnapprovedOrderIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\OrderReceivedSlipIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\ReceivedIndexPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\ReceivedLabelPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Received\ReceivedLabelSettingPresenter;
use JoyPla\InterfaceAdapters\Presenters\Web\Product\ItemList\ItemListPrintPresenter;

class PresenterProvider
{
    public function getConsumptionPrintPresenter()
    {
        return new ConsumptionPrintPresenter();
    }

    public function getConsumptionShowPresenter()
    {
        return new ConsumptionShowPresenter();
    }

    public function getItemRequestShowPresenter()
    {
        return new ItemRequestShowPresenter();
    }

    public function getPickingListPresenter()
    {
        return new PickingListPresenter();
    }

    public function getOrderIndexPresenter()
    {
        return new OrderIndexPresenter();
    }

    public function getOrderPrintPresenter()
    {
        return new OrderPrintPresenter();
    }

    public function getUnapprovedOrderIndexPresenter()
    {
        return new UnapprovedOrderIndexPresenter();
    }

    public function getOrderReceivedSlipIndexPresenter()
    {
        return new OrderReceivedSlipIndexPresenter();
    }

    public function getReceivedIndexPresenter()
    {
        return new ReceivedIndexPresenter();
    }

    public function getReceivedLabelPresenter()
    {
        return new ReceivedLabelPresenter();
    }

    public function getReceivedLabelSettingPresenter()
    {
        return new ReceivedLabelSettingPresenter();
    }

    public function getItemListShowPresenter()
    {
        return new ItemListPrintPresenter();
    }
}
