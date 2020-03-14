<?php

class Zend_Controller_Router_Rest_Route extends Zend_Controller_Router_Route {

  protected $method;

  public function __construct(
       $method, $route, $defaults = array(), $reqs = array(), Zend_Translate $translator = null, $locale = null
  ) {
    parent::__construct($route, $defaults, $reqs, $translator, $locale);
    $this->method = $method;
  }

  public function match($path, $partial = false){
    $request = new Zend_Controller_Request_Http();
    return mb_strtolower($request->getMethod()) === mb_strtolower($this->method) ? parent::match($path, $partial) : false;
  }
}
