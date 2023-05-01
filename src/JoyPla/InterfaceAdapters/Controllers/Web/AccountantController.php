<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Enterprise\Models\AccountantId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Service\Repository\RepositoryProvider;

class AccountantController extends Controller
{
    public function index($vars)
    {
        $body = View::forge('html/Accountant/Index', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show($vars)
    {
        if (Gate::denies('list_of_accountant_slips')) {
            Router::abort(403);
        }
        $accountant = ModelRepository::getAccountantInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->where('accountantId', $vars['accountantId'])
            ->get()
            ->first();

        if (!$accountant) {
            Router::abort(404);
        }

        if (
            Gate::allows('is_user') &&
            $this->request->user()->divisionId !== $accountant->divisionId
        ) {
            Router::abort(404);
        }

        $body = View::forge(
            'html/Accountant/Show',
            [
                'accountantId' => $vars['accountantId'],
            ],
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function print($vars)
    {
        if (Gate::denies('list_of_accountant_slips')) {
            Router::abort(403);
        }

        $accountant = (new RepositoryProvider())
            ->getAccountantRepository()
            ->findByAccountantId(
                new HospitalId($this->request->user()->hospitalId),
                new AccountantId($vars['accountantId'])
            );

        if (!$accountant) {
            Router::abort(404);
        }

        if (
            Gate::allows('is_user') &&
            $this->request->user()->divisionId !==
                $accountant->getDivisionId()->value()
        ) {
            Router::abort(404);
        }

        $items = [[]];
        $x = 0;
        $count = 0;
        foreach ($accountant->getItems() as $key => $item) {
            $count++;
            $items[$x][] = $item->toArray();
            if (
                ($count % 11 === 0 && $x === 0) ||
                ($count % 13 === 0 && $x > 0)
            ) {
                $count = 0;
                $x++;
            }
        }

        $body = View::forge(
            'printLayout/Accountant/Show',
            [
                'accountant' => $accountant->toArray(),
                'accountantItems' => $items,
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
