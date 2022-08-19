<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Card;
use JoyPla\Enterprise\Models\CardId;
use JoyPla\Enterprise\Models\HospitalId;

class CardRepository implements CardRepositoryInterface
{
    public function get(HospitalId $hospitalId , array $cardIds)
    {
        $instance = Card::where('hospitalId', $hospitalId->value())->value('cardId');
        
        if(count($cardIds) === 0)
        {
            return [];
        }

        $cardIds = array_map(function( CardId $id) use ($instance) {
            $instance->orWhere('cardId',$id->value());
            return $id;
        },$cardIds);

        return array_map(function($item)
        {   
            return new CardId( $item->cardId ) ;
        },( $instance->get() )->data->all());
    }

    public function reset(HospitalId $hospitalId , array $cardIds)
    {
        $instance = Card::where('hospitalId', $hospitalId->value());
        
        if(count($cardIds) === 0)
        {
            return ;
        }

        $cardIds = array_map(function( CardId $id) use ($instance) {
            $instance->orWhere('cardId',$id->value());
            return $id;
        },$cardIds);

        $instance->update([
            'payoutId' => ""
        ]);

        return ;
    }
}

interface CardRepositoryInterface 
{
    public function get(HospitalId $hospitalId , array $cardIds);
}