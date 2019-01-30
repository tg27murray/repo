<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Products;
use Core\H;
use App\Models\ProductImages;
use App\Lib\Utilities\Uploads;
use Core\Session;
use Core\Router;

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
      // $this->request->csrfCheck();
      $files = $_FILES['productImages'];//H::dnd($files);
      if($files['tmp_name'][0] == ''){
        $product->addErrorMessage('productImages','You must choose an image.');
      } else {
        $uploads = new Uploads($files);
        $uploads->runValidation();
        $imagesErrors = $uploads->validates();
        if(is_array($imagesErrors)){
          $msg = "";
          foreach($imagesErrors as $name => $message){
            $msg .= $message . " ";
          }
          $product->addErrorMessage('productImages',trim($msg));
        }

      }
      $product->assign($this->request->get(),Products::blackList);
      $product->save();
      if($product->validationPassed()){
        //upload images
        ProductImages::uploadProductImages($product->id,$uploads);
        //redirect
        Session::addMsg('success','Product Added!');
        Router::redirect('adminproducts');
      }
    }
    $this->view->product = $product;
    $this->view->formAction = PROOT.'adminproducts/add';
    $this->view->displayErrors = $product->getErrorMessages();
    $this->view->render('adminproducts/add');
  }
}
