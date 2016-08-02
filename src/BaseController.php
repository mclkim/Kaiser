<?php

namespace Kaiser;

use \Kaiser\Exception\ApplicationException;

class BaseController extends Singleton
{
    protected $container;

    function __construct($container = [])
    {
        if (is_array($container)) {
            $container = new Container ($container);
        }
        if (!$container instanceof ContainerInterface) {
            // exit ( 'Expected a ContainerInterface' );
            throw new \RuntimeException ('Expected a ContainerInterface');
        }
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    protected function setContainer($id, $value)
    {
        return $this->container->offsetSet($id, $value);
    }

    protected function config()
    {
        return $this->container->get('config');
    }

    protected function logger()
    {
        return $this->container->get('logger');
    }

    protected function request()
    {
        return $this->container->get('request');
    }

    protected function response()
    {
        return $this->container->get('response');
    }

    protected function router()
    {
        return $this->container->get('router');
    }

    public static function normalizeClassName($name)
    {
        $name = str_replace('/', '\\', $name);

        if (is_object($name))
            $name = get_class($name);

        $name = '\\' . ltrim($name, '\\');
        return $name;
    }

    protected function findController($controller, $action, $inPath)
    {
        $directory = is_array($inPath) ? $inPath : array(
            $inPath
        );

        /**
         * Workaround: Composer does not support case insensitivity.
         */
        if (!class_exists($controller)) {
            $controller = self::normalizeClassName($controller);
            foreach ($directory as $inPath) {
                $controllerFile = $inPath . strtolower(str_replace('\\', '/', $controller)) . '.php';
                if (file_exists($controllerFile)) {
                    include_once($controllerFile);
                    break;
                }
            }
        }

        if (!class_exists($controller)) {
//			return false;
            throw new ApplicationException (sprintf('The Class "%s" does not found', $controller));
        }

        $controllerObj = [
            new $controller ($this->container),
            $action
        ];

        if (is_callable($controllerObj)) {
            return $controllerObj;
        }

//		return false;
        throw new ApplicationException (sprintf("The Action '%s' is not found in the controller '%s'", $action, $controller));
    }
}