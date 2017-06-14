<?php
/**
 * First Data Payeezy Gateway
 */
namespace Omnipay\PayeezyDirect;

use Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Payeezy Direct';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiKey'          => '',
            'apiSecret'       => '',
            'merchantToken'   => '',
            'transArmorToken' => '',
            'testMode'        => false,
        );
    }

    /**
     * Get API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Set API Key
     *
     * @return PayeezyDirect provides a fluent interface.
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Get API Key
     *
     * Calls to the Payeezy Gateway API are secured with a gateway ID and
     * password.
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->getParameter('apiSecret');
    }

    /**
     * Set Password
     *
     * Calls to the Payeezy Gateway API are secured with a gateway ID and
     * password.
     *
     * @return PayeezyDirect provides a fluent interface.
     */
    public function setApiSecret($value)
    {
        return $this->setParameter('apiSecret', $value);
    }

    /**
     * Get Merchant Token
     *
     * @return string
     */
    public function getMerchantToken()
    {
        return $this->getParameter('merchantToken');
    }

    /**
     * Set Merchant Token
     *
     * @return PayeezyDirect provides a fluent interface.
     */
    public function setMerchantToken($value)
    {
        return $this->setParameter('merchantToken', $value);
    }

    /**
     * Get Hmac
     *
     * @return string
     */
    public function getTransArmorToken()
    {
        return $this->getParameter('transArmorToken');
    }

    /**
     * Set TransArmorToken
     *
     * @return PayeezyDirect provides a fluent interface.
     */
    public function setTransArmorToken($value)
    {
        return $this->setParameter('transArmorToken', $value);
    }

    public function getPaymentMethod()
    {
        return $this->getParameter('payment_method');
    }

    /**
     * Create a purchase request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\PayeezyDirect\Message\PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayeezyDirect\Message\PurchaseRequest', $parameters);
    }

    /**
     * Create an authorize request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\PayeezyDirect\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayeezyDirect\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Create a capture request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\PayeezyDirect\Message\CaptureRequest
     */
    public function capture(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayeezyDirect\Message\CaptureRequest', $parameters);
    }

    /**
     * Create a refund request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\PayeezyDirect\Message\RefundRequest
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayeezyDirect\Message\RefundRequest', $parameters);
    }

    /**
     * Create a void request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\PayeezyDirect\Message\VoidRequest
     */
    public function void(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayeezyDirect\Message\VoidRequest', $parameters);
    }

    public function createCard(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayeezyDirect\Message\CreateCardRequest', $parameters);
    }
}
