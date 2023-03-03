<?php

namespace JoyPla\Service\UseCase\Web;

use JoyPla\Application\Interactors\Web\Consumption\ConsumptionPrintInteractor;
use JoyPla\Application\Interactors\Web\Consumption\ConsumptionShowInteractor;
use JoyPla\Application\Interactors\Web\ItemRequest\ItemRequestShowInteractor;
use JoyPla\Application\Interactors\Web\ItemRequest\PickingListInteractor;
use JoyPla\Application\Interactors\Web\Order\OrderIndexInteractor;
use JoyPla\Application\Interactors\Web\Order\UnapprovedOrderIndexInteractor;
use JoyPla\Application\Interactors\Web\Received\OrderReceivedSlipIndexInteractor;
use JoyPla\Application\Interactors\Web\Received\ReceivedIndexInteractor;
use JoyPla\Application\Interactors\Web\Received\ReceivedLabelInteractor;
use JoyPla\Service\Presenter\Web\PresenterProvider;
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

    public function getConsumptionShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ConsumptionShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct(
                    $presenterProvider->getConsumptionShowPresenter(),
                    $repositoryProvider
                );
            }
        };
    }

    public function getConsumptionPrintInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ConsumptionShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct(
                    $presenterProvider->getConsumptionPrintPresenter(),
                    $repositoryProvider
                );
            }
        };
    }

    public function getItemRequestShowInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ItemRequestShowInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getPickingListInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends PickingListInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getOrderIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct(
                    $presenterProvider->getOrderIndexPresenter(),
                    $repositoryProvider
                );
            }
        };
    }

    public function getUnapprovedOrderIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct(
                    $presenterProvider->getUnapprovedOrderIndexPresenter(),
                    $repositoryProvider
                );
            }
        };
    }

    public function getOrderPrintInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct(
                    $presenterProvider->getOrderPrintPresenter(),
                    $repositoryProvider
                );
            }
        };
    }

    public function getOrderReceivedSlipIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends OrderReceivedSlipIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getReceivedIndexInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ReceivedIndexInteractor {
            public function __construct(
                RepositoryProvider $repositoryProvider,
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
            }
        };
    }

    public function getReceivedLabelInteractor()
    {
        return new class(
            $this->repositoryProvider,
            $this->presenterProvider
        ) extends ReceivedLabelInteractor {
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
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
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
                PresenterProvider $presenterProvider
            ) {
                parent::__construct($presenterProvider, $repositoryProvider);
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
