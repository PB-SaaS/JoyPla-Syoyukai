<?php

declare(strict_types=1);

namespace Medicode\Authentication\Domain;

use Medicode\Authentication\Domain\ValueObjects\AccessToken;
use Medicode\Authentication\Domain\ValueObjects\ExpirationDate;
use Medicode\Authentication\Domain\ValueObjects\MedicodeApiId;
use Medicode\Authentication\Domain\ValueObjects\Password;

class Authentication
{
    /**
     * @var MedicodeApiId
     */
    private MedicodeApiId $medicodeApiId;
    
    /**
     * @var Password
     */
    private Password $password;
    
    /**
     * @var AccessToken
     */
    private AccessToken $accessToken;
    
    /**
     * @var ExpirationDate
     */
    private ExpirationDate $expirationDate;
    
    /**
     * AuthenticationData constructor.
     * @param MedicodeApiId $medicodeApiId
     * @param Password $password
     * @param AccessToken $accessToken
     * @param ExpirationDate $expirationDate
     */
    public function __construct(
        MedicodeApiId $medicodeApiId,
        Password $password,
        AccessToken $accessToken,
        ExpirationDate $expirationDate
    ) {
        $this->medicodeApiId = $medicodeApiId;
        $this->password = $password;
        $this->accessToken = $accessToken;
        $this->expirationDate = $expirationDate;
    }
    
    
    /**
     * @return MedicodeApiId
     */
    public function getMedicodeApiId(): MedicodeApiId
    {
        return $this->medicodeApiId;
    }
    
    
    /**
     * @return Password
     */
    public function getPassword(): Password
    {
        return $this->password;
    }
    
    
    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }
    
    
    /**
     *  @return ExpirationDate
     */
    public function getExpirationDate(): ExpirationDate
    {
        return $this->expirationDate;
    }
    
    
    /**
     * @param AccessToken $accessToken
     * @return Authentication
     */
    public function setAccessToken(AccessToken $accessToken): Authentication
    {
        $this->accessToken = $accessToken;
        return $this;
    }
    
    
    /**
     * @param ExpirationDate $expirationDate
     * @return Authentication
     */
    public function setExpirationDate(ExpirationDate $expirationDate): Authentication
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }
    
    
    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $date = $this->expirationDate->getValue();
        
        if (!$this->accessToken->getValue() || !$date) {
            return false;
        }
        
        if (strpos($date, '年') !== false)
        {
            $date = $this->getDateTime($date);
        }
        
        if (strtotime($date) < strtotime(date('Y/m/d H:i:s'))) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param string $date
     * @return string
     */
    private function getDateTime(string $date): string
    {
        $timestamp = date_create_from_format('Y年m月d日 H時i分s秒', $date)->getTimestamp();
        return date('Y/m/d H:i:s', $timestamp);
    }
}
