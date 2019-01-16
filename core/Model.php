<?php
namespace Core;

class Model {
  protected $_db, $_table, $_modelName, $_softDelete = false,$_validates=true,$_validationErrors=[];
  public $id;

  public function __construct($table) {
    $this->_db = DB::getInstance();
    $this->_table = $table;
    $this->_modelName = str_replace(' ', '', ucwords(str_replace('_',' ', $this->_table)));
  }

  public function get_columns() {
    return $this->_db->get_columns($this->_table);
  }

  protected function _softDeleteParams($params){
    if($this->_softDelete){
      if(array_key_exists('conditions',$params)){
        if(is_array($params['conditions'])){
          $params['conditions'][] = "deleted != 1";
        } else {
          $params['conditions'] .= " AND deleted != 1";
        }
      } else {
        $params['conditions'] = "deleted != 1";
      }
    }
    return $params;
  }

  public function find($params = []) {
    $params = $this->_softDeleteParams($params);
    $resultsQuery = $this->_db->find($this->_table, $params,get_class($this));
    if(!$resultsQuery) return [];
    return $resultsQuery;
  }

  public function findFirst($params = []) {
    $params = $this->_softDeleteParams($params);
    $resultQuery = $this->_db->findFirst($this->_table, $params,get_class($this));
    return $resultQuery;
  }

  public function findById($id) {
    return $this->findFirst(['conditions'=>"id = ?", 'bind' => [$id]]);
  }

  public function save() {
    $this->validator();
    if($this->_validates){
      $this->beforeSave();
      $fields = H::getObjectProperties($this);
      // determine whether to update or insert
      if(property_exists($this, 'id') && $this->id != '') {
        $save = $this->update($this->id, $fields);
        $this->afterSave();
        return $save;
      } else {
        $save = $this->insert($fields);
        $this->afterSave();
        return $save;
      }
    }
    return false;
  }

  public function insert($fields) {
    if(empty($fields)) return false;
    if(array_key_exists('id', $fields)) unset($fields['id']);
    return $this->_db->insert($this->_table, $fields);
  }

  public function update($id, $fields) {
    if(empty($fields) || $id == '') return false;
    return $this->_db->update($this->_table, $id, $fields);
  }

  public function delete($id = '') {
    if($id == '' && $this->id == '') return false;
    $id = ($id == '')? $this->id : $id;
    if($this->beforeDelete()){
      if($this->_softDelete) {
        $delete =  $this->update($id, ['deleted' => 1]);
      }
      $delete = $this->_db->delete($this->_table, $id);
      if($delete){
        $this->afterDelete();
      }
    } else {
      $delete = false;
    }
    return $delete;
  }

  public function query($sql, $bind=[]) {
    return $this->_db->query($sql, $bind);
  }

  public function data() {
    $data = new stdClass();
    foreach(H::getObjectProperties($this) as $column => $value) {
      $data->column = $value;
    }
    return $data;
  }

  public function assign($params) {
    if(!empty($params)) {
      foreach($params as $key => $val) {
        if(property_exists($this,$key)){
          $this->$key = $val;
        }
      }
      return true;
    }
    return false;
  }

  protected function populateObjData($result) {
    foreach($result as $key => $val) {
      $this->$key = $val;
    }
  }

  public function validator(){}

  public function runValidation($validator){
    $key = $validator->field;
    if(!$validator->success){
      $this->_validates = false;
      $this->_validationErrors[$key] = $validator->msg;
    }
  }

  public function getErrorMessages(){
    return $this->_validationErrors;
  }

  public function validationPassed(){
    return $this->_validates;
  }

  public function addErrorMessage($field,$msg){
    $this->_validates = false;
    $this->_validationErrors[$field] = $msg;
  }

  public function beforeSave(){}
  public function afterSave(){}

  /**
  * Runs before save needs to return a boolean
  * @return boolean
  **/
  public function beforeDelete(){
    return true;
  }
  public function afterDelete(){}

  public function timeStamps(){
    $now = date('Y-m-d H:i:s');
    $this->updated_at = $now;
    if(empty($this->id)){
      $this->created_at = $now;
    }
  }
}
