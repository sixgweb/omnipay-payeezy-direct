<?php
/**
 * First Data Payeezy Void Request
 */

namespace Omnipay\PayeezyDirect\Message;

/**
 * First Data Payeezy Void Request
 */
class CaptureRequest extends AbstractRequest
{
    protected $transaction_type = self::TRAN_TAGGEDPREAUTHCOMPLETE;

    public function getData()
    {

        // validation
        $this->validate('amount','transactionReference');

        $data = array_merge(parent::getData(), [
            'transaction_tag'    => $this->getTransactionTag(),
        ]);

        return $data;
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/' . $this->getTransactionId();
    }
}
