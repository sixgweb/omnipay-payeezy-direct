<?php
namespace Omnipay\PayeezyDirect\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * First Data Payeezy Refund Request
 */
class RefundRequest extends VoidRequest
{
    protected $transaction_type = self::TRAN_TAGGEDREFUND;

}
