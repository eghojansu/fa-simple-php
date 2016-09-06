<?php

namespace app\core;

/**
 * User class
 */
class User extends Magic
{
    protected $data;

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
     * Check user was login
     * @return bool
     */
    public function hasBeenLogin()
    {
        return $this->get('login');
    }

    /**
     * check user is login
     */
    public function isAnonym()
    {
        return !$this->hasBeenLogin();
    }

    /**
     * Check user role, can be used to check multiple role
     *
     * @param  string|string[]  $role
     * @return boolean
     */
    public function is($role)
    {
        $currentRole = $this->get('role');
        $roles = is_array($role) ? $role : explode(',', $role);
        $currentRoles = is_array($currentRole) ? $currentRole : explode(',', $currentRole);
        $intersection = array_intersect($roles, $currentRoles);

        return !empty($intersection);
    }

    /**
     * is complementer
     *
     * @param  string|string[]  $role
     * @return boolean
     */
    public function isNot($role)
    {
        return !$this->is($role);
    }

    protected function &getPool()
    {
        return $this->data;
    }
}
