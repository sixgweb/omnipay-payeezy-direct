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
        return is_object($this->data) && $this->getDataItem('transaction_status') == 'approved';
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



    public function getMessage()
    {
        return $this->getDataItem('Error')->messages[0]->description;
    }

    /**
     * Get the error code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getDataItem('Error')->messages[0]->code;
    }
}
