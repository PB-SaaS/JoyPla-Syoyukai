<?php

declare(strict_types=1);

require_once "Medicode/autoload.php";
require_once "Medicode/public/function.php";

use Exception;
use Medicode\Shared\Exceptions\IAppException;
use Medicode\Shared\Exceptions\ApiException;
use Medicode\Shared\Exceptions\ValidationException;
use Medicode\Authentication\UseCases\GetAuthentication\GetAuthenticationInteractor;
use Medicode\Authentication\UseCases\UpdateAccessToken\UpdateAccessTokenInteractor;
use Medicode\Order\UseCases\SendOrder\SendOrderInteractor;
use Medicode\Authentication\Adapters\Repository\MedicodeAuthenticationRepository;
use Medicode\Authentication\Adapters\Repository\SPIRALAuthenticationRepository;
use Medicode\Order\Adapters\Repository\OrderRepository;
use Medicode\InterfaceAdapters\Controllers\SendOrderBatchController;

global $SPIRAL;
$errors = [];
$logs = [];

try {
    
    $action = new SendOrderBatchController(
        new GetAuthenticationInteractor(new MedicodeAuthenticationRepository(), new SPIRALAuthenticationRepository()),
        new UpdateAccessTokenInteractor(new MedicodeAuthenticationRepository(), new SPIRALAuthenticationRepository()),
        new SendOrderInteractor(new OrderRepository()));
    $result = $action->index();
    
} catch (IAppException $ex) {
    
    $errors[] = [
        'now',
        date("Y-m-d H:i:s") . "." . substr(explode(".", (microtime(true) . ".000"))[1], 0, 3),
        $ex->getCode(),
        $ex->getMessage()."\n".'[ '.$ex->getFile().' line: '.$ex->getLine().' ]'
    ];
    
} catch (Exception $e) {
    
    echo $e;
    
} finally {
    
    $ok = 0;
    $ng = 0;
    $total = ($result['data']) ? $result['data']->count() : 0;
    
    if ($result['data']) {
        foreach ($result['data'] as $order) {
            if ($order->getIsValid()) {
                $ok++;
            }
            if (!$order->getIsValid()) {
                $ng++;
            }
        }
    }
    
    if ($ok > 0 || $result['code'] === 0)
    {
        $logs[] = [
            'now',
            '',
            $result['code'],
            $result['message']."\n Total = ".$total." Success = ".$ok." Error = ".$ng
        ];
    }
    
    if ($ng > 0)
    {
        $errors[] = [
            'now',
            date("Y-m-d H:i:s") . "." . substr(explode(".", (microtime(true) . ".000"))[1], 0, 3),
            600,
            "対象の発注データにフォーマットエラーのものがあります。"."\n"."■フォーマットエラー件数: {$ng}/{$total}"
        ];
    }
    
    if (count($errors) > 0)
    {
        $logs = array_merge($logs, $errors);
        notifyError($errors);
    }
    
    bulkInsertLog($logs);
    
    echo "results..."."\n";
    foreach ($logs as $log)
    {
        echo "code: ".$log[2]."\n".$log[3]."\n\n";
    }
}
