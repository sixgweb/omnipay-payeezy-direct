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

	protected $transactionType = self::TRAN_PURCHASE;

    public function getData()
    {

        $this->validate('amount', 'card');

        $data = array_merge(parent::getData(), [
            'merchant_ref'     => '',
			'amount'           => $this->getSubmitAmount(),
            'currency_code'    => $this->getCurrency(),
            'partial_redemption' => 'false',
        ]);

        // credit card
        $data['method'] = 'credit_card';
        $data['credit_card'] = [
            'type'            => $this->getCard()->getBrand(),
            'cardholder_name' => $this->getCard()->getName(),
            'card_number'     => $this->getCard()->getNumber(),
            'exp_date'        => $this->getCard()->getExpiryDate('m') . $this->getCard()->getExpiryDate('y'),
            'cvv'             => $this->getCard()->getCvv()
        ];

        // billing address
        $data['billing_address'] = [
            'city'            => $this->getCard()->getBillingCity(),
            'country'         => $this->getCard()->getBillingCountry(),
            'street'          => $this->getCard()->getBillingAddress1(),
            'state_province'  => $this->getCard()->getBillingState(),
            'zip_postal_code' => $this->getCard()->getBillingPostcode(),
            'phone'           => [
                // 'type'   => '',
                'number' => $this->getCard()->getBillingPhone()
            ],
        ];

        return json_encode($data, JSON_FORCE_OBJECT);
    }

}
