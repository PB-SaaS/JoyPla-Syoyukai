<div class="uk-width-2-3@m uk-margin-auto uk-margin-remove-top uk-margin-bottom" id="mainPage">

    <h1>商品情報登録 - 入力</h1>

    <?php if($errors && $errors->isError()): ?>
    <p class="header_emesg">入力内容に不備があります。入力された値をご確認ください</p>
    <?php endif ?>
    <p class="header_rmesg">必要事項をご入力の上、確認ボタンを押してください。</p>

    <div>
        <form method="post" x-bind:action="_ROOT">
            <input type="hidden" name="_method" value="post">
            <input type="hidden" name="_csrf" value="<?= $csrf ?>">
            <input type="hidden" name="path" value="registItemAndPriceAndInHPForm/confirm">
            <div class="smp_tmpl">
                <dl class="cf">
                    <dt class="title">
                        商品名 <span class="need">必須</span>
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->itemName->message()) ? "error" : "" ?>" type="text" name="itemName" value="<?php echo $input["itemName"]; ?>" maxlength="128" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->itemName->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        分類
                    </dt><dd class="data ">
                        <select class="<?= ($errors && $errors->category->message()) ? "error" : "" ?>" name="category">
                            <option value="">----- 選択してください -----</option>
                            <option value="1"  <?php ($input["category"] === "1")  ? "selected" : "" ; ?>>医療材料</option>
                            <option value="2"  <?php ($input["category"] === "2")  ? "selected" : "" ; ?>>薬剤</option>
                            <option value="3"  <?php ($input["category"] === "3")  ? "selected" : "" ; ?>>試薬</option>
                            <option value="4"  <?php ($input["category"] === "4")  ? "selected" : "" ; ?>>日用品</option>
                            <option value="99" <?php ($input["category"] === "99") ? "selected" : "" ; ?>>その他</option>
                        </select>
                        <br>
                        <span class="msg"><?= ($errors)? $errors->category->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        小分類
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->smallCategory->message()) ? "error" : "" ?>" type="text" name="smallCategory" value="<?php echo $input["smallCategory"]; ?>" maxlength="128" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->smallCategory->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        製品コード
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->itemCode->message()) ? "error" : "" ?>" type="text" name="itemCode" value="<?php echo $input["itemCode"]; ?>" maxlength="128" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->itemCode->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        規格
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->itemStandard->message()) ? "error" : "" ?>" type="text" name="itemStandard" value="<?php echo $input["itemStandard"]; ?>" maxlength="128" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->itemStandard->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        JANコード <span class="need">必須</span>
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->itemJANCode->message()) ? "error" : "" ?>" type="text" name="itemJANCode" value="<?php echo $input["itemJANCode"]; ?>" maxlength="128" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->itemJANCode->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        メーカー名
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->makerName->message()) ? "error" : "" ?>" type="text" name="makerName" value="<?php echo $input["makerName"]; ?>" maxlength="128" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->makerName->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        カタログNo
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->catalogNo->message()) ? "error" : "" ?>" type="text" name="catalogNo" value="<?php echo $input["catalogNo"]; ?>" maxlength="128" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->catalogNo->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        シリアルNo
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->serialNo->message()) ? "error" : "" ?>" type="text" name="serialNo" value="<?php echo $input["serialNo"]; ?>" maxlength="128" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->serialNo->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        ロット管理フラグ
                    </dt><dd class="data multi2">
                        <ul class="cf">
                            <li><label><input class="input" type="checkbox" name="lotManagement" value="1" <?php echo $checked["lotManagement"][1] ?>><span>はい</span></label></li>
                        </ul>
                        <input type="hidden" value="" name="lotManagement">
                        <span class="msg"><?= ($errors)? $errors->lotManagement->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        償還価格フラグ
                    </dt><dd class="data multi2">
                        <ul class="cf">
                            <li><label><input class="input" type="checkbox" name="officialFlag" value="1" <?php echo $checked["officialFlag"][1] ?>><span>はい</span></label></li>
                        </ul>
                        <input type="hidden" value="" name="officialFlag">
                        <span class="msg"><?= ($errors)? $errors->officialFlag->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        償還価格
                    </dt><dd class="data real">
                        
                        <input class="input <?= ($errors && $errors->officialprice->message()) ? "error" : "" ?>" type="text" name="officialprice" value="<?php echo $input["officialprice"]; ?>" maxlength="20" style="text-align: right;" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->officialprice->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        旧償還価格
                    </dt><dd class="data real">
                        <input class="input <?= ($errors && $errors->officialpriceOld->message()) ? "error" : "" ?>" type="text" name="officialpriceOld" value="<?php echo $input["officialpriceOld"]; ?>" maxlength="20" style="text-align: right;" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->officialpriceOld->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        入数 <span class="need">必須</span>
                    </dt><dd class="data integer">
                        <input class="input <?= ($errors && $errors->quantity->message()) ? "error" : "" ?>" type="number" name="quantity" value="<?php echo $input["quantity"]; ?>" maxlength="10" style="text-align: right;" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->quantity->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        入数単位 <span class="need">必須</span>
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->quantityUnit->message()) ? "error" : "" ?>" type="text" name="quantityUnit" value="<?php echo $input["quantityUnit"]; ?>" maxlength="32" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->quantityUnit->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        個数単位 <span class="need">必須</span>
                    </dt><dd class="data ">
                        <input class="input <?= ($errors && $errors->itemUnit->message()) ? "error" : "" ?>" type="number" name="itemUnit" value="<?php echo $input["itemUnit"]; ?>" maxlength="32" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->itemUnit->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        定価
                    </dt><dd class="data real">
                        <input class="input <?= ($errors && $errors->minPrice->message()) ? "error" : "" ?>" type="number" name="minPrice" value="<?php echo $input["itemName"]; ?>" maxlength="20" style="text-align: right;" >
                        <br>
                        <span class="msg"><?= ($errors)? $errors->minPrice->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        卸業者
                        <span class="need">必須</span>
                    </dt>
                    <dd class="data ">
                        <select name="distributorId" id="distributorId" class="uk-select <?= ($errors && $errors->distributorId->message()) ? "error" : "" ?>">
                            <option value="">
                                --- 選択してください ---
                            </option>
                            <?php
                            foreach($distributor as $key){
                                $selected = ($input["distributorId"] === $key["distributorId"]) ? "selected" : "";
                                echo "<option value='" . $key["distributorId"] . "' " . $selected . ">" . $key["distributorName"] . "</option>\n";
                            }
                            ?>
                        </select>
                        <br>
                        <span class="msg"><?= ($errors)? $errors->distributorId->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        卸業者管理コード
                    </dt>
                    <dd class="data ">
                        <input class="input <?= ($errors && $errors->distributorMCode->message()) ? "error" : "" ?>" type="text" name="distributorMCode" value="<?php echo $input["distributorMCode"]; ?>" maxlength="128">
                        <br>
                        <span class="msg"><?= ($errors)? $errors->distributorMCode->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        購買価格
                        <span class="need">必須</span>
                    </dt>
                    <dd class="data real">
                        <input class="input <?= ($errors && $errors->price->message()) ? "error" : "" ?>" type="number" name="price" value="<?php echo $input["price"]; ?>" maxlength="20" style="text-align: right;">
                        <br>
                        <span class="msg"><?= ($errors)? $errors->price->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        単価
                    </dt>
                    <dd class="data real">
                        <input class="input <?= ($errors && $errors->unitPrice->message()) ? "error" : "" ?>" type="number" name="unitPrice" value="<?php echo $input["unitPrice"]; ?>" maxlength="20" style="text-align: right;">
                        <br>
                        <button type="button" onclick="getUnitPrice()" class="uk-button uk-button-default" title="購買価格÷入数の値を自動で入力します">単価を自動計算</button>
                        <script>
                            function getUnitPrice() {
                                let price = $('input[name=price]')[0].value;
                                let quantity = $('input[name=quantity]')[0].value;

                                let unitPrice = 0;
                                if (price == "" || price == 0) {
                                    unitPrice = 0
                                }
                                if (quantity == "" || quantity == 0) {
                                    unitPrice = 0
                                }
                                unitPrice = (price / quantity);

                                $('input[name=unitPrice]')[0].value = unitPrice;
                            }
                        </script>
                        <br>
                        <span class="msg"><?= ($errors)? $errors->unitPrice->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        保険請求分類（医科）
                    </dt>
                    <dd class="data ">
                        <textarea class="<?= ($errors && $errors->medicineCategory->message()) ? "error" : "" ?>" name="medicineCategory" rows="4" wrap="soft"><?php echo $input["medicineCategory"]; ?></textarea><br>
                        <span class="msg"><?= ($errors)? $errors->medicineCategory->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        保険請求分類（在宅）
                    </dt>
                    <dd class="data ">
                        <textarea class="<?= ($errors && $errors->homeCategory->message()) ? "error" : "" ?>" name="homeCategory" rows="4" wrap="soft"><?php echo $input["homeCategory"]; ?></textarea><br>
                        <span class="msg"><?= ($errors)? $errors->homeCategory->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        測定機器名
                    </dt>
                    <dd class="data ">
                        <input class="input <?= ($errors && $errors->measuringInst->message()) ? "error" : "" ?>" type="text" name="measuringInst" value="<?php echo $input["measuringInst"]; ?>" maxlength="128"><br>
                        <span class="msg"><?= ($errors)? $errors->measuringInst->message() : "" ?></span>
                    </dd>
                </dl>
                <dl class="cf">
                    <dt class="title">
                        特記事項
                    </dt>
                    <dd class="data ">
                        <textarea class="<?= ($errors && $errors->notice->message()) ? "error" : "" ?>" name="notice" rows="4" wrap="soft"><?php echo $input["notice"]; ?></textarea><br>
                        <span class="msg"><?= ($errors)? $errors->notice->message() : "" ?></span>
                    </dd>
                </dl>
            </div>
            <input class="submit" type="submit" name="submit" value="確認">
        </form>
    </div>
</div>
