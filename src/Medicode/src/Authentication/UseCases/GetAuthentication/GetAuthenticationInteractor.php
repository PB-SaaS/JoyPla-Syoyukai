<?php

declare(strict_types=1);

namespace Medicode\Authentication\UseCases\GetAuthentication;

use Medicode\Authentication\Domain\Authentication;
use Medicode\Authentication\UseCases\Contracts\IMedicodeAuthenticationRepository;
use Medicode\Authentication\UseCases\Contracts\ISPIRALAuthenticationRepository;
use Medicode\Authentication\UseCases\GetAuthentication\IGetAuthentication;

class GetAuthenticationInteractor implements IGetAuthentication
{
    
    private IMedicodeAuthenticationRepository $medicodeAuthenticationRepository;
    private ISPIRALAuthenticationRepository $spiralAuthenticationRepository;
    
    /**
     * AuthenticateInteractor constructor.
     * @param IMedicodeAuthenticationRepository $medicodeAuthenticationRepository
     * @param ISPIRALAuthenticationRepository $spiralAuthenticationRepository
     */
    public function __construct(
        IMedicodeAuthenticationRepository $medicodeAuthenticationRepository,
        ISPIRALAuthenticationRepository $spiralAuthenticationRepository
    ) {
        $this->medicodeAuthenticationRepository = $medicodeAuthenticationRepository;
        $this->spiralAuthenticationRepository = $spiralAuthenticationRepository;
    }
    
    
    /**
     * @return Authentication
     */
    public function handle(): Authentication
    {
        $authentication = $this->spiralAuthenticationRepository->get();
        
        if (!$authentication->isValid())
        {
            list($accessToken, $expirationDate) = $this->medicodeAuthenticationRepository->get($authentication->getMedicodeApiId(), $authentication->getPassword());
            $authentication->setAccessToken($accessToken);
            $authentication->setExpirationDate($expirationDate);
            $this->spiralAuthenticationRepository->update($authentication);
        }
        
        return $authentication;
    }
}
