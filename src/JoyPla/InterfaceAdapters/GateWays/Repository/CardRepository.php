<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\Card;
use JoyPla\Enterprise\Models\CardId;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Lot;
use JoyPla\Enterprise\Models\LotDate;
use JoyPla\Enterprise\Models\LotNumber;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class CardRepository implements CardRepositoryInterface
{
    public function getCards(HospitalId $hospitalId, array $cardIds)
    {
        $instance = ModelRepository::getCardViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->value('cardId');

        if (count($cardIds) === 0) {
            return [];
        }

        $cardIds = array_map(function (CardId $id) use ($instance) {
            $instance->orWhere('cardId', $id->value());
            return $id;
        }, $cardIds);

        return array_map(function ($item) {
            return new Card(
                new CardId($item->cardId),
                new InHospitalItemId($item->inHospitalItemId),
                new Lot(
                    new LotNumber($item->lotNumber),
                    new LotDate($item->lotDate)
                ),
                new HospitalId($item->hospitalId),
                new DivisionId($item->divisionId),
                Quantity::create($item)
            );
        }, $instance->get()->all());
    }

    public function update(HospitalId $hospitalId, array $cards)
    {
        $instance = ModelRepository::getCardInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        if (count($cards) === 0) {
            return;
        }

        $cards = array_map(function (Card $card) {
            return [
                'updateTime' => 'now',
                'cardId' => $card->toArray()['cardId'],
                'hospitalId' => $card->toArray()['hospitalId'],
                'divisionId' => $card->toArray()['divisionId'],
                'inHospitalItemId' => $card->toArray()['inHospitalItemId'],
                'quantity' => $card->toArray()['quantity']['quantityNum'],
                'payoutId' => '',
                'lotNumber' => $card->toArray()['lot']['lotNumber'],
                'lotDate' => $card->toArray()['lot']['lotDate'],
            ];
        }, $cards);

        return $instance->updateBulk('cardId', $cards);
    }

    public function reset(HospitalId $hospitalId, array $cardIds)
    {
        $instance = ModelRepository::getCardInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        if (count($cardIds) === 0) {
            return;
        }

        $cardIds = array_map(function (CardId $id) use ($instance) {
            $instance->orWhere('cardId', $id->value());
            return $id;
        }, $cardIds);

        $instance->update([
            'updateTime' => 'now',
            'payoutId' => '',
            'lotNumber' => '',
            'lotDate' => '',
        ]);

        return;
    }
}

interface CardRepositoryInterface
{
}
