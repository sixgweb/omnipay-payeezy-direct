<?php
/**
 * First Data Payeezy Gateway
 */
namespace Omnipay\PayeezyDirect;

use Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway
{
    use GetterSetterTrait;

    public function getName()
    {
        return 'Payeezy Direct';
    }

    public function getDefaultParameters()
    {
        return array(
            'api_key'          => '',
            'api_secret'       => '',
            'merchant_token'   => '',
            'transarmor_token' => '',
            'testMode'         => false,
        );
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
     * complete an authorized purchase.
     *
     * @param array $parameters
     *
     * @return \Omnipay\PayeezyDirect\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayeezyDirect\Message\CompletePurchaseRequest', $parameters);
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
