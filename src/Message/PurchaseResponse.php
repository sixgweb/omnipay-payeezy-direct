<?php
/**
 * First Data Connect Purchase Response
 */

namespace Omnipay\PayeezyDirect\Message;


/**
 * First Data Connect Purchase Response
 */
class PurchaseResponse extends Response
{
    /** @inheritDoc */
    public function isSuccessful()
    {
        return is_object($this->data) && $this->getDataItem('transaction_status') !== 'approved';
    }
}
