<?php

/**
 * User class
 */
class User
{
    protected $data;
    protected $redirectOK = false;

    public function __construct()
    {
        @session_start();
        $key = App::$instance->get('session');
        if (empty($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        $this->data =& $_SESSION[$key];
    }

    /**
     * Register data
     * @param  array  $data
     */
    public function register(array $data)
    {
        $this->data = array_replace_recursive($this->data, $data);

        return $this;
    }

    /**
     * Get var
     * @param  string $var
     * @return  mixed
     */
    public function get($var)
    {
        return isset($this->data[$var])?$this->data[$var]:null;
    }

    /**
     * Set var
     * @param string $var
     * @param mixed $val
     */
    public function set($var, $val)
    {
        $this->data[$var] = $val;

        return $this;
    }

    /**
     * Check var existance
     * @param  string $var
     * @return bool
     */
    public function exists($var)
    {
        return isset($this->data[$var]);
    }

    /**
     * Clear var
     * @param  string $var
     */
    public function clear($var)
    {
        unset($this->data[$var]);

        return $this;
    }

    /**
     * Check var content is equal
     * @param  string  $var
     * @param  string  $val
     * @return boolean
     */
    public function is($var, $val)
    {
        return $this->get($var) === $val;
    }

    /**
     * Cut var
     * @param  string $var
     * @return mixed
     */
    public function cut($var)
    {
        $val = $this->get($var);
        $this->clear($var);

        return $val;
    }

    /**
     * Copy var
     * @param  string $source var name
     * @param  string $dest   var name
     */
    public function copy($source, $dest)
    {
        $this->data[$dest] = $this->get($source);

        return $this;
    }

    /**
     * Append val to $var
     * @param  string $var
     * @param  string $val
     */
    public function append($var, $val)
    {
        $this->data[$var] = $this->get($var) . $val;

        return $this;
    }

    /**
     * Prepend val to $var
     * @param  string $var
     * @param  string $val
     */
    public function prepend($var, $val)
    {
        $this->data[$var] = $val . $this->get($var);

        return $this;
    }

    /**
     * Check user was login
     * @return bool
     */
    public function wasLogin()
    {
        return $this->get('login');
    }

    /**
     * login user
     * @param  string $role
     * @param  array  $data
     */
    public function login($role, array $data)
    {
        $this->set('login', true);
        $this->set('role', $role);
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * logout user
     */
    public function logout()
    {
        $this->data = [];

        return $this;
    }

    /**
     * Set or get flash message
     * @param  string $var
     * @param  mixed $val
     * @return mixed
     */
    public function message($var, $val = null)
    {
        if (!is_null($val)) {
            return $this->set($var,$val);
        }

        $data = $this->get($var);
        $this->clear($var);

        return $data;
    }

    /**
     * check user is login
     */
    public function mustLogin()
    {
        $this->redirectOK = !$this->wasLogin();

        return $this;
    }

    /**
     * check user is anonym
     */
    public function mustAnonym()
    {
        $this->redirectOK = $this->wasLogin();

        return $this;
    }

    /**
     * check user role was equal
     */
    public function must($role)
    {
        $ok = false;
        if (is_array($role)) {
            foreach ($role as $r) {
                $ok |= $this->is('role', $r);
            }
        } else {
            $ok = $this->is('role', $role);
        }
        $this->redirectOK = !$ok;

        return $this;
    }

    /**
     * Redirect
     * @see  Response::redirect
     */
    public function orRedirect()
    {
        if ($this->redirectOK) {
            call_user_func_array([App::$instance->service->get('response'),'redirect'], func_get_args());
        }

        return $this;
    }
}