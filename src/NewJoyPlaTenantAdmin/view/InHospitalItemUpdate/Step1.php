<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top">
    <div class="uk-container uk-container-expand">
    	<h1>院内商品情報変更 - 金額管理情報選択</h1>
		<div class="uk-margin spiral_table_area" style="display:none">
			<form class="uk-form-horizontal uk-margin-large" action="<?php echo $api_url ?>" method="post">
			    <div class="smp_tmpl uk-text-left">
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							病院名
						</dt><dd class="data uk-form-controls">
						 %val:usr:hospitalName% <br>						</dd>
					</dl>
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							金額管理情報 <span class="need uk-label uk-label-danger">必須</span>
						</dt><dd class="data uk-form-controls">
                        
                        <select class="uk-select <?php echo ($error['priceId'] != "")? "uk-form-danger" : "" ?>" name="priceId">
                            <option value=''>----- 選択してください -----</option>
                            <?php
                            foreach($priceinfo as $p)
                            {
                                $selected = '';
                                if($currentPriceId === $p->priceId)
                                {
                                    $selected = 'selected';
                                }
                                echo "<option value='".$p->priceId."' ".$selected.">".$p->distributorName."：&yen;".number_format($p->price,2)."/".$p->quantity.$p->quantityUnit."(".$p->itemUnit.")</option>";
                            }
                            ?>
                        </select>
                        <br>
                        <span class="uk-text-danger"><?php echo $error['priceId'] ?></span>
                        				</dd>
					</dl>
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							メーカー名
						</dt><dd class="data uk-form-controls">
						 %val:usr:makerName% <br>						</dd>
					</dl>
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							商品名
						</dt><dd class="data uk-form-controls">
						 %val:usr:itemName% <br>						</dd>
					</dl>
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							製品コード
						</dt><dd class="data uk-form-controls">
						 %val:usr:itemCode% <br>						</dd>
					</dl>
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							規格
						</dt><dd class="data uk-form-controls">
						 %val:usr:itemStandard% <br>						</dd>
					</dl>
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							JANコード
						</dt><dd class="data uk-form-controls">
						 %val:usr:itemJANCode% <br>						</dd>
					</dl>
                </div>
                <div class="uk-text-center uk-margin">
                    <input type="hidden" name="Action" value="update">
                    <button type="submit" class="uk-button uk-button-primary uk-margin-large-right uk-margin-large-left" name="step" value="2">変更へ進む</button>
                </div>
				
            </form>
		</div>
    </div>
</div>
