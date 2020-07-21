<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

/**
 * The full name representation like Mr J Smith
 */
class FullName implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
