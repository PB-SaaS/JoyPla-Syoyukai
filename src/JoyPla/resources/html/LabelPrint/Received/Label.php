<div id="top" v-cloak>
    <div class="paper A4 ">
        <!-- here -->
        <div class="p-6 relative">
            <?php
            $num = 1 ;
            $nowTime = date('Y年m月d日 H時i分s秒');
            foreach($viewModel->print as $print) {
                for($rnum = 1 ; $rnum <= $print->count; $rnum++){
                    if(($num - 1 )% 2 == 0){
                        echo "<div>";
                    }
                    $design = $print->labeldesign;
                    $design = str_replace('%JoyPla:nowTime%',			date('Y年m月d日 H時i分s秒'), 				$design);//バーコードの値
                    $design = str_replace('%JoyPla:barcodeId%',			$print->barcodeStr(), 						$design);//バーコードの値
                    $design = str_replace('%JoyPla:num%',				$num, 										$design);//枚目
                    $design = str_replace('%JoyPla:inHPId%',			$print->receivedItem->getInHospitalItemId()->value(), 					$design);//院内商品ID
                    $design = str_replace('%JoyPla:itemName%',			$print->receivedItem->getItem()->getItemName()->value(),                    		$design);//商品名
                    $design = str_replace('%JoyPla:itemCode%',			$print->receivedItem->getItem()->getItemCode()->value(), 		                    $design);//製品コードb
                    $design = str_replace('%JoyPla:itemStandard%',		$print->receivedItem->getItem()->getItemStandard()->value(),	                    $design);//商品規格
                    $design = str_replace('%JoyPla:itemJANCode%',		$print->receivedItem->getItem()->getItemJANCode()->value(), 	                    $design);//JANコードb
                    $design = str_replace('%JoyPla:itemUnit%',			$print->receivedItem->getQuantity()->getItemUnit(), 		                    $design);//個数単位
                    $design = str_replace('%JoyPla:quantity%',			(int)$print->receivedItem->getQuantity()->getQuantityNum(), 		                    $design);//入り数
                    $design = str_replace('%JoyPla:catalogNo%',			$print->receivedItem->getItem()->getCatalogNo()->value(), 		                    $design);//カタログ名
                    $design = str_replace('%JoyPla:distributorName%',	$print->receivedItem->getDistributor()->getDistributorName(),			       	$design);//卸業者名
                    $design = str_replace('%JoyPla:itemMaker%',			$print->receivedItem->getItem()->getMakerName()->value(), 		                    $design);//メーカー名
                    $design = str_replace('%JoyPla:quantityUnit%',		$print->receivedItem->getQuantity()->getQuantityUnit(),	                    $design);//入数単位
                    $design = str_replace('%JoyPla:divisionName%',		$print->receivedItem->getDivision()->getDivisionName()->value(),							$design);//部署 
                    $design = str_replace('%JoyPla:officialFlag%',		($print->receivedItem->getRedemption()->getRedemptionFlag())? "償還" : "" ,								$design);//償還フラグ
                    $design = str_replace('%JoyPla:officialFlag:id%',   $print->receivedItem->getRedemption()->getRedemptionFlag(),					    $design);//償還フラグ id
                    $design = str_replace('%JoyPla:lotNumber%',			$print->receivedItem->getLot()->getLotNumber()->value(), 		                    $design);//ロット
                    $design = str_replace('%JoyPla:lotDate%',			$print->receivedItem->getLot()->getLotDate()->value(), 		                    $design);//使用期限
                    
                    echo htmlspecialchars_decode($design,ENT_QUOTES);

                    if(($num)% 2 == 0){
                        echo "</div>";
                    }
                    $num ++ ;
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
var JoyPlaApp = Vue.createApp({
    components: {
    },
    setup(){
      const numberFormat = (value) => {
          if (! value ) { return 0; }
          return new Intl.NumberFormat('ja-JP').format(value);
      };
      return {
        numberFormat,
      }
    },
    data() {
    },
    async created() {
    },
    methods: {
    }
}).mount('#top');
</script>