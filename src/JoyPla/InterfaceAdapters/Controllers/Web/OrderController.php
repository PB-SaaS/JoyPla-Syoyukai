<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Web\Order\OrderIndexInputData;
use JoyPla\Application\InputPorts\Web\Order\OrderIndexInputPortInterface;
use JoyPla\Enterprise\Models\OrderItem;
use JoyPla\Enterprise\Models\OrderStatus;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class OrderController extends Controller
{
    public function bulkEdit()
    {
        $orderitems = ModelRepository::getOrderItemViewInstance()
            ->where('orderStatus', OrderStatus::UnOrdered)
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->get()
            ->all();

        $orderitems = array_map(function ($orderItem) {
            return OrderItem::create($orderItem)->toArray();
        }, $orderitems);

        $stocks = ModelRepository::getStockViewInstance()->where(
            'hospitalId',
            $this->request->user()->hospitalId
        );

        foreach ($orderitems as $orderItem) {
            $stocks
                ->orWhere('divisionId', $orderItem['division']['divisionId'])
                ->orWhere('inHospitalItemId', $orderItem['inHospitalItemId']);
        }
        $stocks = $stocks->get()->all();
        foreach ($orderitems as &$orderItem) {
            $stock = array_find($stocks, function ($stock) use ($orderItem) {
                return $orderItem['division']['divisionId'] ===
                    $stock->divisionId &&
                    $orderItem['inHospitalItemId'] === $stock->inHospitalItemId;
            });
            $orderItem['stockQuantity'] = $stock ? $stock->stockQuantity : 0;
        }

        $body = View::forge(
            'html/Order/BulkEdit',
            compact('orderitems'),
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function register($vars)
    {
        if (Gate::denies('register_of_unordered_slips')) {
            Router::abort(403);
        }
        $body = View::forge('html/Order/Register', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function unapprovedShow()
    {
        if (Gate::denies('list_of_unordered_slips')) {
            Router::abort(403);
        }
        $body = View::forge('html/Order/UnapprovedShow', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function unapprovedIndex(
        $vars,
        OrderIndexInputPortInterface $inputPort
    ) {
        if (Gate::denies('list_of_unordered_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_unordered_slips');

        $inputData = new OrderIndexInputData(
            $this->request->user(),
            $vars['orderId'],
            true,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function fixedQuantityOrder($vars)
    {
        if (Gate::denies('fixed_quantity_order_slips')) {
            Router::abort(403);
        }
        $body = View::forge(
            'html/Order/FixedQuantityOrder',
            [],
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show()
    {
        if (Gate::denies('list_of_order_slips')) {
            Router::abort(403);
        }

        $body = View::forge('html/Order/Show', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function index($vars, OrderIndexInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_order_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_order_slips');

        $inputData = new OrderIndexInputData(
            $this->request->user(),
            $vars['orderId'],
            false,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function print($vars, OrderIndexInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_order_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_order_slips');

        $inputData = new OrderIndexInputData(
            $this->request->user(),
            $vars['orderId'],
            false,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }
}
