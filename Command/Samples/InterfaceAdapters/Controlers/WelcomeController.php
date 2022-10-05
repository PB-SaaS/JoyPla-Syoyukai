<?php
echo '<?php

namespace '.$projectName.'\InterfaceAdapters\Controllers\Web ;

use framework\Http\Request;
use framework\Http\Controller;
use framework\Http\View;

class WelcomeController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index(array $vars)
    {
        //
    }

    public function create(array $vars)
    {
        //
    }

    public function store(array $vars)
    {
        //
    }

    public function show(array $vars)
    {
        $body = View::forge("html/welcome")->render();
        echo View::forge("template/base",compact("body"),false)->render();
    }

    public function edit(array $vars)
    {
        //
    }

    public function update(array $vars)
    {
        //
    }

    public function destroy(array $vars)
    {
        //
    }
}
';
