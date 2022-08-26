<?php

declare(strict_types=1);

namespace Medicode\Authentication\Domain\ValueObjects;

class AccessToken
{
    /**
     * @var string
     * SPIRALアカウントに固有のメディコード認証用アクセストークン
     */
    private string $accessToken = '';
    
    /**
     * AccessToken constructor.
     * @param string $accessToken
     */
    public function __construct(string $accessToken = '')
    {
        $this->accessToken = $accessToken;
    }
    
    
    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->accessToken;
    }
}
