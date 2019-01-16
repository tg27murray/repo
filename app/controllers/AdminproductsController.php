<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Products;

class AdminproductsController extends Controller {
  public function __construct($controller,$action){
    parent::__construct($controller,$action);
    $this->view->setLayout('admin');
  }

  public function indexAction(){
    $this->view->render('adminproducts/index');
  }

  public function addAction(){
    $product = New Products();
    if($this->request->isPost()){
      $this->request->csrfCheck();
      $product->assign($this->request->get());
      $product->save();
    }
    $this->view->product = $product;
    $this->view->formAction = PROOT.'adminproducts/add';
    $this->view->displayErrors = $product->getErrorMessages();
    $this->view->render('adminproducts/add');
  }
}
