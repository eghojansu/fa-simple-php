<?php

namespace app\module;

use app\Controller;

class ErrorController extends Controller
{
    public function notFound()
    {
        return $this->render('not-found', null, [
                'title'=>'404 - Halaman tidak ditemukan',
                'message'=>'Halaman yang anda minta tidak ditemukan!',
            ], [
                'HTTP/1.0 404 Not Found'
            ]);
    }
    public function notAllowed()
    {
        return $this->render('not-found', null, [
                'title'=>'405 - Forbidden',
                'message'=>'Anda dilarang mengakses halaman ini!',
            ], [
                'HTTP/1.0 405 Forbidden'
            ]);
    }
}
