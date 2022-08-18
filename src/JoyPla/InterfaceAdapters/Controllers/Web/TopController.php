<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use framework\Http\Controller;
use framework\Http\View;

class TopController extends Controller
{
    public function index($vars) {
        $body = View::forge('html/Common/Top', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function orderpage($vars) {
        $body = View::forge('html/Common/OrderPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function consumptionpage($vars)
    {
        $body = View::forge('html/Common/ConsumptionPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function stocktakingpage($vars)
    {
        $body = View::forge('html/Common/StocktakingPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function payoutpage($vars)
    {
        $body = View::forge('html/Common/PayoutPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function stockpage($vars)
    {
        $body = View::forge('html/Common/StockPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function cardpage($vars)
    {
        $body = View::forge('html/Common/CardPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function trackrecordpage($vars)
    {
        $body = View::forge('html/Common/TrackRecordPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function monthlyreportpage($vars)
    {
        $body = View::forge('html/Common/MonthlyReportPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function estimatepage($vars)
    {
        $body = View::forge('html/Common/EstimatePage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function lendingpage($vars)
    {
        $body = View::forge('html/Common/LendingPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function productpage($vars)
    {
        $body = View::forge('html/Common/ProductPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function userpage($vars)
    {
        $body = View::forge('html/Common/UserPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function optionpage($vars)
    {
        $body = View::forge('html/Common/OptionPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}

