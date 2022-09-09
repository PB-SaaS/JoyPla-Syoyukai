<?php

namespace framework\SpiralConnecter;

use HttpRequestParameter;

interface SpiralConnecterInterface {
    public function request(XSpiralApiHeaderObject $header, HttpRequestParameter $httpRequestParameter);
}