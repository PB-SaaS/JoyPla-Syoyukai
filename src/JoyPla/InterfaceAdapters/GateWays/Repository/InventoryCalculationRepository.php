<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\InventoryCalculation;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class InventoryCalculationRepository implements
    InventoryCalculationRepositoryInterface
{
    public function saveToArray(array $inventoryCalculations)
    {
        $inventoryCalculationsToArray = array_map(function (
            InventoryCalculation $inventoryCalculation
        ) {
            return $inventoryCalculation->toArray();
        },
        $inventoryCalculations);

        $insert = [];

        foreach ($inventoryCalculationsToArray as $inventoryCalculation) {
            $insert[] = [
                'hospitalId' => $inventoryCalculation['hospitalId'],
                'divisionId' => $inventoryCalculation['divisionId'],
                'inHospitalItemId' => $inventoryCalculation['inHospitalItemId'],
                'orderWithinCount' => $inventoryCalculation['orderedQuantity'],
                'count' => $inventoryCalculation['calculationQuantity'],
                'stockQuantity' => $inventoryCalculation['calculationQuantity'],
                'pattern' => $inventoryCalculation['pattern'],
                'lotNumber' => $inventoryCalculation['lot']['lotNumber'],
                'lotDate' => $inventoryCalculation['lot']['lotDate'],
                'lotUniqueKey' => $inventoryCalculation['uniqKey'],
            ];
        }
        ModelRepository::getInventoryAdjustmentTransactionInstance()->insert(
            $insert
        );

        return $inventoryCalculation;
    }
}

interface InventoryCalculationRepositoryInterface
{
    public function saveToArray(array $inventoryCalculation);
}
