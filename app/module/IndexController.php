<?php

namespace app\module;

use app\UserBaseController;

class IndexController extends UserBaseController
{
    public function main()
    {
        return $this->render(null);
    }
}
