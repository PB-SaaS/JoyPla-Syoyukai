<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top">
    <div class="uk-container uk-container-expand">
        <div uk-grid>
            <div class="uk-width-1-2@m">
                <h1>施設情報詳細[%val:usr:hospitalName%]</h1>
            </div>
        </div>
        <div>
            <ul class="uk-child-width-expand uk-tab">
                <li class="<?php echo $switch_1 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&table_cache=true'">基本情報</a></li>
                <li class="<?php echo $switch_2 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&Switcher=Division&table_cache=true'">部署情報</a></li>
                <li class="<?php echo $switch_3 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&Switcher=Users&table_cache=true'">ユーザー情報</a></li>
                <li class="<?php echo $switch_4 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&Switcher=Distributor&table_cache=true'">卸業者情報</a></li>
            </ul>
            <?php if($switch_1 != ""): ?>
            <table class="uk-table uk-table-divider">
                <tbody>
                    <tr>
                        <th>
                            登録日時
                        </th>
                        <td>
                            %val:usr:registrationTime%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            更新日時
                        </th>
                        <td>
                            %val:usr:updateTime%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            病院ID
                        </th>
                        <td>
                            %val:usr:hospitalId%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            病院名
                        </th>
                        <td>
                            %val:usr:hospitalName%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            郵便番号
                        </th>
                        <td>
                            %val:usr:postalCode%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            都道府県
                        </th>
                        <td>
                            %val:usr:prefectures%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            住所
                        </th>
                        <td>
                            %val:usr:address%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            電話番号
                        </th>
                        <td>
                            %val:usr:phoneNumber%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            FAX番号
                        </th>
                        <td>
                            %val:usr:faxNumber%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            責任者氏名
                        </th>
                        <td>
                            %val:usr:name%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            責任者氏名（カナ）
                        </th>
                        <td>
                            %val:usr:nameKana%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            メールアドレス
                        </th>
                        <td>
                            %val:usr:mailAddress%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            責任者連絡先
                        </th>
                        <td>
                            %val:usr:contactAddress%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            入庫先カスタマイズ
                        </th>
                        <td>
                            %val:usr:receivingTarget%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            登録ユーザー可能数
                        </th>
                        <td>
                            %val:usr:registerableNum%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            消費単価使用フラグ
                        </th>
                        <td>
                            %val:usr:billingUnitPrice:v%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            払出単価使用フラグ
                        </th>
                        <td>
                            %val:usr:payoutUnitPrice:v%
                        </td>
                    </tr>
                    <tr>
                        <th>
                            棚卸単価使用フラグ
                        </th>
                        <td>
                            %val:usr:invUnitPrice:v%
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr></tr>
                </tfoot>
            </table>
            <?php elseif($switch_2 != ""): ?>
           
        	<h3>部署一覧</h3>
        	<div uk-margin>
        	    <form action="<?php echo $base_url ?>" method="post">
        	        <button type="submit" class="uk-button uk-button-primary" name="Action" value="divisionReg">部署登録</button>
        	    </form>
        	</div>
    		<div class="uk-margin spiral_table_area" style="display:none">
    			%sf:usr:search6:table:mstfilter%
    		</div>
            <?php elseif($switch_3 != ""): ?>
           
        	<h3>ユーザー一覧</h3>
        	<div uk-margin>
        	    <form action="<?php echo $base_url ?>" method="post">
        	        <button type="submit" class="uk-button uk-button-primary" name="Action" value="hospitalUserRegDivisionList">病院ユーザー追加</button>
        	    </form>
        	</div>
    		<div class="uk-margin spiral_table_area" style="display:none">
    			%sf:usr:search9:table:mstfilter%
    		</div>
            	
            <?php elseif($switch_4 != ""): ?>
           
        	<h3>卸業者一覧</h3>
        	<div uk-margin>
        	    <form action="<?php echo $base_url ?>" method="post">
        	        <button type="submit" class="uk-button uk-button-primary" name="Action" value="distributorReg">卸業者登録</button>
        	    </form>
        	</div>
    		<div class="uk-margin spiral_table_area" style="display:none">
    			%sf:usr:search19:table:mstfilter%
    		</div>
            <?php else: ?>


            <?php endif ?>
        </div>
    </div>
</div>