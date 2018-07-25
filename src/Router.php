<?php

namespace Mcl\Kaiser;

class Router
{
    const NOT_FOUND = 0;
    const FOUND = 1;
    const NOT_FOUND_ACTION = 2;
    const METHOD_NOT_ALLOWED = 3;

    private $AppMap;

    public function dispatch($url)
    {
        $route = new Route($url);
        $route->getRoute();

        $controller = $route->getController();
        $action = $route->getAction();
        $parameters = $route->getParameters();
        $map = $this->getAppMap();

        //TODO::
        return $this->findController($controller, $action, $parameters, $map);
    }

    function getAppMap()
    {
        return $this->AppMap;
    }

    function setAppMap($map = [])
    {
        $this->AppMap = is_array($map) ? $map : ["" => $map];;
    }

    protected function findController($controller, $action, $parameters, $map)
    {
        foreach ($map as $prefix => $path) {
            $classname = trim($prefix, '\\') . '/' . trim($controller, '/');
            $classname = self::normalizeClassName($classname);
            /**
             * TODO::UNIX 시스템에서 파일이름의 대소문자 구별한다.(2016-12-02)
             */
            if (!class_exists($classname)) {
                $controllerFile = trim($path, '/') . '/' . trim($controller, '/') . '.php';
                $controllerFile = realpath($controllerFile);
                if (file_exists($controllerFile)) {
                    include_once($controllerFile);
                    break;
                }
            }
        }

        if (!class_exists($classname)) {
            return [self::NOT_FOUND, $classname, $action, $parameters];
        }

        if (!is_callable([new $classname, $action])) {
            return [self::NOT_FOUND_ACTION, $classname, $action, $parameters];
        }

        return [self::FOUND, $classname, $action, $parameters];
    }

    public static function normalizeClassName($name)
    {
        $name = str_replace('/', '\\', $name);

        if (is_object($name))
            $name = get_class($name);

        $name = '\\' . ltrim($name, '\\');
        return $name;
    }
}