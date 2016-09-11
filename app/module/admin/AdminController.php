<?php

namespace app\module\admin;

use app\AdminBaseController;
use app\core\Database;
use app\core\Helper;
use app\core\Request;
use app\core\User;

class AdminController extends AdminBaseController
{
    protected $homeUrl = 'admin';

    public function main()
    {
        return $this->render('default');
    }

    public function account(User $user, Database $db, Request $request)
    {
        $fields = [
            'username'=>$request->get('username', $user->get('username')),
            'password'=>$request->get('password', $user->get('password')),
            'new_password'=>$request->get('new_password', $user->get('new_password')),
            'name'=>$request->get('name', $user->get('name')),
        ];
        $error   = null;
        $selfUrl = $this->homeUrl.'/account';

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

        return $this->render('profil', [
            'form'=>$form,
            'avatar'=>$avatar,
            'backUrl'=>'index',
            ]);
    }

    public function logout(User $user)
    {
        $user->logout();

        return $this->redirect('admin/login');
    }
}
