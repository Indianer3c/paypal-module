<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;
use OxidProfessionalServices\PayPal\Api\Model\CommonV4\CommonV4Money;
use Webmozart\Assert\Assert;

/**
 * Auth-Capture Tolerance details.
 *
 * generated from: model-auth_tolerance.json
 */
class AuthTolerance implements JsonSerializable
{
    use BaseModel;

    /**
     * The percentage, as a fixed-point, signed decimal number. For example, define a 19.99% interest rate as
     * `19.99`.
     *
     * @var string | null
     */
    public $percent;

    /**
     * The currency and amount for a financial transaction, such as a balance or payment due.
     *
     * @var CommonV4Money | null
     */
    public $absolute;

    public function validate($from = null)
    {
        $within = isset($from) ? "within $from" : "";
        !isset($this->absolute) || Assert::isInstanceOf(
            $this->absolute,
            CommonV4Money::class,
            "absolute in AuthTolerance must be instance of CommonV4Money $within"
        );
        !isset($this->absolute) ||  $this->absolute->validate(AuthTolerance::class);
    }

    private function map(array $data)
    {
        if (isset($data['percent'])) {
            $this->percent = $data['percent'];
        }
        if (isset($data['absolute'])) {
            $this->absolute = new CommonV4Money($data['absolute']);
        }
    }

    public function __construct(array $data = null)
    {
        if (isset($data)) {
            $this->map($data);
        }
    }
}
