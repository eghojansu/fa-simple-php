<?php

/**
 * User class
 */
class User extends Magic
{
    protected $data;
    protected $redirectOK = false;

    public function __construct()
    {
        @session_start();
        $key = App::instance()->get('session');
        if (empty($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        $this->data =& $_SESSION[$key];
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
            call_user_func_array([App::instance()->service->get('response'),'redirect'], func_get_args());
        }

        return $this;
    }

    protected function &getPool()
    {
        return $this->data;
    }
}