<?php
/**
 * First Data Payeezy Abstract Request
 */

namespace Omnipay\PayeezyDirect\Message;

/**
 * First Data Payeezy Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /** @var string Method used to calculate the hmac strings. */
    const METHOD_POST = 'POST';

    /** @var string Content type use to calculate the hmac string */
    const CONTENT_TYPE = 'application/json; charset=UTF-8';

    /** API version to use. */
    const API_VERSION = 'v1';

    /** @var string live endpoint URL base */
    protected $liveEndpoint = 'https://api.payeezy.com/';

    /** @var string test endpoint URL base */
    protected $testEndpoint = 'https://api-cert.payeezy.com/';

    /** @var int api transaction type */
    protected $transactionType;

    // Transaction types
    const TRAN_PURCHASE                 = 'purchase';
    const TRAN_PREAUTH                  = 'authorize';
    const TRAN_PREAUTHCOMPLETE          = 'capture';
    const TRAN_TAGGEDPREAUTHCOMPLETE    = 'capture';
    const TRAN_VOID                     = 'void';
    const TRAN_TAGGEDVOID               = 'void';
    const TRAN_REFUND                   = 'refund';
    const TRAN_TAGGEDREFUND             = 'refund';

    // const TRAN_FORCEDPOST               = '03';
    // const TRAN_PREAUTHONLY              = '05';
    // const TRAN_PAYPALORDER              = '07';
    // const TRAN_CASHOUT                  = '83';
    // const TRAN_ACTIVATION               = '85';
    // const TRAN_BALANCEINQUIRY           = '86';
    // const TRAN_RELOAD                   = '88';
    // const TRAN_DEACTIVATION             = '89';

    /** @var array Names of the credit card types. */
    protected static $cardTypes = array(
        'visa'        => 'Visa',
        'mastercard'  => 'Mastercard',
        'discover'    => 'Discover',
        'amex'        => 'American Express',
        'diners_club' => 'Diners Club',
        'jcb'         => 'JCB',
    );

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

    /**
     * Set transaction type
     *
     * @param int $transactionType
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setTransactionType($transactionType)
    {
        $this->setParameter('transaction_type',  $transactionType);
    }

    /**
     * Get transaction type
     *
     * @return int
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Get the transaction headers.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'apikey'        => $this->getApiKey(),
            'token'         => $this->getMerchantToken(),
        ];
    }

    /**
     * Get the card type name, from the card type code.
     *
     * @param string $type
     *
     * @return string
     */
    public static function getCardType($type)
    {
        if (isset(self::$cardTypes[$type])) {
            return self::$cardTypes[$type];
        }
        return $type;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
			'transaction_type' => $this->transactionType,
		];
    }

    /**
	 * Payeezy
	 *
	 * HMAC Authentication
	 * @return array $headers
	 */

	public function getAuthorizationHeaders() {
        $nonce = strval(hexdec(bin2hex(openssl_random_pseudo_bytes(4, $cstrong))));
        // time stamp in milli seconds
        $timestamp = strval(time() * 1000);
        $data = $this->getApiKey() . $nonce . $timestamp . $this->getMerchantToken() . $this->getData();
        // HMAC Hash in hex
        $hmac = hash_hmac('sha256', $data, $this->getApiSecret(), false);

        $authorization = base64_encode($hmac);

        return [
            'Authorization' => $authorization,
            'nonce'         => $nonce,
            'timestamp'     => $timestamp,
        ];
	}

    /**
     * @param mixed $data
     *
     * @return Response
     */
    public function sendData($data)
    {

        $endpoint = $this->getEndpoint();
        $headers  = $this->getHeaders();

        // add HMAC auth data
        $headers = array_merge($headers, $this->getAuthorizationHeaders());

        $client = $this->httpClient->post($endpoint, $headers);

        $client->setBody($data, $headers['Content-Type']);
        $client->getCurlOptions()->set(CURLOPT_PORT, 443);
        $client->getCurlOptions()->set(CURLOPT_SSLVERSION, 6);

        try {
            $httpResponse = $client->send();

        } catch (\Exception $e) {
            echo($client->getBody());
            echo(PHP_EOL . '- - - - ' . PHP_EOL);
            echo($client->getResponse()->getBody());

        }



        return $this->createResponse($httpResponse->getBody());
    }

    /**
     * Get the endpoint URL for the request.
     *
     * @return string
     */
    protected function getEndpoint()
    {
        return ($this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint) . self::API_VERSION . '/transactions';
    }

    /**
     * Create the response object.
     *
     * @param $data
     *
     * @return Response
     */
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    /**
     * Validates and returns the formated amount.
     *
     * @throws InvalidRequestException on any validation failure.
     * @return string The amount formatted to the correct number of decimal places for the selected currency.
     */
    public function getSubmitAmount()
    {
        return number_format(parent::getAmount(), 2, '', '');
    }

    public function getTransactionId()
    {
        return explode(':', $this->getTransactionReference())[0];
    }

    public function getTransactionTag()
    {
        return explode(':', $this->getTransactionReference())[1];
    }
}
