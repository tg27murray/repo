<?php
  namespace App\Controllers;
  use Core\{Controller,Cookie,H, Session, Router};
  use App\Models\{Products,Carts,CartItems, Transactions};
  use App\Lib\Gateways\Gateway;

  class CartController extends Controller {

    public function indexAction() {
      $cart_id = (Cookie::exists(CART_COOKIE_NAME))? Cookie::get(CART_COOKIE_NAME): false;
      $itemCount = 0;
      $subTotal = 0.00;
      $shippingTotal = 0.00;
      $items = Carts::findAllItemsByCartId((int)$cart_id);//H::dnd($items);
      foreach($items as $item){
        $itemCount += $item->qty;
        $shippingTotal += ($item->qty * $item->shipping);
        $subTotal += ($item->qty * $item->price);
      }
      $this->view->subTotal = number_format($subTotal,2);
      $this->view->shippingTotal = number_format($shippingTotal, 2);
      $this->view->grandTotal = number_format($subTotal + $shippingTotal, 2);
      $this->view->itemCount = $itemCount;
      $this->view->items = $items;
      $this->view->cartId = $cart_id;
      $this->view->render('cart/index');
    }

    public function addToCartAction($product_id){
      if($this->request->isPost()){
        $this->request->csrfCheck();
        $cart = Carts::findCurrentCartOrCreateNew();
        $item = CartItems::addProductToCart($cart->id,(int)$product_id,(int)$this->request->get('option_id'));
        $errors = $item->getErrorMessages();
        if(empty($errors)){
          $item->qty = $item->qty + 1;
          $item->save();
        } else {
          Session::addMsg('danger','You must choose an option.');
          Router::redirect('products/details/'.$product_id);
        }
        $this->view->render('cart/addToCart');
      }
    }

    public function changeQtyAction($direction,$item_id){
      $item = CartItems::findById((int)$item_id);
      if($direction == 'down'){
        $item->qty -= 1;
      } else {
        $item->qty += 1;
      }

      if($item->qty > 0){
        $item->save();
      }
      Session::addMsg('info',"Cart Updated");
      Router::redirect('cart');
    }

    public function removeItemAction($item_id){
      $item = CartItems::findById((int)$item_id);
      $item->delete();
      Session::addMsg('info',"Cart Updated");
      Router::redirect('cart');
    }

    public function checkoutAction($cart_id){
      $gw = Gateway::build();
      $gw->populateItems((int)$cart_id);
      $tx = new Transactions();

      if($this->request->isPost()){
        $whiteList = ['name','shipping_address1','shipping_address2','shipping_city','shipping_state','shipping_zip'];
        $this->request->csrfCheck();
        $tx->assign($this->request->get(),$whiteList,false);
        $tx->validateShipping();
        $step = $this->request->get('step');
        if($step == '2'){
          $resp = $gw->processForm($this->request->get());
          $tx = $resp['tx'];
          if($resp['success'] != true){
            $tx->addErrorMessage('card-element',$resp['msg']);
          } else {
            Router::redirect('cart/thankYou/'.$tx->id);
          }
        }
      }

      $this->view->gatewayToken = $gw->getToken();
      $this->view->formErrors = $tx->getErrorMessages();
      $this->view->tx = $tx;
      $this->view->grandTotal = $gw->grandTotal;
      $this->view->items = $gw->items;
      $this->view->cartId = $cart_id;
      if(!$this->request->isPost() || !$tx->validationPassed()){
        $this->view->render('cart/shipping_address_form');
      } else {
        $this->view->render($gw->getView());
      }
    }

    public function thankYouAction($tx_id){
      $tx = Transactions::findById((int)$tx_id);
      $this->view->tx = $tx;
      $this->view->render('cart/thankYou');
    }
  }
