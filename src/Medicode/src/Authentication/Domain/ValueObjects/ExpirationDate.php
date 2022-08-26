<?php

declare(strict_types=1);

namespace Medicode\Authentication\Domain\ValueObjects;

class ExpirationDate
{
    /**
     * @var string
     * SPIRALアカウントに固有のメディコード認証用アクセストークン有効期限
     */
    private string $expirationDate = '';
    
    /**
     * ExpirationDate constructor.
     * @param string $expirationDate format = Y/m/d H:i:s
     */
    public function __construct(string $expirationDate = '')
    {
        if ($expirationDate) {
            
            if (strpos($expirationDate, '年') !== false)
            {
                $expirationDate = $this->getDateTime($expirationDate);
            }
            
            if (strtotime(date('Y/m/d H:i:s')) < strtotime($expirationDate)) {
                $this->expirationDate = $expirationDate;
            }
        }
    }
    
    
    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->expirationDate;
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
