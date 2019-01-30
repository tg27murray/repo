<?php
  namespace App\Models;
  use Core\Model;
  use Core\Validators\{RequiredValidator,NumericValidator};

  class Products extends Model {

    public $id, $created_at, $updated_at, $name, $price, $list, $shipping, $body, $deleted=0;
    const blackList = ['id','deleted'];
    protected static $_table = 'products';
    protected static $_softDelete = true;

    public function beforeSave(){
      $this->timeStamps();
    }

    public function validator(){
      $requiredFields = ['name'=>"Name",'price'=>'Price','list'=>'List Price','shipping'=>'Shipping','body'=>'Body'];
      foreach($requiredFields as $field => $display){
        $this->runValidation(new RequiredValidator($this,['field'=>$field,'msg'=>$display." is required."]));
      }
      $this->runValidation(new NumericValidator($this,['field'=>'price','msg'=>'Price must be a number.']));
      $this->runValidation(new NumericValidator($this,['field'=>'list','msg'=>'List Price must be a number.']));
      $this->runValidation(new NumericValidator($this,['field'=>'shipping','msg'=>'Shipping must be a number.']));
    }
  }
