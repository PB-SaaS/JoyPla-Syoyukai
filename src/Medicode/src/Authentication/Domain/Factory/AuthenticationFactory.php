<?php

declare(strict_types=1);

namespace Medicode\Authentication\Domain\Factory;

use Medicode\Authentication\Domain\Authentication;
use Medicode\Authentication\Domain\ValueObjects\AccessToken;
use Medicode\Authentication\Domain\ValueObjects\ExpirationDate;
use Medicode\Authentication\Domain\ValueObjects\MedicodeApiId;
use Medicode\Authentication\Domain\ValueObjects\Password;

final class AuthenticationFactory
{
    /**
     * @param array $values
     * @return Authentication
     */
    public static function create(array $values = []): Authentication
    {
        $medicodeApiId = '';
        $password = '';
        $accessToken = '';
        $expirationDate = '';
        
        if (array_key_exists('medicodeApiId', $values)) {
            $medicodeApiId = new MedicodeApiId(trim((string)$values['medicodeApiId']));
        }
        
        if (array_key_exists('medicodePW', $values)) {
            $password = new Password(trim((string)$values['medicodePW']));
        }
        
        if (array_key_exists('medicodeToken', $values)) {
            $accessToken = new AccessToken((string)$values['medicodeToken']);
        }
        
        if (array_key_exists('expirationDate', $values)) {
            $expirationDate = new ExpirationDate((string)$values['expirationDate']);
        }
        
        return new Authentication($medicodeApiId, $password, $accessToken, $expirationDate);
    }
}
