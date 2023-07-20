<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Application\InputPorts\Web\Product\ItemList\ItemListShowInputData;
use JoyPla\Application\InputPorts\Web\Product\ItemList\ItemListShowInputPortInterface;
use JoyPla\Service\Repository\RepositoryProvider;

class ItemListController extends Controller
{
    public function index($vars)
    {
        $body = View::forge('html/Product/ItemList/Index', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show($vars)
    {
        if (Gate::denies('register_of_itemList')) {
            Router::abort(403);
        }
        $itemList = ModelRepository::getItemListTableInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->where('itemListId', $vars['itemListId'])
            ->get()
            ->first();

        if (!$itemList) {
            Router::abort(404);
        }

        if (
            Gate::allows('is_user') &&
            $this->request->user()->divisionId !== $itemList->divisionId &&
            $itemList->usableStatus === '1' //部署限定
        ) {
            Router::abort(403);
        }

        $body = View::forge(
            'html/Product/ItemList/Show',
            [
                'itemListId' => $vars['itemListId'],
            ],
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function print($vars/* , ItemListShowInputPortInterface $inputPort */)
    {
        if (Gate::denies('register_of_itemList')) {
            Router::abort(403);
        }
/* 
        $gate = Gate::getGateInstance('register_of_itemList');

        $inputData = new ItemListShowInputData(
            $this->request->user(),
            $vars['itemListId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
 */
        $itemList = ModelRepository::getItemListTableViewInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->where('itemListId', $vars['itemListId'])
            ->get()
            ->first();

        if (!$itemList) {
            Router::abort(404);
        }

        if (
            Gate::allows('is_user') &&
            $this->request->user()->divisionId !== $itemList->divisionId &&
            $itemList->usableStatus === '1' //部署限定
        ) {
            Router::abort(403);
        }

        $itemListRows = ModelRepository::getItemListRowsViewInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->where('itemListId', $vars['itemListId'])
            ->get()
            ->all();

        $body = View::forge(
            'printLayout/Product/ItemList',
            [
                'itemListId' => $vars['itemListId'],
                'itemList' => $itemList,
                'itemListRows' => $itemListRows,
            ],
            false
        )->render();
        echo view('printLayout/Common/Template', compact('body'), false)->render();
    }
}
