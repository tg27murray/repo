<?php

abstract class CustomValidator {
  public $success=true, $msg="", $field, $rule;
  protected $_model;

  public function __construct($model,$params){
    $this->_model = $model;
    if(!array_key_exists('field',$params)){
      throw new Exception("You must add a field to the params array.");
    } else {
      $this->field = (is_array($params['field']))?$params['field'][0] : $params['field'];
    }

    if(!property_exists($model,$this->field)){
      throw new Exception("The field must exist as a property on the model.");
    }

    if(!array_key_exists('msg',$params)){
      throw new Exception("You must add a msg to the params array.");
    } else {
      $this->msg = $params['msg'];
    }

    if(array_key_exists('rule',$params)){
      $this->rule = $params['rule'];
    }

    try {
      $this->success = $this->runValidation();
      // return $this;
    } catch (Exception $e){
      echo "Validation Exception on ". get_class() .": " . $e->getMessage() . "\n";
    }
  }

  abstract public function runValidation();
}
