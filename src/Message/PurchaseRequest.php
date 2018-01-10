<?php
/**
 * First Data Connect Purchase Request
 */

namespace Omnipay\PayeezyDirect\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\CreditCard;

/**
 * First Data Connect Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{

    protected $transaction_type = self::TRAN_PURCHASE;

    // just a simple fn to check for a value or return null
    protected function getOrNull($array, $key) {
        return isset($array[$key]) ? $array[$key] : null;
    }

    public function getData()
    {

        // handle special cases with apple pay, google play and 3ds where we don't have card data
        switch ($this->getPaymentMethod()) {
            case 'apple_pay':
                // make sure whave ap data sent
                $this->validate('apple_pay');
                $ap_data = $this->getApplePay();
                $billing = $ap_data['billingContact'];
                $token   = $ap_data['token'];
                // set as much card data from the apple pay response
                $this->setCard(new CreditCard([
                    'firstName'       => $this->getOrNull($billing, 'givenName'),
                    'lastName'        => $this->getOrNull($billing, 'familyName'),
                    'email'           => $this->getOrNull($billing, 'email'),
                    'number'          => $token['transactionIdentifier'],
                    'type'            => strtolower($token['paymentMethod']['network']),
                    'billingAddress1' => $billing['addressLines'][0],
                    'billingAddress2' => isset($billing['addressLines'][1]) ? $billing['addressLines'][1] : null,
                    'billingCity'     => $this->getOrNull($billing, 'locality'),
                    'billingPostcode' => $this->getOrNull($billing, 'postalCode'),
                    'billingState'    => $this->getOrNull($billing, 'administrativeArea'),
                    'billingCountry'  => $this->getOrNull($billing, 'country'),
                    'billingPhone'    => $this->getOrNull($billing, 'phoneNumber'),
                ]));
            break;

            case 'android_pay':
                $this->validate('android_pay');
                $ap_data = $this->getAndroidPay();
            break;
        }

        // make sure we have an amount and card data
        $this->validate('amount', 'card');

        // get transaction data
        $data = parent::getData();

        // common data
        $card = [
            'type'            => $this->getCard()->getBrand() ?: $this->getTokenBrand(),
            'cardholder_name' => $this->getCard()->getName(),
            'exp_date'        => $this->getCard()->getExpiryDate('my'),
            'cvv'             => $this->getCard()->getCvv(),
        ];

        switch ($this->getPaymentMethod()) {
            // credit card
            case 'credit_card':
                $this->getCard()->validate();
                $data['credit_card'] = $card + [
                    'card_number' => $this->getCard()->getNumber(),
                ];
            break;

            case 'token':
                $data['token'] = [
                    'token_type' => 'FDToken',
                    'token_data' => $card + [
                        'value'  => $this->getCardReference(),
                    ],
                ];
            break;

            case 'apple_pay':
                $threeDS = $this->getApplePay();
                // Apple Pay is considered 3d Secure
                $data['method'] = '3ds';
                // set 3ds data
                $data['3DS'] = $threeDS['token']['paymentData'];
                // mark as Apple Pay
                $data['3DS']['type'] = 'A';// A is for Apple Pay
            break;

            case 'android_pay':
            break;

            case 'valuelink':
                $data['valuelink'] = [
                    'cardholder_name'  => $this->getCard()->getName(),
                    'cc_number'        => $this->getCard()->getNumber(),
                    'credit_card_type' => 'Gift',
                ];
            break;
        }

        // billing address
        $data['billing_address'] = [
            'city'            => $this->getCard()->getBillingCity(),
            'country'         => $this->getCard()->getBillingCountry(),
            'street'          => $this->getCard()->getBillingAddress1(),
            'state_province'  => $this->getCard()->getBillingState(),
            'zip_postal_code' => $this->getCard()->getBillingPostcode(),
            'phone'           => [
                'number' => $this->getCard()->getBillingPhone()
            ],
        ];

        // order number/merchant reference number
        if ($order_number = $this->getOrderNumber()) {
            $data['merchant_ref'] = $order_number;
        }

        // add reversal id for timeout voids
        if ($this->getReversalId()) {
            $data['reversal_id'] = $this->getReversalId();
        }

        return $data;
    }

}
