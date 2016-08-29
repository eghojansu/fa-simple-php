<?php

namespace app\module;

use app\core\Controller;
use app\core\Database;
use app\core\Request;
use app\core\User;

class LoginController extends Controller
{
    protected $template = 'login';

    public function main(User $user, Request $request, Database $db)
    {
        if ($user->hasBeenLogin()) {
            return $this->redirect('index');
        }

        $username = $request->get('username');
        $password = $request->get('password');
        $error = null;
        if ($request->isPost()) {
            $filter = ['username = ? and password = ?',
                $username, $password,
            ];
            $data = $db->findOne('user', $filter);

            if (empty($data)) {
                $error = 'Login gagal! Username atau password tidak cocok!';
            } else {
                $user->login($data['level'], $data);

                return $this->redirect('index');
            }
        }

        $form = $this->form
            ->setData([
                'username'=>$username,
                ])
            ->setAttrs([
                'id'=>'login-form'
                ])
            ->setDefaultLabelAttrs([
                'class'=>'sr-only'
                ])
            ->setDefaultControlAttrs([
                'class'=>'form-control form-block'
                ])
        ;

        return $this->render(null, [
            'form'=>$form,
            'error'=>$error,
            ]);
    }
}
