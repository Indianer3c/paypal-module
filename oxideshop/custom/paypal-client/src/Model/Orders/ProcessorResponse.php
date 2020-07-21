<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

/**
 * The processor information. Might be required for payment requests, such as direct credit card transactions.
 */
class ProcessorResponse implements \JsonSerializable
{
    /** @var string */
    public $avs_code;

    /** @var string */
    public $cvv_code;

    /** @var string */
    public $response_code;

    /** @var string */
    public $payment_advice_code;

    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
