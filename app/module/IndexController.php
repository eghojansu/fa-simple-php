<?php

namespace app\module;

use app\Controller;
use app\User;

class IndexController extends Controller
{
    public function main()
    {
        return $this->render('default', 'default');
    }

    public function beforeRoute(User $user)
    {
        if ($user->isAnonym()) {
            return $this->redirect('account/login');
        }
    }
}
