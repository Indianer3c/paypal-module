<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Subscriptions;

/**
 * The application context, which customizes the payer experience during the subscription approval process with PayPal.
 */
class CustomizedXUnsupportedNineEightNineFourApplicationContext implements \JsonSerializable
{
    /** @var string */
    public $brand_name;

    /** @var string */
    public $locale;

    /** @var string */
    public $shipping_preference;

    /** @var PaymentMethod */
    public $payment_method;

    /** @var string */
    public $return_url;

    /** @var string */
    public $cancel_url;

    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
