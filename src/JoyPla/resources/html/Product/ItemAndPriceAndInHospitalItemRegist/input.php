<?php
if($validate){
    $val = $validate->getResults();
    $is_error = $validate->isError();
}
?>
<div id="top" v-cloak>
    <v-loading :show="loading"></v-loading>
    <header-navi></header-navi>
    <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
    <div id="content" class="flex h-full px-1">
        <div class="flex-auto">
            <div class="index container mx-auto mb-96">
                <h1 class="text-2xl mb-2">商品情報登録 - 入力</h1>
                <hr>
                <div>

                    <?php if($validate && $is_error): ?>
                    <p class="header_emesg">入力内容に不備があります。入力された値をご確認ください</p>
                    <?php endif ?>
                    <p class="header_rmesg">必要事項をご入力の上、確認ボタンを押してください。</p>

                    <form method="post" :action="_ROOT" name="regForm">
                        <input type="hidden" name="_method" value="post">
                        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                        <input type="hidden" name="path" value="/product/ItemAndPriceAndInHospitalRegist/confirm">
                        <div class="smp_tmpl">
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="itemName"
                                :rules="{ required: true, }"
                                label="商品名"
                                title="商品名"
                                >
                                </v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    商品名 <span class="bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="itemName" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['itemName']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['itemName']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['itemName']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-select
                                name="category"
                                label="分類"
                                title="分類"
                                :options="[
                                    { value: '1',  label: '医療材料' },
                                    { value: '2',  label: '薬剤' },
                                    { value: '3',  label: '試薬' },
                                    { value: '4',  label: '日用品' },
                                    { value: '99', label: 'その他' },
                                ]"
                                errorMessage="<?= ($is_error)? $val['category']['message'] : '' ?>"
                                >
                                </v-select>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    分類
                                </div>
                                <div class="relative">
                                    <select name="category"
                                    class="appearance-none border w-full py-2 px-3 leading-tight 
                                    <?= ($is_error && $val['category']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    >
                                        <option value="">----- 選択してください -----</option>
                                        <option value="1"  <?php echo ($val['category']['value'] === "1")  ? "selected" : ""?>>医療材料</option>
                                        <option value="2"  <?php echo ($val['category']['value'] === "2")  ? "selected" : ""?>>薬剤</option>
                                        <option value="3"  <?php echo ($val['category']['value'] === "3")  ? "selected" : ""?>>試薬</option>
                                        <option value="4"  <?php echo ($val['category']['value'] === "4")  ? "selected" : ""?>>日用品</option>
                                        <option value="99" <?php echo ($val['category']['value'] === "99") ? "selected" : ""?>>その他</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-red-500"><?php echo html($val['category']['message']) ?></span>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="smallCategory"
                                label="小分類"
                                title="小分類"
                                errorMessage="<?= ($is_error)? $val['smallCategory']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    小分類
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="smallCategory" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['smallCategory']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['smallCategory']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['smallCategory']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="itemCode"
                                label="製品コード"
                                title="製品コード"
                                errorMessage="<?= ($is_error)? $val['itemCode']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    製品コード
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="itemCode" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['itemCode']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['itemCode']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['itemCode']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="itemStandard"
                                label="規格"
                                title="規格"
                                errorMessage="<?= ($is_error)? $val['itemStandard']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    規格
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="itemStandard" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['itemStandard']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['itemStandard']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['itemStandard']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="itemJANCode"
                                label="JANコード"
                                title="JANコード"
                                :rules="{ required: true, regex: /^\d{13}$/}"
                                errorMessage="<?= ($is_error)? $val['itemJANCode']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    JANコード <span class="bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="itemJANCode" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['itemJANCode']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['itemJANCode']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['itemJANCode']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="makerName"
                                label="メーカー名"
                                title="メーカー名"
                                errorMessage="<?= ($is_error)? $val['makerName']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    メーカー名
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="makerName" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['makerName']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['makerName']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['makerName']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="catalogNo"
                                label="カタログNo"
                                title="カタログNo"
                                errorMessage="<?= ($is_error)? $val['catalogNo']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    カタログNo
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="catalogNo" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['catalogNo']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['catalogNo']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['catalogNo']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="serialNo"
                                label="シリアルNo"
                                title="シリアルNo"
                                errorMessage="<?= ($is_error)? $val['serialNo']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    シリアルNo
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="serialNo" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['serialNo']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['serialNo']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['serialNo']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-text>
                                    ロット管理フラグ
                                </v-text>
                                <v-checkbox 
                                value="1" 
                                name="lotManagement" 
                                label="ロット管理フラグ" 
                                title="はい"
                                ></v-checkbox>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    ロット管理フラグ
                                </div>
                                <label>
                                    <input type="checkbox" class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain mr-2 cursor-pointer" value="1" name="lotManagement"
                                    <?= ($val['lotManagement']['value']) ? 'checked' : '' ?> >はい
                                </label><br>
                                <span class="text-red-500"><?php echo html($val['lotManagement']['message']) ?></span>

<!-- 
                                <dt class="title">
                                    ロット管理フラグ
                                </dt><dd class="data multi2">
                                    <ul class="cf">
                                        <li><label><input class="input" type="checkbox" name="lotManagement" value="1" <?php echo $checked["lotManagement"][1] ?>><span>はい</span></label></li>
                                    </ul>
                                    <input type="hidden" value="" name="lotManagement">
                                    <span class="msg"><?= ($errors)? $errors->lotManagement->message() : "" ?></span>
                                </dd>
 -->
                            </div>
                            <div class="cf">
<!-- 
                                <v-text>
                                    償還価格フラグ
                                </v-text>
                                <v-checkbox 
                                value="1" 
                                name="officialFlag" 
                                label="償還価格フラグ" 
                                title="はい"
                                ></v-checkbox>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    償還価格フラグ
                                </div>
                                <label>
                                    <input type="checkbox" class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain mr-2 cursor-pointer" value="1" name="officialFlag"
                                    <?= ($val['officialFlag']['value']) ? 'checked' : '' ?> >はい
                                </label><br>
                                <span class="text-red-500"><?php echo html($val['officialFlag']['message']) ?></span>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="officialprice"
                                label="償還価格"
                                title="償還価格"
                                errorMessage="<?= ($is_error)? $val['officialprice']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    償還価格
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="officialprice" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['officialprice']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['officialprice']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['officialprice']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="officialpriceOld"
                                label="旧償還価格"
                                title="旧償還価格"
                                errorMessage="<?= ($is_error)? $val['officialpriceOld']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    旧償還価格
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="officialpriceOld" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['officialpriceOld']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['officialpriceOld']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['officialpriceOld']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="quantity"
                                :rules="{ required: true, }"
                                label="入数"
                                title="入数"
                                errorMessage="<?= ($is_error)? $val['quantity']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    入数 <span class="bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="quantity" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['quantity']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['quantity']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['quantity']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="quantityUnit"
                                :rules="{ required: true, }"
                                label="入数単位"
                                title="入数単位"
                                errorMessage="<?= ($is_error)? $val['quantityUnit']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    入数単位 <span class="bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="quantityUnit" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['quantityUnit']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['quantityUnit']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['quantityUnit']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="itemUnit"
                                :rules="{ required: true, }"
                                label="個数単位"
                                title="個数単位"
                                errorMessage="<?= ($is_error)? $val['itemUnit']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    個数単位 <span class="bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="itemUnit" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['itemUnit']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['itemUnit']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['itemUnit']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="minPrice"
                                label="定価"
                                title="定価"
                                errorMessage="<?= ($is_error)? $val['minPrice']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    定価
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="minPrice" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['minPrice']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['minPrice']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['minPrice']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-select
                                name="distributorId"
                                :rules="{ required: true, }"
                                label="卸業者"
                                title="卸業者"
                                :options="[
                                    <?php
                                    foreach($distributor as $key){
                                        echo "{ value: '".$key["distributorId"]."',  label: '".$key["distributorName"]."' },\n";
                                    }
                                    ?>
                                ]"
                                errorMessage="<?= ($is_error)? $val['distributorId']['message'] : '' ?>"
                                >
                                </v-select>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    卸業者 <span class="bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                </div>
                                <div class="relative">
                                    <select name="distributorId"
                                    class="appearance-none border w-full py-2 px-3 leading-tight 
                                    <?= ($is_error && $val['distributorId']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    >
                                        <option value="">----- 選択してください -----</option>
                                        <?php
                                        foreach($distributor as $key){
                                            $selected = ($input["distributorId"] === $key["distributorId"]) ? "selected" : "";
                                            echo "<option value='" . html($key["distributorId"]) . "' " . $selected . ">" . html($key["distributorName"]) . "</option>\n";
                                        }
                                        ?>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-red-500"><?php echo html($val['distributorId']['message']) ?></span>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="distributorMCode"
                                label="卸業者管理コード"
                                title="卸業者管理コード"
                                errorMessage="<?= ($is_error)? $val['distributorMCode']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    卸業者管理コード
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="distributorMCode" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['distributorMCode']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['distributorMCode']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['distributorMCode']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="price"
                                :rules="{ required: true, }"
                                label="購買価格"
                                title="購買価格"
                                errorMessage="<?= ($is_error)? $val['price']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    購買価格 <span class="bg-red-400 text-white text-md font-medium inline-flex items-center px-2.5 rounded">必須</span>
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="price" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['price']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['price']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['price']['message']) ?></span>
                                </div>
                            </div>
                            <div class="cf">
<!-- 
                                <v-input
                                type="number"
                                name="unitPrice"
                                label="単価"
                                title="単価"
                                errorMessage="<?= ($is_error)? $val['unitPrice']['message'] : '' ?>"
                                ></v-input>
                                <button type="button" @click.native="getUnitPrice" class="
                                disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none
                                bg-white hover:border-gray-400 text-gray-700 py-2 px-4 border border-gray-300">
                                    単価を自動計算
                                </button>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    単価
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="unitPrice" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['unitPrice']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['unitPrice']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['unitPrice']['message']) ?></span>
                                    <button type="button" onclick="getUnitPrice()" class="
                                    disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none
                                    bg-white hover:border-gray-400 text-gray-700 py-2 px-4 border border-gray-300">
                                        単価を自動計算
                                    </button>
                                    <script>
                                        function getUnitPrice(){
                                            let price = document.getElementsByName('price')[0].value;
                                            let quantity = document.getElementsByName('quantity')[0].value;

                                            let unitPrice = 0;
                                            if (price == "" || price == 0) {
                                                unitPrice = 0
                                            }
                                            if (quantity == "" || quantity == 0) {
                                                unitPrice = 0
                                            }
                                            unitPrice = (price / quantity);

                                            document.getElementsByName('unitPrice')[0].value = unitPrice;
                                        }
                                    </script>
                                </div>
                            </div>
                            <div class="cf" id="medicineCategory">
<!-- 
                                <v-textarea
                                name="medicineCategory"
                                label="保険請求分類（医科）"
                                title="保険請求分類（医科）"
                                errorMessage="<?= ($is_error)? $val['medicineCategory']['message'] : '' ?>"
                                ></v-textarea>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    保険請求分類（医科）
                                </div>
                                <div class="relative">
                                    <textarea
                                        name="medicineCategory"
                                        class="appearance-none w-full py-2 px-3 leading-tight h-32 text-left flex-initial bg-white border 
                                        <?= ($is_error && $val['medicineCategory']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                        onkeydown="countText(this, 'mediCat')"
                                    ><?=$val['medicineCategory']['value'];?></textarea>
                                    <span class="absolute bottom-4 right-6"><span id="mediCat">0</span>文字</span>
                                </div>
                                <span class="text-red-500"><?php echo html($val['medicineCategory']['message']) ?></span>
                            </div>
                            <div class="cf">
<!-- 
                                <v-textarea
                                name="homeCategory"
                                label="保険請求分類（在宅）"
                                title="保険請求分類（在宅）"
                                errorMessage="<?= ($is_error)? $val['homeCategory']['message'] : '' ?>"
                                ></v-textarea>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    保険請求分類（在宅）
                                </div>
                                <div class="relative">
                                    <textarea
                                        name="homeCategory"
                                        class="appearance-none w-full py-2 px-3 leading-tight h-32 text-left flex-initial bg-white border 
                                        <?= ($is_error && $val['homeCategory']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                        onkeydown="countText(this, 'homeCat')"
                                    ><?=$val['homeCategory']['value'];?></textarea>
                                    <span class="absolute bottom-4 right-6"><span id="homeCat">0</span>文字</span>
                                </div>
                                <span class="text-red-500"><?php echo html($val['homeCategory']['message']) ?></span>
                            </div>
                            <dl class="cf">
<!-- 
                                <v-input
                                type="text"
                                name="measuringInst"
                                label="測定機器名"
                                title="測定機器名"
                                errorMessage="<?= ($is_error)? $val['measuringInst']['message'] : '' ?>"
                                ></v-input>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    測定機器名
                                </div>
                                <div class="flex-auto">
                                    <input type="text" name="measuringInst" class="appearance-none w-full py-2 px-3 leading-tight text-left flex-initial bg-white border 
                                    <?= ($is_error && $val['measuringInst']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                    value="<?php echo html($val['measuringInst']['value']) ?>">
                                    <span class="text-red-500"><?php echo html($val['measuringInst']['message']) ?></span>
                                </div>

                            </dl>
                            <dl class="cf">
<!-- 
                                <v-textarea
                                name="notice"
                                label="特記事項"
                                title="特記事項"
                                errorMessage="<?= ($is_error)? $val['notice']['message'] : '' ?>"
                                ></v-textarea>
 -->
                                <div class="flex-initial lg:w-1/6 w-auto lg:whitespace-pre whitespace-normal">
                                    特記事項
                                </div>
                                <div class="relative">
                                    <textarea
                                        name="notice"
                                        class="appearance-none w-full py-2 px-3 leading-tight h-32 text-left flex-initial bg-white border 
                                        <?= ($is_error && $val['notice']['message']) ? 'text-red-500 border-red-500' : 'text-gray-700 border-gray-300' ?>" 
                                        onkeydown="countText(this, 'noticeCount')"
                                    ><?=$val['notice']['value'];?></textarea>
                                    <span class="absolute bottom-4 right-6"><span id="noticeCount">0</span>文字</span>
                                </div>
                                <span class="text-red-500"><?php echo html($val['notice']['message']) ?></span>
                            </dl>
                        </div>
                        <div class="text-center py-1">
                            <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit" value="確認">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function countText(elm, id){
        document.getElementById(id).innerHTML = elm.value.length;
    }
</script>
<script>
var JoyPlaApp = Vue
    .createApp({
        setup() {
            const {ref, onCreated, onMounted} = Vue;
            const loading = ref(false);
            const start = () => {
                loading.value = true;
            }

            const complete = () => {
                loading.value = false;
            }

            const sleepComplate = () => {
                window.setTimeout(function () {
                    complete();
                }, 500);
            }
            start();

            onMounted(() => {
                sleepComplate()
            });

            const { useForm } = VeeValidate;
            const { handleSubmit , control, meta , validate , values , isSubmitting  } = useForm({
                initialValues: {
                },
                validateOnMount : false
            });
/* 
            const onSubmit = async (event) => {
                const { valid, errors } = await validate();
                if(!valid){
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '入力エラーがございます。ご確認ください',
                    })
                    event.preventDefault();
                }else{
                    document.getElementsByName("regForm")[0].submit();
                }
            };
 */
            const breadcrumbs = [
            {
                text: '商品メニュー',
                disabled: false,
                href: _ROOT + '&path=/product',
            },
            {
                text: '商品・金額・院内商品情報登録',
                disabled: true, 
            }
            ];

/*
            async () =>{
                const { valid, errors } = await validate();
                if(!valid){
                    Swal.fire({
                        icon: 'error',
                        title: '入力エラー',
                        text: '入力エラーがございます。ご確認ください',
                    });
                }

                return handleSubmit((values, actions) => {
                    // Send data to API
                    alert(JSON.stringify(values, null, 2));
                });
            };*/

            return {
                loading, 
                start, 
                complete , 
                breadcrumbs, 
            }
        },
        components: {
            'v-checkbox': vCheckbox,
            'v-loading': vLoading,
            'v-text': vText,
            'v-input' : vInput,
            'v-textarea' : vTextarea,
            'v-select': vSelect,
            'v-checkbox': vCheckbox,
            'v-button-default' : vButtonDefault,
            'v-button-primary' : vButtonPrimary,
            'v-breadcrumbs': vBreadcrumbs,
            'header-navi': headerNavi
        }
    })
    .mount('#top');
</script>