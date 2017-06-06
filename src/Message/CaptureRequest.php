<?php
/**
 * First Data Payeezy Capture Request
 */

namespace Omnipay\PayeezyDirect\Message;

/**
 * First Data Payeezy Capture Request
 */
class CaptureRequest extends RefundRequest
{
    protected $action = self::TRAN_TAGGEDPREAUTHCOMPLETE;
}
