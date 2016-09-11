<?php

namespace app\module;

use app\core\Controller;

class Error extends Controller
{
    protected $template = 'not-found';

    public function notFound(array $data = [])
    {
        return $this->render(null, $data + [
                'title'=>'404 - Halaman tidak ditemukan',
                'message'=>'Halaman yang anda minta tidak ditemukan!',
            ], [
                'HTTP/1.0 404 Not Found'
            ]);
    }

    public function notAllowed(array $data = [])
    {
        return $this->render(null, $data + [
                'title'=>'405 - Forbidden',
                'message'=>'Anda dilarang mengakses halaman ini!',
            ], [
                'HTTP/1.0 405 Forbidden'
            ]);
    }
}
