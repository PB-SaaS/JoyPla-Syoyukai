<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListIndexInputData;
use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListIndexInputPortInterface;
use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListRegisterInputData;
use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListShowInputData;
use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListShowInputPortInterface;
use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListUpdateInputData;
use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListUpdateInputPortInterface;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
//画面表示・登録・更新・削除なのでclass名はStocktakingListでOK

class StocktakingListController extends Controller
{
    public function register(
        $vars,
        StocktakingListRegisterInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $gate = Gate::getGateInstance('register_of_stocktakingList');

        $inputData = new StocktakingListRegisterInputData(
            $this->request->user(),
            [
                'hospitalId' => $this->request->user()->hospitalId,
                'divisionId' => $this->request->get('divisionId'),
                'stocktakingListName' => $this->request->get('stocktakingListName'),
            ],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function index($vars, StocktakingListIndexInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $gate = Gate::getGateInstance('list_of_stocktakingList_slips');

        $search = $this->request->get('search');
/* 使用権限対策。ひとまず画面上での切り替えで対応する。
        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$this->request->user()->divisionId];
        }
 */
        $inputData = new StocktakingListIndexInputData(
            $this->request->user(),
            $search
        );

        $inputPort->handle($inputData);
    }

    public function show($vars, StocktakingListShowInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $stocktakingListId = $vars['stockListId'];

        $inputData = new StocktakingListShowInputData(
            $this->request->user(),
            $stocktakingListId
        );

        $inputPort->handle($inputData);
    }

    public function update($vars, StocktakingListUpdateInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $stocktakingListId = $vars['stockListId'];
        $stocktakingList = $this->request->get('stocktakingList');
        $stocktakingListName = $this->request->get('stocktakingListName');

        $inputData = new StocktakingListUpdateInputData(
            $this->request->user(),
            $stocktakingListId,
            $stocktakingList['items'] ?? [],
            $stocktakingListName,
        );

        $inputPort->handle($inputData);
    }

    public function delete($vars)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $stockListId = $vars['stockListId'];

        ModelRepository::getStocktakingListTableInstance()
            ->where('stockListId', $stockListId)
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->delete();
        echo (new ApiResponse('', 1, 200, 'deleted', []))->toJson();
    }
}
