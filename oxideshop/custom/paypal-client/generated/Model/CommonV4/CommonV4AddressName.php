<?php

namespace OxidProfessionalServices\PayPal\Api\Model\CommonV4;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;
use Webmozart\Assert\Assert;

/**
 * The name and address, typically used for billing and shipping purposes.
 *
 * generated from:
 * MerchantsCommonComponentsSpecification-v1-schema-common_components-v4-schema-json-openapi-2.0-address_name.json
 */
class CommonV4AddressName extends CommonV4AddressPortable implements JsonSerializable
{
    use BaseModel;

    /**
     * The name on the address, for example,  Mr J. Smith.
     *
     * @var string | null
     * minLength: 1
     * maxLength: 200
     */
    public $addressee;

    public function validate($from = null)
    {
        $within = isset($from) ? "within $from" : "";
        !isset($this->addressee) || Assert::minLength(
            $this->addressee,
            1,
            "addressee in CommonV4AddressName must have minlength of 1 $within"
        );
        !isset($this->addressee) || Assert::maxLength(
            $this->addressee,
            200,
            "addressee in CommonV4AddressName must have maxlength of 200 $within"
        );
    }

    private function map(array $data)
    {
        if (isset($data['addressee'])) {
            $this->addressee = $data['addressee'];
        }
    }

    public function __construct(array $data = null)
    {
        parent::__construct($data);
        if (isset($data)) {
            $this->map($data);
        }
    }
}
