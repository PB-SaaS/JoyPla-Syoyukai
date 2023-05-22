<?php

require_once 'src/framework/Exception/ClassNotFoundException.php';
require_once 'src/framework/Exception/DependencyResolveFailedException.php';
require_once 'src/framework/Exception/ExceptionHandler.php';
require_once 'src/framework/Exception/NotFoundException.php';
require_once 'src/framework/Facades/Gate.php';
require_once 'src/framework/Facades/GateDefine.php';
require_once 'src/framework/Facades/GateInterface.php';
require_once 'src/framework/Http/Controller/Controller.php';
require_once 'src/framework/Http/Kernel.php';
require_once 'src/framework/Http/Middleware/Middleware.php';
require_once 'src/framework/Http/Middleware/MiddlewareInterface.php';
require_once 'src/framework/Http/Middleware/MiddlewareTrait.php';
require_once 'src/framework/Http/Middleware/VerifyCsrfTokenMiddleware.php';
require_once 'src/framework/Http/Request/HttpRequest.php';
require_once 'src/framework/Http/Request/HttpRequestParameter.php';
require_once 'src/framework/Http/Request/Request.php';
require_once 'src/framework/Http/Response/ApiResponse.php';
require_once 'src/framework/Http/Response/Response.php';
require_once 'src/framework/Http/Session/Session.php';
require_once 'src/framework/Http/View.php';
require_once 'src/framework/Routing/Route.php';
require_once 'src/framework/Routing/Router.php';
require_once 'src/framework/Core/ApiSpiral.php';
require_once 'src/framework/Core/Auth.php';
require_once 'src/framework/Core/Collection.php';
require_once 'src/framework/Core/Config.php';
require_once 'src/framework/Core/Csrf.php';
require_once 'src/framework/Core/Func.php';
require_once 'src/framework/Core/Logger.php';
require_once 'src/framework/Core/SpiralArea.php';
require_once 'src/framework/Core/SpiralDBFilter.php';
require_once 'src/framework/Core/SpiralDataBase.php';
require_once 'src/framework/Core/SpiralORM.php';
require_once 'src/framework/Core/SpiralSendMail.php';
require_once 'src/framework/Core/SpiralTable.php';
require_once 'src/framework/Core/Util.php';
require_once 'src/framework/Library/BladeLikeEngine/BladeLikeView.php';
require_once 'src/framework/Library/BladeLikeEngine/BladeOne.php';
require_once 'src/framework/Library/BladeLikeEngine/BladeOneCustom.php';
require_once 'src/framework/Library/SiDateTime/HolidayConfig.php';
require_once 'src/framework/Library/SiDateTime/SiDateTime.php';
require_once 'src/framework/Library/SiValidator/SiRuleInterface.php';
require_once 'src/framework/Library/SiValidator/SiValidator.php';
require_once 'src/framework/Library/SiValidator/SiValidatorDefineRule.php';
require_once 'src/framework/Batch/BatchJob.php';
require_once 'src/framework/Batch/BatchScheduler.php';
require_once 'src/framework/SpiralConnecter/SpiralConnecterInterface.php';
require_once 'src/framework/SpiralConnecter/SpiralConnecter.php';
require_once 'src/framework/SpiralConnecter/SpiralDB.php';
require_once 'src/framework/SpiralConnecter/SpiralManager.php';
require_once 'src/framework/SpiralConnecter/SpiralExpressManager.php';
require_once 'src/framework/SpiralConnecter/Paginator.php';
require_once 'src/framework/SpiralConnecter/SpiralApiConnecter.php';
require_once 'src/framework/Application.php';

use framework\Library\SiValidator;
use framework\SpiralConnecter\SpiralDB;
use framework\SpiralConnecter\SpiralManager;

// assertを有効にし、出力を抑制する
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);

SpiralDB::setToken(
    '00011BfiY3b78290dd5fb1d4239f583e9f4506bc811ed9238b80',
    '691019e4cf839065209ef1a548a1d3dac95126c3'
);

$values = ['name' => 'John Doe'];
$rules = ['name' => ['maxword:10']];
$labels = ['name' => '名前'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['name']['result']);
var_dump($results['name']['message']);

// not_regex:patternルールのテスト
$values = ['email' => 'john@example.com'];
$rules = ['email' => ['not_regex:/^admin/']];
$labels = ['email' => 'メールアドレス'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['email']['result']);
var_dump($results['email']['message']);

$values = ['email' => 'admin@example.com'];
$rules = ['email' => ['not_regex:/^admin/']];
$labels = ['email' => 'メールアドレス'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['email']['result']);
var_dump($results['email']['message']);

// regex:patternルールのテスト
$values = ['email' => 'john@example.com'];
$rules = ['email' => ['regex:/^[a-z]+@[a-z]+\.[a-z]+$/']];
$labels = ['email' => 'メールアドレス'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());

$values = ['email' => 'johndoe'];
$rules = ['email' => ['regex:/^[a-z]+@[a-z]+\.[a-z]+$/']];
$labels = ['email' => 'メールアドレス'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['email']['result']);
var_dump($results['email']['message']);

// size:valueルールのテスト
$values = ['password' => '1234'];
$rules = ['password' => ['size:4']];
$labels = ['password' => 'パスワード'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());

$values = ['password' => 'password'];
$rules = ['password' => ['size:8']];
$labels = ['password' => 'パスワード'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['password']['result']);
var_dump($results['password']['message']);

// stringルールのテスト
$values = ['name' => '山田太郎'];
$rules = ['name' => ['string']];
$labels = ['name' => '氏名'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());

$values = ['name' => 12345];
$rules = ['name' => ['string']];
$labels = ['name' => '氏名'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['name']['result']);
var_dump($results['name']['message']);

// timezoneルールのテスト
$values = ['timezone' => 'Asia/Tokyo'];
$rules = ['timezone' => ['timezone']];
$labels = ['timezone' => 'タイムゾーン'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());

$values = ['timezone' => 'Invalid/Timezone'];
$rules = ['timezone' => ['timezone']];
$labels = ['timezone' => 'タイムゾーン'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['timezone']['result']);
var_dump($results['timezone']['message']);

$values = ['email' => 'test@example.com'];
$rules = ['email' => ['unique:users,email']];
$labels = ['email' => 'メールアドレス'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());

// テストデータを登録
SpiralDB::title('users')->insert([['email' => 'test@example.com']]);

$values = ['email' => 'test@example.com'];
$rules = ['email' => ['unique:users,email']];
$labels = ['email' => 'メールアドレス'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['email']['result']);
var_dump($results['email']['message']);

// urlルールのテスト
$values = ['url' => 'https://example.com'];
$rules = ['url' => ['url']];
$labels = ['url' => 'URL'];
$validator = SiValidator::make($values, $rules, $labels);

// not_regex:patternルールのテスト
$values = ['phone' => '09012345678'];
$rules = ['phone' => ['not_regex:/[^\d]/']];
$labels = ['phone' => '電話番号'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());

$values = ['phone' => '090-1234-5678'];
$rules = ['phone' => ['not_regex:/[^\d]/']];
$labels = ['phone' => '電話番号'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['phone']['result']);
var_dump($results['phone']['message']);

// regex:patternルールのテスト
$values = ['postal_code' => '123-4567'];
$rules = ['postal_code' => ['regex:/^[0-9]{3}-[0-9]{4}$/']];
$labels = ['postal_code' => '郵便番号'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());

$values = ['postal_code' => '12-34567'];
$rules = ['postal_code' => ['regex:/^[0-9]{3}-[0-9]{4}$/']];
$labels = ['postal_code' => '郵便番号'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['postal_code']['result']);
var_dump($results['postal_code']['message']);

// size:valueルールのテスト
$values = ['size' => '12.34'];
$rules = ['size' => ['size:5']];
$labels = ['size' => 'サイズ'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());

$values = ['size' => '1234'];
$rules = ['size' => ['size:5']];
$labels = ['size' => 'サイズ'];
$validator = SiValidator::make($values, $rules, $labels);
$results = $validator->getResults();
var_dump($validator->isError());
var_dump($results['size']['result']);
var_dump($results['size']['message']);
