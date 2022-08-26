<?php

declare(strict_types=1);

namespace Medicode\Authentication\Domain\ValueObjects;

use Medicode\Shared\Exceptions\ValidationException;

class MedicodeApiId
{
    /**
     * @var string
     * SPIRALアカウントに固有のメディコード認証用医療機関コード
     */
    private string $medicodeApiId;
    
    /**
     * MedicodeApiId constructor.
     * @param string $medicodeApiId
     */
    public function __construct(string $medicodeApiId)
    {
        
        if (!$medicodeApiId) {
            throw new ValidationException('medicodeApiId is required.', 208);
        }
        
        if (!preg_match('/^([0-9]{10})$/', $medicodeApiId)) {
            throw new ValidationException('medicodeApiId is invalid format.', 201);
        }
        
        $this->medicodeApiId = $medicodeApiId;
    }
    
    
    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->medicodeApiId;
    }
}
