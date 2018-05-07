<?php
/**
 * First Data Payeezy Response
 */

namespace Omnipay\PayeezyDirect\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = json_decode($data);
    }

    public function isSuccessful()
    {
        // list of successful values
        $successful = ['approved', 'Scored Successfully'];
        // make sure status result is in our array of
        return is_object($this->data) && in_array($this->getDataItem('transaction_status'), $successful);
    }

    /**
     * Get an item from the internal data array
     *
     * This is a short cut function to ensure that we test that the item
     * exists in the data array before we try to retrieve it.
     *
     * @param $itemname
     * @return mixed|null
     */
    public function getDataItem($key)
    {
        if (isset($this->data->{$key})) {
            return $this->data->{$key};
        }
    }

    public function getTransactionReference()
    {
        return $this->getDataItem('transaction_id') . ':' . $this->getDataItem('transaction_tag');
    }

    public function getTransactionId()
    {
        return $this->getDataItem('transaction_id');
    }

    public function getTransactionTag()
    {
        return $this->getDataItem('transaction_tag');
    }

    public function getAmount()
    {
        return $this->getDataItem('amount') ? number_format($this->getDataItem('amount') / 100, 2) : null;
    }

    /**
     * get token of card from purchase
     * @return string
     */
    public function getCardReference() {
        return isset($this->data->token->token_data->value) ? $this->data->token->token_data->value : null;
    }

    /**
     * Get the error code.
     *
     * @return string
     */
    public function getCode()
    {
        if ($code = $this->getDataItem('code')) {
            return $code;
        }
        if ($error = $this->getDataItem('Error')) {
            return $error->messages[0]->code;
        }
        // gateway error
        if ($this->getDataItem('gateway_resp_code') !== '00') {
            return $this->getDataItem('gateway_resp_code');
        }
        // bank error, 00 is error, 100-164 is approved
        if ($this->getDataItem('bank_resp_code') < 100 || $this->getDataItem('bank_resp_code') > 164) {
            return $this->getDataItem('bank_resp_code');
        }
        // server fault
        if ($fault = $this->getDataItem('fault')) {
            return $fault->detail->errorcode;
        }
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    public function getMessage()
    {
        if ($message = $this->getDataItem('message')) {
            return $message;
        }
        if ($error = $this->getDataItem('Error')) {
            return $error->messages[0]->description;
        }
        // gateway error
        if ($this->getDataItem('gateway_resp_code') !== '00') {
            return $this->getDataItem('gateway_message');
        }
        // bank error, 00 is error, 100-164 is approved
        if ($this->getDataItem('bank_resp_code') < 100 || $this->getDataItem('bank_resp_code') > 164) {
            return $this->getDataItem('bank_message');
        }
        // server fault
        if ($fault = $this->getDataItem('fault')) {
            return $fault->faultstring;
        }
    }
}
