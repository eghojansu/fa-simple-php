<?php

/**
 * Inspired by Composer/ClassLoader
 */
class Loader
{
    protected $dirs = [];

    /**
     * Add dir lookup
     *
     * @param string $dir
     */
    public function add($dir)
    {
        $this->dirs[] = rtrim(strtr($dir, '\\', '/'), '/').'/';

        return $this;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    /**
     * Unregisters this instance as an autoloader.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return bool|null True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            includeFile($file);

            return true;
        }
    }

    /**
     * Find file
     *
     * @param  string $class
     * @return string|boolean
     */
    protected function findFile($class)
    {
        $class = ltrim($class, '\\');
        $file = str_replace('\\', '/', $class);
        $ext = '.php';
        foreach ($this->dirs as $dir) {
            if (is_readable($f = $dir.$file.$ext) || is_readable($f = $dir.strtolower($file).$ext)) {
                return $f;
            }
        }

        return false;
    }
}

/**
 * Scope isolated include.
 *
 * Prevents access to $this/self from included files.
 */
function includeFile($file)
{
    include $file;
}
