<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
    <div class="uk-container uk-container-expand">
    	<h1>金額・院内商品情報登録 - 病院選択</h1>
		<div class="uk-margin spiral_table_area" style="display:none">
			<form class="uk-form-horizontal uk-margin-large" action="<?php echo $api_url; ?>" method="post">
			    <div class="smp_tmpl uk-text-left">
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							病院 <span class="need uk-label uk-label-danger">必須</span>
						</dt><dd class="data uk-form-controls">
                        
                        <select class="uk-select <?php echo $error[
                            'hospitalId'
                        ] != ''
                            ? 'uk-form-danger'
                            : ''; ?>" name="hospitalId">
                            <option value=''>----- 選択してください -----</option>
                            <?php foreach ($hospital as $h) {
                                $selected = '';
                                if ($current_hospitalId === $h->hospitalId) {
                                    $selected = 'selected';
                                }
                                echo "<option value='" .
                                    $h->hospitalId .
                                    "' " .
                                    $selected .
                                    '>' .
                                    $h->hospitalName .
                                    '</option>';
                            } ?>
                        </select>
                        <br>
                        <span class="uk-text-danger"><?php echo $error[
                            'hospitalId'
                        ]; ?></span>
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
                    <input type="hidden" name="Action" value="priceAndInHospitalItemReg">
                    <button class="uk-button uk-button-primary uk-margin-large-right uk-margin-large-left" name="step" value="2">卸業者選択へ進む</button>
                </div>
            
            </form>
		</div>
    </div>
</div>
