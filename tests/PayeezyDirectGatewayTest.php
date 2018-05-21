<?php

namespace Omnipay\PayeezyDirect;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;
use DateTime;

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
        // $this->gateway->setEnvironment('api-cert'); // some functions only work in cert environment

        $this->options = [
            'testMode' => true,
            'amount'   => '13.12',
            'currency' => 'USD',
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
        // $this->setMockHttpResponse('PurchaseSuccess.txt');
        $options = array_merge($this->options, [
            'card'           => $this->getValidCard(),
            'paymentMethod'  => 'card',
        ]);
        $response = $this->gateway->purchase($options)->send();
        $this->assertInstanceOf('Omnipay\PayeezyDirect\Message\Response', $response);
        $this->assertEquals($response->getAmount(), $options['amount']);
        $this->assertTrue($response->isSuccessful());
    }

    public function testPurchaseFailure()
    {
        // $this->setMockHttpResponse('PurchaseSuccess.txt');
        $options = array_merge($this->options, [
            'card'           => $this->getValidCard(),
            'paymentMethod'  => 'card',
            'amount'         => 5811.00, // fail error code 811, bad vvv
        ]);
        $response = $this->gateway->purchase($options)->send();
        $this->assertInstanceOf('Omnipay\PayeezyDirect\Message\Response', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(811, $response->getCode());
    }

    public function testGiftCardPurchaseSuccess()
    {
        // $this->setMockHttpResponse('PurchaseSuccess.txt');
        $options = array_merge($this->options, [
            'paymentMethod'  => 'valuelink',
            'card'           => [
                'name'   => 'John Smith',
                'number' => '7777045839985463',
            ],
        ]);
        $response = $this->gateway->purchase($options)->send();
        $this->assertInstanceOf('Omnipay\PayeezyDirect\Message\Response', $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function testAuthorizeSuccess()
    {
        // $this->setMockHttpResponse('AuthorizeSuccess.txt');
        $options = array_merge($this->options, [
            'card'           => $this->getValidCard(),
            'paymentMethod'  => 'card',
            'amount'         => 13.00,
        ]);
		$response = $this->gateway->authorize($options)->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testTimeoutVoidSuccess()
    {
        // get valid reversal_id
        $reversal_id = $this->gateway->setReversalId()->getReversalId();
        $this->assertStringStartsWith('Re-txn-', $reversal_id);
        // mock repsonse due to only workign in production or cert env
        $this->setMockHttpResponse('TimeoutVoidSuccess.txt');
		$response = $this->gateway->void($this->options)->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testVoidSuccess()
    {
        // make purchase
        $options = array_merge($this->options, [
            'card'           => $this->getValidCard(),
            'paymentMethod'  => 'card',
        ]);
        $response = $this->gateway->purchase($options)->send();
        // void purchase
        $options = array_merge($this->options, [
            'paymentMethod'  => 'card',
            'transactionReference' => $response->getTransactionReference(),
        ]);
		$response = $this->gateway->void($options)->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testAuthorizeAndCaptureSuccess()
    {
        // auth purchase
        $options = array_merge($this->options, [
            'card'           => $this->getValidCard(),
            'paymentMethod'  => 'card',
        ]);
        $response = $this->gateway->authorize($options)->send();
        // complete purchase
        $options = array_merge($this->options, [
            'paymentMethod'  => 'card',
            'transactionReference' => $response->getTransactionReference(),
        ]);
		$response = $this->gateway->capture($options)->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testCreateCardSuccess()
    {
        // $this->setMockHttpResponse('CreateCardSuccess.txt');
        $options = array_merge($this->options, [
            'card'           => $this->getValidCard(),
            'paymentMethod'  => 'card',
        ]);
		$response = $this->gateway->createCard($options)->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testTokenPurchaseSuccess()
    {
        // get card data
        $options = array_merge($this->options, [
            'card'           => $this->getValidCard(),
            'paymentMethod'  => 'card',
        ]);
        // tokenize with $0 Auth
		$response = $this->gateway->createCard($options)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getCardReference());
        // set token value and card brand
        $options = array_merge($this->options, [
            'cardReference' => $response->getCardReference(),
            'paymentMethod' => 'token', // paying with token
            'tokenBrand'    => 'visa', // must store type with card token, name, exp, etc. PZ requires this to be passed every time
            'card'          => $this->getValidCard(), // unlike other gateways, we need token + full card data
        ]);
        $response = $this->gateway->purchase($options)->send();
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * FraudDetect scoring
     */
    public function testAuthAndScorePassed()
    {
        // start with new card
        $card = new CreditCard($this->getValidCard());

        // get options
        $options = array_merge($this->options, [
            'card'          => $card,
            'paymentMethod' => 'card',
            'orderNumber'   => 'TEST-' . time(),
        ]);

        // tokenize card with $0 Auth
        $response = $this->gateway->createCard($options)->send();
        $response_data = $response->getData();

        // test
        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getCardReference());

        // set token value and card brand
        $options = array_merge($this->options, [
            'cardReference' => '2537446225198291', //normally use $response->getCardReference(),  // only cc token that works in sandbox
            'paymentMethod' => 'token', // paying with token
            'tokenBrand'    => $card->getBrand(), // must store type with card token, name, exp, etc. PZ requires this to be passed every time
            'card'          => [
                'number'      => $card->getNumber(),
                'expiryMonth' => $card->getExpiryDate('m'),
                'expiryYear'  => $card->getExpiryDate('Y'),
            ],
            'scoreData' => [
                'original_transaction_type' => 'transaction/authorization', // required
    			'original_transaction_id'   => $response->getTransactionId(), // required
            	'merchant' => [
    				'merchant_unique_id' => 'SUNOCO_WALLET', // only id that works in the sandbox
    				"location" => [
    					"location_id"      => '1001',
    					"merchant_address" => [
    						"street"          => '742 Evergreen Terrace',
    						"street2"         => 'Suite 1',
    						"state_province"  => 'MA',
    						"city"            => 'Springfield',
    						"country"         => 'us',
    						"zip_postal_code" => '01776',
    					],
    					"time_zone_offset" => '-04:00',
    					// "hierarchy" => "abc"
    			  ],
    			],
            	"customer" => [
    				"id"            => 1234,
    				"start_date"    => (new DateTime)->format('Y-m-d'),
    				"first_name"    => 'Homer',
    				"last_name"     => 'Simpson',
    				"email"         => 'homer@snpp.com',
    				"session_id"    => md5(uniqid('test-', true)),
    				"username"      => 'hsimpson',
    				"gender"        => 'male',
    				"date_of_birth" => '1957',
    				"address" => [
                        "street"          => '742 Evergreen Terrace',
						"street2"         => 'Suite 1',
						"state_province"  => 'MA',
						"city"            => 'Springfield',
						"country"         => 'us',
						"zip_postal_code" => '01776',
    					"phone" => [
    						"type"   => "cell",
    						"number" => '111-111-1111',
    					],
    				],
    			],
            	"device" => [
        			"device_type" => "device/mobile",
        			"device_id" => md5(uniqid('device-', true)),
        		// 	"networks" => [
        		// 		[
        		// 			"network_type" => "network/mobile",
        		// 			"mac" => "02:00:00:00:00:00",
        		// 			"ssid" => "Boston-5G-1",
        		// 			"bssid" => "e8:fc:af:fb:4b:8c",
        		// 			"ip" => "10.192.168.1",
        		// 			"phone_number" => "+22 607 123 4567",
        		// 			"carrier_name" => "T-Mobile",
        		// 			"mobile_country_code" => "310",
        		// 			"mobile_network_code" => "004",
        		// 			"mobile_subscription_identification_number" => "123456789",
        		// 			"location_area_code" => "12345",
        		// 			"cell_id" => "2224",
        		// 			"standard" => "HSDPA+",
        		// 			// "user_defined" => [
    			// 			//     "used_data" => "{String}"
        		// 			// ]
        		// 		]
        		// 	],
        			"latitude"         => 38.736946,
        			"longitude"        => -9.142685,
        			"imei"             => "49-015420-323751",
        			"model"            => "iPhone 6s Plus",
        			"manufacturer"     => "Apple",
        			"timezone_offset"  => "+02:00",
        			"rooted"           => false,
        			"malware_detected" => false,
        			// "user_defined" => { 
        			// 	   "used_data" => "{String}",
        			// 	   "battery" => "{string}"
        			// }
        		],
        		"billing_address" => [
                    "first_name"      => "Homer",
    				"last_name"       => "Simpson",
        			"middle_name"     => "Jay",
        			"street"          => "742 Evergreen Terrace", // need street to send billing address
        			"state_province"  => "NY",
        			"city"            => "Springfield",
        			"country"         => 'us',
        			"zip_postal_code" => '017776',
        			"phone" => [
        				"type"   => "cell",
        				"number" => "212-515-1212"
        			],
        		],
                "loyalty" => [
                    "id"      => 666,
                    "program" => "POINTS_PROGRAM",
                    "balance" => 12,
                ],
            ]
        ]);

        // send to score
        $response = $this->gateway->score($options)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(0, $response->getScore());
        $this->assertFalse($response->isFraud());

        // print_r($response->getData());exit;

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
