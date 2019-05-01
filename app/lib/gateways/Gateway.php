<?php
  namespace App\Lib\Gateways;
  use App\Lib\Gateways\{StripeGateway};

  class Gateway {
    public static function build(){
      if(GATEWAY == 'stripe'){
        return new StripeGateway();
      } else if(GATEWAY == 'braintree') {
        return new BraintreeGateway();
      }
    }
  }
