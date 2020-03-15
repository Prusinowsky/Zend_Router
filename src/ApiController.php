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

    public function responseSuccess($data) {
        return $this->responseJson([
          'success' => true,
          'data' => $data
        ], 200);
    }

    public function responseError($errors) {
        return $this->responseJson([
          'succes' => false,
          'errors' => $errors
        ], 400);
    }
}
