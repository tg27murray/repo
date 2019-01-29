<?php
namespace App\Models;
use Core\Model;
use Core\H;

class ProductImages extends Model{
  public $id, $url, $product_id, $name, $deleted=0;
  protected static $_table = 'product_images';
  protected static $_softDelete = true;

  public function validateImages($images){
    $files = self::restructureFiles($images);
    $errors = [];
    $maxSize = 5242880;
    $allowedTypes = [IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG];
    foreach($files as $file){
      $name = $file['name'];
      // check filesize
      if($file['size'] > $maxSize){
        $errors[$name] = $name . " is over the max allowed size of 5mb.";
      }

      // checking file type
      if(!in_array(exif_imagetype($file['tmp_name']),$allowedTypes)){
        $errors[$name] = $name . " is not an allowed file type. Please use a jpeg, gif, or png.";
      }

    }
    return (empty($errors))? true : $errors;
  }

  public static function uploadProductImages($product_id,$files){
    $path = 'uploads'.DS.'product_images'.DS.'product_'.$product_id.DS;
    foreach($files as $file){
      $parts = explode('.',$file['name']);
      $ext = end($parts);
      $hash = sha1(time().$product_id.$file['tmp_name']);
      $uploadName = $hash . '.' . $ext;
      $image = new self();
      $image->url = $path . $uploadName;
      $image->name = $uploadName;
      $image->product_id = $product_id;
      if($image->save()){
        if (!file_exists($path)) {
            mkdir($path);
          }
        move_uploaded_file($file['tmp_name'],ROOT.DS.$image->url);
      }
    }
  }

  public static function restructureFiles($files){
    $structured = [];
    foreach($files['tmp_name'] as $key => $val){
      $structured[] = [
        'tmp_name'=>$files['tmp_name'][$key],'name'=>$files['name'][$key],
        'size'=>$files['size'][$key],'error'=>$files['error'][$key],'type'=>$files['type'][$key]
      ];
    }
    return $structured;
  }
}
