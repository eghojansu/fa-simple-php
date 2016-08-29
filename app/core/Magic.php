<?php

namespace app\core;

abstract class Magic
{
    /**
     * Get variables pool connection
     * @return array
     */
    abstract protected function &getPool();

    /**
     * Get variable
     * @param  string $var     variable name
     * @param  mixed $default  default value if variable doesn't exists
     * @return mixed
     */
    public function get($var, $default = null)
    {
        $pool = $this->getPool();

        return isset($pool[$var])?$pool[$var]:$default;
    }

    /**
     * Set variable
     * @param string $var variable name
     * @param mixed $val value
     */
    public function set($var, $val)
    {
        $pool =& $this->getPool();
        $pool[$var] = $val;


        return $this;
    }

    /**
     * Check variable existance
     * @param  string $var variable name
     * @return bool
     */
    public function exists($var)
    {
        $pool = $this->getPool();

        return (bool) isset($pool[$var]);
    }

    /**
     * Remove variable
     * @param  string $var variable name
     */
    public function clear($var)
    {
        $pool =& $this->getPool();
        unset($pool[$var]);

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
        return $this->set($dest, $this->get($source));
    }

    /**
     * Append variable with other value, only for string type
     * @param  string $var variable name
     * @param  string $val value to append
     */
    public function append($var, $val)
    {
        return $this->set($var, $this->get($var) . $val);
    }

    /**
     * Prepend variable with other value, only for string type
     * @param  string $var variable name
     * @param  string $val value to prepend
     */
    public function prepend($var, $val)
    {
        return $this->set($var, $val . $this->get($var));
    }

    /**
     * Register data
     * @param  array  $data
     */
    public function register(array $data)
    {
        $pool =& $this->getPool();
        $pool = array_replace_recursive($pool, $data);

        return $this;
    }
}
