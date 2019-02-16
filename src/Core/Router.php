<?php

namespace Barbershop\Core;

use Barbershop\Controllers\ErrorController;
use Barbershop\Controllers\CustomerController;
use Barbershop\Utils\DependencyInjector;


class Router 
{
    protected $di;
    private $routeMap;
    private static $regexPatters = [
        "number" => "\d+",
        "string" => "\w"
    ];

    public function __construct(DependencyInjector $di) {
        $this->di = $di;
        $json = file_get_contents(__DIR__ . "/../Utils/config/routes.json");
        $this->routeMap = json_decode($json, true);
    }

    
    public function route(Request $request): string {
        $path = $request->getPath();
        foreach ($this->routeMap as $route => $info) {
            $regexRoute = $this->getRegexRoute($route, $info);
            if (preg_match("@^/$regexRoute$@", $path)) {
                return $this->executeController($route, $path, $info, $request);
            }    
        }    
        
        $errorController = new ErrorController($this->di, $request);
        return $errorController->notFound();
    }
    
    private function getRegexRoute(
        string $route,
        array $info
    ) : string {
        if (isset($info["params"])) {
             foreach ($info["params"] as $name=>$type) {
                $route = str_replace(
                    ":" . $name, self::$regexPatters[$type], $route
                );    
            }
        }
        
        return $route;
    }
    
    private function extractParams(
        string $route,
        string $path
    ): array {
        $params = [];
        
        $pathParts = explode("/", $path);
        $routerParts = explode("/", $route);
        
        foreach ($routerParts as $key=>$routePart) {
            if (strpos($routePart, ":") === 0) {
                $name = substr($routePart, 1);
                $params[$name] = $pathParts[$key+1];
            }
        }
        
        return $params;
    }
    private function executeController(
        string $route,
        string $path,
        array $info,
        Request $request
    ): string {
        $controllerName = "\Barbershop\Controllers\\"
            . $info["controller"] . "Controller";
        $controller = new $controllerName($this->di, $request);

        // if ($request->getCookies()->has("user")) {
        //     $cookie = $request->getCookies()->get("user");
        //     $controller->setCookie($cookie);
        // } else {
        //      setcookie("user");
        // }

        $params = $this->extractParams($route, $path);
        return call_user_func_array([$controller, $info["method"]], $params);
    }
}    