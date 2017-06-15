<?php
/**
 * First Data Payeezy Authorize Request
 */

namespace Omnipay\PayeezyDirect\Message;

/**
 * First Data Payeezy Authorize Request
 */
class CreateCardRequest extends AuthorizeRequest
{

    public function getData()
    {
        // tokenize a card with a $0 auth
        $this->setAmount('0.00');
        // method has to be card
        $this->setPaymentMethod('card');
        // get data
        return parent::getData();
    }

}
