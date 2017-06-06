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
    protected $transactionType = self::TRAN_PREAUTH;
}
