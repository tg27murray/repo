<?php
namespace App\Models;
use Core\{Model,Session,Cookie,H};
use Core\Validators\RequiredValidator;
use App\Models\{Carts,Products,ProductOptionRefs};

class CartItems extends Model {

  public $id,$created_at,$updated_at,$cart_id,$product_id,$qty=0,$option_id,$deleted=0;
  protected static $_table = 'cart_items';
  protected static $_softDelete = true;

  public function beforeSave(){
    $this->timeStamps();
  }

  public static function findByProductIdOrCreate($cart_id,$product_id,$option_id){
    $item = self::findFirst([
      'conditions' => "cart_id = ? AND product_id = ? AND option_id = ?",
      'bind' => [$cart_id,$product_id,$option_id]
    ]);
    if(!$item){
      $item = new self();
      $item->cart_id = $cart_id;
      $item->product_id = $product_id;
      $item->option_id = $option_id;
      // $item->save();
    }
    return $item;
  }

  public static function addProductToCart($cart_id,$product_id,$option_id=null){
    $product = Products::findById((int)$product_id);
    if($product){
      $item = self::findByProductIdOrCreate($cart_id,$product_id,$option_id);
      // validate to make sure there is an option selected if necessary
      if($item->qty >= $item->qtyAvailable()){
        $item->addErrorMessage('option_id','You have reached the maximum available');
      }
      if($product->hasOptions() && empty($option_id)){
        $item->addErrorMessage('option_id','You must choose an option.');
      }
    }
    return $item;
  }

  public function qtyAvailable(){
    $available = 0;
    $model = (!empty($this->option_id))? ProductOptionRefs::findByProductId($this->product_id,$this->option_id) : Products::findById($this->product_id);
    if($model){
      $available = $model->inventory;
    }
    return $available;
  }

}
