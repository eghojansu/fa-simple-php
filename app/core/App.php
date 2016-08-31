<?php

namespace app\core;

use ReflectionMethod;

/**
 * Main application class
 *
 * Simplify access to global vars,
 */
class App extends Magic
{
    const PACKAGE = 'eghojansu/fa-simple-php';
    const VERSION = '2.1.1';

    protected $data = [
        // hold output
        'quiet' => false,
        // stop after output
        'halt'  => true,
        // dont send header
        'headerOff' => false,
        // show file entry in url
        'showEntryFile'=>false,
        'continueOnDBError'=>true,
        // controller namespace
        'controllerNamespace'=>'app\\module\\',
        // main controller
        'controllerDefault'=>'app\\module\\IndexController',
        // error handler
        'controllerError'=>'app\\module\\Error',
        // controller suffix
        'controllerSuffix'=>'Controller',
        // main controller method
        'controllerDefaultMethod'=>'main',
    ];
    // default services
    protected $rules = [
        HTML::class => [
            'shared'=>true,
        ],
        Request::class => [
            'shared'=>true,
        ],
        Response::class => [
            'shared'=>true,
        ],
        User::class => [
            'shared'=>true,
        ],
        Helper::class => [
            'shared'=>true,
        ],
        BatchInsert::class => [
            'substitutions'=>['Database'=>['instance'=>Database::class]]
        ],
    ];
    protected $assetRoot;
    protected $service;
    private static $instance;

    public function __construct()
    {
        $this->data['templatePath'] = Helper::fixSlashes(dirname(__DIR__).'/template');
    }


    /**
     * Get instance
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Get service instance
     * @return mixed
     */
    public function service()
    {
        if (!$this->service) {
            $this->service = new Service;
            foreach ($this->rules as $key => $value) {
                $this->service->addRule($key, $value);
            }
        }

        if ($args = func_get_args()) {
            return call_user_func_array([$this->service, 'get'], $args);
        }

        return $this->service;
    }

    /**
     * Activate debug mode
     * @return  mixed
     */
    public function debug($status = null)
    {
        if (is_null($status)) {
            return $this->get('debug');
        }

        return $this->set('debug', $status);
    }

    /**
     * Read file
     * @param  string $file
     * @return string
     */
    public function read($file)
    {
        return @file_get_contents($file)?:null;
    }

    /**
     * Write to file
     * @param  string $file
     * @param  string $content
     * @param  boolean $append
     */
    public function write($file, $content, $append = false)
    {
        return @file_put_contents($file, $content, $append?FILE_APPEND:0);
    }

    /**
     * Load array variabel from file
     * @return  array
     */
    public function load($file)
    {
        if (is_readable($file)) {
            $data = require($file);

            return $data ?: [];
        }

        return [];
    }

    /**
     * Generate url
     * @param  string $path   Can use {param} to dinamically replace param
     *                        if param not exists in path, it will be appended
     *                        as query
     * @param  array  $params
     * @param  boolean $absolute
     * @return string
     */
    public function url($path = null, array $params = [], $absolute = false)
    {
        $pattern = '/\{(\w+)\}/';
        if (preg_match_all($pattern, $path, $matches)) {
            $replace = [];
            foreach ($matches[1] as $key) {
                $replace[] = isset($params[$key])?$params[$key]:null;
                unset($params[$key]);
            }
            $path = str_replace($matches[0], $replace, $path);
        }

        $request = $this->service(Request::class);
        $url = $absolute ?
            ($path?$request->baseUrl():$request->currentUrl()):
            ($path?$request->basePath():$request->currentPath(!$this->data['showEntryFile']));
        $url  .= ltrim($path, '/')
              . ($params?'?'.http_build_query($params):'');

        return $url;
    }

    /**
     * Generate asset url
     * @param  string $path
     * @return string
     */
    public function asset($path)
    {
        if (empty($this->assetRoot)) {
            $this->assetRoot = $this->service(Request::class)->basePath();
        }

        return $this->assetRoot.ltrim($path, '/');
    }

    /**
     * Return data
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Call object method
     *
     * @param  object $object
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function call($object, $method, array $args)
    {
        $mref = new ReflectionMethod($object, $method);
        $params = $mref->getParameters();
        $newArgs = [];
        foreach ($params as $key => $param) {
            $pclass = $param->getClass();
            $newArgs[] = $pclass?
                $this->service($pclass->name):
                ($args ? array_shift($args) : $param->getDefaultValue());
        }
        $args = array_merge($newArgs, $args);

        return call_user_func_array([$object, $method], $args);
    }

    protected function &getPool()
    {
        return $this->data;
    }
}
