<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Partner;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;
use Webmozart\Assert\Assert;

/**
 * Seller’s consent to operate on this financial instrument.
 *
 * generated from: referral_data-mandate.json
 */
class Mandate implements JsonSerializable
{
    use BaseModel;

    /**
     * Whether mandate was accepted or not.
     *
     * @var boolean
     */
    public $accepted;

    public function validate($from = null)
    {
        $within = isset($from) ? "within $from" : "";
        Assert::notNull($this->accepted, "accepted in Mandate must not be NULL $within");
    }

    private function map(array $data)
    {
        if (isset($data['accepted'])) {
            $this->accepted = $data['accepted'];
        }
    }

    public function __construct(array $data = null)
    {
        if (isset($data)) { $this->map($data); }
    }
}
