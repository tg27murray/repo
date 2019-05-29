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
    <?= FH::selectBlock('Brand','brand_id',$this->product->brand_id,$this->brands,['class'=>'form-control input-sm'],['class'=>'form-group col-md-3'],$this->displayErrors) ?>
  </div>

  <div class="row">
    <?= FH::textareaBlock('Body','body',$this->product->body,['class'=>'form-control','rows'=>'6'],['class'=>'form-group col-md-12'],$this->displayErrors) ?>
    <?= FH::checkboxBlock('Featured','featured',$this->product->isChecked(),[],['class'=>'form-group col-md-12'],$this->displayErrors) ?>
    <?= FH::checkboxBlock('Has Options','has_options',$this->product->hasOptions(),[],['class'=>'form-group col-md-12'],$this->displayErrors) ?>
  </div>

  <div id="optionsWrapper" class="row mb-3 <?= ($this->product->hasOptions()?"d-block" : "d-none")?>">
    <div class="col-12">
      put table and select search box here
    </div>
  </div>

  <div class="row">
    <?= FH::inputBlock('file',"Upload ProductImages:",'productImages[]','',['class'=>'form-control','multiple'=>'multiple'],['class'=>'form-group col-md-6'],$this->displayErrors) ?>
  </div>
  <div class="row">
    <?= FH::submitBlock('Save',['class'=>'btn btn-large btn-primary'],['class'=>'text-right col-md-12']); ?>
  </div>
</form>

<script>
  document.getElementById('has_options').addEventListener('change',function(evt){
    var wrapper = document.getElementById('optionsWrapper');
    if(evt.target.checked){
      wrapper.classList.add('d-block');
      wrapper.classList.remove('d-none');
    } else {
      wrapper.classList.add('d-none');
      wrapper.classList.remove('d-block');
    }
  });
</script>
