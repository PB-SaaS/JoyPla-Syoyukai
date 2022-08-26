<?php

declare(strict_types=1);

namespace Medicode\Authentication\Domain\ValueObjects;

use Medicode\Shared\Exceptions\ValidationException;

class Password
{
    /**
     * @var string
     * SPIRALアカウントに固有のメディコード認証用パスワード
     */
    private string $password;
    
    /**
     * PAssword constructor.
     * @param string $password
     */
    public function __construct(string $password)
    {
        if (!$password) {
            throw new ValidationException('medicodeApi password is required.', 208);
        }
        
        if (strlen($password) > 11) {
            throw new ValidationException('medicodeApi password is invalid format.', 201);
        }
        
        $this->password = $password;
    }
    
    
    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->password;
    }
}
