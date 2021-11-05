<?php

/*
namespace App\Api;

class UpdateInventory{
    
    private $spiralDataBase;
    private $userInfo;

    private $database = 'invInvEndData';
    private $inventoryEndDatabase = 'NJ_InventoryEDB';
    private $inventoryHistDatabase = 'NJ_InventoryHDB';
    private $column = array('inHospitalItemId','inventoryEndId','inventoryHId','inventryAmount');

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function update(string $inHospitalItemId, int $price, int $unitPrice){
        $makePriceUpdateData = [];

        $makePriceUpdateData = $this->makePriceUpdateData($price, $unitPrice);

        $result = $this->updateInvInvEndDB($inHospitalItemId, $makePriceUpdateData);

        if ((int)$result['code'] !== 0) {
            var_dump($result);
            return false;
        }
        if ((int)$result['count'] < 1) {
            return 'no-record found';
        }

        $invInvEndDB = $this->getInvInvEndDB();

        list($endIds, $histIds) = $this->getInvIds($inHospitalItemId, $invInvEndDB);

        $endTotal = $this->makeInvEndTotal($invInvEndDB, $endIds);
        if (count($endTotal) > 0) {
            $endBulkData = array_chunk($endTotal, 999, true);
            foreach ($endBulkData as $items_1000) {
                $result = $this->updateInvEndDB($items_1000);
                if ((int)$result['code'] !== 0) {
                    var_dump($result);
                    return false;
                }
            }
        }

        $histTotal = $this->makeInvHistTotal($invInvEndDB, $histIds);
        if(count($histTotal) > 0) {
            $histBulkData = array_chunk($histTotal, 999, true);
            foreach ($histBulkData as $items_1000) {
                $result = $this->updateInvHistDB($items_1000);
                if((int)$result['code'] !== 0){
                    var_dump($result);
                    return false;
                }
            }
        }

        return true;

    }


    private function makePriceUpdateData(int $price, int $unitPrice){
        $updateData = [['name' => 'updateTime', 'value' => 'now']];
        if ($price > 0) { $updateData[] = ['name' => 'price', 'value' => $price]; }
        if ($unitPrice > 0) { $updateData[] = ['name' => 'unitPrice', 'value' => $unitPrice]; }
        
        return $updateData;
    }

    private function getInvInvEndDB(){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSearchCondition('inventoryStatus','1');
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectFieldsToArray($this->column);
        $result = $this->spiralDataBase->doSelectLoop();
        return $this->spiralDataBase->arrayToNameArray($result['data'],$this->column);
    }

    private function getInvIds(string $inHospitalItemId, array $array){
        $endIds = [];
        $histIds = [];
        foreach($array as $data) {
            if ($inHospitalItemId === $data['inHospitalItemId']) {
                if (!in_array($data['inventoryEndId'], $endIds)) {
                    $endIds[] = $data['inventoryEndId'];
                }
                if (!in_array($data['inventoryHId'], $histIds)) {
                    $histIds[] = $data['inventoryHId'];
                }
            }
        }
        return [$endIds, $histIds];
    }

    private function makeInvEndTotal(array $invInvEndData, array $ids){
        $endBulkData = [];
        foreach ($ids as $id) {
            $total = 0;
            foreach ($invInvEndData as $data) {
                if ($id === $data['inventoryEndId']) {
                    $total += (int)$data['inventryAmount'];
                }
            }
            $endBulkData[] = array($id, $total);
        }
        return $endBulkData;
    }

    private function makeInvHistTotal(array $invInvEndData, array $ids){
        $histBulkData = [];
        foreach ($ids as $id) {
            $total = 0;
            foreach ($invInvEndData as $data) {
                if ($id === $data['inventoryHId']) {
                    $total += (int)$data['inventryAmount'];
                }
            }
            $histBulkData[] = array($id, $total);
        }
        return $histBulkData;
    }

    private function updateInvInvEndDB(string $inHospitalItemId, array $array){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSearchCondition('inventoryStatus','1');
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('inHospitalItemId',$inHospitalItemId);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doUpdate($array);
    }

    private function updateInvEndDB(array $blukUpdateData){
        $this->spiralDataBase->setDataBase($this->inventoryEndDatabase);
        $columns = array('inventoryEndId','totalAmount');
        $this->spiralDataBase->addSelectNameCondition('');
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        return $this->spiralDataBase->doBulkUpdate('inventoryEndId',$columns,$blukUpdateData);
    }

    private function updateInvHistDB(array $blukUpdateData){
        $this->spiralDataBase->setDataBase($this->inventoryHistDatabase);
        $columns = array('inventoryHId','totalAmount');
        $this->spiralDataBase->addSelectNameCondition('');
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        return $this->spiralDataBase->doBulkUpdate('inventoryHId',$columns,$blukUpdateData);
    }

}