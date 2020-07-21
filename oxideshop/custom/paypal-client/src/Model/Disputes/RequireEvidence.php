<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Disputes;

/**
 * Sandbox only. Updates the state of a dispute, by ID, to either <code>WAITING_FOR_BUYER_RESPONSE</code> or <code>WAITING_FOR_SELLER_RESPONSE</code>. This state change enables either the customer or merchant to submit evidence for the dispute. Specify an <code>action</code> value in the JSON request body to indicate whether the state change enables the customer or merchant to submit evidence.
 */
class RequireEvidence implements \JsonSerializable
{
    /** @var string */
    public $action;

    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
