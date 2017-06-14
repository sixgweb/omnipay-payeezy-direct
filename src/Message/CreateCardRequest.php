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
    public function getSubmitAmount()
    {
        return 0;
    }

}
