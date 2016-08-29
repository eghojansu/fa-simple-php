<?php

namespace app\core;

use LogicException;

class Controller
{
    protected $app;
    protected $template;
    protected $templatePath;
    protected $viewPath;
    protected $extension = '.php';
    protected $services = [
        'user'=>User::class,
        'database'=>Database::class,
        'request'=>Request::class,
        'response'=>Response::class,
        'form'=>Form::class,
        'html'=>HTML::class,
        'helper'=>Helper::class,
        'validation'=>Validation::class,
    ];

    public function __construct()
    {
        $class = get_called_class();
        $nsp = substr($class, 0, strrpos($class, '\\'));
        $this->app = App::instance();
        $this->templatePath = $this->app->get('templatePath');
        $this->viewPath = Helper::fixSlashes(dirname(dirname(__DIR__)).'/'.$nsp.'/view');
    }

    public function __get($var)
    {
        if (isset($this->services[$var])) {
            return $this->app->service($this->services[$var]);
        }

        throw new LogicException("Service '$var' not found");
    }

    protected function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    protected function redirect($path = null, array $param = [])
    {
        $response = $this->response
            ->addHeader('location', $this->app->url($path, $param))
            ->clearContent()
        ;

        return $response;
    }

    protected function json($data)
    {
        $response = $this->response
            ->addHeader('Content-type', 'application/json')
            ->setContent(is_string($data) ? $data : json_encode($data))
        ;

        return $response;
    }

    protected function render($_view = null, array $_data = [], array $_headers = [])
    {
        $pageTitle = $this->app->get('name');
        extract($_data);
        $_content = null;

        if ($_view) {
            ob_start();
            require $this->viewPath.$_view.$this->extension;
            $_content = ob_get_clean();
        }

        if ($this->template) {
            ob_start();
            require $this->templatePath.$this->template.$this->extension;
            $_content = ob_get_clean();
        }

        $response = $this->response
            ->addHeader('Content-type', 'text/html')
            ->addHeaders($_headers)
            ->setContent($_content)
        ;

        return $response;
    }

    protected function csv($filename, array $headers, array $data, $delimiter = ';', $delay = true)
    {
        $temp = tempnam(sys_get_temp_dir(), 'csv');
        $fp = fopen($temp, 'wb');
        if ($fp) {
            //add BOM to fix UTF-8 in Excel
            fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            fputcsv($fp, $headers, $delimiter);
            foreach ($data as $datum) {
                fputcsv($fp, $datum, $delimiter);
            }
            fclose($fp);
        }

        $response = $this->response
            ->addHeader('Pragma', 'public')
            ->addHeader('Expires', '0')
            ->addHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->addHeader('Content-Description', 'File Transfer')
            ->addHeader('Content-Type', 'text/x-csv')
            ->addHeader('Content-Disposition: attachment; filename="'.$filename.'.csv";')
            ->addHeader('Content-Transfer-Encoding: binary')
        ;

        if ($delay) {
            $response->setContent($this->app->read($temp));
        } else {
            $response->sendHeader();
            readfile($temp);
        }

        unlink($temp);

        return $response;
    }
}
