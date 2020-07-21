<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Disputes;

/**
 * The PayPal payer ID, which is a masked version of the PayPal account number intended for use with third parties. The account number is reversibly encrypted and a proprietary variant of Base32 is used to encode the result.
 */
class PayerId implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
