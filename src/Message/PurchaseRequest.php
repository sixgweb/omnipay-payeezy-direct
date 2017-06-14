<?php
/**
 * First Data Connect Purchase Request
 */

namespace Omnipay\PayeezyDirect\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;

/**
 * First Data Connect Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{

    protected $transaction_type = self::TRAN_PURCHASE;

    public function getData()
    {

        $this->validate('amount', 'card');

        $data = array_merge(parent::getData(), [
            'merchant_ref'       => '',
            'amount'             => $this->getSubmitAmount(),
            'currency_code'      => $this->getCurrency(),
            'partial_redemption' => 'false',
        ]);

        // common data
        $card = [
            'type'            => $this->getCard()->getBrand(),
            'cardholder_name' => $this->getCard()->getName(),
            'exp_date'        => $this->getCard()->getExpiryDate('my'),
            'cvv'             => $this->getCard()->getCvv(),
        ];

        // token
        if ($this->getPaymentMethod() == 'token') {
            $data['method'] = 'token';
            $data['token'] = [
                    'token_type' => 'FDToken',
                    'token_data' => array_merge($card, [
                    'value' => $this->getCardReference(),
                ])
            ];
        } else {
            // credit card
            $data['method'] = 'credit_card';
            $data['credit_card'] = array_merge($card, [
                'card_number' => $this->getCard()->getNumber(),
            ]);
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

        // add reversal id for timeout voids
        if ($this->getReversalId()) {
            $data['reversal_id'] = $this->getReversalId();
        }

        return json_encode($data, JSON_FORCE_OBJECT);
    }

}
