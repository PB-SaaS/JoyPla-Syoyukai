<div
    class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
    <div class="uk-container uk-container-expand">
        <div uk-grid="uk-grid">
            <div class="uk-width-1-2@m">
                <h1>卸業者情報詳細[%val:usr:hospitalName% : %val:usr:distributorName%]</h1>
            </div>
        	<div class="uk-width-1-2@m uk-text-right">
        	    <form action="<?php echo $base_url; ?>" method="post">
        	        <button type="submit" class="uk-button uk-button-primary" name="Action" value="divisionUserInv">卸業者ユーザー招待</button>
        	    </form>
        	</div>
        </div>
        <div>
            <ul class="uk-child-width-expand uk-tab">
                <li class="<?php echo $switch_1; ?>">
                    <a href="#" onclick="location.href='<?php echo $base_url; ?>&table_cache=true'">基本情報</a>
                </li>
                <li class="<?php echo $switch_2; ?>">
                    <a
                        href="#"
                        onclick="location.href='<?php echo $base_url; ?>&Switcher=Users&table_cache=true'">招待ユーザー一覧</a>
                </li>
            </ul>
        </div>
        <?php if ($switch_1 != ''): ?>
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
                        共通ID
                    </th>
                    <td>
                        %val:usr:distCommonId%
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
                        卸業者ID
                    </th>
                    <td>
                        %val:usr:distributorId%
                    </td>
                </tr>
                <tr>
                    <th>
                        卸業者名
                    </th>
                    <td>
                        %val:usr:distributorName%
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
                        見積対応可能業者フラグ
                    </th>
                    <td>
                        %val:usr:vendorFlag%
                    </td>
                </tr>
                <tr>
                    <th>
                        発注方法
                    </th>
                    <td>
                        %val:usr:orderMethod%
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr></tr>
            </tfoot>
        </table>
        <?php elseif ($switch_2 != ''): ?>
        	<h3>招待ユーザー一覧</h3>
    		<div class="uk-margin spiral_table_area" style="display:none">
    			%sf:usr:search22:table:mstfilter%
    		</div>
        <?php endif; ?>
        <script>
            let elem = $('table tbody a');
            let param = "<?php echo $_GET['BACK']; ?>";
            if(param != "")
            {
            	for(let index = 0 ; index < elem.length ; index++){
            		elem[index].href += "&BACK="+param;
            	};
            }
        </script>
    </div>
</div>