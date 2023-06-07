<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Exception\NotFoundException;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Enterprise\Models\AcceptanceId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Service\Repository\RepositoryProvider;

class AcceptanceController extends Controller
{
    public function index($vars)
    {
        $body = View::forge('html/Acceptance/Index', [
            'userDivisionId' => $this->request->user()->divisionId,
        ], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    public function show($vars)
    {
        $acceptance = ModelRepository::getAcceptanceInstance()->where('hospitalId', $this->request->user()->hospitalId)->where('acceptanceId', $vars['acceptanceId'])->get();

        if($acceptance->count() == 0){
            throw new NotFoundException('not found' , 404);
        }

        $acceptance = $acceptance->first();

        $isUnitPrice = ModelRepository::getHospitalInstance()->where('hospitalId',$this->request->user()->hospitalId)->resetValue('payoutUnitPrice')->get()->first();

        $body = View::forge('html/Acceptance/Show', [
            'acceptanceId' => $vars['acceptanceId'],
            'isUpdateSuccess' => (!gate('is_user') || (gate('is_user') && $this->request->user()->divisionId === $acceptance->sourceDivisionId)),
            'isPayoutSuccess' => (!gate('is_user') || (gate('is_user') && $this->request->user()->divisionId === $acceptance->targetDivisionId)),
            'isUnitPrice' => ($isUnitPrice->payoutUnitPrice == '1')
        ], false)->render();

        echo view('html/Common/Template', [
            'body' => $body,
        ], false)->render();
    }

    public function print($vars){
        
        if (Gate::denies('list_of_accountant_slips')) {
            Router::abort(403);
        }

        $acceptance = (new RepositoryProvider())
            ->getAcceptanceRepository()
            ->findByAcceptanceId(
                new HospitalId($this->request->user()->hospitalId),
                new AcceptanceId($vars['acceptanceId'])
            );

        if (!$acceptance) {
            Router::abort(404);
        }

        $items = [[]];
        $x = 0;
        $count = 0;
        foreach ($acceptance->_items as $key => $item) {
            $count++;
            $inHospitalItem = array_find($acceptance->_inHospitalItems, function($inHospitalItem) use ($item) {
                return $inHospitalItem->inHospitalItemId === $item->inHospitalItemId;
            });
            $item->itemName = $inHospitalItem->itemName;
            $item->makerName = $inHospitalItem->makerName;
            $item->itemStandard = $inHospitalItem->itemStandard;
            $item->itemJANCode = $inHospitalItem->itemJANCode;
            $item->itemCode = $inHospitalItem->itemCode;
            $items[$x][] = $item;
            if (
                ($count % 11 === 0 && $x === 0) ||
                ($count % 13 === 0 && $x > 0)
            ) {
                $count = 0;
                $x++;
            }
        }

        $body = View::forge(
            'printLayout/Acceptance/Show',
            [
                'acceptance' => $acceptance->toArray(),
                'acceptanceItems' => $items,
            ],
            false
        )->render();
        echo view(
            'printLayout/Common/Template',
            compact('body'),
            false
        )->render();
    }
}
