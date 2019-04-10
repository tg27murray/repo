<?php
  namespace App\Controllers;
  use Core\{Controller,Cookie,H};
  use App\Models\{Products,Carts,CartItems};

  class CartController extends Controller {

    public function indexAction() {
      $this->view->render('cart/index');
    }

    public function addToCartAction($product_id){
      $cart = Carts::findCurrentCartOrCreateNew();
      $item = CartItems::addProductToCart($cart->id,(int)$product_id);
      $item->qty = $item->qty + 1;
      $item->save();
      $this->view->render('cart/addToCart');
    }
  }
