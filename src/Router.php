<?php

namespace Prusinowsky;

use Zend_Debug;
use Zend_Controller_Router_Rest_Route;
use Zend_Controller_Router_Rest_Route_Static;
use isArray;

class Router {

  protected $_routes = [];

  public function __constructor(){

  }

  public function loadRoutes($routerPaths){
    if(\is_array($routerPaths))
      foreach ($routerPaths as $path) {
        $routerCollector = include $path;
        $this->_routes = \array_merge($this->_routes, $routerCollector->getRoutes());
      }
    elseif(\is_string($routerPaths))
      $this->loadRoutes([$routerPaths]);
    else
      throw new \Zend_Exception("Error during loading Routes");
  }

  public function apply($router){
    foreach ($this->_routes as $route) {
      if($route['method'] === 'all' && $route['type'] == 'STATIC'){
        $zendRoute = new \Zend_Controller_Router_Route_Static(
          $route['path'],
          $route['callback']
        );
        $router->addRoute($route['name'], $zendRoute);
      } elseif ($route['type'] == 'STATIC') {
        $zendRoute = new \Zend_Controller_Router_Rest_Route_Static(
          $route['method'],
          $route['path'],
          $route['callback']
        );
        $router->addRoute($route['name'], $zendRoute);
      }

      if($route['method'] === 'all' && $route['type'] == 'ROUTE'){
        $zendRoute = new \Zend_Controller_Router_Route(
          $route['path'],
          $route['callback']
        );
        $router->addRoute($route['name'], $zendRoute);
      } elseif ($route['type'] == 'ROUTE') {
        $zendRoute = new \Zend_Controller_Router_Rest_Route(
          $route['method'],
          $route['path'],
          $route['callback']
        );
        $router->addRoute($route['name'], $zendRoute);
      }
    }
  }
}
