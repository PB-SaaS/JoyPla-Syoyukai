<?php

namespace JoyPla\Service\UseCase\Api;

use JoyPla\Application\Interactors\Api\Accountant\AccountantIndexInteractor;
use JoyPla\Application\Interactors\Api\Accountant\AccountantRegisterInteractor;
use JoyPla\Application\Interactors\Api\Accountant\AccountantShowInteractor;
use JoyPla\Application\Interactors\Api\Accountant\AccountantUpdateInteractor;
use JoyPla\Application\Interactors\Api\Barcode\BarcodeOrderSearchInteractor;
use JoyPla\Application\Interactors\Api\Barcode\BarcodeSearchInteractor;
use JoyPla\Application\Interactors\Api\Consumption\ConsumptionDeleteInteractor;
use JoyPla\Application\Interactors\Api\Consumption\ConsumptionIndexInteractor;
use JoyPla\Application\Interactors\Api\Consumption\ConsumptionRegisterInteractor;
use JoyPla\Application\Interactors\Api\Distributor\DistributorIndexInteractor;
use JoyPla\Application\Interactors\Api\Division\DivisionIndexInteractor;
use JoyPla\Application\Interactors\Api\InHospitalItem\InHospitalItemIndexInteractor;
use JoyPla\Application\Interactors\Api\InHospitalItem\InHospitalItemShowInteractor;
use JoyPla\Application\Interactors\Api\ItemRequest\ItemRequestBulkUpdateInteractor;
use JoyPla\Application\Interactors\Api\ItemRequest\ItemRequestDeleteInteractor;
use JoyPla\Application\Interactors\Api\ItemRequest\ItemRequestHistoryInteractor;
use JoyPla\Application\Interactors\Api\ItemRequest\ItemRequestRegisterInteractor;
use JoyPla\Application\Interactors\Api\ItemRequest\ItemRequestUpdateInteractor;
use JoyPla\Application\Interactors\Api\ItemRequest\RequestItemDeleteInteractor;
use JoyPla\Application\Interactors\Api\ItemRequest\TotalizationInteractor;
use JoyPla\Application\Interactors\Api\ItemList\ItemListIndexInteractor;
use JoyPla\Application\Interactors\Api\ItemList\ItemListRegisterInteractor;
use JoyPla\Application\Interactors\Api\ItemList\ItemListShowInteractor;
use JoyPla\Application\Interactors\Api\ItemList\ItemListUpdateInteractor;
use JoyPla\Application\Interactors\Api\Notification\NotificationShowInteractor;
use JoyPla\Application\Interactors\Api\Order\FixedQuantityOrderInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderDeleteInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderItemBulkUpdateInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderRegisterInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderRevisedInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderShowInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedApprovalAllInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedApprovalInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedDeleteInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedItemDeleteInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnapprovedUpdateInteractor;
use JoyPla\Application\Interactors\Api\Order\OrderUnReceivedShowInteractor;
use JoyPla\Application\Interactors\Api\Payout\PayoutRegisterInteractor;
use JoyPla\Application\Interactors\Api\Price\PriceRegisterInteractor;
use JoyPla\Application\Interactors\Api\Price\PriceShowInteractor;
use JoyPla\Application\Interactors\Api\Received\ReceivedRegisterByOrderSlipInteractor;
use JoyPla\Application\Interactors\Api\Received\ReceivedRegisterInteractor;
use JoyPla\Application\Interactors\Api\Received\ReceivedReturnRegisterInteractor;
use JoyPla\Application\Interactors\Api\Received\ReceivedShowInteractor;
use JoyPla\Application\Interactors\Api\ReceivedReturn\ReturnShowInteractor;
use JoyPla\Application\Interactors\Api\Reference\ConsumptionHistoryShowInteractor;
use JoyPla\Service\Presenter\Api\PresenterProvider;
use JoyPla\Service\Repository\QueryProvider;
use JoyPla\Service\Repository\RepositoryProvider;

class UseCaseProvider
{
    private RepositoryProvider $repositoryProvider;
    private QueryProvider $queryProvider;
    private PresenterProvider $presenterProvider;

    public function __construct(
        RepositoryProvider $repositoryProvider,
        QueryProvider $queryProvider,
        PresenterProvider $presenterProvider
    ) {
        $this->repositoryProvider = $repositoryProvider;
        $this->queryProvider = $queryProvider;
        $this->presenterProvider = $presenterProvider;
    }

    public function getDivisionIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends DivisionIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getDistributorIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends DistributorIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getInHospitalItemIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends InHospitalItemIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getInHospitalItemShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends InHospitalItemShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getConsumptionRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ConsumptionRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getConsumptionIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ConsumptionIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getConsumptionDeleteInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ConsumptionDeleteInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderItemBulkUpdateInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderItemBulkUpdateInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderUnapprovedUpdateInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderUnapprovedUpdateInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getFixedQuantityOrderInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends FixedQuantityOrderInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderUnReceivedShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderUnReceivedShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderUnapprovedDeleteInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderUnapprovedDeleteInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderDeleteInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderDeleteInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderUnapprovedApprovalInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderUnapprovedApprovalInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderUnapprovedApprovalAllInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderUnapprovedApprovalAllInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderUnapprovedItemDeleteInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderUnapprovedItemDeleteInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderRevisedInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderRevisedInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getReceivedRegisterByOrderSlipInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ReceivedRegisterByOrderSlipInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getReceivedReturnRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ReceivedReturnRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getReceivedShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ReceivedShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getReceivedRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ReceivedRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getReturnShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ReturnShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getConsumptionHistoryShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ConsumptionHistoryShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getPriceShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends PriceShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getPriceRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends PriceRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getPayoutRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends PayoutRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getNotificationShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends NotificationShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getTotalizationInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends TotalizationInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getRequestItemDeleteInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends RequestItemDeleteInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getBarcodeSearchInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends BarcodeSearchInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getBarcodeOrderSearchInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends BarcodeOrderSearchInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemRequestRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemRequestRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemRequestBulkUpdateInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemRequestBulkUpdateInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemRequestHistoryInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemRequestHistoryInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemRequestDeleteInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemRequestDeleteInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemRequestUpdateInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemRequestUpdateInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getAccountantRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends AccountantRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getAccountantIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends AccountantIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getAccountantShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends AccountantShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getAccountantUpdateInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends AccountantUpdateInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemListIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemListIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemListRegisterInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemListRegisterInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemListShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemListShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getItemListUpdateInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemListUpdateInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    /*

    public function getGroupUseCase()
    {
        return new class(
            $this->repositoryProvider,
            $this->queryProvider
        ) extends GroupInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                QueryProvider $queryProvider
            ) {
                parent::__construct($repositoryProvider, $queryProvider);
            }
        };
    }

    public function getUserUseCase()
    {
        return new class(
            $this->repositoryProvider,
            $this->queryProvider
        ) extends UserInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                QueryProvider $queryProvider
            ) {
                parent::__construct($repositoryProvider, $queryProvider);
            }
        };
    }

    public function getUserPasswordUseCase()
    {
        return new class(
            $this->repositoryProvider
        ) extends UserPasswordInteractor {
            public function __construct(RepositoryProvider $repositoryProvider)
            {
                parent::__construct($repositoryProvider);
            }
        };
    }
    */
}
