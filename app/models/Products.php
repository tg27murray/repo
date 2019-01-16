<?php
  namespace App\Models;
  use Core\Model;
  use Core\Validators\{RequiredValidator,NumericValidator};

  class Products extends Model {

    public $id, $created_at, $updated_at, $name, $price, $list, $shipping, $description, $deleted=0;

    public function __construct() {
      $table = 'products';
      parent::__construct($table);
    }

    public function beforeSave(){
      $this->timeStamps();
    }

    public function validator(){
      $requiredFields = ['name'=>"Name",'price'=>'Price','list'=>'List Price','shipping'=>'Shipping'];
      foreach($requiredFields as $field => $display){
        $this->runValidation(new RequiredValidator($this,['field'=>$field,'msg'=>$display." is required."]));
      }
      $this->runValidation(new NumericValidator($this,['field'=>'price','msg'=>'Price must be a number.']));
    }
  }
