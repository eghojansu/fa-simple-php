<?php

namespace app\core;

/**
 * User class
 */
class User extends Magic
{
    protected $data;
    protected $rolePointer;

    public function __construct()
    {
        @session_start();
        $key = App::instance()->get('session');
        if (empty($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        $this->data =& $_SESSION[$key];
        $this->rolePointer = $this->rolePointer?:$this->get('lastRole');
    }

    /**
     * Get user data
     * @param  string|null $role
     * @return array
     */
    public function data($role = null)
    {
        $login = $this->get('login')?:[];
        $role = false === $role? null: ($role ?: $this->rolePointer);
        if ($role) {
            if (isset($login[$role])) {
                $data = [];
            }
            else {
                $data = $login[$role];
            }
        }
        else {
            $data = $login;
        }

        return $data;
    }

    /**
     * Set Role
     * @param string $role
     * @return  object $this
     */
    public function setRolePointer($role)
    {
        $this->rolePointer = $role;

        return $this;
    }

    /**
     * login user
     * @param  string $role
     * @param  array  $data
     */
    public function login($role, array $data)
    {
        if (!isset($this->data['login'])) {
            $this->data['login'] = [];
        }
        $this->data['login'][$role] = $data;
        $this->data['lastRole'] = $role;
        $this->rolePointer = $role;

        return $this;
    }

    /**
     * logout user
     */
    public function logout($role = null)
    {
        $role = false === $role? null: ($role ?: $this->rolePointer);
        unset($this->data['login'][$role]);

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
        return !empty($this->data['login']);
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
        $roles = is_array($role) ? $role : explode(',', $role);
        $currentRoles = isset($this->data['login']) ? array_keys($this->data['login']) : [];
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
        if ($this->rolePointer && isset($this->data['login']) && isset($this->data['login'][$this->rolePointer])) {
            $data =& $this->data['login'][$this->rolePointer];

            return $data;
        }

        return $this->data;
    }
}
