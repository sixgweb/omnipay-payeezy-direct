<?php
/**
 * First Data Payeezy Authorize Request
 */

namespace Omnipay\PayeezyDirect\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * First Data Payeezy Score Request
 */
class ScoreRequest extends AbstractRequest
{
    protected $transaction_type = self::TRAN_SCORE_ONLY;

    public function getData()
    {
        $this->validate('amount', 'card', 'score_data');

        // concat basic data
        $data = array_merge_recursive(parent::getData(), $this->getScoreData(), [
            'amount'                    => $this->getSubmitAmount(),
            'currency_code'             => $this->getCurrency(),
            'payment' => [
                'payment_type' => 'payment/card',
                'method' => [
                    'method_type' => 'method/card',
                    'card_brand' =>  $this->getTokenBrand(),
                    'card' => [
                        'exp_date'       => $this->getCard()->getExpiryDate('mY'),
                        'cvv_present'    => $this->getCard()->getCvv() ? true : false,
                    ]
                ],
                'pin_present'  => false,
                'entry_method' => 'remote'
            ],
        ]);

        // use token
        if ($this->getPaymentMethod() == 'token') {
            $data['payment']['method']['card']['ta_token']     = $this->getCardReference();
            $data['payment']['method']['card']['ta_token_key'] = $this->getTransArmorToken();
        }

        return $data;
    }

}
