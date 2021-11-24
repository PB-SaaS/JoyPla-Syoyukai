<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top">
    <div class="uk-container uk-container-expand">
    	<h1>月次レポート【<?php echo $title ?>】</h1>
		<div class="uk-margin spiral_table_area">
			<form class="uk-margin-large" action="<?php echo $api_url ?>" method="post">
			    <div class="smp_tmpl uk-text-left uk-width-3-4@m uk-margin-auto">
					<dl class="cf">
						<dt class="title uk-form-label uk-margin-remove-top">
							病院 <span class="need uk-label uk-label-danger">必須</span>
						</dt>
						<dd class="data uk-form-controls">
						    <div uk-grid uk-margin>
						        <div class="uk-width-2-3@m">
                                    <select class="uk-select <?php echo ($error['hospitalId'] != "")? "uk-form-danger" : "" ?>" name="hospitalId">
                                        <option value=''>----- 選択してください -----</option>
                                        <?php
                                        foreach($hospital as $h)
                                        {
                                            $selected = '';
                                            if($current_hospitalId === $h->hospitalId)
                                            {
                                                $selected = 'selected';
                                            }
                                            echo "<option value='".$h->hospitalId."' ".$selected.">".$h->hospitalName."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
						        <div class="uk-width-1-3@m">
                                    <button class="uk-button uk-button-primary" name="step" value="2">月次レポート表示</button>
                                </div>
                            </div>
                            <br>
                            <span class="uk-text-danger"><?php echo $error['hospitalId'] ?></span>
        				</dd>
					</dl>
                    <input type="hidden" name="Action" value="<?php echo $Action ?>">
                </div>
            </form>
		</div>
    </div>
</div>
