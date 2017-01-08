<?php

namespace Application\Module\Api\Controller;

use Application\Module\Api\Controller\BaseController;

class Test extends BaseController
{
    public function index($params)
    {
        return $params;
    }
}