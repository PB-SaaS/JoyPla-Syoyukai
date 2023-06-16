<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\Acceptance {

    use ApiResponse;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Acceptance\AcceptanceRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Acceptance\AcceptanceRegisterInputPortInterface;
    use JoyPla\Enterprise\Models\Acceptance;
    use JoyPla\Enterprise\Models\AcceptanceId;
    use JoyPla\Enterprise\Models\AcceptanceItem;
    use JoyPla\Enterprise\Models\AcceptanceItemId;
    use JoyPla\Enterprise\Models\DateYearMonthDay;
    use JoyPla\Enterprise\Models\Division;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InHospitalItem;
    use JoyPla\Enterprise\Models\InHospitalItemId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\ItemId;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\RequestItemCount;
    use JoyPla\Enterprise\Models\UnitPrice;
    use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
    use JoyPla\InterfaceAdapters\GateWays\Repository\RequestItemCountRepository;
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
                    return $value->getDivisionId()->value() ==
                        $sourceDivisionId;
                });

                $targetDivisionId = $acceptanceItem->acceptanceTargetDivisionId;
                $targetDivision = array_find($divisions, function (
                    Division $value
                ) use ($targetDivisionId) {
                    return $value->getDivisionId()->value() ==
                        $targetDivisionId;
                });

                if (array_find($acceptances, function (Acceptance $value) use (
                        $targetDivision,
                        $sourceDivision
                    ) {
                        return $value->equalDivisions(
                            $sourceDivision->getDivisionId(),
                            $targetDivision->getDivisionId()
                        );
                    })
                ) {
                    continue;
                }

                $acceptances[] = new Acceptance(
                    AcceptanceId::generate(),
                    new DateYearMonthDay('now'),
                    $hospital->getHospitalId(),
                    $sourceDivision->getDivisionId(),
                    $targetDivision->getDivisionId(),
                );
            }

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

                if ($hospitalRow->payoutUnitPrice !== '1') {
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

                foreach ($acceptances as $key => $acceptance) {
                    if (
                        $acceptance->equalDivisions(
                            $sourceDivision->getDivisionId(),
                            $targetDivision->getDivisionId(),
                        )
                    ) {
                        $lot = new Lot(
                            new LotNumber($acceptanceItem->lotNumber),
                            new LotDate($acceptanceItem->lotDate)
                        );
                        $item = new AcceptanceItem(
                            $acceptance->getAcceptanceId(),
                            AcceptanceItemId::generate(),
                            $inHospitalItem->getInHospitalItemId(),
                            $lot->getLotDate(),
                            $lot->getLotNumber(),
                            $inHospitalItem->getQuantity()->getQuantityNum(),
                            $inHospitalItem->getQuantity()->getQuantityUnit(),
                            $inHospitalItem->getQuantity()->getItemUnit(),
                            $inHospitalItem->getPrice(),
                            new UnitPrice($unitprice),
                            $acceptanceItem->acceptanceQuantity,
                            0
                        );
                        $acceptances[$key]->addItem($item);
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
                            ->getSourceDivisionId()
                            ->value()
                    );

                    foreach ($acceptance->getItems() as $acceptanceItem) {
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
                    foreach ($acceptance->getItems() as $item) {
                        $inHospitalItemId = $item->getInHospitalItemId()->value();

                        $inHospitalItem = array_find($inHospitalItems, function (
                            $value
                        ) use ($inHospitalItemId) {
                            return $value->getInHospitalItemId()->value() ===
                                $inHospitalItemId;
                        });

                        $stock = array_find($stocks->all(), function ($stock) use (
                            $item,
                            $acceptance
                        ) {
                            return $acceptance
                                ->getSourceDivisionId()
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
                            $inHospitalItem->getItem()->getItemId(),
                            ((int) $item->getAcceptanceQuantity()) * -1,
                            $acceptance->getTargetDivisionId(),//請求元＝払出先
                            $acceptance->getSourceDivisionId()//請求先＝払出元
                        );
                    }
                }
            }

            $inventoryCalculations = [];
            foreach ($acceptances as $acceptance) {
                foreach ($acceptance->getItems() as $item) {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $acceptance->getHospitalId(),
                        $acceptance->getSourceDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        4,
                        new Lot(
                            $item->getLotNumber(),
                            $item->getLotDate()
                        ),
                        $item->getAcceptanceQuantity() * -1
                    );
                }
            }

            if(!$inputData->isOnlyAcceptance){
                $this->repositoryProvider
                    ->getRequestItemCountRepository()
                    ->saveToArray($requestItemCounts);
            }

            $this->repositoryProvider->getAcceptanceRepository()->saveToArray($acceptances);

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);


            $ids = array_map(function($item){
                return $item->getAcceptanceId()->value();
            },$acceptances);
            echo (new ApiResponse($ids, count($acceptances), 200 , 'success' , []))->toJson();
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
        public bool $isOnlyAcceptance = false;

        public function __construct(
            Auth $user,
            array $acceptanceItems,
            bool $isOnlyMyDivision,
            bool $isOnlyAcceptance = false
        ) {
            $this->user = $user;
            $this->acceptanceItems = array_map(function ($v) {
                $object = new stdClass();
                //$object->recordId = $v['recordId'];
                $object->inHospitalItemId = $v['inHospitalItemId'];
                $object->acceptanceSourceDivisionId = $v['sourceDivisionId'];
                $object->acceptanceTargetDivisionId = $v['targetDivisionId'];
                $object->acceptanceQuantity = $v['acceptanceQuantity'];
                $object->lotNumber = $v['lotNumber'];
                $object->lotDate = $v['lotDate'];
                $object->card = $v['card'];
                return $object;
            }, $acceptanceItems);

            $this->isOnlyMyDivision = $isOnlyMyDivision;
            $this->isOnlyAcceptance = $isOnlyAcceptance;
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
