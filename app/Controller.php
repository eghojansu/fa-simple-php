<?php

namespace app;

use app\core\Controller as ControllerBase;
use app\module\Error;

abstract class Controller extends ControllerBase
{
    protected function notFound(array $data = [])
    {
        $instance = $this->app->service(Error::class);

        return $this->app->call($instance, 'notFound', [$data]);
    }

    protected function notAllowed(array $data = [])
    {
        $instance = $this->app->service(Error::class);

        return $this->app->call($instance, 'notAllowed', [$data]);
    }
}
