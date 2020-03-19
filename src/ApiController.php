<?php

class ApiController extends Zend_Controller_Action {

    public function responseJson($data, $status = 200) {
      $this->getHelper('Layout')
         ->disableLayout();

      $this->getHelper('ViewRenderer')
           ->setNoRender();

      $this->getResponse()
          ->setHttpResponseCode($status);

      $this->getResponse()
         ->setHeader('Content-Type', 'application/json');

      $this->getResponse()
         ->setHeader('Access-Control-Allow-Origin', isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*');

      $this->getResponse()
            ->setHeader('Access-Control-Allow-Credentials', 'true');

      echo Zend_Json::encode($data);

      return;
    }

    public function payload(){
      return Zend_Json::decode($this->getRequest()->getRawBody());
    }

    public function responseSuccess($data, $status = false) {
        return $this->responseJson([
          'success' => true,
          'data' => $data
        ], $status ? $status : 200);
    }

    public function responseError($errors, $status = false) {
        return $this->responseJson([
          'succes' => false,
          'errors' => $errors
        ], $status ? $status : 400);
    }
}
