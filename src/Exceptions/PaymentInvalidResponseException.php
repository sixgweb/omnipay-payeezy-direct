<?php


namespace Omnipay\PayeezyDirect\Exceptions;


use Exception;
use Throwable;

class PaymentInvalidResponseException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Error decoding response: $message", $code, $previous);
    }
}