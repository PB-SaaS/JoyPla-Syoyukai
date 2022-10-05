<?php
echo '<?php

namespace '.$projectName.';

use framework\Application;

class '.$projectName.'Application extends Application
{
    public function __construct()
    {
        config_path("'.$projectName.'/config/app");

        parent::__construct();
        $this->boot();
    }

    public function boot()
    {
    }
}
';