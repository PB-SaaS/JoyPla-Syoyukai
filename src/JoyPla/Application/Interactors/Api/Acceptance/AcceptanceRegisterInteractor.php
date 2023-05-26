<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\Acceptance {

    use Exception;
    use JoyPla\Application\InputPorts\Api\Acceptance\AcceptanceRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Acceptance\AcceptanceRegisterInputPortInterface;
    use JoyPla\Enterprise\Models\Division;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InHospitalItem;
    use JoyPla\Enterprise\Models\InHospitalItemId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class AcceptanceRegisterInteractor
     * @package JoyPla\Application\Interactors\Acceptance\Api
     */
    class AcceptanceRegisterInteractor implements AcceptanceRegisterInputPortInterface
    {
        private PresenterProvider $presenterProvider;
        private RepositoryProvider $repositoryProvider;

        public function __construct(
            PresenterProvider $presenterProvider,
            RepositoryProvider $repositoryProvider
        ) {
            $this->presenterProvider = $presenterProvider;
            $this->repositoryProvider = $repositoryProvider;
        }

        /**
         * @param AcceptanceRegisterInputData $inputData
         */
        public function handle(AcceptanceRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $hospitalRow = $this->repositoryProvider
                ->getHospitalRepository()
                ->findRow($hospitalId);

            $hospital = Hospital::create($hospitalRow);

            $inputData->acceptanceItems = array_map(function ($v) use ($inputData) {
                if (
                    $inputData->isOnlyMyDivision &&
                    $inputData->user->divisionId !== $v->acceptanceSourceDivisionId
                ) {
                    throw new Exception('Illegal request', 403);
                }
                if ($v->acceptanceSourceDivisionId == $v->acceptanceTargetDivisionId) {
                    throw new Exception('Invalid request', 999);
                }
                return $v;
            }, $inputData->acceptanceItems);

            $inHospitalItemIds = array_map(function ($acceptanceItem) {
                return new InHospitalItemId($acceptanceItem->inHospitalItemId);
            }, $inputData->acceptanceItems);

            $inHospitalItems = $this->repositoryProvider
                ->getInHospitalItemRepository()
                ->getByInHospitalItemIds($hospitalId, $inHospitalItemIds);

            $inHospitalItems = array_map(function (
                InHospitalItem $inHospitalItem
            ) {
                return $inHospitalItem;
            },
            $inHospitalItems);

            if (count($inHospitalItems) === 0) {
                throw new Exception("Acceptance items don't exist.", 999);
            }

            $divisions = $this->repositoryProvider
                ->getDivisionRepository()
                ->findByHospitalId($hospitalId);

            $acceptances = [];
            foreach ($inputData->acceptanceItems as $acceptanceItem) {
                if ((int) $acceptanceItem->acceptanceQuantity < 1) {
                    continue;
                }

                $sourceDivisionId = $acceptanceItem->acceptanceSourceDivisionId;
                $sourceDivision = array_find($divisions, function (
                    Division $value
                ) use ($sourceDivisionId) {
                    return $value->getDivisionId()->value() ===
                        $sourceDivisionId;
                });

                $targetDivisionId = $acceptanceItem->acceptanceTargetDivisionId;
                $targetDivision = array_find($divisions, function (
                    Division $value
                ) use ($targetDivisionId) {
                    return $value->getDivisionId()->value() ===
                        $targetDivisionId;
                });
/*
                if (
                    !!array_find($acceptances, function (Acceptance $value) use (
                        $targetDivision,
                        $sourceDivision
                    ) {
                        return $value->equalDivisions(
                            $sourceDivision,
                            $targetDivision
                        );
                    })
                ) {
                    continue;
                }

                $acceptances[] = new Acceptance(
                    AcceptanceHId::generate(),
                    new DateYearMonthDayHourMinutesSecond(''),
                    [],
                    $hospital,
                    $sourceDivision,
                    $targetDivision
                );
                */
            }

/*
            $cards = [];

            foreach ($inputData->acceptanceItems as $acceptanceItem) {
                if ((int) $acceptanceItem->acceptanceQuantity < 1) {
                    continue;
                }
                $sourceDivisionId = $acceptanceItem->acceptanceSourceDivisionId;
                $sourceDivision = array_find($divisions, function (
                    Division $value
                ) use ($sourceDivisionId) {
                    return $value->getDivisionId()->value() ===
                        $sourceDivisionId;
                });

                $targetDivisionId = $acceptanceItem->acceptanceTargetDivisionId;
                $targetDivision = array_find($divisions, function (
                    Division $value
                ) use ($targetDivisionId) {
                    return $value->getDivisionId()->value() ===
                        $targetDivisionId;
                });

                $inHospitalItemId = $acceptanceItem->inHospitalItemId;

                $inHospitalItem = array_find($inHospitalItems, function (
                    $value
                ) use ($inHospitalItemId) {
                    return $value->getInHospitalItemId()->value() ===
                        $inHospitalItemId;
                });

                $unitprice = $inHospitalItem->getUnitPrice()->value();

                if ($hospitalRow->acceptanceUnitPrice !== '1') {
                    if (
                        $inHospitalItem->getQuantity()->getQuantityNum() != 0 &&
                        $inHospitalItem->getPrice()->value() != 0
                    ) {
                        $unitprice =
                            (int) $inHospitalItem->getPrice()->value() /
                            (int) $inHospitalItem
                                ->getQuantity()
                                ->getQuantityNum();
                    } else {
                        $unitprice = 0;
                    }
                }

                foreach ($acceptances as &$acceptance) {
                    if (
                        $acceptance->equalDivisions(
                            $sourceDivision,
                            $targetDivision
                        )
                    ) {
                        $item = new AcceptanceItem(
                            $acceptance->getAcceptanceHId(),
                            '',
                            $inHospitalItem->getInHospitalItemId(),
                            $inHospitalItem->getItem(),
                            $hospitalId,
                            $sourceDivision,
                            $targetDivision,
                            $inHospitalItem->getQuantity(),
                            $inHospitalItem->getPrice(),
                            new UnitPrice($unitprice),
                            new AcceptanceQuantity($acceptanceItem->acceptanceQuantity),
                            new Lot(
                                new LotNumber($acceptanceItem->lotNumber),
                                new LotDate($acceptanceItem->lotDate)
                            ),
                            $inHospitalItem->isLotManagement(),
                            new CardId($acceptanceItem->card)
                        );
                        $acceptance = $acceptance->addAcceptanceItem($item);
                    }
                }
            }

            if(!$inputData->isOnlyAcceptance){
                $stockViewInstance = ModelRepository::getStockViewInstance()->where(
                    'hospitalId',
                    $hospitalId->value()
                );

                foreach ($acceptances as $acceptance) {
                    $stockViewInstance->orWhere(
                        'divisionId',
                        $acceptance
                            ->getSourceDivision()
                            ->getDivisionId()
                            ->value()
                    );

                    foreach ($acceptance->getAcceptanceItems() as $acceptanceItem) {
                        $stockViewInstance->orWhere(
                            'inHospitalItemId',
                            $acceptanceItem->getInHospitalItemId()->value()
                        );
                    }
                }
                
                $stocks = $stockViewInstance->get();

                if ((int) $stocks->count() === 0) {
                    throw new Exception("Stocks don't exist.", 998);
                }

                foreach ($acceptances as $acceptance) {
                    foreach ($acceptance->getAcceptanceItems() as $item) {
                        $stock = array_find($stocks->all(), function ($stock) use (
                            $item
                        ) {
                            return $item
                                ->getSourceDivision()
                                ->getDivisionId()
                                ->value() === $stock->divisionId &&
                                $item->getInHospitalItemId()->value() ===
                                    $stock->inHospitalItemId;
                        });

                        if (!$stock) {
                            throw new Exception("Stocks don't exist.", 998);
                        }

                        $requestItemCounts[] = new RequestItemCount(
                            $stock->recordId,
                            $hospitalId,
                            $item->getInHospitalItemId(),
                            $item->getItem()->getItemId(),
                            (int) $item->getAcceptanceQuantity()->value() * -1,
                            $acceptance->getTargetDivision()->getDivisionId(),
                            $acceptance->getSourceDivision()->getDivisionId()
                        );
                    }
                }
            }

            */
            $inventoryCalculations = [];
            foreach ($acceptances as $acceptance) {
                foreach ($acceptance->getAcceptanceItems() as $item) {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $item->getSourceDivision()->getDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        4,
                        $item->getLot(),
                        $item->getAcceptanceQuantity()->value() * -1
                    );
                    /*
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $item->getTargetDivision()->getDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        5,
                        $item->getLot(),
                        $item->getAcceptanceQuantity()->value()
                    );
                    */
                }
            }
            var_dump($inHospitalItems);
/*
            $this->repositoryProvider
                ->getAcceptanceRepository()
                ->saveToArray($acceptances);

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            if (!empty($updateCards)) {
                $this->repositoryProvider
                    ->getCardRepository()
                    ->update($hospitalId, $updateCards);
            }
            */

        }
    }
}

/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Api\Acceptance {
    use Auth;
    use stdClass;

    /**
     * Class AcceptanceRegisterInputData
     * @package JoyPla\Application\InputPorts\Acceptance\Api
     */
    class AcceptanceRegisterInputData
    {
        public Auth $user;
        public array $acceptanceItems;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            array $acceptanceItems,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->acceptanceItems = array_map(function ($v) {
                $object = new stdClass();
                //$object->recordId = $v['recordId'];
                $object->inHospitalItemId = $v['inHospitalItemId'];
                $object->acceptanceSourceDivisionId = $v['sourceDivisionId'];
                $object->acceptanceTargetDivisionId = $v['targetDivisionId'];
                $object->acceptanceQuantity = $v['AcceptanceQuantity'];
                $object->lotNumber = $v['lotNumber'];
                $object->lotDate = $v['lotDate'];
                $object->card = $v['card'];
                return $object;
            }, $acceptanceItems);

            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface AcceptanceRegisterInputPortInterface
     * @package JoyPla\Application\InputPorts\Acceptance\Api
     */
    interface AcceptanceRegisterInputPortInterface
    {
        /**
         * @param AcceptanceRegisterInputData $inputData
         */
        public function handle(AcceptanceRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\Acceptance {
    /**
     * Class AcceptanceRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Acceptance\Api;
     */
    class AcceptanceRegisterOutputData
    {
        public array $ids;

        public function __construct(array $ids)
        {
            $this->ids = $ids;
        }
    }

    /**
     * Interface AcceptanceRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Acceptance\Api;
     */
    interface AcceptanceRegisterOutputPortInterface
    {
        /**
         * @param AcceptanceRegisterOutputData $outputData
         */
        public function output(AcceptanceRegisterOutputData $outputData);
    }
}
