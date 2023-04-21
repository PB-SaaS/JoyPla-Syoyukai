<?php

namespace JoyPla\Batch;

use framework\Batch\BatchJob;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class ReservationPriceBatch extends BatchJob
{
    public function handle()
    {
        for ($count = 0; $count < 5; $count++) {
            $data = ModelRepository::getReservationPriceViewInstance()
                ->where('reservationTime', date('Y-m-d H:i:s'), '<=')
                ->where('isActive', 't')
                ->orderBy('id', 'asc')
                ->paginate(1000);

            if ($data->getTotal() === 0) {
                break;
            }

            $insert = array_map(function ($record) {
                return [
                    'registrationTime' => 'now',
                    'priceId' => $record->priceId,
                    'itemId' => $record->itemId,
                    'itemsAuthKey' => $record->authKey,
                    'hospitalId' => $record->hospitalId,
                    'distributorId' => $record->distributorId,
                    'quantity' => $record->quantity,
                    'quantityUnit' => $record->quantityUnit,
                    'itemUnit' => $record->itemUnit,
                    'price' => $record->price,
                    'notice' => $record->notice,
                    'unitPrice' => $record->unitPrice,
                    'distributorMCode' => $record->distributorMCode,
                ];
            }, $data->getData()->all());

            ModelRepository::getPriceUpsertTransactionInstance()->insert(
                $insert
            );

            $instance = ModelRepository::getReservationPriceInstance()
                ->where('reservationTime', date('Y-m-d H:i:s'), '<=')
                ->where('isActive', 't');

            foreach ($data->getData()->all() as $item) {
                $instance->orWhere('recordId', $item->id);
            }

            $instance->update([
                'updateTime' => 'now',
                'isActive' => 'f',
            ]);
        }
    }
}
