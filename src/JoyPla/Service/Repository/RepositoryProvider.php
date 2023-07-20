<?php

namespace JoyPla\Service\Repository;

use JoyPla\InterfaceAdapters\GateWays\Repository\AcceptanceRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\AccountantItemRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\AccountantLogRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\AccountantRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\BarcodeRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\CardRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ConsumptionHistoryRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ConsumptionRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\DistributorRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\InHospitalItemRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ItemAndPriceAndInHospitalItemRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ItemListRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRequestRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\NotificationRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\PayoutRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\PriceAndInHospitalItemRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\PriceRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ReceivedRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\RequestItemCountRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\ReturnRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\StockRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\StocktakingListRepository;
use JoyPla\InterfaceAdapters\GateWays\Repository\TotalizationRepository;

class RepositoryProvider
{
    public function getBarcodeRepository()
    {
        return new BarcodeRepository();
    }

    public function getDivisionRepository()
    {
        return new DivisionRepository();
    }

    public function getInHospitalItemRepository()
    {
        return new InHospitalItemRepository();
    }

    public function getDistributorRepository()
    {
        return new DistributorRepository();
    }

    public function getConsumptionHistoryRepository()
    {
        return new ConsumptionHistoryRepository();
    }

    public function getConsumptionRepository()
    {
        return new ConsumptionRepository();
    }

    public function getInventoryCalculationRepository()
    {
        return new InventoryCalculationRepository();
    }

    public function getCardRepository()
    {
        return new CardRepository();
    }

    public function getOrderRepository()
    {
        return new OrderRepository();
    }

    public function getHospitalRepository()
    {
        return new HospitalRepository();
    }

    public function getStockRepository()
    {
        return new StockRepository();
    }

    public function getReceivedRepository()
    {
        return new ReceivedRepository();
    }

    public function getReturnRepository()
    {
        return new ReturnRepository();
    }

    public function getItemAndPriceAndInHospitalItemRepository()
    {
        return new ItemAndPriceAndInHospitalItemRepository();
    }

    public function getItemRepository()
    {
        return new ItemRepository();
    }

    public function getItemRequestRepository()
    {
        return new ItemRequestRepository();
    }

    public function getNotificationRepository()
    {
        return new NotificationRepository();
    }

    public function getPayoutRepository()
    {
        return new PayoutRepository();
    }

    public function getPriceAndInHospitalItemRepository()
    {
        return new PriceAndInHospitalItemRepository();
    }

    public function getPriceRepository()
    {
        return new PriceRepository();
    }

    public function getRequestItemCountRepository()
    {
        return new RequestItemCountRepository();
    }

    public function getTotalizationRepository()
    {
        return new TotalizationRepository();
    }

    public function getAccountantRepository()
    {
        return new AccountantRepository();
    }

    public function getAccountantItemRepository()
    {
        return new AccountantItemRepository();
    }

    public function getAccountantLogRepository()
    {
        return new AccountantLogRepository();
    }

    public function getItemListRepository()
    {
        return new ItemListRepository();
    }

    public function getAcceptanceRepository()
    {
        return new AcceptanceRepository();
    }
    public function getStocktakingListRepository()
    {
        return new StocktakingListRepository();
    }

}
