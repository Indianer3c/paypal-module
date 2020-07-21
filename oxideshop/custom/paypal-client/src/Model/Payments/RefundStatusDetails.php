<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Payments;

/**
 * The details of the refund status.
 */
class RefundStatusDetails implements \JsonSerializable
{
    /** @var string */
    public $reason;

    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
