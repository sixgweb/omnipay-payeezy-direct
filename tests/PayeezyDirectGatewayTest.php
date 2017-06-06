<?php

namespace Omnipay\PayeezyDirect;

use Omnipay\Tests\GatewayTestCase;

class PayeezyDirectGatewayTest extends GatewayTestCase
{
    /** @var  PayeezyDirect */
    protected $gateway;

    /** @var  array */
    protected $options;

    public function setUp()
    {
        parent::setUp();

		// Payeezy Direct sandbox credentials
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setApiKey('y6pWAJNyJyjGv66IsVuWnklkKUPFbb0a');
        $this->gateway->setApiSecret('86fbae7030253af3cd15faef2a1f4b67353e41fb6799f576b5093ae52901e6f7');
        $this->gateway->setMerchantToken('fdoa-a480ce8951daa73262734cf102641994c1e55e7cdf4c02b6');
        $this->gateway->setTransArmorToken('NOIW');

        $this->options = [
            'amount'   => '13.00',
            'card'     => $this->getValidCard(),
            'currency' => 'USD',
            'testMode' => true,
        ];
    }

    public function testProperties()
    {
		$this->assertEquals('y6pWAJNyJyjGv66IsVuWnklkKUPFbb0a', $this->gateway->getApiKey());
		$this->assertEquals('86fbae7030253af3cd15faef2a1f4b67353e41fb6799f576b5093ae52901e6f7', $this->gateway->getApiSecret());
		$this->assertEquals('fdoa-a480ce8951daa73262734cf102641994c1e55e7cdf4c02b6', $this->gateway->getMerchantToken());
		$this->assertEquals('NOIW', $this->gateway->getTransArmorToken());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->gateway->purchase($this->options)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('ET114094:155692013', $response->getTransactionReference());
    }

    public function testAuthorizeSuccess()
    {
        $this->setMockHttpResponse('AuthorizeSuccess.txt');
		$response = $this->gateway->authorize($this->options)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('ET153636:156306794', $response->getTransactionReference());
    }

    public function testVoidSuccess()
    {
        $this->setMockHttpResponse('VoidSuccess.txt');
        $options = array_merge($this->options, [
            'transactionReference' => 'ET171025:156304361'
        ]);
		$response = $this->gateway->void($options)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('ET171025:156304361', $response->getTransactionReference());
    }

    public function testCreateCardSuccess()
    {
        $this->setMockHttpResponse('CreateCardSuccess.txt');
		$response = $this->gateway->createCard($this->options)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('ET143165:156310751', $response->getTransactionReference());
        $this->assertEquals('1033081934821111', $response->getCardReference());
    }

    /**
     * [getValidCard Payeezy direct requests a 1XX CVV to show as valid]
     * @return [type] array
     */
    public function getValidCard() {
        $card_data = parent::getValidCard();
        $card_data['cvv'] = rand(100,199);
        return $card_data;
    }
}
