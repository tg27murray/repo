<?php
  namespace App\Controllers;
  use Core\Controller;
  use Core\H;

  class HomeController extends Controller {

    public function __construct($controller, $action) {
      parent::__construct($controller, $action);
    }

    public function indexAction() {
      $this->view->render('home/index');
    }
  }
