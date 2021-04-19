<?php


namespace Omnipay\PayeezyDirect\Exceptions;


use Exception;
use Throwable;

class PaymentInvalidRequestException extends Exception
{
    public function __construct($message = "", $code = 409, Throwable $previous = null)
    {
        parent::__construct("Error encoding request: $message", $code, $previous);
    }
}