<?php

/**
 * Main application class
 *
 * Simplify access to global vars,
 */
class App
{
    protected $data = [
        // hold output
        'quiet' => false,
        // stop after output
        'halt'  => true,
        // dont send header
        'headerOff' => false,
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
        'validation' => [
            'instanceOf'=>'Validation',
        ]
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
     * Register array variabel
     */
    public function register(array $data)
    {
        $this->data = array_replace_recursive($this->data, $data);

        return $this;
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
     * Get variable
     * @param  string $var     variable name
     * @param  mixed $default  default value if variable doesn't exists
     * @return mixed
     */
    public function get($var, $default = null)
    {
        return isset($this->data[$var])?$this->data[$var]:$default;
    }

    /**
     * Set variable
     * @param string $var variable name
     * @param mixed $val value
     */
    public function set($var, $val)
    {
        $this->data[$var] = $val;

        return $this;
    }

    /**
     * Check variable existance
     * @param  string $var variable name
     * @return bool
     */
    public function exists($var)
    {
        return (bool) isset($this->data[$var]);
    }

    /**
     * Remove variable
     * @param  string $var variable name
     */
    public function clear($var)
    {
        unset($this->data[$var]);

        return $this;
    }

    /**
     * Cut variable
     * @param  string $source variable name
     * @return mixed
     */
    public function cut($var)
    {
        $val = $this->get($var);
        $this->clear($var);

        return $val;
    }

    /**
     * Copy variable
     * @param  string $source variable name
     * @param  string $dest   variable name
     * @return mixed
     */
    public function copy($source, $dest)
    {
        $this->data[$dest] = $this->get($source);

        return $this;
    }

    /**
     * Append variable with other value, only for string type
     * @param  string $var variable name
     * @param  string $val value to append
     */
    public function append($var, $val)
    {
        $this->data[$var] = $this->get($var) . $val;

        return $this;
    }

    /**
     * Prepend variable with other value, only for string type
     * @param  string $var variable name
     * @param  string $val value to prepend
     */
    public function prepend($var, $val)
    {
        $this->data[$var] = $val . $this->get($var);

        return $this;
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
}