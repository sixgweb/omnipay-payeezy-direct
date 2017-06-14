<?php
/**
 * First Data Payeezy Void Request
 */

namespace Omnipay\PayeezyDirect\Message;

/**
 * First Data Payeezy Void Request
 */
class VoidRequest extends AbstractRequest
{
    protected $transaction_type = self::TRAN_TAGGEDVOID;

    public function getData()
    {

        $reversal_id = $this->getReversalId();

        // validation
        $this->validate('amount');
        // if no reversal id for timeout void, make sure we have a transacrion reference
        if (!$reversal_id) {
            $this->validate('transactionReference');
        }

        $data = array_merge(parent::getData(), [
            'amount'             => $this->getSubmitAmount(),
            'currency_code'      => $this->getCurrency(),
            'partial_redemption' => 'false',
            'method'             => $this->getPaymentMethod(),
        ]);

        // add reversal id for timeout voids
        if ($reversal_id) {
            $data['reversal_id'] = $reversal_id;
        } else {
            // use tag
            $data['transaction_tag'] = $this->getTransactionTag();
        }

        return json_encode($data, JSON_FORCE_OBJECT);
    }

    public function getEndpoint()
    {
        if ($this->getReversalId()) {
            return parent::getEndpoint();
        } else {
            return parent::getEndpoint() . '/' . $this->getTransactionId();
        }
    }
}
