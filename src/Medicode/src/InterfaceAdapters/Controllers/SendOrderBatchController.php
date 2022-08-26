<?php

declare(strict_types=1);

namespace Medicode\InterfaceAdapters\Controllers;

use Medicode\Authentication\UseCases\GetAuthentication\IGetAuthentication;
use Medicode\Authentication\UseCases\UpdateAccessToken\IUpdateAccessToken;
use Medicode\Order\UseCases\SendOrder\ISendOrder;
use Medicode\Authentication\Domain\Authentication;

final class SendOrderBatchController
{
    
    private IGetAuthentication $getAuthInteractor;
    private IUpdateAccessToken $updateTokenInteractor;
    private ISendOrder $sendOrderInteractor;
    
    public function __construct(
        IGetAuthentication $getAuthInteractor,
        IUpdateAccessToken $updateTokenInteractor,
        ISendOrder $sendOrderInteractor
    ) {
        $this->getAuthInteractor = $getAuthInteractor;
        $this->updateTokenInteractor = $updateTokenInteractor;
        $this->sendOrderInteractor = $sendOrderInteractor;
    }
    
    
    /**
     * @return array
     */
    public function index(): array
    {
        $response = [];
        $authentication = $this->getAuthInteractor->handle();
        $response = $this->sendOrderInteractor->handle($authentication->getAccessToken());
        
        if ($response['code'] === 991)
        {
            $response = [];
            $newAuthentication = $this->updateTokenInteractor->handle($authentication->getMedicodeApiId(), $authentication->getPassword());
            $response = $this->sendOrderInteractor->handle($newAuthentication->getAccessToken());
        }
        
        return $response;
    }
}