<?php

namespace app\module;

use app\UserController;
use app\core\User;

class IndexController extends UserController
{
    public function main()
    {
        return $this->render('default');
    }
}
