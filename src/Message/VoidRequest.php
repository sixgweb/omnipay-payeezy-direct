<?php
/**
 * First Data Payeezy Void Request
 */

namespace Omnipay\PayeezyDirect\Message;

/**
 * First Data Payeezy Void Request
 */
class VoidRequest extends RefundRequest
{
    protected $transactionType = self::TRAN_TAGGEDVOID;
}
