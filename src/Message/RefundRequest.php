<?php
namespace Omnipay\PayeezyDirect\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * First Data Payeezy Refund Request
 */
class RefundRequest extends AbstractRequest
{
    protected $transactionType = self::TRAN_TAGGEDREFUND;

    public function getData()
    {
        $this->validate('transactionReference', 'amount');

        $data = array_merge(parent::getData(), [
            'amount'             => $this->getSubmitAmount(),
            'currency_code'      => $this->getCurrency(),
            'partial_redemption' => 'false',
            'method'             => 'credit_card',
            'transaction_tag'    => $this->getTransactionTag(),
        ]);

        return json_encode($data, JSON_FORCE_OBJECT);
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/' . $this->getTransactionId();
    }

}
