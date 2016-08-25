<?php

namespace app;

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
            require $file;

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
        $class = ltrim(strtr($class, '\\', '/'), '/').'.php';
        foreach ($this->dirs as $dir) {
            $file = $dir.$class;
            if (is_file($file)) {
                return $file;
            } else {
                $lowerfile = strtolower($file);
                foreach (glob(dirname($file).'/*.php') as $file) {
                    if ($lowerfile === strtolower($file)) {
                        return $file;
                    }
                }
            }
        }

        return false;
    }
}
