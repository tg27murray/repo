<?php
  namespace Migrations;
  use Core\Migration;

  class Migration1557882435 extends Migration {
    public function up() {
      $table = "products";
      $this->addIndex($table,'name');
      $this->addIndex($table,'price');
    }
  }
