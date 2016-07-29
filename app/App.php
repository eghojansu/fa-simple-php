<?php

/**
 * Main application class
 *
 * Simplify access to global vars,
 */
class App extends Magic
{
    protected $data = [
        // hold output
        'quiet' => false,
        // stop after output
        'halt'  => true,
        // dont send header
        'headerOff' => false,
        // show file entry in url
        'showEntryFile'=>false,
    ];
    // default services
    protected $rules = [
        'html' => [
            'instanceOf'=>'HTML',
            'shared'=>true,
        ],
        'request' => [
            'instanceOf'=>'Request',
            'shared'=>true,
        ],
        'response' => [
            'instanceOf'=>'Response',
            'shared'=>true,
        ],
        'user' => [
            'instanceOf'=>'User',
            'shared'=>true,
        ],
        'model' => [
            'instanceOf'=>'Model',
            'shared'=>true,
            'substitutions'=>['Database'=>['instance'=>'database']]
        ],
        'validation' => [
            'instanceOf'=>'Validation',
        ],
        'form' => [
            'instanceOf'=>'Form',
        ],
        'batchInsert' => [
            'instanceOf'=>'BatchInsert',
            'substitutions'=>['Database'=>['instance'=>'database']]
        ],
    ];
    protected $assetRoot;
    public    $service;
    private static $instance;


    /**
     * Construct
     * @return Service
     */
    public function __construct()
    {
        $this->service = new Service;
        $this->registerServices($this->rules);
        $this->data['modulePath'] = Helper::fixSlashes(__DIR__.'/modules/');
        $this->data['templatePath'] = Helper::fixSlashes(__DIR__.'/template/');
    }

    /**
     * Get instance
     */
    public function instance()
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
        if ($args = func_get_args()) {
            return call_user_func_array([$this->service, 'get'], $args);
        }

        return $this->service;
    }

    /**
     * Activate debug mode
     */
    public function debug()
    {
        return $this->set('debug', true);
    }

    /**
     * Register services
     * @param  array  $rules
     * @see  Level-2/Dice
     */
    public function registerServices(array $rules)
    {
        foreach ($rules as $key => $value) {
            $this->service->addRule($key, $value);
        }

        return $this;
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
     * @return string
     */
    public function url($path = null, array $params = [])
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

        $url  = ($path?$this->service->get('request')->baseUrl():
                    $this->service->get('request')->currentUrl())
              . ltrim($path, '/')
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
            $this->assetRoot = $this->service->get('request')->basePath();
        }

        return $this->assetRoot.ltrim($path, '/');
    }

    /**
     * Get path
     * @param  string $path
     * @return string
     */
    public function urlPath($path)
    {
        $pos = strpos($path, '.');
        if (false !== $pos) {
            $path = substr($path, 0, $pos);
        }

        return str_replace($this->data['modulePath'], '', $path);
    }

    protected function &getPool()
    {
        return $this->data;
    }
}