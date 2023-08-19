<?php

namespace Prusinowsky;

class RouterCollector {
  private static $counter = 0;
  protected $_routesParms = [];

  public function __constructor(){

  }

  public function hostname($path, $callback){
    $this->pushRoute('all', 'HOSTNAME', $path, $this->formatCallback($callback));
    return $this;
  }

  public function any($path, $callback){
    return $this->match(['get', 'post', 'put', 'patch', 'delete'], $path, $callback);
  }

  public function get($path, $callback){
    return $this->match(['get'], $path, $callback);
  }

  public function post($path, $callback){
    return $this->match(['post'], $path, $callback);
  }

  public function put($path, $callback){
    return $this->match(['put'], $path, $callback);
  }

  public function patch($path, $callback){
    return $this->match(['patch'], $path, $callback);
  }

  public function delete($path, $callback){
    return $this->match(['delete'], $path, $callback);
  }

  public function group($callback){
    $this->_routesParms[\count($this->_routesParms)-1]['chains']
      = $callback(new RouterCollector())->getRoutes();
    return $this;
  }

  public function match(
    $methods, $path, $callback
  ){
    $methods = $this->getMethods($methods);
    foreach ($methods as $method) {
      $this->pushRoute(
        $method,
        $this->getRouteType($path),
        $path,
        $this->formatCallback($callback)
      );
    }
    return $this;
  }

  public function name($name){
    $this->_routesParms[\count($this->_routesParms)-1]['name'] = $name;
    return $this;
  }

  public function defaults($defaultArr){
    $this->_routesParms[\count($this->_routesParms)-1]['callback']
      = \array_merge($this->_routesParms[\count($this->_routesParms)-1]['callback'], $defaultArr);
    return $this;
  }

  protected function pushRoute($method, $type, $path, $callback){
    $this->_routesParms[] = [
      'name' => "route-".(self::$counter++),
      'method' => $method,
      'type' => $type,
      'path' => $path,
      'callback' => $callback
    ];
  }

  protected function getMethods($methods){
    return \count(\array_diff(['get', 'post', 'put', 'patch', 'delete'], $methods))
            ? [$methods[0]] : ['all'];
  }

  protected function getRouteType($path){
    return (\strpos($path, ':') !== false) ? 'ROUTE' : 'STATIC';
  }

  protected function formatCallback($callback)
  {
    if(\is_string($callback)){
      return $this->formatStringAsCallback($callback);
    } elseif(\is_array($callback)) {
      return $callback;
    }
    else {
      throw new \Zend_Exception("Bad callback from string");
    }
  }

  protected function formatStringAsCallback($callback){
    if(\strpos($callback, '#') !== false)
      list($module, $rest) = \explode('#',$callback);
    else
      list($module, $rest) = ['default', $callback];

    if(\strpos($callback, '@') !== false)
      list($controller, $action) = \explode('@', $rest);
    else
      throw new \Zend_Exception("Bad callback for route");

    $controller = str_replace("Controller", "", $controller);

    return [
      'module' => \mb_strtolower($module),
      'controller' => \mb_strtolower($controller),
      'action' => \mb_strtolower($action)
    ];
  }

  public function getRoutes(){
    return $this->_routesParms;
  }

}
