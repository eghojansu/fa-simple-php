<?php

namespace app\module\master;

use app\UserController as UserControllerBase;
use app\core\Database;
use app\core\HTML;
use app\core\Request;
use app\core\User;

class UserController extends UserControllerBase
{
    protected $homeUrl = 'master/user';

    public function main(User $user, Database $db, Request $request, HTML $html, $page = 1)
    {
        $filter = ['id <> :self', ':self'=>$user->get('id')];
        if ('' !== ($keyword = $request->query('keyword'))) {
            $filter[0] .= ' and (name like :keyword or username like :keyword)';
            $filter[':keyword'] = '%'.$keyword.'%';
        }
        $subset = $db->paginate('user', $filter, null, is_numeric($page)?$page:1);
        $pagination = $html->pagination($subset, ['route'=>$this->homeUrl.'/{page}','params'=>['page'=>$page]]);

        return $this->render('user/list', [
            'subset'=>$subset,
            'homeUrl'=>$this->homeUrl,
            'keyword'=>$keyword,
            'pagination'=>$pagination,
            'createUrl'=>$this->homeUrl.'/create',
            'updateUrl'=>$this->homeUrl.'/update/{id}',
            'deleteUrl'=>$this->homeUrl.'/delete/{id}',
            'detailUrl'=>$this->homeUrl.'/detail/{id}',
            ]);
    }

    public function create(User $user, Request $request, Database $db)
    {
        $fields = [
            'name'=>$request->get('name'),
            'username'=>$request->get('username'),
            'password'=>$request->get('password'),
            'level'=>$request->get('level'),
        ];
        $error = null;

        $labels = $this->app->load('app/config/translations/user-labels.php');
        if ($request->isPost()) {
            $rules = [
                'name,username,password'=>'required',
                'username'=>'unique(user)'
            ];
            $error = $this->validation
                ->setData($fields)
                ->setRules($rules)
                ->setLabels($labels)
                ->validate()
                ->getError()
            ;

            if (!$error) {
                $saved = $db->insert('user', $fields);
                if ($saved) {
                    $user->message('success', 'Data sudah disimpan!');

                    return $this->redirect($this->homeUrl);
                }
                else {
                    $error = 'Data gagal disimpan!';
                }
            }
            $user->message('error', $error);
        }

        $form = $this->form
            ->setData($fields)
            ->setLabels($labels)
            ->setAttrs([
                'class'=>'form-horizontal',
            ])
            ->setDefaultControlAttrs([
                'class'=>'form-control',
            ])
            ->setDefaultLabelAttrs([
                'class'=>'form-label col-md-2',
            ])
        ;

        return $this->render('user/create', [
            'form'=>$form,
            'homeUrl'=>$this->homeUrl,
            ]);
    }

    public function update(User $user, Request $request, Database $db, $id)
    {
        $error = null;
        $filter = ['id = ? and id <> ?',
            $id, $user->get('id'),
        ];
        $record = $db->findOne('user', $filter);
        if (!$record) {
            $user->message('warning', 'Data tidak ditemukan');

            return $this->redirect($homeUrl);
        }

        $fields = [
            'name'=>$request->get('name', $record['name']),
            'username'=>$request->get('username', $record['username']),
            'password'=>$request->get('password', $record['password']),
            'level'=>$request->get('level', $record['level']),
        ];

        $labels = $this->app->load('app/config/translations/user-labels.php');
        if ($request->isPost()) {
            $rules = [
                'name,username,password'=>'required',
            ];
            $error = $this->validation
                ->setData($fields)
                ->setRules($rules)
                ->setLabels($labels)
                ->validate()
                ->getError()
            ;

            if (!$error) {
                $saved = $db->update('user', $fields, $filter);
                if ($saved) {
                    $user->message('success', 'Data sudah disimpan!');

                    return $this->redirect($this->homeUrl);
                }
                else {
                    $error = 'Data gagal disimpan!';
                }
            }
            $user->message('error', $error);
        }

        $form = $this->form
            ->setData($fields)
            ->setLabels($labels)
            ->setAttrs([
                'class'=>'form-horizontal',
            ])
            ->setDefaultControlAttrs([
                'class'=>'form-control',
            ])
            ->setDefaultLabelAttrs([
                'class'=>'form-label col-md-2',
            ])
        ;

        return $this->render('user/update', [
            'form'=>$form,
            'homeUrl'=>$this->homeUrl,
            ]);
    }

    public function detail(Database $db, User $user, $id)
    {
        $filter = [
            'id = ? and id <> ?',
            $id,
            $user->get('id'),
        ];
        $record = $db->findOne('user', $filter);
        if (empty($record)) {
            $user->message('error', 'Data tidak ditemukan');

            return $this->redirect($this->homeUrl);
        }

        return $this->render('user/detail', [
            'record'=>$record,
            'homeUrl'=>$this->homeUrl,
            ]);
    }

    public function delete(User $user, Database $db, $id)
    {
        $filter = [
            'id = ? and id <> ?',
            $id,
            $user->get('id'),
        ];

        $db->delete('user', $filter);
        $user->message('info', 'Data sudah dihapus!');

        return $this->redirect($this->homeUrl);
    }
}
