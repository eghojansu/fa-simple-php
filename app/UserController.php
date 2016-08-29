<?php

namespace app;

use app\core\Controller;
use app\core\User;

class UserController extends Controller
{
    protected $template = 'default';

    public function beforeRoute(User $user)
    {
        if ($user->isAnonym()) {
            return $this->redirect('account/login');
        }
    }
}
