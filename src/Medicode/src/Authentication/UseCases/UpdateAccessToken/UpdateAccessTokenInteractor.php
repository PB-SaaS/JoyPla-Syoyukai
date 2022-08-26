<?php

declare(strict_types=1);

namespace Medicode\Authentication\UseCases\UpdateAccessToken;

use Medicode\Authentication\Domain\Authentication;
use Medicode\Authentication\Domain\ValueObjects\MedicodeApiId;
use Medicode\Authentication\Domain\ValueObjects\Password;
use Medicode\Authentication\UseCases\Contracts\IMedicodeAuthenticationRepository;
use Medicode\Authentication\UseCases\Contracts\ISPIRALAuthenticationRepository;
use Medicode\Authentication\UseCases\UpdateAccessToken\IUpdateAccessToken;

class UpdateAccessTokenInteractor implements IUpdateAccessToken
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
     * @param MedicodeApiId $medicodeApiId
     * @param Password $password
     * @return Authentication
     */
    public function handle(MedicodeApiId $medicodeApiId, Password $password): Authentication
    {
        list($accessToken, $expirationDate) = $this->medicodeAuthenticationRepository->get($medicodeApiId, $password);
        
        $authentication = new Authentication($medicodeApiId, $password, $accessToken, $expirationDate);
        
        $this->spiralAuthenticationRepository->update($authentication);
        
        return $authentication;
    }
}
