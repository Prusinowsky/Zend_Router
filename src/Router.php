<?php

namespace Prusinowsky;

use Zend_Controller_Router_Rest_Route;
use Zend_Controller_Router_Rest_Route_Static;

class Router {

  protected $_routes = [];
  protected $_zendRoutes = [];

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
    $this->prepareZendRoutes();

    foreach ($this->_zendRoutes as $route) {
      $router->addRoute($route['name'], $route['core']);
    }
    
  }

  protected function prepareZendRoutes($routes = true){
    $routes = ($routes === true) ? $this->_routes : $routes;
    $this->_routes = [];

    foreach ($routes as $route) {
      $this->_routes = \array_merge($this->_routes, $this->smashIfChain($route));
    }

    foreach ($this->_routes as $route) {
      $this->pushZendRoutes($route['name'], $this->getZendRouteAbstract($route));
    }
  }

  protected function pushZendRoutes($name, $core){
    $this->_zendRoutes[] = ['name' => $name, 'core' => $core];
  }

  protected function smashIfChain($route){
    if(\array_key_exists('chains', $route)){
        $output = [];
        $head = $route;
        unset($head['chains']);
        $output[] = $head;
        foreach($route['chains'] as $chain){
          $temp = $head;
          $temp['name'] = $chain['name'];
          $temp = \array_merge($temp, ['chains' => [$chain]]);
          $output[] = $temp;
        }
        return $output;
    }
    return [$route];
  }

  protected function getZendRouteAbstract($route){

    if($route['method'] === 'all' && $route['type'] == 'STATIC'){
      return $this->getZendStaticRoute($route);
    } elseif ($route['type'] == 'STATIC') {
      return $this->getZendRestStaticRoute($route);
    }

    if($route['method'] === 'all' && $route['type'] == 'ROUTE'){
      return $this->getZendRoute($route);
    } elseif ($route['type'] == 'ROUTE') {
      return $this->getZendRestRoute($route);
    }

    if($route['type'] == 'HOSTNAME') {
      return $this->getZendHostname($route);
    }

    throw new \Zend_Exception("Unrecognized route type");
  }

  protected function getZendStaticRoute($route){
    $zendRoute = new \Zend_Controller_Router_Route_Static(
      $route['path'],
      $route['callback']
    );
    return $zendRoute;
  }

  protected function getZendRestStaticRoute($route){
    $zendRoute = new \Zend_Controller_Router_Rest_Route_Static(
      $route['method'],
      $route['path'],
      $route['callback']
    );
    return $zendRoute;
  }

  protected function getZendRoute($route){
    $zendRoute = new \Zend_Controller_Router_Route(
      $route['path'],
      $route['callback']
    );
    return $zendRoute;
  }

  protected function getZendRestRoute($route){
    $zendRoute = new \Zend_Controller_Router_Rest_Route(
      $route['method'],
      $route['path'],
      $route['callback']
    );
    return $zendRoute;
  }

  protected function getZendHostname($route){
    $zendRoute = new \Zend_Controller_Router_Route_Hostname(
      $route['path'],
      $route['callback']
    );
    if(\array_key_exists('chains', $route)){
      $zendRoute = $this->getZendChain($zendRoute, \array_key_exists('chains', $route) ? $route['chains'] : []);
    }
    return $zendRoute;
  }

  protected function getZendChain($zendRoute, $routes){
    foreach($routes as $route){
      return $zendRoute->chain($this->getZendRouteAbstract($route));
    }

  }
}
