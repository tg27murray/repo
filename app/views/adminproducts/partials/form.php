<?php
  use Core\FH;
?>
<form action="<?=$this->formAction?>" method="POST" enctype="multipart/form-data">
  <?= FH::csrfInput();?>
  <?= FH::displayErrors($this->displayErrors)?>
  <div class="row">
    <?= FH::inputBlock('text','Name','name',$this->product->name,['class'=>'form-control input-sm'],['class'=>'form-group col-md-6'],$this->displayErrors) ?>
    <?= FH::inputBlock('text','Price','price',$this->product->price,['class'=>'form-control input-sm'],['class'=>'form-group col-md-2'],$this->displayErrors) ?>
    <?= FH::inputBlock('text','List Price','list',$this->product->list,['class'=>'form-control input-sm'],['class'=>'form-group col-md-2'],$this->displayErrors) ?>
    <?= FH::inputBlock('text','Shipping','shipping',$this->product->shipping,['class'=>'form-control input-sm'],['class'=>'form-group col-md-2'],$this->displayErrors) ?>

  </div>

  <div class="row">
    <?= FH::textareaBlock('Body','body',$this->product->body,['class'=>'form-control','rows'=>'6'],['class'=>'form-group col-md-12'],$this->displayErrors) ?>
  </div>

  <div class="row">
    <div class="form-group col-md-6">
      <label for="productImages" class="control-label">Upload Product Images:</label>
      <input type="file" multiple="multiple" name="productImages[]" id="productImages" class="form-control"/>
    </div>
  </div>
  <div class="row">
    <?= FH::submitBlock('Save',['class'=>'btn btn-large btn-primary'],['class'=>'text-right col-md-12']); ?>
  </div>
</form>
