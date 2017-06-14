<?php
/**
 * First Data Payeezy Authorize Request
 */

namespace Omnipay\PayeezyDirect\Message;

/**
 * First Data Payeezy Authorize Request
 */
class AuthorizeRequest extends PurchaseRequest
{
    protected $transaction_type = self::TRAN_PREAUTH;
}
