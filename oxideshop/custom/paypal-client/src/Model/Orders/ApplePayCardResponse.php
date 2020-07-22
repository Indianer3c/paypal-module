<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;
use Webmozart\Assert\Assert;

/**
 * The Card from Apple Pay Wallet used to fund the payment
 *
 * generated from: MerchantsCommonComponentsSpecification-v1-schema-apple_pay_card_response.json
 */
class ApplePayCardResponse extends CardResponse implements JsonSerializable
{
    use BaseModel;

    /**
     * @var string
     * The card holder's name as it appears on the card.
     *
     * maxLength: 300
     */
    public $name;

    /**
     * @var AddressPortable
     * The portable international postal address. Maps to
     * [AddressValidationMetadata](https://github.com/googlei18n/libaddressinput/wiki/AddressValidationMetadata) and
     * HTML 5.1 [Autofilling form controls: the autocomplete
     * attribute](https://www.w3.org/TR/html51/sec-forms.html#autofilling-form-controls-the-autocomplete-attribute).
     */
    public $billing_address;

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
        !isset($this->name) || Assert::maxLength($this->name, 300, "name in ApplePayCardResponse must have maxlength of 300 $within");
        !isset($this->billing_address) || Assert::isInstanceOf($this->billing_address, AddressPortable::class, "billing_address in ApplePayCardResponse must be instance of AddressPortable $within");
        !isset($this->billing_address) || $this->billing_address->validate(ApplePayCardResponse::class);
        !isset($this->country_code) || Assert::minLength($this->country_code, 2, "country_code in ApplePayCardResponse must have minlength of 2 $within");
        !isset($this->country_code) || Assert::maxLength($this->country_code, 2, "country_code in ApplePayCardResponse must have maxlength of 2 $within");
    }

    public function __construct()
    {
    }
}
