<?php

namespace app;

use app\core\Controller;

class UserController extends Controller
{
    protected $template = 'default';

    public function _beforeRoute()
    {
        if ($this->user->isAnonym()) {
            return $this->redirect('login');
        }
    }
}
