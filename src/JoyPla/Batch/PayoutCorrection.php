<?php

namespace JoyPla\Batch;

use framework\Batch\BatchJob;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class PayoutCorrection extends BatchJob
{
    public function handle()
    {
        for ($count = 0; $count < 5; $count++) {
            $data = ModelRepository::getPayoutInstance()
                ->where('payoutDate', '', 'ISNULL')
                ->orderBy('id', 'asc')
                ->paginate(1000);

            if ($data->getTotal() === 0) {
                break;
            }

            $update = [];
            foreach( $data->getData()->all() as $data){
                $update[] = [
                    'payoutHistoryId' => $data->payoutHistoryId,
                    'payoutDate' => $data->registrationTime,
                ];
            }

            ModelRepository::getPayoutInstance()->updateBulk(
                'payoutHistoryId',
                $update
            );
        }
    }
}
