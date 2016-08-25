<?php

namespace app\module\account;

use app\Controller;
use app\Database;
use app\Helper;
use app\Request;
use app\User;

class AccountController extends Controller
{
    public function main()
    {
        return $this->login();
    }

    public function login(User $user, Request $request, Database $db)
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

        return $this->render(null, 'login', [
            'form'=>$form,
            'error'=>$error,
            ]);
    }

    public function profil(User $user, Database $db, Request $request)
    {
        if ($user->isAnonym()) {
            return $this->redirect('account/login');
        }

        $fields = [
            'username'=>$request->get('username', $user->get('username')),
            'password'=>$request->get('password', $user->get('password')),
            'new_password'=>$request->get('new_password', $user->get('new_password')),
            'name'=>$request->get('name', $user->get('name')),
        ];
        $error   = null;
        $selfUrl = 'account/profil';

        $labels = $this->app->load('app/config/translations/user-labels.php');
        if ($request->isPost()) {
            $old_password = $user->get('password');
            $rules = [
                'name,username'=>'required',
                'password'=>'required,Password saat ini tidak boleh kosong',
                '-password'=>"equal($old_password),Password saat ini tidak valid",
                'new_password'=>'minLength(4,allowEmpty)',
            ];
            $error = $this->validation
                ->setData($fields)
                ->setRules($rules)
                ->setLabels($labels)
                ->validate()
                ->getError()
            ;

            if (!$error) {
                // handle file
                $filename = $request->baseDir().'asset/avatars/user-'.$user->get('id');
                if (Helper::handleFileUpload('avatar', $filename, $this->app->get('imageTypes'))) {
                    $fields['avatar'] = basename($filename);
                }

                if ($fields['new_password']) {
                    $fields['password'] = $fields['new_password'];
                }
                unset($fields['new_password']);

                $filter = ['id = ?',
                    $user->get('id'),
                ];
                $saved = $db->update('user', $fields, $filter);
                if ($saved) {
                    $user->register($fields);
                    $user->message('success', 'Data sudah diupdate');

                    return $this->redirect($selfUrl);
                }
                else {
                    $error = 'Data gagal disimpan!';
                }
            }
            $user->message('error', $error);
        }

        $avatar = $user->get('avatar');
        $avatar = $this->app->asset($avatar?'asset/avatars/'.$avatar:'asset/images/avatar.png');

        $form = $this->form
            ->setData($fields)
            ->setLabels($labels)
            ->setAttrs([
                'class'=>'form-horizontal',
                'enctype'=>'multipart/form-data'
            ])
            ->setDefaultControlAttrs([
                'class'=>'form-control',
            ])
            ->setDefaultLabelAttrs([
                'class'=>'form-label col-md-4',
            ])
        ;

        return $this->render('default', 'profil', [
            'form'=>$form,
            'avatar'=>$avatar,
            'backUrl'=>'index',
            ]);
    }

    public function logout(User $user)
    {
        $user->logout();

        return $this->redirect('account/login');
    }
}
