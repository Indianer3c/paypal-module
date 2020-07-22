<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;
use Webmozart\Assert\Assert;

/**
 * Information used to pay using POLi.
 *
 * generated from: MerchantsCommonComponentsSpecification-v1-schema-poli.json
 */
class Poli implements JsonSerializable
{
    use BaseModel;

    /**
     * @var string
     * The full name representation like Mr J Smith
     *
     * minLength: 3
     * maxLength: 300
     */
    public $name;

    /**
     * @var string
     * The [two-character ISO 3166-1 code](/docs/integration/direct/rest/country-codes/) that identifies the country
     * or region.<blockquote><strong>Note:</strong> The country code for Great Britain is <code>GB</code> and not
     * <code>UK</code> as used in the top-level domain names for that country. Use the `C2` country code for China
     * worldwide for comparable uncontrolled price (CUP) method, bank card, and cross-border
     * transactions.</blockquote>
     *
     * minLength: 2
     * maxLength: 2
     */
    public $country_code;

    public function validate($from = null)
    {
        $within = isset($from) ? "within $from" : "";
        !isset($this->name) || Assert::minLength($this->name, 3, "name in Poli must have minlength of 3 $within");
        !isset($this->name) || Assert::maxLength($this->name, 300, "name in Poli must have maxlength of 300 $within");
        !isset($this->country_code) || Assert::minLength($this->country_code, 2, "country_code in Poli must have minlength of 2 $within");
        !isset($this->country_code) || Assert::maxLength($this->country_code, 2, "country_code in Poli must have maxlength of 2 $within");
    }

    public function __construct()
    {
    }
}
