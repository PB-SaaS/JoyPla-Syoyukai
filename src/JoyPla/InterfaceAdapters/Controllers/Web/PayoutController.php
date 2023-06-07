<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\PayoutHistoryId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Service\Repository\RepositoryProvider;

class PayoutController extends Controller
{
    public function register($vars)
    {
        if (Gate::denies('register_of_payout')) {
            Router::abort(403);
        }

        $payoutUnitPriceUseFlag = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->resetValue(['payoutUnitPrice'])
            ->get()
            ->first();
        $payoutUnitPriceUseFlag =
            $payoutUnitPriceUseFlag->payoutUnitPrice;

        $body = View::forge('html/Payout/Register', compact('payoutUnitPriceUseFlag'), false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function index($vars)
    {
        if (Gate::denies('list_of_payout_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_payout_slips');

        $body = View::forge('html/Payout/Index', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show($vars)
    {
        if (Gate::denies('list_of_payout_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_payout_slips');

        $payout = ModelRepository::getPayoutInstance()->where('payoutHistoryId' , $vars['payoutHistoryId'])->where('hospitalId', $this->request->user()->hospitalId)->get()->first();

        if(empty($payout) || ( $gate->isOnlyMyDivision() && $payout->targetDivisionId !== $this->request->user()->divisionId)){
            Router::abort(403);
        }

        $body = View::forge('html/Payout/Show', [
            'payoutHistoryId' => $payout->payoutHistoryId
        ], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    

    public function print($vars){
        
        if (Gate::denies('list_of_accountant_slips')) {
            Router::abort(403);
        }

        $payout = (new RepositoryProvider())
            ->getPayoutRepository()
            ->findByPayoutHistoryId(
                new HospitalId($this->request->user()->hospitalId),
                new PayoutHistoryId($vars['payoutHistoryId'])
            );

        if (!$payout) {
            Router::abort(404);
        }

        $items = [[]];
        $x = 0;
        $count = 0;
        foreach ($payout->_items as $key => $item) {
            $count++;
            $inHospitalItem = array_find($payout->_inHospitalItems, function($inHospitalItem) use ($item) {
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
            'printLayout/Payout/Show',
            [
                'payout' => $payout->toArray(),
                'payoutItems' => $items,
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
