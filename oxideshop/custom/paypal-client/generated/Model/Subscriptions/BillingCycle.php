<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Subscriptions;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;
use Webmozart\Assert\Assert;

/**
 * The billing cycle details.
 *
 * generated from: billing_cycle.json
 */
class BillingCycle implements JsonSerializable
{
    use BaseModel;

    /** A regular billing cycle. */
    public const TENURE_TYPE_REGULAR = 'REGULAR';

    /** A trial billing cycle. */
    public const TENURE_TYPE_TRIAL = 'TRIAL';

    /**
     * The pricing scheme details.
     *
     * @var PricingScheme | null
     */
    public $pricing_scheme;

    /**
     * The frequency of the billing cycle.
     *
     * @var Frequency
     */
    public $frequency;

    /**
     * The tenure type of the billing cycle. In case of a plan having trial cycle, only 2 trial cycles are allowed
     * per plan.
     *
     * use one of constants defined in this class to set the value:
     * @see TENURE_TYPE_REGULAR
     * @see TENURE_TYPE_TRIAL
     * @var string
     * minLength: 1
     * maxLength: 24
     */
    public $tenure_type;

    /**
     * The order in which this cycle is to run among other billing cycles. For example, a trial billing cycle has a
     * `sequence` of `1` while a regular billing cycle has a `sequence` of `2`, so that trial cycle runs before the
     * regular cycle.
     *
     * @var int
     */
    public $sequence;

    /**
     * The number of times this billing cycle gets executed. Trial billing cycles can only be executed a finite
     * number of times (value between <code>1</code> and <code>999</code> for <code>total_cycles</code>). Regular
     * billing cycles can be executed infinite times (value of <code>0</code> for <code>total_cycles</code>) or a
     * finite number of times (value between <code>1</code> and <code>999</code> for <code>total_cycles</code>).
     *
     * @var int | null
     */
    public $total_cycles = 1;

    public function validate($from = null)
    {
        $within = isset($from) ? "within $from" : "";
        !isset($this->pricing_scheme) || Assert::isInstanceOf(
            $this->pricing_scheme,
            PricingScheme::class,
            "pricing_scheme in BillingCycle must be instance of PricingScheme $within"
        );
        !isset($this->pricing_scheme) ||  $this->pricing_scheme->validate(BillingCycle::class);
        Assert::notNull($this->frequency, "frequency in BillingCycle must not be NULL $within");
        Assert::isInstanceOf(
            $this->frequency,
            Frequency::class,
            "frequency in BillingCycle must be instance of Frequency $within"
        );
         $this->frequency->validate(BillingCycle::class);
        Assert::notNull($this->tenure_type, "tenure_type in BillingCycle must not be NULL $within");
        Assert::minLength(
            $this->tenure_type,
            1,
            "tenure_type in BillingCycle must have minlength of 1 $within"
        );
        Assert::maxLength(
            $this->tenure_type,
            24,
            "tenure_type in BillingCycle must have maxlength of 24 $within"
        );
        Assert::notNull($this->sequence, "sequence in BillingCycle must not be NULL $within");
    }

    private function map(array $data)
    {
        if (isset($data['pricing_scheme'])) {
            $this->pricing_scheme = new PricingScheme($data['pricing_scheme']);
        }
        if (isset($data['frequency'])) {
            $this->frequency = new Frequency($data['frequency']);
        }
        if (isset($data['tenure_type'])) {
            $this->tenure_type = $data['tenure_type'];
        }
        if (isset($data['sequence'])) {
            $this->sequence = $data['sequence'];
        }
        if (isset($data['total_cycles'])) {
            $this->total_cycles = $data['total_cycles'];
        }
    }

    public function __construct(array $data = null)
    {
        $this->frequency = new Frequency();
        if (isset($data)) { $this->map($data); }
    }
}
