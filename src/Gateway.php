<?php
/**
 * First Data Payeezy Gateway
 */
namespace Omnipay\PayeezyDirect;

use Omnipay\Common\AbstractGateway;
use Omnipay\PayeezyDirect\Message\PurchaseRequest;
use Omnipay\PayeezyDirect\Message\AuthorizeRequest;
use Omnipay\PayeezyDirect\Message\CaptureRequest;
use Omnipay\PayeezyDirect\Message\RefundRequest;
use Omnipay\PayeezyDirect\Message\VoidRequest;
use Omnipay\PayeezyDirect\Message\CreateCardRequest;

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
     * @return PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    /**
     * Create an authorize request.
     *
     * @param array $options
     *
     * @return AuthorizeRequest
     */
    public function authorize(array $options = [])
    {
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    /**
     * complete an authorized purchase.
     *
     * @param array $options
     *
     * @return \Omnipay\Common\Message\AbstractRequest|CaptureRequest
     */
    public function capture(array $options = [])
    {
        return $this->createRequest(CaptureRequest::class, $options);
    }

    /**
     * Create a refund request.
     *
     * @param array $options
     *
     * @return \Omnipay\PayeezyDirect\Message\RefundRequest
     */
    public function refund(array $options = [])
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    /**
     * Create a void request.
     *
     * @param array $options
     *
     * @return \Omnipay\PayeezyDirect\Message\VoidRequest
     */
    public function void(array $options = [])
    {
        return $this->createRequest(VoidRequest::class, $options);
    }

    /**
     * @param array $options
     * @return \Omnipay\PayeezyDirect\Message\CreateCardRequest
     */
    public function createCard(array $options = [])
    {
        return $this->createRequest(CreateCardRequest::class, $options);
    }
}
