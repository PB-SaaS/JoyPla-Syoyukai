<?php

declare(strict_types=1);

namespace Medicode\Authentication\Adapters\Repository;

use Medicode\Shared\Exceptions\ApiException;
use Medicode\Authentication\Domain\ValueObjects\MedicodeApiId;
use Medicode\Authentication\Domain\ValueObjects\Password;
use Medicode\Authentication\Domain\ValueObjects\AccessToken;
use Medicode\Authentication\Domain\ValueObjects\ExpirationDate;
use Medicode\Authentication\UseCases\Contracts\IMedicodeAuthenticationRepository;

class MedicodeAuthenticationRepository implements IMedicodeAuthenticationRepository
{
    /**
     * @param MedicodeApiId $medicodeApiId
     * @param Password $password
     * @return array
     */
    public function get(MedicodeApiId $medicodeApiId, Password $password): array
    {
        $responseHeader = [];
        $response = '';
        $status = 0;
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, AUTHAPI_URL);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, SSL_CIPHER_LIST);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-API-Id: '.$medicodeApiId->getValue(), 'X-API-Password: '.$password->getValue()]);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        $info = curl_getinfo($curl);
        
        curl_close($curl);
        
        if (!$response)
        {
            throw new ApiException('メディコード認証APIの実行に失敗しました。', 806);
        }
        
        if ($httpCode !== 200) {
            throw new ApiException($errno.': '.$error, $httpCode);
        }
        
        $header = substr($response, 0, $info['header_size']);
        $body = substr($response, $info['header_size']);
        $responseHeader = $this->getHeaderArray($header);
        $status = (int)$responseHeader['X-API-Status'];
        
        if ($status !== 200)
        {
            throw new ApiException('メディコード認証APIの実行に失敗しました。', $status);
        }
        
        $responseBody = json_decode($body);
        
        if (!$responseBody->accessToken || !$responseBody->expirationDate)
        {
            throw new ApiException('メディコード認証APIレスポンスから値を取得できませんでした。', 800);
        }
        
        $accessToken = new AccessToken($responseBody->accessToken);
        $expirationDate = new ExpirationDate($responseBody->expirationDate);
        
        return [$accessToken, $expirationDate];
    }
    
    
    /**
     * @param string $header
     * @return array
     */
    private function getHeaderArray(string $header): array
    {
        $headerArray = [];
        $_header = str_replace("\r", '', $header);
        $tmp_header = explode("\n", $_header);

        foreach ($tmp_header as $row) {
            $tmp = explode(': ', $row);
            $key = trim($tmp[0]);
            if ($key === '') {
                continue;
            }
            $value = str_replace($key.': ', '', $row);
            $headerArray[$key] = trim($value);
        }
        return $headerArray;
    }
}
