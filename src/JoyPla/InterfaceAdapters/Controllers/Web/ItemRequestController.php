<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Web\ItemRequest\ItemRequestShowInputData;
use JoyPla\Application\InputPorts\Web\ItemRequest\ItemRequestShowInputPortInterface;
use JoyPla\Application\InputPorts\Web\ItemRequest\PickingListInputData;
use JoyPla\Application\InputPorts\Web\ItemRequest\PickingListInputPortInterface;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class ItemRequestController extends Controller
{
    public function register($vars)
    {
        if (Gate::denies('register_of_item_requests')) {
            Router::abort(403);
        }

        $payoutUnitPriceUseFlag = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->value('payoutUnitPrice')
            ->get()
            ->first();
        $payoutUnitPriceUseFlag = $payoutUnitPriceUseFlag->payoutUnitPrice;
        $body = View::forge(
            'html/ItemRequest/Register',
            compact('payoutUnitPriceUseFlag'),
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function history($vars)
    {
        if (Gate::denies('list_of_item_request_history')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_item_request_history');
        $body = View::forge('html/ItemRequest/History', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show($vars, ItemRequestShowInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_item_request_history')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_item_request_history');
        $inputData = new ItemRequestShowInputData(
            $this->request->user(),
            $vars['requestHId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function totalization($vars)
    {
        if (Gate::denies('totalization_of_item_requests')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('totalization_of_item_requests');
        $body = View::forge(
            'html/ItemRequest/Totalization',
            [],
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function pickingList($vars, PickingListInputPortInterface $inputPort)
    {
        $token = $this->request->get('_token');
        Csrf::validate($token, true);

        if (Gate::denies('totalization_of_item_requests')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('totalization_of_item_requests');

        $search = $this->request->get('search');

        $user = $this->request->user();

        if ($gate->isOnlyMyDivision()) {
            $search['targetDivisionIds'] = [$user->divisionId];
        }

        $inputData = new PickingListInputData(
            $this->request->user(),
            $search,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function list($vars)
    {
        if (Gate::denies('item_request_bulk')) {
            Router::abort(403);
        }

        $items = ModelRepository::getTotalRequestByDivisionInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->where('requestQuantity', 0, '>')
            ->orderBy('id', 'asc')
            ->get();

        $inItem = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $this->request->user()->hospitalId
        );

        $stock = ModelRepository::getStockInstance()->where(
            'hospitalId',
            $this->request->user()->hospitalId
        );

        $division = ModelRepository::getDivisionInstance()->where(
            'hospitalId',
            $this->request->user()->hospitalId
        );

        foreach ($items as $item) {
            $inItem->orWhere('inHospitalItemId', $item->inHospitalItemId);
            $division->orWhere('divisionId', $item->targetDivisionId);
            $division->orWhere('divisionId', $item->sourceDivisionId);
            $stock->orWhere('divisionId', $item->targetDivisionId);
            $stock->orWhere('divisionId', $item->sourceDivisionId);
            $stock->orWhere('inHospitalItemId', $item->inHospitalItemId);
        }

        $stock = $stock->get();
        $division = $division->get();
        $inItem = $inItem->get();

        foreach ($items as &$item) {
            $item->set(
                '_item',
                array_find($inItem, function ($s) use ($item) {
                    return $s->inHospitalItemId == $item->inHospitalItemId;
                })
            );
            $item->set(
                '_targetDivision',
                array_find($division, function ($s) use ($item) {
                    return $s->divisionId == $item->targetDivisionId;
                })
            );
            $item->set(
                '_sourceDivision',
                array_find($division, function ($s) use ($item) {
                    return $s->divisionId == $item->sourceDivisionId;
                })
            );
            $item->set(
                '_targetStock',
                array_find($stock, function ($s) use ($item) {
                    return $s->divisionId == $item->targetDivisionId &&
                        $s->inHospitalItemId == $item->inHospitalItemId;
                })
            );
            $item->set(
                '_sourceStock',
                array_find($stock, function ($s) use ($item) {
                    return $s->divisionId == $item->sourceDivisionId &&
                        $s->inHospitalItemId == $item->inHospitalItemId;
                })
            );
            $item->set('rowRequestQuantity', $item->requestQuantity);
        }

        $items = $items->all();

        $gate = Gate::getGateInstance('item_request_bulk');
        $body = View::forge(
            'html/ItemRequest/Bulk',
            compact('items'),
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
