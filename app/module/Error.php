<?php

namespace app\module;

use app\core\Controller;

class Error extends Controller
{
    protected $template = 'not-found';

    public function notFound()
    {
        return $this->render(null, [
                'title'=>'404 - Halaman tidak ditemukan',
                'message'=>'Halaman yang anda minta tidak ditemukan!',
            ], [
                'HTTP/1.0 404 Not Found'
            ]);
    }

    public function notAllowed()
    {
        return $this->render(null, [
                'title'=>'405 - Forbidden',
                'message'=>'Anda dilarang mengakses halaman ini!',
            ], [
                'HTTP/1.0 405 Forbidden'
            ]);
    }
}
