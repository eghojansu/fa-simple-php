<?php

namespace app\module;

use app\UserController;

class IndexController extends UserController
{
    public function main()
    {
        return $this->render('default');
    }
}
