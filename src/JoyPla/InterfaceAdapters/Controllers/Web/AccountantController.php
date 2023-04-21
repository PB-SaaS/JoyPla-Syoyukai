<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
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
}
