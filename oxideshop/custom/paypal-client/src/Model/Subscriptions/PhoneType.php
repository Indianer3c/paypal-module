<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Subscriptions;

/**
 * The phone type.
 */
class PhoneType implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
