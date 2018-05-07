<?php

namespace Omnipay\PayeezyDirect;

trait GetterSetterTrait
{
	/**
     * Get API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('api_key');
    }

    /**
     * Set API Key
     *
     * @return PayeezyDirect provides a fluent interface.
     */
    public function setApiKey($value)
    {
        return $this->setParameter('api_key', $value);
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
        return $this->getParameter('api_secret');
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
        return $this->setParameter('api_secret', $value);
    }

    /**
     * Get Merchant Token
     *
     * @return string
     */
    public function getMerchantToken()
    {
        return $this->getParameter('merchant_token');
    }

    /**
     * Set Merchant Token
     *
     * @return PayeezyDirect provides a fluent interface.
     */
    public function setMerchantToken($value)
    {
        return $this->setParameter('merchant_token', $value);
    }

    /**
     * Get Hmac
     *
     * @return string
     */
    public function getTransArmorToken()
    {
        return $this->getParameter('transarmor_token');
    }

    /**
     * Set TransArmorToken
     *
     * @return PayeezyDirect provides a fluent interface.
     */
    public function setTransArmorToken($value)
    {
        return $this->setParameter('transarmor_token', $value);
    }

    public function getEnvironment()
    {
		// default to anything we've set manually
		$env = $this->getParameter('environment');
		// if nothing set manually
		if (!$env) {
			// use test mode or production
			$env = $this->getTestMode() ? 'api-cert' : 'api';
		}
		return $env;
    }

    public function setEnvironment($value)
    {
        return $this->setParameter('environment', $value);
    }


    // set a reversal id or make a generic one for this api key
    public function setReversalId($value=null)
    {
		// default
		if (!$value) {
			$value = "Re-txn-" . md5($this->getApiKey() . microtime(1));
		}
        return $this->setParameter('reversal_id', $value);
    }

    public function getReversalId()
    {
        return $this->getParameter('reversal_id');
    }

	public function setOrderNumber($value)
	{
		return $this->setParameter('order_number', $value);
	}

	public function getOrderNumber()
	{
		return $this->getParameter('order_number');
	}

	public function setTokenBrand($value)
	{
		return $this->setParameter('token_brand', $value);
	}

	public function getTokenBrand()
	{
		return $this->getParameter('token_brand');
	}

	public function setScoreData($value)
	{
		return $this->setParameter('score_data', $value);
	}

	public function getScoreData()
	{
		return $this->getParameter('score_data');
	}
}
