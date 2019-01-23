<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Products;
use Core\H;
use App\Models\ProductImages;

class AdminproductsController extends Controller {
  public function __construct($controller,$action){
    parent::__construct($controller,$action);
    $this->view->setLayout('admin');
  }

  public function indexAction(){
    $this->view->render('adminproducts/index');
  }

  public function addAction(){
    $product = new Products();
    $productImage = new ProductImages();
    if($this->request->isPost()){
      $files = $_FILES['productImages'];
      $this->request->csrfCheck();
      $imagesErrors = $productImage->validateImages($files);
      if(is_array($imagesErrors)){
        $msg = "";
        foreach($imagesErrors as $name => $message){
          $msg .= $message . " ";
        }
        $product->addErrorMessage('productImages',trim($msg));
      }
      $product->assign($this->request->get(),Products::blackList);
      $product->save();
      if($product->validationPassed()){
        //upload images
        $structuredFiles = ProductImages::restructureFiles($files);
        ProductImages::uploadProductImages($product->id,$structuredFiles);
      }
    }
    $this->view->product = $product;
    $this->view->formAction = PROOT.'adminproducts/add';
    $this->view->displayErrors = $product->getErrorMessages();
    $this->view->render('adminproducts/add');
  }
}
