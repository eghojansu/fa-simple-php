<?php

namespace app;

use app\core\Controller;

class AdminController extends Controller
{
    protected $homeUrl;
    protected $template = 'admin';

    public function _beforeRoute()
    {
        if ($this->user->isNot('admin')) {
            return $this->redirect('admin/login');
        }

        $this->app->set('currentPath', $this->homeUrl);
    }
}
