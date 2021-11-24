<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top">
    <div class="uk-container uk-container-expand">
    	<h1>見積金額情報登録 - 卸業者選択</h1>
		<div class="uk-margin spiral_table_area" style="display:none">
			<form class="uk-form-horizontal uk-margin-large" action="<?php echo $api_url ?>" method="post">
			    <div class="smp_tmpl uk-text-left">
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							病院
						</dt><dd class="data uk-form-controls">
                        <?php echo $price_info_view->hospitalName ?> <br>						</dd>
					</dl>
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							卸業者 <span class="need uk-label uk-label-danger">必須</span>
						</dt><dd class="data uk-form-controls">
                        
                        <select class="uk-select <?php echo ($error['distributorId'] != "")? "uk-form-danger" : "" ?>" name="distributorId">
                            <option value=''>----- 選択してください -----</option>
                            <?php
                            foreach($distributor as $d)
                            {
                                $selected = '';
                                if($current_distributorId === $d->distributorId)
                                {
                                    $selected = 'selected';
                                }
                                echo "<option value='".$d->distributorId."' ".$selected.">".$d->distributorName."</option>";
                            }
                            ?>
                        </select> <br>	
                        <span class="uk-text-danger"><?php echo $error['distributorId'] ?>					</dd>
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
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							特記事項
						</dt><dd class="data uk-form-controls">
						 %val:usr:notice:br% <br>						</dd>
					</dl>
                </div>
                <div class="uk-text-center uk-margin">
                    <button class="uk-button uk-button-default " name="step" value="1">戻る</button>
                    <input type="hidden" name="hospitalId" value="<?php echo $price_info_view->hospitalId ?>">
                    <input type="hidden" name="Action" value="update">
                    <button class="uk-button uk-button-primary uk-margin-large-right uk-margin-large-left" name="step" value="2">登録へ進む</button>
                </div>
            
            </form>
		</div>
    </div>
</div>
