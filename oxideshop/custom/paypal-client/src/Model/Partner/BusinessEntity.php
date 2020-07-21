<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Partner;

/**
 * The business entity of the account.
 */
class BusinessEntity extends string implements \JsonSerializable
{
    /** @var BeneficialOwners */
    public $beneficial_owners;

    /** @var array */
    public $office_bearers;

    /** @var CurrencyRange */
    public $annual_sales_volume_range;

    /** @var CurrencyRange */
    public $average_monthly_volume_range;

    /** @var string */
    public $business_description;

    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
