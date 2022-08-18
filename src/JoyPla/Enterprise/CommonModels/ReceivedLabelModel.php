<?php

namespace JoyPla\Enterprise\CommonModels;

use Collection;
use JoyPla\Enterprise\Models\ReceivedItem;

class ReceivedLabelModel
{
    public function __construct(
        ReceivedItem $receivedItem,
        int $count = 1, 
        string $labeldesign = ""
        )
    {
        $this->receivedItem = $receivedItem;
        $this->count = $count;
        if($labeldesign === ""){
            $labeldesign = self::defaultdesign();
        }
        $this->labeldesign = $labeldesign;
    }

    public static function defaultdesign()
    {
        return <<<EOL
        <div class="printarea">
		<span>%JoyPla:distributorName%</span><br>
		<span>メーカー名：%JoyPla:itemMaker%</span><br>
		<span>商品名：%JoyPla:itemName%</span><br>
		<span>規格：%JoyPla:itemStandard%</span><br>
		<span>商品コード：%JoyPla:itemCode%</span>
		<span>入数：%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
		<span>%JoyPla:lotNumber% %JoyPla:lotDate%</span>
		<span>%JoyPla:nowTime%</span><br>
		<div class="text-center" id="barcode_%JoyPla:num%">%JoyPla:barcodeId%</div>
        </div>
EOL;
    }

    public function barcodeStr()
    {       
        $num = str_replace('rec_', '', $this->receivedItem->getReceivedItemId()->value());
        return "20".$num; // 検収書から生成できるバーコードは20にする。
    }
}