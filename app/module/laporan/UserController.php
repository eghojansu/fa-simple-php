<?php

namespace app\module\laporan;

use app\UserController as UserControllerBase;
use app\core\Database;
use app\core\HTML;
use app\core\Request;

class UserController extends UserControllerBase
{
    protected $homeUrl = 'laporan/user';

    public function main(Database $db, Request $request, HTML $html, $page = 1)
    {
        $filter = [];
        if ($keyword = $request->query('keyword')) {
            $filter = [
                '(name like :keyword or username like :keyword)',
                ':keyword' => '%'.$keyword.'%'
            ];
        }
        $subset = $db->paginate('user', $filter, null, is_numeric($page)?$page:1);
        $pagination = $html->pagination($subset, ['route'=>$this->homeUrl.'/{page}','params'=>['page'=>$page]]);

        return $this->render('user/list', [
            'subset'=>$subset,
            'homeUrl'=>$this->homeUrl,
            'keyword'=>$keyword,
            'pagination'=>$pagination,
            'printUrl'=>$this->homeUrl.'/printreport',
            'downloadUrl'=>$this->homeUrl.'/download',
            ]);
    }

    public function printReport(Database $db, Request $request)
    {
        $filter = [];
        if ($keyword = $request->query('keyword')) {
            $filter = [
                '(name like :keyword or username like :keyword)',
                ':keyword' => '%'.$keyword.'%'
            ];
        }
        $data = $db->find('user', $filter);

        return $this->render('user/print', [
            'data'=>$data,
            'homeUrl'=>$this->homeUrl,
            ]);
    }

    public function download(Database $db, Request $request)
    {
        $filter = [];
        if ($keyword = $request->query('keyword')) {
            $filter = [
                '(name like :keyword or username like :keyword)',
                ':keyword' => '%'.$keyword.'%'
            ];
        }
        $data = $db->select('name,username,level', 'user', $filter);

        $filename = 'user-report';
        $header = ['Name','Username','Level'];
        $delimiter = ';';
        $delay = false;

        return $this->csv($filename, $header, $data, $delimiter, $delay);
    }

    public function _beforeRoute()
    {
        parent::_beforeRoute();
        $this->app->set('currentPath', $this->homeUrl);
    }
}
