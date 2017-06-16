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

        // make sure we have an amount and card data
        $this->validate('amount', 'card');

        $data = array_merge(parent::getData(), [
            'amount'             => $this->getSubmitAmount(),
            'currency_code'      => $this->getCurrency(),
            'method'             => $this->getPaymentMethod() == 'card' ? 'credit_card' : $this->getPaymentMethod(),
            'method'             => $this->getPaymentMethod() == 'card' ? 'credit_card' : $this->getPaymentMethod(),
        ]);

        if ($merchant_ref = $this->getMerchantRef()) {
            $data['merchant_ref'] = $merchant_ref;
        }

        // common data
        $card = [
            'type'            => $this->getCard()->getBrand() ?: $this->getTokenBrand(),
            'cardholder_name' => $this->getCard()->getName(),
            'exp_date'        => $this->getCard()->getExpiryDate('my'),
            'cvv'             => $this->getCard()->getCvv(),
        ];

        switch ($this->getPaymentMethod()) {
            // credit card
            case 'card':
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

        return $data;
    }

}
