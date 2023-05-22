<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\ItemList\ItemListIndexInputData;
use JoyPla\Application\InputPorts\Api\ItemList\ItemListIndexInputPortInterface;
use JoyPla\Application\InputPorts\Api\ItemList\ItemListRegisterInputData;
use JoyPla\Application\InputPorts\Api\ItemList\ItemListRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\ItemList\ItemListShowInputData;
use JoyPla\Application\InputPorts\Api\ItemList\ItemListShowInputPortInterface;
use JoyPla\Application\InputPorts\Api\ItemList\ItemListUpdateInputData;
use JoyPla\Application\InputPorts\Api\ItemList\ItemListUpdateInputPortInterface;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
//画面表示・登録・更新・削除なのでclass名はItemListでOK

class ItemListController extends Controller
{
    public function register(
        $vars,
        ItemListRegisterInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $gate = Gate::getGateInstance('register_of_itemList');

        $inputData = new ItemListRegisterInputData(
            $this->request->user(),
            [
                'hospitalId' => $this->request->user()->hospitalId,
                'divisionId' => $this->request->get('divisionId'),
                'itemListName' => $this->request->get('itemListName'),
                'usableStatus' => $this->request->get('usableStatus'),
            ],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function index($vars, ItemListIndexInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $gate = Gate::getGateInstance('list_of_itemList_slips');

        $search = $this->request->get('search');
/* 使用権限対策。ひとまず画面上での切り替えで対応する。
        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$this->request->user()->divisionId];
        }
 */
        $inputData = new ItemListIndexInputData(
            $this->request->user(),
            $search
        );

        $inputPort->handle($inputData);
    }

    public function show($vars, ItemListShowInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $itemListId = $vars['itemListId'];

        $inputData = new ItemListShowInputData(
            $this->request->user(),
            $itemListId
        );

        $inputPort->handle($inputData);
    }

    public function update($vars, ItemListUpdateInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $itemListId = $vars['itemListId'];
        $itemList = $this->request->get('itemList');
        $itemListName = $this->request->get('itemListName');

        $inputData = new ItemListUpdateInputData(
            $this->request->user(),
            $itemListId,
            $itemList['items'] ?? [],
            $itemListName,
        );

        $inputPort->handle($inputData);
    }

    public function delete($vars)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $itemListId = $vars['itemListId'];

        ModelRepository::getItemListTableInstance()
            ->where('itemListId', $itemListId)
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->delete();
        echo (new ApiResponse('', 1, 200, 'deleted', []))->toJson();
    }
}
