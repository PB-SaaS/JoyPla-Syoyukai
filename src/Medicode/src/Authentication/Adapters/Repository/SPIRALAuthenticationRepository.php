<?php

declare(strict_types=1);

namespace Medicode\Authentication\Adapters\Repository;

use SpiralApiRequest;
use Medicode\Shared\Exceptions\ApiException;
use Medicode\Authentication\Domain\Authentication;
use Medicode\Authentication\Domain\Factory\AuthenticationFactory;
use Medicode\Authentication\UseCases\Contracts\ISPIRALAuthenticationRepository;

class SPIRALAuthenticationRepository implements ISPIRALAuthenticationRepository
{
    /**
     * @return Authentication
     */
    public function get(): Authentication
    {
        global $SPIRAL;
        $record = [];
        $param = [
            'db_title' => SETTING_DB,
            'select_columns' => ['medicodeApiId', 'medicodePW', 'medicodeToken', 'expirationDate'],
            'sort' => [['name' => 'registrationTime', 'order' => 'asc']],
            'search_condition' => [
                ['name' => 'isUsed', 'value' => 1],
                ['name' => 'medicodeApiId', 'value' => '', 'operator' => 'ISNOTNULL'],
                ['name' => 'medicodePW', 'value' => '', 'operator' => 'ISNOTNULL']
            ]
        ];
        
        $result = $this->api('database', 'select', $param);
        
        $code = (int)$result->get('code');
        if ($code !== 0) {
            throw new ApiException($result->get('message'), $code);
        }
        
        if ($code === 0) {
            $count = (int)$result->get('count');
            if ($count === 0) {
                throw new ApiException('使用できる認証データが登録されていません。', 999);
            }
            if ($count > 1) {
                throw new ApiException('使用できる認証データが複数登録されています。', 999);
            }
            $records = $result->get('data');
        }
        
        $columns = count($param['select_columns']);
        for ($i = 0; $i < $columns; $i++)
        {
            $record[$param['select_columns'][$i]] = $records[0][$i];
        }
        
        return AuthenticationFactory::create($record);
    }
    
    
    /**
     * @param Authentication $authentication
     */
    public function update(Authentication $authentication): void
    {
        global $SPIRAL;
        $param = [
            'db_title' => SETTING_DB,
            'search_condition' => [['name' => 'medicodeApiId', 'value' => $authentication->getMedicodeApiId()->getValue()]],
            'data' => [['name' => 'updateTime', 'value' => 'now'],
                       ['name' => 'medicodeToken', 'value' => $authentication->getAccessToken()->getValue()],
                       ['name' => 'expirationDate', 'value' => $authentication->getExpirationDate()->getValue()]]
        ];
        
        $result = $this->api('database', 'update', $param);
        
        $code = (int)$result->get('code');
        if ($code !== 0) {
            throw new ApiException($result->get('message'), $code);
        }
    }
    
    
    private function api($_app, $_method, $_params)
    {
        global $SPIRAL;
        $communicator = $SPIRAL->getSpiralApiCommunicator();
        $request = new SpiralApiRequest();
        $request->putAll($_params);
        return $communicator->request($_app, $_method, $request);
    }
}
