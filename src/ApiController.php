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

      echo Zend_Json::encode($data);

      return;
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
