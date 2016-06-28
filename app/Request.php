<?php

/**
 * Request class
 */
class Request
{
    protected $baseUrl;

    /**
     * Check request method if it's post
     * @return boolean
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if it's an XMLHttpRequest / ajax
     * @return boolean
     */
    public function isXMLHttpRequest()
    {
        return empty($_SERVER['X-Requested-With'])?false:(
            'XMLHttpRequest'===$_SERVER['X-Requested-With']);
    }

    /**
     * Get data from global $_POST var
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($_POST[$name])?$_POST[$name]:$default;
    }

    /**
     * Get data from global $_GET var
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function query($name, $default = null)
    {
        return isset($_GET[$name])?$_GET[$name]:$default;
    }

    /**
     * Get global $_POST
     * @param  array  $filter field to skip
     * @return array
     */
    public function data($filter = [])
    {
        if (!is_array($filter)) {
            $filter = array_filter(explode(',', str_replace(' ', '', $filter)));
        }

        $data = array_intersect_key($_POST, array_flip($filter));

        return $data;
    }

    /**
     * Get global $_GET
     * @param  array  $filter field to skip
     * @return array
     */
    public function params($filter = [])
    {
        if (!is_array($filter)) {
            $filter = array_filter(explode(',', str_replace(' ', '', $filter)));
        }

        $data = array_intersect_key($_GET, array_flip($filter));

        return $data;
    }

    /**
     * Get current url
     * @return string
     */
    public function currentUrl()
    {
        $currentUrl = $this->baseUrl()
                    . $this->currentPath();

        return $currentUrl;
    }

    /**
     * Get base url
     * @return string
     */
    public function baseUrl()
    {
        if (empty($this->baseUrl)) {
            $this->baseUrl = 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']?'s':'')
                     . '://'
                     . $_SERVER['SERVER_NAME']
                     . rtrim($this->basePath(), '/')
                     . (1*$_SERVER['SERVER_PORT'] === 80?'':':'.$_SERVER['SERVER_PORT'])
                     . '/'
                     ;
        }

        return $this->baseUrl;
    }

    /**
     * Get current path
     * @return string
     */
    public function currentPath()
    {
        $cp = str_replace($this->basePath(), '', $_SERVER['REQUEST_URI']);
        $cp = explode('?', $cp);
        $cp = $cp[0];

        return $cp;
    }

    /**
     * Get base path
     * @return string
     */
    public function basePath()
    {
        $basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->baseDir());

        return $basePath;
    }

    /**
     * get base dir
     * @return string
     */
    public function baseDir()
    {
        return Helper::fixSlashes(dirname($_SERVER['SCRIPT_FILENAME']));
    }
}