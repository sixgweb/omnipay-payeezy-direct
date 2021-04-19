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

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws InvalidCreditCardException
     */
    public function getData()
    {

        // make sure we have an amount and card data
        $this->validate('amount', 'card');

        $data = array_merge(parent::getData(), [
            'amount'             => $this->getSubmitAmount(),
            'currency_code'      => $this->getCurrency(),
            'method'             => $this->getPaymentMethod(),
        ]);

        // order number/merchant reference number
        if ($order_number = $this->getOrderNumber()) {
            $data['merchant_ref'] = $order_number;
        }

        // common data
        $card = [
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
                    'type'        => $this->getCard()->getBrand(),
                ];
            break;

            case 'token':
                $this->validate('token_brand');
                $data['token'] = [
                    'token_type' => 'FDToken',
                    'token_data' => $card + [
                        'type'   => $this->getTokenBrand(),
                        'value'  => $this->getCardReference(),
                    ],
                ];
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

        // add reversal id for timeout voids
        if ($this->getReversalId()) {
            $data['reversal_id'] = $this->getReversalId();
        }

        return $data;
    }

}
