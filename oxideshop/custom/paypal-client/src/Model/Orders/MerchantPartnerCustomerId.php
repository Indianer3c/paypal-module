<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

/**
 * The unique ID for a customer in merchant's or partner's system of records.
 */
class MerchantPartnerCustomerId implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
